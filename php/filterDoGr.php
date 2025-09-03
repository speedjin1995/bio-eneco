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
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d H:i:00');
  $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d H:i:59');
	$searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier_code = '".$_POST['supplier']."'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_code = '".$_POST['product']."'";
}

if($_POST['rawMaterial'] != null && $_POST['rawMaterial'] != '' && $_POST['rawMaterial'] != '-'){
	$searchQuery .= " and raw_mat_code = '".$_POST['rawMaterial']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['purchaseOrder'] != null && $_POST['purchaseOrder'] != '' && $_POST['purchaseOrder'] != '-'){
	$searchQuery .= " and purchase_order = '".$_POST['purchaseOrder']."'";
}

if($searchValue != ''){
  $searchQuery = " and (transaction_id like '%".$searchValue."%' or lorry_plate_no1 like '%".$searchValue."%')";
}

$allQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND transaction_status = '".$_POST['status']."' group by purchase_order";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = implode("', '", $_SESSION["plant"]);
  $allQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND transaction_status = '".$_POST['status']."' and plant_code IN ('$username') group by purchase_order";
}

$sel = mysqli_query($db, $allQuery); 
// $records = mysqli_fetch_assoc($sel);
// $totalRecords = $records['allcount'];
$totalRecords = 0;
while($row2 = mysqli_fetch_assoc($sel)) {
  $totalRecords++;
}

## Total number of record with filtering
// $filteredQuery = "select count(*) as allcount from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by purchase_order";
// if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
//   $username = implode("', '", $_SESSION["plant"]);
//   $filteredQuery = "select count(*) as allcount from Weight where is_complete = 'Y' AND is_cancel <> 'Y' and plant_code IN ('$username')".$searchQuery;
// }

// $sel = mysqli_query($db, $filteredQuery);
// $records = mysqli_fetch_assoc($sel);
// $totalRecordwithFilter = $records['allcount']; 

## Fetch records
$empQuery = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by purchase_order order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
// var_dump($empQuery);
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = implode("', '", $_SESSION["plant"]);
  $empQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username')".$searchQuery." group by purchase_order order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}

$empRecords = mysqli_query($db, $empQuery); 
$data = array();
$totalRecordwithFilter = 0;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "transaction_id"=>$row['transaction_id'],
    "transaction_status"=>$row['transaction_status'],
    "weight_type"=>$row['weight_type'],
    "transaction_date"=>$row['transaction_date'],
    "customer_code"=>$row['customer_code'],
    "customer_name"=>$row['customer_name'],
    "supplier_name"=>$row['supplier_name'],
    "customer"=>($row['transaction_status'] == 'Sales' ? $row['customer_name'] : $row['supplier_name']),
    "product_code"=>($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
    "product_name"=>($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
    "purchase_order"=>$row['purchase_order'],
    "plant_code"=>$row['plant_code'],
    "plant_name"=>$row['plant_name'],
    "delivery_no"=>$row['delivery_no'],
    "order_weight"=>$row['order_weight'],
    "supplier_weight"=>$row['supplier_weight'],
    "po_supply_weight"=>$row['po_supply_weight'],
    "tare_weight1_date"=>$row['tare_weight1_date'],
    "created_date"=>$row['created_date'],
    "created_by"=>$row['created_by'],
    "modified_date"=>$row['modified_date'],
    "modified_by"=>$row['modified_by']
  );
  $totalRecordwithFilter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "sql" => $empQuery
);

echo json_encode($response);

?>