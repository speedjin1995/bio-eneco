<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
// // Load the database configuration file 
session_start();
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 

// Excel file name for download 
if($_GET["selectedValue"]){
    if($_GET['selectedValue'] == 'Agent'){
        $fileName = "SalesRep-data_" . date('Y-m-d') . ".xls";
    }else{
        $fileName = $_GET['selectedValue']."-data_" . date('Y-m-d') . ".xls";
    }
}

## Search 
$searchQuery = " ";

if($_GET['selectedValue'] == "Customer")
{
    ## Fetch records
    $empQuery = "select * from Customer WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Customer Code"=>$row['customer_code'],
        "Company Reg No"=>$row['company_reg_no'],
        "Company Name"=>$row['name'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no'],
        );
    }

    $columnNames = ["Customer Code", "Company Reg No", "Company Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_GET['selectedValue'] == "Destination")
{
    ## Fetch records
    $empQuery = "select * from Destination WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Destination Code"=>$row['destination_code'],
        "Destination Name"=>$row['name'],
        "Description"=>$row['description'],
        );
    }

    $columnNames = ["Destination Code", "Destination Name", "Description"];
}

if($_GET['selectedValue'] == "Product")
{
    ## Fetch records
    $empQuery = "select * from Product WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Product Code"=>$row['product_code'],
        "Product Name"=>$row['name'],
        "Product Price"=>$row['price'],
        "Description"=>$row['description'],
        "Variance Type"=>$row['variance'],
        "High"=>$row['high'],
        "Low"=>$row['low'],
        );
    }

    $columnNames = ["Product Code", "Product Name", "Product Price", "Description", "Variance Type", "High", "Low"];
}

if($_GET['selectedValue'] == "Raw Materials")
{
    ## Fetch records
    $empQuery = "select * from Raw_Mat WHERE status ='0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Raw Material Code"=>$row['raw_mat_code'],
        "Raw Material Name"=>$row['name'],
        "Raw Material Price"=>$row['price'],
        "Description"=>$row['description'],
        "Variance Type"=>$row['variance'],
        "High"=>$row['high'],
        "Low"=>$row['low'],
        "Type"=>$row['type'],
        );
    }

    $columnNames = ["Raw Material Code", "Raw Material Name", "Raw Material Price", "Description", "Variance Type", "High", "Low", "Type"];
}

if($_GET['selectedValue'] == "Supplier")
{
    ## Fetch records
    $empQuery = "select * from Supplier WHERE status = '0'".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Supplier Code"=>$row['supplier_code'],
        "Company Reg No"=>$row['company_reg_no'],
        "Supplier Name"=>$row['name'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no']
        );
    }

    $columnNames = ["Supplier Code", "Company Reg No", "Supplier Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_GET['selectedValue'] == "Vehicle")
{
    ## Fetch records
    $empQuery = "select * from Vehicle WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Vehicle No"=>$row['veh_number'],
        "Vehicle Weight"=>$row['vehicle_weight'],
        "Transporter Code"=>$row['transporter_code'],
        "Transporter Name"=>$row['transporter_name'],
        "EX-Quarry / Delivered"=>($row['ex_del'] == 'EX') ? "E" : "D",
        "Customer Code"=>$row['customer_code'],
        "Customer Name"=>$row['customer_name'],
        );
    }

    $columnNames = ["Vehicle No", "Vehicle Weight", "Transporter Code", "Transporter Name", "EX-Quarry / Delivered", "Customer Code", "Customer Name"];
}

if($_GET['selectedValue'] == "Agent")
{
    ## Fetch records
    $empQuery = "select * from Agents WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Sales Representative Code"=>$row['agent_code'],
        "Sales Representative Name"=>$row['name'],
        "Description"=>$row['description'],
        );
    }

    $columnNames = ["Sales Representative Code", "Sales Representative Name", "Description"];
}

if($_GET['selectedValue'] == "Transporter")
{
    ## Fetch records
    $empQuery = "select * from Transporter WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Transporter Code"=>$row['transporter_code'],
        "Company Reg No"=>$row['company_reg_no'],
        "Transporter Name"=>$row['name'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no']
        );
    }

    $columnNames = ["Transporter Code", "Company Reg No", "Transporter Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_GET['selectedValue'] == "Unit")
{
    ## Fetch records
    $empQuery = "select * from Unit WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Unit"=>$row['unit'],
        );
    }

    $columnNames = ["Unit"];
}

if($_GET['selectedValue'] == "User")
{
    ## Fetch records
    $empQuery = "select * from Users WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Employee Code"=>$row['employee_code'],
        "Username"=>$row['username'],
        "Name"=>$row['name'],
        "Email"=>$row['useremail'],
        "Role"=>$row['role'],
        );
    }

    $columnNames = ["Employee Code", "Username", "Name", "Email", "Role"];
}


if($_GET['selectedValue'] == "Plant")
{
    ## Fetch records
    $empQuery = "select * from Plant WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Plant Code"=>$row['plant_code'],
        "Plant Name"=>$row['name'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no'],
        );
        
    }

    $columnNames = ["Plant Code", "Plant Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_GET['selectedValue'] == "Site")
{
    ## Fetch records
    $empQuery = "select * from Site WHERE status = '0'";
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Site Code"=>$row['site_code'],
        "Site Name"=>$row['name'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no'],
        );
    }

    $columnNames = ["Site Code", "Site Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

// Display column names as first row 
$excelData = implode("\t", array_values($columnNames)) . "\n";

if(count($data) > 0){
    
    foreach ($data as $row){
        unset($row['id']);
        $lineData = []; // Ensure it starts as an empty array each iteration

        foreach ($row as $rowData) { 
            $lineData[] = $rowData; 
        }

        # Added checking to fix duplicated issue
        if (!empty($lineData)) {
            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
    }
}else{
    $excelData .= 'No records found...'. "\n"; 
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>
