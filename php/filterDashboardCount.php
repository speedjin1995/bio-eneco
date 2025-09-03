<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and transaction_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and transaction_date <= '".$toDateTime."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

## Fetch records
$empQuery = "
  SELECT 'Pending' AS status, COUNT(*) AS count FROM Weight WHERE is_complete='N' AND is_cancel <> 'Y' AND status = '0'".$searchQuery."
  UNION ALL 
  SELECT 'Complete' AS status, COUNT(*) AS count FROM Weight WHERE is_complete='Y' AND is_cancel <> 'Y' AND status = '0'".$searchQuery."
  UNION ALL 
  SELECT 'Cancelled' AS status, COUNT(*) AS count FROM Weight WHERE is_cancel = 'Y' AND status = '0'".$searchQuery."
";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

## Process results
$pendingCount = 0;
$completeCount = 0;
$cancelledCount = 0;

while ($row = mysqli_fetch_assoc($empRecords)) {
  if ($row['status'] == 'Pending'){
    $pendingCount = $row['count'];
  }elseif ($row['status'] == 'Complete') {
    $completeCount = $row['count'];
  }elseif ($row['status'] == 'Cancelled') {
    $cancelledCount = $row['count'];
  }
}

## Format data for DataTable
$data = [
  [
    "Pending" => $pendingCount,
    "Complete" => $completeCount,
    "Cancelled" => $cancelledCount,
  ]
];

## Response
$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>