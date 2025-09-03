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
        $Code = $rows['Item'];
        $Name = !empty($rows['Description']) ? trim($rows['Description']) : '';
        $Description = !empty($rows['Description']) ? trim($rows['Description']) : '';
        $Price = '0.00';
        $action = "1";
        
        if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, description, price, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssss', $Code, $Name, $Description, $Price, $uid, $uid);
            $insert_stmt->execute();
            $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
            $insert_stmt->close();

            $sel = mysqli_query($db,"select count(*) as allcount from Product");
            $records = mysqli_fetch_assoc($sel);
            $totalRecords = $records['allcount'];

            if ($insert_log = $db->prepare("INSERT INTO Product_Log (product_id, product_code, name, description, price, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                $insert_log->bind_param('sssssss', $totalRecords, $Code, $Name, $Description, $Price, $action, $uid);
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
