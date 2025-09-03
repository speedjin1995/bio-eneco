<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['id'], $_POST['statusA'], $_POST['reasons'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$status = filter_input(INPUT_POST, 'statusA', FILTER_SANITIZE_STRING);
	$del = $_POST['reasons'];
	$compl = 'Y';

	if($status=='N'){
		$compl = 'N';
	}

	if ($stmt2 = $db->prepare("UPDATE Weight SET is_complete=?, is_approved=?, approved_reason=? WHERE id=?")) {
		$stmt2->bind_param('ssss', $compl, $status, $del, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> ($status=='Y' ? 'Approved' : 'Rejected')
				)
			);

			// $stmt2->close();
			$db->close();
		} 
		else{
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
