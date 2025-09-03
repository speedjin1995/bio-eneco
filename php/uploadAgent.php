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
        $SRPCode = !empty($rows['SRPCode']) ? trim($rows['SRPCode']) : '';
        $SRPName = !empty($rows['SRPName']) ? trim($rows['SRPName']) : '';
        $Remark = !empty($rows['Remark']) ? trim($rows['Remark']) : '';
        $status = '0';
        $actionId = 1;

        # Checking for existing agent code.
        if($SRPCode != null && $SRPCode != ''){
            $agentQuery = "SELECT * FROM Agents WHERE agent_code = '$SRPCode' AND status = '0'";
            $agentDetail = mysqli_query($db, $agentQuery);
            $agentRow = mysqli_fetch_assoc($agentDetail);

            if(empty($agentRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Agents (agent_code, name, description, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssss', $SRPCode, $SRPName, $Remark, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $agentId = $insert_stmt->insert_id;
                    $insert_stmt->close(); 

                    if ($insert_log = $db->prepare("INSERT INTO Agents_Log (agent_id, agent_code, name, description, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                        $insert_log->bind_param('ssssss', $agentId, $SRPCode, $SRPName, $Remark, $actionId, $uid);
                        $insert_log->execute();
                        $insert_log->close();
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
