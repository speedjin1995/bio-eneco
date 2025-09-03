<?php
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$startDate = '6/1/2025'; // Hardcoded or dynamic as needed
$endDate = date('n/j/Y'); // Today’s date in m/d/Y format, e.g., 4/29/2025
//$endDate = '6/30/2025';

$url = "https://sturgeon-still-falcon.ngrok-free.app/purchase_orders?start_date=$endDate&end_date=$endDate";


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
    require_once __DIR__ . '/../php/db_connect.php';
    $services = 'PullPO';
    $requests = json_encode($data);

    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;

    # Company Details
    $CompanyCode = '';
    $CompanyName = '';

    $companyQuery = "SELECT * FROM Company";
    $companyDetail = mysqli_query($db, $companyQuery);
    $companyRow = mysqli_fetch_assoc($companyDetail);

    if (!empty($companyRow)) {
        $CompanyCode = $companyRow['company_code'];
        $CompanyName = $companyRow['name'];
    }

    $agents = $data['data'];
    $errorSoProductArray = [];
    
    foreach ($agents as $rows) { 
        $OrderDate = (isset($rows['DOCDATE']) && !empty($rows['DOCDATE']) && $rows['DOCDATE'] !== '' && $rows['DOCDATE'] !== null) ? DateTime::createFromFormat('j/n/Y', $rows['DOCDATE'])->format('Y-m-d H:i:s') : '';
        $PONumber = (isset($rows['DOCNO']) && !empty($rows['DOCNO']) && $rows['DOCNO'] !== '' && $rows['DOCNO'] !== null) ? trim($rows['DOCNO']) : '';
        $SupplierCode = (isset($rows['CODE']) && !empty($rows['CODE']) && $rows['CODE'] !== '' && $rows['CODE'] !== null) ? trim($rows['CODE']) : '';
        // $SupplierName = (isset($rows['COMPANYNAME']) && !empty($rows['COMPANYNAME']) && $rows['COMPANYNAME'] !== '' && $rows['COMPANYNAME'] !== null) ? trim($rows['COMPANYNAME']) : '';
        $SupplierName = '';
        if (!empty($SupplierCode)) {
            $SupplierName = searchSupplierByCode($SupplierCode, $db);
        }
        $TransporterCode = (isset($rows['SHIPPER']) && !empty($rows['SHIPPER']) && $rows['SHIPPER'] !== '' && $rows['SHIPPER'] !== null) ? trim($rows['SHIPPER']) : '';
        $TransporterName = '';
        if (!empty($TransporterCode)) {
            $TransporterName = searchTransporterNameByCode($TransporterCode, $db);
        }
        $AgentCode = (isset($rows['AGENT']) && !empty($rows['AGENT']) && $rows['AGENT'] !== '' && $rows['AGENT'] !== null) ? trim($rows['AGENT']) : '';
        $AgentName = '';
        if (!empty($AgentCode)) {
            $AgentName = searchAgentNameByCode($AgentCode, $db);
        }
        $RawMaterialCode = (isset($rows['ITEMCODE']) && !empty($rows['ITEMCODE']) && $rows['ITEMCODE'] !== '' && $rows['ITEMCODE'] !== null) ? trim($rows['ITEMCODE']) : '';
        // $RawMaterialName = (isset($rows['DESCRIPTION']) && !empty($rows['DESCRIPTION']) && $rows['DESCRIPTION'] !== '' && $rows['DESCRIPTION'] !== null) ? trim($rows['DESCRIPTION']) : '';
        $RawMaterialName = '';
        if (!empty($RawMaterialCode)) {
            $RawMaterialName = searchRawNameByCode($RawMaterialCode, $db);
        }
        $VehNumber = (isset($rows['ITEMDESC2']) && !empty($rows['ITEMDESC2']) && $rows['ITEMDESC2'] !== '' && $rows['ITEMDESC2'] !== null) ? trim($rows['ITEMDESC2']) : '';
        $Remarks = !empty($rows['REMARK1']) ? trim($rows['REMARK1']) : '';
        $DestinationName =  (isset($rows['REMARK2']) && !empty($rows['REMARK2']) && $rows['REMARK2'] !== '' && $rows['REMARK2'] !== null) ? trim($rows['REMARK2']) : '';
        $DestinationCode = '';
        if(!empty($DestinationName)){
            $DestinationCode = searchDestinationCodeByName($DestinationName, $db);
        }
        $ExOrDel = (isset($rows['DOCREF1']) && !empty($rows['DOCREF1']) && $rows['DOCREF1'] !== '' && $rows['DOCREF1'] !== null) ? trim($rows['DOCREF1']) : '';
        $ConvertedSupplierQuantity = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
        $ConvertedBalance = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
        $ConvertedUnitId = (isset($rows['UOM']) && !empty($rows['UOM']) && $rows['UOM'] !== '' && $rows['UOM'] !== null) ? searchUnitIdByCode(trim($rows['UOM']), $db) : '';
        $PlantCode = (isset($rows['PROJECT']) && !empty($rows['PROJECT']) && $rows['PROJECT'] !== '' && $rows['PROJECT'] !== null) ? trim($rows['PROJECT']) : '';
        $PlantName = '';
        if (!empty($PlantCode)) {
            $PlantName = searchPlantNameByCode($PlantCode, $db);
        }
        $UnitPrice = (isset($rows['UNITPRICE']) && !empty($rows['UNITPRICE']) && $rows['UNITPRICE'] !== '' && $rows['UNITPRICE'] !== null) ? (float) trim($rows['UNITPRICE']) : '';
        $status = 'Open';
        $actionId = 1;

        # Supplier Checking & Processing
        if($SupplierCode != null && $SupplierCode != ''){
            $supplierQuery = "SELECT * FROM Supplier WHERE supplier_code = '$SupplierCode' AND status = '0'";
            $supplierDetail = mysqli_query($db, $supplierQuery);
            $supplierRow = mysqli_fetch_assoc($supplierDetail);
            
            if(empty($supplierRow)){
                $errMsg = "Supplier: ".$SupplierCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        else{
            $errMsg = "Supplier: ".$SupplierCode." doesn't exist in master data.";
            $errorSoProductArray[] = $errMsg;
            continue;
        }

        # Transporter Checking & Processing
        if($TransporterCode != null && $TransporterCode != ''){
            $transporterQuery = "SELECT * FROM Transporter WHERE transporter_code = '$TransporterCode' AND status = '0'";
            $transporterDetail = mysqli_query($db, $transporterQuery);
            $transporterSite = mysqli_fetch_assoc($transporterDetail);
            
            if(empty($transporterSite)){
                $errMsg = "Transporter: ".$TransporterCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        else{
            $errMsg = "Transporter: ".$TransporterCode." doesn't exist in master data.";
            $errorSoProductArray[] = $errMsg;
            continue;
        }

        # Agent Checking & Processing
        if($AgentCode != null && $AgentCode != ''){
            $agentQuery = "SELECT * FROM Agents WHERE agent_code = '$AgentCode' AND status = '0'";
            $agentDetail = mysqli_query($db, $agentQuery);
            $agentRow = mysqli_fetch_assoc($agentDetail);
            
            if(empty($agentRow)){
                $errMsg = "Sales Representative: ".$AgentCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        
        # Vehicle Checking & Processing
        if($VehNumber != null && $VehNumber != ''){
            $vehQuery = "SELECT * FROM Vehicle WHERE veh_number = '$VehNumber' AND status = '0'";
            $vehDetail = mysqli_query($db, $vehQuery);
            $vehRow = mysqli_fetch_assoc($vehDetail);
            
            if(empty($vehRow)){
                $vehSupplierCode = NULL;
                $vehSupplierName = NULL;
                $vehTransporterCode = NULL;
                $vehTransporterName = NULL;

                if ($ExOrDel == 'E'){
                    $vehTransporterCode = $TransporterCode;
                    $vehTransporterName = $TransporterName;
                }else{
                    $vehSupplierCode = $SupplierCode;
                    $vehSupplierName = $SupplierName;
                }

                if($insert_veh = $db->prepare("INSERT INTO Vehicle (veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_veh->bind_param('ssssssss', $VehNumber, $vehTransporterCode, $vehTransporterName, $vehSupplierCode, $vehSupplierName, $ExOrQuarry, $uid, $uid);
                    $insert_veh->execute();
                    $vehId = $insert_veh->insert_id; // Get the inserted vehicle ID
                    $insert_veh->close();
                    
                    if ($insert_veh_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_veh_log->bind_param('sssssssss', $vehId, $VehNumber, $vehTransporterCode, $vehTransporterName, $vehSupplierCode, $vehSupplierName, $ExOrQuarry, $actionId, $uid);
                        $insert_veh_log->execute();
                        $insert_veh_log->close();
                    }    
                }

                // $errMsg = "Vehicle: ".$VehNumber." doesn't exist in master data.";
                // $errorSoProductArray[] = $errMsg;
                // continue;
            }
        }
        
        # Destination Checking & Processing
        if($DestinationName != null && $DestinationName != ''){
            $destinationQuery = "SELECT * FROM Destination WHERE name = '$DestinationName' AND status = '0'";
            $destinationDetail = mysqli_query($db, $destinationQuery);
            $destinationRow = mysqli_fetch_assoc($destinationDetail);
            
            if(empty($destinationRow)){
                $code = 'destination';
                $firstChar = substr($DestinationName, 0, 1);
                if (ctype_alpha($firstChar)) { //Check if letter is alphabet 
                    $firstChar = strtoupper($firstChar);
                }

                // Auto gen destination code
                if($update_stmt2 = $db->prepare("SELECT * FROM miscellaneous WHERE code=? AND name=?")){
                    $update_stmt2->bind_param('ss', $code, $firstChar);

                    if (! $update_stmt2->execute()) {
                        echo json_encode(
                            array(
                                "status" => "failed",
                                "message" => "Something went wrong when generating destination code"
                            )
                        ); 
                    }
                    else{
                        $result2 = $update_stmt2->get_result();
                        $DestinationCode = $firstChar."-";
                        if ($row2 = $result2->fetch_assoc()) {
                            $charSize = strlen($row2['value']);
                            $misValue = $row2['value'];

                            for($i=0; $i<(5-(int)$charSize); $i++){
                                $DestinationCode.='0';  // S0000
                            }
                    
                            $DestinationCode .= $misValue;  //S00009

                            $misValue++;
                        }
                    }
                }

                if($insert_destination = $db->prepare("INSERT INTO Destination (destination_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_destination->bind_param('ssss', $DestinationCode, $DestinationName, $uid, $uid);
                    $insert_destination->execute();
                    $destinationId = $insert_destination->insert_id; // Get the inserted destination ID
                    $insert_destination->close();
                    
                    if ($insert_destination_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_destination_log->bind_param('sssss', $destinationId, $DestinationCode, $DestinationName, $actionId, $uid);
                        $insert_destination_log->execute();
                        $insert_destination_log->close();
                    }    
                }

                // $errMsg = "Destination: ".$DestinationCode." doesn't exist in master data.";
                // $errorSoProductArray[] = $errMsg;
                // continue;
            }
        }

        # Plant Checking & Processing
        if($PlantCode != null && $PlantCode != ''){
            $plantQuery = "SELECT * FROM Plant WHERE plant_code = '$PlantCode' AND status = '0'";
            $plantDetail = mysqli_query($db, $plantQuery);
            $plantRow = mysqli_fetch_assoc($plantDetail);
            
            if(empty($plantRow)){
                $errMsg = "Plant: ".$PlantCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        else{
            $errMsg = "Plant: ".$PlantCode." doesn't exist in master data.";
            $errorSoProductArray[] = $errMsg;
            continue;
        }

        # Raw Material Checking & Processing
        $rawMatId = '';
        if($RawMaterialCode != null && $RawMaterialCode != ''){
            $rawMatQuery = "SELECT * FROM Raw_Mat WHERE raw_mat_code = '$RawMaterialCode' AND status = '0'";
            $rawMatDetail = mysqli_query($db, $rawMatQuery);
            $rawMatRow = mysqli_fetch_assoc($rawMatDetail);
            
            if(empty($rawMatRow)){
                $errMsg = "Raw Material: ".$RawMaterialCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
            else{
                $rawMatId = $rawMatRow['id'];
            }
        }
        else{
            $errMsg = "Raw Material: ".$RawMaterialCode." doesn't exist in master data.";
            $errorSoProductArray[] = $errMsg;
            continue;
        }

        //Checking to pull rate in raw mat
        $SupplierQuantity = 0;
        if (isset($rawMatId) && !empty($rawMatId)){
            $rawMatUomQuery = "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id = '$rawMatId' AND unit_id = '2' AND status = '0'";
            $rawMatUomDetail = mysqli_query($db, $rawMatUomQuery);
            $rawMatUomRow = mysqli_fetch_assoc($rawMatUomDetail);

            if (empty($rawMatUomRow)){
                $errMsg = "Raw Material UOM for raw material code: ".$RawMaterialCode." and UOM: KG doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
            else{                
                $SupplierQuantity = $ConvertedSupplierQuantity / $rawMatUomRow['rate'];
            }
        }

        # Checking for existing PO No.
        if($PONumber != null && $PONumber != ''){
            $poQuery = "SELECT COUNT(*) AS count FROM Purchase_Order WHERE po_no = '$PONumber' AND raw_mat_code = '$RawMaterialCode' AND deleted = '0'";
            $poDetail = mysqli_query($db, $poQuery);
            $poRow = mysqli_fetch_assoc($poDetail);
            $poCount = (int) $poRow['count'];
            
            if($poCount < 1){
                $TotalPrice = 0;
                if (isset($UnitPrice) && !empty($UnitPrice) && isset($SupplierQuantity) && !empty($SupplierQuantity)){
                    $supplierQtyMt = $SupplierQuantity/1000;
                    $TotalPrice = $UnitPrice * $supplierQtyMt;
                }

                $system = 'SYSTEM';

                if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_code, company_name, supplier_code, supplier_name, order_date, po_no, agent_code, agent_name, destination_code, destination_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit, order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssssssssssssssssssssssss', $CompanyCode, $CompanyName, $SupplierCode, $SupplierName, $OrderDate, $PONumber, $AgentCode, $AgentName, $DestinationCode, $DestinationName, $RawMaterialCode, $RawMaterialName, $PlantCode, $PlantName, $TransporterCode, $TransporterName, $VehNumber, $ExOrDel, $ConvertedSupplierQuantity, $ConvertedBalance, $ConvertedUnitId, $SupplierQuantity, $SupplierQuantity, $UnitPrice, $TotalPrice, $Remarks, $status, $system, $system);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }else{
                $errMsg = "Purchase order for P/O No: ".$PONumber." + Raw Material: ".$RawMaterialName." already exist.";
                $errorSoProductArray[] = $errMsg;
            }
        }
    }

    if (!empty($errorSoProductArray)){
        $response = json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorSoProductArray 
            )
        );
        $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
        $stmtU->bind_param('ss', $response, $invid);
        $stmtU->execute();

        $db->close();
        echo $response;
    }
    else{
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
} 
else {
    require_once __DIR__ . '/../php/db_connect.php';
    $services = 'PullPO';
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