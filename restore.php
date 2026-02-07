<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>



<head>

    <title>Reports | Synctronix - Weighing System</title>
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
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .modal-header {
            padding: var(1rem, 1rem) !important;
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

                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Restore Weight Data</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center py-5">
                                            <i class="ri-upload-cloud-2-line" style="font-size: 72px; color: #405189;"></i>
                                            <h4 class="mt-4">Upload Excel File to Restore Data</h4>
                                            <p class="text-muted">Select an Excel file (.xls or .xlsx) exported from Weight table</p>
                                            <form id="uploadForm" enctype="multipart/form-data" class="mt-4">
                                                <div class="row justify-content-center">
                                                    <div class="col-md-6">
                                                        <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx" required>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-3">
                                                    <i class="ri-upload-line me-1"></i> Upload and Restore
                                                </button>
                                            </form>
                                            <div id="uploadProgress" class="mt-4" style="display:none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2">Processing file...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    

                        </div> <!-- end .h-100-->

                    </div> <!-- end col -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Import Errors</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="errorContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>

    <script type="text/javascript">
    $(function () {
        $('#uploadForm').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData();
            var fileInput = $('#excelFile')[0];
            
            if(fileInput.files.length === 0){
                alert("Please select a file");
                return;
            }
            
            formData.append('excelFile', fileInput.files[0]);
            
            $('#spinnerLoading').show();
            $.ajax({
                url: 'php/restoreWeight.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    var obj = JSON.parse(response);
                    if(obj.status === 'success'){
                        $('#excelFile').val('');
                        $('#spinnerLoading').hide();
                        
                        if(obj.errorDetails && obj.errorDetails.length > 0){
                            var errorHtml = '<div class="alert alert-warning"><strong>Import Summary:</strong> ' + obj.message + '</div>';
                            errorHtml += '<table class="table table-bordered"><thead><tr><th>Row</th><th>Error</th></tr></thead><tbody>';
                            obj.errorDetails.forEach(function(error){
                                errorHtml += '<tr><td>' + error.row + '</td><td>' + error.error + '</td></tr>';
                            });
                            errorHtml += '</tbody></table>';
                            $('#errorContent').html(errorHtml);
                            $('#errorModal').modal('show');
                        } else {
                            alert(obj.message);
                        }
                    }
                    else{
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                },
                error: function(){
                    $('#spinnerLoading').hide();
                    alert("Error uploading file");
                }
            });
        });
    });
    </script>
</body>
</html>