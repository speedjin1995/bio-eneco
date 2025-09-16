<?php

require_once 'db_connect.php';
// // Load the database configuration file 
session_start();
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
if($_GET["file"] == 'weight'){
    $fileName = "Weight-data_" . date('Y-m-d') . ".xls";
}else{
    $fileName = "Count-data_" . date('Y-m-d') . ".xls";
}

## Search 
$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $searchQuery = "and plant_code IN ('$username')";
}

if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y', $_GET['fromDate']);
    $formatted_date = $dateTime->format('Y-m-d 00:00:00');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date >= '".$formatted_date."'";
    }
}

if($_GET['toDate'] != null && $_GET['toDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y', $_GET['toDate']);
    $formatted_date = $dateTime->format('Y-m-d 23:59:59');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date <= '".$formatted_date."'";
    }
}

if($_GET['status'] != null && $_GET['status'] != '' && $_GET['status'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_GET['status']."'";
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_GET['status']."'";
    }	
}

if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_GET['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_GET['customer']."'";
    }
}

if(isset($_GET['supplier']) && $_GET['supplier'] != null && $_GET['supplier'] != '' && $_GET['supplier'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.supplier_code = '".$_POST['supplier']."'";
    }
    else{
        $searchQuery .= " and count.supplier_code = '".$_POST['supplier']."'";
    }
}

if($_GET['vehicle'] != null && $_GET['vehicle'] != '' && $_GET['vehicle'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
}

if($_GET['weighingType'] != null && $_GET['weighingType'] != '' && $_GET['weighingType'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_GET['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_GET['weighingType']."%'";
    }
}

if($_GET['product'] != null && $_GET['product'] != '' && $_GET['product'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_GET['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_GET['product']."'";
    }
}

if(isset($_GET['rawMat']) && $_GET['rawMat'] != null && $_GET['rawMat'] != '' && $_GET['rawMat'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.raw_mat_code = '".$_GET['rawMat']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_GET['rawMat']."'";
    }
}

if(isset($_GET['plant']) && $_GET['plant'] != null && $_GET['plant'] != '' && $_GET['plant'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_GET['plant']."'";
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_GET['plant']."'";
    }
}

if(isset($_GET['batchDrum']) && $_GET['batchDrum'] != null && $_GET['batchDrum'] != '' && $_GET['batchDrum'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.batch_drum = '".$_GET['batchDrum']."'";
    }
    else{
        $searchQuery .= " and count.batch_drum = '".$_GET['batchDrum']."'";
    }
}

$isMulti = '';
if(isset($_GET['isMulti']) && $_GET['isMulti'] != null && $_GET['isMulti'] != '' && $_GET['isMulti'] != '-'){
    $isMulti = $_GET['isMulti'];
}

// Column names 
$fields = array('ticket_no', 'doc_date', 'lorry_no', 'type_code', 'type_desc', 'sku_code', 'sku_desc', 'custcode', 'custname', 'suppcode', 'suppname',  'trans_code', 'tran_desc', 
            'do_no', 'first_weight', 'second_weight', 'deduction', 'net_weight', 'unit_price', 'total_value', 'time_in', 'time_out', 'weigh_mode', 'usersname'); 


// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database
if($_GET["file"] == 'weight'){
    if ($isMulti == 'Y'){
        $id = $_GET['id']; 
        $sql = "select * from Weight WHERE id IN ($id)";
    }else{
        $sql = "select * from Weight WHERE Weight.is_cancel = 'N'".$searchQuery;
    }

    $query = $db->query($sql);
}
else{
    $query = $db->query("select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
    count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
    count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
    count.remark, count.deleted, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
    count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
    count.productName = products.id AND status.id=count.status AND units.id=count.unit ".$searchQuery."");
}

if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){
        $lineData = []; // Ensure it starts as an empty array each iteration
        $type_code = 'S';
        $type_desc = 'Sales';

        if($_GET["file"] == 'weight'){
            if ($row['transaction_status'] == 'Local'){
                $type_code = 'IT';
                $type_desc = 'Internal Transfer';
            }else if ($row['transaction_status'] == 'Purchase'){
                $type_code = 'P';
                $type_desc = 'Purchase';
            }else if ($row['transaction_status'] == 'Sales'){
                $type_code = 'S';
                $type_desc = 'Sales';
            }
            else{
                $type_code = 'ITR';
                $type_desc = 'Internal Transfer Receive';
            }
            
            $lineData = array($row['transaction_id'], $row['transaction_date'], $row['lorry_plate_no1'], $type_code, $type_desc,
                (($type_code=='S' || $type_code=='IT') ? $row['product_code'] : $row['raw_mat_code']), 
                (($type_code=='S' || $type_code=='IT') ? $row['product_name'] : $row['raw_mat_name']),
                $row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'],
                $row['transporter_code'], $row['transporter'], $row['delivery_no'], $row['gross_weight1'], $row['tare_weight1'], '0', $row['nett_weight1'], 
                '0.00', '0.00', $row['gross_weight1_date'], $row['tare_weight1_date'], $row['manual_weight'], $row['created_by']);   
        }
        else{
            $lineData = array($row['serialNo'], $row['product_name'], $row['units'], $row['unitWeight'], $row['tare'], $row['currentWeight'], $row['actualWeight'],
            $row['totalPCS'], $row['moq'], $row['unitPrice'], $row['totalPrice'], $row['veh_number'], $row['lots_no'], $row['batchNo'], $row['invoiceNo']
            , $row['deliveryNo'], $row['purchaseNo'], $row['customer_name'], $row['packages'], $row['dateTime'], $row['remark'], $row['status'], $deleted);
        }

        # Added checking to fix duplicated issue
        if (!empty($lineData)) {
            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
    } 
}else{ 
    $excelData .= 'No records found...'. "\n"; 
} 
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>