<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.php" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.jpg" alt="assets/images/logo-sm-bk.png" height="70">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-lg.png" alt="assets/images/logo-lg-bk.png" height="60">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.php" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.jpg" alt="assets/images/logo-sm-bk.png" height="70">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-lg.png" alt="assets/images/logo-lg-bk.png" height="60">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span><?=$lang['t-menu']?></span></li>
                <!--li class="nav-item">
                    <a href="dashboard.php" class="nav-link"><i class="mdi mdi-billboard"></i><?=$lang['t-billboard']?></a>
                </li-->
                <?php
                    if($_SESSION["roles"] == 'MANAGER' || $_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN'){
                        echo '<li class="nav-item">
                            <a href="weighingDashboard.php" class="nav-link"><i class="mdi mdi-view-dashboard"></i>'.$lang['t-dashboard'].'</a>
                        </li>';
                    }
                ?>
                
                <li class="nav-item">
                    <a href="index.php" class="nav-link"><i class="mdi mdi-weight"></i><?=$lang['t-weighing']?></a>
                </li>                
                
                <!-- <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="sidebarDashboards">
                        <i class="ri-dashboard-2-line"></i> <span><?=$lang['t-weightweighing']?></span>
                    </a>
                    <div class="collapse show menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="weighing.php" class="nav-link"><?=$lang['t-weighing']?></a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAccounting" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAccounting">
                        <i class="ri-pages-line"></i> <span><?=$lang['t-accounting']?></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAccounting">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="salesOrder.php" class="nav-link"><?=$lang['t-so']?></a>
                            </li>
                            <li class="nav-item">
                                <a href="deliveryOrder.php" class="nav-link"><?=$lang['t-do']?></a>
                            </li>   
                            <li class="nav-item">
                                <a href="purchaseOrder.php" class="nav-link"><?=$lang['t-po']?></a>
                            </li>  
                            <li class="nav-item">
                                <a href="goodsReceived.php" class="nav-link"><?=$lang['t-gr']?></a>
                            </li>             
                        </ul>
                    </div>
                </li-->

                <?php
                    if($_SESSION["roles"] == 'MANAGER' || $_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN'){
                        echo '
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarStock" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarStock">
                                <i class="ri-pages-line"></i> <span>'.$lang['t-stock'].'</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStock">
                                <ul class="nav nav-sm flex-column">
                                    <!--li class="nav-item">
                                        <a href="stockTake.php" class="nav-link"><i class="mdi mdi-domain"></i>'.$lang['t-bitumen'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="stockTakeLog.php" class="nav-link"><i class="mdi mdi-warehouse"></i>'.$lang['t-stockTakeLog'].'</a>
                                    </li-->
                                    <li class="nav-item">
                                        <a href="inventory.php" class="nav-link"><i class="mdi mdi-shipping-pallet"></i>'.$lang['t-inventory'].'</a>
                                    </li>              
                                </ul>
                            </div>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMasterdata" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarMasterdata">
                                <i class="ri-pages-line"></i> <span>'.$lang['t-masterdata'].'</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMasterdata">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="customer.php" class="nav-link">'.$lang['t-customer'].'</a>
                                    </li>
                                    <!--li class="nav-item">
                                        <a href="destination.php" class="nav-link">'.$lang['t-destination'].'</a>
                                    </li-->
                                    <li class="nav-item">
                                        <a href="product.php" class="nav-link">'.$lang['t-product'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="rawMaterial.php" class="nav-link">'.$lang['t-raw-mat'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="supplier.php" class="nav-link">'.$lang['t-supplier'].'</a>
                                    </li>       
                                    <li class="nav-item">
                                        <a href="vehicle.php" class="nav-link">'.$lang['t-vehicle'].'</a>
                                    </li>   
                                    <!--li class="nav-item">
                                        <a href="agent.php" class="nav-link">'.$lang['t-agent'].'</a>
                                    </li-->                   
                                    <li class="nav-item">
                                        <a href="transporter.php" class="nav-link">'.$lang['t-transporter'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="unit.php" class="nav-link">'.$lang['t-unit'].'</a>
                                    </li>                           
                                    <li class="nav-item">
                                        <a href="user.php" class="nav-link">'.$lang['t-user'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="plant.php" class="nav-link">'.$lang['t-plant'].'</a>
                                    </li>                                    
                                    <!--li class="nav-item">
                                        <a href="site.php" class="nav-link">'.$lang['t-site'].'</a>
                                    </li-->                   
                                </ul>
                            </div>
                        </li>';
                    }else{
                        echo '
                        <li class="nav-item" style="display:none">
                            <a class="nav-link menu-link" href="#sidebarStock" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarStock">
                                <i class="ri-pages-line"></i> <span>'.$lang['t-stock'].'</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStock">
                                <ul class="nav nav-sm flex-column">
                                    <!--li class="nav-item">
                                        <a href="stockTake.php" class="nav-link"><i class="mdi mdi-domain"></i>'.$lang['t-bitumen'].'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="stockTakeLog.php" class="nav-link"><i class="mdi mdi-warehouse"></i>'.$lang['t-stockTakeLog'].'</a>
                                    </li-->
                                    <li class="nav-item">
                                        <a href="inventory.php" class="nav-link"><i class="mdi mdi-shipping-pallet"></i>'.$lang['t-inventory'].'</a>
                                    </li>              
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMasterdata" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarMasterdata">
                                <i class="ri-pages-line"></i> <span>'.$lang['t-masterdata'].'</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMasterdata">
                                <ul class="nav nav-sm flex-column">
                                    <!--li class="nav-item">
                                        <a href="destination.php" class="nav-link">'.$lang['t-destination'].'</a>
                                    </li-->
                                    <li class="nav-item">
                                        <a href="vehicle.php" class="nav-link">'.$lang['t-vehicle'].'</a>
                                    </li>       
                                </ul>
                            </div>
                        </li>';
                    }
                ?>

                <li class="nav-item" <?php
                        if($_SESSION["roles"] == 'NORMAL'){
                            echo 'style="display:none;"';
                        }
                    ?>>
                    <a class="nav-link menu-link" href="#sidebarReport" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarReport">
                        <i class="ri-account-circle-line"></i> <span><?=$lang['t-report']?></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarReport">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <!-- <li class="nav-item">
                                    <a href="weighingReport.php" class="nav-link"><?=$lang['t-weighingReport']?></a>
                                </li> -->
                                <?php
                                    if($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN'){
                                        echo '
                                            <li class="nav-item">
                                                <a href="salesReport.php" class="nav-link">'.$lang['t-soReport'].'</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="purchaseReport.php" class="nav-link">'.$lang['t-poReport'].'</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="publicReport.php" class="nav-link">'.$lang['t-publicReport'].'</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="auditLog.php" class="nav-link">'.$lang['t-auditLog'].'</a>
                                            </li>
                                            <!--li class="nav-item">
                                                <a href="apiLog.php" class="nav-link">'.$lang['t-apiLog'].'</a>
                                            </li-->
                                        ';
                                    }
                                    else if($_SESSION["roles"] == 'MANAGER'){
                                        echo '
                                            <li class="nav-item">
                                                <a href="salesReport.php" class="nav-link">'.$lang['t-soReport'].'</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="purchaseReport.php" class="nav-link">'.$lang['t-poReport'].'</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="publicReport.php" class="nav-link">'.$lang['t-publicReport'].'</a>
                                            </li>
                                        ';
                                    }
                                ?>                            
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAuth">
                        <i class="ri-account-circle-line"></i> <span><?=$lang['t-setting']?></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAuth">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <?php
                                    if($_SESSION["roles"] == 'SADMIN'){
                                        echo '<li class="nav-item">
                                            <a href="cronjobSetup.php" class="nav-link">'.$lang['t-cronjobSetup'].'</a>
                                        </li> ';
                                    }

                                    if($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN'){
                                        echo '<li class="nav-item">
                                            <a href="companyProfile.php" class="nav-link">'.$lang['t-companyProfile'].'</a>
                                        </li> ';
                                    }
                                ?>
                                
                                <li class="nav-item">
                                    <a href="portSetup.php" class="nav-link"><?=$lang['t-portSetup']?></a>
                                </li> 
                                <li class="nav-item">
                                    <a href="myProfile.php" class="nav-link"><?=$lang['t-myProfile']?></a>
                                </li> 
                                <li class="nav-item">
                                    <a href="ChangePassword.php" class="nav-link"><?=$lang['t-changePassword']?></a>
                                </li>                                 
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="php/logout.php" class="nav-link"><i class="mdi mdi-logout-variant"></i> <span><?=$lang['t-logout']?></span></a>
                </li>                 
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
