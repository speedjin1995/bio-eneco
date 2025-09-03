<?php
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$url = "https://sturgeon-still-falcon.ngrok-free.app/shippers";

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
    require_once 'db_connect.php';
    $services = 'PullTransporter';
    $requests = json_encode($data);

    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;
    $agents = $data['data'];
    
    foreach ($agents as $agent) {
        $code = $db->real_escape_string($agent['CODE']);
        $desc = $db->real_escape_string($agent['DESCRIPTION']);
        $active = ($agent['ISACTIVE'] === "True") ? 1 : 0;

        // Check if the agent already exists
        $checkQuery = "SELECT COUNT(*) AS count FROM Transporter WHERE transporter_code = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result['count'] > 0) {
            // Update if exists
            $updateQuery = "UPDATE Transporter SET name = ?, modified_by = ? WHERE transporter_code = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bind_param("sss", $desc, $uid, $code);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert if not exists
            $insertQuery = "INSERT INTO Transporter (transporter_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bind_param("ssss", $code, $desc, $uid, $uid);
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
    require_once 'db_connect.php';
    $services = 'PullTransporter';
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