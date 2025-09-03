<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['userID'], $_POST['type'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$status = "Open";

	if ($type == 'Sales'){
		$sql = "UPDATE Sales_Order SET status=? WHERE id=?";
	}else{
		$sql = "UPDATE Purchase_Order SET status=? WHERE id=?";
	}

	if ($stmt2 = $db->prepare($sql)) {
		$stmt2->bind_param('ss', $status, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Open"
				)
			);
		} else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somethings wrong"
	        )
	    );
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
