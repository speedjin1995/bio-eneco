<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['cronjobName'], $_POST['cronjobFile'], $_POST['duration'], $_POST['unit'])) {

    if (empty($_POST["id"])) {
        $id = null;
    } else {
        $id = trim($_POST["id"]);
    }

    if (empty($_POST["cronjobName"])) {
        $cronjobName = null;
    } else {
        $cronjobName = trim($_POST["cronjobName"]);
    }

    if (empty($_POST["cronjobFile"])) {
        $cronjobFile = null;
    } else {
        $cronjobFile = trim($_POST["cronjobFile"]);
    }

    if (empty($_POST["duration"])) {
        $duration = null;
    } else {
        $duration = trim($_POST["duration"]);
    }

    if (empty($_POST["unit"])) {
        $unit = null;
    } else {
        $unit = trim($_POST["unit"]);
    }

    if(! empty($id))
    {
        if ($update_stmt = $db->prepare("UPDATE Cronjob_Table SET cronjob_name=?, cronjob_file=?, duration=?, unit=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssss', $cronjobName, $cronjobFile, $duration, $unit, $id);

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
                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Cronjob_Table (cronjob_name, cronjob_file, duration, unit) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $cronjobName, $cronjobFile, $duration, $unit);

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
                $insert_stmt->close();
                $db->close();
                
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );

            }
        }
    }
    
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>