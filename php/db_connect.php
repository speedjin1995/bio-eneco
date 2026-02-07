<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kuala_Lumpur');
$db = mysqli_connect("localhost", "shelfking_bioeneco", "@Sync5500", "shelfking_bioeneco");

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}
?>