<?php
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$url = "https://sturgeon-still-falcon.ngrok-free.app/items";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 100);
curl_setopt($curl, CURLOPT_VERBOSE, true);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode([
        "status" => "failed",
        "message" => curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

// Decode the JSON
$data = json_decode($response, true);

if (!empty($data['data'])) {
    require_once 'db_connect.php';
    $services = 'PullRawMaterials';
    $requests = json_encode($data);

    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;
    $agents = $data['data'];
    
    foreach ($agents as $agent) {
        if (!empty($agent['conversions']) && is_array($agent['conversions']) && isset($agent['UOM']) && !empty($agent['UOM'])) {
            $code = $db->real_escape_string($agent['CODE']);
            $desc = $db->real_escape_string($agent['DESCRIPTION']);
            $active = ($agent['ISACTIVE'] === "True") ? 1 : 0;
    
            // Use a new statement for checking existence
            $checkQuery = "SELECT COUNT(*) AS count FROM Raw_Mat WHERE raw_mat_code = ?";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bind_param("s", $code);
            $checkStmt->execute();
            $result = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            // Just to query the id
            $unitStmt2 = $db->prepare("SELECT id FROM Unit WHERE unit = ?");
            $unitStmt2->bind_param("s", $agent['UOM']);
            $unitStmt2->execute();
            $unitResult2 = $unitStmt2->get_result();
    
            if ($unitRow2 = $unitResult2->fetch_assoc()) {
                $unitId2 = $unitRow2['id'];
            }

            if ($result['count'] > 0) {
                // Update if exists
                $updateQuery = "UPDATE Raw_Mat SET name = ?, description = ?, basic_uom = ?, modified_by = ? WHERE raw_mat_code = ?";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bind_param("sssss", $desc, $desc, $unitId2, $uid, $code);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Insert if not exists
                $insertQuery = "INSERT INTO Raw_Mat (raw_mat_code, name, description, basic_uom, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $db->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $code, $desc, $desc, $unitId2, $uid, $uid);
                $insertStmt->execute();
                $insertStmt->close();
            }

            // Just to query the id
            $getIdQuery = "SELECT id FROM Raw_Mat WHERE raw_mat_code = ?";
            $getIdStmt = $db->prepare($getIdQuery);
            $getIdStmt->bind_param("s", $code);
            $getIdStmt->execute();
            $getIdResult = $getIdStmt->get_result()->fetch_assoc();
            $productId = $getIdResult['id'];
            $getIdStmt->close();

            // Delete old conversion
            $deleteStmt = $db->prepare("DELETE FROM Raw_Mat_UOM WHERE raw_mat_id = ?");
            $deleteStmt->bind_param("i", $productId);
            $deleteStmt->execute();
            $deleteStmt->close();

            foreach ($agent['conversions'] as $conv) {
                $uomCode = $db->real_escape_string($conv['UOM']);
                $rateVal = floatval($conv['RATE']);
        
                // Step 1: Get unit_id from Unit table
                $unitStmt = $db->prepare("SELECT id FROM Unit WHERE unit = ?");
                $unitStmt->bind_param("s", $uomCode);
                $unitStmt->execute();
                $unitResult = $unitStmt->get_result();
        
                if ($unitRow = $unitResult->fetch_assoc()) {
                    $unitId = $unitRow['id'];
        
                    // Step 2: Insert into Raw_Mat_UOM
                    $productStmt = $db->prepare("INSERT INTO Raw_Mat_UOM (raw_mat_id, unit_id, rate) VALUES (?, ?, ?)");
                    $productStmt->bind_param("ssd", $productId, $unitId, $rateVal);
                    $productStmt->execute();
                    $productStmt->close();
                }
        
                $unitStmt->close();
            }
        }
    }

    $response = json_encode(
        array(
            "status" => "success",
            "message" => "Data synced successfully!"
        )
    );
    $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
    $stmtU->bind_param('ss', $response, $invid);
    $stmtU->execute();

    $db->close();
    echo $response;
} 
else {
    require_once 'db_connect.php';
    $services = 'PullRawMaterials';
    $requests = json_encode($data);

    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;

    $response = json_encode(
        array(
            "status" => "failed",
            "message" => "Invalid data received from API"
        )
    );
    $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
    $stmtU->bind_param('ss', $response, $invid);
    $stmtU->execute();

    $db->close();
    echo $response;
}
?>