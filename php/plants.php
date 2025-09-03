<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
if (isset($_POST['plantCode'], $_POST['plantName'])) {

    if (empty($_POST["id"])) {
        $transporterId = null;
    } else {
        $transporterId = trim($_POST["id"]);
    }

    if (empty($_POST["plantCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["plantCode"]);
    }

    if (empty($_POST["plantName"])) {
        $companyName = null;
    } else {
        $companyName = trim($_POST["plantName"]);
    }

    if (empty($_POST["addressLine1"])) {
        $addressLine1 = null;
    } else {
        $addressLine1 = trim($_POST["addressLine1"]);
    }

    if (empty($_POST["addressLine2"])) {
        $addressLine2 = null;
    } else {
        $addressLine2 = trim($_POST["addressLine2"]);
    }

    if (empty($_POST["addressLine3"])) {
        $addressLine3 = null;
    } else {
        $addressLine3 = trim($_POST["addressLine3"]);
    }

    if (empty($_POST["phoneNo"])) {
        $phoneNo = null;
    } else {
        $phoneNo = trim($_POST["phoneNo"]);
    }

    if (empty($_POST["faxNo"])) {
        $faxNo = null;
    } else {
        $faxNo = trim($_POST["faxNo"]);
    }

    if (empty($_POST["defaultType"])) {
        $defaultType = null;
    } else {
        $defaultType = trim($_POST["defaultType"]);
    }

    if (empty($_POST["runningNoS"])) {
        $runningNoS = 0;
    } else {
        $runningNoS = trim($_POST["runningNoS"]);
    }

    /*if (empty($_POST["runningNoP"])) {
        $runningNoP = 0;
    } else {
        $runningNoP = trim($_POST["runningNoP"]);
    }

    if (empty($_POST["runningNoIt"])) {
        $runningNoIt = 0;
    } else {
        $runningNoIt = trim($_POST["runningNoIt"]);
    }

    if (empty($_POST["runningNoItr"])) {
        $runningNoItr = 0;
    } else {
        $runningNoItr = trim($_POST["runningNoItr"]);
    }*/
    
    if(! empty($transporterId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Plant SET plant_code=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, default_type=?, do_no=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssssss', $transporterCode, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $defaultType, $runningNoS, $username, $username, $transporterId);

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
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Plant (plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, default_type, do_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $transporterCode, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $defaultType, $runningNoS, $username, $username);

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
                $plantId = $insert_stmt->insert_id; // Get the inserted plant ID

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }
            
            $insert_stmt->close();
            $db->close();
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