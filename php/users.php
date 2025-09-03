<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['username'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['employeeCode'], $_POST['username'], $_POST['useremail'], $_POST['roles'], $_POST['allowManual'])){
    $id = $_SESSION['id'];
    $name = $_SESSION["username"];

    $param_code = null;
    $password = filter_input(INPUT_POST, 'userpassword', FILTER_SANITIZE_STRING);
    // $password = "123456";
    $param_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $param_useremail = filter_input(INPUT_POST, 'useremail', FILTER_SANITIZE_STRING);
    $param_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $param_role = filter_input(INPUT_POST, 'roles', FILTER_SANITIZE_STRING);
    $param_allowmanual = filter_input(INPUT_POST, 'allowManual', FILTER_SANITIZE_STRING);
    
    if(isset($_POST['employeeCode']) && $_POST['employeeCode'] != null){
        $param_code = filter_input(INPUT_POST, 'employeeCode', FILTER_SANITIZE_STRING);
    }

    $param_plant = array();

    if(isset($_POST['plantId']) && $_POST['plantId'] != null){
        $param_plant = $_POST['plantId'];
    }

    $param_plant = json_encode($param_plant);
    $param_created_by = $name;
    $param_modified_by = $name;

    if($_POST['id'] != null && $_POST['id'] != ''){
        // Generate new password if password is not empty
        if ($password != '' && $password != null) {
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_token = bin2hex(random_bytes(50)); // generate unique token
        } else {
            // If user exists, get the existing user details
            if ($user_stmt = $db->prepare("SELECT * FROM Users WHERE id=?")){
                $user_stmt->bind_param("s", $_POST['id']);
                $user_stmt->execute();
                $result = $user_stmt->get_result();
                $user = $result->fetch_assoc();

                if ($user) {
                    // If the user exists, $param_password will be the existing password
                    $param_password = $user['password'];
                }
                $user_stmt->close();
            }
        }

        if ($update_stmt = $db->prepare("UPDATE Users SET username=?, name=?, useremail=?, password=?, role=?, modified_by=?, plant_id=?, allow_manual=?, employee_code=? WHERE id=?")) {
            $update_stmt->bind_param("ssssssssss", $param_username, $param_name, $param_useremail, $param_password, $param_role, $param_modified_by, $param_plant, $param_allowmanual, $param_code, $_POST['id']);
            $action = "2";
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
            $db->close();
        }
    }
    else{
        // Generate new password if password is not empty
        if ($password != '' && $password != null) {
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_token = bin2hex(random_bytes(50)); // generate unique token
        } else {
            // If password is empty, throw error
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Password is required when adding a new user"
                )
            );

            return;
        }

        if ($insert_stmt = $db->prepare("INSERT INTO Users (employee_code, useremail, username, name, password, token, role, plant_id, allow_manual, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param("sssssssssss", $param_code, $param_useremail, $param_username, $param_name, $param_password, $param_token, $param_role, $param_plant, $param_allowmanual, $param_created_by, $param_modified_by);
            $action = "1";

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                echo json_encode(
                    array(
                        "status" => "success", 
                        "message" => "Added Successfully!!",
                        "plants" => $param_plant
                    )
                );
            }

            $insert_stmt->close();
            $db->close();
        }
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>