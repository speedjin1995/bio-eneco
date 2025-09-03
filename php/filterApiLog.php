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
    $searchQuery = " WHERE created_datetime >= '".$fromDateTime."'";
}

if($_POST['toDateSearch'] != null && $_POST['toDateSearch'] != ''){
    $toDate = new DateTime($_POST['toDateSearch']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
    $searchQuery .= " and created_datetime <= '".$toDateTime."'";
}

if($_POST['selectedValue'] != null && $_POST['selectedValue'] != '' && $_POST['selectedValue'] != '-'){
    $searchQuery .= " and services = '".$_POST['selectedValue']."'";
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
$empQuery = "select * from Api_Log".$searchQuery;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $prettyRequest = !empty($row['request']) && is_array(json_decode($row['request'], true)) ? json_encode(json_decode($row['request'], true), JSON_PRETTY_PRINT) : '';
    $prettyResponse = !empty($row['response']) && is_array(json_decode($row['response'], true)) ? json_encode(json_decode($row['response'], true), JSON_PRETTY_PRINT) : '';
    $prettyError = !empty($row['error_message']) && is_array(json_decode($row['error_message'], true)) ? json_encode(json_decode($row['error_message'], true), JSON_PRETTY_PRINT) : '';
    
    $data[] = array( 
        "No" => $row['id'],
        "Request" => '<pre style="white-space: pre-wrap;">' . htmlspecialchars($prettyRequest) . '</pre>',
        "Response" => '<pre style="white-space: pre-wrap;">' . htmlspecialchars($prettyResponse) . '</pre>',
        "Error Message" => '<pre style="white-space: pre-wrap;">' . htmlspecialchars($prettyError) . '</pre>',
        "Service" => htmlspecialchars($row['services']),
        "Created Date" => $row['created_datetime'],
    );

}

$columnNames = ["No", "Request", "Response", "Error Message", "Service", "Created Date"];

## Response
$response = [
    "columnNames" => $columnNames,
    "dataTable" => $data
];

header("Content-Type: application/json");
echo json_encode($response);
?>