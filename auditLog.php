<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Weighing | Synctronix - Weighing System</title>
    <?php include 'layouts/title-meta.php'; ?>

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    
    <?php include 'layouts/head-css.php'; ?>

</head>

<?php include 'layouts/body.php'; ?>

<div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div>
                            <div class="row mb-3 pb-1">
                                <div class="col-12">
                                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                        <div class="flex-grow-1">
                                            <!--h4 class="fs-16 mb-1">Good Morning, Anna!</h4>
                                            <p class="text-muted mb-0">Here's what's happening with your store
                                                today.</p-->
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="javascript:void(0);">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="fromDateSearch" class="form-label">From Date</label>
                                                        <input type="date" class="form-control flatpickrStart" data-provider="flatpickr" id="fromDateSearch">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="toDateSearch" class="form-label">To Date</label>
                                                        <input type="date" class="form-control flatpickrEnd" data-provider="flatpickr" id="toDateSearch">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="reportType" class="form-label">Status</label>
                                                        <select id="reportType" name="reportType" class="form-select" data-choices data-choices-sorting="true" >
                                                            <option value="Customer" selected>Customer</option>
                                                            <!-- <option value="Destination">Destination</option> -->
                                                            <option value="Product">Product</option>
                                                            <option value="Raw Materials">Raw Material</option>
                                                            <option value="Supplier">Supplier</option>
                                                            <option value="Vehicle">Vehicle</option>
                                                            <!-- <option value="Agent">Sales Representative</option> -->
                                                            <option value="Transporter">Transporter</option>
                                                            <option value="Unit">Unit</option>
                                                            <option value="User">User</option>
                                                            <option value="Plant">Plant</option>
                                                            <!-- <option value="Site">Site</option> -->
                                                            <option value="Weight">Weighing</option>
                                                            <!-- <option value="SO">Sales Order</option> -->
                                                            <!-- <option value="PO">Purchase Order</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode customerInput">
                                                    <div class="mb-3">
                                                        <label for="customerCode" class="form-label">Customer Code</label>
                                                        <input type="text" class="form-control" placeholder="Customer Code" name="customerCode" id="customerCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode destinationInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="destinationCode" class="form-label">Destination Code</label>
                                                        <input type="text" class="form-control" placeholder="Destination Code" name="destinationCode" id="destinationCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode productInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="productCode" class="form-label">Product Code</label>
                                                        <input type="text" class="form-control" placeholder="Product Code" name="productCode" id="productCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode rawMatInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="rawMatCode" class="form-label">Raw Material Code</label>
                                                        <input type="text" class="form-control" placeholder="Raw Material Code" name="rawMatCode" id="rawMatCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode supplierInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="supplierCode" class="form-label">Supplier Code</label>
                                                        <input type="text" class="form-control" placeholder="Supplier Code" name="supplierCode" id="supplierCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode vehicleInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="vehicleNo" class="form-label">Vehicle No</label>
                                                        <input type="text" class="form-control" placeholder="Vehicle No" name="vehicleNo" id="vehicleNo">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode agentInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="agentCode" class="form-label">Sales Representative Code</label>
                                                        <input type="text" class="form-control" placeholder="Sales Representative Code" name="agentCode" id="agentCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode transporterInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="transporterCode" class="form-label">Transporter Code</label>
                                                        <input type="text" class="form-control" placeholder="Transporter Code" name="transporterCode" id="transporterCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode unitInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="unit" class="form-label">Unit</label>
                                                        <input type="text" class="form-control" placeholder="Unit Code" name="unit" id="unit">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode userInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="userCode" class="form-label">User Code</label>
                                                        <input type="text" class="form-control" placeholder="User Code" name="userCode" id="userCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode plantInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="plantCode" class="form-label">Plant Code</label>
                                                        <input type="text" class="form-control" placeholder="Plant Code" name="plantCode" id="plantCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode siteInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="siteCode" class="form-label">Site Code</label>
                                                        <input type="text" class="form-control" placeholder="Site Code" name="siteCode" id="siteCode">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode weightInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="weight" class="form-label">Weighing Transaction ID</label>
                                                        <input type="text" class="form-control" placeholder="Weighing Transaction ID" name="weight" id="weight">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode soInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="custPoNo" class="form-label">Customer P/O No</label>
                                                        <input type="text" class="form-control" placeholder="Customer P/O No" name="custPoNo" id="custPoNo">
                                                    </div>
                                                </div>
                                                <div class="col-3 inputCode poInput" style="display:none">
                                                    <div class="mb-3">
                                                        <label for="poNo" class="form-label">P/O No</label>
                                                        <input type="text" class="form-control" placeholder="P/O No" name="poNo" id="poNo">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">  
                                                <div class="col-3">
                                                </div>
                                                <div class="col-3">
                                                </div>
                                                <div class="col-3">
                                                </div>                                                                                                                                                                                                                                                                                                                                        
                                                <div class="col-3">
                                                    <div class="text-end mt-4">
                                                        <button type="button" class="btn btn-primary" id="searchLog">
                                                            <i class="bx bx-search-alt"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>                                                                        
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <div class="row">
                                <div class="col-xl-3 col-md-6 add-new-weight">

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Transporter</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="transporterForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporterCode" class="col-sm-4 col-form-label">Transporter Code</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="transporterCode" name="transporterCode" placeholder="Transporter Code" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="companyRegNo" class="col-sm-4 col-form-label">Company Reg No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="companyRegNo" name="companyRegNo" placeholder="Company Reg No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="companyName" class="col-sm-4 col-form-label">Company Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Customer Code">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine1" class="col-sm-4 col-form-label">Address Line 1</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine1" name="addressLine1" placeholder="Address Line 1">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine2" class="col-sm-4 col-form-label">Address Line 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine2" name="addressLine2" placeholder="Address Line 2">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine3" class="col-sm-4 col-form-label">Address Line 3</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine3" name="addressLine3" placeholder="Address Line 3">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="phoneNo" class="col-sm-4 col-form-label">Phone No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="faxNo" class="col-sm-4 col-form-label">Fax No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="faxNo" name="faxNo" placeholder="Fax No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" id="id" name="id">                                                                                                                                                         
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="submitTransporter">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Previous Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="dataTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr id="headerRow">
                                                                <!-- Column names will be dynamically updated here -->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Table rows will be dynamically updated here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end row-->
                                    </div> <!-- end .h-100-->
                                </div> <!-- end col -->
                            </div><!-- container-fluid -->

                        </div> <!-- end .h-100-->
                    </div> <!-- end col -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->




    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <script src="assets/js/pages/form-validation.init.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>

    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>



<script type="text/javascript">

let table;

$(function () {
    $('#reportType').on('change', function(){
        if($(this).val() == "Customer")
        {
            $('.inputCode').hide();
            $('.customerInput').show();
        }
        else if($(this).val() == "Destination")
        {
            $('.inputCode').hide();
            $('.destinationInput').show();
        }
        else if($(this).val() == "Product")
        {
            $('.inputCode').hide();
            $('.productInput').show();
        }
        else if($(this).val() == "Raw Materials")
        {
            $('.inputCode').hide();
            $('.rawMatInput').show();
        }
        else if($(this).val() == "Supplier")
        {
            $('.inputCode').hide();
            $('.supplierInput').show();
        }
        else if($(this).val() == "Vehicle")
        {
            $('.inputCode').hide();
            $('.vehicleInput').show();
        }
        else if($(this).val() == "Agent")
        {
            $('.inputCode').hide();
            $('.agentInput').show();
        }
        else if($(this).val() == "Transporter")
        {
            $('.inputCode').hide();
            $('.transporterInput').show();
        }
        else if($(this).val() == "Unit")
        {
            $('.inputCode').hide();
            $('.unitInput').show();
        }
        else if($(this).val() == "User")
        {
            $('.inputCode').hide();
            $('.userInput').show();
        }
        else if($(this).val() == "Plant")
        {
            $('.inputCode').hide();
            $('.plantInput').show();
        }
        else if($(this).val() == "Site")
        {
            $('.inputCode').hide();
            $('.siteInput').show();
        }
        else if($(this).val() == "Weight")
        {
            $('.inputCode').hide();
            $('.weightInput').show();
        }
        else if($(this).val() == "SO")
        {
            $('.inputCode').hide();
            $('.soInput').show();
        }
        else if($(this).val() == "PO")
        {
            $('.inputCode').hide();
            $('.poInput').show();
        }
        
    });

    var startDate = new Date();
    startDate.setDate(startDate.getDate() - 1);

    $(".flatpickrStart").flatpickr({
        defaultDate: new Date(startDate), 
        dateFormat: "y-m-d"
    });

    $(".flatpickrEnd").flatpickr({
        defaultDate: new Date(), 
        dateFormat: "y-m-d"
    });

    // Handle change event of the dropdown list
    $('#searchLog').click(function() {
        var selectedValue = $('#reportType').val();
        // Call a function to update the DataTable based on the selected value
        updateDataTable(selectedValue);
    });

    // Function to update the DataTable
    function updateDataTable(selectedValue) {
        // $.ajax({
        //     url: "php/filterAuditLog.php",
        //     type: "POST",
        //     data: { 
        //         selectedValue: selectedValue,
        //         fromDateSearch: $('#fromDateSearch').val(),
        //         toDateSearch: $('#toDateSearch').val(),
        //         customerCode: $('#customerCode').val(),
        //         destinationCode: $('#destinationCode').val(),
        //         productCode: $('#productCode').val(),
        //         rawMatCode: $('#rawMatCode').val(),
        //         supplierCode: $('#supplierCode').val(),
        //         vehicleNo: $('#vehicleNo').val(),
        //         agentCode: $('#agentCode').val(),
        //         transporterCode: $('#transporterCode').val(),
        //         unit: $('#unit').val(),
        //         userCode: $('#userCode').val(),
        //         plantCode: $('#plantCode').val(),
        //         siteCode: $('#siteCode').val(),
        //         weight: $('#weight').val(),
        //         custPoNo: $('#custPoNo').val(),
        //         poNo: $('#poNo').val(),
        //     },
        //     success: function(data) {

        //         if (table) {
        //             table.destroy();
        //         }
        //         // Once you receive the updated DataTable from the server, update the HTML table
        //         var dataTable = data.dataTable;
        //         var columnNames = data.columnNames;

        //         var headerRow = $("#headerRow");
        //         headerRow.empty();

        //         // Update the column names
        //         $.each(columnNames, function(index, columnName) {
        //         var th = $("<th>").text(columnName);
        //         headerRow.append(th);
        //         });

        //         var tableBody = $("#dataTable tbody");
        //         tableBody.empty();

        //         $.each(dataTable, function(index, item) {
        //         var row = $("<tr>");
        //         $.each(columnNames, function(index, columnName) {
        //             var cell = $("<td>").text(item[columnName]);
        //             row.append(cell);
        //         });
        //         tableBody.append(row);
        //         });

        //         // table.draw();
        //         table = $("#dataTable").DataTable();
        //     },
        //     error: function(error) {
        //         console.log("Error occurred while fetching the updated DataTable.");
        //     }
        // });

        $.ajax({
            url: "php/filterAuditLog.php",
            type: "POST",
            data: {
                selectedValue: selectedValue,
                fromDateSearch: $('#fromDateSearch').val(),
                toDateSearch: $('#toDateSearch').val(),
                customerCode: $('#customerCode').val(),
                destinationCode: $('#destinationCode').val(),
                productCode: $('#productCode').val(),
                rawMatCode: $('#rawMatCode').val(),
                supplierCode: $('#supplierCode').val(),
                vehicleNo: $('#vehicleNo').val(),
                agentCode: $('#agentCode').val(),
                transporterCode: $('#transporterCode').val(),
                unit: $('#unit').val(),
                userCode: $('#userCode').val(),
                plantCode: $('#plantCode').val(),
                siteCode: $('#siteCode').val(),
                weight: $('#weight').val(),
                custPoNo: $('#custPoNo').val(),
                poNo: $('#poNo').val(),
            },
            dataType: "json",
            success: function (response) {
                // Destroy and clean existing DataTable
                if ($.fn.DataTable.isDataTable("#dataTable")) {
                    $('#dataTable').DataTable().clear().destroy();
                    $('#dataTable').empty(); // 💥 Important to reset the table headers
                }

                // Generate column definitions dynamically
                let columns = response.columnNames.map(column => ({
                    data: column,
                    title: column
                }));

                // Initialize DataTable with dynamic columns
                table = $("#dataTable").DataTable({
                    data: response.dataTable,
                    columns: columns,
                    responsive: true,
                    autoWidth: false,
                    processing: true,
                    searching: true
                });

                // Enable expandable row for "Weight"
                $('#dataTable tbody').on('click', 'tr', function (e) {
                    var tr = $(this); // The row that was clicked
                    var row = table.row(tr); 

                    // Exclude specific td elements by checking the event target
                    if ($(e.target).closest('td').hasClass('dtr-control') || $(e.target).closest('td').hasClass('action-button')) {
                        return;
                    }

                    if ($('#reportType').val() == 'Weight'){
                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            $.post('php/getWeight.php', { userID: row.data().id, format: 'EXPANDABLE', type: 'Log' }, function (data) {
                                var obj = JSON.parse(data);
                                if (obj.status === 'success') {
                                    row.child(format(obj.message)).show();
                                    tr.addClass("shown");
                                }
                            });
                        }
                    }        
                });
            },
            error: function (error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    $('#exportExcel').on('click', function(){
        var selectedValue = $('#reportType').val();
        var fromDateSearch = $('#fromDateSearch').val();
        var toDateSearch = $('#toDateSearch').val();
        var customerCode = $('#customerCode').val();
        var destinationCode = $('#destinationCode').val();
        var productCode = $('#productCode').val();
        var rawMatCode = $('#rawMatCode').val();
        var supplierCode = $('#supplierCode').val();
        var vehicleNo = $('#vehicleNo').val();
        var agentCode = $('#agentCode').val();
        var transporterCode = $('#transporterCode').val();
        var unit = $('#unit').val();
        var userCode = $('#userCode').val();
        var plantCode = $('#plantCode').val();
        var siteCode = $('#siteCode').val();
        var weight = $('#weight').val();
        var custPoNo = $('#custPoNo').val();
        var poNo = $('#poNo').val();

        window.open("php/exportAuditExcel.php?selectedValue="+selectedValue+"&fromDateSearch="+fromDateSearch+"&toDateSearch="+toDateSearch+
        "&customerCode="+customerCode+"&destinationCode="+destinationCode+"&productCode="+productCode+"&rawMatCode="+rawMatCode+"&supplierCode="+supplierCode+
        "&vehicleNo="+vehicleNo+"&agentCode="+agentCode+"&transporterCode="+transporterCode+"&unit="+unit+"&userCode="+userCode+"&plantCode="+plantCode+
        +"&siteCode="+siteCode+"&weight="+weight+"&custPoNo="+custPoNo+"&poNo="+poNo);
    });

    // $('#exportExcel').click(function() {
    //     var selectedValue = $('#reportType').val();

    //     $.ajax({
    //         url: "php/exportAuditExcel.php",
    //         type: "POST",
    //         data: {
    //             selectedValue: selectedValue,
    //             fromDateSearch: $('#fromDateSearch').val(),
    //             toDateSearch: $('#toDateSearch').val(),
    //             customerCode: $('#customerCode').val(),
    //             destinationCode: $('#destinationCode').val(),
    //             productCode: $('#productCode').val(),
    //             rawMatCode: $('#rawMatCode').val(),
    //             supplierCode: $('#supplierCode').val(),
    //             vehicleNo: $('#vehicleNo').val(),
    //             agentCode: $('#agentCode').val(),
    //             transporterCode: $('#transporterCode').val(),
    //             unit: $('#unit').val(),
    //             userCode: $('#userCode').val(),
    //             plantCode: $('#plantCode').val(),
    //             siteCode: $('#siteCode').val(),
    //             weight: $('#weight').val(),
    //             custPoNo: $('#custPoNo').val(),
    //             poNo: $('#poNo').val(),
    //         },
    //         dataType: "json",
    //         success: function (response) {
    //             window.open(response, '_blank');
    //         },
    //         error: function (error) {
    //             console.error("Error fetching data:", error);
    //         }
    //     });
    // });
});

function format (row) {
    var custSupplier = '';
    var productRawMat = '';
    var orderSuppWeight = '';
    var transactionStatus = '';
    var loadDrum = (row.load_drum == 'LOAD') ? "By-Load" : "By-Drum";
    var exDel = (row.ex_del == 'EX') ? "EX-Quarry" : "Delivered";

    if (row.transaction_status == 'Sales' || row.transaction_status == 'Local'){
        custSupplier = row.customer_code + '-' + row.customer_name;
        productRawMat = row.product_code + '-' + row.product_name;
        orderSuppWeight = row.order_weight;
        transactionStatus = (row.transaction_status == 'Sales') ? 'S - Sales' : 'IT - Internal Transfer';
    }else{
        custSupplier = row.supplier_code + '-' + row.supplier_name;
        productRawMat = row.raw_mat_code + '-' + row.raw_mat_name;
        orderSuppWeight = row.supplier_weight;
        transactionStatus = 'P - Local';
    }
    
    var returnString = `
        <!-- Section 01 -->
        <div class="row">
            <div class="col-6">
                <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>CUSTOMER TYPE:</strong> ${row.weight_type}</p>
                <p><strong>WEIGHT STATUS:</strong> ${transactionStatus}</p>
                <p><strong>TRANSACTION DATE:</strong> ${row.transaction_date}</p>
                <p><strong>MANUAL WEIGHT:</strong> ${row.manual_weight}</p>
                <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
            </div>
            <div class="col-6">
                <p><strong>CONTAINER NO:</strong> ${row.container_no}</p>
                <p><strong>CUSTOMER/SUPPLIER:</strong> ${custSupplier}</p>
                <p><strong>PRODUCT/RAW MATERIAL:</strong> ${productRawMat}</p>
                <p><strong>TRANSPORTER:</strong> ${row.transporter_code} - ${row.transporter}</p>
                <p><strong>PLANT:</strong> ${row.plant_code} - ${row.plant_name}</p>
            </div>
            
        </div>
        <hr>
        <!-- Section 02 -->
        <div class="row">
            <div class="col-6">
                <p><strong>ORDER/SUPPLIER WEIGHT:</strong> ${orderSuppWeight}</p>
                <p><strong>WEIGHT DIFFERENCE:</strong> ${row.reduce_weight}</p>
                <p><strong>UNIT PRICE:</strong> ${row.unit_price}</p>
                <p><strong>TOTAL PRICE:</strong> ${row.total_price}</p>
                <p><strong>REMARK:</strong> ${row.remarks}</p>
            </div>
            <div class="col-6">
                <p><strong>VEHICLE PLATE:</strong> ${row.lorry_plate_no1}</p>
                <p><strong>IN WEIGHT:</strong> ${row.gross_weight1} KG</p>
                <p><strong>IN DATE/TIME:</strong> ${row.gross_weight1_date}</p>
                <p><strong>OUT WEIGHT:</strong> ${row.tare_weight1} KG</p>
                <p><strong>OUT DATE/TIME:</strong> ${row.tare_weight1_date}</p>
                <p><strong>NETT WEIGHT:</strong> ${row.nett_weight1} KG</p>
            </div>
        </div>
    
    `;
    
    return returnString;
}

</script>
    </body>

    </html>