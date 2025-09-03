<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";
require_once "php/requires/lookup.php";

if($_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant_id"]);

    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and id IN ('$username')");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and id IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

$role = $_SESSION["roles"];
// $role = 'NORMAL';
// if ($user != null && $user != ''){
//     $stmt3 = $db->prepare("SELECT * from Users WHERE id = ?");
//     $stmt3->bind_param('s', $user);
//     $stmt3->execute();
//     $result3 = $stmt3->get_result();
        
//     if(($row3 = $result3->fetch_assoc()) !== null){
//         $role = $row3['role'];
//     }
// }
?>

<head>

    <title>Dashboard | Synctronix - Weighing System</title>
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
        .dashboard-row {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
    
</head>

<?php include 'layouts/body.php'; ?>

<!-- <div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div> -->

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
                                        <div class="mt-3 mt-lg-0">
                                            <form action="javascript:void(0);">
                                                <div class="row g-3 mb-0 align-items-center">

                                            </form>
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header fs-5 bg-primary" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch" style="color:white">
                                        <i class="mdi mdi-chevron-down pull-right"></i>
                                        Search Records
                                    </div>
                                    <div id="collapseSearch" class="collapse" aria-labelledby="collapseSearch">                                    
                                        <div class="card-body">
                                            <form action="javascript:void(0);">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="fromDateSearch" class="form-label">From Date</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="toDateSearch" class="form-label">To Date</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="plantSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary" id="filterSearch"><i class="bx bx-search-alt"></i> Search</button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table id="dashboardTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%;">
                                                    <thead style="text-align: center; font-size: 14px;">
                                                        <tr>
                                                            <th colspan="3" class="bg-primary" style="color: white"> Dashboard</th>
                                                        </tr>
                                                        <tr style="background-color: rgba(13, 110, 253, 0.6);">
                                                            <th>Pending</th>
                                                            <th>Complete</th>
                                                            <th>Cancelled</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div><!--end row-->
                                    </div> <!-- end .h-100-->
                                </div> <!-- end col -->
                            </div><!-- container-fluid -->

                            <div class="row mt-3" id="displayDashboardDetail" style="display:none;">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table id="dashboardDetailTable" class="table table-bordered table-striped display">
                                                    <thead>
                                                        <tr>
                                                            <th id="dashboardDetailHeader" class="bg-primary" style="color:white">Overall Weighing & Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="dashboardDetailBody">

                                                    </tbody>
                                                </table>
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
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

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
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
    var table;

    $(function () {
        var userRole = '<?=$role ?>';
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1); // January 1st of this year
        const endOfYear = new Date(today.getFullYear(), 11, 31); // December 31st of this year

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: startOfYear
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: endOfYear
        });


        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

        table = $("#dashboardTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'searching': false,
            'paging': false,
            'info': false,
            'ajax': {
                'type': 'POST',
                'url':'php/filterDashboardCount.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    plant: plantNoI
                } 
            },
            'columns': [
                { 
                    data: 'Pending',
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('id', 'Pending');
                        $(td).addClass('clickable-column');
                        if (parseInt(cellData) > 0) {
                            $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                        }        
                    }
                },
                { 
                    data: 'Complete',
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('id', 'Complete');
                        $(td).addClass('clickable-column');
                        if (parseInt(cellData) > 0) {
                            $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                        }
                    }
                },
                { 
                    data: 'Cancelled',
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).attr('id', 'Cancelled');
                        $(td).addClass('clickable-column');
                        if (parseInt(cellData) > 0) {
                            $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                        }
                    }
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('dashboard-row');
            },
        });

        // Attach click event to dynamically created elements
        $('#dashboardTable tbody').on('click', '.clickable-column', function() {
            var status = $(this).attr('id');

            displayDashboardDetail(status);
        });
        
        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

            //Destroy the old Datatable
            $("#dashboardTable").DataTable().clear().destroy();

            //Hide DashboardDetail Table
            $('#displayDashboardDetail').hide();

            //Create new Datatable
            table = $("#dashboardTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'searching': false,
                'paging': false,
                'info': false,
                'ajax': {
                    'type': 'POST',
                    'url':'php/filterDashboardCount.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        plant: plantNoI
                    } 
                },
                'columns': [
                    { 
                        data: 'Pending',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).attr('id', 'Pending');
                            $(td).addClass('clickable-column');
                            if (parseInt(cellData) > 0) {
                                $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                            }        
                        }
                    },
                    { 
                        data: 'Complete',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).attr('id', 'Complete');
                            $(td).addClass('clickable-column');
                            if (parseInt(cellData) > 0) {
                                $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                            }
                        }
                    },
                    { 
                        data: 'Cancelled',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).attr('id', 'Cancelled');
                            $(td).addClass('clickable-column');
                            if (parseInt(cellData) > 0) {
                                $(td).html(cellData + ' <i class="mdi mdi-eye" style="cursor: pointer;"></i>');
                            }
                        }
                    }
                ],
                "createdRow": function(row, data, dataIndex) {
                    $(row).addClass('dashboard-row');
                },
            });
        });

        // Event listener for changes in the #fromDateSearch
        $('#fromDateSearch').on('change', function () {
            const fromDate = $(this).val();
            const toDate = $('#toDateSearch').val();

            if (dateDifferenceInMonths(fromDate, toDate) > 11) {
                alert('The date difference cannot be more than 12 months.');
                const adjustedToDate = new Date(fromDate);
                adjustedToDate.setMonth(adjustedToDate.getMonth() + 12);
                $('#fromDateSearch').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: adjustedToDate
                });
            }
        });

        // Event listener for changes in the #fromDateSearch
        $('#toDateSearch').on('change', function () {
            const fromDate = $('#fromDateSearch').val();
            const toDate = $(this).val();
            
            if (dateDifferenceInMonths(fromDate, toDate) > 11) {
                alert('The date difference cannot be more than 12 months.');
                const adjustedToDate = new Date(fromDate);
                adjustedToDate.setMonth(adjustedToDate.getMonth() + 12);
                $('#toDateSearch').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: adjustedToDate
                });
            }
        });
    });

    function parseDate(dateString) {
        const [day, month, year] = dateString.split("-");
        var reformatDate = year+'-'+month+'-'+day;
        
        return reformatDate; // month is 0-based
    }

    function dateDifferenceInMonths(fromDate, toDate) {
        const from = new Date(parseDate(fromDate)); 
        const to = new Date(parseDate(toDate));
        const months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
        return months;
    }

    function displayDashboardDetail(id){
        var fromDateValue = $('#fromDateSearch').val();
        var toDateValue = $('#toDateSearch').val();
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var rowCount = $('#dashboardDetailTable tbody tr');

        if (fromDateValue && toDateValue){
            $('#displayDashboardDetail').show();

            if (rowCount.length > 0){
                // Destroy old datatable
                $("#dashboardDetailTable").DataTable().clear().destroy();
            }

            $.ajax({
                url: 'php/loadDashboardDetail.php',
                type: 'POST',
                data: {
                    status: id,
                    fromDate: fromDateValue,
                    toDate: toDateValue,
                    plant: plantNoI
                },
                dataType: 'json',
                success: function(response) {
                    // Build dynamic columns but keep the first column fixed
                    var dynamicColumns = response.columns.map(function(col, index) {
                        return { data: col, orderable: false }; // Disable ordering per column
                    });

                    $('#dashboardDetailTable').find('#dashboardDetailHeader').attr("colspan", response.columns.length); 

                    // Initialize DataTable
                    var dashboardDetailTable = $("#dashboardDetailTable").DataTable({
                        "destroy": true, // Allow reinitialization
                        "responsive": true,
                        "autoWidth": false,
                        'processing': true,
                        'paging': false,
                        'info': false,
                        'searching': false,
                        "ordering": false,
                        'data': response.aaData, // Load dynamic data
                        'columns': dynamicColumns, // Use dynamic columns
                        'createdRow': function(row, data, dataIndex) {
                            $('td:eq(0)', row).css({
                                'font-weight': 'bold',
                                'color': 'black',
                                'background-color': 'rgba(13, 110, 253, 0.6)'
                            });

                            if (dataIndex === 0) { 
                                $(row).css({
                                    'font-weight': 'bold',
                                    'color': 'black',
                                'background-color': 'rgba(13, 110, 253, 0.6)'
                                });

                                // Apply styling to the last column of each row
                                $('td:last', row).css({
                                    'font-weight': 'bold',
                                    'color': 'black',
                                    'background-color': '#FFECB3' // Example: Yellow background for last column
                                });
                            }
                        }
                    });
                }
            });
        }else{
            $('#displayDashboardDetail').hide();
            alert("Please filter from and to date.");
        }
        }
    </script>
</body>
</html>