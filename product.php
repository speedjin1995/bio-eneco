<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
    require_once "php/db_connect.php";
    $rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
    $unit = $db->query("SELECT * FROM Unit WHERE status = '0'");
    $unit2 = $db->query("SELECT * FROM Unit WHERE status = '0'");
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' ORDER BY name ASC");
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
    
    <style>
        /* Custom styling for Select2 elements inside #rawMaterialTable */
        #rawMaterialTable .select2-container .select2-selection--single {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            height: auto !important;
        }

        /* Custom styling for the dropdown arrow */
        #rawMaterialTable .select2-container .select2-selection__arrow {
            padding-top: 33px !important;
            height: auto !important;
        }
    </style>

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
                        <div class="h-100">
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

                            <!-- <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="javascript:void(0);">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="customerCode" class="form-label">Customer Code</label>
                                                        <input type="text" class="form-control" placeholder="Customer Code" id="customerCode">
                                                    </div>
                                                </div>
                                                <div class="col-3">

                                                </div>
                                                <div class="col-3">
  
                                                </div>
                                                <div class="col-3">
                                                    <div class="text-end mt-4">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bx bx-search-alt"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>                                                                        
                                    </div>
                                </div>
                            </div> -->
                            
                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <div class="row">
                                <div class="col-xl-3 col-md-6 add-new-weight">

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="productForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productCode" class="col-sm-4 col-form-label">Product Code</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="productCode" name="productCode" placeholder="Product Code" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Product Name" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productPrice" class="col-sm-4 col-form-label">Product Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="productPrice" name="productPrice" placeholder="Product Price">
                                                                                        <!--div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                                                           
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label">Description</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label">Variance Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="varianceType" name="varianceType">
                                                                                            <option value="" selected disabled hidden>Please Select</option>
                                                                                            <option value="W">kg</option>
                                                                                            <option value="P">%</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label">High (+)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="high" name="high" placeholder="High" value="0">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label">Low (-)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="low" name="low" placeholder="Low" value="0">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="basicUom" class="col-sm-4 col-form-label">UOM</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="basicUom" name="basicUom" class="form-select select2" >
                                                                                            <?php while($rowUnit=mysqli_fetch_assoc($unit)){ ?>
                                                                                                <option value="<?=$rowUnit['id'] ?>"><?=$rowUnit['unit'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" id="id" name="id">                                                                                                                                                         
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-header">
                                                                        <div class="d-flex justify-content-between">
                                                                            <div>
                                                                                <h5 class="card-title mb-0">UOM Conversion</h5>
                                                                            </div>
                                                                            <div class="flex-shrink-0">
                                                                                <button type="button" class="btn btn-primary add-uom"><i class="ri-add-circle-line align-middle me-1"></i>Add UOM</button>
                                                                            </div> 
                                                                        </div> 
                                                                    </div>

                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <table class="table table-primary">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th width="10%">No</th>
                                                                                            <th>UOM</th>
                                                                                            <th>Rate</th>
                                                                                            <th>Action</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody id="uomTable"></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-header p-2">
                                                                        <div class="d-flex justify-content-end">
                                                                            <div class="flex-shrink-0">
                                                                                <button type="button" class="btn btn-primary add-material"><i class="ri-add-circle-line align-middle me-1"></i>Add Raw Material</button>
                                                                            </div> 
                                                                        </div>
                                                                    </div>

                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <table class="table table-primary">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th width="7%">No</th>
                                                                                            <th width="17%">Raw Material</th>
                                                                                            <th width="20%">Basic UOM</th>
                                                                                            <th>Weight (KG)</th>
                                                                                            <th width="15%">Plant</th>
                                                                                            <th width="15%">By-Batch/By-Drum</th>
                                                                                            <th width="5%">Action</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody id="rawMaterialTable"></tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="submitProduct">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="uploadModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title">Upload Excel File</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton">Preview Data</button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger" id="submitWeights">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                                                <a href="template/Product_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        Download Template 
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExcel" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Upload Excel
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <button type="button" id="pullSql" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-add-line align-middle me-1"></i>
                                                                    Pull From SQL
                                                                </button>
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="fa-solid fa-ban align-middle me-1"></i>
                                                                    Delete Product
                                                                </button>
                                                                <button type="button" id="addProduct" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New Product
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="productTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Product Code</th>
                                                                    <th>Product Name</th>
                                                                    <th>Product Price</th>
                                                                    <th>Description</th>
                                                                    <th>Basic UOM</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
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

    <script type="text/html" id="rawMaterialDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="no" name="no" readonly>
                <input type="text" class="form-control" id="productRawMatCode" name="productRawMatCode" hidden>
                <input type="text" class="form-control" id="rawMatBasicUomUnitId" name="rawMatBasicUomUnitId" hidden>
            </td>
            <td>
                <select class="form-control select2" style="width: 100%; background-color:white;" id="rawMats" name="rawMats">
                    <?php while($rowRawMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                        <option value="<?=$rowRawMat['id'] ?>" data-name="<?=$rowRawMat['name'] ?>" data-code="<?=$rowRawMat['raw_mat_code'] ?>"><?=$rowRawMat['name']?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <div class="row">
                    <div class="input-group">
                        <input type="number" class="form-control" id="rawMatBasicUom" name="rawMatBasicUom" style="background-color:white;" value="0">
                        <div class="input-group-text rawMatBasicUnit" id="rawMatBasicUnit">MT</div>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" class="form-control input-readonly" id="rawMatWeight" name="rawMatWeight" style="background-color:white;" value="0" readonly>
            </td>
            <td>
                <select class="form-control select2" style="width: 100%; background-color:white;" id="plant" name="plant">
                    <?php while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                        <option value="<?=$rowPlant['id'] ?>" data-name="<?=$rowPlant['name'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name']?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <select class="form-control select2" style="width: 100%; background-color:white;" id="batchDrum" name="batchDrum">
                    <option value="Batch">By-Batch</option>
                    <option value="Drum">By-Drum</option>
                </select>
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="uomDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="uomNo" name="uomNo" readonly>
                <input type="text" class="form-control" id="uomId" name="uomId" hidden>
            </td>
            <td>
                <select class="form-control select2" style="width: 100%; background-color:white;" id="uom" name="uom">
                    <?php while($unitRow=mysqli_fetch_assoc($unit2)){ ?>
                        <option value="<?=$unitRow['id'] ?>"><?=$unitRow['unit']?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" id="rate" name="rate" style="background-color:white;" value="0">
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>


<script type="text/javascript">

var table;
var rowCount = $("#rawMaterialTable").find(".details").length;
var rowNoCount = 1;
var uomRowCount = $("#uomTable").find(".details").length;
var uomNoCount = 1;

$(function () {
    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#productTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });

    // Initialize all Select2 elements in the modal
    $('#addModal .select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addModal') // Ensures dropdown is not cut off
    });

    // Apply custom styling to Select2 elements in addModal
    $('#addModal .select2-container .select2-selection--single').css({
        'padding-top': '4px',
        'padding-bottom': '4px',
        'height': 'auto'
    });

    $('#addModal .select2-container .select2-selection__arrow').css({
        'padding-top': '33px',
        'height': 'auto'
    });

    table = $("#productTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadProducts.php'
        },
        'columns': [
            {
                // Add a checkbox with a unique ID for each row
                data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                className: 'select-checkbox',
                orderable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                }
            },
            { data: 'product_code' },
            { data: 'name' },
            { data: 'price' },
            { data: 'description' },
            { data: 'basic_uom' },
            { data: 'status' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    if(row.status == 'Inactive'){
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item remove-item-btn" id="reactivate'+data+'" onclick="reactivate('+data+')">Reactivate </a></li></ul></div>';
                    }
                    else{
                        // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-success btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                        '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete </a></li></ul></div>';
                    }
                }
            }
        ]       
    });
    
    // $.validator.setDefaults({
    //     submitHandler: function() {
    $('#submitProduct').on('click', function(){
        // custom validation for select2
        $('#addModal .select2[required]').each(function () {
            var select2Field = $(this);
            var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
            var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

            // Check if the value is empty
            if (select2Field.val() === "" || select2Field.val() === null) {
                select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                // Add error message if not already present
                if (select2Container.next('.select2-error').length === 0) {
                    select2Container.after(errorMsg);
                }

                isValid = false;
            } else {
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            }
        });

        if($('#productForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/products.php', $('#productForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success')
                {
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed')
                {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else
                {

                }
            });
        }
        // }
    });

    $('#submitWeights').on('click', function(){
        $('#spinnerLoading').show();
        var formData = $('#uploadForm').serializeArray();
        var data = [];
        var rowIndex = -1;
        formData.forEach(function(field) {
        var match = field.name.match(/([a-zA-Z0-9]+)\[(\d+)\]/);
        if (match) {
            var fieldName = match[1];
            var index = parseInt(match[2], 10);
            if (index !== rowIndex) {
            rowIndex = index;
            data.push({});
            }
            data[index][fieldName] = field.value;
        }
        });

        // Send the JSON array to the server
        $.ajax({
            url: 'php/uploadProducts.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                var obj = JSON.parse(response);
                if (obj.status === 'success') {
                    $('#spinnerLoading').hide();
                    $('#uploadModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                    $('#customerTable').DataTable().ajax.reload(null, false);
                } 
                else if (obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                } 
                else {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', 'Failed to save');
                    $("#failBtn").click();
                }
            }
        });
    });

    $('#addProduct').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#productCode').val("");
        $('#addModal').find('#productName').val("");
        $('#addModal').find('#productPrice').val("");
        $('#addModal').find('#description').val("");
        $('#addModal').find('#varianceType').val("");
        $('#addModal').find('#high').val("0");
        $('#addModal').find('#low').val("0");
        $('#addModal').find('#basicUom').val("").trigger('change');
        $('#rawMaterialTable').html('');
        rowCount = 1;

        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal .select2[required]').each(function () {
            var select2Field = $(this);
            var select2Container = select2Field.next('.select2-container');
            
            select2Container.find('.select2-selection').css('border', ''); // Remove red border
            select2Container.next('.select2-error').remove(); // Remove error message
        });

        $('#addModal').modal('show');
        
        $('#productForm').validate({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    $('#pullSql').on('click', function(){
        $('#spinnerLoading').show();
        // Send the JSON array to the server
        $.ajax({
            url: 'php/pullProducts.php',
            type: 'POST',
            contentType: 'application/json',
            success: function(response) {
                var obj = JSON.parse(response);
                if (obj.status === 'success') {
                    $('#spinnerLoading').hide();
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                    window.location.reload();
                } 
                else if (obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                    window.location.reload();
                } 
                else {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', 'Failed to save');
                    $("#failBtn").click();
                    window.location.reload();
                }
            }
        });
    });

    // Find and remove selected table rows
    $("#rawMaterialTable").on('click', 'button[id^="remove"]', function () {
        $(this).parents("tr").remove();

        $("#rawMaterialTable tr").each(function (index) {
            $(this).find('input[name^="no"]').val(index + 1);
        });

        rowNoCount = ($("#rawMaterialTable").find(".details").length)+1; //Fixed for no issue
    });

    // Event delegation to calculate product percentage from order weight
    $("#rawMaterialTable").on('change', 'input[id^="rawMatBasicUom"]', function(){
        // Retrieve the input's attributes
        const rawMatBasicUomThis = $(this);
        var basicUom = $(this).val();
        var rawMatId = $(this).closest('.details').find('select[id^="rawMats"]').val();
        var basicUomUnitId = rawMatBasicUomThis.closest('.details').find('input[id^="rawMatBasicUomUnitId"]').val();

        if (rawMatId){
            if (basicUomUnitId == 2){
                rawMatBasicUomThis.closest('.details').find('input[id^="rawMatWeight"]').val(basicUom);
            }else{
                // Call to backend to get conversion rate
                if (rawMatId && basicUom){
                    $.post('php/getProdRawMatUOM.php', {userID: rawMatId, type: 'PO'}, function(data)
                    {
                        var obj = JSON.parse(data);
                        if(obj.status === 'success'){
                            // Processing for order quantity (KG)
                            var rate = parseFloat(obj.message.rate);
                            var weight = basicUom/rate;
                            weight = parseInt(weight);

                            rawMatBasicUomThis.closest('.details').find('input[id^="rawMatWeight"]').val(weight);
                        }
                        else if(obj.status === 'failed'){
                            alert(obj.message);
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            alert(obj.message);
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                    });
                }
            }
        }
    });

    // Event delegation to calculate product percentage from order weight
    $("#rawMaterialTable").on('change', 'select[id^="rawMats"]', function(){
        // Retrieve the input's attributes
        var rawMatCode = $(this).closest('.details').find('select[id^="rawMats"]').find(":selected").data('code');
        const rawMatThis = $(this);
        rawMatThis.closest('.details').find('input[id^="productRawMatCode"]').val(rawMatCode);

        if (rawMatCode){
            $.post('php/getProdRawMatUOM.php', {userID: rawMatCode, type: 'PO', action: 'getBasicUOM'}, function(data)
            {
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    // Change Basic UOM
                    var basicUom = obj.message.basic_uom;
                    var basicUomUnitId = obj.message.basic_uom_id;

                    rawMatThis.closest('.details').find('.rawMatBasicUnit').text(basicUom);
                    rawMatThis.closest('.details').find('input[id^="rawMatBasicUomUnitId"]').val(basicUomUnitId);
                }
                else if(obj.status === 'failed'){
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });

        }
    });

    $(".add-material").click(function(){
        var $addContents = $("#rawMaterialDetail").clone();
        $("#rawMaterialTable").append($addContents.html());

        $("#rawMaterialTable").find('.details:last').attr("id", "detail" + rowCount);
        $("#rawMaterialTable").find('.details:last').attr("data-index", rowCount);
        $("#rawMaterialTable").find('#remove:last').attr("id", "remove" + rowCount);

        $("#rawMaterialTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowNoCount);
        $("#rawMaterialTable").find('#productRawMatCode:last').attr('name', 'productRawMatCode['+rowCount+']').attr("id", "productRawMatCode" + rowCount);
        $("#rawMaterialTable").find('#rawMatBasicUomUnitId:last').attr('name', 'rawMatBasicUomUnitId['+rowCount+']').attr("id", "rawMatBasicUomUnitId" + rowCount);
        $("#rawMaterialTable").find('#rawMats:last').attr('name', 'rawMats['+rowCount+']').attr("id", "rawMats" + rowCount).select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
        }).trigger('change');
        $("#rawMaterialTable").find('#rawMatBasicUom:last').attr('name', 'rawMatBasicUom['+rowCount+']').attr("id", "rawMatBasicUom" + rowCount);
        $("#rawMaterialTable").find('#rawMatBasicUnit:last').attr('name', 'rawMatBasicUnit['+rowCount+']').attr("id", "rawMatBasicUnit" + rowCount);
        $("#rawMaterialTable").find('#rawMatWeight:last').attr('name', 'rawMatWeight['+rowCount+']').attr("id", "rawMatWeight" + rowCount);
        $("#rawMaterialTable").find('#plant:last').attr('name', 'plant['+rowCount+']').attr("id", "plant" + rowCount).select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
        });
        $("#rawMaterialTable").find('#batchDrum:last').attr('name', 'batchDrum['+rowCount+']').attr("id", "batchDrum" + rowCount).select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
        });

        rowCount++;
        rowNoCount++;
    });

    // Find and remove selected table rows
    $("#uomTable").on('click', 'button[id^="remove"]', function () {
        $(this).parents("tr").remove();

        $("#uomTable tr").each(function (index) {
            $(this).find('input[name^="uomNo"]').val(index + 1);
        });

        uomNoCount = ($("#uomTable").find(".details").length)+1; //Fixed for no issue
    });

    $(".add-uom").click(function(){
        var $addContents = $("#uomDetail").clone();
        $("#uomTable").append($addContents.html());

        $("#uomTable").find('.details:last').attr("id", "detail" + uomRowCount);
        $("#uomTable").find('.details:last').attr("data-index", uomRowCount);
        $("#uomTable").find('#remove:last').attr("id", "remove" + uomRowCount);

        $("#uomTable").find('#uomNo:last').attr('name', 'uomNo['+uomRowCount+']').attr("id", "uomNo" + uomRowCount).val(uomNoCount);
        $("#uomTable").find('#uomId:last').attr('name', 'uomId['+uomRowCount+']').attr("id", "uomId" + uomRowCount);
        $("#uomTable").find('#uom:last').attr('name', 'uom['+uomRowCount+']').attr("id", "uom" + uomRowCount).select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#uomTable') // Prevents dropdown cutoff inside modals/tables
        });
        $("#uomTable").find('#rate:last').attr('name', 'rate['+uomRowCount+']').attr("id", "rate" + uomRowCount);
    
        // Apply custom styling to Select2 elements in addModal
        $('#uomTable .select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('#uomTable .select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

        uomRowCount++;
        uomNoCount++;
    });

    $('#uploadExcel').on('click', function(){
        $('#uploadModal').modal('show');

        $('#uploadForm').validate({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    $('#exportExcel').on('click', function(){
        window.open("php/exportMasterData.php?selectedValue=Product");
    });

    $('#uploadModal').find('#previewButton').on('click', function(){
        var fileInput = document.getElementById('fileInput');
        var file = fileInput.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var data = e.target.result;
            // Process data and display preview
            displayPreview(data);
        };

        reader.readAsBinaryString(file);
    });

    $('#multiDeactivate').on('click', function () {
        $('#spinnerLoading').show();
        var selectedIds = []; // An array to store the selected 'id' values

        $("#productTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteProduct.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        toastr["success"](obj.message, "Success:");
                        $('#spinnerLoading').hide();
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }

            $('#spinnerLoading').hide();
        } 
        else {
            // Optionally, you can display a message or take another action if no IDs are selected
            alert("Please select at least one product to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getProduct.php', {userID: id}, function(data)
    {
        var obj = JSON.parse(data);
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#productCode').val(obj.message.product_code);
            $('#addModal').find('#productName').val(obj.message.name);
            $('#addModal').find('#productPrice').val(obj.message.price);
            $('#addModal').find('#description').val(obj.message.description);
            $('#addModal').find('#varianceType').val(obj.message.variance);
            $('#addModal').find('#high').val(obj.message.high);
            $('#addModal').find('#low').val(obj.message.low);
            $('#addModal').find('#basicUom').val(obj.message.basic_uom).trigger('change');

            $('#rawMaterialTable').html('');
            rowCount = 1;
            rowNoCount = 1;

            if (obj.message.rawMats.length > 0){
                for(var i = 0; i < obj.message.rawMats.length; i++){
                    var item = obj.message.rawMats[i];
                    var $addContents = $("#rawMaterialDetail").clone();
                    $("#rawMaterialTable").append($addContents.html());

                    $("#rawMaterialTable").find('.details:last').attr("id", "detail" + rowCount);
                    $("#rawMaterialTable").find('.details:last').attr("data-index", rowCount);
                    $("#rawMaterialTable").find('#remove:last').attr("id", "remove" + rowCount);

                    $("#rawMaterialTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(item.no);
                    $("#rawMaterialTable").find('#productRawMatCode:last').attr('name', 'productRawMatCode['+rowCount+']').attr("id", "productRawMatCode" + rowCount).val(item.raw_mat_code);
                    $("#rawMaterialTable").find('#rawMatBasicUomUnitId:last').attr('name', 'rawMatBasicUomUnitId['+rowCount+']').attr("id", "rawMatBasicUomUnitId" + rowCount).val(item.basic_uom_unit_id);
                    $("#rawMaterialTable").find('#rawMats:last').attr('name', 'rawMats['+rowCount+']').attr("id", "rawMats" + rowCount).val(item.raw_mat_id).select2({
                        allowClear: true,
                        placeholder: "Please Select",
                        dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
                    });
                    $("#rawMaterialTable").find('#rawMatBasicUom:last').attr('name', 'rawMatBasicUom['+rowCount+']').attr("id", "rawMatBasicUom" + rowCount).val(item.raw_mat_basic_uom);
                    $("#rawMaterialTable").find('#rawMatWeight:last').attr('name', 'rawMatWeight['+rowCount+']').attr("id", "rawMatWeight" + rowCount).val(item.raw_mat_weight);
                    $("#rawMaterialTable").find('#plant:last').attr('name', 'plant['+rowCount+']').attr("id", "plant" + rowCount).val(item.plant_id).select2({
                        allowClear: true,
                        placeholder: "Please Select",
                        dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
                    });
                    $("#rawMaterialTable").find('#batchDrum:last').attr('name', 'batchDrum['+rowCount+']').attr("id", "batchDrum" + rowCount).val(item.batch_drum).select2({
                        allowClear: true,
                        placeholder: "Please Select",
                        dropdownParent: $('#rawMaterialTable') // Prevents dropdown cutoff inside modals/tables
                    });

                    // Apply custom styling to Select2 elements in addModal
                    $('#rawMaterialTable .select2-container .select2-selection--single').css({
                        'padding-top': '4px',
                        'padding-bottom': '4px',
                        'height': 'auto'
                    });

                    $('#rawMaterialTable .select2-container .select2-selection__arrow').css({
                        'padding-top': '33px',
                        'height': 'auto'
                    });

                    rowCount++;
                    rowNoCount++;
                }
            }

            $('#uomTable').html('');
            uomRowCount = 0;
            uomNoCount = 1;

            if (obj.message.prodUom.length > 0){
                for(var i = 0; i < obj.message.prodUom.length; i++){
                    var item = obj.message.prodUom[i];
                    var $addContents = $("#uomDetail").clone();
                    $("#uomTable").append($addContents.html());

                    $("#uomTable").find('.details:last').attr("id", "detail" + uomRowCount);
                    $("#uomTable").find('.details:last').attr("data-index", uomRowCount);
                    $("#uomTable").find('#remove:last').attr("id", "remove" + uomRowCount);

                    $("#uomTable").find('#uomNo:last').attr('name', 'uomNo['+uomRowCount+']').attr("id", "uomNo" + uomRowCount).val(item.no);
                    $("#uomTable").find('#uomId:last').attr('name', 'uomId['+uomRowCount+']').attr("id", "uomId" + uomRowCount).val(item.id);
                    $("#uomTable").find('#uom:last').attr('name', 'uom['+uomRowCount+']').attr("id", "uom" + uomRowCount).val(item.unit_id).select2({
                        allowClear: true,
                        placeholder: "Please Select",
                        dropdownParent: $('#uomTable') // Prevents dropdown cutoff inside modals/tables
                    });
                    $("#uomTable").find('#rate:last').attr('name', 'rate['+uomRowCount+']').attr("id", "rate" + uomRowCount).val(item.rate);

                    // Apply custom styling to Select2 elements in addModal
                    $('#uomTable .select2-container .select2-selection--single').css({
                        'padding-top': '4px',
                        'padding-bottom': '4px',
                        'height': 'auto'
                    });

                    $('#uomTable .select2-container .select2-selection__arrow').css({
                        'padding-top': '33px',
                        'height': 'auto'
                    });

                    uomRowCount++;
                    uomNoCount++;
                }
            }

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });
            
            $('#addModal').modal('show');
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    $('#spinnerLoading').show();
    if (confirm('Are you sure you want to cancel this item?')) {
        $.post('php/deleteProduct.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                table.ajax.reload();
                $('#spinnerLoading').hide();
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }
    $('#spinnerLoading').hide();
}

function displayPreview(data) {
    // Parse the Excel data
    var workbook = XLSX.read(data, { type: 'binary' });

    // Get the first sheet
    var sheetName = workbook.SheetNames[0];
    var sheet = workbook.Sheets[sheetName];

    // Convert the sheet to an array of objects
    var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    // Get the headers
    var headers = jsonData[0];

    // Ensure we handle cases where there may be less than 15 columns
    while (headers.length < 2) {
        headers.push(''); // Adding empty headers to reach 15 columns
    }

    // Create HTML table headers
    var htmlTable = '<table style="width:100%;"><thead><tr>';
    headers.forEach(function(header) {
        htmlTable += '<th>' + header + '</th>';
    });
    htmlTable += '</tr></thead><tbody>';

    // Iterate over the data and create table rows
    for (var i = 1; i < jsonData.length; i++) {
        htmlTable += '<tr>';
        var rowData = jsonData[i];

        // Ensure we handle cases where there may be less than 15 cells in a row
        while (rowData.length < 2) {
            rowData.push(''); // Adding empty cells to reach 15 columns
        }

        for (var j = 0; j < 2; j++) {
            var cellData = rowData[j];
            var formattedData = cellData;

            // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
            if (typeof cellData === 'number' && cellData > 0) {
                var excelDate = XLSX.SSF.parse_date_code(cellData);
            }

            htmlTable += '<td><input type="text" id="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+(i-1)+'" name="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
        }
        htmlTable += '</tr>';
    }

    htmlTable += '</tbody></table>';

    var previewTable = document.getElementById('previewTable');
    previewTable.innerHTML = htmlTable;
}

function reactivate(id) {
  if (confirm('Do you want to reactivate this item?')) {
    $('#spinnerLoading').show();
    $.post('php/reactivateMasterData.php', {userID: id, type: "Product"}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
            table.ajax.reload();
            $('#spinnerLoading').hide();
            $("#successBtn").attr('data-toast-text', obj.message);
            $("#successBtn").click();
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }

        $('#spinnerLoading').hide();
    });
  }

  $('#spinnerLoading').hide();
}

$('#productForm').validate({
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
</script>
    </body>

    </html>