<?php
require_once "db_connect.php";
require_once "requires/lookup.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT Inventory.*, Raw_Mat.raw_mat_code, Raw_Mat.name from Inventory, Raw_Mat WHERE Inventory.raw_mat_id = Raw_Mat.id AND Inventory.id=?")) {
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
                $message['raw_mat_code'] = $row['raw_mat_code'];
                $message['name'] = $row['name'];
                $message['raw_mat_basic_uom'] = $row['raw_mat_basic_uom'];
                $message['raw_mat_weight'] = $row['raw_mat_weight'];
                $message['raw_mat_count'] = $row['raw_mat_count'];

                $rawMatCode = $row['raw_mat_code'];
                $status = '0';

                if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=? AND status=?")) {
                    $update_stmt->bind_param('ss', $rawMatCode, $status);
                    
                    // Execute the prepared query.
                    if (! $update_stmt->execute()) {
                        echo json_encode(
                            array(
                                "status" => "failed",
                                "message" => "Something went wrong"
                            )); 
                    }
                    else{
                        $result2 = $update_stmt->get_result();
                        
                        while ($row2 = $result2->fetch_assoc()) {
                            $message['basic_uom'] = searchUnitById($row2['basic_uom'], $db);
                            $message['basic_uom_id'] = $row2['basic_uom'];
                            $message['raw_mat_id'] = $row2['id'];
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
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>