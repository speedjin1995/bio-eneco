<?php
require_once "db_connect.php";
require_once "requires/lookup.php";

session_start();

if(isset($_POST['type'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $code = '';
    $format = '';
    $material = '';
    $plant = '';

    if (isset($_POST['code']) && $_POST['code'] != ''){
        $code = $_POST['code'];
    }

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    if (isset($_POST['material']) && $_POST['material'] != ''){
        $material = $_POST['material'];
    }

    if (isset($_POST['plant']) && $_POST['plant'] != ''){
        $plant = $_POST['plant'];
    }

    if (isset($_POST['customer']) && $_POST['customer'] != ''){
        $customer = $_POST['customer'];
    }

    if (isset($_POST['supplier']) && $_POST['supplier'] != ''){
        $supplier = $_POST['supplier'];
    }

    $searchQuery = '';
    /*if (isset($_POST['vehicle']) && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
        $searchQuery .= " and veh_number = '".$_POST['vehicle']."'";
    }*/

    if (isset($_POST['transporter']) && $_POST['transporter'] != '' && $_POST['transporter'] != '-'){
        $searchQuery .= " and transporter_name = '".$_POST['transporter']."'";
    }

    if (isset($_POST['customerSupplier']) && $_POST['customerSupplier'] != '' && $_POST['customerSupplier'] != '-'){
        if ($type == 'Purchase'){
            $searchQuery .= " and supplier_name = '".$_POST['customerSupplier']."'";
        }else{
            $searchQuery .= " and customer_name = '".$_POST['customerSupplier']."'";
        }
    }

    if ($format == 'getProdRaw'){
        if ($type == 'Purchase'){
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND status='Open' AND deleted='0'")) {
                $update_stmt->bind_param('s', $code);
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $rawMatCode = $row['raw_mat_code'];
                        $rawMatName = $row['raw_mat_name']; 
                        $suppCode = $row['supplier_code'];
                        $suppName = $row['supplier_name'];

                        // Query for raw mat
                        $rawMatId = '';
                        if (isset($rawMatCode)){
                            $rawMatQuery = "SELECT * FROM Raw_Mat WHERE raw_mat_code = '$rawMatCode' AND status = '0'";
                            $rawMatRecords = mysqli_query($db, $rawMatQuery);
                            $rawMatRow = mysqli_fetch_assoc($rawMatRecords);
                            
                            if(!empty($rawMatRow)){
                              $rawMatId = $rawMatRow['id'];
                            }
                        }
                        
                        $message[] = array(
                            "prodMatId"=>$rawMatId,
                            "prodMatCode"=>$rawMatCode,
                            "prodMatName"=>$rawMatName,
                            "custSuppCode"=>$suppCode,
                            "custSuppName"=>$suppName,
                        );
                        
                    }

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }else{
            if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND status='Open' AND deleted='0'")) {
                $update_stmt->bind_param('s', $code);
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $productCode = $row['product_code'];
                        $productName = $row['product_name']; 
                        $customerCode = $row['customer_code'];
                        $customerName = $row['customer_name'];

                        // Query for product
                        $productId = '';
                        if (isset($productCode)){
                            $prodQuery = "SELECT * FROM Product WHERE product_code = '$productCode' AND status = '0'";
                            $prodRecords = mysqli_query($db, $prodQuery);
                            $prodRow = mysqli_fetch_assoc($prodRecords);
                            
                            if(!empty($prodRow)){
                              $productId = $prodRow['id'];
                            }
                        }
                        
                        $message[] = array(
                            "prodMatId"=>$productId,
                            "prodMatCode"=>$productCode,
                            "prodMatName"=>$productName,
                            "custSuppCode"=>$customerCode,
                            "custSuppName"=>$customerName,
                        );
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }
    }elseif($format == 'getSoPo'){
        if ($type == 'Purchase'){
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE status='Open' AND deleted='0'".$searchQuery)) {
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        if (!in_array($row['po_no'], $message)) {
                            $message[] = $row['po_no'];
                        }
                    }

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }else{
            if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE status='Open' AND deleted='0'".$searchQuery)) {
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        if (!in_array($row['order_no'], $message)) {
                            $message[] = $row['order_no'];
                        }
                    }

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }
    }else{
        $final_weight = [];
        $customerSupplierName = '';
        $destinationName = '';
        $siteName = '';
        $agentName = '';
        $productName = '';
        $plantName = '';
        $balance = 0;
        $order_supplier_weight = 0;
        $converted_order_supplier_weight = 0;
        $converted_order_supplier_unit = 0;
        $unitPrice = 0;

        // $previousRecordsTag = true;
        $count = 1;
    
        if ($type == 'Purchase'){
            //if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND plant_code=? AND status='Open' AND deleted='0'")) {
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND supplier_code=? AND status='Open' AND deleted='0'")) {
                //$update_stmt->bind_param('sss', $code, $material, $plant);
                $update_stmt->bind_param('sss', $code, $material, $supplier);
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $customerSupplierName = $row['supplier_name'];
                        $destinationName = $row['destination_name'];
                        $siteName = $row['site_name'];
                        $agentName = $row['agent_name'];
                        $productName = $row['raw_mat_name'];
                        $plantName = $row['plant_name'];
                        $transporterName = $row['transporter_name'];
                        $vehNo = $row['veh_number'];
                        $exDel = $row['exquarry_or_delivered'];
                        $order_supplier_weight = $row['order_quantity'];
                        $balance = $row['balance'];
                        $converted_order_supplier_weight = $row['converted_order_qty'];
                        $converted_order_supplier_unit = searchUnitById($row['converted_unit'], $db);
                        $converted_order_supplier_unitId = $row['converted_unit'];
                        $unitPrice = $row['unit_price'];
                        $remarks = $row['remarks'];
                    }
    
                    // $empQuery = "SELECT * FROM Weight WHERE status = '0' AND purchase_order = '$code' AND transaction_status = '$type' ORDER BY id ASC"; 
                    // $empRecords = mysqli_query($db, $empQuery);
                    // if (mysqli_num_rows($empRecords) == 0) { // Check if records exist
                    //     // No records found
                    //     $previousRecordsTag = false;
    
                    //     // while ($row = mysqli_fetch_assoc($empRecords)) {
                    //     //     $final_weight[] = !empty($row['final_weight']) ? $row['final_weight'] : 0;
                    //     // }
                    // }
    
                    // prevRecordTag
                    // $finalWeight = array_sum($final_weight);
                    $message['customer_supplier_name'] = $customerSupplierName;
                    $message['destination_name'] = $destinationName;
                    $message['site_name'] = $siteName;
                    $message['agent_name'] = $agentName;
                    $message['product_name'] = $productName;
                    $message['plant_name'] = $plantName;
                    $message['transporter_name'] = $transporterName;
                    $message['veh_number'] = $vehNo;
                    $message['ex_del'] = $exDel;
                    $message['order_supplier_weight'] = $order_supplier_weight;
                    $message['balance'] = $balance;
                    $message['converted_order_supplier_weight'] = $converted_order_supplier_weight;
                    $message['converted_order_supplier_unit'] = $converted_order_supplier_unit;
                    $message['converted_order_supplier_unitId'] = $converted_order_supplier_unitId;
                    $message['unit_price'] = $unitPrice;
                    $message['remarks'] = $remarks;
                    // $message['final_weight'] = $finalWeight;
                    // $message['previousRecordsTag'] = $previousRecordsTag;
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }else{
            //if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND plant_code=?  AND status='Open' AND deleted='0'")) {
            if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND customer_code=? AND status='Open' AND deleted='0'")) {
                //$update_stmt->bind_param('sss', $code, $material, $plant);
                $update_stmt->bind_param('sss', $code, $material, $customer);
                
                // Execute the prepared query.
                if (!$update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $customerSupplierName = $row['customer_name'];
                        $destinationName = $row['destination_name'];
                        $siteName = $row['site_name'];
                        $agentName = $row['agent_name'];
                        $productName = $row['product_name'];
                        $plantName = $row['plant_name'];
                        $transporterName = $row['transporter_name'];
                        $vehNo = $row['veh_number'];
                        $exDel = $row['exquarry_or_delivered'];
                        $order_supplier_weight = $row['order_quantity'];
                        $balance = $row['balance'];
                        $converted_order_supplier_weight = $row['converted_order_qty'];
                        $converted_order_supplier_unit = searchUnitById($row['converted_unit'], $db);
                        $converted_order_supplier_unitId = $row['converted_unit'];
                        $unitPrice = $row['unit_price'];
                        $remarks = $row['remarks'];
                    }  
    
                    // $empQuery = "SELECT * FROM Weight WHERE status = '0' AND purchase_order = '$code' AND transaction_status = '$type' ORDER BY id ASC"; 
                    // $empRecords = mysqli_query($db, $empQuery);
                    // if (mysqli_num_rows($empRecords) == 0) { // Check if records exist
                    //     // No records found
                    //     $previousRecordsTag = false;
    
                    //     // while ($row = mysqli_fetch_assoc($empRecords)) {
                    //     //     $final_weight[] = !empty($row['final_weight']) ? $row['final_weight'] : 0;
                    //     // }
                    // }
    
                    // prevRecordTag
                    // $finalWeight = array_sum($final_weight);
                    $message['customer_supplier_name'] = $customerSupplierName;
                    $message['destination_name'] = $destinationName;
                    $message['site_name'] = $siteName;
                    $message['agent_name'] = $agentName;
                    $message['product_name'] = $productName;
                    $message['plant_name'] = $plantName;
                    $message['transporter_name'] = $transporterName;
                    $message['veh_number'] = $vehNo;
                    $message['ex_del'] = $exDel;
                    $message['order_supplier_weight'] = $order_supplier_weight;
                    $message['balance'] = $balance;
                    $message['converted_order_supplier_weight'] = $converted_order_supplier_weight;
                    $message['converted_order_supplier_unit'] = $converted_order_supplier_unit;
                    $message['converted_order_supplier_unitId'] = $converted_order_supplier_unitId;
                    $message['unit_price'] = $unitPrice;
                    $message['remarks'] = $remarks;
                    // $message['final_weight'] = $finalWeight;
                    // $message['previousRecordsTag'] = $previousRecordsTag;
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>