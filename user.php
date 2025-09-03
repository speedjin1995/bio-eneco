<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";
require_once "php/db_connect.php";

// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
$name = $_SESSION["username"];

$query = "SELECT role_code, role_name from roles WHERE role_code <> 'SADMIN' AND deleted = '0'";

if($_SESSION["roles"] == 'ADMIN'){
    $query = "SELECT role_code, role_name from roles WHERE role_code <> 'SADMIN' AND role_code <> 'ADMIN' AND deleted = '0'";
}

$stmt2 = $link->prepare($query);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $code, $name);

// Pull plants
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $query4 = "SELECT id, name FROM Plant WHERE status = '0' and plant_code IN ('$username')";
}
else{
    $query4 = "SELECT id, name FROM Plant WHERE status = '0'";
}

$stmt4 = $link->prepare($query4);
mysqli_stmt_execute($stmt4);
mysqli_stmt_store_result($stmt4);
mysqli_stmt_bind_result($stmt4, $pcode, $pname);
?>

<head>

    <title>Users | Synctronix - Weighing System</title>
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

    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <?php include 'layouts/head-css.php'; ?>

</head>

<?php include 'layouts/body.php'; ?>

<!-- Begin page -->
<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
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
                                                    <h5 class="card-title mb-0">User Records</h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="template/User_Template.xlsx" download>
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
                                                        Delete User
                                                    </button>
                                                    <button type="button" id="addMembers" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                        <i class="ri-add-circle-line align-middle me-1"></i>
                                                        Add New User
                                                    </button>
                                                </div> 
                                            </div> 

                                            <!-- <div class="row">
                                                <div class="col-10">
                                                    <h5 class="card-title mb-0">User Records</h5>
                                                </div>
                                                <div class="col-2 d-flex justify-content-end">
                                                    <button type="button" id="addMembers" class="btn btn-md btn-soft-success" data-bs-toggle="modal" data-bs-target="#addModal">
                                                        <i class="ri-add-circle-line align-middle me-1"></i>
                                                        Add New User
                                                    </button>              
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="card-body">
                                            <table id="usersTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                        <th>Employee Code</th>
                                                        <th>Username</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Plant Name</th>
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
            </div> <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div><!-- end main content-->
    </div><!-- END layout-wrapper -->

    <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
    <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="memberForm" class="needs-validation" novalidate autocomplete="off">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="id" name="id"> 
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="employeeCode" class="col-sm-4 col-form-label">Employee Code </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="employeeCode" name="employeeCode" placeholder="Employee Code" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="username" class="col-sm-4 col-form-label">Username *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="name" class="col-sm-4 col-form-label">Employee Name *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Employee Name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="useremail" class="col-sm-4 col-form-label">Employee Email</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="useremail" name="useremail" placeholder="Employee Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="userpassword" class="col-sm-4 col-form-label">Employee Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="userpassword" name="userpassword" placeholder="Employee Password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="roles" class="col-sm-4 col-form-label">Role *</label>
                                                    <div class="col-sm-8">
                                                        <select id="roles" name="roles" class="select2" required>
                                                            <option select="selected" value="">Please Select</option>
                                                            <?php while(mysqli_stmt_fetch($stmt2)){ ?>
                                                                <option value="<?=$code ?>"><?=$name ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="plantId" class="col-sm-4 col-form-label">Plant</label>
                                                    <div class="col-sm-8">
                                                        <select id="plantId" name="plantId[]" class="form-control" multiple="multiple">
                                                            <?php while(mysqli_stmt_fetch($stmt4)){ ?>
                                                                <option value="<?=$pcode ?>"><?=$pname ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>      
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="allowManual" class="col-sm-4 col-form-label">Allow Manual *</label>
                                                    <div class="col-sm-8">
                                                        <select id="allowManual" name="allowManual" class="form-control" required>
                                                            <option value="Y">YES</option>
                                                            <option value="N">NO</option>
                                                        </select>
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
                                <button type="button" class="btn btn-primary" id="submitMember">Submit</button>
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
                        <button type="button" class="btn btn-danger" id="uploadUser">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <script src="assets/js/pages/form-validation.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <script>
    $(function () {
        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#usersTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });

        // Initialize plantId elements in the modal
        $('#addModal #plantId').select2({
            allowClear: true,
            multiple: true,
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });

        $("#plantId").on("select2:select change", function () {
            $(".select2-selection__choice").css({
                "background-color": "rgb(64, 81, 137)",
                "color": "white"
            });

            $(".select2-selection__choice__remove").css({
                "color": "white"
            });
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
        
        table = $("#usersTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/loadMembers.php'
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
                { data: 'employee_code' },
                { data: 'username' },
                { data: 'name' },
                { data: 'useremail' },
                { data: 'role' },
                { data: 'plant' },
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
        
        $('#submitMember').on('click', function(){
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
            if($('#memberForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/users.php', $('#memberForm').serialize(), function(data){
                    var obj = JSON.parse(data); 

                    if(obj.status === 'success'){
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
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Something wrong when edit');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#addMembers').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#employeeCode').val("");
            $('#addModal').find('#username').val("");
            $('#addModal').find('#name').val("");
            $('#addModal').find('#useremail').val("");
            $('#addModal').find('#userpassword').val("");
            $('#addModal').find('#roles').val("");
            $('#addModal').find('#plantId').val('').trigger('change');
            $('#addModal').find('#allowManual').val("N");

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            $('#addModal').modal('show');
            
            $('#memberForm').validate({
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

        $('#uploadUser').on('click', function(){
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
                url: 'php/uploadUser.php',
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
            window.open("php/exportMasterData.php?selectedValue=User");
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

            $("#usersTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to cancel these items?')) {
                    $.post('php/deleteUser.php', {userID: selectedIds, type: 'MULTI'}, function(data){
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
                alert("Please select at least one user to delete.");
                $('#spinnerLoading').hide();
            }     
        });
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getUser.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#employeeCode').val(obj.message.employee_code);
                $('#addModal').find('#username').val(obj.message.username);
                $('#addModal').find('#name').val(obj.message.name);
                $('#addModal').find('#useremail').val(obj.message.useremail);
                $('#addModal').find('#userpassword').val('');
                $('#addModal').find('#roles').val(obj.message.role_code).trigger('change');
                $("#addModal").find("#plantId").val(JSON.parse(obj.message.plant)).trigger("change");
                $('#addModal').find('#allowManual').val(obj.message.allow_manual);

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal').modal('show');
                
                $('#memberForm').validate({
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
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
            }
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id){
        $('#spinnerLoading').show();
        if (confirm('Are you sure you want to cancel this item?')) {
            $.post('php/deleteUser.php', {userID: id}, function(data){
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

        // Ensure we handle cases where there may be less than 5 columns
        while (headers.length < 5) {
            headers.push(''); // Adding empty headers to reach 5 columns
        }

        // Create HTML table headers
        var htmlTable = '<table style="width:40%;"><thead><tr>';
        headers.forEach(function(header) {
            htmlTable += '<th>' + header + '</th>';
        });
        htmlTable += '</tr></thead><tbody>';

        // Iterate over the data and create table rows
        for (var i = 1; i < jsonData.length; i++) {
            htmlTable += '<tr>';
            var rowData = jsonData[i];

            // Ensure we handle cases where there may be less than 5 cells in a row
            while (rowData.length < 5) {
                rowData.push(''); // Adding empty cells to reach 5 columns
            }

            for (var j = 0; j < 5; j++) {
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
            $.post('php/reactivateMasterData.php', {userID: id, type: "User"}, function(data){
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
    </script>

    </body>

    </html>