<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Read value
// $draw = $_POST['draw'];
// $row = $_POST['start'];
// $rowperpage = $_POST['length']; // Rows display per page
// $columnIndex = $_POST['order'][0]['column']; // Column index
// $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
// $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
// $searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDateSearch'] != null && $_POST['fromDateSearch'] != ''){
    $fromDate = new DateTime($_POST['fromDateSearch']);
    $fromDateTime = date_format($fromDate,"Y-m-d 00:00:00");
    $searchQuery = " and declaration_datetime >= '".$fromDateTime."'";
}

if($_POST['toDateSearch'] != null && $_POST['toDateSearch'] != ''){
    $toDate = new DateTime($_POST['toDateSearch']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
    $searchQuery .= " and declaration_datetime <= '".$toDateTime."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '')
{
    $searchQuery .= " and plant_id = '".$_POST['plant']."'";
}


## Total number of records without filtering
// $sel = mysqli_query($db,"select count(*) as allcount from Customer_Log");
// $records = mysqli_fetch_assoc($sel);
// $totalRecords = $records['allcount'];

// ## Total number of record with filtering
// $sel = mysqli_query($db,"select count(*) as allcount from Customer_Log".$searchQuery);
// $records = mysqli_fetch_assoc($sel);
// $totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from Stock_Take WHERE status = '0'".$searchQuery." order by declaration_datetime asc";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
        "id"=>$row['id'],
        "declaration_datetime"=>$row['declaration_datetime'],
        "plant"=>$row['plant_id'],
        "sixty_seventy_production"=>$row['sixty_seventy_production'],
        "sixty_seventy_os"=>$row['sixty_seventy_os'],
        "sixty_seventy_incoming"=>$row['sixty_seventy_incoming'],
        "sixty_seventy_usage"=>$row['sixty_seventy_usage'],
        "sixty_seventy_bookstock"=>$row['sixty_seventy_bookstock'],
        "sixty_seventy_ps"=>$row['sixty_seventy_ps'],
        "sixty_seventy_diffstock"=>$row['sixty_seventy_diffstock'],
        "sixty_seventy_actual_usage"=>$row['sixty_seventy_actual_usage'],
        "lfo_production"=>$row['lfo_production'],
        "lfo_os"=>$row['lfo_os'],
        "lfo_incoming"=>$row['lfo_incoming'],
        "lfo_ps"=>$row['lfo_ps'],
        "lfo_usage"=>$row['lfo_usage'],
        "lfo_actual_usage"=>$row['lfo_actual_usage'],
        "diesel_production"=>$row['diesel_production'],
        "diesel_os"=>$row['diesel_os'],
        "diesel_incoming"=>$row['diesel_incoming'],
        "diesel_mreading"=>$row['diesel_mreading'],
        "diesel_transport"=>$row['diesel_transport'],
        "diesel_ps"=>$row['diesel_ps'],
        "diesel_usage"=>$row['diesel_usage'],
        "diesel_actual_usage"=>$row['diesel_actual_usage'],
    );
}

## Response
$response = array(
//   "draw" => intval($draw),
//   "iTotalRecords" => $totalRecords,
//   "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
);

echo json_encode($response);

?>