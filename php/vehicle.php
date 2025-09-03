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
if (isset($_POST['vehicleNo'])) {

    if (empty($_POST["id"])) {
        $vehicleId = null;
    } else {
        $vehicleId = trim($_POST["id"]);
    }

    if (empty($_POST["vehicleNo"])) {
        $vehicleNo = null;
    } else {
        $vehicleNo = trim($_POST["vehicleNo"]);
    }

    if (empty($_POST["vehicleWeight"])) {
        $vehicleWeight = 0;
    } else {
        $vehicleWeight = trim($_POST["vehicleWeight"]);
    }

    if (empty($_POST["exDel"])) {
        $exDel = null;
    } else {
        if ($_POST["exDel"] == 'true'){
            $exDel = 'EX';
        }else{
            $exDel = 'DEL';
        }
    }

    if (empty($_POST["transporterName"])) {
        $transporter = null;
    } else {
        $transporter = trim($_POST["transporterName"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["customer"])) {
        $customer = null;
    } else {
        $customer = trim($_POST["customer"]);
    }

    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if(! empty($vehicleId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Vehicle SET veh_number=?, vehicle_weight=?, transporter_code=?, transporter_name=?, ex_del=?, customer_code=?, customer_name=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssss', $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $username, $username, $vehicleId);

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

        # Check if vehicle no with both E & D exist
        $vehicleQuery = "SELECT COUNT(*) AS count FROM Vehicle WHERE veh_number = '$vehicleNo' AND status = '0' AND ex_del IN ('EX', 'DEL')";
        $vehicleDetail = mysqli_query($db, $vehicleQuery);
        $vehicleRow = mysqli_fetch_assoc($vehicleDetail);
        $vehicleCount = (int) $vehicleRow['count'];

        if ($vehicleCount > 1){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "This vehicle is already exist"
                )
            );
        }else{
            if ($insert_stmt = $db->prepare("INSERT INTO Vehicle (veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssss', $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $username, $username);
    
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
                    $vehicleId = $insert_stmt->insert_id;
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