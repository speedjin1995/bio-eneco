<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'], $_POST['type'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

    if ($type == 'SO'){
        if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order_Log WHERE order_no=? ORDER BY id DESC LIMIT 1")) {
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
                
                if ($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    $message['id'] = $row['id'];
                    $message['balance'] = $row['balance'];
                    $message['converted_balance'] = $row['converted_balance'];
                    $message['order_quantity'] = $row['order_quantity'];
                    $message['converted_order_qty'] = $row['converted_order_qty'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
        }
    }else{
        if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order_Log WHERE po_no=? ORDER BY id DESC LIMIT 1")) {
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
                
                if ($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    $message['id'] = $row['id'];
                    $message['balance'] = $row['balance'];
                    $message['converted_balance'] = $row['converted_balance'];
                    $message['order_quantity'] = $row['order_quantity'];
                    $message['converted_order_qty'] = $row['converted_order_qty'];
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