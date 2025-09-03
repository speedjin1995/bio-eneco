<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    $CompanyCode = '';
    $CompanyName = '';

    $companyQuery = "SELECT * FROM Company";
    $companyDetail = mysqli_query($db, $companyQuery);
    $companyRow = mysqli_fetch_assoc($companyDetail);

    if (!empty($companyRow)) {
        $CompanyCode = $companyRow['company_code'];
        $CompanyName = $companyRow['name'];
    }

    $errorSoProductArray = [];
    foreach ($data as $rows) {
        $OrderDate = (isset($rows['DOCDATE']) && !empty($rows['DOCDATE']) && $rows['DOCDATE'] !== '' && $rows['DOCDATE'] !== null) ? DateTime::createFromFormat('Y-m-d', excelSerialToDate($rows['DOCDATE']))->format('Y-m-d H:i:s') : '';
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
        $VehNumber = (isset($rows['DESCRIPTION2']) && !empty($rows['DESCRIPTION2']) && $rows['DESCRIPTION2'] !== '' && $rows['DESCRIPTION2'] !== null) ? trim($rows['DESCRIPTION2']) : '';
        $Remarks = !empty($rows['DOCREF4']) ? trim($rows['DOCREF4']) : '';
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

        # Company Checking & Processing
        // if($CompanyCode != null && $CompanyCode != ''){
        //     $companyQuery = "SELECT * FROM Company WHERE company_code = '$CompanyCode'";
        //     $companyDetail = mysqli_query($db, $companyQuery);
        //     $companyRow = mysqli_fetch_assoc($companyDetail);
            
        //     if(empty($companyRow)){
        //         if($insert_company = $db->prepare("INSERT INTO Company (company_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_company->bind_param('ssss', $CompanyCode, $CompanyName, $uid, $uid);
        //             $insert_company->execute();
        //             $companyId = $insert_company->insert_id; // Get the inserted company ID
        //             $insert_company->close();
                    
        //             if ($insert_company_log = $db->prepare("INSERT INTO Company_Log (company_id, company_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_company_log->bind_param('sssss', $companyId, $CompanyCode, $CompanyName, $actionId, $uid);
        //                 $insert_company_log->execute();
        //                 $insert_company_log->close();
        //             }    
        //         }
        //     }
        // }

        # Supplier Checking & Processing
        if($SupplierCode != null && $SupplierCode != ''){
            $supplierQuery = "SELECT * FROM Supplier WHERE supplier_code = '$SupplierCode' AND status = '0'";
            $supplierDetail = mysqli_query($db, $supplierQuery);
            $supplierRow = mysqli_fetch_assoc($supplierDetail);
            
            if(empty($supplierRow)){
                // if($insert_supplier = $db->prepare("INSERT INTO Supplier (supplier_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_supplier->bind_param('ssss', $SupplierCode, $SupplierName, $uid, $uid);
                //     $insert_supplier->execute();
                //     $supplierId = $insert_supplier->insert_id; // Get the inserted supplier ID
                //     $insert_supplier->close();
                    
                //     if ($insert_supplier_log = $db->prepare("INSERT INTO Supplier_Log (supplier_id, supplier_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_supplier_log->bind_param('sssss', $supplierId, $SupplierCode, $SupplierName, $actionId, $uid);
                //         $insert_supplier_log->execute();
                //         $insert_supplier_log->close();
                //     }    
                // }

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
                // if($insert_transporter = $db->prepare("INSERT INTO Transporter (transporter_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_transporter->bind_param('ssss', $TransporterCode, $TransporterName, $uid, $uid);
                //     $insert_transporter->execute();
                //     $transporterId = $insert_transporter->insert_id; // Get the inserted transporter ID
                //     $insert_transporter->close();
                    
                //     if ($insert_transporter_log = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_transporter_log->bind_param('sssss', $transporterId, $TransporterCode, $TransporterName, $actionId, $uid);
                //         $insert_transporter_log->execute();
                //         $insert_transporter_log->close();
                //     }    
                // }

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
                // if($insert_agent = $db->prepare("INSERT INTO Agents (agent_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_agent->bind_param('ssss', $AgentCode, $AgentName, $uid, $uid);
                //     $insert_agent->execute();
                //     $agentId = $insert_agent->insert_id; // Get the inserted agent ID
                //     $insert_agent->close();
                    
                //     if ($insert_agent_log = $db->prepare("INSERT INTO Agents_Log (agent_id, agent_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_agent_log->bind_param('sssss', $agentId, $AgentCode, $AgentName, $actionId, $uid);
                //         $insert_agent_log->execute();
                //         $insert_agent_log->close();
                //     }    
                // }

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
                // if($insert_plant = $db->prepare("INSERT INTO Plant (plant_code, created_by, modified_by) VALUES (?, ?, ?)")) {
                //     $insert_plant->bind_param('sss', $PlantCode, $uid, $uid);
                //     $insert_plant->execute();
                //     $plantId = $insert_plant->insert_id; // Get the inserted plant ID
                //     $insert_plant->close();
                    
                //     if ($insert_plant_log = $db->prepare("INSERT INTO Plant_Log (plant_id, plant_code, action_id, action_by) VALUES (?, ?, ?, ?)")) {
                //         $insert_plant_log->bind_param('ssss', $plantId, $PlantCode, $actionId, $uid);
                //         $insert_plant_log->execute();
                //         $insert_plant_log->close();
                //     }    
                // }

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
                // if($insert_raw_mat = $db->prepare("INSERT INTO Raw_Mat (raw_mat_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_raw_mat->bind_param('ssss', $RawMaterialCode, $RawMaterialName, $uid, $uid);
                //     $insert_raw_mat->execute();
                //     $rawMatId = $insert_raw_mat->insert_id; // Get the inserted destination ID
                //     $insert_raw_mat->close();
                    
                //     if ($insert_raw_mat_log = $db->prepare("INSERT INTO Raw_Mat_Log (raw_mat_id, raw_mat_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_raw_mat_log->bind_param('sssss', $rawMatId, $RawMaterialCode, $RawMaterialName, $actionId, $uid);
                //         $insert_raw_mat_log->execute();
                //         $insert_raw_mat_log->close();
                //     }    
                // }

                $errMsg = "Raw Material: ".$RawMaterialCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }else{
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
            }else{                
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

                if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_code, company_name, supplier_code, supplier_name, order_date, po_no, agent_code, agent_name, destination_code, destination_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit,order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
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

    $db->close();

    if (!empty($errorSoProductArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorSoProductArray 
            )
        );
    }else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Added Successfully!!" 
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
