<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $rows) {
        $DestinationCode = !empty($rows['DestinationCode']) ? trim($rows['DestinationCode']) : '';
        $DestinationName = !empty($rows['DestinationName']) ? trim($rows['DestinationName']) : '';
        $Description = !empty($rows['Description']) ? trim($rows['Description']) : '';
        $misValue = '';

        # Check if destination code exist in DB
        $status = "0";
        $destinationQuery = "SELECT * FROM Destination WHERE destination_code = '$DestinationCode' AND status = '$status'";
        $destinationDetail = mysqli_query($db, $destinationQuery);
        $destinationRow = mysqli_fetch_assoc($destinationDetail);

        if(empty($destinationRow)){
            if (empty($DestinationCode)){
                $code = 'destination';
                $firstChar = substr($DestinationName, 0, 1);
                if (ctype_alpha($firstChar)) { //Check if letter is alphabet 
                    $firstChar = strtoupper($firstChar);
                }

                // Auto gen destination code
                if($update_stmt2 = $db->prepare("SELECT * FROM Miscellaneous WHERE code=? AND name=?")){
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
            }

            if ($insert_stmt = $db->prepare("INSERT INTO Destination (destination_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssss', $DestinationCode, $DestinationName, $Description, $uid, $uid);
                $insert_stmt->execute();
                $desId = $insert_stmt->insert_id; // Get the inserted destination ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, description, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssss', $desId, $DestinationCode, $DestinationName, $description, $action, $uid);
                    $insert_log->execute();
                    $insert_log->close();
                }            
                
                // Update miscellaneous
                if(!empty($misValue)){
                    if ($update_miscellaneous = $db->prepare("UPDATE Miscellaneous SET value=? WHERE code=? AND name=?")) {
                        $update_miscellaneous->bind_param('sss', $misValue, $code, $firstChar);
                        $update_miscellaneous->execute();
                        $update_miscellaneous->close();
                    } 
                }
                           
            }
        }
        
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
