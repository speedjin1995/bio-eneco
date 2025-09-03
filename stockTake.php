<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");

?>

<head>

    <title>Bitumen | Synctronix - Weighing System</title>
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
                                    <div class="card-header fs-5" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch">
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
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select" >
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant)){ ?>
                                                                    <option value="<?=$rowPlantF['id'] ?>"><?=$rowPlantF['name'] ?></option>
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
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Inventory</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="addWeight" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                Add Bitumen Used
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Plant</th>
                                                                    <th>Declaration <br> Date</th>
                                                                    <th>Total (60/70) <br> Weight</th>
                                                                    <th>Total (60/70) <br> Temperature</th>
                                                                    <th>Total (60/70) <br> Level</th>
                                                                    <th>Total <br> LFO</th>
                                                                    <th>Total <br> Diesel</th>
                                                                    <th>Total <br> Hotoil</th>
                                                                    <th>Total <br> PG79</th>
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
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->
        <!-- /.modal-dialog -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Add Bitumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="siteForm" class="needs-validation" novalidate autocomplete="off">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="plant" class="col-sm-4 col-form-label">Plant</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select select2" id="plant" name="plant" required>
                                                                <?php while($rowPlant=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlant['id'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name'] ?></option>
                                                                <?php } ?>
                                                            </select>  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="datetime" class="col-sm-4 col-form-label">Date/Time</label>
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="datetime" name="datetime" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="rawMatName" class="col-sm-4 col-form-label">PG76</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control" id="rawMatName" name="rawMatName" placeholder="PG76">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="weight" class="col-sm-4 col-form-label">CRMB</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control" id="weight" name="weight" placeholder="crmb">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="drum" class="col-sm-4 col-form-label">LFO</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control" id="drum" name="drum" placeholder="LFO">
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="drum" class="col-sm-4 col-form-label">Diesel</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control" id="diesel" name="diesel" placeholder="Diesel">
                                                        </div>
                                                    </div>
                                                </div>                                                     -->
                                                <input type="hidden" class="form-control" id="bitumenId" name="bitumenId"> 
                                                <input type="hidden" class="form-control" id="plantCode" name="plantCode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Bitumen</h5>
                                                    <button type="button" class="btn btn-primary add-bitumen" id="addBitumen">Add Bitumen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>60/70</th>
                                                            <th>Temperature (&deg;C)</th>
                                                            <th>Level (cm)</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bitumenTable"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalSixtySeventy" name="totalSixtySeventy" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th><input type="number" class="form-control" id="totalTemp" name="totalTemp" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th><input type="number" class="form-control" id="totalLevel" name="totalLevel" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">LFO</h5>
                                                    <button type="button" class="btn btn-primary add-lfo" id="addLFO">Add LFO</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>LFO</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lfoTable"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalLfo" name="totalLfo" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Diesel</h5>
                                                    <button type="button" class="btn btn-primary add-diesel" id="addDiesel">Add Diesel</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Diesel</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="dieselTable"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalDiesel" name="totalDiesel" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Hotoil</h5>
                                                    <button type="button" class="btn btn-primary add-hotoil" id="addHotoil">Add Hotoil</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Hotoil</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="hotoilTable"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalHotoil" name="totalHotoil" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Bitumen PG 76</h5>
                                                    <button type="button" class="btn btn-primary add-pg-79" id="addpg79">Add PG 76</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Bitumen PG 76</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="pg79Table"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalPgSevenNine" name="totalPgSevenNine" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Fibre</h5>
                                                    <button type="button" class="btn btn-primary add-fibre" id="addFibre">Add Fibre</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Type (kg/bag)</th>
                                                            <th>No. of Bags</th>
                                                            <th>Quantity (mt)</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameMr6" name="fibreNameMr6" value="MR6" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeMr6" name="fibreTypeMr6" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsMr6" name="fibreBagsMr6" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyMr6" name="fibreQtyMr6" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameRpf" name="fibreNameRpf" value="RPF" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeRpf" name="fibreTypeRpf" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsRpf" name="fibreBagsRpf" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyRpf" name="fibreQtyRpf" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameNova" name="fibreNameNova" value="Nova Fiber" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeNova" name="fibreTypeNova" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsNova" name="fibreBagsNova" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyNova" name="fibreQtyNova" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameForta" name="fibreNameForta" value="Forta Fiber" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeForta" name="fibreTypeForta" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsForta" name="fibreBagsForta" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyForta" name="fibreQtyForta" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tbody id="fibreTable"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <tbody>
                                                        <tr>
                                                            <th>Aggregrates</th>
                                                            <th>40mm</th>
                                                            <th>28mm</th>
                                                            <th>20mm</th>
                                                            <th>14mm</th>
                                                            <th>10mm</th>
                                                            <th>QD</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Quantity (mt)</td>
                                                            <td><input type="number" class="form-control" id="40mm" name="40mm"></td>
                                                            <td><input type="number" class="form-control" id="28mm" name="28mm"></td>
                                                            <td><input type="number" class="form-control" id="20mm" name="20mm"></td>
                                                            <td><input type="number" class="form-control" id="14mm" name="14mm"></td>
                                                            <td><input type="number" class="form-control" id="10mm" name="10mm"></td>
                                                            <td><input type="number" class="form-control" id="QD" name="QD"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Emulsion</th>
                                                            <th>RS1-K</th>
                                                            <th>K1-40</th>
                                                            <th>SS1K</th>
                                                            <th>Others</th>
                                                            <th>Transport</th>
                                                            <th>Burner</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Quantity (dr)</td>
                                                            <td><input type="number" class="form-control" id="rs1k" name="rs1k"></td>
                                                            <td><input type="number" class="form-control" id="k140" name="k140"></td>
                                                            <td><input type="number" class="form-control" id="ss1k" name="ss1k"></td>
                                                            <td><input type="number" class="form-control" id="others" name="others"></td>
                                                            <td><input type="number" class="form-control" id="transport" name="transport"></td>
                                                            <td><input type="number" class="form-control" id="burner" name="burner"></td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="3">OPC</th>
                                                            <th colspan="3">Lime/Filler</th>
                                                            <th></th>
                                                        </tr>
                                                        <tr>
                                                            <td>D/O No</td>
                                                            <td>Incoming (mt)</td>
                                                            <td>Quantity (mt)</td>
                                                            <td>D/O No</td>
                                                            <td>Incoming (mt)</td>
                                                            <td>Quantity (mt)</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="number" class="form-control" id="opcDo" name="opcDo"></td>
                                                            <td><input type="number" class="form-control" id="opcIncoming" name="opcIncoming"></td>
                                                            <td><input type="number" class="form-control" id="opcQty" name="opcQty"></td>
                                                            <td><input type="number" class="form-control" id="limeDo" name="limeDo"></td>
                                                            <td><input type="number" class="form-control" id="limeIncoming" name="limeIncoming"></td>
                                                            <td><input type="number" class="form-control" id="limeQty" name="limeQty"></td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="submitSite">Submit</button>
                                </div>
                            </div><!--end col-->                                                               
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <!-- END layout-wrapper -->

    <script type="text/html" id="bitumenDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="no" name="no" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="sixtyseventy" name="sixtyseventy" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="temp" name="temp" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="level" name="level" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="lfoDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="lfoNo" name="lfoNo" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="lfoWeight" name="lfoWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="dieselDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="dieselNo" name="dieselNo" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="dieselWeight" name="dieselWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="hotoilDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="hotoilNo" name="hotoilNo" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="hotoilWeight" name="hotoilWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="pg79Detail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="pg79No" name="pg79No" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="pgSevenNine" name="pgSevenNine" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="fibreDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="fibreNo" name="fibreNo" hidden>
                <input type="text" class="form-control" id="fibreName" name="fibreName" style="background-color:white;" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreType" name="fibreType" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreNoOfBags" name="fibreNoOfBags" style="background-color:white;" value="0" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreQty" name="fibreQty" style="background-color:white;" value="0.00" readonly>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

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

    var bitumenCount = $("#bitumenTable").find(".details").length;
    var lfoCount = $("#lfoTable").find(".details").length;
    var dieselCount = $("#dieselTable").find(".details").length;
    var hotoilCount = $("#hotoilTable").find(".details").length;
    var pg79Count = $("#pg79Table").find(".details").length;
    var fibreCount = $("#fibreTable").find(".details").length;

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

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

        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });
        
        $('#datetime').flatpickr({
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            defaultDate: today
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

        var table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'order': [[ 1, 'asc' ]],
            'columnDefs': [ 
                { orderable: false, targets: [0] },
                { orderable: false, targets: [3] },
                { orderable: false, targets: [4] },
                { orderable: false, targets: [5] },
                { orderable: false, targets: [6] },
                { orderable: false, targets: [7] },
                { orderable: false, targets: [8] },
                { orderable: false, targets: [9] }
            ],
            'ajax': {
                'url':'php/filterBitumen.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    plant: plantNoI
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'plant' },
                { data: 'declaration_datetime' },
                { data: 'totalSixtySeventy' },
                { data: 'totalTemperature' },
                { data: 'totalLevel' },
                { data: 'totalLfo' },
                { data: 'totalDiesel' },
                { data: 'totalHotoil' },
                { data: 'totalPgSevenNine' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pen align-bottom me-2 text-muted"></i> Edit</a></li></ul></div>';
                    }
                }
            ] 
        });

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

            $("#weightTable").DataTable().clear().destroy();

            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                'order': [[ 1, 'asc' ]],
                'columnDefs': [ 
                    { orderable: false, targets: [0] },
                    { orderable: false, targets: [3] },
                    { orderable: false, targets: [4] },
                    { orderable: false, targets: [5] },
                    { orderable: false, targets: [6] },
                    { orderable: false, targets: [7] },
                    { orderable: false, targets: [8] },
                    { orderable: false, targets: [9] }
                ],
                'ajax': {
                    'url':'php/filterBitumen.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        plant: plantNoI
                    } 
                },
                'columns': [
                    { data: 'no' },
                    { data: 'plant' },
                    { data: 'declaration_datetime' },
                    { data: 'totalSixtySeventy' },
                    { data: 'totalTemperature' },
                    { data: 'totalLevel' },
                    { data: 'totalLfo' },
                    { data: 'totalDiesel' },
                    { data: 'totalHotoil' },
                    { data: 'totalPgSevenNine' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pen align-bottom me-2 text-muted"></i> Edit</a></li></ul></div>';
                        }
                    }
                ] 
            });
        });

        $('#addWeight').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#plant').val("").trigger('change');
            $('#addModal').find('#datetime').val(formatDate4(today));
            $('#bitumenTable').html('');
            $('#addModal').find('#totalSixtySeventy').val(0);
            $('#addModal').find('#totalTemp').val(0);
            $('#addModal').find('#totalLevel').val(0);
            $('#lfoTable').html('');
            $('#addModal').find('#totalLfo').val(0);
            $('#dieselTable').html('');
            $('#addModal').find('#totalDiesel').val(0);
            $('#hotoilTable').html('');
            $('#addModal').find('#totalHotoil').val(0);
            $('#pg79Table').html('');
            $('#addModal').find('#totalPgSevenNine').val(0);
            $('#addModal').find('#fibreTypeMr6').val(0.00);
            $('#addModal').find('#fibreBagsMr6').val(0);
            $('#addModal').find('#fibreQtyMr6').val(0.00);
            $('#addModal').find('#fibreTypeRpf').val(0.00);
            $('#addModal').find('#fibreBagsRpf').val(0);
            $('#addModal').find('#fibreQtyRpf').val(0.00);
            $('#addModal').find('#fibreTypeNova').val(0.00);
            $('#addModal').find('#fibreBagsNova').val(0);
            $('#addModal').find('#fibreQtyNova').val(0.00);
            $('#addModal').find('#fibreTypeForta').val(0.00);
            $('#addModal').find('#fibreBagsForta').val(0);
            $('#addModal').find('#fibreQtyForta').val(0.00);
            $('#fibreTable').html('');
            $('#addModal').find('#40mm').val("");
            $('#addModal').find('#28mm').val("");
            $('#addModal').find('#20mm').val("");
            $('#addModal').find('#14mm').val("");
            $('#addModal').find('#10mm').val("");
            $('#addModal').find('#QD').val("");
            $('#addModal').find('#rs1k').val("");
            $('#addModal').find('#k140').val("");
            $('#addModal').find('#ss1k').val("");
            $('#addModal').find('#others').val("");
            $('#addModal').find('#transport').val("");
            $('#addModal').find('#burner').val("");
            $('#addModal').find('#limeDo').val("");
            $('#addModal').find('#limeIncoming').val("");
            $('#addModal').find('#limeQty').val("");
            $('#addModal').find('#opcDo').val("");
            $('#addModal').find('#opcIncoming').val("");
            $('#addModal').find('#opcQty').val("");

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            $('#addModal').modal('show');
            
            $('#siteForm').validate({
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

        $('#submitSite').on('click', function(){
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

            if($('#siteForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/bitumen.php', $('#siteForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{

                    }
                });
            }
        });

        $('#exportPdf').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';

            $.post('php/exportPdf.php', {
                file: 'weight',
                fromDate: fromDateI,
                toDate: toDateI,
                status: statusI,
                customer: customerNoI,
                vehicle: vehicleNoI,
                weighingType: invoiceNoI,
                product: transactionStatusI
            }, function(response){
                var obj = JSON.parse(response);

                if(obj.status === 'success'){
                    var printWindow = window.open('', '', 'height=400,width=800');
                    printWindow.document.write(obj.message);
                    printWindow.document.close();
                    setTimeout(function(){
                        printWindow.print();
                        printWindow.close();
                    }, 500);
                }
                else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                }
                else{
                    toastr["error"]("Something wrong when activate", "Failed:");
                }
            }).fail(function(error){
                console.error("Error exporting PDF:", error);
                alert("An error occurred while generating the PDF.");
            });
        });

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
            
            window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
            "&status="+statusI+"&customer="+customerNoI+"&vehicle="+vehicleNoI+
            "&weighingType="+invoiceNoI+"&product="+transactionStatusI);
        });

        $('#plant').on('change', function(){
            $('#plantCode').val($('#plant :selected').data('code'));
        });

        // Find and remove selected table rows for bitumenTable
        $("#bitumenTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#bitumenTable tr").each(function (index) {
                $(this).find('input[name^="no"]').val(index + 1);
            });

            bitumenCount--;
        });

        // Event delegation for order weight to calculate total sixtyseventy
        $("#bitumenTable").on('change', 'input[id^="sixtyseventy"]', function(){
            var totalSum = 0;

            // Loop through each sixtyseventy input and sum up the values
            $('input[id^="sixtyseventy"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the totalsixtyseventy input field
            $('#totalSixtySeventy').val(totalSum.toFixed(2));
        });

        // Event delegation for order weight to calculate total temp
        $("#bitumenTable").on('change', 'input[id^="temp"]', function(){
            var totalSum = 0;

            // Loop through each temp input and sum up the values
            $('input[id^="temp"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the totalTemp input field
            $('#totalTemp').val(totalSum.toFixed(2));
        });

        // Event delegation for order weight to calculate total level
        $("#bitumenTable").on('change', 'input[id^="level"]', function(){
            var totalSum = 0;

            // Loop through each level input and sum up the values
            $('input[id^="level"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the totalLevel input field
            $('#totalLevel').val(totalSum.toFixed(2));
        });

        $(".add-bitumen").click(function(){
            var $addContents = $("#bitumenDetail").clone();
            $("#bitumenTable").append($addContents.html());

            $("#bitumenTable").find('.details:last').attr("id", "detail" + bitumenCount);
            $("#bitumenTable").find('.details:last').attr("data-index", bitumenCount);
            $("#bitumenTable").find('#remove:last').attr("id", "remove" + bitumenCount);

            $("#bitumenTable").find('#no:last').attr('name', 'no['+bitumenCount+']').attr("id", "no" + bitumenCount).css("text-align", "center").val(bitumenCount + 1);
            $("#bitumenTable").find('#sixtyseventy:last').attr('name', 'sixtyseventy['+bitumenCount+']').attr("id", "sixtyseventy" + bitumenCount).css("text-align", "center");
            $("#bitumenTable").find('#temp:last').attr('name', 'temp['+bitumenCount+']').attr("id", "temp" + bitumenCount).css("text-align", "center");
            $("#bitumenTable").find('#level:last').attr('name', 'level['+bitumenCount+']').attr("id", "level" + bitumenCount).css("text-align", "center");

            bitumenCount++;
        });

        // Find and remove selected table rows for lfoTable
        $("#lfoTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#lfoTable tr").each(function (index) {
                $(this).find('input[name^="lfoNo"]').val(index + 1);
            });

            $('input[id^="lfoWeight"]').trigger('change');

            lfoCount--;
        });

        // Event delegation for order weight to calculate lfo total
        $("#lfoTable").on('change', 'input[id^="lfoWeight"]', function(){
            var totalSum = 0;

            // Loop through each lfo input and sum up the values
            $('input[id^="lfoWeight"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the lfo input field
            $('#totalLfo').val(totalSum.toFixed(2));
        });

        $(".add-lfo").click(function(){
            var $addContents = $("#lfoDetail").clone();
            $("#lfoTable").append($addContents.html());

            $("#lfoTable").find('.details:last').attr("id", "detail" + lfoCount);
            $("#lfoTable").find('.details:last').attr("data-index", lfoCount);
            $("#lfoTable").find('#remove:last').attr("id", "remove" + lfoCount);

            $("#lfoTable").find('#lfoNo:last').attr('name', 'lfoNo['+lfoCount+']').attr("id", "lfoNo" + lfoCount).css("text-align", "center").val(lfoCount + 1);
            $("#lfoTable").find('#lfoWeight:last').attr('name', 'lfoWeight['+lfoCount+']').attr("id", "lfoWeight" + lfoCount).css("text-align", "center");

            lfoCount++;
        });

        // Find and remove selected table rows for lfoTable
        $("#dieselTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#dieselTable tr").each(function (index) {
                $(this).find('input[name^="dieselNo"]').val(index + 1);
            });

            $('input[id^="dieselWeight"]').trigger('change');

            dieselCount--;
        });

        // Event delegation for order weight to calculate diesel total
        $("#dieselTable").on('change', 'input[id^="dieselWeight"]', function(){
            var totalSum = 0;

            // Loop through each diesel input and sum up the values
            $('input[id^="dieselWeight"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the diesel input field
            $('#totalDiesel').val(totalSum.toFixed(2));
        });

        $(".add-diesel").click(function(){
            var $addContents = $("#dieselDetail").clone();
            $("#dieselTable").append($addContents.html());

            $("#dieselTable").find('.details:last').attr("id", "detail" + dieselCount);
            $("#dieselTable").find('.details:last').attr("data-index", dieselCount);
            $("#dieselTable").find('#remove:last').attr("id", "remove" + dieselCount);

            $("#dieselTable").find('#dieselNo:last').attr('name', 'dieselNo['+dieselCount+']').attr("id", "dieselNo" + dieselCount).css("text-align", "center").val(dieselCount + 1);
            $("#dieselTable").find('#dieselWeight:last').attr('name', 'dieselWeight['+dieselCount+']').attr("id", "dieselWeight" + dieselCount).css("text-align", "center");

            dieselCount++;
        });

        // Find and remove selected table rows for hotoilTable
        $("#hotoilTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#hotoilTable tr").each(function (index) {
                $(this).find('input[name^="hotoilNo"]').val(index + 1);
            });

            $('input[id^="hotoilWeight"]').trigger('change');

            hotoilCount--;
        });

        // Event delegation for order weight to calculate hotoil total
        $("#hotoilTable").on('change', 'input[id^="hotoilWeight"]', function(){
            var totalSum = 0;

            // Loop through each hotoil input and sum up the values
            $('input[id^="hotoilWeight"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the hotoil input field
            $('#totalHotoil').val(totalSum.toFixed(2));
        });

        $(".add-hotoil").click(function(){
            var $addContents = $("#hotoilDetail").clone();
            $("#hotoilTable").append($addContents.html());

            $("#hotoilTable").find('.details:last').attr("id", "detail" + hotoilCount);
            $("#hotoilTable").find('.details:last').attr("data-index", hotoilCount);
            $("#hotoilTable").find('#remove:last').attr("id", "remove" + hotoilCount);

            $("#hotoilTable").find('#hotoilNo:last').attr('name', 'hotoilNo['+hotoilCount+']').attr("id", "hotoilNo" + hotoilCount).css("text-align", "center").val(hotoilCount + 1);
            $("#hotoilTable").find('#hotoilWeight:last').attr('name', 'hotoilWeight['+hotoilCount+']').attr("id", "hotoilWeight" + hotoilCount).css("text-align", "center");

            hotoilCount++;
        });

        // Find and remove selected table rows for lfoTable
        $("#pg79Table").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#pg79Table tr").each(function (index) {
                $(this).find('input[name^="pg79No"]').val(index + 1);
            });

            $('input[id^="pgSevenNine"]').trigger('change');

            pg79Count--;
        });

        // Event delegation for order weight to calculate pgSevenNine total
        $("#pg79Table").on('change', 'input[id^="pgSevenNine"]', function(){
            var totalSum = 0;

            // Loop through each pgSevenNine input and sum up the values
            $('input[id^="pgSevenNine"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the pgSevenNine input field
            $('#totalPgSevenNine').val(totalSum.toFixed(2));
        });

        $(".add-pg-79").click(function(){
            var $addContents = $("#pg79Detail").clone();
            $("#pg79Table").append($addContents.html());

            $("#pg79Table").find('.details:last').attr("id", "detail" + pg79Count);
            $("#pg79Table").find('.details:last').attr("data-index", pg79Count);
            $("#pg79Table").find('#remove:last').attr("id", "remove" + pg79Count);

            $("#pg79Table").find('#pg79No:last').attr('name', 'pg79No['+pg79Count+']').attr("id", "pg79No" + pg79Count).css("text-align", "center").val(pg79Count + 1);
            $("#pg79Table").find('#pgSevenNine:last').attr('name', 'pgSevenNine['+pg79Count+']').attr("id", "pgSevenNine" + pg79Count).css("text-align", "center");

            pg79Count++;
        });

        // Find and remove selected table rows for fibreTable
        $("#fibreTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#fibreTable tr").each(function (index) {
                $(this).find('input[name^="fibreNo"]').val(index + 1);
            });

            fibreCount--;
        });

        // Event delegation for order weight to calculate fibreQty for each fibre type
        $("#fibreTable").on('change', 'input[id^="fibreType"]', function(){
            $(this).closest('.details').find('input[id^="fibreQty"]').trigger('change');
        });

        $("#fibreTable").on('change', 'input[id^="fibreNoOfBags"]', function(){
            $(this).closest('.details').find('input[id^="fibreQty"]').trigger('change');
        });

        $("#fibreTable").on('change', 'input[id^="fibreQty"]', function(){
            var type = parseFloat($(this).closest('.details').find('input[id^="fibreType"]').val()) || 0;
            var bags = parseFloat($(this).closest('.details').find('input[id^="fibreNoOfBags"]').val()) || 0;

            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $(".add-fibre").click(function(){
            var $addContents = $("#fibreDetail").clone();
            $("#fibreTable").append($addContents.html());

            $("#fibreTable").find('.details:last').attr("id", "detail" + fibreCount);
            $("#fibreTable").find('.details:last').attr("data-index", fibreCount);
            $("#fibreTable").find('#remove:last').attr("id", "remove" + fibreCount);

            $("#fibreTable").find('#fibreNo:last').attr('name', 'fibreNo['+fibreCount+']').attr("id", "fibreNo" + fibreCount).css("text-align", "center").val(fibreCount + 1);
            $("#fibreTable").find('#fibreName:last').attr('name', 'fibreName['+fibreCount+']').attr("id", "fibreName" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreType:last').attr('name', 'fibreType['+fibreCount+']').attr("id", "fibreType" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreNoOfBags:last').attr('name', 'fibreNoOfBags['+fibreCount+']').attr("id", "fibreNoOfBags" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreQty:last').attr('name', 'fibreQty['+fibreCount+']').attr("id", "fibreQty" + fibreCount).css("text-align", "center");

            fibreCount++;
        });

        $('#fibreTypeMr6').on('keyup', function(){
            $('#fibreQtyMr6').trigger('change');
        });

        $('#fibreBagsMr6').on('keyup', function(){
            $('#fibreQtyMr6').trigger('change');
        });

        $('#fibreQtyMr6').on('change', function(){
            // Handle the change event for fibreQtyMr6
            var type = parseFloat($('#fibreTypeMr6').val());
            var bags = parseFloat($('#fibreBagsMr6').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeRpf').on('keyup', function(){
            $('#fibreQtyRpf').trigger('change');
        });

        $('#fibreBagsRpf').on('keyup', function(){
            $('#fibreQtyRpf').trigger('change');
        });

        $('#fibreQtyRpf').on('change', function(){
            // Handle the change event for fibreQtyRpf
            var type = parseFloat($('#fibreTypeRpf').val());
            var bags = parseFloat($('#fibreBagsRpf').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeNova').on('keyup', function(){
            $('#fibreQtyNova').trigger('change');
        });

        $('#fibreBagsNova').on('keyup', function(){
            $('#fibreQtyNova').trigger('change');
        });

        $('#fibreQtyNova').on('change', function(){
            // Handle the change event for fibreQtyNova
            var type = parseFloat($('#fibreTypeNova').val());
            var bags = parseFloat($('#fibreBagsNova').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeForta').on('keyup', function(){
            $('#fibreQtyForta').trigger('change');
        });

        $('#fibreBagsForta').on('keyup', function(){
            $('#fibreQtyForta').trigger('change');
        });

        $('#fibreQtyForta').on('change', function(){
            // Handle the change event for fibreQtyForta
            var type = parseFloat($('#fibreTypeForta').val());
            var bags = parseFloat($('#fibreBagsForta').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getBitumen.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#bitumenId').val(obj.message.id);
                $('#addModal').find('#plant').val(obj.message.plant_id).trigger('change ');
                $('#addModal').find('#plantCode').val(obj.message.plant_code);
                $('#addModal').find('#datetime').val(formatDate4(new Date(obj.message.declaration_datetime)));

                // Bitumen Table Processing
                $('#bitumenTable').html('');
                bitumenCount = 0;
                if (obj.message.sixtysevn.length > 0){
                    for(var i = 0; i < obj.message.sixtysevn.length; i++){
                        var item = obj.message.sixtysevn[i]; 
                        var $addContents = $("#bitumenDetail").clone();
                        $("#bitumenTable").append($addContents.html());

                        $("#bitumenTable").find('.details:last').attr("id", "detail" + bitumenCount);
                        $("#bitumenTable").find('.details:last').attr("data-index", bitumenCount);
                        $("#bitumenTable").find('#remove:last').attr("id", "remove" + bitumenCount);

                        $("#bitumenTable").find('#no:last').attr('name', 'no['+bitumenCount+']').attr("id", "no" + bitumenCount).css("text-align", "center").val(bitumenCount + 1);
                        $("#bitumenTable").find('#sixtyseventy:last').attr('name', 'sixtyseventy['+bitumenCount+']').attr("id", "sixtyseventy" + bitumenCount).css("text-align", "center").val(item.sixtyseventy);
                        $("#bitumenTable").find('#temp:last').attr('name', 'temp['+bitumenCount+']').attr("id", "temp" + bitumenCount).css("text-align", "center").val(item.temperature);
                        $("#bitumenTable").find('#level:last').attr('name', 'level['+bitumenCount+']').attr("id", "level" + bitumenCount).css("text-align", "center").val(item.level);

                        bitumenCount++;
                    }
                }
                $('#addModal').find('#totalSixtySeventy').val(obj.message.totalSixtySeventy);
                $('#addModal').find('#totalTemp').val(obj.message.totalTemp);
                $('#addModal').find('#totalLevel').val(obj.message.totalLevel);

                // LFO Table Processing
                $('#lfoTable').html('');
                lfoCount = 0;
                if (obj.message.lfo.length > 0){
                    for(var i = 0; i < obj.message.lfo.length; i++){
                        var item = obj.message.lfo[i]; 
                        var $addContents = $("#lfoDetail").clone();
                        $("#lfoTable").append($addContents.html());

                        $("#lfoTable").find('.details:last').attr("id", "detail" + lfoCount);
                        $("#lfoTable").find('.details:last').attr("data-index", lfoCount);
                        $("#lfoTable").find('#remove:last').attr("id", "remove" + lfoCount);

                        $("#lfoTable").find('#lfoNo:last').attr('name', 'lfoNo['+lfoCount+']').attr("id", "lfoNo" + lfoCount).css("text-align", "center").val(lfoCount + 1);
                        $("#lfoTable").find('#lfoWeight:last').attr('name', 'lfoWeight['+lfoCount+']').attr("id", "lfoWeight" + lfoCount).css("text-align", "center").val(item.lfoWeight);

                        lfoCount++;
                    }
                }
                $('#addModal').find('#totalLfo').val(obj.message.totalLfo);

                // Diesel Table Processing
                $('#dieselTable').html('');
                dieselCount = 0;
                if (obj.message.diesel.length > 0){
                    for(var i = 0; i < obj.message.diesel.length; i++){
                        var item = obj.message.diesel[i]; 
                        var $addContents = $("#dieselDetail").clone();
                        $("#dieselTable").append($addContents.html());

                        $("#dieselTable").find('.details:last').attr("id", "detail" + dieselCount);
                        $("#dieselTable").find('.details:last').attr("data-index", dieselCount);
                        $("#dieselTable").find('#remove:last').attr("id", "remove" + dieselCount);

                        $("#dieselTable").find('#dieselNo:last').attr('name', 'dieselNo['+dieselCount+']').attr("id", "dieselNo" + dieselCount).css("text-align", "center").val(dieselCount + 1);
                        $("#dieselTable").find('#dieselWeight:last').attr('name', 'dieselWeight['+dieselCount+']').attr("id", "dieselWeight" + dieselCount).css("text-align", "center").val(item.dieselWeight);

                        dieselCount++;
                    }
                }
                $('#addModal').find('#totalDiesel').val(obj.message.totalDiesel);

                // Hotoil Table Processing
                $('#hotoilTable').html('');
                hotoilCount = 0;
                if (obj.message.hotoil.length > 0){
                    for(var i = 0; i < obj.message.hotoil.length; i++){
                        var item = obj.message.hotoil[i]; 
                        var $addContents = $("#hotoilDetail").clone();
                        $("#hotoilTable").append($addContents.html());

                        $("#hotoilTable").find('.details:last').attr("id", "detail" + hotoilCount);
                        $("#hotoilTable").find('.details:last').attr("data-index", hotoilCount);
                        $("#hotoilTable").find('#remove:last').attr("id", "remove" + hotoilCount);

                        $("#hotoilTable").find('#hotoilNo:last').attr('name', 'hotoilNo['+hotoilCount+']').attr("id", "hotoilNo" + hotoilCount).css("text-align", "center").val(hotoilCount + 1);
                        $("#hotoilTable").find('#hotoilWeight:last').attr('name', 'hotoilWeight['+hotoilCount+']').attr("id", "hotoilWeight" + hotoilCount).css("text-align", "center").val(item.hotoilWeight);

                        hotoilCount++;
                    }
                }
                $('#addModal').find('#totalHotoil').val(obj.message.totalHotoil);

                // PG79 Table Processing
                $('#pg79Table').html('');
                pg79Count = 0;
                if (obj.message.pgSeventyNine.length > 0){ 
                    for(var i = 0; i < obj.message.pgSeventyNine.length; i++){
                        var item = obj.message.pgSeventyNine[i];
                        var $addContents = $("#pg79Detail").clone();
                        $("#pg79Table").append($addContents.html());

                        $("#pg79Table").find('.details:last').attr("id", "detail" + pg79Count);
                        $("#pg79Table").find('.details:last').attr("data-index", pg79Count);
                        $("#pg79Table").find('#remove:last').attr("id", "remove" + pg79Count);

                        $("#pg79Table").find('#pg79No:last').attr('name', 'pg79No['+pg79Count+']').attr("id", "pg79No" + pg79Count).css("text-align", "center").val(pg79Count + 1);
                        $("#pg79Table").find('#pgSevenNine:last').attr('name', 'pgSevenNine['+pg79Count+']').attr("id", "pgSevenNine" + pg79Count).css("text-align", "center").val(item.pgSevenNine);

                        pg79Count++;
                    }
                }
                $('#addModal').find('#totalPgSevenNine').val(obj.message.totalPgSevenNine);

                // Fibre Table Processing
                $('#addModal').find('#fibreNameMr6').val(obj.message.fibreNameMr6);
                $('#addModal').find('#fibreTypeMr6').val(obj.message.fibreTypeMr6);
                $('#addModal').find('#fibreBagsMr6').val(obj.message.fibreBagsMr6);
                $('#addModal').find('#fibreQtyMr6').val(obj.message.fibreQtyMr6);
                $('#addModal').find('#fibreNameRpf').val(obj.message.fibreNameRpf);
                $('#addModal').find('#fibreTypeRpf').val(obj.message.fibreTypeRpf);
                $('#addModal').find('#fibreBagsRpf').val(obj.message.fibreBagsRpf);
                $('#addModal').find('#fibreQtyRpf').val(obj.message.fibreQtyRpf);
                $('#addModal').find('#fibreNameNova').val(obj.message.fibreNameNova);
                $('#addModal').find('#fibreTypeNova').val(obj.message.fibreTypeNova);
                $('#addModal').find('#fibreBagsNova').val(obj.message.fibreBagsNova);
                $('#addModal').find('#fibreQtyNova').val(obj.message.fibreQtyNova);
                $('#addModal').find('#fibreNameForta').val(obj.message.fibreNameForta);
                $('#addModal').find('#fibreTypeForta').val(obj.message.fibreTypeForta);
                $('#addModal').find('#fibreBagsForta').val(obj.message.fibreBagsForta);
                $('#addModal').find('#fibreQtyForta').val(obj.message.fibreQtyForta);

                $('#fibreTable').html('');
                fibreCount = 0;
                if (obj.message.fibre.length > 0){ 
                    for(var i = 0; i < obj.message.fibre.length; i++){
                        var item = obj.message.fibre[i];
                        var $addContents = $("#fibreDetail").clone();
                        $("#fibreTable").append($addContents.html());

                        $("#fibreTable").find('.details:last').attr("id", "detail" + fibreCount);
                        $("#fibreTable").find('.details:last').attr("data-index", fibreCount);
                        $("#fibreTable").find('#remove:last').attr("id", "remove" + fibreCount);

                        $("#fibreTable").find('#fibreNo:last').attr('name', 'fibreNo['+fibreCount+']').attr("id", "fibreNo" + fibreCount).css("text-align", "center").val(fibreCount + 1);
                        $("#fibreTable").find('#fibreName:last').attr('name', 'fibreName['+fibreCount+']').attr("id", "fibreName" + fibreCount).css("text-align", "center").val(item.fibreName);
                        $("#fibreTable").find('#fibreType:last').attr('name', 'fibreType['+fibreCount+']').attr("id", "fibreType" + fibreCount).css("text-align", "center").val(item.fibreType);
                        $("#fibreTable").find('#fibreNoOfBags:last').attr('name', 'fibreNoOfBags['+fibreCount+']').attr("id", "fibreNoOfBags" + fibreCount).css("text-align", "center").val(item.fibreBags);
                        $("#fibreTable").find('#fibreQty:last').attr('name', 'fibreQty['+fibreCount+']').attr("id", "fibreQty" + fibreCount).css("text-align", "center").val(item.fibreQty);

                        fibreCount++;
                    }
                }

                // Remaining Fields Processing
                $('#addModal').find('#40mm').val(obj.message.fortymm);
                $('#addModal').find('#28mm').val(obj.message.twentyeightmm);
                $('#addModal').find('#20mm').val(obj.message.twentyMM);
                $('#addModal').find('#14mm').val(obj.message.fourteenMM);
                $('#addModal').find('#10mm').val(obj.message.tenMM);
                $('#addModal').find('#QD').val(obj.message.QD);
                $('#addModal').find('#rs1k').val(obj.message.rs1k);
                $('#addModal').find('#k140').val(obj.message.k140);
                $('#addModal').find('#ss1k').val(obj.message.ss1k);
                $('#addModal').find('#others').val(obj.message.others);
                $('#addModal').find('#transport').val(obj.message.transport);
                $('#addModal').find('#burner').val(obj.message.burner);
                $('#addModal').find('#opcDo').val(obj.message.opcDo);
                $('#addModal').find('#opcIncoming').val(obj.message.opcIncoming);
                $('#addModal').find('#opcQty').val(obj.message.opcQty);
                $('#addModal').find('#limeDo').val(obj.message.limeDo);
                $('#addModal').find('#limeIncoming').val(obj.message.limeIncoming);
                $('#addModal').find('#limeQty').val(obj.message.limeQty);

                $('#addModal').modal('show');
            
                $('#siteForm').validate({
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
        $.post('php/deleteWeight.php', {userID: id}, function(data){
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

    function print(id) {
        $.post('php/print.php', {userID: id, file: 'weight'}, function(data){
            var obj = JSON.parse(data);

            if(obj.status === 'success'){
                var printWindow = window.open('', '', 'height=400,width=800');
                printWindow.document.write(obj.message);
                printWindow.document.close();
                setTimeout(function(){
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
            }
        });
    }
    </script>
</body>
</html>