<?php
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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

function formatExcelDate($value) {
    if (empty($value)) return null;

    // If numeric (Excel serial)
    if (is_numeric($value)) {
        return date('Y-m-d H:i:s', Date::excelToTimestamp($value));
    }

    // If string date
    return date('Y-m-d H:i:s', strtotime($value));
}

function buildInsertQuery($table, $data) {
    $columns = [];
    $placeholders = [];
    $values = [];
    $types = '';

    foreach ($data as $column => $value) {
        $columns[] = $column;

        if ($value === null || $value === '') {
            // 🔥 Use MySQL DEFAULT
            $placeholders[] = "DEFAULT";
        } else {
            $placeholders[] = "?";
            $values[] = $value;
            $types .= "s"; // adjust if needed
        }
    }

    $sql = "INSERT INTO $table (" . implode(',', $columns) . ")
            VALUES (" . implode(',', $placeholders) . ")";

    return [$sql, $types, $values];
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
        
        $row[5] = formatExcelDate($row[5]);    // transaction_date
        $row[40] = formatExcelDate($row[40]);  // gross_weight1_date
        $row[42] = formatExcelDate($row[42]);  // tare_weight1_date
        $row[45] = formatExcelDate($row[45]);  // gross_weight2_date
        $row[47] = formatExcelDate($row[47]);  // tare_weight2_date
        $row[56] = formatExcelDate($row[56]);  // ✅ created_date
        $row[58] = formatExcelDate($row[58]);  // ✅ modified_date
        
        $data = [
            "transaction_id" => $row[1],
            "transaction_status" => $row[2],
            "weight_type" => $row[3],
            "customer_type" => $row[4],
            "transaction_date" => $row[5],
            "lorry_plate_no1" => $row[6],
            "lorry_plate_no2" => $row[7],
            "supplier_weight" => $row[8],
            "po_supply_weight" => $row[9],
            "order_weight" => $row[10],
            "tin_no" => $row[11],
            "id_no" => $row[12],
            "id_type" => $row[13],
            "plant_code" => $row[14],
            "plant_name" => $row[15],
            "site_code" => $row[16],
            "site_name" => $row[17],
            "agent_code" => $row[18],
            "agent_name" => $row[19],
            "customer_code" => $row[20],
            "customer_name" => $row[21],
            "supplier_code" => $row[22],
            "supplier_name" => $row[23],
            "product_code" => $row[24],
            "product_name" => $row[25],
            "product_description" => $row[26],
            "ex_del" => $row[27],
            "raw_mat_code" => $row[28],
            "raw_mat_name" => $row[29],
            "container_no" => $row[30],
            "invoice_no" => $row[31],
            "purchase_order" => $row[32],
            "delivery_no" => $row[33],
            "transporter_code" => $row[34],
            "transporter" => $row[35],
            "destination_code" => $row[36],
            "destination" => $row[37],
            "remarks" => $row[38],
            "gross_weight1" => $row[39],
            "gross_weight1_date" => $row[40],
            "tare_weight1" => $row[41],
            "tare_weight1_date" => $row[42],
            "nett_weight1" => $row[43],
            "gross_weight2" => $row[44],
            "gross_weight2_date" => $row[45],
            "tare_weight2" => $row[46],
            "tare_weight2_date" => $row[47],
            "nett_weight2" => $row[48],
            "reduce_weight" => $row[49],
            "final_weight" => $row[50],
            "weight_different" => $row[51],
            "is_complete" => $row[52],
            "is_cancel" => $row[53],
            "is_approved" => $row[54],
            "manual_weight" => $row[55],
            "indicator_id" => $row[56],
            "weighbridge_id" => $row[57],
            "created_date" => $row[58],
            "created_by" => $row[59],
            "modified_date" => $row[60],
            "modified_by" => $row[61],
            "indicator_id_2" => $row[62],
            "unit_price" => $row[63],
            "sub_total" => $row[64],
            "sst" => $row[65],
            "total_price" => $row[66],
            "load_drum" => $row[67],
            "no_of_drum" => $row[68],
            "batch_drum" => $row[69],
            "status" => $row[70],
            "approved_by" => $row[71],
            "approved_reason" => $row[72],
            "synced" => $row[73],
            "received" => $row[74],
            "cancelled_reason" => $row[75]
        ];
        
        list($sql, $types, $values) = buildInsertQuery("Weight", $data);

        $stmt = $db->prepare($sql);
        
        if (!$stmt) {
            $errors++;
            $errorDetails[] = [
                "row" => $index + 2,
                "error" => $db->error
            ];
            continue;
        }
    
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
    
        if($stmt->execute()){
            $inserted++;
        } else {
            $errors++;
            $errorDetails[] = [
                "row" => $index + 2,
                "error" => $stmt->error
            ];
        }
    
        $stmt->close();
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
