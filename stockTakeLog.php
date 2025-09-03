<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>


<?php
require_once "php/db_connect.php";

$user = $_SESSION['id'];
$plantId = $_SESSION['plant'];

$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' AND id IN ('27','31','32') ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant_id"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and id IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant_id"]);
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and id IN ('$username')");
}
else{
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}
?>

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
                                                        <label for="plantSearch" class="form-label">Plant</label>
                                                        <select id="plantSearch" class="form-select select2" >
                                                            <?php while($rowPlantF = mysqli_fetch_assoc($plant)){ ?>
                                                                <option value="<?=$rowPlantF['id'] ?>"><?=$rowPlantF['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="rawMatSearch" class="form-label">Raw Material</label>
                                                        <select id="rawMatSearch" class="form-select select2" >
                                                            <option selected>-</option>
                                                            <?php while($rowRawMatF = mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                <option value="<?=$rowRawMatF['id'] ?>"><?=$rowRawMatF['name'] ?></option>
                                                            <?php } ?>
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
                                    
                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="stockTakeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Generate Stock Take Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="stockTakeForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="fromDateTime" class="col-sm-4 col-form-label">From Date/Time</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="fromDateTime" name="fromDateTime" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                                                            
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="toDateTime" class="col-sm-4 col-form-label">To Date/Time</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="toDateTime" name="toDateTime" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="submitStockTake">Submit</button>
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
                                                                <button type="button" id="genStockTake" class="btn btn-primary waves-effect waves-light">
                                                                    <i class="ri-stock-line align-middle me-1"></i>
                                                                    Generate Stock Take
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id ="sixtySeventyTable" class="table table-bordered nowrap table-striped align-middle w-100" style="display:none;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Production</th>
                                                                    <th>O/S</th>
                                                                    <th>Incoming</th>
                                                                    <th>Usage</th>
                                                                    <th>Book Stock</th>
                                                                    <th>P/S</th>
                                                                    <th>Diff Stock</th>
                                                                    <th>Actual Bit Usage (%)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                        <table id ="lfoTable" class="table table-bordered nowrap table-striped align-middle w-100" style="display:none;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Production</th>
                                                                    <th>O/S</th>
                                                                    <th>Incoming</th>
                                                                    <th>P/S</th>
                                                                    <th>Usage</th>
                                                                    <th>Actual LFO Usage (%)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                        <table id ="dieselTable" class="table table-bordered nowrap table-striped align-middle w-100" style="display:none;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Production</th>
                                                                    <th>O/S</th>
                                                                    <th>Incoming</th>
                                                                    <th>M.Reading</th>
                                                                    <th>Transport</th>
                                                                    <th>P/S</th>
                                                                    <th>Actual Burner Usage</th>
                                                                    <th>Actual Diesel Usage (%)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
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

let sixtySeventyTable;
let lfoTable;
let dieselTable;

$(function () {
    const today = new Date();
    const tomorrow = new Date(today);
    const yesterday = new Date(today);
    var startDate = new Date();
    startDate.setDate(startDate.getDate() - 1);

    $('#fromDateTime').flatpickr({
        dateFormat: "Y-m-d",
        defaultDate: today
    });

    $('#toDateTime').flatpickr({
        dateFormat: "Y-m-d",
        defaultDate: today
    });

    $(".flatpickrStart").flatpickr({
        defaultDate: new Date(startDate), 
        dateFormat: "y-m-d"
    });

    $(".flatpickrEnd").flatpickr({
        defaultDate: new Date(), 
        dateFormat: "y-m-d"
    });

    // Initialize all Select2 elements in the modal
    $('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
    });

    // Apply custom styling to Select2 elements in addModal
    $('.select2-container .select2-selection--single').css({
        'padding-top': '4px',
        'padding-bottom': '4px',
        'height': 'auto'
    });

    $('.select2-container .select2-selection__arrow').css({
        'padding-top': '33px',
        'height': 'auto'
    });

    // Handle change event of the dropdown list
    $('#searchLog').click(function() {
        var rawMat = $('#rawMatSearch').val();

        if (rawMat == '27') {
            $('#sixtySeventyTable').show();
            $('#lfoTable').hide();
            $('#dieselTable').hide();
        } else if (rawMat == '31') {
            $('#sixtySeventyTable').hide();
            $('#lfoTable').hide();
            $('#dieselTable').show();
        } else if (rawMat == '32') {
            $('#sixtySeventyTable').hide();
            $('#lfoTable').show();
            $('#dieselTable').hide();
        } else {
            $('#sixtySeventyTable').hide();
            $('#lfoTable').hide();
            $('#dieselTable').hide();
        }
        // Call a function to update the DataTable based on the selected value
        if (rawMat == '27' || rawMat == '31' || rawMat == '32') {
            updateDataTable(rawMat);
        } else {
            // If no valid raw material is selected, clear the table
            if ($.fn.DataTable.isDataTable("#sixtySeventyTable")) {
                $('#sixtySeventyTable').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable("#lfoTable")) {
                $('#lfoTable').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable("#dieselTable")) {
                $('#dieselTable').DataTable().clear().destroy();
            }
        }
    });

    $('#exportExcel').on('click', function(){
        var fromDateSearch = $('#fromDateSearch').val();
        var toDateSearch = $('#toDateSearch').val();
        var plantSearch = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var rawMatSearch = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';


        window.open("php/exportStockTake.php?fromDateSearch="+fromDateSearch+"&toDateSearch="+toDateSearch+"&plant="+plantSearch+"&rawMaterial="+rawMatSearch);
    });

    $('#genStockTake').on('click', function(){
        $(('#stockTakeModal')).modal('show');
    });

    $('#submitStockTake').on('click', function(){
        if($('#stockTakeForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/insertStockTakeLog.php', $('#stockTakeForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success'){
                    window.location.reload();
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $("#failBtn").attr('data-toast-text', 'Something went wrong!' );
                    $("#failBtn").click();
                }
            });
        }
    });
});

// Function to update the DataTable
function updateDataTable(selectedValue) {
    if (selectedValue == '27') {
        // Destroy and clean existing DataTable
        if ($.fn.DataTable.isDataTable("#sixtySeventyTable")) {
            $('#sixtySeventyTable').DataTable().clear().destroy();
        }

        //Create new Datatable
        sixtySeventyTable = $("#sixtySeventyTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'paging': false,
            'searching': false,
            'info': false,
            'lengthChange': false,
            'ordering': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterStockTakeLog.php',
                'data': {
                    fromDateSearch: $('#fromDateSearch').val(),
                    toDateSearch: $('#toDateSearch').val(),
                    rawMat: $('#rawMatSearch').val(),
                    plant: $('#plantSearch').val(),
                } 
            },
            'columns': [
                { data: 'declaration_datetime' },
                { data: 'sixty_seventy_production' },
                { data: 'sixty_seventy_os' },
                { data: 'sixty_seventy_incoming' },
                { data: 'sixty_seventy_usage' },
                { data: 'sixty_seventy_bookstock' },
                { data: 'sixty_seventy_ps' },
                { data: 'sixty_seventy_diffstock' },
                { data: 'sixty_seventy_actual_usage' }
            ]
        });
    }else if (selectedValue == '32'){
        // Destroy and clean existing DataTable
        if ($.fn.DataTable.isDataTable("#lfoTable")) {
            $('#lfoTable').DataTable().clear().destroy();
        }

        //Create new Datatable
        lfoTable = $("#lfoTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'paging': false,
            'searching': false,
            'info': false,
            'lengthChange': false,
            'ordering': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterStockTakeLog.php',
                'data': {
                    fromDateSearch: $('#fromDateSearch').val(),
                    toDateSearch: $('#toDateSearch').val(),
                    rawMat: $('#rawMatSearch').val(),
                    plant: $('#plantSearch').val(),
                } 
            },
            'columns': [
                { data: 'declaration_datetime' },
                { data: 'lfo_production' },
                { data: 'lfo_os' },
                { data: 'lfo_incoming' },
                { data: 'lfo_ps' },
                { data: 'lfo_usage' },
                { data: 'lfo_actual_usage' }
            ]
        });
    }else if (selectedValue == '31'){
        // Destroy and clean existing DataTable
        if ($.fn.DataTable.isDataTable("#dieselTable")) {
            $('#dieselTable').DataTable().clear().destroy();
        }

        //Create new Datatable
        dieselTable = $("#dieselTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'paging': false,
            'searching': false,
            'info': false,
            'lengthChange': false,
            'ordering': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterStockTakeLog.php',
                'data': {
                    fromDateSearch: $('#fromDateSearch').val(),
                    toDateSearch: $('#toDateSearch').val(),
                    rawMat: $('#rawMatSearch').val(),
                    plant: $('#plantSearch').val(),
                } 
            },
            'columns': [
                { data: 'declaration_datetime' },
                { data: 'diesel_production' },
                { data: 'diesel_os' },
                { data: 'diesel_incoming' },
                { data: 'diesel_mreading' },
                { data: 'diesel_transport' },
                { data: 'diesel_ps' },
                { data: 'diesel_usage' },
                { data: 'diesel_actual_usage' }
            ]
        });
    }
}

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