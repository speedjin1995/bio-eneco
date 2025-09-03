<?php
require_once "db_connect.php";
require_once "requires/lookup.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $format = '';

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    if ($format == 'EXPANDABLE'){
        if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE id=?")) {
            $update_stmt->bind_param('s', $id);
            
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
                
                while ($row = $result->fetch_assoc()) {
                    $message['id'] = $row['id'];
                    $message['company_code'] = $row['company_code'] ?? '';
                    $message['company_name'] = $row['company_name'] ?? '';
                    $message['supplier_code'] = $row['supplier_code'] ?? '';
                    $message['supplier_name'] = $row['supplier_name'] ?? '';
                    $message['site_code'] = $row['site_code'] ?? '';
                    $message['site_name'] = $row['site_name'] ?? '';
                    $message['order_date'] = $row['order_date'];
                    $message['order_no'] = $row['order_no'];
                    $message['po_no'] = $row['po_no'];
                    $message['agent_code'] = $row['agent_code'] ?? '';
                    $message['agent_name'] = $row['agent_name'] ?? '';
                    $message['destination_code'] = $row['destination_code'] ?? '';
                    $message['destination_name'] = $row['destination_name'] ?? '';
                    $message['raw_mat_code'] = $row['raw_mat_code'] ?? '';
                    $message['raw_mat_name'] = $row['raw_mat_name'] ?? '';
                    $message['plant_code'] = $row['plant_code'] ?? '';
                    $message['plant_name'] = $row['plant_name'] ?? '';
                    $message['transporter_code'] = $row['transporter_code'] ?? '';
                    $message['transporter_name'] = $row['transporter_name'] ?? '';
                    $message['veh_number'] = $row['veh_number'];
                    if ($row['exquarry_or_delivered'] == 'E'){
                        $message['exquarry_or_delivered'] = 'EX-QUARRY';
                    }else{
                        $message['exquarry_or_delivered'] = 'DELIVERED';
                    }
                    $message['converted_order_qty'] = $row['converted_order_qty'] ?? '';
                    $message['converted_balance'] = $row['converted_balance'] ?? '';
                    $message['converted_unit'] = $row['converted_unit'] ?? '';
                    $message['order_quantity'] = $row['order_quantity'] ?? '';
                    $message['balance'] = $row['balance'] ?? '';
                    $message['unit_price'] = $row['unit_price'] ?? 0;
                    $message['total_price'] = $row['total_price'] ?? 0;
                    $message['remarks'] = $row['remarks'] ?? '';
                    $message['balance'] = $row['balance'];

                    $weightData = array();

                    if($row['po_no'] != null && $row['po_no'] != ''){
                        $poNo = $row['po_no'];
                        $rawMatCode = $row['raw_mat_code'];
                        $plantCode = $row['plant_code'];
                        $weightQuery = "SELECT * FROM Weight WHERE purchase_order = '$poNo' AND raw_mat_code = '$rawMatCode' AND status = '0' AND transaction_status = 'Purchase' ORDER BY id ASC";
                        $weightRecords = mysqli_query($db, $weightQuery);

                        while($weightRow = mysqli_fetch_assoc($weightRecords)) {
                            $weightData[] = array(
                                "id" => $weightRow['id'],
                                "transaction_id" => $weightRow['transaction_id'],
                                "raw_mat_code" => $weightRow['raw_mat_code'],
                                "raw_mat_name" => $weightRow['raw_mat_name'],
                                "delivery_no" => $weightRow['delivery_no'] ?? '',
                                "lorry_plate_no1" => $weightRow['lorry_plate_no1'],
                                "nett_weight1" => $weightRow['nett_weight1'],
                                "created_by" => searchNamebyId($weightRow['created_by'], $db)
                            );
                        }   
                    }

                    $message['weights'] = $weightData;
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
        }
    }else{
        if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE id=?")) {
            $update_stmt->bind_param('s', $id);
            
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
                
                while ($row = $result->fetch_assoc()) {
                    $message['id'] = $row['id'];
                    $message['company_code'] = $row['company_code'];
                    $message['supplier_code'] = $row['supplier_code'];
                    $message['site_code'] = $row['site_code'];
                    $message['order_date'] = $row['order_date'];
                    $message['order_no'] = $row['order_no'];
                    $message['po_no'] = $row['po_no'];
                    $message['agent_code'] = $row['agent_code'];
                    $message['destination_code'] = $row['destination_code'];
                    $message['raw_mat_code'] = $row['raw_mat_code'];
                    $message['raw_mat_name'] = $row['raw_mat_name'];
                    $message['plant_code'] = $row['plant_code'];
                    $message['transporter_code'] = $row['transporter_code'];
                    $message['veh_number'] = $row['veh_number'];
                    $message['exquarry_or_delivered'] = $row['exquarry_or_delivered'];
                    $message['converted_order_qty'] = $row['converted_order_qty'];
                    $message['converted_balance'] = $row['converted_balance'];
                    $message['converted_unit'] = $row['converted_unit'];
                    $message['converted_unit_text'] = searchUnitById($row['converted_unit'], $db);
                    $message['order_quantity'] = $row['order_quantity'];
                    $message['balance'] = $row['balance'];
                    $message['unit_price'] = $row['unit_price'];
                    $message['total_price'] = $row['total_price'];
                    $message['remarks'] = $row['remarks'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
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