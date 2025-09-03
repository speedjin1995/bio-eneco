<?php
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$startDate = '5/7/2025'; // Hardcoded or dynamic as needed
$endDate = date('n/j/Y'); // Today’s date in m/d/Y format, e.g., 4/29/2025
//$endDate = '6/30/2025';

$url = "https://sturgeon-still-falcon.ngrok-free.app/sales_orders?start_date=$endDate&end_date=$endDate";

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
    $services = 'PullSO';
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
        $SONumber = (isset($rows['DOCNO']) && !empty($rows['DOCNO']) && $rows['DOCNO'] !== '' && $rows['DOCNO'] !== null) ? trim($rows['DOCNO']) : '';
        $OrderNumber = (isset($rows['DOCNOEX']) && !empty($rows['DOCNOEX']) && $rows['DOCNOEX'] !== '' && $rows['DOCNOEX'] !== null) ? trim($rows['DOCNOEX']) : '';
        $CustomerCode = (isset($rows['CODE']) && !empty($rows['CODE']) && $rows['CODE'] !== '' && $rows['CODE'] !== null) ? trim($rows['CODE']) : '';
        $CustomerName = '';
        if (!empty($CustomerCode)){
            $CustomerName = searchCustomerByCode($CustomerCode, $db);
        }
        // $CustomerName = (isset($rows['COMPANYNAME']) && !empty($rows['COMPANYNAME']) && $rows['COMPANYNAME'] !== '' && $rows['COMPANYNAME'] !== null) ? trim($rows['COMPANYNAME']) : '';
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
        $ProductCode = (isset($rows['ITEMCODE']) && !empty($rows['ITEMCODE']) && $rows['ITEMCODE'] !== '' && $rows['ITEMCODE'] !== null) ? trim($rows['ITEMCODE']) : '';
        // $ProductName = (isset($rows['DESCRIPTION']) && !empty($rows['DESCRIPTION']) && $rows['DESCRIPTION'] !== '' && $rows['DESCRIPTION'] !== null) ? trim($rows['DESCRIPTION']) : '';
        $ProductName = '';
        if (!empty($ProductCode)) {
            $ProductName = searchProductNameByCode($ProductCode, $db);
        }
        $VehNumber = (isset($rows['ITEMDESC2']) && !empty($rows['ITEMDESC2']) && $rows['ITEMDESC2'] !== '' && $rows['ITEMDESC2'] !== null) ? trim($rows['ITEMDESC2']) : '';
        $Remarks = !empty($rows['REMARKS']) ? trim($rows['REMARKS']) : '';
        $DestinationName =  (isset($rows['REMARK2']) && !empty($rows['REMARK2']) && $rows['REMARK2'] !== '' && $rows['REMARK2'] !== null) ? trim($rows['REMARK2']) : '';
        $DestinationCode = '';
        if(!empty($DestinationName)){
            $DestinationCode = searchDestinationCodeByName($DestinationName, $db);
        }
        $ExOrQuarry = (isset($rows['DOCREF1']) && !empty($rows['DOCREF1']) && $rows['DOCREF1'] !== '' && $rows['DOCREF1'] !== null) ? trim($rows['DOCREF1']) : '';
        $ConvertedOrderQuantity = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
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

        # Customer Checking & Processing
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode' AND status = '0'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                $errMsg = "Customer: ".$CustomerCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        else{
            $errMsg = "Customer: ".$CustomerCode." doesn't exist in master data.";
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
                $vehCustomerCode = NULL;
                $vehCustomerName = NULL;
                $vehTransporterCode = NULL;
                $vehTransporterName = NULL;

                if ($ExOrQuarry == 'E'){
                    $vehTransporterCode = $TransporterCode;
                    $vehTransporterName = $TransporterName;
                }else{
                    $vehCustomerCode = $CustomerCode;
                    $vehCustomerName = $CustomerName;
                }

                if($insert_veh = $db->prepare("INSERT INTO Vehicle (veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_veh->bind_param('ssssssss', $VehNumber, $vehTransporterCode, $vehTransporterName, $vehCustomerCode, $vehCustomerName, $ExOrQuarry, $uid, $uid);
                    $insert_veh->execute();
                    $vehId = $insert_veh->insert_id; // Get the inserted vehicle ID
                    $insert_veh->close();
                    
                    if ($insert_veh_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_veh_log->bind_param('sssssssss', $vehId, $VehNumber, $vehTransporterCode, $vehTransporterName, $vehCustomerCode, $vehCustomerName, $ExOrQuarry, $actionId, $uid);
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

        # Product Checking & Processing
        $productId = '';
        if($ProductCode != null && $ProductCode != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$ProductCode' AND status = '0'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                $errMsg = "Product: ".$ProductCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
            else{
                $productId = $productRow['id'];
            }
        }
        else{
            $errMsg = "Product: ".$ProductCode." doesn't exist in master data.";
            $errorSoProductArray[] = $errMsg;
            continue;
        }

        //Checking to pull rate in product
        $OrderQuantity = 0;
        if (isset($productId) && !empty($productId)){
            $productUomQuery = "SELECT * FROM Product_UOM WHERE product_id = '$productId' AND unit_id = '2' AND status = '0'";
            $productUomDetail = mysqli_query($db, $productUomQuery);
            $productUomRow = mysqli_fetch_assoc($productUomDetail);

            if (empty($productUomRow)){
                $errMsg = "Product UOM for product code: ".$ProductCode." and UOM: KG doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }else{                
                $OrderQuantity = $ConvertedOrderQuantity / $productUomRow['rate'];
            }
        }

        # Checking for existing Order No.
        if($OrderNumber != null && $OrderNumber != ''){
            $soQuery = "SELECT COUNT(*) AS count FROM Sales_Order WHERE order_no = '$OrderNumber' AND product_code = '$ProductCode' AND deleted = '0'";
            $soDetail = mysqli_query($db, $soQuery);
            $soRow = mysqli_fetch_assoc($soDetail);
            $soCount = (int) $soRow['count'];
            
            if($soCount < 1){
                $TotalPrice = 0;
                if (isset($UnitPrice) && !empty($UnitPrice) && isset($OrderQuantity) && !empty($OrderQuantity)){
                    $orderQtyMt = $OrderQuantity/1000;
                    $TotalPrice = $UnitPrice * $orderQtyMt;
                }
                
                $system = 'SYSTEM';

                if ($insert_stmt = $db->prepare("INSERT INTO Sales_Order (company_code, company_name, customer_code, customer_name, order_date, order_no, so_no, agent_code, agent_name, destination_code, destination_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit, order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssssssssssssssssssssss', $CompanyCode, $CompanyName, $CustomerCode, $CustomerName, $OrderDate, $OrderNumber, $SONumber, $AgentCode, $AgentName, $DestinationCode, $DestinationName, $ProductCode, $ProductName, $PlantCode, $PlantName, $TransporterCode, $TransporterName, $VehNumber, $ExOrQuarry, $ConvertedOrderQuantity, $ConvertedBalance, $ConvertedUnitId, $OrderQuantity, $OrderQuantity, $UnitPrice, $TotalPrice, $Remarks, $status, $system, $system);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }
            else{
                $errMsg = "Sales order for Customer P/O No: ".$OrderNumber." + Product: ".$ProductName." already exist.";
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