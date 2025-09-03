<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE id=?")) {
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
                $message['product_code'] = $row['raw_mat_code'];
                $message['name'] = $row['name'];
                $message['price'] = $row['price'];
                $message['description'] = $row['description'];
                $message['variance'] = $row['variance'];
                $message['high'] = $row['high'];
                $message['low'] = $row['low'];
                $message['type'] = $row['type'];
                $message['basic_uom'] = $row['basic_uom'];
            }

            // retrieve uom
            $empQuery = "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id = $id AND status = '0' ORDER BY id ASC";
            $empRecords = mysqli_query($db, $empQuery);
            $rawMatUom = array();
            $rawMatUomCount = 1;

            while($row2 = mysqli_fetch_assoc($empRecords)) {
                $rawMatUom[] = array(
                    "no" => $rawMatUomCount,
                    "id" => $row2['id'],
                    "raw_mat_id" => $row2['raw_mat_id'],
                    "unit_id" => $row2['unit_id'],
                    "rate" => $row2['rate'],
                );
                $rawMatUomCount++;
            }

            $message['rawMatUom'] = $rawMatUom;
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
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