<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['transactionId'], $_POST['transactionStatus'], $_POST['weightType'], $_POST['transactionDate'], $_POST['grossIncoming'], $_POST['grossIncomingDate']
, $_POST['manualWeight'], $_POST['plantCode'], $_POST['plant'], $_POST['exDel'], $_POST['loadDrum'])) {
    $isCancel = 'N';
    $isComplete = 'N';
    $isApproved = 'Y';
    $received = 'Y';
    $misValue = '';

    if (empty($_POST["id"])) {
        $weightId = null;
    } else {
        $weightId = trim($_POST["id"]);
    }

    if (empty($_POST["plantId"])) {
        $plantId = null;
    } else {
        $plantId = trim($_POST["plantId"]);
    }

    if (empty($_POST["plantCode"])) {
        $plantCode = null;
    } else {
        $plantCode = trim($_POST["plantCode"]);
    }

    if (empty($_POST["plant"])) {
        $plant = null;
    } else {
        $plant = trim($_POST["plant"]);
    }

    if (empty($_POST["exDel"])) {
        $exDel = null;
    } else {
        if ($_POST["exDel"] == 'true'){
            $exDel = 'EX';
        }else{
            $exDel = 'DEL';
        }
    }

    if (empty($_POST["loadDrum"])) {
        $loadDrum = null;
    } else {
        if ($_POST["loadDrum"] == 'true'){
            $loadDrum = 'LOAD';
        }else{
            $loadDrum = 'DRUM';
        }
    }

    if (empty($_POST["transactionId"])) {
        $status = $_POST['transactionStatus'];

		if($update_stmt2 = $db->prepare("SELECT * FROM status WHERE status=?")){
			$update_stmt2->bind_param('s', $status);

			if (! $update_stmt2->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong when pulling status"
                    )
                ); 
            }
            else{
                $result2 = $update_stmt2->get_result();
				$id = '1';
				$transactionId = '';

				/*if ($row2 = $result2->fetch_assoc()) {
					//$id = $row2['misc_id'];
					$transactionId .= $row2['prefix'];
				}*/

                $queryPlant = "SELECT do_no as curcount FROM Plant WHERE plant_code='$plantCode'";

				if ($update_stmt = $db->prepare($queryPlant)) {
					// Execute the prepared query.
					if (! $update_stmt->execute()) {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Something went wrong"
							)); 
					}
					else{
						$result = $update_stmt->get_result();
						$message = array();
						
						if ($row = $result->fetch_assoc()) {
							$charSize = strlen($row['curcount']);
							$misValue = $row['curcount'];
		
							for($i=0; $i<(6-(int)$charSize); $i++){
								$transactionId.='0';  // S0000
							}
					
							$transactionId .= $misValue;  //S00009
                        }
                    }
                }
            }
		}
    } 
    else {
        $transactionId = trim($_POST["transactionId"]);
    }

    if (empty($_POST["transactionStatus"])) {
        $transactionStatus = null;
    } else {
        $transactionStatus = trim($_POST["transactionStatus"]);
    }

    if (empty($_POST["unitPrice"])) {
        $unitPrice = null;
    } else {
        $unitPrice = trim($_POST["unitPrice"]);
    }

    if (empty($_POST["subTotalPrice"])) {
        $subTotalPrice = '0.00';
    } else {
        $subTotalPrice = trim($_POST["subTotalPrice"]);
    }

    if (empty($_POST["sstPrice"])) {
        $sstPrice = '0.00';
    } else {
        $sstPrice = trim($_POST["sstPrice"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = '0.00';
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["weightType"])) {
        $weightType = 'Normal';
    } else {
        $weightType = trim($_POST["weightType"]);
    }

    if (empty($_POST["customerType"])) {
        $customerType = 'Normal';
    } else {
        $customerType = trim($_POST["customerType"]);
    }

    if (empty($_POST["transactionDate"])) {
        $transactionDate = null;
    } else {
        $transactionDate = DateTime::createFromFormat('d-m-Y', $_POST["transactionDate"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["poSupplyWeight"])) {
        $poSupplyWeight = null;
    } else {
        $poSupplyWeight = trim($_POST["poSupplyWeight"]);
    }

    if (empty($_POST["supplierWeight"])) {
        $supplierWeight = null;
    } else {
        $supplierWeight = trim($_POST["supplierWeight"]);
    }

    if (empty($_POST["orderWeight"])) {
        $orderWeight = null;
    } else {
        $orderWeight = trim($_POST["orderWeight"]);
    }

    if (empty($_POST["grossIncoming"])) {
        $grossIncoming = 0;
    } else {
        $grossIncoming = trim($_POST["grossIncoming"]);
    }

    if (empty($_POST["grossIncomingDate"])) {
        $grossIncomingDate = (new DateTime())->format('Y-m-d H:i:s');
    } 
    else {
        $grossIncomingDate = trim(str_replace(["AM", "PM"], "", $_POST["grossIncomingDate"]));
        $grossIncomingDate = DateTime::createFromFormat('d/m/Y H:i:s', $grossIncomingDate)->format('Y-m-d H:i:s');
    }
    
    if (empty($_POST["tareOutgoing"])) {
        $tareOutgoing = 0;
    } else {
        $tareOutgoing = trim($_POST["tareOutgoing"]);
    }

    if (empty($_POST["tareOutgoingDate"])) {
        $tareOutgoingDate = null;
    } else {
        $tareOutgoingDate = trim(str_replace(["AM", "PM"], "", $_POST["tareOutgoingDate"]));
        $tareOutgoingDate = DateTime::createFromFormat('d/m/Y H:i:s', $tareOutgoingDate)->format('Y-m-d H:i:s');
    }

    if (empty($_POST["nettWeight"])) {
        $nettWeight = 0;
    } else {
        $nettWeight = trim($_POST["nettWeight"]);
    }

    if (empty($_POST["basicNettWeight"])) {
        $basicNettWeight = 0;
    } else {
        $basicNettWeight = trim($_POST["basicNettWeight"]);
    }

    if (empty($_POST["manualWeight"])) {
        $manualWeight = null;
    } else {
        $manualWeight = trim($_POST["manualWeight"]);
    }

    if (empty($_POST["weighbridge"])) {
        $weighbridge = 'Weigh1';
    } else {
        $weighbridge = trim($_POST["weighbridge"]);
    }

    if (empty($_POST["indicatorId"])) {
        $indicatorId = null;
    } else {
        $indicatorId = trim($_POST["indicatorId"]);
    }

    if (empty($_POST["invoiceNo"])) {
        $invoiceNo = null;
    } else {
        $invoiceNo = trim($_POST["invoiceNo"]);
    }


    /*if ($transactionStatus == 'Sales'){
        $deliveryNo = $transactionId;
    }else{*/
    if (empty($_POST["deliveryNo"])) {
        $deliveryNo = null;
    } else {
        $deliveryNo = trim($_POST["deliveryNo"]);
    }
    //}

    if ($transactionStatus == 'Purchase'){
        if (empty($_POST["purchaseOrder"])) {
            $purchaseOrder = null;
        } else {
            $purchaseOrder = trim($_POST["purchaseOrder"]);
        }
    }else{
        if (empty($_POST["salesOrder"])) {
            $purchaseOrder = null;
        } else {
            $purchaseOrder = trim($_POST["salesOrder"]);
        }
    }

    if (empty($_POST["containerNo"])) {
        $containerNo = null;
    } else {
        $containerNo = trim($_POST["containerNo"]);
    }

    if (empty($_POST["customerName"])) {
        if (empty($_POST["custName"])){
            $customerName = null;
        }else{
            $customerName = trim($_POST["custName"]);
        }
    } else {
        $customerName = trim($_POST["customerName"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }

    if (empty($_POST["rawMaterialName"])) {
        $rawMaterialName = null;
    } else {
        $rawMaterialName = trim($_POST["rawMaterialName"]);
    }

    if (empty($_POST["rawMaterialId"])) {
        $rawMaterialId = null;
    } else {
        $rawMaterialId = trim($_POST["rawMaterialId"]);
    }

    if (empty($_POST["siteName"])) {
        $siteName = null;
    } else {
        $siteName = trim($_POST["siteName"]);
    }

    if (empty($_POST["transporter"])) {
        if (empty($_POST["transporterName"])){
            $transporter = null;
        }else{
            $transporter = trim($_POST["transporterName"]);
        }
    } else {
        $transporter = trim($_POST["transporter"]);
    }

    if (empty($_POST["weightDifference"])) {
        $weightDifference = null;
    } else {
        $weightDifference = trim($_POST["weightDifference"]);
    }
    
    if (empty($_POST["destination"])) {
        $destination = null;
    } else {
        $destination = trim($_POST["destination"]);
    }

    if (empty($_POST["reduceWeight"])) {
        $reduceWeight = '0';
    } else {
        $reduceWeight = trim($_POST["reduceWeight"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = '0.00';
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["otherRemarks"])) {
        $otherRemarks = null;
    } else {
        $otherRemarks = trim($_POST["otherRemarks"]);
    }

    // container
    if (empty($_POST["vehiclePlateNo2"])) {
        $vehiclePlateNo2 = null;
    } else {
        $vehiclePlateNo2 = trim($_POST["vehiclePlateNo2"]);
    }

    if (empty($_POST["grossIncoming2"])) {
        $grossIncoming2 = null;
    } else {
        $grossIncoming2 = trim($_POST["grossIncoming2"]);
    }

    if (empty($_POST["grossIncomingDate2"])) {
        $grossIncomingDate2 = null;
    } else {
        $grossIncomingDate2 = DateTime::createFromFormat('d/m/Y H:i:s A', $_POST["grossIncomingDate2"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["tareOutgoing2"])) {
        $tareOutgoing2 = null;
    } else {
        $tareOutgoing2 = trim($_POST["tareOutgoing2"]);
    }

    if (empty($_POST["tareOutgoingDate2"])) {
        $tareOutgoingDate2 = null;
    } else {
        $tareOutgoingDate2 = DateTime::createFromFormat('d/m/Y H:i:s A', $_POST["tareOutgoingDate2"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["nettWeight2"])) {
        $nettWeight2 = null;
    } else {
        $nettWeight2 = trim($_POST["nettWeight2"]);
    }

    if (empty($_POST["agent"])) {
        $agent = null;
    } else {
        $agent = trim($_POST["agent"]);
    }

    if (empty($_POST["agentCode"])) {
        $agentCode = null;
    } else {
        $agentCode = trim($_POST["agentCode"]);
    }
    
    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if (empty($_POST["supplierCode"])) {
        $supplierCode = null;
    } else {
        $supplierCode = trim($_POST["supplierCode"]);
    }

    if (empty($_POST["supplierName"])) {
        $supplierName = null;
    } else {
        $supplierName = trim($_POST["supplierName"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }
    
    if (empty($_POST["productId"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["productId"]);
    }

    if (empty($_POST["productId"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["productId"]);
    }

    if (empty($_POST["rawMaterialCode"])) {
        $rawMaterialCode = null;
    } else {
        $rawMaterialCode = trim($_POST["rawMaterialCode"]);
    }

    if (empty($_POST["rawMaterialId"])) {
        $rawMaterialId = null;
    } else {
        $rawMaterialId = trim($_POST["rawMaterialId"]);
    }

    if (empty($_POST["siteCode"])) {
        $siteCode = null;
    } else {
        $siteCode = trim($_POST["siteCode"]);
    }

    if (empty($_POST["destinationCode"])) {
        $destinationCode = null;
    } else {
        $destinationCode = trim($_POST["destinationCode"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["finalWeight"])) {
        $finalWeight = '0';
    } else {
        $finalWeight = trim($_POST["finalWeight"]);
    }

    if (empty($_POST["indicatorId2"])) {
        $indicatorId2 = null;
    } else {
        $indicatorId2 = trim($_POST["indicatorId2"]);
    }

    if (empty($_POST["productDescription"])) {
        $productDescription = null;
    } else {
        $productDescription = trim($_POST["productDescription"]);
    }

    if (empty($_POST["vehiclePlateNo1"])) {
        $vehiclePlateNo1 = null;
    } else {
        $vehiclePlateNo1 = trim($_POST["vehiclePlateNo1"]);
    }

    if(filter_has_var(INPUT_POST,'manualVehicle')) {
        $vehiclePlateNo1 = trim($_POST["vehicleNoTxt"]);
    }

    if($weightType == 'Normal' && ($grossIncoming != null && $tareOutgoing != null)){
        $isComplete = 'Y';
    }
    else if($weightType == 'Container' && ($grossIncoming != null && $tareOutgoing != null && $grossIncoming2 != null && $tareOutgoing2 != null)){
        $isComplete = 'Y';
    }
    else{
        $isComplete = 'N';
    }

    if(isset($_POST['status']) && $_POST['status'] != null && $_POST['status'] != ''){
        if($_POST['status'] == 'pending'){
            $isComplete = 'N';
            $isApproved = 'N';
        }
    }

    if($isComplete == 'Y' && $transactionStatus == 'Local'){
        $received = 'N';
    }

    if(isset($_POST['bypassReason']) && $_POST['bypassReason'] != null && $_POST['bypassReason'] != ''){
        $approved_reason = $_POST['bypassReason'];
    }
    else{
        $approved_reason = null;
    }

    if(isset($_POST['noOfDrum']) && $_POST['noOfDrum'] != null && $_POST['noOfDrum'] != ''){
        $noOfDrum = $_POST['noOfDrum'];
    }
    else{
        $noOfDrum = null;
    }

    if (empty($_POST["batchDrum"])) {
        $batchDrum = null;
    } else {
        $batchDrum = trim($_POST["batchDrum"]);
    }

    if(isset($_POST['balance']) && $_POST['balance'] != null && $_POST['balance'] != ''){
        $prevBalance = $_POST['balance'];
    }
    else{
        $prevBalance = 0;
    }

    if(isset($_POST['tinNo']) && $_POST['tinNo'] != null && $_POST['tinNo'] != ''){
        $tinNo = $_POST['tinNo'];
    }
    else{
        $tinNo = null;
    }

    if(isset($_POST['idNo']) && $_POST['idNo'] != null && $_POST['idNo'] != ''){
        $idNo = $_POST['idNo'];
    }
    else{
        $idNo = null;
    }

    if(isset($_POST['idType']) && $_POST['idType'] != null && $_POST['idType'] != ''){
        $idType = $_POST['idType'];
    }
    else{
        $idType = null;
    }

    /*if($_POST['grossIncomingDate'] != null && $_POST['grossIncomingDate'] != ''){
        // $inDate = new DateTime($_POST['grossIncomingDate']);
        // $inCDateTime = date_format($inDate,"Y-m-d H:i:s");
        $pStatus = "Pending";
    }

    if($_POST['tareOutgoingDate'] != null && $_POST['tareOutgoingDate'] != ''){
        // $outDate = new DateTime($_POST['tareOutgoingDate']);
        // $outGDateTime = date_format($outDate,"Y-m-d H:i:s");
        $pStatus = "Complete";
    }*/

    if(! empty($weightId)){
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";

        # Update PO or SO table row balance only if status is Purchase or Sales
        if ($transactionStatus == 'Purchase' || $transactionStatus == 'Sales'){
            if ($isComplete == 'Y' && $isCancel == 'N'){
                if($transactionStatus == 'Purchase'){
                    $soPoQuantity = $poSupplyWeight;
                    $orderSuppWeight = $supplierWeight;
                    $prodRawCode = $rawMaterialCode;
                    $prodRawName = $rawMaterialName;
                    $weighing_stmt = $db->prepare("SELECT * FROM Weight WHERE purchase_order=? AND raw_mat_code=? AND raw_mat_name=? AND plant_code=? AND plant_name=? AND status='0' AND is_complete='Y' AND is_cancel='N' AND id !=?");
                }elseif($transactionStatus == 'Sales'){
                    $soPoQuantity = $orderWeight;
                    $orderSuppWeight = $nettWeight;
                    $prodRawCode = $productCode;
                    $prodRawName = $productName;
                    $weighing_stmt = $db->prepare("SELECT * FROM Weight WHERE purchase_order=? AND product_code=? AND product_name=? AND plant_code=? AND plant_name=? AND status='0' AND is_complete='Y' AND is_cancel='N' AND id !=?");
                }

                # Weighing Details
                $weighing_stmt->bind_param('ssssss', $purchaseOrder, $prodRawCode, $prodRawName, $plantCode, $plant, $weightId);
                $weighing_stmt->execute();
                $result = $weighing_stmt->get_result(); 
                $weighing_stmt->close();

                if ($result->num_rows > 0) {
                    $orderSuppWeights = 0;

                    while ($row = $result->fetch_assoc()) {
                        if ($row['transaction_status'] == 'Purchase'){
                            $orderSuppWeights += $row['supplier_weight'];
                        }else{
                            $orderSuppWeights += $row['nett_weight1'];
                        }
                    }

                    $orderSuppWeights = $orderSuppWeights + $orderSuppWeight;
                    $currentBalance = $soPoQuantity - $orderSuppWeights;
                } else {
                    // No records found
                    $currentBalance = $soPoQuantity - $orderSuppWeight;
                }
            
                $poSoStatus = ($currentBalance <= 26) ? 'Close' : 'Open';

                # Inventory Logic
                $previousNettWeight = 0;
                // Query previous weight log
                $weight_log_stmt = $db->prepare("SELECT * FROM Weight_Log WHERE transaction_id=? ORDER BY 1 DESC");
                $weight_log_stmt->bind_param('s', $transactionId);
                $weight_log_stmt->execute();
                $weight_log_result = $weight_log_stmt->get_result();
                $weight_log_stmt->close();
                
                if ($weight_log_result->num_rows > 0){
                    $weightLogRow = $weight_log_result->fetch_assoc();
                    $previousNettWeight = $weightLogRow['nett_weight1'];
                }

                $nettWeightDifference = (float) $nettWeight - (float) $previousNettWeight;
            }
        }elseif($transactionStatus == 'WIP'){
            # Inventory Logic
            $previousNettWeight = 0;
            // Query previous weight log
            $weight_log_stmt = $db->prepare("SELECT * FROM Weight_Log WHERE transaction_id=? ORDER BY 1 DESC");
            $weight_log_stmt->bind_param('s', $transactionId);
            $weight_log_stmt->execute();
            $weight_log_result = $weight_log_stmt->get_result();
            $weight_log_stmt->close();

            if ($weight_log_result->num_rows > 0){
                $weightLogRow = $weight_log_result->fetch_assoc();
                $previousNettWeight = $weightLogRow['nett_weight1'];
            }

            $nettWeightDifference = (float) $nettWeight - (float) $previousNettWeight;
        }

        if ($update_stmt = $db->prepare("UPDATE Weight SET transaction_id=?, transaction_status=?, weight_type=?, customer_type=?, transaction_date=?, lorry_plate_no1=?, lorry_plate_no2=?, supplier_weight=?, po_supply_weight=?, order_weight=?, tin_no=?, id_no=?, id_type=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?,
        product_code=?, product_name=?, ex_del=?, raw_mat_code=?, raw_mat_name=?, site_name=?, site_code=?, container_no=?, invoice_no=?, purchase_order=?, delivery_no=?, transporter_code=?, transporter=?, destination_code=?, destination=?, remarks=?, gross_weight1=?, gross_weight1_date=?, tare_weight1=?, tare_weight1_date=?, nett_weight1=?,
        gross_weight2=?, gross_weight2_date=?, tare_weight2=?, tare_weight2_date=?, nett_weight2=?, reduce_weight=?, final_weight=?, weight_different=?, is_complete=?, is_cancel=?, manual_weight=?, indicator_id=?, weighbridge_id=?, created_by=?, modified_by=?, indicator_id_2=?, 
        product_description=?, unit_price=?, sub_total=?, sst=?, total_price=?, is_approved=?, approved_reason=?, plant_code=?, plant_name=?, agent_code=?, agent_name=?, load_drum=?, no_of_drum=?, batch_drum=?, received=? WHERE id=?"))
        {
            $update_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $poSupplyWeight, $orderWeight, $tinNo, $idNo, $idType, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum, $batchDrum, $received, $weightId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else
            {
                // Update Balance 
                if ($transactionStatus == 'Purchase' || $transactionStatus == 'Sales'){
                    if ($isComplete == 'Y' && $isCancel == 'N'){
                        if ($transactionStatus == 'Purchase'){
                            $sql =  "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status=?";
                            $prodRawId = $rawMaterialId;
                            $updatePoSoStmt = $db->prepare("UPDATE Purchase_Order SET converted_balance=?, balance=?, status=? WHERE po_no=? AND raw_mat_code=? AND plant_code=? AND plant_name=?");
                        }elseif($transactionStatus == 'Sales'){
                            $sql = "SELECT * FROM Product_UOM WHERE product_id=? AND unit_id=? AND status=?";
                            $prodRawId = $productId;
                            $updatePoSoStmt = $db->prepare("UPDATE Sales_Order SET converted_balance=?, balance=?, status=? WHERE order_no=? AND product_code=? AND plant_code=? AND plant_name=?");
                        }

                        // get conversion UOM
                        $conversion_stmt = $db->prepare($sql);
                        $unit = '2';
                        $status = '0';
                        $conversion_stmt->bind_param('sss', $prodRawId, $unit, $status);
                        $conversion_stmt->execute();
                        $conversion_result = $conversion_stmt->get_result();

                        $convertedBalance = 0;
                        $rate = 1;
                        if ($conversion_result->num_rows > 0){
                            $conversionRow = $conversion_result->fetch_assoc();
                            $rate = $conversionRow['rate'];
                        }
                        $conversion_stmt->close();
                        $convertedBalance = $currentBalance * (float) $rate;

                        // Update Balance 
                        $updatePoSoStmt->bind_param('sssssss', $convertedBalance, $currentBalance, $poSoStatus, $purchaseOrder, $prodRawCode, $plantCode, $plant);
                        $updatePoSoStmt->execute();
                        $updatePoSoStmt->close();
                    }
                }

                # Update Inventory Raw Material
                if ($transactionStatus == 'Purchase'){
                    if ($isComplete == 'Y' && $isCancel == 'N'){
                        $inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_code=? AND status='0'");
                        $inventory_stmt->bind_param('ss', $rawMaterialId, $plantCode);
                        $inventory_stmt->execute();
                        $inventory_result = $inventory_stmt->get_result();

                        while ($inventoryRow = $inventory_result->fetch_assoc()) {
                            $basicUomWeight = $inventoryRow['raw_mat_basic_uom'];
                            $weight = $inventoryRow['raw_mat_weight'];
                            $invId = $inventoryRow['id'];

                            $addedWeight = (float) $weight + (float) $nettWeightDifference;
                            $addedBasicNettWeight = $addedWeight * $rate;

                            $upd_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");
                            $upd_inv_stmt->bind_param('sss', $addedBasicNettWeight, $addedWeight, $invId);
                            $upd_inv_stmt->execute();
                            $upd_inv_stmt->close();
                        }

                        $inventory_stmt->close();
                    }
                }elseif ($transactionStatus == 'Sales') {
                    if ($isComplete == 'Y' && $isCancel == 'N'){
                        $productRawMat_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id=? AND plant_id=? AND status='0'");
                        $productRawMat_stmt->bind_param('ss', $productId, $plantId);
                        $productRawMat_stmt->execute();
                        $productRawMat_result = $productRawMat_stmt->get_result();

                        while ($productRawMatRow = $productRawMat_result->fetch_assoc()) {
                            $rawMatCode = $productRawMatRow['raw_mat_code'];
                            $rawMatId = searchRawMatIdByCode($rawMatCode, $db);
                            $rawMatBasicUom = $productRawMatRow['raw_mat_basic_uom'];
                            $rawMatWeight = $productRawMatRow['raw_mat_weight'];

                            $deltaRawMatWeight = (float) $rawMatWeight * $nettWeightDifference; // Multiply Weight Difference Only
                            $deltaBasicUom = $deltaRawMatWeight * $rate;

                            // Query Inventory for Raw Material
                            $inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_code=? AND status='0'");
                            $inventory_stmt->bind_param('ss', $rawMatId, $plantCode);
                            $inventory_stmt->execute();
                            $inventory_result = $inventory_stmt->get_result();
                            $invRow = $inventory_result->fetch_assoc();
                            $inventory_stmt->close();

                            if (!empty($invRow)){
                                $invId = $invRow['id'];
                                $currentBasicUom = (float)$invRow['raw_mat_basic_uom'];
                                $currentWeight  = (float)$invRow['raw_mat_weight'];

                                // Calculation with delta
                                $newBasicUom = $currentBasicUom - $deltaBasicUom;
                                $newWeight = $currentWeight - $deltaRawMatWeight;

                                // Update Inventory
                                $upd_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");
                                $upd_inv_stmt->bind_param('sss', $newBasicUom, $newWeight, $invId);
                                $upd_inv_stmt->execute();
                                $upd_inv_stmt->close();
                            }
                        }

                        $productRawMat_stmt->close();
                    }
                }elseif ($transactionStatus == 'WIP') {
                    // Logic to minus materials need for WIP product then add amount for WIP product (EDIT VERSION)
                    if ($isComplete == 'Y' && $isCancel == 'N'){
                        if($product_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id=? AND plant_id=? AND status='0'")){
                            $product_stmt->bind_param('ss', $productId, $plantId);
                            $product_stmt->execute();
                            $product_result = $product_stmt->get_result();

                            while ($productRow = $product_result->fetch_assoc()) {
                                $rawMatId = $productRow['raw_mat_id'];
                                $rawMatWeight = $productRow['raw_mat_weight'];

                                $deltaRawMatWeight = (float) $rawMatWeight * $nettWeightDifference; // Use weight difference for edit

                                // Query for rate conversion
                                $unitId = '2'; // KG unit ID
                                $rate_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status='0'");
                                $rate_stmt->bind_param('ss', $rawMatId, $unitId);
                                $rate_stmt->execute();
                                $rate_result = $rate_stmt->get_result();
                                $rate_row = $rate_result->fetch_assoc();
                                $rate_stmt->close();

                                if (!empty($rate_row) && isset($rate_row) && $rate_row != null) {
                                    $rate = $rate_row['rate'];

                                    if ($inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_id=? AND status='0'")){
                                        $inventory_stmt->bind_param('ss', $rawMatId, $plantId);
                                        $inventory_stmt->execute();
                                        $inventory_result = $inventory_stmt->get_result();

                                        while ($inventoryRow = $inventory_result->fetch_assoc()) {
                                            $inventoryBalance = $inventoryRow['raw_mat_weight'];
                                            $inventoryId = $inventoryRow['id'];

                                            $newInventoryBalance = (float) $inventoryBalance - (float) $deltaRawMatWeight;
                                            $newInvBasicUom = $newInventoryBalance * $rate;

                                            // Update Inventory
                                            if ($upd_inventory_stmt = $db->prepare("UPDATE Inventory SET raw_mat_weight=?, raw_mat_basic_uom=? WHERE id=?")) {
                                                $upd_inventory_stmt->bind_param('sss', $newInventoryBalance, $newInvBasicUom, $inventoryId);
                                                $upd_inventory_stmt->execute();
                                                $upd_inventory_stmt->close();
                                            }
                                        }

                                        $inventory_stmt->close();
                                    }
                                }
                            }

                            $product_stmt->close();

                            // Add WIP product Inventory - After all raw materials are deducted (EDIT VERSION)
                            if ($wip_inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_id=? AND status='0'")) {
                                $wip_inventory_stmt->bind_param('ss', $productId, $plantId);
                                $wip_inventory_stmt->execute();
                                $wip_inventory_result = $wip_inventory_stmt->get_result();

                                if ($wip_inventory_row = $wip_inventory_result->fetch_assoc()) {
                                    $currentWipWeight = $wip_inventory_row['raw_mat_weight'];
                                    $wipInventoryId = $wip_inventory_row['id'];

                                    // Get product UOM rate for conversion
                                    $wipUnitId = 2;
                                    $wip_product_rate_stmt = $db->prepare("SELECT * FROM Raw_Mat_Uom WHERE raw_mat_id=? AND unit_id=? AND status='0'");
                                    $wip_product_rate_stmt->bind_param('ss', $productId, $wipUnitId);
                                    $wip_product_rate_stmt->execute();
                                    $wip_product_rate_result = $wip_product_rate_stmt->get_result();
                                    $wip_product_rate_row = $wip_product_rate_result->fetch_assoc();
                                    $wip_product_rate_stmt->close();

                                    $wipRate = 1; // Default rate
                                    if (!empty($wip_product_rate_row) && isset($wip_product_rate_row) && $wip_product_rate_row != null) {
                                        $wipRate = $wip_product_rate_row['rate'];

                                        // Add the weight difference to WIP inventory
                                        $newWipWeight = (float) $currentWipWeight + (float) $nettWeightDifference;
                                        $newWipBasicUom = $newWipWeight * $wipRate;

                                        // Update WIP inventory
                                        if ($upd_wip_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_weight=?, raw_mat_basic_uom=? WHERE id=?")) {
                                            $upd_wip_inv_stmt->bind_param('sss', $newWipWeight, $newWipBasicUom, $wipInventoryId);
                                            $upd_wip_inv_stmt->execute();
                                            $upd_wip_inv_stmt->close();
                                        }
                                    }
                                }

                                $wip_inventory_stmt->close();
                            }
                        }
                    }
                }

                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!",
                        "id"=>$weightId
                    )
                );
            }
            
        }
    }
    else{
        $action = "1"; 
        
        if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, customer_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, ex_del, raw_mat_code, raw_mat_name, site_code, site_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, manual_weight, indicator_id, weighbridge_id, created_by, modified_by, indicator_id_2, 
        product_description, unit_price, sub_total, sst, total_price, is_approved, approved_reason, plant_code, plant_name, agent_code, agent_name, load_drum, no_of_drum, batch_drum, received) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $poSupplyWeight, $orderWeight, $tinNo, $idNo, $idType, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum, $batchDrum, $received);

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $misValue++;
                $id = $insert_stmt->insert_id;

                $queryPlantU = "UPDATE Plant SET do_no=? WHERE plant_code='$plantCode'";
                
                ///insert miscellaneous
                if ($update_stmt = $db->prepare($queryPlantU)){
                    $update_stmt->bind_param('s', $misValue);
                    
                    // Execute the prepared query.
                    if (! $update_stmt->execute()){
        
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt->error
                            )
                        );
                    } 
                    else{
                        $update_stmt->close();
                        //$db->close();

                        # Update PO or SO table row balance only if status is Purchase or Sales
                        if ($transactionStatus == 'Purchase' || $transactionStatus == 'Sales'){
                            if ($isComplete == 'Y' && $isCancel == 'N'){
                                if($transactionStatus == 'Purchase'){
                                    $soPoQuantity = $poSupplyWeight;
                                    $orderSuppWeight = $supplierWeight;
                                    $prodRawCode = $rawMaterialCode;
                                    $prodRawName = $rawMaterialName;
                                    $weighing_stmt = $db->prepare("SELECT * FROM Weight WHERE purchase_order=? AND raw_mat_code=? AND raw_mat_name=? AND plant_code=? AND plant_name=? AND status='0' AND is_complete='Y' AND is_cancel='N' AND id !=?");
                                }elseif($transactionStatus == 'Sales'){
                                    $soPoQuantity = $orderWeight;
                                    $orderSuppWeight = $nettWeight;
                                    $prodRawCode = $productCode;
                                    $prodRawName = $productName;
                                    $weighing_stmt = $db->prepare("SELECT * FROM Weight WHERE purchase_order=? AND product_code=? AND product_name=? AND plant_code=? AND plant_name=? AND status='0' AND is_complete='Y' AND is_cancel='N' AND id !=?");
                                }
                
                                # Weighing Details
                                $weighing_stmt->bind_param('ssssss', $purchaseOrder, $prodRawCode, $prodRawName, $plantCode, $plant, $id);
                                $weighing_stmt->execute();
                                $result = $weighing_stmt->get_result();
                
                                if ($result->num_rows > 0) {
                                    $orderSuppWeights = 0;
                
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['transaction_status'] == 'Purchase'){
                                            $orderSuppWeights += $row['supplier_weight'];
                                        }else{
                                            $orderSuppWeights += $row['nett_weight1'];
                                        }
                                    }
                
                                    $orderSuppWeights = $orderSuppWeights + $orderSuppWeight;
                                    $currentBalance = $soPoQuantity - $orderSuppWeights;
                                } else {
                                    // No records found
                                    $currentBalance = $soPoQuantity - $orderSuppWeight;
                                }
                            
                                $poSoStatus = ($currentBalance <= 26) ? 'Close' : 'Open';

                                if ($transactionStatus == 'Purchase'){
                                    $sql =  "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status=?";
                                    $prodRawId = $rawMaterialId;
                                    $updatePoSoStmt = $db->prepare("UPDATE Purchase_Order SET converted_balance=?, balance=?, status=? WHERE po_no=? AND raw_mat_code=? AND plant_code=? AND plant_name=?");
                                }elseif($transactionStatus == 'Sales'){
                                    $sql = "SELECT * FROM Product_UOM WHERE product_id=? AND unit_id=? AND status=?";
                                    $prodRawId = $productId;
                                    $updatePoSoStmt = $db->prepare("UPDATE Sales_Order SET converted_balance=?, balance=?, status=? WHERE order_no=? AND product_code=? AND plant_code=? AND plant_name=?");
                                }

                                // get conversion UOM
                                $conversion_stmt = $db->prepare($sql);
                                $unit = '2';
                                $status = '0';
                                $conversion_stmt->bind_param('sss', $prodRawId, $unit, $status);
                                $conversion_stmt->execute();
                                $conversion_result = $conversion_stmt->get_result();

                                $convertedBalance = 0;
                                $rate = 1;
                                if ($conversion_result->num_rows > 0){
                                    $conversionRow = $conversion_result->fetch_assoc();
                                    $rate = $conversionRow['rate'];
                                }
                                $conversion_stmt->close();
                                $convertedBalance = $currentBalance * (float) $rate;

                                // Update Balance 
                                $updatePoSoStmt->bind_param('sssssss', $convertedBalance, $currentBalance, $poSoStatus, $purchaseOrder, $prodRawCode, $plantCode, $plant);
                                $updatePoSoStmt->execute();
                                $updatePoSoStmt->close();
                            }
                        }

                        # Update Inventory Raw Material
                        if ($transactionStatus == 'Purchase'){
                            if ($isComplete == 'Y' && $isCancel == 'N'){
                                $inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_code=? AND status='0'");
                                $inventory_stmt->bind_param('ss', $rawMaterialId, $plantCode);
                                $inventory_stmt->execute();
                                $inventory_result = $inventory_stmt->get_result();

                                while ($inventoryRow = $inventory_result->fetch_assoc()) {
                                    $basicUomWeight = $inventoryRow['raw_mat_basic_uom'];
                                    $weight = $inventoryRow['raw_mat_weight'];
                                    $invId = $inventoryRow['id'];

                                    $addedBasicNettWeight = (float)$basicUomWeight + (float)$basicNettWeight;
                                    $addedWeight = (float)$weight + (float)$nettWeight;

                                    $upd_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");
                                    $upd_inv_stmt->bind_param('sss', $addedBasicNettWeight, $addedWeight, $invId);
                                    $upd_inv_stmt->execute();
                                    $upd_inv_stmt->close();
                                }

                                $inventory_stmt->close();
                            }
                        }elseif ($transactionStatus == 'Sales') {
                            if ($isComplete == 'Y' && $isCancel == 'N'){
                                $productRawMat_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id=? AND status='0'");
                                $productRawMat_stmt->bind_param('s', $productId);
                                $productRawMat_stmt->execute();
                                $productRawMat_result = $productRawMat_stmt->get_result();

                                while ($productRawMatRow = $productRawMat_result->fetch_assoc()) {
                                    $rawMatCode = $productRawMatRow['raw_mat_code'];
                                    $rawMatId = searchRawMatIdByCode($rawMatCode, $db);
                                    $rawMatBasicUom = $productRawMatRow['raw_mat_basic_uom'];
                                    $rawMatWeight = $productRawMatRow['raw_mat_weight'];

                                    //$multipliedBasicUom = (float) $rawMatBasicUom * (float) $basicNettWeight;
                                    $multipliedRawMatWeight = (float) $rawMatWeight * (float) $nettWeight;

                                    // Query for rate conversion
                                    $rateStatus = '0';
                                    $unitId = '2';
                                    $rate_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status=?");
                                    $rate_stmt->bind_param('sss', $rawMatId, $unitId, $rateStatus);
                                    $rate_stmt->execute();
                                    $rateResult = $rate_stmt->get_result();
                                    $rateRow = $rateResult->fetch_assoc();

                                    if (!empty($rateRow)){
                                        $rate = $rateRow['rate'];    
                                        $multipliedBasicUom = $multipliedRawMatWeight * (float) $rate;

                                        // Query Inventory for Raw Material
                                        $inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_code=? AND status='0'");
                                        $inventory_stmt->bind_param('ss', $rawMatId, $plantCode);
                                        $inventory_stmt->execute();
                                        $inventory_result = $inventory_stmt->get_result();
                                        $invRow = $inventory_result->fetch_assoc();
                                        $inventory_stmt->close();

                                        if (!empty($invRow)){
                                            $basicUomWeight = $invRow['raw_mat_basic_uom'];
                                            $weight = $invRow['raw_mat_weight'];
                                            $invId = $invRow['id'];

                                            // Calculation to deduct
                                            $deductedBasicNettWeight = (float)$basicUomWeight - (float)$multipliedBasicUom;
                                            $deductedWeight = (float)$weight - (float)$multipliedRawMatWeight;

                                            // Update Inventory
                                            $upd_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");
                                            $upd_inv_stmt->bind_param('sss', $deductedBasicNettWeight, $deductedWeight, $invId);
                                            $upd_inv_stmt->execute();
                                            $upd_inv_stmt->close();
                                        }
                                    }
                                }

                                $productRawMat_stmt->close();
                            }
                        }elseif ($transactionStatus == 'WIP') {
                            // Logic to minus materials need for WIP product then add amount for WIP product
                            if ($isComplete == 'Y' && $isCancel == 'N'){
                                if($product_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id=? AND plant_id=? AND status='0'")){
                                    $product_stmt->bind_param('ss', $productId, $plantId);
                                    $product_stmt->execute();
                                    $product_result = $product_stmt->get_result();

                                    while ($productRow = $product_result->fetch_assoc()) {
                                        $rawMatId = $productRow['raw_mat_id'];
                                        $rawMatWeight = $productRow['raw_mat_weight'];

                                        $calculatedWeight = (float) $nettWeight * (float) $rawMatWeight;

                                        // Query for rate conversion
                                        $unitId = '2'; // KG unit ID
                                        $rate_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status='0'");
                                        $rate_stmt->bind_param('ss', $rawMatId, $unitId);
                                        $rate_stmt->execute();
                                        $rate_result = $rate_stmt->get_result();
                                        $rate_row = $rate_result->fetch_assoc();
                                        $rate_stmt->close();

                                        if (!empty($rate_row) && isset($rate_row) && $rate_row != null) {
                                            $rate = $rate_row['rate'];

                                            if ($inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_id=? AND status='0'")){
                                                $inventory_stmt->bind_param('ss', $rawMatId, $plantId);
                                                $inventory_stmt->execute();
                                                $inventory_result = $inventory_stmt->get_result();

                                                while ($inventoryRow = $inventory_result->fetch_assoc()) {
                                                    $inventoryBalance = $inventoryRow['raw_mat_weight'];
                                                    $inventoryId = $inventoryRow['id'];

                                                    $newInventoryBalance = (float) $inventoryBalance - (float) $calculatedWeight;
                                                    $newInvBasicUom = $newInventoryBalance * $rate;

                                                    // Update Inventory
                                                    if ($upd_inventory_stmt = $db->prepare("UPDATE Inventory SET raw_mat_weight=?, raw_mat_basic_uom=? WHERE id=?")) {
                                                        $upd_inventory_stmt->bind_param('sss', $newInventoryBalance, $newInvBasicUom, $inventoryId);
                                                        $upd_inventory_stmt->execute();
                                                        $upd_inventory_stmt->close();
                                                    }
                                                }

                                                $inventory_stmt->close();
                                            }
                                        }
                                    }

                                    $product_stmt->close();

                                    // Add WIP product Inventory - After all raw materials are deducted
                                    if ($wip_inventory_stmt = $db->prepare("SELECT * FROM Inventory WHERE raw_mat_id=? AND plant_id=? AND status='0'")) {
                                        $wip_inventory_stmt->bind_param('ss', $productId, $plantId);
                                        $wip_inventory_stmt->execute();
                                        $wip_inventory_result = $wip_inventory_stmt->get_result();

                                        if ($wip_inventory_row = $wip_inventory_result->fetch_assoc()) {
                                            $currentWipWeight = $wip_inventory_row['raw_mat_weight'];
                                            $wipInventoryId = $wip_inventory_row['id'];

                                            // Get product UOM rate for conversion
                                            $wipUnitId = 2;
                                            $wip_product_rate_stmt = $db->prepare("SELECT * FROM Raw_Mat_Uom WHERE raw_mat_id=? AND unit_id=? AND status='0'");
                                            $wip_product_rate_stmt->bind_param('ss', $productId, $wipUnitId);
                                            $wip_product_rate_stmt->execute();
                                            $wip_product_rate_result = $wip_product_rate_stmt->get_result();
                                            $wip_product_rate_row = $wip_product_rate_result->fetch_assoc();
                                            $wip_product_rate_stmt->close();

                                            $wipRate = 1; // Default rate
                                            if (!empty($wip_product_rate_row) && isset($wip_product_rate_row) && $wip_product_rate_row != null) {
                                                $wipRate = $wip_product_rate_row['rate'];

                                                // Add the produced PG76 to inventory
                                                $newWipWeight = (float) $currentWipWeight + (float) $nettWeight;
                                                $newWipBasicUom = $newWipWeight * $wipRate;

                                                // Update WIP inventory
                                                if ($upd_wip_inv_stmt = $db->prepare("UPDATE Inventory SET raw_mat_weight=?, raw_mat_basic_uom=? WHERE id=?")) {
                                                    $upd_wip_inv_stmt->bind_param('sss', $newWipWeight, $newWipBasicUom, $wipInventoryId);
                                                    $upd_wip_inv_stmt->execute();
                                                    $upd_wip_inv_stmt->close();
                                                }
                                            }
                                        }
                                        $wip_inventory_result->close();
                                    }
                                }
                            }
                        }

                        if($transactionStatus == 'Receive'){
                            $rid = trim($_POST["rid"]);
                            $queryPlantR = "UPDATE Weight SET received='Y' WHERE id='$rid'";

                            if ($update_stmtr = $db->prepare($queryPlantR)){
                                $update_stmtr->execute();
                                $update_stmtr->close();
                            }
                            
                        }

                        echo json_encode(
                            array(
                                "status"=> "success", 
                                "message"=> "Added Successfully!!",
                                "id"=>$id
                            )
                        );
                    }
                } 
                else{
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
                }

                $insert_stmt->close();
                $db->close();
            }
        }
    }   
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>