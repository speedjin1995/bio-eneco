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
        $OrderDate = (isset($rows['DOCDATE']) && !empty($rows['DOCDATE']) && $rows['DOCDATE'] !== '' && $rows['DOCDATE'] !== null) ? DateTime::createFromFormat('d/m/Y', $rows['DOCDATE'])->format('Y-m-d H:i:s') : '';
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
        $VehNumber = (isset($rows['DESCRIPTION2']) && !empty($rows['DESCRIPTION2']) && $rows['DESCRIPTION2'] !== '' && $rows['DESCRIPTION2'] !== null) ? trim($rows['DESCRIPTION2']) : '';
        $Remarks = !empty($rows['DOCREF4']) ? trim($rows['DOCREF4']) : '';
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

        # Site Checking & Processing
        // if($SiteCode != null && $SiteCode != ''){
        //     $siteQuery = "SELECT * FROM Site WHERE site_code = '$SiteCode'";
        //     $siteDetail = mysqli_query($db, $siteQuery);
        //     $siteRow = mysqli_fetch_assoc($siteDetail);
            
        //     if(empty($siteRow)){
        //         if($insert_site = $db->prepare("INSERT INTO Site (site_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_site->bind_param('ssss', $SiteCode, $SiteName, $uid, $uid);
        //             $insert_site->execute();
        //             $siteId = $insert_site->insert_id; // Get the inserted site ID
        //             $insert_site->close();
                    
        //             if ($insert_site_log = $db->prepare("INSERT INTO Site_Log (site_id, site_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_site_log->bind_param('sssss', $siteId, $SiteCode, $SiteName, $actionId, $uid);
        //                 $insert_site_log->execute();
        //                 $insert_site_log->close();
        //             }    
        //         }
        //     }
        // }

        # Customer Checking & Processing
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode' AND status = '0'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                // if($insert_customer = $db->prepare("INSERT INTO Customer (customer_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_customer->bind_param('ssss', $CustomerCode, $CustomerName, $uid, $uid);
                //     $insert_customer->execute();
                //     $customerId = $insert_customer->insert_id; // Get the inserted customer ID
                //     $insert_customer->close();
                    
                //     if ($insert_customer_log = $db->prepare("INSERT INTO Customer_Log (customer_id, customer_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_customer_log->bind_param('sssss', $customerId, $CustomerCode, $CustomerName, $actionId, $uid);
                //         $insert_customer_log->execute();
                //         $insert_customer_log->close();
                //     }    
                // }

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

        # Product Checking & Processing
        $productId = '';
        if($ProductCode != null && $ProductCode != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$ProductCode' AND status = '0'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                // if($insert_product = $db->prepare("INSERT INTO Product (product_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                //     $insert_product->bind_param('ssss', $ProductCode, $ProductName, $uid, $uid);
                //     $insert_product->execute();
                //     $productId = $insert_product->insert_id; // Get the inserted destination ID
                //     $insert_product->close();
                    
                //     if ($insert_product_log = $db->prepare("INSERT INTO Product_Log (product_id, product_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //         $insert_product_log->bind_param('sssss', $productId, $ProductCode, $ProductName, $actionId, $uid);
                //         $insert_product_log->execute();
                //         $insert_product_log->close();
                //     }    
                // }
            
                $errMsg = "Product: ".$ProductCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }else{
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
                # Old Code
                // if ($insert_stmt = $db->prepare("INSERT INTO Sales_Order (company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, order_load, order_quantity, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                //     $insert_stmt->bind_param('sssssssssssssssssssssss', $CompanyCode, $CompanyName, $CustomerCode, $CustomerName, $SiteCode, $SiteName, $OrderDate, $OrderNumber, $SONumber, $DeliveryDate, $SalesrepCode, $SalesrepName, $DestinationCode, $DestinationName, $DeliverToName, $ProductCode, $ProductName, $OrderLoad, $OrderQuantity, $Remarks, $status, $uid, $uid);
                //     $insert_stmt->execute();
                //     $insert_stmt->close(); 
                // }

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
            }else{
                $errMsg = "Sales order for Customer P/O No: ".$OrderNumber." + Product: ".$ProductName." already exist.";
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
