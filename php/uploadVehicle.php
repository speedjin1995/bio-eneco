<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    foreach ($data as $rows) {
        $VehicleNo = !empty($rows['VehicleNo']) ? trim($rows['VehicleNo']) : '';
        $ExQuarryDelivered = !empty($rows['ExQuarryDelivered']) ? trim($rows['ExQuarryDelivered']) : '';

        if ($ExQuarryDelivered == 'Ex-Quarry'){
            $ExQuarryDelivered = 'EX';
        }else{
            $ExQuarryDelivered = 'DEL';
        }

        $TransporterCode = !empty($rows['TransporterCode']) ? trim($rows['TransporterCode']) : '';
        $TransporterName = !empty($rows['TransporterName']) ? trim($rows['TransporterName']) : '';
        $CustomerCode = !empty($rows['CustomerCode']) ? trim($rows['CustomerCode']) : '';
        $CustomerName = !empty($rows['CustomerName']) ? trim($rows['CustomerName']) : '';
        $status = '0';
        $actionId = 1;

        # Company Checking & Processing
        if($TransporterCode != null && $TransporterCode != ''){
            $transporterQuery = "SELECT * FROM Transporter WHERE transporter_code = '$TransporterCode'";
            $transporterDetail = mysqli_query($db, $transporterQuery);
            $transporterRow = mysqli_fetch_assoc($transporterDetail);
            
            if(empty($transporterRow)){
                if($insert_transporter = $db->prepare("INSERT INTO Transporter (transporter_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_transporter->bind_param('ssss', $TransporterCode, $TransporterName, $uid, $uid);
                    $insert_transporter->execute();
                    $transporterId = $insert_transporter->insert_id; // Get the inserted company ID
                    $insert_transporter->close();
                    
                    if ($insert_transporter_log = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_transporter_log->bind_param('sssss', $transporterId, $TransporterCode, $TransporterName, $actionId, $uid);
                        $insert_transporter_log->execute();
                        $insert_transporter_log->close();
                    }    
                }
            }
        }

        # Customer Checking & Processing
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                if($insert_customer = $db->prepare("INSERT INTO Customer (customer_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_customer->bind_param('ssss', $CustomerCode, $CustomerName, $uid, $uid);
                    $insert_customer->execute();
                    $customerId = $insert_customer->insert_id; // Get the inserted customer ID
                    $insert_customer->close();
                    
                    if ($insert_customer_log = $db->prepare("INSERT INTO Customer_Log (customer_id, customer_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_customer_log->bind_param('sssss', $customerId, $CustomerCode, $CustomerName, $actionId, $uid);
                        $insert_customer_log->execute();
                        $insert_customer_log->close();
                    }    
                }
            }
        }

        # Checking for existing Vehicle No.
        if($VehicleNo != null && $VehicleNo != ''){
            $vehQuery = "SELECT * FROM Vehicle WHERE veh_number = '$VehicleNo' AND status = '0'";
            $vehDetail = mysqli_query($db, $vehQuery);
            $vehRow = mysqli_fetch_assoc($vehDetail);

            if(empty($vehRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Vehicle (veh_number, transporter_code, transporter_name, ex_del, customer_code, customer_name, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssss', $VehicleNo, $TransporterCode, $TransporterName, $ExQuarryDelivered, $CustomerCode, $CustomerName, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $vehicleId = $insert_stmt->insert_id;
                    $insert_stmt->close(); 

                    if ($insert_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, transporter_code, transporter_name, ex_del, customer_code, customer_name, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_log->bind_param('sssssssss', $vehicleId, $VehicleNo, $TransporterCode, $TransporterName, $ExQuarryDelivered, $CustomerCode, $CustomerName, $actionId, $uid);
                        $insert_log->execute();
                        $insert_log->close();
                    }  
                }
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
