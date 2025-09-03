<?php
## Database configuration
require_once 'db_connect.php';
session_start();

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and Inventory.plant_code = '".$_POST['plant']."'";
}

## Total number of records without filtering
$allQuery = "select count(*) as allcount from Inventory, Raw_Mat where Inventory.status = '0' and Inventory.raw_mat_id = Raw_Mat.id".$searchQuery;
$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Inventory, Raw_Mat where Inventory.status = '0' and Inventory.raw_mat_id = Raw_Mat.id".$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select Inventory.*, Raw_Mat.raw_mat_code, Raw_Mat.name from Inventory, Raw_Mat where Inventory.status = '0' and Inventory.raw_mat_id = Raw_Mat.id".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$salesCount = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "no"=>$salesCount,
    "raw_mat_code"=>$row['raw_mat_code'],
    "name"=>$row['name'],
    "raw_mat_weight"=>$row['raw_mat_weight'],
    "raw_mat_count"=>$row['raw_mat_count']
  );

  $salesCount++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>