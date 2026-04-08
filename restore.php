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
                                            <div class="row justify-content-center">
                                                <div class="col-md-6">
                                                    <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx" required>
                                                </div>
                                            </div>
                                            <a href="template/Weight_Backup_Template.xlsx" download>
                                                <button type="button" id="downloadTemplate" class="btn btn-success mt-3">
                                                    <i class="ri-download-2-line me-1"></i>
                                                    Download Template
                                                </button>
                                            </a>
                                            <button type="button" id="uploadButton" class="btn btn-primary mt-3">
                                                <i class="ri-upload-line me-1"></i> Upload and Restore
                                            </button>
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

                            <!-- Error Modal -->
                            <div class="modal fade" id="errorModal" style="display:none">
                                <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header bg-gray-dark color-palette">
                                            <h4 class="modal-title">Error Log</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="form-group">
                                                    <ol id="errorList" class="text-danger mt-2" style="padding-left: 20px;"></ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  

                            <div class="modal fade" id="uploadModal">
                                <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                    <div class="modal-content">
                                        <form role="form" id="uploadForm">
                                            <div class="modal-header bg-gray-dark color-palette">
                                                <h4 class="modal-title">Preview Data</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
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

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>

    <script type="text/javascript">
    $(function () {
        $('#uploadButton').on('click', function () {
            var fileInput = $('#excelFile');
            var file = fileInput[0].files[0];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var data = e.target.result;
                // Process data and display preview
                displayPreview(data);
            };

            reader.readAsBinaryString(file);

            $('#uploadModal').modal('show');
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

            $.ajax({
                url: 'php/restoreWeight.php',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj.status === 'success') {
                        $('#spinnerLoading').hide();
                        window.location.reload();
                    } 
                    else if (obj.status === 'failed') {
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    } 
                    else if (obj.status === 'error') {
                        $('#spinnerLoading').hide();
                        $('#uploadModal').modal('hide');
                        $('#errorModal').find('#errorList').empty();
                        var errorMessage = obj.message;
                        for (var i = 0; i < errorMessage.length; i++) {
                            $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);                            
                        }
                        $('#errorModal').modal('show');
                    } 
                    else {
                        $('#spinnerLoading').hide();
                    }
                }
            });
        });

        // $('#uploadForm').on('submit', function(e){
        //     e.preventDefault();
        //     var formData = new FormData();
        //     var fileInput = $('#excelFile')[0];
            
        //     if(fileInput.files.length === 0){
        //         alert("Please select a file");
        //         return;
        //     }
            
        //     formData.append('excelFile', fileInput.files[0]);
            
        //     $('#spinnerLoading').show();
        //     $.ajax({
        //         url: 'php/restoreWeight.php',
        //         type: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response){
        //             var obj = JSON.parse(response);
        //             if(obj.status === 'success'){
        //                 $('#excelFile').val('');
        //                 $('#spinnerLoading').hide();
                        
        //                 if(obj.errorDetails && obj.errorDetails.length > 0){
        //                     var errorHtml = '<div class="alert alert-warning"><strong>Import Summary:</strong> ' + obj.message + '</div>';
        //                     errorHtml += '<table class="table table-bordered"><thead><tr><th>Row</th><th>Error</th></tr></thead><tbody>';
        //                     obj.errorDetails.forEach(function(error){
        //                         errorHtml += '<tr><td>' + error.row + '</td><td>' + error.error + '</td></tr>';
        //                     });
        //                     errorHtml += '</tbody></table>';
        //                     $('#errorContent').html(errorHtml);
        //                     $('#errorModal').modal('show');
        //                 } else {
        //                     alert(obj.message);
        //                 }
        //             }
        //             else{
        //                 $('#spinnerLoading').hide();
        //                 alert(obj.message);
        //             }
        //         },
        //         error: function(){
        //             $('#spinnerLoading').hide();
        //             alert("Error uploading file");
        //         }
        //     });
        // });
    });

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

        // Ensure we handle cases where there may be less than 76 columns
        while (headers.length < 76) {
            headers.push(''); // Adding empty headers to reach 76 columns
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

            // Ensure we handle cases where there may be less than 76 cells in a row
            while (rowData.length < 76) {
                rowData.push(''); // Adding empty cells to reach 76 columns
            }

            for (var j = 0; j < 76; j++) {
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
    </script>
</body>
</html>