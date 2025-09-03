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
        $SiteCode = !empty($rows['SiteCode']) ? trim($rows['SiteCode']) : '';
        $SiteName = !empty($rows['SiteName']) ? trim($rows['SiteName']) : '';
        $AddressLine1 = !empty($rows['AddressLine1']) ? trim($rows['AddressLine1']) : '';
        $AddressLine2 = !empty($rows['AddressLine2']) ? trim($rows['AddressLine2']) : '';
        $AddressLine3 = !empty($rows['AddressLine3']) ? trim($rows['AddressLine3']) : '';
        $PhoneNo = !empty($rows['PhoneNo']) ? trim($rows['PhoneNo']) : '';
        $FaxNo = !empty($rows['FaxNo']) ? trim($rows['FaxNo']) : '';
        
        # Check if Site exist in DB
        $status = "0";
        $siteQuery = "SELECT * FROM Site WHERE site_code = '$SiteCode' AND status = '$status'";
        $siteDetail = mysqli_query($db, $siteQuery);
        $siteRow = mysqli_fetch_assoc($siteDetail);

        if(empty($siteRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Site (site_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssss', $SiteCode, $SiteName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $uid, $uid);
                $insert_stmt->execute();
                $siteId = $insert_stmt->insert_id; // Get the inserted site ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Site_Log (site_id, site_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssssss', $siteId, $SiteCode, $SiteName, $AddressLine1, $AddressLine2, $AddressLine3, $PhoneNo, $FaxNo, $action, $uid);
                    $insert_log->execute();
                    $insert_log->close();
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
