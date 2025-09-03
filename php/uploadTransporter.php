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
        $TransporterCode = !empty($rows['TransporterCode']) ? trim($rows['TransporterCode']) : '';
        $CompanyRegNo = !empty($rows['CompanyRegNo']) ? trim($rows['CompanyRegNo']) : '';
        $CompanyName = !empty($rows['CompanyName']) ? trim($rows['CompanyName']) : '';
        $AddressLine1 = !empty($rows['AddressLine1']) ? trim($rows['AddressLine1']) : '';
        $AddressLine2 = !empty($rows['AddressLine2']) ? trim($rows['AddressLine2']) : '';
        $AddressLine3 = !empty($rows['AddressLine3']) ? trim($rows['AddressLine3']) : '';
        $PhoneNo = !empty($rows['PhoneNo']) ? trim($rows['PhoneNo']) : '';
        $FaxNo = !empty($rows['FaxNo']) ? trim($rows['FaxNo']) : '';
        $status = '0';
        $actionId = 1;

        # Checking for existing transporter code.
        if($TransporterCode != null && $TransporterCode != ''){
            $transporterQuery = "SELECT * FROM Transporter WHERE transporter_code = '$TransporterCode' AND status = '0'";
            $transporterDetail = mysqli_query($db, $transporterQuery);
            $transporterRow = mysqli_fetch_assoc($transporterDetail);

            if(empty($transporterRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Transporter (transporter_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssssss', $TransporterCode, $CompanyRegNo, $CompanyName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $transporterId = $insert_stmt->insert_id;
                    $insert_stmt->close(); 

                    if ($insert_log = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_log->bind_param('sssssssssss', $transporterId, $TransporterCode, $CompanyRegNo, $CompanyName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $actionId, $uid);
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
