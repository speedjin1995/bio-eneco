<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
include 'phpqrcode/qrlib.php';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_POST['userID'], $_POST["file"])){
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if (empty($_POST["prePrint"])) {
        $prePrintStatus = 'N';
    } else {
        $prePrintStatus = trim($_POST["prePrint"]);
    }

    if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
        $select_stmt->bind_param('s', $id);

        $compname = '';
        if ($company_stmt = $db->prepare("SELECT * FROM Company WHERE id=?")) {
            $companyId = 1;
            $company_stmt->bind_param('s', $companyId);
            $company_stmt->execute();
            $company_result = $company_stmt->get_result();
            if ($company_row = $company_result->fetch_assoc()) {
                $compname = $company_row['name'];
            }
            $company_stmt->close();
        }

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
                // Plant Info
                $compaddress = '';
                $compaddress2 = '';
                $compaddress3 = '';
                $compphone = '';

                if ($plant_stmt = $db->prepare("SELECT * FROM Plant WHERE plant_code=?")) {
                    $plant_stmt->bind_param('s', $row['plant_code']);
                    $plant_stmt->execute();
                    $plant_result = $plant_stmt->get_result();
                    if ($plant_row = $plant_result->fetch_assoc()) {
                        $compaddress = $plant_row['address_line_1'];
                        $compaddress2 = $plant_row['address_line_2'];
                        $compaddress3 = $plant_row['address_line_3'];
                        $compphone = $plant_row['phone_no'];
                    }
                    $plant_stmt->close();
                }

                // Info Section
                $transactionStatus = $row['transaction_status'];
                $ticketNo = $row['transaction_id'];
                $vehicleNo = $row['lorry_plate_no1'];
                $customerCode = $row['customer_code'];
                $customerName = $row['customer_name'];
                $supplierCode = $row['supplier_code'];
                $supplierName = $row['supplier_name'];
                $transporterCode = $row['transporter_code'];
                $transporterName = $row['transporter'];
                $remarks = $row['remarks'];
                $statusCode = 'S';

                if ($transactionStatus == 'Purchase') {
                    $productCode = $row['raw_mat_code'];
                    $productName = $row['raw_mat_name'];
                    $statusCode = 'P';
                }
                else if($transactionStatus == 'Receive'){
                    $productCode = $row['raw_mat_code'];
                    $productName = $row['raw_mat_name'];
                    $statusCode = 'ITR';
                }
                else if($transactionStatus == 'Local'){
                   $productCode = $row['product_code'];
                    $productName = $row['product_name'];
                    $statusCode = 'IT';
                }
                else{
                    $statusCode = 'S';
                    $productCode = $row['product_code'];
                    $productName = $row['product_name'];
                }

                // Weighing Section
                $transactionDate = new DateTime($row['transaction_date']);
                $formattedTransactionDate = $transactionDate->format('d/m/Y');
                $grossWeight1Date = new DateTime($row['gross_weight1_date']);
                $timeIn = $grossWeight1Date->format('H:i');
                $grossWeight1 = number_format($row['gross_weight1']);
                $tareWeight1Date = new DateTime($row['tare_weight1_date']);
                $timeOut = $tareWeight1Date->format('H:i');
                $tareWeight1 = number_format($row['tare_weight1']);
                $nettWeight1 = number_format($row['nett_weight1']);
                $finalWeight = number_format($row['final_weight']);

                $message = '
                    <html lang="en">
                    <head>
                        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                        <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />
                        <style>
                            @media print {
                                * {
                                    font-family: "Courier New", Courier, monospace !important;
                                }
                            
                                body {
                                    margin: 10px 0;
                                    padding: 0;
                                }
                            
                                table, th, td {
                                    border: 1px dashed black;
                                    border-collapse: collapse;
                                    text-align: center;
                                    padding: 2px;
                                }
                            
                                @page {
                                    size: A5 landscape;
                                    margin: 10mm;
                                }
                            }
                            
                            @page {
                                size: A5 landscape;
                                margin: 10mm;
                            }

                            body {
                                font-family: "Courier New", monospace;
                                font-size: 15px;
                                line-height: 1.2;
                            }

                            .custom-hr {
                                border-top: 1px solid #000;
                                height: 1px;
                                margin: 0;
                            }

                            .body_1 p {
                                margin-bottom: 2px;
                                font-size: 14px;
                            }

                            .info-section {
                                display: flex;
                                justify-content: space-between;
                                margin: 0 10px;
                                white-space: nowrap;
                            }

                            .info-left, .info-right {
                                width: 100%;
                            }

                            .info-line {
                                display: flex;
                                margin-bottom: 2px;
                            }

                            .info-label {
                                width: 170px;
                                display: inline-block;
                            }

                            .info-code {
                                width: 100px;
                                display: inline-block;
                            }

                            .info-name {
                                display: inline-block;
                            }

                            .info-value {
                                flex: 1;
                            }

                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin: 0 10px;
                                font-size: 15px;
                            }

                            table, th, td {
                                border: 1px dashed black;
                            }

                            th, td {
                                padding: 4px;
                                text-align: center;
                                font-weight: normal;
                            }
                            
                            tbody tr td {
                                padding: 8px 10px; /* Add space inside cells */
                                line-height: 1; /* Adjust spacing vertically */
                            }

                            .signature-section {
                                display: flex;
                                justify-content: space-between;
                                margin-top: 50px;
                                text-align: center;
                            }

                            .signature-box {
                                width: 30%;
                                border-top: 1px solid black;
                                padding-top: 5px;
                                font-size: 14px;
                            }
                        </style>
                    </head>

                    <body>
                        <div class="container-fluid">
                            <!-- Header -->
                            <div style="text-align: center; margin-bottom: 5px;">
                                <h3 class="mb-1 fw-bold text-dark">'.$compname.'</h3>
                                <p style="font-size: 14px; margin: 1px 0;">'.$compaddress.'</p>
                                <p style="font-size: 14px; margin: 1px 0;">'.$compaddress2.'</p>
                                <p style="font-size: 14px; margin: 1px 0;">'.$compaddress.'</p>
                                <p style="font-size: 14px; margin: 1px 0;">TEL : '.$compphone.'</p>
                            </div>

                            <!-- Information Section -->
                            <div class="info-section">
                                <div class="info-left">
                                    <div class="info-line">
                                        <span class="info-label">W/B TICKET NUMBER</span>
                                        <span>: '.$ticketNo.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">VEHICLE NO.</span>
                                        <span>: '.$vehicleNo.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">CUSTOMER CODE</span>
                                        <span class="info-code">: '.$customerCode.'</span>
                                        <span class="info-name">'.$customerName.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">SUPPLIER CODE</span>
                                        <span class="info-code">: '.$supplierCode.'</span>
                                        <span class="info-name">'.$supplierName.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">PRODUCT CODE</span>
                                        <span class="info-code">: '.$productCode.'</span>
                                        <span class="info-name">'.$productName.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">TRANSPORTER CODE</span>
                                        <span class="info-code">: '.$transporterCode.'</span>
                                        <span class="info-name">'.$transporterName.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">TRANSACTION TYPE</span>
                                        <span class="info-code">: '.$statusCode.'</span>
                                        <span class="info-code">'.$transactionStatus.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">D.O. NUMBER</span>
                                        <span class="info-code">:</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">REMARK</span>
                                        <span class="info-code">: '.$remarks.'</span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">PRICE/KG</span>
                                        <span class="info-code">: RM 0.00</span>
                                        <span style="margin-left: 50px;">AMOUNT</span>
                                        <span>: RM 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Weight Table -->
                            <table>
                                <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>TIME<br>IN</th>
                                        <th>TIME<br>OUT</th>
                                        <th>FIRST<br>WT (KG)</th>
                                        <th>SECOND<br>WT (KG)</th>
                                        <th>NETT<br>WT (KG)</th>
                                        <th>MOISTURE<br>WT (KG)</th>
                                        <th>FINAL<br>WT (KG)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>'.$formattedTransactionDate.'</td>
                                        <td>'.$timeIn.'</td>
                                        <td>'.$timeOut.'</td>
                                        <td>'.$grossWeight1.'</td>
                                        <td>'.$tareWeight1.'</td>
                                        <td>'.$nettWeight1.'</td>
                                        <td>0</td>
                                        <td>'.$finalWeight.'</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Signatures -->
                            <div class="signature-section">
                                <div class="signature-box">
                                    (W/B CLERK/SUPERVISOR)
                                </div>
                                <div class="signature-box">
                                    (LORRY DRIVER)
                                </div>
                                <div class="signature-box">
                                    (RECEIVED BY)
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
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => 'Unable to read data'
                    )
                );
            }
        }

        $select_stmt->close();
        $db->close();
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
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>