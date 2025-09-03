<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

session_start();

if($_SESSION['id'] != null && $_SESSION['id'] != ''){
    $id = $_SESSION['id'];
}else{
    $id = 'SYSTEM';
}

$plant = [26, 27, 28];

if($_POST['fromDateTime'] != null && $_POST['fromDateTime'] != ''){
    $fromDate = DateTime::createFromFormat('Y-m-d', $_POST['fromDateTime']);
}else{
    $fromDate = date('Y-m-d');
}

if($_POST['toDateTime'] != null && $_POST['toDateTime'] != ''){
    $toDate = DateTime::createFromFormat('Y-m-d', $_POST['toDateTime']);
}else{
    $toDate = date('Y-m-d');
}

// Get all dates between fromDate and toDate (inclusive)
$dateRange = [];
$currentDate = clone $fromDate; // Clone to avoid modifying original

while ($currentDate <= $toDate) {
    $dateRange[] = $currentDate->format('Y-m-d');
    $currentDate->add(new DateInterval('P1D')); // Add 1 day
}

foreach ($dateRange as $processDate) {
    $today = $processDate;
    $startDate = $processDate . ' 00:00:00';
    $endDate = $processDate . ' 23:59:59';

    foreach ($plant as $plantId) {
        // Check if record already exists
        $checkRecordQuery = "SELECT id FROM Stock_Take WHERE declaration_datetime = ? AND plant_id = ? AND status = '0'";
        $check_record_stmt = $db->prepare($checkRecordQuery);
        if ($check_record_stmt) {
            $check_record_stmt->bind_param('ss', $endDate, $plantId);
            $check_record_stmt->execute();
            $check_result = $check_record_stmt->get_result();
            $recordExists = $check_result->num_rows > 0;
            $check_row = $check_result->fetch_assoc();
            $existId = $check_row['id'] ?? null;
            $check_record_stmt->close();
        }else{
            $check_record_stmt->close();

            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Failed to prepare check query: " . $db->error
                )
            );
            exit;
        }

        // 1. Check if today has declaration for this plant
        $bitumenQuery = "SELECT * FROM Bitumen WHERE plant_id = '$plantId' AND declaration_datetime >= '$startDate' AND declaration_datetime <= '$endDate' AND status = '0' ORDER BY declaration_datetime LIMIT 1";
        $bitumenResult = mysqli_query($db, $bitumenQuery);
        $hasDeclaration = mysqli_num_rows($bitumenResult) > 0;

        // 2. Get previous declaration date for this plant
        $prevQuery = "SELECT * FROM Bitumen WHERE plant_id = '$plantId' AND declaration_datetime < '$startDate' AND status = '0' ORDER BY declaration_datetime DESC LIMIT 1";
        $prevResult = mysqli_query($db, $prevQuery);
        $prevRow = mysqli_fetch_assoc($prevResult);
        $prev_ps_6070 = $prevRow ? (json_decode($prevRow['60/70'], true)['totalSixtySeventy'] ?? 0) : 0;
        $prev_ps_lfo = $prevRow ? (json_decode($prevRow['lfo'], true)['totalLfo'] ?? 0) : 0;
        $prev_ps_diesel = $prevRow ? (json_decode($prevRow['diesel'], true)['totalDiesel'] ?? 0) : 0;
        $prev_date = $prevRow ? substr($prevRow['declaration_datetime'], 0, 10) : $today;

        if (!$hasDeclaration) {
            // CASE 1: No declaration for today: insert previous PS as OS/bookstock/PS, others 0
            $sixty_seventy_os = $sixty_seventy_bookstock = $sixty_seventy_ps = (float) number_format($prev_ps_6070, 2);
            $sixty_seventy_production = $sixty_seventy_incoming = $sixty_seventy_usage = $sixty_seventy_diffstock = $sixty_seventy_actual_usage = 0;

            $lfo_os = $lfo_bookstock = $lfo_ps = (float) number_format($prev_ps_lfo, 2);
            $lfo_production = $lfo_incoming = $lfo_usage = $lfo_diffstock = $lfo_actual_usage = 0;

            $diesel_os = $diesel_bookstock = $diesel_ps = (float) number_format($prev_ps_diesel, 2);
            $diesel_production = $diesel_incoming = $diesel_mreading = $diesel_transport = $diesel_usage = $diesel_diffstock = $diesel_actual_usage = 0;

        } else {
            // CASE 2 or 3: There is a declaration for today
            $todayRow = mysqli_fetch_assoc($bitumenResult);

            // Determine if there is a gap between today and previous declaration
            $gapDays = $prev_date ? (strtotime($today) - strtotime($prev_date)) / 86400 : 1;
            $plantCode = searchPlantCodeById($plantId, $db);

            if ($gapDays > 1) {
                // ---- GAP LOGIC: SUM FIELDS FROM TRANSACTIONAL TABLES ----

                $sumStart = date('Y-m-d', strtotime($prev_date . ' +1 day'));
                $sumEnd = $today;

                // 1. 60/70 (raw_mat_id=27, raw_mat_code='BTBI001')
                // Production
                $productionQuery = "SELECT SUM(nett_weight1)/1000 as total_prod FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Sales'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$sumStart 00:00:00'
                    AND tare_weight1_date <= '$sumEnd 23:59:59'
                    AND status = '0'";
                $productionResult = mysqli_query($db, $productionQuery);
                $production_sum = ($productionResult && $row = mysqli_fetch_assoc($productionResult)) ? floatval($row['total_prod'] ?? 0) : 0;

                // Incoming
                $incomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$sumStart 00:00:00'
                    AND tare_weight1_date <= '$sumEnd 23:59:59'
                    AND raw_mat_code = 'BTBI001'
                    AND status = '0'";
                $incomingResult = mysqli_query($db, $incomingQuery);
                $incoming_sum = ($incomingResult && $row = mysqli_fetch_assoc($incomingResult)) ? floatval($row['total_in'] ?? 0) : 0;

                // Usage: (opening + incoming - closing)
                $usageQuery = "(
                                    SELECT raw_mat_weight/1000 as cs
                                    FROM Inventory_Log
                                    WHERE plant_id = '$plantId' AND raw_mat_id = 27
                                        AND DATE(event_date) >= '$prev_date'
                                    ORDER BY event_date DESC
                                    LIMIT 1
                                    )
                                    UNION ALL
                                    (
                                    SELECT raw_mat_weight/1000 as cs
                                    FROM Inventory_Log
                                    WHERE plant_id = '$plantId' AND raw_mat_id = 27
                                        AND DATE(event_date) < '$sumEnd'
                                    ORDER BY event_date DESC
                                    LIMIT 1
                                )";
                $usageResult = mysqli_query($db, $usageQuery);
                if ($usageResult) {
                    $usageCount = 0; 
                    $closing = 0;
                    $opening = 0;
                    while ($row = mysqli_fetch_assoc($usageResult)) {
                        if ($usageCount === 0) {
                            // First row is the closing from previous declaration date
                            $closing = floatval($row['cs'] ?? 0);
                            $usageCount++;
                        } else {
                            // Second row is the closing from today
                            $opening = floatval($row['cs'] ?? 0);
                        }
                    }
                }

                $usage_sum = ($opening + $incoming_sum) - $closing; 

                // Today's P/S
                $sixtySeventyData = json_decode($todayRow['60/70'], true);
                $ps_6070_today = $sixtySeventyData['totalSixtySeventy'] ?? 0;

                $sixty_seventy_os = number_format($prev_ps_6070, 2);
                $sixty_seventy_production = number_format($production_sum, 2);
                $sixty_seventy_incoming = number_format($incoming_sum, 2);
                $sixty_seventy_usage = number_format($usage_sum, 2);
                $sixty_seventy_bookstock = number_format((float) $prev_ps_6070 + (float) $sixty_seventy_incoming - (float) $sixty_seventy_usage, 2);
                $sixty_seventy_ps = number_format($ps_6070_today, 2);
                $sixty_seventy_diffstock = number_format((float) $sixty_seventy_ps - (float) $sixty_seventy_bookstock, 2);
                $sixty_seventy_actual_usage = $production_sum > 0 ? number_format(((($prev_ps_6070+$incoming_sum)-$ps_6070_today) / $production_sum) * 100, 2) : 0;

                // 2. LFO (raw_mat_id=31, raw_mat_code='LFFO001')
                // Incoming
                $lfoIncoming = 0;
                $lfoIncomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$sumStart 00:00:00'
                    AND tare_weight1_date <= '$sumEnd 23:59:59'
                    AND raw_mat_code = 'LFFO001'
                    AND status = '0'";
                $lfoIncomingResult = mysqli_query($db, $lfoIncomingQuery);
                if ($lfoIncomingResult && $row = mysqli_fetch_assoc($lfoIncomingResult)) {
                    $lfoIncoming = floatval($row['total_in'] ?? 0);
                }

                // Today's LFO P/S
                $lfoData = json_decode($todayRow['lfo'], true);
                $ps_lfo_today = $lfoData['totalLfo'] ?? 0;

                $lfo_production = number_format($production_sum, 2);
                $lfo_os = number_format($prev_ps_lfo, 2);
                $lfo_incoming = number_format($lfoIncoming, 2);
                $lfo_ps = number_format($ps_lfo_today, 2);
                $lfoUsageCalculation = ((float) $prev_ps_lfo + (float)$lfoIncoming) - (float)$ps_lfo_today;
                $lfo_usage = number_format($lfoUsageCalculation, 2); 
                $lfo_actual_usage = number_format(((float)$lfoUsageCalculation / (float)$production_sum), 2);

                // 3. Diesel (raw_mat_id=32, raw_mat_code='DIE001')
                // Incoming
                $dieselIncoming = 0;
                $dieselIncomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$sumStart 00:00:00'
                    AND tare_weight1_date <= '$sumEnd 23:59:59'
                    AND raw_mat_code = 'DIE001'
                    AND status = '0'";
                $dieselIncomingResult = mysqli_query($db, $dieselIncomingQuery);
                if ($dieselIncomingResult && $row = mysqli_fetch_assoc($dieselIncomingResult)) {
                    $dieselIncoming = floatval($row['total_in'] ?? 0);
                }
                
                $dieselData = json_decode($todayRow['diesel'], true);
                $diesel_lfo_today = $dieselData['totalDiesel'] ?? 0;

                $diesel_production = number_format($production_sum, 2);
                $diesel_os = number_format($prev_ps_diesel, 2);
                $diesel_incoming = number_format($dieselIncoming, 2);
                $diesel_mreading = 0.00;
                $diesel_transport = 0.00;
                $diesel_ps = number_format($diesel_lfo_today, 2);
                $diesel_usage = number_format((float) $prev_ps_diesel + (float) $dieselIncoming - (float) $diesel_lfo_today, 2);
                $diesel_actual_usage = number_format((float) $diesel_usage / (float) $production_sum, 2);
            } else {
                // ---- NORMAL DECLARATION DAY (NO GAP): Use only today's data ----

                // Production (60/70)
                $productionQuery = "SELECT SUM(nett_weight1)/1000 as total_prod FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Sales'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$startDate'
                    AND tare_weight1_date <= '$endDate'
                    AND status = '0'";
                $productionResult = mysqli_query($db, $productionQuery);
                $production_sum = ($productionResult && $row = mysqli_fetch_assoc($productionResult)) ? floatval($row['total_prod'] ?? 0) : 0; 

                // Incoming (60/70)
                $incomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$startDate'
                    AND tare_weight1_date <= '$endDate'
                    AND raw_mat_code = 'BTBI001'
                    AND status = '0'";
                $incomingResult = mysqli_query($db, $incomingQuery);
                $incoming_sum = ($incomingResult && $row = mysqli_fetch_assoc($incomingResult)) ? floatval($row['total_in'] ?? 0) : 0;

                // Usage: (opening + incoming - closing)
                $usageQuery = "(
                                    SELECT raw_mat_weight/1000 as cs
                                    FROM Inventory_Log
                                    WHERE plant_id = '$plantId' AND raw_mat_id = 27
                                        AND DATE(event_date) < '$endDate'
                                    ORDER BY event_date DESC
                                    LIMIT 1
                                    )
                                    UNION ALL
                                    (
                                    SELECT raw_mat_weight/1000 as cs
                                    FROM Inventory_Log
                                    WHERE plant_id = '$plantId' AND raw_mat_id = 27
                                        AND DATE(event_date) < '$startDate'
                                    ORDER BY event_date DESC
                                    LIMIT 1
                                )";
                $usageResult = mysqli_query($db, $usageQuery);
                if ($usageResult) {
                    $usageCount = 0; 
                    $closing = 0;
                    $opening = 0;
                    while ($row = mysqli_fetch_assoc($usageResult)) {
                        if ($usageCount === 0) {
                            // First row is the closing from previous declaration date
                            $closing = floatval($row['cs'] ?? 0);
                            $usageCount++;
                        } else {
                            // Second row is the closing from today
                            $opening = floatval($row['cs'] ?? 0);
                        }
                    }
                }
                $usage_sum = ($opening + $incoming_sum) - $closing; 

                // Today's P/S
                $sixtySeventyData = json_decode($todayRow['60/70'], true);
                $ps_6070_today = $sixtySeventyData['totalSixtySeventy'] ?? 0;

                $sixty_seventy_os = number_format($prev_ps_6070, 2);
                $sixty_seventy_production = number_format($production_sum, 2);
                $sixty_seventy_incoming = number_format($incoming_sum, 2);
                $sixty_seventy_usage = number_format($usage_sum, 2);
                $sixty_seventy_bookstock = number_format((float) $prev_ps_6070 + (float) $sixty_seventy_incoming - (float) $sixty_seventy_usage, 2);
                $sixty_seventy_ps = number_format($ps_6070_today, 2);
                $sixty_seventy_diffstock = number_format((float) $sixty_seventy_ps - (float) $sixty_seventy_bookstock, 2);
                $sixty_seventy_actual_usage = $production_sum > 0 ? number_format(((($prev_ps_6070+$incoming_sum)-$ps_6070_today) / $production_sum) * 100, 2) : 0;

                // 2. LFO (raw_mat_id=31, raw_mat_code='LFFO001')
                // Incoming
                $lfoIncoming = 0;
                $lfoIncomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$startDate'
                    AND tare_weight1_date <= '$endDate'
                    AND raw_mat_code = 'LFFO001'
                    AND status = '0'";
                $lfoIncomingResult = mysqli_query($db, $lfoIncomingQuery);
                if ($lfoIncomingResult && $row = mysqli_fetch_assoc($lfoIncomingResult)) {
                    $lfoIncoming = floatval($row['total_in'] ?? 0);
                }

                // Today's LFO P/S
                $lfoData = json_decode($todayRow['lfo'], true);
                $ps_lfo_today = $lfoData['totalLfo'] ?? 0;

                $lfo_production = number_format($production_sum, 2);
                $lfo_os = number_format($prev_ps_lfo, 2);
                $lfo_incoming = number_format($lfoIncoming, 2);
                $lfo_ps = number_format($ps_lfo_today, 2);
                $lfoUsageCalculation = ((float) $prev_ps_lfo + (float)$lfoIncoming) - (float)$ps_lfo_today;
                $lfo_usage = number_format($lfoUsageCalculation, 2); 
                $lfo_actual_usage = number_format(((float)$lfoUsageCalculation / (float)$production_sum), 2);

                // 3. Diesel (raw_mat_id=32, raw_mat_code='DIE001')
                // Incoming
                $dieselIncoming = 0;
                $dieselIncomingQuery = "SELECT SUM(supplier_weight)/1000 as total_in FROM Weight
                    WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase'
                    AND plant_code = '$plantCode'
                    AND tare_weight1_date >= '$startDate'
                    AND tare_weight1_date <= '$endDate'
                    AND raw_mat_code = 'DIE001'
                    AND status = '0'";
                $dieselIncomingResult = mysqli_query($db, $dieselIncomingQuery);
                if ($dieselIncomingResult && $row = mysqli_fetch_assoc($dieselIncomingResult)) {
                    $dieselIncoming = floatval($row['total_in'] ?? 0);
                }
                
                $dieselData = json_decode($todayRow['diesel'], true);
                $diesel_lfo_today = $dieselData['totalDiesel'] ?? 0;

                $diesel_production = number_format($production_sum, 2);
                $diesel_os = number_format($prev_ps_diesel, 2);
                $diesel_incoming = number_format($dieselIncoming, 2);
                $diesel_mreading = 0.00;
                $diesel_transport = 0.00;
                $diesel_ps = number_format($diesel_lfo_today, 2);
                $diesel_usage = number_format((float) $prev_ps_diesel + (float) $dieselIncoming - (float) $diesel_lfo_today, 2);
                $diesel_actual_usage = number_format((float) $diesel_usage / (float) $production_sum, 2);
            }
        }

        if ($recordExists) {
            // Update existing record
            if ($update_stmt = $db->prepare("UPDATE Stock_Take SET sixty_seventy_production = ?, sixty_seventy_os = ?, sixty_seventy_incoming = ?, sixty_seventy_usage = ?, sixty_seventy_bookstock = ?, sixty_seventy_ps = ?, sixty_seventy_diffstock = ?, sixty_seventy_actual_usage = ?, lfo_production = ?, lfo_os = ?, lfo_incoming = ?, lfo_ps = ?, lfo_usage = ?, lfo_actual_usage = ?, diesel_production = ?, diesel_os = ?, diesel_incoming = ?, diesel_mreading = ?, diesel_transport = ?, diesel_ps = ?, diesel_usage = ?, diesel_actual_usage = ? WHERE id = ?")){
                $update_stmt->bind_param("sssssssssssssssssssssss", $sixty_seventy_production, $sixty_seventy_os, $sixty_seventy_incoming, $sixty_seventy_usage, $sixty_seventy_bookstock, $sixty_seventy_ps, $sixty_seventy_diffstock, $sixty_seventy_actual_usage, $lfo_production, $lfo_os, $lfo_incoming, $lfo_ps, $lfo_usage, $lfo_actual_usage, $diesel_production, $diesel_os, $diesel_incoming, $diesel_mreading, $diesel_transport, $diesel_ps, $diesel_usage, $diesel_actual_usage, $existId);
                $update_stmt->execute();
                $update_stmt->close();
            }
        } else {
            // Insert into Stock_Take
            if ($insert_stmt = $db->prepare("INSERT INTO Stock_Take (
                declaration_datetime, plant_id, 
                sixty_seventy_production, sixty_seventy_os, sixty_seventy_incoming, sixty_seventy_usage, 
                sixty_seventy_bookstock, sixty_seventy_ps, sixty_seventy_diffstock, sixty_seventy_actual_usage, 
                lfo_production, lfo_os, lfo_incoming, lfo_ps, lfo_usage, lfo_actual_usage, 
                diesel_production, diesel_os, diesel_incoming, diesel_mreading, diesel_transport, 
                diesel_ps, diesel_usage, diesel_actual_usage
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $declarationDateTime = $endDate;
                $insert_stmt->bind_param("ssssssssssssssssssssssss",
                    $declarationDateTime, $plantId,
                    $sixty_seventy_production, $sixty_seventy_os, $sixty_seventy_incoming, $sixty_seventy_usage,
                    $sixty_seventy_bookstock, $sixty_seventy_ps, $sixty_seventy_diffstock, $sixty_seventy_actual_usage,
                    $lfo_production, $lfo_os, $lfo_incoming, $lfo_ps, $lfo_usage, $lfo_actual_usage,
                    $diesel_production, $diesel_os, $diesel_incoming, $diesel_mreading, $diesel_transport, $diesel_ps, $diesel_usage, $diesel_actual_usage
                );
                $insert_stmt->execute();
                $insert_stmt->close();
            }
        }
    }
}

echo json_encode(
    array(
        "status" => "success",
        "message" => "Processed Successfully!!"
    )
);

$db->close();
?>