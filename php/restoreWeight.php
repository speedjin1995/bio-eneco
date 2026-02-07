<?php
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();

$logDir = __DIR__ . '/../logs';
if(!is_dir($logDir)){
    @mkdir($logDir, 0777, true);
}
$logFile = $logDir . '/restore_' . date('Y-m-d') . '.txt';

function writeLog($logFile, $message){
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

if(!isset($_FILES['excelFile'])){
    writeLog($logFile, "FAILED: No file uploaded");
    echo json_encode(array("status" => "failed", "message" => "No file uploaded"));
    exit;
}

$file = $_FILES['excelFile']['tmp_name'];
$fileExtension = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);

if(!in_array($fileExtension, ['xls', 'xlsx'])){
    writeLog($logFile, "FAILED: Invalid file format - $fileExtension");
    echo json_encode(array("status" => "failed", "message" => "Invalid file format. Only .xls and .xlsx allowed"));
    exit;
}

try {
    writeLog($logFile, "START: Processing file - " . $_FILES['excelFile']['name']);
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    $header = array_shift($rows);
    $inserted = 0;
    $errors = 0;
    $errorDetails = array();
    
    foreach($rows as $index => $row){
        // If Id and transaction_id are empty, skip the row
        if(empty($row[0]) && empty($row[1])){
            continue;
        } 

        // Check if transaction_id already exists
        if (!empty($row[1])) {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM Weight WHERE transaction_id = ?");
            $checkStmt->bind_param("s", $row[1]);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();
            
            if($count > 0){
                continue;
            }
        }
        
        $stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, customer_type, transaction_date, 
            lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, 
            plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, 
            supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code, 
            raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, 
            destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, 
            nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, 
            reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, 
            indicator_id, weighbridge_id, created_date, created_by, modified_date, modified_by, indicator_id_2, 
            unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, 
            approved_reason, synced, received, cancelled_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss",
            $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10],
            $row[11], $row[12], $row[13], $row[14], $row[15], $row[16], $row[17], $row[18], $row[19], $row[20],
            $row[21], $row[22], $row[23], $row[24], $row[25], $row[26], $row[27], $row[28], $row[29], $row[30],
            $row[31], $row[32], $row[33], $row[34], $row[35], $row[36], $row[37], $row[38], $row[39], $row[40],
            $row[41], $row[42], $row[43], $row[44], $row[45], $row[46], $row[47], $row[48], $row[49], $row[50],
            $row[51], $row[52], $row[53], $row[54], $row[55], $row[56], $row[57], $row[58], $row[59], $row[60],
            $row[61], $row[62], $row[63], $row[64], $row[65], $row[66], $row[67], $row[68], $row[69], $row[70],
            $row[71], $row[72], $row[73], $row[74], $row[75]
        );
        
        if($stmt->execute()){
            $inserted++;
        } else {
            $errors++;
            $errorDetails[] = array(
                "row" => $index + 2,
                "error" => $stmt->error
            );
        }
    }
    
    writeLog($logFile, "SUCCESS: Inserted: $inserted, Errors: $errors");
    if(!empty($errorDetails)){
        foreach($errorDetails as $err){
            writeLog($logFile, "ERROR: Row {$err['row']} - {$err['error']}");
        }
    }
    
    echo json_encode(
        array(
            "status" => "success", 
            "message" => "Import completed. Inserted: $inserted, Errors: $errors",
            "errorDetails" => $errorDetails
        )
    );
    
} catch(Exception $e) {
    writeLog($logFile, "EXCEPTION: " . $e->getMessage());
    echo json_encode(array("status" => "failed", "message" => "Error: " . $e->getMessage()));
}
?>
