<?php
require_once "db_connect.php";
require_once "requires/lookup.php";

session_start();

if(isset($_POST['userID'], $_POST['type'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $status = 0;
    $action = '';
    $unitID = 2;

    if (isset($_POST['action']) && $_POST['action'] != ''){
        $action = $_POST['action'];
    }

    if (isset($_POST['unitID']) && $_POST['unitID'] != ''){
        $unitID = $_POST['unitID'];
    }

    if ($action == 'getBasicUOM'){
        if ($type == 'SO'){
            if ($update_stmt = $db->prepare("SELECT * FROM Product WHERE product_code=? AND status=?")) {
                $update_stmt->bind_param('ss', $id, $status);
                
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
                        $message['basic_uom'] = searchUnitById($row['basic_uom'], $db);
                        $message['basic_uom_id'] = $row['basic_uom'];
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
        }elseif($type == 'PO'){
            if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=? AND status=?")) {
                $update_stmt->bind_param('ss', $id, $status);
                
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
                        $message['basic_uom'] = searchUnitById($row['basic_uom'], $db);
                        $message['basic_uom_id'] = $row['basic_uom'];
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
        }
    }else{
        if ($type == 'SO'){
            if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id=? AND status=?")) {
                $update_stmt->bind_param('sss', $id, $unitID, $status);
                
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
                        $message['product_id'] = $row['product_id'];
                        $message['unit_id'] = $row['unit_id'];
                        $message['rate'] = $row['rate'];
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
        }elseif($type == 'PO'){
            if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id=? AND status=?")) {
                $update_stmt->bind_param('sss', $id, $unitID, $status);
                
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
                        $message['raw_mat_id'] = $row['raw_mat_id'];
                        $message['unit_id'] = $row['unit_id'];
                        $message['rate'] = $row['rate'];
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
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>