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
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
    $formatted_date = $dateTime->format('Y-m-d H:i');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date >= '".$formatted_date."'";
    }
}

if($_GET['toDate'] != null && $_GET['toDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
    $formatted_date = $dateTime->format('Y-m-d H:i');

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
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){

    $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME', 'SUPPLIER CODE', 'SUPPLIER NAME', 
            'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'TRANSPORTER CODE', 'DELIVERED BY', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
            ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 
            'PLANT CODE', 'PLANT NAME', 'WEIGHTED BY', 'REMARKS'); 

    // if ($_GET['status'] == 'Sales'){
    //     $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'TIN NO.', 'ID NO.', 'ID TYPE', 'CUSTOMER CODE', 'CUSTOMER NAME', 
    //         'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
    //         'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
    //         ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 
    //         'PLANT CODE', 'PLANT NAME', 'WEIGHTED BY', 'REMARKS'); 
    // }
    // else{
    //     $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME', 
    //         'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
    //         'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
    //         ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
    //         'PLANT NAME', 'WEIGHTED BY', 'REMARKS'); 
    // }
}
else{
    $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME', 'SUPPLIER CODE', 'SUPPLIER NAME', 
            'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'TRANSPORTER CODE', 'DELIVERED BY', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 
            'NET WEIGHT (MT)', ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
            'PLANT NAME', 'UNIT PRICE (RM)', 'TOTAL PRICE (RM)', 'WEIGHTED BY', 'REMARKS'); 

    // if ($_GET['status'] == 'Sales'){
    //     $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'TIN NO.', 'ID NO.', 'ID TYPE','CUSTOMER CODE', 'CUSTOMER NAME', 
    //         'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
    //         'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
    //         ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
    //         'PLANT NAME', 'UNIT PRICE (RM)', 'TOTAL PRICE (RM)', 'WEIGHTED BY', 'REMARKS'); 
    // }else{
    //     $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME', 
    //         'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
    //         'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
    //         ($_GET['status'] == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
    //         'PLANT NAME', 'UNIT PRICE (RM)', 'TOTAL PRICE (RM)', 'WEIGHTED BY', 'REMARKS'); 
    // }
}


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

        if($_GET["file"] == 'weight'){
            $exDel = '';
            
            if ($row['ex_del'] == 'EX'){
                $exDel = 'E';
            }else{
                $exDel = 'D';
            }
            
            if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
                $lineData = array_merge(
                    array($row['transaction_id'], $row['weight_type'], $row['transaction_date'], $row['lorry_plate_no1']),
                    array($row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], 
                    ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
                    ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
                    $row['product_description'], $row['transporter_code'], $row['transporter'], $row['delivery_no'], 
                    number_format((float)$row['gross_weight1'] / 1000, 2, '.', ''), number_format((float)$row['tare_weight1'] / 1000, 2, '.', ''), 
                    number_format((float)$row['nett_weight1'] / 1000, 2, '.', ''), 
                    ($row['transaction_status'] == 'Sales' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')), $row['gross_weight1_date'], $row['tare_weight1_date'], 
                    $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks'])
                );
                
                // $lineData = array_merge(
                //     array($row['transaction_id'], $row['weight_type'], $row['transaction_date'], $row['lorry_plate_no1']),
                //     ($row['transaction_status'] == 'Sales' ? array($row['tin_no'], $row['id_no'], $row['id_type']) : array()),
                //     array($row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], 
                //     ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
                //     ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
                //     $row['product_description'], $row['destination_code'], $row['destination'], $row['transporter_code'], 
                //     $row['transporter'], $exDel, $row['batch_drum'], $row['purchase_order'], $row['delivery_no'], 
                //     number_format((float)$row['gross_weight1'] / 1000, 2, '.', ''), number_format((float)$row['tare_weight1'] / 1000, 2, '.', ''), 
                //     number_format((float)$row['nett_weight1'] / 1000, 2, '.', ''), 
                //     ($row['transaction_status'] == 'Sales' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')), 
                //     number_format((float)$row['weight_different'] / 1000, 2, '.', ''), $row['gross_weight1_date'], $row['tare_weight1_date'], 
                //     $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks'])
                // );
            }
            else{
                // $unitPrice = 0.0;
                // $totalPrice = 0.0;

                // if($row['purchase_order'] != null && $row['purchase_order'] != '' && $row['purchase_order'] != '-'){
                //     if($row['transaction_status'] == 'Sales'){
                //         $query2 = $db->query("select * from Sales_Order WHERE order_no = '".$row['purchase_order']."' AND product_code = '".$row['product_code']."' AND plant_code = '".$row['plant_code']."'");
                    
                //         if($row2 = $query2->fetch_assoc()){ 
                //             $unitPrice = $row2['unit_price'];
                //             $totalPrice = (float)$unitPrice * ((float)$row['nett_weight1'] / 1000);
                //             $totalPrice = number_format($totalPrice, 2, '.', '');
                //         }
                //     }
                //     else{
                //         $query2 = $db->query("select * from Purchase_Order WHERE po_no = '".$row['purchase_order']."' AND raw_mat_code = '".$row['raw_mat_code']."' AND plant_code = '".$row['plant_code']."'");
                    
                //         if($row2 = $query2->fetch_assoc()){ 
                //             $unitPrice = $row2['unit_price'];
                //             $totalPrice = (float)$unitPrice * ((float)$row['nett_weight1'] / 1000);
                //             $totalPrice = number_format($totalPrice, 2, '.', '');
                //         }
                //     }
                // }

                $lineData = array_merge(
                    array($row['transaction_id'], $row['weight_type'], $row['transaction_date'], $row['lorry_plate_no1']),
                    array($row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], 
                    ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
                    ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
                    $row['product_description'], $row['transporter_code'], $row['transporter'], $row['delivery_no'], 
                    number_format((float)$row['gross_weight1'] / 1000, 2, '.', ''), number_format((float)$row['tare_weight1'] / 1000, 2, '.', ''), 
                    number_format((float)$row['nett_weight1'] / 1000, 2, '.', ''), 
                    ($row['transaction_status'] == 'Sales' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')), $row['gross_weight1_date'], $row['tare_weight1_date'], 
                    $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['unit_price'], $row['total_price'], $row['created_by'], $row['remarks'])
                );
            }
                
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