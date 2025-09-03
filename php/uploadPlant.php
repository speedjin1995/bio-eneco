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
        $PlantCode = !empty($rows['PlantCode']) ? trim($rows['PlantCode']) : '';
        $PlantName = !empty($rows['PlantName']) ? trim($rows['PlantName']) : '';
        $AddressLine1 = !empty($rows['AddressLine1']) ? trim($rows['AddressLine1']) : '';
        $AddressLine2 = !empty($rows['AddressLine2']) ? trim($rows['AddressLine2']) : '';
        $AddressLine3 = !empty($rows['AddressLine3']) ? trim($rows['AddressLine3']) : '';
        $PhoneNo = !empty($rows['PhoneNo']) ? trim($rows['PhoneNo']) : '';
        $FaxNo = !empty($rows['FaxNo']) ? trim($rows['FaxNo']) : '';
        
        # Check if plant exist in DB
        $status = "0";
        $plantQuery = "SELECT * FROM Plant WHERE plant_code = '$PlantCode' AND status = '$status'";
        $plantDetail = mysqli_query($db, $plantQuery);
        $plantRow = mysqli_fetch_assoc($plantDetail);

        if(empty($plantRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Plant (plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssss', $PlantCode, $PlantName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $uid, $uid);
                $insert_stmt->execute();
                $plantId = $insert_stmt->insert_id; // Get the inserted unit ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Plant_Log (plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssssss', $plantId, $PlantCode, $PlantName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $action, $uid);
                    $insert_log->execute();
                    $insert_log->close();
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
