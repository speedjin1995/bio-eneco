<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>API Log | Synctronix - Weighing System</title>
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
                                                        <label for="reportType" class="form-label">Service</label>
                                                        <select id="reportType" name="reportType" class="form-select" data-choices data-choices-sorting="true" >
                                                            <option value="PullCustomer" selected>Pull Customer</option>
                                                            <option value="PullProduct">Pull Product</option>
                                                            <option value="PullRawMaterials">Pull Raw Material</option>
                                                            <option value="PullSupplier">Pull Supplier</option>
                                                            <option value="PullAgent">Pull Sales Representative</option>
                                                            <option value="PullTransporter">Pull Transporter</option>
                                                            <option value="PullSO">Pull Sales Order</option>
                                                            <option value="PullPO">Pull Purchase Order</option>
                                                            <option value="PostSO">Post Sales Order</option>
                                                            <option value="PostPO">Post Purchase Order</option>
                                                        </select>
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
                                                            <!-- <div class="flex-shrink-0">
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                            </div>  -->
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
        $.ajax({
            url: "php/filterApiLog.php",
            type: "POST",
            data: {
                selectedValue: selectedValue,
                fromDateSearch: $('#fromDateSearch').val(),
                toDateSearch: $('#toDateSearch').val()
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
});

function format (row) {
    var custSupplier = '';
    var productRawMat = '';
    var orderSuppWeight = '';
    var loadDrum = (row.load_drum == 'LOAD') ? "By-Load" : "By-Drum";
    var exDel = (row.ex_del == 'EX') ? "EX-Quarry" : "Delivered";

    if (row.transaction_status == 'Sales'){
        custSupplier = row.customer_code + '-' + row.customer_name;
        productRawMat = row.product_code + '-' + row.product_name;
        orderSuppWeight = row.order_weight;
    }else{
        custSupplier = row.supplier_code + '-' + row.supplier_name;
        productRawMat = row.raw_mat_code + '-' + row.raw_mat_name;
        orderSuppWeight = row.supplier_weight;
    }

    var returnString = `
    <!-- Weighing Section -->
    <div class="row">
        <div class="col-3">
            <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
            <p><strong>CUSTOMER TYPE:</strong> ${row.weight_type}</p>
            <p><strong>WEIGHT STATUS:</strong> ${row.transaction_status}</p>
            <p><strong>TRANSACTION DATE:</strong> ${row.transaction_date}</p>
            <p><strong>INVOICE NO:</strong> ${row.invoice_no}</p>
            <p><strong>MANUAL WEIGHT:</strong> ${row.manual_weight}</p>
            <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
            <p><strong>SO/PO NO:</strong> ${row.purchase_order}</p>
        </div>
        <div class="col-3">
            <p><strong>CONTAINER NO:</strong> ${row.container_no}</p>
            <p><strong>CUSTOMER/SUPPLIER:</strong> ${custSupplier}</p>
            <p><strong>PRODUCT/RAW MATERIAL:</strong> ${productRawMat}</p>
            <p><strong>TRANSPORTER:</strong> ${row.transporter_code} - ${row.transporter}</p>
            <p><strong>SALES REPRESENTATIVE:</strong> ${row.agent_code} - ${row.agent_name}</p>
            <p><strong>DESTINATION:</strong> ${row.destination_code} - ${row.destination}</p>
            <p><strong>SITE:</strong> ${row.site_code} - ${row.site_name}</p>
            <p><strong>PLANT:</strong> ${row.plant_code} - ${row.plant_name}</p>
        </div>
        <div class="col-3">
            <p><strong>EX-QUARRY/DELIVERED:</strong> ${exDel}</p>
            <p><strong>BY-LOAD/BY-DRUM:</strong> ${loadDrum}</p>
            <p><strong>ORDER/SUPPLIER WEIGHT:</strong> ${orderSuppWeight}</p>
            <p><strong>WEIGHT DIFFERENCE:</strong> ${row.reduce_weight}</p>
            <p><strong>UNIT PRICE:</strong> ${row.unit_price}</p>
            <p><strong>SUB-TOTAL PRICE:</strong> ${row.sub_total}</p>
            <p><strong>SST (6%):</strong> ${row.sst}</p>
            <p><strong>TOTAL PRICE:</strong> ${row.total_price}</p>
        </div>
        <div class="col-3">
            <p><strong>VEHICLE PLATE:</strong> ${row.lorry_plate_no1}</p>
            <p><strong>NO OF DRUM:</strong> ${row.no_of_drum}</p>
            <p><strong>IN WEIGHT:</strong> ${row.gross_weight1} KG</p>
            <p><strong>IN DATE/TIME:</strong> ${row.gross_weight1_date}</p>
            <p><strong>OUT WEIGHT:</strong> ${row.tare_weight1} KG</p>
            <p><strong>OUT DATE/TIME:</strong> ${row.tare_weight1_date}</p>
            <p><strong>NETT WEIGHT:</strong> ${row.nett_weight1} KG</p>
            <p><strong>REMARK:</strong> ${row.remarks}</p>
        </div>
    </div>`;
    
    return returnString;
}

</script>
    </body>

    </html>