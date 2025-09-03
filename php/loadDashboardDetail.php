<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['status'] != null && $_POST['status'] != ''){
    if ($_POST['status'] == 'Pending'){
        $searchQuery = " and is_complete = 'N' and is_cancel <> 'Y'";
    }elseif($_POST['status'] == 'complete'){
        $searchQuery = " and status = 'Complete'";
    }elseif ($_POST['status'] == 'cancel') {
        $searchQuery = " and status = 'Cancelled'";
    }
}

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
    $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
    $searchQuery .= " and transaction_date >= '".$fromDateTime."'";  
    $fromDate = $dateTime;
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
    $toDateTime = $dateTime->format('Y-m-d 23:59:59');
    $searchQuery .= " and transaction_date <= '".$toDateTime."'";
    $toDate = $dateTime;
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

$months = calcDateDifference($fromDate, $toDate);

if(empty($months)){
    $columns = ["column_1"]; // Start with the first static column
    $columns[] = "last_column"; // Always add the last column for "Total Nett & Total Price"
    $data = [];
}else{
    ## Fetch records
    $empQuery = "SELECT transaction_status, DATE_FORMAT(transaction_date, '%b-%y') AS month, SUM(final_weight) AS total_weight, SUM(total_price) AS total_price FROM Weight WHERE status = 0".$searchQuery." GROUP BY transaction_status, month ORDER BY transaction_status, transaction_date";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    // Individual Values
    $salesWeight = array_fill_keys($months, 0);
    $purchaseWeight = array_fill_keys($months, 0);
    $localWeight = array_fill_keys($months, 0);
    $miscWeight = array_fill_keys($months, 0);

    $salesPrice = array_fill_keys($months, 0);
    $purchasePrice = array_fill_keys($months, 0);
    $localPrice = array_fill_keys($months, 0);
    $miscPrice = array_fill_keys($months, 0);

    while ($row = mysqli_fetch_assoc($empRecords)) {
        $monthYear = $row['month'];
        $weight = (float) $row['total_weight'];
        $price = (float) $row['total_price'];

        switch ($row['transaction_status']) {
        case 'Sales':
            $salesWeight[$monthYear] = $weight;
            $salesPrice[$monthYear] = $price;
            break;
        case 'Purchase':
            $purchaseWeight[$monthYear] = $weight;
            $purchasePrice[$monthYear] = $price;
            break;
        case 'Local':
            $localWeight[$monthYear] = $weight;
            $localPrice[$monthYear] = $price;
            break;
        case 'Misc':
            $miscWeight[$monthYear] = $weight;
            $miscPrice[$monthYear] = $price;
            break;
        }
    }

    ## Format data for DataTable
    $columns = ["column_1"]; // Start with the first static column
    foreach ($months as $index => $month) {
        $columns[] = "column_" . ($index + 2); // Create dynamic column names (column_2, column_3, etc.)
    }
    $columns[] = "last_column"; // Always add the last column for "Sub Total"

    // Create data rows
    $data[] = array_merge(
        ["column_1" => ""],
        array_combine(array_slice($columns, 1, -1), $months),
        ["last_column" => "Total"]
    );

    // Sales
    $salesRow = ["column_1" => "Sales"];
    $salesTotalWeight = 0;
    $salesTotalPrice = 0;

    foreach ($months as $month) {
        $weight = isset($salesWeight[$month]) ? number_format($salesWeight[$month], 2) : number_format(0, 2);
        $price = isset($salesPrice[$month]) ? number_format($salesPrice[$month], 2) : 0;

        $salesRow["column_" . (array_search($month, $months) + 2)] = "<span style='font-weight: 600;'>Nett Weight:</span> <br>{$weight} KG";

        $salesTotalWeight += (float) $salesWeight[$month];
        $salesTotalPrice += (float) $price;
    }

    $salesTotalWeight = number_format($salesTotalWeight, 2);
    $salesTotalPrice = number_format($salesTotalPrice, 2);

    $salesRow["last_column"] = "<span style='font-weight: 600;'>Total Nett Weight:</span><br> {$salesTotalWeight} KG";
    $data[] = $salesRow;

    // Purchase
    $purchaseRow = ["column_1" => "Purchase"];
    $purchaseTotalWeight = 0;
    $purchaseTotalPrice = 0;

    foreach ($months as $month) {
        $weight = isset($purchaseWeight[$month]) ? number_format($purchaseWeight[$month], 2) : number_format(0, 2);
        $price = isset($purchasePrice[$month]) ? number_format($purchasePrice[$month], 2) : 0;

        $purchaseRow["column_" . (array_search($month, $months) + 2)] = "<span style='font-weight: 600;'>Nett Weight:</span> <br>{$weight} KG";

        $purchaseTotalWeight += (float) $purchaseWeight[$month];
        $purchaseTotalPrice += (float) $price;
    }

    $purchaseTotalWeight = number_format($purchaseTotalWeight, 2);
    $purchaseTotalPrice = number_format($purchaseTotalPrice, 2);

    $purchaseRow["last_column"] = "<span style='font-weight: 600;'>Total Nett Weight:</span><br> {$purchaseTotalWeight} KG";
    $data[] = $purchaseRow;

    // Local
    $localRow = ["column_1" => "Public"];
    $localTotalWeight = 0;
    $localTotalPrice = 0;

    foreach ($months as $month) {
        $weight = isset($localWeight[$month]) ? number_format($localWeight[$month], 2) : number_format(0, 2);
        $price = isset($localPrice[$month]) ? number_format($localPrice[$month], 2) : 0;

        $localRow["column_" . (array_search($month, $months) + 2)] = "<span style='font-weight: 600;'>Nett Weight:</span> <br>{$weight} KG";

        $localTotalWeight += (float) $localWeight[$month];
        $localTotalPrice += (float) $price;
    }

    $localTotalWeight = number_format($localTotalWeight, 2);
    $localTotalPrice = number_format($localTotalPrice, 2);

    $localRow["last_column"] = "<span style='font-weight: 600;'>Total Nett Weight:</span><br> {$localTotalWeight} KG";
    $data[] = $localRow;

    //   Misc
    //   $miscRow = ["column_1" => "Misc"];
    //   $miscTotalWeight = 0;
    //   $miscTotalPrice = 0;

    //   foreach ($months as $month) {
    //     $weight = isset($miscWeight[$month]) ? number_format($miscWeight[$month], 2) : number_format(0, 2);
    //     $price = isset($miscPrice[$month]) ? number_format($miscPrice[$month], 2) : 0;

    //     $miscRow["column_" . (array_search($month, $months) + 2)] = "<span style='font-weight: 600;'>Nett Weight:</span> <br>{$weight} KG<br> <span style='font-weight: 600;'>Price:</span> <br>RM {$price}";

    //     $miscTotalWeight += (float) $weight;
    //     $miscTotalPrice += (float) $price;
    //   }
    //   $miscRow["last_column"] = "<span style='font-weight: 600;'>Total Nett Weight:</span><br> {$miscTotalWeight} MT<br><span style='font-weight: 600;'>Total Price:</span><br> RM {$miscTotalPrice}";
    //   $data[] = $miscRow;
}

## Response
$response = array(
    "columns" => $columns,
    "aaData" => $data
);

echo json_encode($response);

function calcDateDifference($fromDate, $toDate){
    // Define the interval of 1 month
    $interval = new DateInterval('P1M'); // P1M means a period of 1 month

    // Create a DatePeriod object
    $period = new DatePeriod($fromDate, $interval, $toDate);

    $months = [];
    foreach ($period as $date) {
        $months[] = $date->format('M-y'); // Store each month in 'YYYY-MM' format
    }

    return $months;
}

?>