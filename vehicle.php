<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
    require_once "php/db_connect.php";

    $transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
    $customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
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
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Vehicle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="vehicleForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehicleNo" class="col-sm-4 col-form-label">Vehicle No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="vehicleNo" name="vehicleNo" placeholder="Vehicle No" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="vehicleWeight" class="col-sm-4 col-form-label">Vehicle Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="vehicleWeight" name="vehicleWeight" placeholder="Vehicle Weight" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="exDel" class="col-sm-4 col-form-label">Ex-Quarry/Delivered</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualEx" value="true">
                                                                                            <label class="form-check-label" for="manualEx">
                                                                                               Ex-Quarry
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualDel" value="false" checked>
                                                                                            <label class="form-check-label" for="manualDel">
                                                                                               Delivered
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" id="transporter" name="transporter">
                                                                                            <option value="" selected disabled hidden>Please Select</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="customer" class="col-sm-4 col-form-label">Customer</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" id="customer" name="customer">
                                                                                            <option value="" selected disabled hidden>Please Select</option>
                                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <input type="hidden" class="form-control" id="id" name="id">
                                                                            <input type="hidden" id="transporterCode" name="transporterCode">
                                                                            <input type="hidden" id="transporterName" name="transporterName">
                                                                            <input type="hidden" id="customerCode" name="customerCode">
                                                                                                                                                         
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="submitVehicle">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="uploadModal" style="display:none">
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
                                                        <button type="button" class="btn btn-danger" id="uploadVehicle">Save changes</button>
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
                                                                <a href="template/Vehicle_Template.xlsx" download>
                                                                    <button type="button" id="downloadTemplate" class="btn btn-info waves-effect waves-light">
                                                                        <i class="ri-file-pdf-line align-middle me-1"></i>
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
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="fa-solid fa-ban align-middle me-1"></i>
                                                                    Delete Vehicle
                                                                </button>
                                                                <button type="button" id="addVehicle" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New Vehicle
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="vehicleTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Vehicle No</th>
                                                                    <!--th>Vehicle Weight</th-->
                                                                    <!--th>EX-Quarry/Delivered</th-->
                                                                    <th>Transporter</th>
                                                                    <!--th>Customer</th-->
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



<script type="text/javascript">

var table;

$(function () {
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

    table = $("#vehicleTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadVehicle.php'
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
            { data: 'veh_number' },
            /*{ data: 'vehicle_weight' },
            { 
                data: 'ex_del',
                render: function ( data, type, row ) {
                    if (data == 'EX'){
                        return "EX-Quarry";
                    }else{
                        return "Delivered";
                    }
                }
            },*/
            { data: 'transporter_name' },
            //{ data: 'customer_name' },
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

    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#vehicleTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });
    
    // $.validator.setDefaults({
    //     submitHandler: function() {
    $('#submitVehicle').on('click', function(){
        if($('#vehicleForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/vehicle.php', $('#vehicleForm').serialize(), function(data){
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
                    alert(obj.message);
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

    $('#addVehicle').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#vehicleNo').val("");
        $('#addModal').find('#vehicleWeight').val("");
        $('#addModal').find('#transporter').val("");
        $('#addModal').find('#transporterCode').val("");
        $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
        $('#addModal').find('#customer').val("");
        $('#addModal').find('#customerCode').val("");

        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal').modal('show');
        
        $('#vehicleForm').validate({
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

    $('#vehicleNo').on('keyup', function(){
        var x = $('#vehicleNo').val();
        x = x.toUpperCase();
        $('#addModal').find('#vehicleNo').val(x);
    });

    //transporter
    $('#transporter').on('change', function(){
        $('#transporterCode').val($('#transporter :selected').data('code'));
        $('#transporterName').val($(this).val());
    });

    //customer
    $('#customer').on('change', function(){
        $('#customerCode').val($('#customer :selected').data('code'));
    });

    $('input[name="exDel"]').change(function() {
        var exDel = $('input[name="exDel"]:checked').val();
        if (exDel == 'true'){ //ex-quarry
            $('#transporter').val('OWN TRANSPORTATION').trigger('change');
            $('#transporter').val('OWN TRANSPORTATION').attr('disabled', true);
            $('#customer').val('').attr('disabled', false).trigger('change');
        }else{ // delivered
            $('#transporter').val('').attr('disabled', false).trigger('change');
            $('#customer').val('').attr('disabled', true).trigger('change');
        }
    });

    $('#uploadVehicle').on('click', function(){
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
            url: 'php/uploadVehicle.php',
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
                    window.location.reload();
                } 
                else if (obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                } 
                else {
                    $('#spinnerLoading').hide();
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', 'Failed to save');
                    $("#failBtn").click();
                }
            }
        });
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
        window.open("php/exportMasterData.php?selectedValue=Vehicle");
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

        $("#vehicleTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteVehicle.php', {userID: selectedIds, type: 'MULTI'}, function(data){
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
            alert("Please select at least one vehicle to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getVehicle.php', {userID: id}, function(data)
    {
        var obj = JSON.parse(data);
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#vehicleNo').val(obj.message.veh_number);
            $('#addModal').find('#vehicleWeight').val(obj.message.vehicle_weight);
            if (obj.message.ex_del == 'EX'){
                $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true).trigger('change');
            }else{
                $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
            }
            $('#addModal').find('#transporter').val(obj.message.transporter_name).trigger('change');
            $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
            $('#addModal').find('#customer').val(obj.message.customer_name).trigger('change');
            $('#addModal').find('#customerCode').val(obj.message.customer_code);

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal').modal('show');
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            alert(obj.message);
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            alert(obj.message);
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    $('#spinnerLoading').show();
    if (confirm('Are you sure you want to cancel this item?')) {
        $.post('php/deleteVehicle.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                table.ajax.reload();
                $('#spinnerLoading').hide();
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                alert(obj.message);
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                alert(obj.message);
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

    // Ensure we handle cases where there may be less than 6 columns
    while (headers.length < 6) {
        headers.push(''); // Adding empty headers to reach 6 columns
    }

    // Create HTML table headers
    var htmlTable = '<table style="width:50%;"><thead><tr>';
    headers.forEach(function(header) {
        htmlTable += '<th>' + header + '</th>';
    });
    htmlTable += '</tr></thead><tbody>';

    // Iterate over the data and create table rows
    for (var i = 1; i < jsonData.length; i++) {
        htmlTable += '<tr>';
        var rowData = jsonData[i];

        // Ensure we handle cases where there may be less than 6 cells in a row
        while (rowData.length < 6) {
            rowData.push(''); // Adding empty cells to reach 6 columns
        }

        for (var j = 0; j < 6; j++) {
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
    $.post('php/reactivateMasterData.php', {userID: id, type: "Vehicle"}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
            table.ajax.reload();
            $('#spinnerLoading').hide();
            $("#successBtn").attr('data-toast-text', obj.message);
            $("#successBtn").click();
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            alert(obj.message);
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            alert(obj.message);
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }

        $('#spinnerLoading').hide();
    });
  }

  $('#spinnerLoading').hide();
}

$('#vehicleForm').validate({
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