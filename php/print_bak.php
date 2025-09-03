<?php

require_once 'db_connect.php';
include 'phpqrcode/qrlib.php';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_POST['userID'], $_POST["file"])){
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
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
                    $type = $row['transaction_status'];
                    $customerCode = '';
                    $customerName = '';
                    $productCode = $row['product_code'];
                    $productName = $row['product_name'];
                    $transportCode = $row['transporter_code'];
                    $transportName = $row['transporter'];
                    $destinationCode = $row['destination_code'];
                    $destinationName = $row['destination'];
                    $loadingChitNo = $row['transaction_id'];
                    $deliverOrderNo = $row['delivery_no'];
                    $lorryNo = $row['lorry_plate_no1'];
                    $poNo = $row['purchase_order'];
                    $grossWeightDate = new DateTime($row['gross_weight1_date']);
                    $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                    $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                    $formattedTareWeightDate = $tareWeightDate->format('H:i');
                    $grossWeight = number_format($row['gross_weight1'] / 1000, 3);
                    $tareWeight = number_format($row['tare_weight1'] / 1000, 3);
                    $nettWeight = number_format($row['nett_weight1'] / 1000, 3);
                    $sysdate = date("d-m-Y");
                    $weightBy = $row['created_by'];
                    
                    if($type == 'Sales'){
                        $customerCode = $row['customer_code'];
                        $customerName = $row['customer_name'];
                    }
                    else{
                        $customerCode = $row['supplier_code'];
                        $customerName = $row['supplier_name'];
                    }
                    
                    
                    $message = '<html>
                        <head>
                            <title>Weighing | Synctronix - Weighing System</title>
                            <!-- Bootstrap CSS -->
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" type="text/css" />
                            <link href="https://your-cdn-link-to-app.min.css" rel="stylesheet" type="text/css" />
                            <link href="https://your-cdn-link-to-custom.min.css" rel="stylesheet" type="text/css" />

                            <style>
                                @page {
                                    size: A5 landscape;
                                    margin: 10px;
                                }

                                .custom-hr {
                                    border-top: 1px solid #000;        /* Remove the default border */
                                    height: 1px;         /* Define the thickness */
                                    margin: 0;           /* Reset margins */
                                }
                            </style>
                        </head>

                        <body>
                            <div class="container-full">
                                <br>
                                <div class="header mb-3">
                                    <div class="row col-12">
                                        <div class="col-10">
                                            <div class="col-12" style="font-size: 18px; font-weight: bold;margin-left:10px">
                                                BLACKTOP LANCHANG SDN BHD<span style="font-size: 12px; margin-left: 5px">198501006021 (138463-T)</span>
                                            </div>
                                            <div class="col-12" style="font-size: 13px">
                                                <span style="margin-left:10px">Office</span><span style="margin-left:39px">:&nbsp;&nbsp; 37, Jalan Perusahaan Amari, Amari Business Park, 68100 Batu Caves, Selangor Darul Ehsan</span>
                                            </div>
                                            <div class="col-12" style="font-size: 13px">
                                                <span style="margin-left:50px">Tel&nbsp;&nbsp;:&nbsp;&nbsp; +603-6096 0383</span>
                                                <span style="margin-left:10px">Email&nbsp;&nbsp;:&nbsp;&nbsp; lowct@eastrock.com.my</span>
                                                <span style="margin-left:10px">Website&nbsp;&nbsp;:&nbsp;&nbsp; www.eastrock.com.my</span>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <img src="assets/images/eastrock_logo.jpg" alt="East Rock Logo" width="100%" style="margin-left:20px;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-7" style="margin-top:60px">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td width="25%" style="border: 0px solid black;">
                                                        <div class="row">
                                                            <div class="col-12 mt-2" style="height: 25px;font-size: 14px;"><b>CUSTOMER</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>PROJECT</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>PRODUCT</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED TO</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED BY</b></div>
                                                        </div>
                                                    </td>
                                                    <td colspan="2" width="75%" style="border: 1px solid black;">
                                                        <div class="row" style="margin-left: 5px">
                                                            <div class="col-12 mt-2" style="height: 25px;font-size: 14px;">'. $customerCode . ' ' . $customerName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $productCode . ' ' . $productName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $destinationCode . ' ' . $destinationName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $transportCode . ' ' . $transportName .'</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="font-size: 9px;">
                                                    <td width="31%" style="border: 0px solid black; margin-bottom:0px;">
                                                        <div style="margin-top:60px">
                                                            <hr class="custom-hr mb-1">
                                                            <div class="text-center" style="font-size: 11px;">Stamped And Signed</div>
                                                        </div>
                                                    </td>
                                                    <td width="31%" style="border: 0px solid black; padding-bottom:0px; ">
                                                        <div style="margin-top:60px;">
                                                            <hr class="custom-hr mb-1">
                                                            <div class="text-center" style="font-size: 11px;">Lorry Driver</div>
                                                        </div>
                                                    </td>
                                                    <td width="38%" style="border: 1px solid black;">
                                                        <div class="row">
                                                            <div class="col-12 mb-4">
                                                                <span style="font-size: 12px;"><b>Waiting Hours:</b></span>
                                                                <span style="margin-left: 10px; font-size: 12px;"></span>
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <span style="font-size: 12px;"><b>From:</b></span>
                                                                <span style="margin-left: 10px; font-size: 12px;"></span>
                                                            </div>
                                                            <div class="col-12">
                                                                <span style="font-size: 12px;"><b>To:</b></span>
                                                                <span style="margin-left: 10px; font-size: 12px;"></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>                
                                        </table>
                                    </div>
                                    <div class="col-4">
                                        <table class="table">
                                            <tbody style="font-size: 11px">
                                                <tr style="border: 1px solid black;">
                                                    <td colspan="2">
                                                        <div class="row" >
                                                            <div class="col-12 mb-2">
                                                                <span style="font-size: 14px;"><b>Date</b></span><span style="margin-left: 78px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$sysdate.'</span>
                                                            </div>
                                                            <div class="col-12 mb-2">
                                                                <span style="font-size: 14px;"><b>Loading Chit No</b></span><span style="margin-left: 29px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$loadingChitNo.'</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <span style="font-size: 14px;"><b>Delivery Order No</b></span><span style="margin-left: 20px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$deliverOrderNo.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="border: 1px solid black;">
                                                    <td colspan="2">
                                                        <div class="row">
                                                            <div class="col-12 mb-2">
                                                                <span style="font-size: 14px;"><b>Lorry No</b></span><span style="margin-left: 22px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$lorryNo.'</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <span style="font-size: 14px;"><b>P/O No</b></span><span style="margin-left: 27px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$poNo.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="border: 1px solid black;">
                                                    <td style="border: 1px solid black; text-align: center;" width="50%"><b>Time</b></td>
                                                    <td style="border: 1px solid black; text-align: center;" width="50%"><b>Weight (MT)</b></td>
                                                </tr>
                                                <tr style="border: 1px solid black; height: 70px;">
                                                    <td style="border: 1px solid black; text-align: center;" width="50%">
                                                        <span style="font-size: 14px;">'.$formattedGrossWeightDate.'</span>
                                                        <br>
                                                        <span style="font-size: 14px;">'.$formattedTareWeightDate.'</span>
                                                    </td>
                                                    <td style="border: 1px solid black; text-align: center;" width="50%">
                                                        <span style="font-size: 14px;">'.$grossWeight.'</span>
                                                        <br>
                                                        <span style="font-size: 14px;">'.$tareWeight.'</span>
                                                        <hr style="width:30%; margin-left: auto; margin-right: auto; margin-top: 5px;">
                                                        <div style="margin-top: -10px;font-size: 14px;">'.$nettWeight.'</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="border: 0px solid black; padding-bottom: 45px;font-size: 14px;">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span><b>Weighted by :</b></span>
                                                                <span style="margin-left: 15px">'.$weightBy.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="border: 0px solid black; text-align: right;">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span><b style="font-size: 15px">No : '.str_replace('P', '', str_replace('S', '', $loadingChitNo)).'</b><b style="font-size: 25px; color: red;"></b></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody> 
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </body></html>';

                    $select_stmt->close();
                    
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