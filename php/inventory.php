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
if (isset($_POST['basicUom'], $_POST['weight'], $_POST['drum'])) {

    if (empty($_POST["id"])) {
        $transporterId = null;
    } else {
        $transporterId = trim($_POST["id"]);
    }

    if (empty($_POST["basicUom"])) {
        $basicUom = '0';
    } else {
        $basicUom = trim($_POST["basicUom"]);
    }

    if (empty($_POST["weight"])) {
        $weight = '0';
    } else {
        $weight = trim($_POST["weight"]);
    }

    if (empty($_POST["drum"])) {
        $drum = '0';
    } else {
        $drum = trim($_POST["drum"]);
    }
    
    if(! empty($transporterId)){
        if ($update_stmt = $db->prepare("UPDATE Inventory SET raw_mat_basic_uom=?, raw_mat_weight=?, raw_mat_count=? WHERE id=?")) {
            $update_stmt->bind_param('ssss', $basicUom, $weight, $drum, $transporterId);

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