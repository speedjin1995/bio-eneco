<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['id'];
}

if(isset($_POST['userName'], $_POST['userEmail'])){
	$name = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_STRING);
	
	if ($stmt2 = $db->prepare("UPDATE Users SET username=?, useremail=? WHERE employee_code=?")) {
		$stmt2->bind_param('sss', $name, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			echo '<script type="text/javascript">alert("Your Email / Username is updated successfully!");</script>'; 
			header("location: ../myProfile.php");
		} else{
			echo '<script type="text/javascript">alert("Failed to update profile!");</script>'; 
			header("location: ../myProfile.php");
		}
	} 
	else{
		echo '<script type="text/javascript">alert("Failed to prepare statements!");</script>'; 
		header("location: ../myProfile.php");
	}
} 
else{
	echo '<script type="text/javascript">alert("Please fill in all fields!");</script>'; 
	header("location: ../myProfile.php");
}
?>
