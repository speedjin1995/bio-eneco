<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];
$type = '';

if($_POST['type'] != null && $_POST['type'] != ''){
    $type = $_POST['type'];
}

## Search 
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d H:i:00');
  $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d H:i:59');
	$searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_code = '".$_POST['product']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['purchaseOrder'] != null && $_POST['purchaseOrder'] != '' && $_POST['purchaseOrder'] != '-'){
	$searchQuery .= " and purchase_order = '".$_POST['purchaseOrder']."'";
}

if ($type == "MULTI"){
    if(is_array($_POST['userID'])){
        $ids = implode(",", $_POST['userID']);
    }else{
        $ids = $_POST['userID'];
    }

    if ($stmt2 = $db->prepare("SELECT * FROM Weight WHERE id IN ($ids) AND synced = 'N'")) {
        if($stmt2->execute()){
            $result = $stmt2->get_result();

            while ($row = $result->fetch_assoc()) {
                $orderNo = $row['purchase_order'];
                $productCode = $row['product_code'];
                $plantCode = $row['plant_code'];
    
                $soNo = '';
                $uom = '';
                $qty = 0;
                $amt = 0;
                $unitPrice = 0;
    
                // Get product ID & basic UOM
                $productId = searchProductIdByCode($productCode, $db);
                $uom = searchProductBasicUomByCode($productCode, $db);
    
                // Convert weight to UOM-based quantity
                if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id='2' AND status='0'")) {
                    $update_stmt->bind_param('s', $productId);
                    $update_stmt->execute();
                    $result2 = $update_stmt->get_result();
                    if ($row4 = $result2->fetch_assoc()) {
                        $qty = $row['nett_weight1'] * $row4['rate'];
                    }
                    $update_stmt->close();
                }
    
                // Get unit price and SO if available
                if ($orderNo === '-' || empty($orderNo)) {
                    $unitPrice = $row['unit_price'];
                } else {
                    if ($select_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $orderNo, $productCode, $plantCode);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) {
                            $unitPrice = $row3['unit_price'] ?? 0;
                            $soNo = $row3['so_no'];
                        }
                        $select_stmt->close();
                    }
                }
    
                $amt = $qty * $unitPrice;
    
                $records[] = [
                    "DOCREF2"     => $row["docref2"],
                    "DOCDATE"     => $row["docdate"],
                    "DESCRIPTION2"=> $row["vehicle_no"],
                    "CODE"        => "300-C0001", // hardcoded or dynamic if needed
                    "COMPANYNAME" => $row["customer_name"],
                    "ITEMCODE"    => $productCode,
                    "DESCRIPTION" => $row["product_name"],
                    "REMARK2"     => $row["destination"],
                    "SHIPPER"     => $row["driver_code"] ?? "T01",
                    "DOCREF1"     => "E",
                    "DOCNOEX"     => "-",
                    "REMARK1"     => $row["doc_no"],
                    "QTY"         => round($qty, 3),
                    "UOM"         => $uom,
                    "PROJECT"     => $row['plant_code'],
                    "LOCATION"    => $row['plant_code'],
                    "UNITPRICE"   => round($unitPrice, 2),
                    "AMOUNT"      => round($amt, 2),
                    "SO_NUMBER"   => $soNo
                ];
            }

            $stmt2->close();
            
            $services = 'PostDeliveryOrder';
            $requests = json_encode($records);
            
            // Insert request into Api_Log
            $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
            $stmtL->bind_param('ss', $services, $requests);
            $stmtL->execute();
            $logId = $stmtL->insert_id;
            
            // Send to API
            $ch = curl_init("https://sturgeon-still-falcon.ngrok-free.app/delivery_order");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($records));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            
            // Decode API response (JSON string to array)
            $apiResponse = json_decode($response, true);
            
            // Prepare loggable response JSON
            if ($httpCode === 200 && isset($responseData["status"]) && $responseData["status"] === "success") {
                $responseToLog = json_encode([
                    "status" => "success", 
                    "message" => "Post Successfully",
                    "posted" => $responseData["results"]
                ]);
            } else {
                $responseToLog = json_encode([
                    "status" => "failed",
                    "http_code" => $httpCode,
                    "error" => $error,
                    "response" => $responseRaw
                ]);
            }
            
            // Update the same Api_Log record with the response
            $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
            $stmtU->bind_param('ss', $responseToLog, $logId);
            $stmtU->execute();
            $stmtU->close();
            
            // Output final response to client
            $db->close();
            echo $responseToLog;
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $stmt2->error
                )
            );
        }
    } 
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something's wrong"
            )
        );
    }
}else{
    $sql = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND synced = 'N'".$searchQuery;
    if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
        $username = implode("', '", $_SESSION["plant"]);
        $sql = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username')".$searchQuery;
    }

    if ($stmt2 = $db->prepare($sql)){
        if($stmt2->execute()){
            $result = $stmt2->get_result();

            while ($row = $result->fetch_assoc()) {
                $orderNo = $row['purchase_order'];
                $productCode = $row['product_code'];
                $plantCode = $row['plant_code'];
    
                $soNo = '';
                $uom = '';
                $qty = 0;
                $amt = 0;
                $unitPrice = 0;
    
                // Get product ID & basic UOM
                $productId = searchProductIdByCode($productCode, $db);
                $uom = searchProductBasicUomByCode($productCode, $db);
    
                // Convert weight to UOM-based quantity
                if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id='2' AND status='0'")) {
                    $update_stmt->bind_param('s', $productId);
                    $update_stmt->execute();
                    $result2 = $update_stmt->get_result();
                    if ($row4 = $result2->fetch_assoc()) {
                        $qty = $row['nett_weight1'] * $row4['rate'];
                    }
                    $update_stmt->close();
                }
    
                // Get unit price and SO if available
                if ($orderNo === '-' || empty($orderNo)) {
                    $unitPrice = $row['unit_price'];
                } else {
                    if ($select_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $orderNo, $productCode, $plantCode);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) {
                            $unitPrice = $row3['unit_price'] ?? 0;
                            $soNo = $row3['so_no'];
                        }
                        $select_stmt->close();
                    }
                }
    
                $amt = $qty * $unitPrice;
    
                $records[] = [
                    "DOCREF2"     => $row["docref2"],
                    "DOCDATE"     => $row["docdate"],
                    "DESCRIPTION2"=> $row["vehicle_no"],
                    "CODE"        => "300-C0001", // hardcoded or dynamic if needed
                    "COMPANYNAME" => $row["customer_name"],
                    "ITEMCODE"    => $productCode,
                    "DESCRIPTION" => $row["product_name"],
                    "REMARK2"     => $row["destination"],
                    "SHIPPER"     => $row["driver_code"] ?? "T01",
                    "DOCREF1"     => "E",
                    "DOCNOEX"     => "-",
                    "REMARK1"     => $row["doc_no"],
                    "QTY"         => round($qty, 3),
                    "UOM"         => $uom,
                    "PROJECT"     => $row['plant_code'],
                    "LOCATION"    => $row['plant_code'],
                    "UNITPRICE"   => round($unitPrice, 2),
                    "AMOUNT"      => round($amt, 2),
                    "SO_NUMBER"   => $soNo
                ];
            }

            $stmt2->close();
            
            $services = 'PostDeliveryOrder';
            $requests = json_encode($records);
            
            // Insert request into Api_Log
            $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
            $stmtL->bind_param('ss', $services, $requests);
            $stmtL->execute();
            $logId = $stmtL->insert_id;
            
            // Send to API
            $ch = curl_init("https://sturgeon-still-falcon.ngrok-free.app/delivery_order");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($records));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            
            // Decode API response (JSON string to array)
            $apiResponse = json_decode($response, true);
            
            // Prepare loggable response JSON
            if ($httpCode === 200 && isset($responseData["status"]) && $responseData["status"] === "success") {
                $responseToLog = json_encode([
                    "status" => "success", 
                    "message" => "Post Successfully",
                    "posted" => $responseData["results"]
                ]);
            } else {
                $responseToLog = json_encode([
                    "status" => "failed",
                    "http_code" => $httpCode,
                    "error" => $error,
                    "response" => $responseRaw
                ]);
            }
            
            // Update the same Api_Log record with the response
            $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
            $stmtU->bind_param('ss', $responseToLog, $logId);
            $stmtU->execute();
            $stmtU->close();
            
            // Output final response to client
            $db->close();
            echo $responseToLog;
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $stmt2->error
                )
            );
        }
    }
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something's wrong"
            )
        );
    }
}
?>