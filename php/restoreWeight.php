<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

function convertDate($value) {
    if (empty($value) || trim($value) === '0000-00-00 00:00:00') return null;
    $formats = ['d/m/Y H:i', 'd/m/Y G:i', 'd/m/Y H:i:s', 'd/m/Y G:i:s', 'Y-m-d H:i:s'];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, trim($value));
        if ($dt) return $dt->format('Y-m-d H:i:s');
    }
    return null;
}

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorArray = [];
    $rowNum = 1; // To keep track of the row number for error reporting

    foreach ($data as $row) {
        $id = !empty($row['id']) ? trim($row['id']) : null;
        $transactionid = !empty($row['transactionid']) ? trim($row['transactionid']) : null;
        $transactionstatus = !empty($row['transactionstatus']) ? trim($row['transactionstatus']) : null;
        $weighttype = !empty($row['weighttype']) ? $row['weighttype'] : null;
        $customertype = !empty($row['customertype']) ? $row['customertype'] : null;
        $transactiondate = convertDate($row['transactiondate'] ?? null);
        $lorryplateno1 = !empty($row['lorryplateno1']) ? $row['lorryplateno1'] : null;
        $lorryplateno2 = !empty($row['lorryplateno2']) ? $row['lorryplateno2'] : null;
        $supplierweight = !empty($row['supplierweight']) ? $row['supplierweight'] : null;
        $posupplyweight = !empty($row['posupplyweight']) ? $row['posupplyweight'] : null;
        $orderweight = !empty($row['orderweight']) ? $row['orderweight'] : null;
        $tinno = !empty($row['tinno']) ? $row['tinno'] : null;
        $idno = !empty($row['idno']) ? $row['idno'] : null;
        $idtype = !empty($row['idtype']) ? $row['idtype'] : null;
        $plantcode = !empty($row['plantcode']) ? $row['plantcode'] : null;
        $plantname = !empty($row['plantname']) ? $row['plantname'] : null;
        $sitecode = !empty($row['sitecode']) ? $row['sitecode'] : null;
        $sitename = !empty($row['sitename']) ? $row['sitename'] : null;
        $agentcode = !empty($row['agentcode']) ? $row['agentcode'] : null;
        $agentname = !empty($row['agentname']) ? $row['agentname'] : null;
        $customercode = !empty($row['customercode']) ? $row['customercode'] : null;
        $customername = !empty($row['customername']) ? $row['customername'] : null;
        $suppliercode = !empty($row['suppliercode']) ? $row['suppliercode'] : null;
        $suppliername = !empty($row['suppliername']) ? $row['suppliername'] : null;
        $productcode = !empty($row['productcode']) ? $row['productcode'] : null;
        $productname = !empty($row['productname']) ? $row['productname'] : null;
        $productdescription = !empty($row['productdescription']) ? $row['productdescription'] : null;
        $exdel = !empty($row['exdel']) ? $row['exdel'] : null;
        $rawmatcode = !empty($row['rawmatcode']) ? $row['rawmatcode'] : null;
        $rawmatname = !empty($row['rawmatname']) ? $row['rawmatname'] : null;
        $containerno = !empty($row['containerno']) ? $row['containerno'] : null;
        $invoiceno = !empty($row['invoiceno']) ? $row['invoiceno'] : null;
        $purchaseorder = !empty($row['purchaseorder']) ? $row['purchaseorder'] : null;
        $deliveryno = !empty($row['deliveryno']) ? $row['deliveryno'] : null;
        $transportercode = !empty($row['transportercode']) ? $row['transportercode'] : null;
        $transporter = !empty($row['transporter']) ? $row['transporter'] : null;
        $destinationcode = !empty($row['destinationcode']) ? $row['destinationcode'] : null;
        $destination = !empty($row['destination']) ? $row['destination'] : null;
        $remarks = !empty($row['remarks']) ? $row['remarks'] : null;
        $grossweight1 = !empty($row['grossweight1']) ? $row['grossweight1'] : null;
        $grossweight1date = convertDate($row['grossweight1date'] ?? null);
        $tareweight1 = !empty($row['tareweight1']) ? $row['tareweight1'] : null;
        $tareweight1date = convertDate($row['tareweight1date'] ?? null);
        $nettweight1 = !empty($row['nettweight1']) ? $row['nettweight1'] : null;
        $grossweight2 = !empty($row['grossweight2']) ? $row['grossweight2'] : null;
        $grossweight2date = convertDate($row['grossweight2date'] ?? null);
        $tareweight2 = !empty($row['tareweight2']) ? $row['tareweight2'] : null;
        $tareweight2date = convertDate($row['tareweight2date'] ?? null);
        $nettweight2 = !empty($row['nettweight2']) ? $row['nettweight2'] : null;
        $reduceweight = !empty($row['reduceweight']) ? $row['reduceweight'] : 0;
        $finalweight = !empty($row['finalweight']) ? $row['finalweight'] : null;
        $weightdifferent = !empty($row['weightdifferent']) ? $row['weightdifferent'] : null;
        $iscomplete = !empty($row['iscomplete']) ? $row['iscomplete'] : null;
        $iscancel = !empty($row['iscancel']) ? $row['iscancel'] : null;
        $isapproved = !empty($row['isapproved']) ? $row['isapproved'] : null;
        $manualweight = !empty($row['manualweight']) ? $row['manualweight'] : null;
        $indicatorid = !empty($row['indicatorid']) ? $row['indicatorid'] : null;
        $weighbridgeid = !empty($row['weighbridgeid']) ? $row['weighbridgeid'] : null;
        $createddate = convertDate($row['createddate'] ?? null);
        $createdby = !empty($row['createdby']) ? $row['createdby'] : null;
        $modifieddate = convertDate($row['modifieddate'] ?? null);
        $modifiedby = !empty($row['modifiedby']) ? $row['modifiedby'] : null;
        $indicatorid2 = !empty($row['indicatorid2']) ? $row['indicatorid2'] : null;
        $unitprice = !empty($row['unitprice']) ? $row['unitprice'] : null;
        $subtotal = !empty($row['subtotal']) ? $row['subtotal'] : 0;
        $sst = !empty($row['sst']) ? $row['sst'] : 0;
        $totalprice = !empty($row['totalprice']) ? $row['totalprice'] : 0;
        $loaddrum = !empty($row['loaddrum']) ? $row['loaddrum'] : null;
        $noofdrum = !empty($row['noofdrum']) ? $row['noofdrum'] : null;
        $batchdrum = !empty($row['batchdrum']) ? $row['batchdrum'] : null;
        $status = !empty($row['status']) ? $row['status'] : 0;
        $approvedby = !empty($row['approvedby']) ? $row['approvedby'] : null;
        $approvedreason = !empty($row['approvedreason']) ? $row['approvedreason'] : null;
        $synced = !empty($row['synced']) ? $row['synced'] : null;
        $received = !empty($row['received']) ? $row['received'] : null;
        $cancelledreason = !empty($row['cancelledreason']) ? $row['cancelledreason'] : null;

        // If transaction_id are empty, skip the row
        if(empty($transactionid)){
            $errMsg = "Row Num: ".$rowNum." do not have a valid transaction ID.";
            $errorArray[] = $errMsg;
            continue;
        }

        // Check if transaction_id already exists
        if (!empty($transactionid)) {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM Weight WHERE transaction_id = ?");
            $checkStmt->bind_param("s", $transactionid);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();
            
            if($count > 0){
                $errMsg = "Row Num: ".$rowNum." with Transaction ID: ".$transactionid." already exists.";
                $errorArray[] = $errMsg;
                continue;
            }
        }

        if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, customer_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code, raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, created_date, created_by, modified_date, modified_by, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, synced, received, cancelled_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionid, $transactionstatus, $weighttype, $customertype, $transactiondate, $lorryplateno1, $lorryplateno2, $supplierweight, $posupplyweight, $orderweight, $tinno, $idno, $idtype, $plantcode, $plantname, $sitecode, $sitename, $agentcode, $agentname, $customercode, $customername, $suppliercode, $suppliername, $productcode, $productname, $productdescription, $exdel, $rawmatcode, $rawmatname, $containerno, $invoiceno, $purchaseorder, $deliveryno, $transportercode, $transporter, $destinationcode, $destination, $remarks, $grossweight1, $grossweight1date, $tareweight1, $tareweight1date, $nettweight1, $grossweight2, $grossweight2date, $tareweight2, $tareweight2date, $nettweight2, $reduceweight, $finalweight, $weightdifferent, $iscomplete, $iscancel, $isapproved, $manualweight, $indicatorid, $weighbridgeid, $createddate, $createdby, $modifieddate, $modifiedby, $indicatorid2, $unitprice,$subtotal, $sst, $totalprice, $loaddrum, $noofdrum, $batchdrum, $status, $approvedby,$approvedreason, $synced, $received, $cancelledreason);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
    }

    $db->close();

    if (!empty($errorArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorArray 
            )
        );
    }else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Restore Successfully!!" 
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
