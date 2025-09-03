<?php
## Database configuration
require_once 'db_connect.php';

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
if($searchValue != ''){
  $searchQuery = " and (lorry_plate_no1 like '%".$searchValue."%' or customer_code like '%".$searchValue."%' or customer_name like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from Weight WHERE received = 'N' AND transaction_status = 'Local'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from Weight WHERE received = 'N' AND transaction_status = 'Local'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from Weight WHERE received = 'N' AND transaction_status = 'Local'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "id"=>$row['id'],
      "transaction_id"=>$row['transaction_id'],
      "lorry_plate_no1"=>$row['lorry_plate_no1'],
      "customer_code"=>$row['customer_code'],
      "customer_name"=>$row['customer_name'],
      "gross_weight1"=>$row['gross_weight1'],
      "gross_weight1_date"=>$row['gross_weight1_date'],
      "tare_weight1"=>$row['tare_weight1'],
      "tare_weight1_date"=>$row['tare_weight1_date'],
      "nett_weight1"=>$row['nett_weight1']
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "query" => $empQuery
);

echo json_encode($response);

?>