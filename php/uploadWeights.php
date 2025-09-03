<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $rows) {
        $Status = !empty($rows['Status']) ? trim($rows['Status']) : 'Sales';
        $WeightType = 'Normal';
        $CustomerType = 'Normal';
        $TransDate = !empty($rows['TransDate']) ? DateTime::createFromFormat('d-m-Y', $rows['TransDate'])->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $VehicleNo = !empty($rows['VehicleNo']) ? trim($rows['VehicleNo']) : '';
        $Project = !empty($rows['Project']) ? trim($rows['Project']) : null;
        $ProjectName = !empty($rows['Project']) ? searchProjectByCode(trim($rows['Project']), $db) : null;
        $ED = !empty($rows['ED']) ? $rows['ED'] : 'DEL'; // Default to 'EX' if ED is null

        if ($ED === 'E') {
            $ED = 'EX';
        } 
        else {
            $ED = 'DEL';
        }

        $POorSO = !empty($rows['POorSO']) ? $rows['POorSO'] : null;
        $DeliveryNo = !empty($rows['DeliveryNo']) ? $rows['DeliveryNo'] : null;
        $DestCode = !empty($rows['DestCode']) ? $rows['DestCode'] : null;
        $Destination = !empty($rows['Destination']) ? $rows['Destination'] : null;
        $DocNo = !empty($rows['DocNo']) ? $rows['DocNo'] : null;
        $PlantName = !empty($rows['PlantName']) ? trim($rows['PlantName']) : '';
        $PlantCode = !empty($rows['PlantCode']) ? trim($rows['PlantCode']) : '';
        $transactionId = '';

        $OrderWeight = null;
        $SupplierWeight = null;
        $CustomerName = null;
        $CustomerCode = null;
        $SupplierName = null;
        $SupplierCode = null;
        $ProductName = null;
        $ProductCode = null;
        $RawMatName = null;
        $RawMatCode = null;

        if($PlantCode != '' && $Status != ''){
            if($update_stmt2 = $db->prepare("SELECT * FROM status WHERE status=?")){
                $update_stmt2->bind_param('s', $Status);
    
                if ($update_stmt2->execute()) {
                    $result2 = $update_stmt2->get_result();
                    $id = '1';
                    $transactionId = $PlantCode.'/';
    
                    if ($row2 = $result2->fetch_assoc()) {
                        $transactionId .= $row2['prefix'];
                    }
    
                    $queryPlant = "SELECT sales as curcount FROM Plant WHERE plant_code='$PlantCode'";
    
                    if($Status == 'Purchase'){
                        $queryPlant = "SELECT purchase as curcount FROM Plant WHERE plant_code='$PlantCode'";
                    }
                    else if($Status == 'Local'){
                        $queryPlant = "SELECT locals as curcount FROM Plant WHERE plant_code='$PlantCode'";
                    }
    
                    if ($update_stmt = $db->prepare($queryPlant)) {
                        // Execute the prepared query.
                        if ($update_stmt->execute()) {
                            $result = $update_stmt->get_result();
                            $message = array();
                            
                            if ($row = $result->fetch_assoc()) {
                                $charSize = strlen($row['curcount']);
                                $misValue = $row['curcount'];
            
                                for($i=0; $i<(5-(int)$charSize); $i++){
                                    $transactionId.='0';  // S0000
                                }
                        
                                $transactionId .= $misValue;  //S00009
                            }
                        }
                    }
                }
            }
            
            if($Status == 'Sales'){
                $OrderWeight = !empty($rows['OrderSupplyWeight']) ? trim($rows['OrderSupplyWeight']) : null;
    
                if($rows['UOM'] == 'MT'){
                    $OrderWeight = (float)$OrderWeight * 1000;
                }
    
                $CustomerName = !empty($rows['CompanyName']) ? $rows['CompanyName'] : null;
                $CustomerCode = !empty($rows['Code']) ? $rows['Code'] : null;
                $ProductName = !empty($rows['Description']) ? $rows['Description'] : null;
                $ProductCode = !empty($rows['ItemCode']) ? $rows['ItemCode'] : null;
            }
            else{
                $SupplierWeight = !empty($rows['OrderSupplyWeight']) ? trim($rows['OrderSupplyWeight']) : null;
    
                if($rows['UOM'] == 'MT'){
                    $SupplierWeight = (float)$SupplierWeight * 1000;
                }
    
                $SupplierName = !empty($rows['CompanyName']) ? $rows['CompanyName'] : null;
                $SupplierCode = !empty($rows['Code']) ? $rows['Code'] : null;
                $RawMatName = !empty($rows['Description']) ? $rows['Description'] : null;
                $RawMatCode = !empty($rows['ItemCode']) ? $rows['ItemCode'] : null;
            }

            if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, customer_type, transaction_date, lorry_plate_no1, supplier_weight, order_weight, customer_code, customer_name, supplier_code, supplier_name,
            product_code, product_name, ex_del, raw_mat_code, raw_mat_name, site_code, site_name, purchase_order, delivery_no, destination_code, destination, remarks, plant_code, plant_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssssssssssssssssssssssssss', $transactionId, $Status, $WeightType, $CustomerType, $TransDate, $VehicleNo, $SupplierWeight, $OrderWeight, $CustomerCode, $CustomerName,
                $SupplierCode, $SupplierName, $ProductCode, $ProductName, $ED, $RawMatCode, $RawMatName, $Project, $ProjectName, $POorSO, $DeliveryNo, $DestCode, $Destination, $DocNo, $PlantCode, $PlantName);
                $insert_stmt->execute();
                $insert_stmt->close();
                $misValue++;

                $queryPlantU = "UPDATE Plant SET sales=? WHERE plant_code='$PlantCode'";

                if($Status == 'Purchase'){
                    $queryPlantU = "UPDATE Plant SET purchase=? WHERE plant_code='$PlantCode'";
                }
                else if($Status == 'Local'){
                    $queryPlantU = "UPDATE Plant SET locals=? WHERE plant_code='$PlantCode'";
                }
                
                $update_stmt = $db->prepare($queryPlantU);
                $update_stmt->bind_param('s', $misValue);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
