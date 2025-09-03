<?php

require_once 'db_connect.php';
session_start();

$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $searchQuery = " and plant_code IN ('$username')";
}

if(isset($_POST['fromDate']) && $_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
    $formatted_date = $dateTime->format('Y-m-d H:i');
    $fromDate = $dateTime->format('d/m/Y');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date >= '".$formatted_date."'";
    }
}

if(isset($_POST['toDate']) && $_POST['toDate'] != null && $_POST['toDate'] != ''){
    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
    $formatted_date = $dateTime->format('Y-m-d H:i');
    $toDate = $dateTime->format('d/m/Y');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date <= '".$formatted_date."'";
    }
}

if(isset($_POST['status']) && $_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_POST['status']."'";
        // if($_POST['status'] == 'Sales'){
        //     //$searchQuery .= " and Weight.transaction_status = '".$_POST['status']."' AND Weight.product_code <> '501A-011'";
        //     $searchQuery .= " and Weight.transaction_status = '".$_POST['status']."'";
        // }
        // else{
        //     //$searchQuery .= " and Weight.transaction_status IN ('Purchase', 'Local') AND Weight.raw_mat_code <> '501A-011'";
        //     $searchQuery .= " and Weight.transaction_status IN ('Purchase', 'Local')";
        // }
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_POST['status']."'";
    }	
}

if(isset($_POST['customer']) && $_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_POST['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_POST['customer']."'";
    }
}

if(isset($_POST['supplier']) && $_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.supplier_code = '".$_POST['supplier']."'";
    }
    else{
        $searchQuery .= " and count.supplier_code = '".$_POST['supplier']."'";
    }
}

if(isset($_POST['vehicle']) && $_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
}

if(isset($_POST['weighingType']) && $_POST['weighingType'] != null && $_POST['weighingType'] != '' && $_POST['weighingType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_POST['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_POST['weighingType']."%'";
    }
}

if(isset($_POST['customerType']) && $_POST['customerType'] != null && $_POST['customerType'] != '' && $_POST['customerType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_type like '%".$_POST['customerType']."%'";
    }
    else{
        $searchQuery .= " and count.customer_type like '%".$_POST['customerType']."%'";
    }
}

if(isset($_POST['product']) && $_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_POST['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_POST['product']."'";
    }
}

if(isset($_POST['rawMat']) && $_POST['rawMat'] != null && $_POST['rawMat'] != '' && $_POST['rawMat'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.raw_mat_code = '".$_POST['rawMat']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_POST['rawMat']."'";
    }
}

if(isset($_POST['destination']) && $_POST['destination'] != null && $_POST['destination'] != '' && $_POST['destination'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.destination = '".$_POST['destination']."'";
    }
    else{
        $searchQuery .= " and count.destination = '".$_POST['destination']."'";
    }
}

if(isset($_POST['plant']) && $_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_POST['plant']."'";
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_POST['plant']."'";
    }
}

if(isset($_POST['batchDrum']) && $_POST['batchDrum'] != null && $_POST['batchDrum'] != '' && $_POST['batchDrum'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.batch_drum = '".$_POST['batchDrum']."'";
    }
    else{
        $searchQuery .= " and count.batch_drum = '".$_POST['batchDrum']."'";
    }
}

$isMulti = '';
if(isset($_POST['isMulti']) && $_POST['isMulti'] != null && $_POST['isMulti'] != '' && $_POST['isMulti'] != '-'){
    $isMulti = $_POST['isMulti'];
}

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        // Company Details
        $companyCode = '';
        $companyName = '';

        if ($company_stmt = $db->prepare("SELECT * FROM Company")) {
            if (! $company_stmt->execute()) {
                echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
            }else{
                $result = $company_stmt->get_result();
                $companyData = $result->fetch_assoc();
                $companyCode = $companyData['company_code'];
                $companyName = $companyData['name'];
            }
        }


        $sql = '';
        if ($_POST['reportType'] == 'SUMMARY') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "SELECT DATE(transaction_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE id IN (".$id.") GROUP BY DATE(transaction_date) ORDER BY DATE(transaction_date) ASC";
            }else{
                $sql = "SELECT DATE(transaction_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(transaction_date) ORDER BY DATE(transaction_date) ASC";
            }

            if ($select_stmt = $db->prepare($sql)){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $totalRecords = 0;
                    $totalProductWeight = 0;
                    $totalTransportWeight = 0;

                    $message = '
                        <html>
                            <head>
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                </style>
                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">'.$companyName.'</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Date</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : '.$companyCode.' / '.$companyCode.'
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : BEN/BEN - Weighing Only
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" width="15%" class="text-start">Date</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Total Amount (RM)</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    $transactionDate = date("d-m-Y", strtotime($row['transaction_date']));
                                                    $productWeight = number_format($row['product_weight']/1000, 2);
                                                    $transportWeight = number_format($row['transport_weight']/1000, 2);

                                                    $totalRecords += $row['total_records'];
                                                    $totalProductWeight += $row['product_weight']/1000;
                                                    $totalTransportWeight += $row['transport_weight']/1000;

                                                    $message .= '<tr>
                                                            <td>'.$transactionDate.'</td>
                                                            <td>'.$row['total_records'].'</td>
                                                            <td>'.$productWeight.'</td>
                                                            <td>'.$transportWeight.'</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                        </tr>';
                                                }
                                                
                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$totalRecords.'</td>
                                                        <td>'.number_format($totalProductWeight, 2).'</td>
                                                        <td>'.number_format($totalTransportWeight, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "query" => "SELECT DATE(transaction_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(transaction_date) ORDER BY DATE(transaction_date) ASC",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'PRODUCT'){
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "SELECT * FROM ( SELECT product_name AS name, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(product_code) IS NOT NULL AND id IN ($id) GROUP BY product_code 
                UNION ALL SELECT raw_mat_code AS code, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(raw_mat_code) IS NOT NULL AND id IN (".$id.") GROUP BY raw_mat_code ) AS combined_results ORDER BY name";
            }else{
                $sql = "SELECT * FROM ( SELECT product_name AS name, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(product_code) IS NOT NULL AND  is_cancel <> 'Y'".$searchQuery." GROUP BY product_code UNION ALL SELECT raw_mat_code AS code, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(raw_mat_code) IS NOT NULL".$searchQuery." GROUP BY raw_mat_code ) AS combined_results ORDER BY name";
            }

            if ($select_stmt = $db->prepare($sql)){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $totalRecords = 0;
                    $totalProductWeight = 0;
                    $totalTransportWeight = 0;

                    $message = '
                        <html>
                            <head>
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                </style>
                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">'.$companyName.'</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Product</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : '.$companyCode.' / '.$companyCode.'
                                                Start Product / Last Product : / QD
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : BEN/BEN - Weighing Only
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" class="text-start">Product Description</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Average Selling Price</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    $product = $row['name'];
                                                    $productWeight = number_format($row['product_weight']/1000, 2);
                                                    $transportWeight = number_format($row['transport_weight']/1000, 2);

                                                    $totalRecords += $row['total_records'];
                                                    $totalProductWeight += $row['product_weight']/1000;
                                                    $totalTransportWeight += $row['transport_weight']/1000;

                                                    $message .= '<tr>
                                                            <td>'.$product.'</td>
                                                            <td>'.$row['total_records'].'</td>
                                                            <td>'.$productWeight.'</td>
                                                            <td>'.$transportWeight.'</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                        </tr>';
                                                }
                                                
                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$totalRecords.'</td>
                                                        <td>'.number_format($totalProductWeight, 2).'</td>
                                                        <td>'.number_format($totalTransportWeight, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'S&PC'){
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY tare_weight1_date";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery.' ORDER BY tare_weight1_date';
            }

            if ($select_stmt = $db->prepare($sql)){
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }
                                            
                                        } 
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="font-size: 11px; text-align: center;">
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                                if($_POST['status'] == 'Sales' || $_POST['status'] == 'Local'){
                                                    $message .= '<th>CUSTOMER</th>';
                                                }
                                                else{
                                                    $message .= '<th>SUPPLIER</th>';
                                                }
                                                    
                                                $message .= '<th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <!--<th>DESTINATION</th>-->';

                                                // if($_POST['status'] == 'Sales'){
                                                //     $message .= '<th>EXQ/DEL</th>';
                                                // }
                                                
                                                $message .= '
                                                <!--<th>BATCH/DRUM</th>-->
                                                <!--<th>PO NO.</th>-->
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>
                                                <th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)').'</th>
                                                <!--<th>VARIANCE <br>(MT)</th>-->
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                                <th>USER</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' || $_POST['status'] == 'Local' ? $row['customer_name'] : $row['supplier_name']);
                                        
                                            if (!isset($groupedData[$productName])) {
                                                $groupedData[$productName] = [];
                                            }
                                        
                                            $groupedData[$productName][] = $row;
                                        }
                                        
                                        // Initialize total values
                                        $grandTotalGross = 0;
                                        $grandTotalTare = 0;
                                        $grandTotalNet = 0;
    
                                        // Generate table grouped by product
                                        foreach ($groupedData as $product => $rows) {
                                            $message .= '<tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>';
                                        
                                            $totalGross = 0;
                                            $totalTare = 0;
                                            $totalNet = 0;
                                        
                                            foreach ($rows as $row) {
                                                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                                                $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                                                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedTareWeightDate = $tareWeightDate->format('H:i');
                                                $transactionDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
                                                $message .= '<tr style="font-size: 10px; text-align: center;">
                                                    <td>' . $row['transaction_id'] . '</td>
                                                    <td>' . $formattedtransactionDate . '</td>
                                                    <td>' . $row['lorry_plate_no1'] . '</td>';
                                                    
                                                    if($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local'){
                                                        $message .= '<td>' . $row['customer_name'] . '</td>';
                                                    }
                                                    else{
                                                        $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                    }
                                                    
                                                    $message .= '<td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <!--<td>' . $row['destination'] . '</td>-->';

                                                    // if($_POST['status'] == 'Sales'){
                                                    //     $message .= '<td>' . $exDel . '</td>';
                                                    // }
                                                    
                                                    $message .= '
                                                    <!--<td>' . $row['batch_drum'] . '</td>-->
                                                    <!--<td>' . $row['purchase_order'] . '</td>-->
                                                    <td>' . $row['delivery_no'] . '</td>
                                                    <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>
                                                    <td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')) . '</td>
                                                    <!--<td>' . number_format($row['weight_different']/1000, 2) . '</td>-->
                                                    <td>' . $formattedGrossWeightDate . '</td>
                                                    <td>' . $formattedTareWeightDate . '</td>
                                                    <td>' . $row['created_by'] . '</td>
                                                </tr>';
                                        
                                                // Calculate subtotals
                                                $totalGross += (float)$row['gross_weight1'];
                                                $totalTare += (float)$row['tare_weight1'];
                                                $totalNet += (float)$row['nett_weight1'];
                                            }
                                        
                                            // Add product-wise subtotal
                                            $message .= '<tr style="font-size: 11px;">
                                                <th colspan="'.($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? '6' : '6').'">Subtotal (' . $product . ')</th>
                                                <th style="border:1px solid black;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;">' . number_format($totalNet/1000, 2) . '</th>
                                            </tr>';
                                        
                                            // Add to grand total
                                            $grandTotalGross += $totalGross;
                                            $grandTotalTare += $totalTare;
                                            $grandTotalNet += $totalNet;
                                        }
                                        
                                        $message .= '</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="font-size: 11px;" colspan="'.($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? '6' : '6').'">Grand Total</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>
                                                </tr>
                                            </tfoot>';
                                        $message .= '</tbody>';
                                        
                                    $message .= '</table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'DO') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY delivery_no ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y'".$searchQuery.' ORDER BY delivery_no ASC';
            }

            if ($select_stmt = $db->prepare($sql)) {
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }
                                            
                                        } 
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="font-size: 9px;">
                                                <th>NO</th>
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';

                                            if($_POST['status'] == 'Sales' || $_POST['status'] == 'Local'){
                                                $message .= '<th>CUSTOMER</th>';
                                            }
                                            else{
                                                $message .= '<th>SUPPLIER</th>';
                                            }

                                                $message .= '<th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <!--<th>EXQ/DEL</th>-->
                                                <!--<th>BATCH/DRUM</th>-->
                                                <!--<th>PO NO.</th>-->
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>
                                                <th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'ORDER <br>WEIGHT (MT)' : 'SUPPLIER <br>WEIGHT (MT)').'</th>
                                                <!--<th>VARIANCE <br>(MT)</th>-->
                                                <th>IS CANCEL</th>
                                                <th>CANCEL <br>REASON</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        
                                        $noCount = 0;
                                        while ($row = $result->fetch_assoc()) {
                                            $noCount++;
                                            $transactionDate =  new DateTime($row['transaction_date']);
                                            $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                            $exDel = '';
                                            
                                            if ($row['ex_del'] == 'EX'){
                                                $exDel = 'E';
                                            }else{
                                                $exDel = 'D';
                                            }

                                            $message .= '<tr style="text-align:center; font-size: 8px;"">
                                                <td>' . $noCount . '</td>
                                                <td>' . $row['transaction_id'] . '</td>
                                                <td>' . $formattedtransactionDate . '</td>
                                                <td>' . $row['lorry_plate_no1'] . '</td>';
                                                
                                                if($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local'){
                                                    $message .= '<td>' . $row['customer_name'] . '</td>';
                                                }
                                                else{
                                                    $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                }
                                                
                                                $message .= '
                                                <td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                <!--<td>' . $exDel . '</td>-->
                                                <!--<td>' . $row['batch_drum'] . '</td>-->
                                                <!--<td>' . $row['purchase_order'] . '</td>-->
                                                <td>' . $row['delivery_no'] . '</td>
                                                <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>
                                                <td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')) . '</td>
                                                <!--<td>' . number_format($row['weight_different']/1000, 2) . '</td>-->
                                                <td>' . $row['is_cancel'] . '</td>
                                                <td>' . $row['cancelled_reason'] . '</td>
                                            </tr>';
                                            
                                        }
                                                                                
                                    $message .= '
                                        </tbody>
                                    </table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }          
        }
        else{
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY tare_weight1_date";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery.' ORDER BY tare_weight1_date';
            }

            if ($select_stmt = $db->prepare($sql)) {
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }
                                            
                                        } 
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="font-size: 11px; text-align: center;">
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                                if($_POST['status'] == 'Sales' || $_POST['status'] == 'Local'){
                                                    $message .= '<th>CUSTOMER</th>';
                                                }
                                                else{
                                                    $message .= '<th>SUPPLIER</th>';
                                                }
                                                
                                                $message .= '<th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <!--<th>DESTINATION</th>-->';

                                                // if($_POST['status'] == 'Sales'){
                                                //     $message .= '<th>EXQ/DEL</th>';
                                                // }
                                                
                                                $message .= '
                                                <!--<th>BATCH/DRUM</th>-->
                                                <!--<th>PO NO.</th>-->
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>
                                                <th>'.($_POST['status'] == 'Sales' || $_POST['status'] == 'Local' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)').'</th>
                                                <!--<th>VARIANCE</th>-->
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                                <th>USER</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']);

                                            if (!isset($groupedData[$productName])) {
                                                $groupedData[$productName] = [];
                                            }
                                        
                                            $groupedData[$productName][] = $row;
                                        }
                                        
                                        // Initialize total values
                                        $grandTotalGross = 0;
                                        $grandTotalTare = 0;
                                        $grandTotalNet = 0;
    
                                        // Generate table grouped by product
                                        foreach ($groupedData as $product => $rows) {
                                            $message .= '<tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>';
                                        
                                            $totalGross = 0;
                                            $totalTare = 0;
                                            $totalNet = 0;
                                        
                                            foreach ($rows as $row) {
                                                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                                                $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                                                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedTareWeightDate = $tareWeightDate->format('H:i');
                                                $transactionDate =  new DateTime($row['transaction_date']);
                                                $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
                                                $message .= '<tr style="font-size: 10px; text-align: center;">
                                                    <td>' . $row['transaction_id'] . '</td>
                                                    <td>' . $formattedtransactionDate . '</td>
                                                    <td>' . $row['lorry_plate_no1'] . '</td>';

                                                    if($_POST['status'] == 'Sales' || $row['transaction_status'] == 'Local'){
                                                        $message .= '<td>' . $row['customer_name'] . '</td>';
                                                    }
                                                    else{
                                                        $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                    }
                                                    
                                                    
                                                    $message .= '<td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <!--<td>' . $row['destination'] . '</td>-->';

                                                    // if($_POST['status'] == 'Sales'){
                                                    //     $message .= '<td style="font-size: 10px; text-align: center;">' . $exDel . '</td>';
                                                    // }
                                                    
                                                    $message .= '
                                                    <!--<td>' . $row['batch_drum'] . '</td>-->
                                                    <!--<td>' . $row['purchase_order'] . '</td>-->
                                                    <td>' . $row['delivery_no'] . '</td>
                                                    <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>
                                                    <td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')) . '</td>
                                                    <td>' . $formattedGrossWeightDate . '</td>
                                                    <td>' . $formattedTareWeightDate . '</td>
                                                    <td style="font-size: 10px; text-align: center;">' . $row['created_by'] . '</td>
                                                </tr>';
                                        
                                                // Calculate subtotals
                                                $totalGross += (float)$row['gross_weight1'];
                                                $totalTare += (float)$row['tare_weight1'];
                                                $totalNet += (float)$row['nett_weight1'];
                                            }
                                        
                                            // Add product-wise subtotal
                                            $message .= '<tr>
                                                <th style="font-size: 11px;" colspan="'.($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? '6' : '6').'">Subtotal (' . $product . ')</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalNet/1000, 2) . '</th>
                                            </tr>';
                                        
                                            // Add to grand total
                                            $grandTotalGross += $totalGross;
                                            $grandTotalTare += $totalTare;
                                            $grandTotalNet += $totalNet;
                                        }
                                        
                                        $message .= '</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="font-size: 11px;" colspan="'.($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? '6' : '6').'">Grand Total</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>
                                                </tr>
                                            </tfoot>';
                                        $message .= '</tbody>';
                                        
                                    $message .= '</table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
    }
    /*else{
        $empQuery = "select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
        count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
        count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
        count.remark, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
        count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
        count.productName = products.id AND status.id=count.status AND units.id=count.unit AND count.deleted = '0' AND count.id=?";

        if ($select_stmt = $db->prepare($empQuery)) {
            $select_stmt->bind_param('s', $id);

            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                

                if ($row = $result->fetch_assoc()) {
                    $message = '<html>
                    <head>
                        <title>Html to PDF</title>
                    </head>
                    <body>
                        <h3>'.$compname.'</h3>
                        <p>No.34, Jalan Bagan 1, <br>Taman Bagan, 13400 Butterworth.<br> Penang. Malaysia.</p>
                        <p>TEL: 6043325822 | EMAIL: admin@synctronix.com.my</p><hr>
                        <table style="width:100%">
                        <tr>
                            <td>
                                <h4>CUSTOMER NAME: '.$row['customer_name'].'</h4>
                            </td>
                            <td>
                                <h4>SERIAL NO: '.$row['serialNo'].'</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>No.34, Jalan Bagan 1, <br>Taman Bagan, <br>13400 Butterworth. Penang. Malaysia.</p>
                            </td>
                            <td>
                                <h4>Status: '.$row['status'].'</h4>
                                <p>Date: 23/03/2022<br>Delivery No: '.$row['deliveryNo'].'</p>
                            </td>
                        </tr>
                        </table>
                        <table style="width:100%; border:1px solid black;">
                        <tr>
                            <th style="border:1px solid black;">Vehicle No.</th>
                            <th style="border:1px solid black;">Product Name</th>
                            <th style="border:1px solid black;">Date & Time</th>
                            <th style="border:1px solid black;">Weight</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">'.$row['veh_number'].'</td>
                            <td style="border:1px solid black;">'.$row['product_name'].'</td>
                            <td style="border:1px solid black;">'.$row['dateTime'].'</td>
                            <td style="border:1px solid black;">'.$row['unitWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Tare Weight</td>
                            <td style="border:1px solid black;">'.$row['tare'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Net Weight</td>
                            <td style="border:1px solid black;">'.$row['actualWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">M.O.Q</td>
                            <td style="border:1px solid black;">'.$row['moq'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Total Weight</td>
                            <td style="border:1px solid black;">'.$row['totalWeight'].' '.$row['units'].'</td>
                        </tr>
                        </table>
                        <p>Remark: '.$row['remark'].'</p>
                    </body>
                </html>';
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));
            }
        }
    } */
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>