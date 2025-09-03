<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
// // Load the database configuration file 
session_start();
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
} 

## Search 
$searchQuery = "";
if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
    $fromDateTime = $dateTime->format('Y-m-d H:i:00');
    $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_GET['toDate'] != null && $_GET['toDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
    $toDateTime = $dateTime->format('Y-m-d H:i:59');
    $searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_GET['status'] != null && $_GET['status'] != '' && $_GET['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_GET['status']."'";
}

if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_GET['customer']."'";
}

if($_GET['supplier'] != null && $_GET['supplier'] != '' && $_GET['supplier'] != '-'){
	$searchQuery .= " and supplier_code = '".$_GET['supplier']."'";
}

if($_GET['product'] != null && $_GET['product'] != '' && $_GET['product'] != '-'){
	$searchQuery .= " and product_code = '".$_GET['product']."'";
}

if($_GET['rawMaterial'] != null && $_GET['rawMaterial'] != '' && $_GET['rawMaterial'] != '-'){
	$searchQuery .= " and raw_mat_code = '".$_GET['rawMaterial']."'";
}

if($_GET['plant'] != null && $_GET['plant'] != '' && $_GET['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_GET['plant']."'";
}

if($_GET['purchaseOrder'] != null && $_GET['purchaseOrder'] != '' && $_GET['purchaseOrder'] != '-'){
	$searchQuery .= " and purchase_order = '".$_GET['purchaseOrder']."'";
}

$isMulti = 'N';
if($_GET['isMulti'] != null && $_GET['isMulti'] != '' && $_GET['isMulti'] != '-'){
    $isMulti = $_GET['isMulti'];
}

// Column names 
$fields = array('DocNo', 'DOCREF2', 'DOCDATE', 'DESCRIPTION2', 'CODE', 'COMPANYNAME', 'ITEMCODE', 'DESCRIPTION', 'REMARK2', 'SHIPPER', 'DOCREF1', 'DOCNOEX', 'REMARK1', 'QTY', 'UOM', 'PROJECT', 'LOCATION', 'UNITPRICE', 'Amount'); 

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

if ($isMulti == 'N'){
    if($_GET["type"] == 'do'){
        // Excel file name for download 
        $fileName = "DO-data_" . date('Y-m-d') . ".xls";

        // Fetch records from database
        $query = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y' AND purchase_order != '-'".$searchQuery." group by purchase_order order by id asc";
        if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
            $username = implode("', '", $_SESSION["plant"]);
            $query = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username') AND purchase_order != '-'".$searchQuery." group by purchase_order order by id asc";
        }

        $do_stmt = $db->query($query);
        if($do_stmt->num_rows > 0){  
            // Output each row of the data 
            while($row = $do_stmt->fetch_assoc()){
                $soNo = $row['purchase_order']; 
                $fromDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
                $fromDateTime = $fromDate->format('Y-m-d H:i:00');
                $toDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
                $toDateTime = $toDate->format('Y-m-d H:i:59');

                $doQuery = "select * from Weight WHERE purchase_order = '$soNo' AND tare_weight1_date >= '$fromDateTime' AND tare_weight1_date <= '$toDateTime' AND is_complete = 'Y' AND status = '0' AND unit_price > 0";
                $doRecords = mysqli_query($db, $doQuery);
                $weighingData = array();

                while($row2 = mysqli_fetch_assoc($doRecords)) {
                    $lineData = []; // Ensure it starts as an empty array each iteration
                    $tareDate = DateTime::createFromFormat('Y-m-d H:i:s', $row2['tare_weight1_date']);
                    $tareDateTime = $tareDate->format('d/m/Y');
                    $exDel = ($row2['ex_del'] == 'EX') ? 'E' : 'D';
                    $orderNo = $row2['purchase_order'];

                    $soNo = '';
                    $uom = '';
                    $qty = '';
                    $amt = '';
                    if ($select_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $orderNo, $row2['product_code'], $row2['plant_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) {
                            $uom = searchUnitById($row3['converted_unit'], $db);
                            $productId = searchProductIdByCode($row3['product_code'], $db);
                            $unitPrice = $row3['unit_price'];
                            $soNo = $row3['so_no'];

                            if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id='2' AND status='0'")) {
                                $update_stmt->bind_param('s', $productId);
                                $update_stmt->execute();
                                $result2 = $update_stmt->get_result();
                                if ($row4 = $result2->fetch_assoc()) {
                                    $qty = $row2['nett_weight1'] * $row4['rate'];
                                    $amt = $qty * $unitPrice;
                                }
                                $update_stmt->close();
                            }
                        }
                        $select_stmt->close();
                    }
                    $lineData = array($soNo, $row2['transaction_id'], $tareDateTime, $row2['lorry_plate_no1'], $row2['customer_code'], $row2['customer_name'], $row2['product_code'], $row2['product_name'], $row2['destination'], $row2['transporter_code'], $exDel, $orderNo, $row2['delivery_no'], $qty, $uom, $row2['plant_code'], $row2['plant_code'], $unitPrice, $amt);

                    # Added checking to fix duplicated issue
                    if (!empty($lineData)) {
                        foreach($lineData as $key => $value) {
                            if($key == 3) { // lorry_plate_no1 is at index 3
                                $lineData[$key] = " " . $value;
                            } else {
                                // Apply normal filtering to other columns
                                filterData($lineData[$key]); 
                            }
                        }

                        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                    }
                }
            } 
        }else{ 
            $excelData .= 'No records found...'. "\n"; 
        } 
    }else{
        // Excel file name for download 
        $fileName = "GR-data_" . date('Y-m-d') . ".xls";

        // Fetch records from database
        $query = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y' AND purchase_order != '-'".$searchQuery." group by purchase_order order by id asc";
        if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
            $username = implode("', '", $_SESSION["plant"]);
            $query = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username') AND purchase_order != '-'".$searchQuery." group by purchase_order order by id asc";
        }

        $do_stmt = $db->query($query);
        if($do_stmt->num_rows > 0){  
            // Output each row of the data 
            while($row = $do_stmt->fetch_assoc()){
                $poNo = $row['purchase_order']; 
                $fromDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
                $fromDateTime = $fromDate->format('Y-m-d H:i:00');
                $toDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
                $toDateTime = $toDate->format('Y-m-d H:i:59');

                $doQuery = "select * from Weight WHERE purchase_order = '$poNo' AND tare_weight1_date >= '$fromDateTime' AND tare_weight1_date <= '$toDateTime' AND is_complete = 'Y' AND status = '0'";
                $doRecords = mysqli_query($db, $doQuery);
                $weighingData = array();

                while($row2 = mysqli_fetch_assoc($doRecords)) {
                    $lineData = []; // Ensure it starts as an empty array each iteration
                    $tareDate = DateTime::createFromFormat('Y-m-d H:i:s', $row2['tare_weight1_date']);
                    $tareDateTime = $tareDate->format('d/m/Y');
                    $exDel = ($row2['ex_del'] == 'EX') ? 'E' : 'D';

                    $uom = '';
                    $qty = '';
                    $amt = '';
                    if ($select_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $poNo, $row2['raw_mat_code'], $row2['plant_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) { 
                            $uom = searchUnitById($row3['converted_unit'], $db);
                            $rawMatId = searchRawMatIdByCode($row3['raw_mat_code'], $db);
                            $unitPrice = $row3['unit_price'];

                            if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id='2' AND status='0'")) {
                                $update_stmt->bind_param('s', $rawMatId);
                                $update_stmt->execute();
                                $result2 = $update_stmt->get_result();
                                if ($row4 = $result2->fetch_assoc()) {
                                    $qty = $row2['nett_weight1'] * $row4['rate'];
                                    $amt = $qty * $unitPrice;
                                }
                                $update_stmt->close();
                            }
                        }
                        $select_stmt->close();
                    }

                    $lineData = array($poNo, $row2['transaction_id'], $tareDateTime, $row2['lorry_plate_no1'], $row2['supplier_code'], $row2['supplier_name'], $row2['raw_mat_code'], $row2['raw_mat_name'], $row2['destination'], $row2['transporter_code'], $exDel, '', $row2['delivery_no'], $qty, $uom, $row2['plant_code'], $row2['plant_code'], $unitPrice, $amt);

                    # Added checking to fix duplicated issue
                    if (!empty($lineData)) {
                        foreach($lineData as $key => $value) {
                            if($key == 3) { // lorry_plate_no1 is at index 3
                                $lineData[$key] = " " . $value;
                            } else {
                                // Apply normal filtering to other columns
                                filterData($lineData[$key]); 
                            }
                        }
                        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                    }
                }
            }
        }else{ 
            $excelData .= 'No records found...'. "\n"; 
        } 
    }
}else{
    $id = $_GET['id']; 

    if($_GET["type"] == 'do'){
        // Excel file name for download 
        $fileName = "DO-data_" . date('Y-m-d') . ".xls";

        // Fetch records from database
        $query = "select * from Weight where id IN (". $id .") order by id asc";

        $do_stmt = $db->query($query);
        if($do_stmt->num_rows > 0){  
            // Output each row of the data 
            while($row = $do_stmt->fetch_assoc()){
                $soNo = $row['purchase_order']; 
                $fromDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
                $fromDateTime = $fromDate->format('Y-m-d H:i:00');
                $toDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
                $toDateTime = $toDate->format('Y-m-d H:i:59');

                $doQuery = "select * from Weight WHERE purchase_order = '$soNo' AND tare_weight1_date >= '$fromDateTime' AND tare_weight1_date <= '$toDateTime' AND is_complete = 'Y' AND status = '0' AND unit_price > 0";
                $doRecords = mysqli_query($db, $doQuery);
                $weighingData = array();

                while($row2 = mysqli_fetch_assoc($doRecords)) {
                    $lineData = []; // Ensure it starts as an empty array each iteration
                    $tareDate = DateTime::createFromFormat('Y-m-d H:i:s', $row2['tare_weight1_date']);
                    $tareDateTime = $tareDate->format('d/m/Y');
                    $exDel = ($row2['ex_del'] == 'EX') ? 'E' : 'D';
                    $orderNo = $row2['purchase_order'];

                    $soNo = '';
                    $uom = '';
                    $qty = '';
                    $amt = '';
                    if ($select_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $orderNo, $row2['product_code'], $row2['plant_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) {
                            $uom = searchUnitById($row3['converted_unit'], $db);
                            $productId = searchProductIdByCode($row3['product_code'], $db);
                            $unitPrice = $row3['unit_price'];
                            $soNo = $row3['so_no'];

                            if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id='2' AND status='0'")) {
                                $update_stmt->bind_param('s', $productId);
                                $update_stmt->execute();
                                $result2 = $update_stmt->get_result();
                                if ($row4 = $result2->fetch_assoc()) {
                                    $qty = $row2['nett_weight1'] * $row4['rate'];
                                    $amt = $qty * $unitPrice;
                                }
                                $update_stmt->close();
                            }
                        }
                        $select_stmt->close();
                    }
                    $lineData = array($soNo, $row2['transaction_id'], $tareDateTime, $row2['lorry_plate_no1'], $row2['customer_code'], $row2['customer_name'], $row2['product_code'], $row2['product_name'], $row2['destination'], $row2['transporter_code'], $exDel, $orderNo, $row2['delivery_no'], $qty, $uom, $row2['plant_code'], $row2['plant_code'], $unitPrice, $amt);

                    # Added checking to fix duplicated issue
                    if (!empty($lineData)) {
                        foreach($lineData as $key => $value) {
                            if($key == 3) { // lorry_plate_no1 is at index 3
                                $lineData[$key] = " " . $value;
                            } else {
                                // Apply normal filtering to other columns
                                filterData($lineData[$key]); 
                            }
                        }
                        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                    }
                }
            } 
        }else{ 
            $excelData .= 'No records found...'. "\n"; 
        } 
    }else{
        // Excel file name for download 
        $fileName = "GR-data_" . date('Y-m-d') . ".xls";

        // Fetch records from database
        $query = "select * from Weight where id IN (". $id .") order by id asc";

        $do_stmt = $db->query($query);
        if($do_stmt->num_rows > 0){  
            // Output each row of the data 
            while($row = $do_stmt->fetch_assoc()){
                $poNo = $row['purchase_order']; 
                $fromDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['fromDate']);
                $fromDateTime = $fromDate->format('Y-m-d H:i:00');
                $toDate = DateTime::createFromFormat('d-m-Y H:i', $_GET['toDate']);
                $toDateTime = $toDate->format('Y-m-d H:i:59');

                $doQuery = "select * from Weight WHERE purchase_order = '$poNo' AND tare_weight1_date >= '$fromDateTime' AND tare_weight1_date <= '$toDateTime' AND is_complete = 'Y' AND status = '0'";
                $doRecords = mysqli_query($db, $doQuery);
                $weighingData = array();

                while($row2 = mysqli_fetch_assoc($doRecords)) {
                    $lineData = []; // Ensure it starts as an empty array each iteration
                    $tareDate = DateTime::createFromFormat('Y-m-d H:i:s', $row2['tare_weight1_date']);
                    $tareDateTime = $tareDate->format('d/m/Y');
                    $exDel = ($row2['ex_del'] == 'EX') ? 'E' : 'D';

                    $uom = '';
                    $qty = '';
                    $amt = '';
                    if ($select_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND plant_code=? AND deleted='0'")) {
                        $select_stmt->bind_param('sss', $poNo, $row2['raw_mat_code'], $row2['plant_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row3 = $result->fetch_assoc()) { 
                            $uom = searchUnitById($row3['converted_unit'], $db);
                            $rawMatId = searchRawMatIdByCode($row3['raw_mat_code'], $db);
                            $unitPrice = $row3['unit_price'];

                            if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id='2' AND status='0'")) {
                                $update_stmt->bind_param('s', $rawMatId);
                                $update_stmt->execute();
                                $result2 = $update_stmt->get_result();
                                if ($row4 = $result2->fetch_assoc()) {
                                    $qty = $row2['nett_weight1'] * $row4['rate'];
                                    $amt = $qty * $unitPrice;
                                }
                                $update_stmt->close();
                            }
                        }
                        $select_stmt->close();
                    }

                    $lineData = array($poNo, $row2['transaction_id'], $tareDateTime, $row2['lorry_plate_no1'], $row2['supplier_code'], $row2['supplier_name'], $row2['raw_mat_code'], $row2['raw_mat_name'], $row2['destination'], $row2['transporter_code'], $exDel, '', $row2['delivery_no'], $qty, $uom, $row2['plant_code'], $row2['plant_code'], $unitPrice, $amt);

                    # Added checking to fix duplicated issue
                    if (!empty($lineData)) {
                        foreach($lineData as $key => $value) {
                            if($key == 3) { // lorry_plate_no1 is at index 3
                                $lineData[$key] = " " . $value;
                            } else {
                                // Apply normal filtering to other columns
                                filterData($lineData[$key]); 
                            }
                        }
                        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                    }
                }
            }
        }else{ 
            $excelData .= 'No records found...'. "\n"; 
        } 
    }
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>