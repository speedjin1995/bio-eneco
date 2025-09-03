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
        $Code = $rows['Code'];
        $Name = !empty($rows['Name']) ? trim($rows['Name']) : '';
        $RegNo = !empty($rows['RegNo']) ? trim($rows['RegNo']) : '';
        $Address1 = !empty($rows['Addr1']) ? $rows['Addr1'] : '';
        $Address2 = !empty($rows['Addr2']) ? $rows['Addr2'] : '';
        $Address3 = !empty($rows['Addr3']) ? $rows['Addr3'] : '';
        $Address4 = !empty($rows['Addr4']) ? $rows['Addr4'] : '';
        $Phone = !empty($rows['Tel']) ? $rows['Tel'] : '';
        $Fax = !empty($rows['Fax']) ? $rows['Fax'] : '';
        $action = "1";
        
        if ($insert_stmt = $db->prepare("INSERT INTO Supplier (supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $Code, $RegNo, $Name, $Address1, $Address2, $Address3, $Address4, $Phone, $Fax, $uid, $uid);
            $insert_stmt->execute();
            $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
            $insert_stmt->close();

            $sel = mysqli_query($db,"select count(*) as allcount from Customer");
            $records = mysqli_fetch_assoc($sel);
            $totalRecords = $records['allcount'];

            if ($insert_log = $db->prepare("INSERT INTO Supplier_Log (supplier_id, supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_log->bind_param('ssssssssssss', $totalRecords, $Code, $RegNo, $Name, $Address1, $Address2, $Address3, $Address4, $Phone, $Fax, $action, $uid);
                $insert_log->execute();
                $insert_log->close();
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
