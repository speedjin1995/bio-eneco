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

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and declaration_datetime >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and declaration_datetime <= '".$toDateTime."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_id = '".$_POST['plant']."'";
}

## Total number of records without filtering
$allQuery = "select count(*) as allcount from Bitumen where status = '0'".$searchQuery;
$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Bitumen where status = '0'".$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select Bitumen.*, Plant.name as plant from Bitumen JOIN Plant ON Bitumen.plant_id = Plant.id where Bitumen.status = '0'".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$salesCount = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  // Process 60/70 data
  $sixtySeventyTemp = json_decode($row['60/70'], true);
  $totalSixtySeventy = $sixtySeventyTemp["totalSixtySeventy"] ?? 0;
  $totalTemperature = $sixtySeventyTemp["totalTemperature"] ?? 0;
  $totalLevel = $sixtySeventyTemp["totalLevel"] ?? 0;

  // Process LFO data
  $lfoTemp = json_decode($row['lfo'], true);
  $totalLfo = $lfoTemp["totalLfo"] ?? 0;

  // Process Diesel data
  $dieselTemp = json_decode($row['diesel'], true);
  $totalDiesel = $dieselTemp["totalDiesel"] ?? 0;

  // Process Hotoil data
  $hotoilTemp = json_decode($row['hotoil'], true);
  $totalHotoil = $hotoilTemp["totalHotoil"] ?? 0;

  // Process PG76 data
  $pg79Temp = json_decode($row['pg76'], true);
  $totalPgSevenNine = $pg79Temp["totalPgSevenNine"] ?? 0;

  $data[] = array( 
    "id"=>$row['id'],
    "no"=>$salesCount,
    "plant"=>$row['plant'],
    "totalSixtySeventy"=>$totalSixtySeventy,
    "totalTemperature"=>$totalTemperature,
    "totalLevel"=>$totalLevel,
    "totalLfo"=>$totalLfo,
    "totalDiesel"=>$totalDiesel,
    "totalHotoil"=>$totalHotoil,
    "totalPgSevenNine"=>$totalPgSevenNine,
    "crmb"=>$row['crmb'],
    "declaration_datetime"=>$row['declaration_datetime'],
    "created_datetime"=>$row['created_datetime']
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