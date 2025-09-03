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
        $EmployeeCode = !empty($rows['EmployeeCode']) ? trim($rows['EmployeeCode']) : '';
        $Username = !empty($rows['Username']) ? trim($rows['Username']) : '';
        $Name = !empty($rows['UserName']) ? trim($rows['UserName']) : '';
        $UserEmail = !empty($rows['UserEmail']) ? trim($rows['UserEmail']) : '';
        $Role = !empty($rows['Role']) ? trim($rows['Role']) : '';
        $password = "123456";
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
        $param_token = bin2hex(random_bytes(50)); // generate unique token

        # Check if employee exist in DB
        $status = "0";
        $userQuery = "SELECT * FROM Users WHERE employee_code = '$EmployeeCode' AND status = '0'";
        $userDetail = mysqli_query($db, $userQuery);
        $userRow = mysqli_fetch_assoc($userDetail);

        if(empty($userRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Users (employee_code, username, name, useremail, role, password, token, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssss', $EmployeeCode, $Username, $Name, $UserEmail, $Role, $param_password, $param_token, $uid, $uid);
                $insert_stmt->execute();
                $userId = $insert_stmt->insert_id; // Get the inserted unit ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Users_Log (user_id, employee_code, username, name, useremail, user_department, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssss', $userId, $EmployeeCode, $Username, $Name, $UserEmail, $Role, $action, $uid);
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
