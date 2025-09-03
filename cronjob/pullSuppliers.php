<?php
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$url = "https://sturgeon-still-falcon.ngrok-free.app/suppliers";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 100);
curl_setopt($curl, CURLOPT_VERBOSE, true);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode([
        "status" => "failed",
        "message" => curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

// Decode the JSON
$data = json_decode($response, true);

if (!empty($data['data'])) {
    require_once __DIR__ . '/../php/db_connect.php';
    $services = 'PullSupplier';
    $requests = json_encode($data);
    
    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;
    
    $agents = $data['data'];
    
    foreach ($agents as $agent) {
        $code = $db->real_escape_string($agent['CODE']);
        $name = $db->real_escape_string($agent['COMPANYNAME']);
        $addr1 = $db->real_escape_string($agent['ADDRESS1']);
        $addr2 = $db->real_escape_string($agent['ADDRESS2']);
        $addr3 = $db->real_escape_string($agent['ADDRESS3']);
        $addr4 = $db->real_escape_string($agent['ADDRESS4']);
        $active = ($agent['STATUS'] === "A") ? 1 : 0;

        // Check if the agent already exists
        $checkQuery = "SELECT COUNT(*) AS count FROM Supplier WHERE supplier_code = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result['count'] > 0) {
            // Update if exists
            $updateQuery = "UPDATE Supplier SET name = ?, address_line_1 = ?, address_line_2 = ?, address_line_3 = ?, address_line_4 = ?, modified_by = ? WHERE supplier_code = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bind_param("sssssss", $name, $addr1, $addr2, $addr3, $addr4, $uid, $code);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert if not exists
            $insertQuery = "INSERT INTO Supplier (supplier_code, name, address_line_1, address_line_2, address_line_3, address_line_4, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bind_param("ssssssss", $code, $name, $addr1, $addr2, $addr3, $addr4, $uid, $uid);
            $insertStmt->execute();
            $insertStmt->close();
        }

        //$stmt->close();
    }

    $response = json_encode(
        array(
            "status" => "success",
            "message" => "Data synced successfully!"
        )
    );
    $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
    $stmtU->bind_param('ss', $response, $invid);
    $stmtU->execute();

    $db->close();
    echo $response;
} 
else {
    require_once __DIR__ . '/../php/db_connect.php';
    $services = 'PullSupplier';
    $requests = json_encode($data);
    
    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;
    
    $response = json_encode(
        array(
            "status" => "failed",
            "message" => "Invalid data received from API"
        )
    );
    $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
    $stmtU->bind_param('ss', $response, $invid);
    $stmtU->execute();

    $db->close();
    echo $response;
}
?>