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
        $Unit = !empty($rows['Unit']) ? trim($rows['Unit']) : '';

        # Check if unit exist in DB
        $status = "0";
        $unitQuery = "SELECT * FROM Unit WHERE unit = '$Unit' AND status = '$status'";
        $unitDetail = mysqli_query($db, $unitQuery);
        $unitRow = mysqli_fetch_assoc($unitDetail);

        if(empty($unitRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Unit (unit, created_by, modified_by) VALUES (?, ?, ?)")) {
                $insert_stmt->bind_param('sss', $Unit, $uid, $uid);
                $insert_stmt->execute();
                $unitId = $insert_stmt->insert_id; // Get the inserted unit ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Unit_Log (unit_id, unit, action_id, action_by) VALUES (?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssss', $unitId, $Unit, $action, $uid);
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
