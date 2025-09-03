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
    $fileName = $_GET['selectedValue']."-audit-data_" . date('Y-m-d') . ".xls";
}

## Search 
$searchQuery = " ";
if($_GET['fromDateSearch'] != null && $_GET['fromDateSearch'] != ''){
    $fromDate = new DateTime($_GET['fromDateSearch']);
    $fromDateTime = date_format($fromDate,"Y-m-d 00:00:00");
    $searchQuery = " WHERE event_date >= '".$fromDateTime."'";
}

if($_GET['toDateSearch'] != null && $_GET['toDateSearch'] != ''){
    $toDate = new DateTime($_GET['toDateSearch']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
    $searchQuery .= " and event_date <= '".$toDateTime."'";
}

if($_GET['selectedValue'] == "Customer")
{
    if($_GET['customerCode'] != null && $_GET['customerCode'] != '' && $_GET['customerCode'] != '-'){
    $searchQuery .= " and customer_code = '".$_GET['customerCode']."'";
    }
}

if($_GET['selectedValue'] == "Destination")
{
    if($_GET['destinationCode'] != null && $_GET['destinationCode'] != '' && $_GET['destinationCode'] != '-'){
    $searchQuery .= " and destination_code = '".$_GET['destinationCode']."'";
    }
}

if($_GET['selectedValue'] == "Product")
{
    if($_GET['productCode'] != null && $_GET['productCode'] != ''){
    $searchQuery .= " and product_code like '%".$_GET['productCode']."%'";
    }
}

if($_GET['selectedValue'] == "Raw Materials")
{
    if($_GET['rawMatCode'] != null && $_GET['rawMatCode'] != ''){
    $searchQuery .= " and raw_mat_code like '%".$_GET['rawMatCode']."%'";
    }
}

if($_GET['selectedValue'] == "Supplier")
{
    if($_GET['supplierCode'] != null && $_GET['supplierCode'] != '' && $_GET['supplierCode'] != '-'){
    $searchQuery .= " and supplier_code = '".$_GET['supplierCode']."'";
    }
}

if($_GET['selectedValue'] == "Vehicle")
{
    if($_GET['vehicleNo'] != null && $_GET['vehicleNo'] != '' && $_GET['vehicleNo'] != '-'){
    $searchQuery .= " and veh_number = '".$_GET['vehicleNo']."'";
    }
}

if($_GET['selectedValue'] == "Agent")
{
    if($_GET['agentCode'] != null && $_GET['agentCode'] != '' && $_GET['agentCode'] != '-'){
    $searchQuery .= " and agent_code = '".$_GET['agentCode']."'";
    }
}

if($_GET['selectedValue'] == "Transporter")
{
    if($_GET['transporterCode'] != null && $_GET['transporterCode'] != '' && $_GET['transporterCode'] != '-'){
    $searchQuery .= " and transporter_code = '".$_GET['transporterCode']."'";
    }
}

if($_GET['selectedValue'] == "Unit")
{
    if($_GET['unit'] != null && $_GET['unit'] != '' && $_GET['unit'] != '-'){
    $searchQuery .= " and unit = '".$_GET['unit']."'";
    }
}

if($_GET['selectedValue'] == "User")
{
    if($_GET['userCode'] != null && $_GET['userCode'] != ''){
    $searchQuery .= " and user_code like '%".$_GET['userCode']."%'";
    }
}

if($_GET['selectedValue'] == "Plant")
{
    if($_GET['plantCode'] != null && $_GET['plantCode'] != ''){
    $searchQuery .= " and plant_code like '%".$_GET['plantCode']."%'";
    }
}

if($_GET['selectedValue'] == "Site")
{
    if($_GET['siteCode'] != null && $_GET['siteCode'] != ''){
    $searchQuery .= " and site_code like '%".$_GET['siteCode']."%'";
    }
}

if($_GET['selectedValue'] == "Weight")
{
    if($_GET['weight'] != null && $_GET['weight'] != ''){
    $searchQuery .= " and transaction_id like '%".$_GET['weight']."%'";
    }
}

if($_GET['selectedValue'] == "SO")
{
    if($_GET['custPoNo'] != null && $_GET['custPoNo'] != ''){
    $searchQuery .= " and order_no like '%".$_GET['custPoNo']."%'";
    }
}

if($_GET['selectedValue'] == "PO")
{
    if($_GET['poNo'] != null && $_GET['poNo'] != ''){
    $searchQuery .= " and po_no like '%".$_GET['poNo']."%'";
    }
}

if($_GET['selectedValue'] == "Customer")
{
    ## Fetch records
    $empQuery = "select * from Customer_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['customer_code'])){
            $customerId = $row['customer_id'];
            $customerData = searchCustomerAuditById($customerId, $db); 
    
            if (!empty($customerData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Customer Code"=>$customerData['customer_code'],
                "Company Reg No"=>$customerData['company_reg_no'],
                "Company Name"=>$customerData['name'],
                "Address line 1"=>$customerData['address_line_1'],
                "Address line 2"=>$customerData['address_line_2'],
                "Address line 3"=>$customerData['address_line_3'],
                "Phone No"=>$customerData['phone_no'],
                "Fax No"=>$customerData['fax_no'],
                "Action"=> searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
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
            "Action"=> searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
    }

    $columnNames = ["Customer Code", "Company Reg No", "Company Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No", "Action", "Action By", "Event Date", ];
}

if($_GET['selectedValue'] == "Destination")
{
    ## Fetch records
    $empQuery = "select * from Destination_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['destination_code'])){
            $destinationId = $row['destination_id'];
            $destinationData = searchDestinationAuditById($destinationId, $db);
    
            if (!empty($destinationData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Destination Code"=>$destinationData['destination_code'],
                "Destination Name"=>$destinationData['name'],
                "Description"=>$destinationData['description'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Destination Code"=>$row['destination_code'],
            "Destination Name"=>$row['name'],
            "Description"=>$row['description'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
    }

    $columnNames = ["Destination Code", "Destination Name", "Description", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Product")
{
    ## Fetch records
    $empQuery = "select * from Product_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['product_code'])){
            $productId = $row['product_id'];
            $productData = searchProductAuditById($productId, $db);
           
            if (!empty($productData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Product Code"=>$productData['product_code'],
                "Product Name"=>$productData['name'],
                "Product Price"=>$productData['price'],
                "Description"=>$productData['description'],
                "Variance Type"=>$productData['variance'],
                "High"=>$productData['high'],
                "Low"=>$productData['low'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Product Code"=>$row['product_code'],
            "Product Name"=>$row['name'],
            "Product Price"=>$row['price'],
            "Description"=>$row['description'],
            "Variance Type"=>$row['variance'],
            "High"=>$row['high'],
            "Low"=>$row['low'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
        
    }

    $columnNames = ["Product Code", "Product Name", "Product Price", "Description", "Variance Type", "High", "Low", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Raw Materials")
{
    ## Fetch records
    $empQuery = "select * from Raw_Mat_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if(empty($row['raw_mat_code'])){
            $rawMatId = $row['raw_mat_id'];
            $rawMatData = searchRawMatAuditById($rawMatId, $db);
    
            if (!empty($rawMatData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Raw Material Code"=>$rawMatData['raw_mat_code'],
                "Raw Material Name"=>$rawMatData['name'],
                "Raw Material Price"=>$rawMatData['price'],
                "Description"=>$rawMatData['description'],
                "Variance Type"=>$rawMatData['variance'],
                "High"=>$rawMatData['high'],
                "Low"=>$rawMatData['low'],
                "Type"=>$rawMatData['type'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
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
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
        
    }

    $columnNames = ["Raw Material Code", "Raw Material Name", "Raw Material Price", "Description", "Variance Type", "High", "Low", "Type", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Supplier")
{
    ## Fetch records
    $empQuery = "select * from Supplier_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['supplier_code'])){
            $supplierId = $row['supplier_id'];
            $supplierData = searchSupplierAuditById($supplierId, $db);
    
            if (!empty($supplierData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Supplier Code"=>$supplierData['supplier_code'],
                "Company Reg No"=>$supplierData['company_reg_no'],
                "Supplier Name"=>$supplierData['name'],
                "Address line 1"=>$supplierData['address_line_1'],
                "Address line 2"=>$supplierData['address_line_2'],
                "Address line 3"=>$supplierData['address_line_3'],
                "Phone No"=>$supplierData['phone_no'],
                "Fax No"=>$supplierData['fax_no'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date']
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Supplier Code"=>$row['supplier_code'],
            "Company Reg No"=>$row['company_reg_no'],
            "Supplier Name"=>$row['name'],
            "Address line 1"=>$row['address_line_1'],
            "Address line 2"=>$row['address_line_2'],
            "Address line 3"=>$row['address_line_3'],
            "Phone No"=>$row['phone_no'],
            "Fax No"=>$row['fax_no'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date']
            );
        }

        
    }

    $columnNames = ["Supplier Code", "Company Reg No", "Supplier Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Vehicle")
{
    ## Fetch records
    $empQuery = "select * from Vehicle_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['veh_number'])){
            $vehicleId = $row['vehicle_id'];
            $vehicleData = searchVehicleAuditById($vehicleId, $db);
    
            if (!empty($vehicleData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Vehicle No"=>$vehicleData['veh_number'],
                "Vehicle Weight"=>$vehicleData['vehicle_weight'],
                "Transporter Code"=>$vehicleData['transporter_code'],
                "Transporter Name"=>$vehicleData['transporter_name'],
                "EX-Quarry / Delivered"=>($vehicleData['ex_del'] == 'EX') ? "E" : "D",
                "Customer Code"=>$vehicleData['customer_code'],
                "Customer Name"=>$vehicleData['customer_name'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Vehicle No"=>$row['veh_number'],
            "Vehicle Weight"=>$row['vehicle_weight'],
            "Transporter Code"=>$row['transporter_code'],
            "Transporter Name"=>$row['transporter_name'],
            "EX-Quarry / Delivered"=>($row['ex_del'] == 'EX') ? "E" : "D",
            "Customer Code"=>$row['customer_code'],
            "Customer Name"=>$row['customer_name'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
        
    }

    $columnNames = ["Vehicle No", "Vehicle Weight", "Transporter Code", "Transporter Name", "EX-Quarry / Delivered", "Customer Code", "Customer Name", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Agent")
{
    ## Fetch records
    $empQuery = "select * from Agents_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['agent_code'])){
            $agentId = $row['agent_id'];
            $agentData = searchAgentAuditById($agentId, $db);
            
            if (!empty($agentData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Sales Representative Code"=>$agentData['agent_code'],
                "Sales Representative Name"=>$agentData['name'],
                "Description"=>$agentData['description'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Sales Representative Code"=>$row['agent_code'],
            "Sales Representative Name"=>$row['name'],
            "Description"=>$row['description'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }  
    }

    $columnNames = ["Sales Representative Code", "Sales Representative Name", "Description", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Transporter")
{
    ## Fetch records
    $empQuery = "select * from Transporter_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['transporter_code'])){
            $transporterId = $row['transporter_id'];
            $transporterData = searchTransporterAuditById($transporterId, $db);
            
            if (!empty($transporterData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Transporter Code"=>$transporterData['transporter_code'],
                "Company Reg No"=>$transporterData['company_reg_no'],
                "Transporter Name"=>$transporterData['name'],
                "Address line 1"=>$transporterData['address_line_1'],
                "Address line 2"=>$transporterData['address_line_2'],
                "Address line 3"=>$transporterData['address_line_3'],
                "Phone No"=>$transporterData['phone_no'],
                "Fax No"=>$transporterData['fax_no'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Transporter Code"=>$row['transporter_code'],
            "Company Reg No"=>$row['company_reg_no'],
            "Transporter Name"=>$row['name'],
            "Address line 1"=>$row['address_line_1'],
            "Address line 2"=>$row['address_line_2'],
            "Address line 3"=>$row['address_line_3'],
            "Phone No"=>$row['phone_no'],
            "Fax No"=>$row['fax_no'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
        
    }

    $columnNames = ["Transporter Code", "Company Reg No", "Transporter Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Unit")
{
    ## Fetch records
    $empQuery = "select * from Unit_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['unit'])){
            $unitId = $row['unit_id'];
            $unitData = searchUnitAuditById($unitId, $db);

            if (!empty($unitData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Unit"=>$unitData['unit'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Unit"=>$row['unit'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
    }

    $columnNames = ["Unit", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "User")
{
    ## Fetch records
    $empQuery = "select * from Users_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['employee_code'])){
            $userId = $row['user_id'];
            $userData = searchUserAuditById($userId, $db);

            if (!empty($userData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Employee Code"=>$userData['employee_code'],
                "Username"=>$userData['username'],
                "Name"=>$userData['name'],
                "Email"=>$userData['useremail'],
                "Role"=>$userData['role'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Employee Code"=>$row['employee_code'],
            "Username"=>$row['username'],
            "Name"=>$row['name'],
            "Email"=>$row['useremail'],
            "Role"=>$row['user_department'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
    }

    $columnNames = ["Employee Code", "Username", "Name", "Email", "Role", "Action", "Action By", "Event Date"];
}


if($_GET['selectedValue'] == "Plant")
{
    ## Fetch records
    $empQuery = "select * from Plant_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['plant_code'])){
            $plantId = $row['plant_id'];
            $plantData = searchPlantAuditById($plantId, $db);

            if (!empty($plantData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Plant Code"=>$plantData['plant_code'],
                "Plant Name"=>$plantData['name'],
                "Address line 1"=>$plantData['address_line_1'],
                "Address line 2"=>$plantData['address_line_2'],
                "Address line 3"=>$plantData['address_line_3'],
                "Phone No"=>$plantData['phone_no'],
                "Fax No"=>$plantData['fax_no'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Plant Code"=>$row['plant_code'],
            "Plant Name"=>$row['name'],
            "Address line 1"=>$row['address_line_1'],
            "Address line 2"=>$row['address_line_2'],
            "Address line 3"=>$row['address_line_3'],
            "Phone No"=>$row['phone_no'],
            "Fax No"=>$row['fax_no'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
        
    }

    $columnNames = ["Plant Code", "Plant Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Site")
{
    ## Fetch records
    $empQuery = "select * from Site_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        if (empty($row['site_code'])){
            $siteId = $row['site_id'];
            $siteData = searchSiteAuditById($siteId, $db);

            if (!empty($siteData)){
                $data[] = array( 
                "id"=>$row['id'],
                "Site Code"=>$siteData['site_code'],
                "Site Name"=>$siteData['name'],
                "Address line 1"=>$siteData['address_line_1'],
                "Address line 2"=>$siteData['address_line_2'],
                "Address line 3"=>$siteData['address_line_3'],
                "Phone No"=>$siteData['phone_no'],
                "Fax No"=>$siteData['fax_no'],
                "Action"=>searchActionNameById($row['action_id'], $db),
                "Action By"=>$row['action_by'],
                "Event Date"=>$row['event_date'],
                );
            }
        }else{
            $data[] = array( 
            "id"=>$row['id'],
            "Site Code"=>$row['site_code'],
            "Site Name"=>$row['name'],
            "Address line 1"=>$row['address_line_1'],
            "Address line 2"=>$row['address_line_2'],
            "Address line 3"=>$row['address_line_3'],
            "Phone No"=>$row['phone_no'],
            "Fax No"=>$row['fax_no'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
            );
        }
    }

    $columnNames = ["Site Code", "Site Name", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "Weight")
{
    ## Fetch records
    $empQuery = "select * from Weight_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
            "id"=>$row['id'],
            "Transaction Id"=>$row['transaction_id'],
            "Weight Status"=>$row['weight_type'],
            "Customer/Supplier"=>($row['transaction_status'] == 'Sales' ? $row['customer_name'] : $row['supplier_name']),
            "Vehicle"=>$row['lorry_plate_no1'],
            "Product/Raw Material"=>($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']),
            "SO/PO"=>$row['purchase_order'],
            "DO"=>$row['delivery_no'],
            "Gross Incoming"=>$row['gross_weight1'],
            "Incoming Date"=>$row['gross_weight1_date'],
            "Tare Outgoing"=>$row['tare_weight1'],
            "Outgoing Date"=>$row['tare_weight1_date'],
            "Nett Weight"=>$row['nett_weight1'],
            "Action"=>searchActionNameById($row['action_id'], $db),
            "Action By"=>$row['action_by'],
            "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Transaction Id", "Weight Status", "Customer/Supplier", "Vehicle", "Product/Raw Material", "SO/PO", "DO", "Gross Incoming", "Incoming Date", "Tare Outgoing", "Outgoing Date", "Nett Weight", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "SO")
{
    ## Fetch records
    $empQuery = "select * from Sales_Order_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Company Code"=>$row['company_code'],
        "Company Name"=>$row['company_name'],
        "Customer Code"=>$row['customer_code'],
        "Customer Name"=>$row['customer_name'],
        "Site Code"=>$row['site_code'],
        "Site Name"=>$row['site_name'],
        "Sales Representative Code"=>$row['agent_code'],
        "Sales Representative Name"=>$row['agent_name'],
        "Destination Code"=>$row['destination_code'],
        "Destination Name"=>$row['destination_name'],
        "Product Code"=>$row['product_code'],
        "Product Name"=>$row['product_name'],
        "Plant Code"=>$row['plant_code'],
        "Plant Name"=>$row['plant_name'],
        "Transporter Code"=>$row['transporter_code'],
        "Transporter Name"=>$row['transporter_name'],
        "Vehicle No"=>$row['veh_number'],
        "EXQ/Del"=>$row['exquarry_or_delivered'],
        "Customer P/O No"=>$row['order_no'],
        "S/O No"=>$row['so_no'],
        "Order Date"=>$row['order_date'],
        "Order Quantity"=>$row['order_quantity'],
        "Balance"=>$row['balance'],
        "Remarks"=>$row['remarks'],
        "Status"=>$row['status'],
        "Action"=>searchActionNameById($row['action_id'], $db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Company Code", "Company Name", "Customer Code", "Customer Name", "Site Code", "Site Name", "Sales Representative Code", "Sales Representative Name", "Destination Code", "Destination Name", "Product Code", "Product Name", "Plant Code", "Plant Name", "Transporter Code", "Transporter Name", "Vehicle No", "EXQ/Del", "Customer P/O No", "S/O No", "Order Date", "Order Quantity", "Balance", "Remarks", "Status", "Action", "Action By", "Event Date"];
}

if($_GET['selectedValue'] == "PO")
{
    ## Fetch records
    $empQuery = "select * from Purchase_Order_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Company Code"=>$row['company_code'],
        "Company Name"=>$row['company_name'],
        "Supplier Code"=>$row['supplier_code'],
        "Supplier Name"=>$row['supplier_name'],
        "Site Code"=>$row['site_code'],
        "Site Name"=>$row['site_name'],
        "Sales Representative Code"=>$row['agent_code'],
        "Sales Representative Name"=>$row['agent_name'],
        "Destination Code"=>$row['destination_code'],
        "Destination Name"=>$row['destination_name'],
        "Raw Material Code"=>$row['raw_mat_code'],
        "Raw Material Name"=>$row['raw_mat_name'],
        "Plant Code"=>$row['plant_code'],
        "Plant Name"=>$row['plant_name'],
        "Transporter Code"=>$row['transporter_code'],
        "Transporter Name"=>$row['transporter_name'],
        "Vehicle No"=>$row['veh_number'],
        "EXQ/Del"=>$row['exquarry_or_delivered'],
        "P/O No"=>$row['po_no'],
        "Order Date"=>$row['order_date'],
        "Order Quantity"=>$row['order_quantity'],
        "Balance"=>$row['balance'],
        "Remarks"=>$row['remarks'],
        "Status"=>$row['status'],
        "Action"=>searchActionNameById($row['action_id'], $db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Company Code", "Company Name", "Supplier Code", "Supplier Name", "Site Code", "Site Name", "Sales Representative Code", "Sales Representative Name", "Destination Code", "Destination Name", "Raw Material Code", "Raw Material Name", "Plant Code", "Plant Name", "Transporter Code", "Transporter Name", "Vehicle No", "EXQ/Del", "P/O No", "Order Date", "Order Quantity", "Balance", "Remarks", "Status", "Action", "Action By", "Event Date"];
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