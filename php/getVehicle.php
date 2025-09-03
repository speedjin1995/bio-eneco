<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    # Enhancement to pull Vehicle by veh_number
    if (empty($_POST["type"])) {
        $type = null;
    } else {
        $type = trim($_POST["type"]);
    }

    if (!empty($type) && $type == 'lookup'){
        if ($veh_chk_stmt = $db->prepare("SELECT COUNT(*) AS COUNT FROM `Weight` WHERE lorry_plate_no1 = ? AND is_complete='N'")){
            $veh_chk_stmt->bind_param('s', $id);
            
            // Execute the prepared query.
            if (! $veh_chk_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $vehicleExist = false;

                $vehResult = $veh_chk_stmt->get_result();
                while ($vehRow = $vehResult->fetch_assoc()) {
                    if ($vehRow['COUNT'] > 0){
                        $vehicleExist = true;
                    }else{
                        $vehicleExist = false;
                    }
                }

                /*if ($vehicleExist){
                    echo json_encode(
                        array(
                            "status" => "error",
                            "message" => 'There is a pending record for this vehicle'
                        ));  
                }else{*/
                    if ($update_stmt = $db->prepare("SELECT Vehicle.* FROM Vehicle WHERE veh_number=? AND status='0'")) {
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
                                $message[] = array(
                                    "id"=>$row['id'],
                                    "veh_number"=>$row['veh_number'],
                                    "vehicle_weight"=>$row['vehicle_weight'],
                                    "transporter_name"=>$row['transporter_name'],
                                    "transporter_code"=>$row['transporter_code'],
                                    "ex_del"=>$row['ex_del'],
                                    "customer_code"=>$row['customer_code'],
                                    "customer_name"=>$row['customer_name'],
                                );
                            }
                            
                            echo json_encode(
                                array(
                                    "status" => "success",
                                    "message" => $message
                                ));   
                        }
                    }
                //}
            }
        }
    }else{
        if ($update_stmt = $db->prepare("SELECT * FROM Vehicle WHERE id=? AND status='0'")) {
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
                    $message['veh_number'] = $row['veh_number'];
                    $message['vehicle_weight'] = $row['vehicle_weight'];
                    $message['transporter_name'] = $row['transporter_name'];
                    $message['transporter_code'] = $row['transporter_code'];
                    $message['ex_del'] = $row['ex_del'];
                    $message['customer_code'] = $row['customer_code'];
                    $message['customer_name'] = $row['customer_name'];
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