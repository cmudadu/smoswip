<?php
    $common_js = md5_file('./js/common.js');
    $common_app_js = md5_file('./js/common_app.js');
    $script_js = "";
    if($script){
        $script_js = md5_file('./js/'.$script);
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>SMOSWIP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="author" content="Cristian Mudadu">
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link rel="stylesheet" href="/vendor/leafletjs/leaflet.css" />
    <link rel="stylesheet" href="/vendor/dropzone/dropzone.min.css" />
    <!-- <link rel = "stylesheet" href = "/vendor/datatables/dataTables.bootstrap4.min.css"/> -->
    <!-- <link rel = "stylesheet" href = "https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/datatables.min.css"/> -->
    <link href="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.8/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion <?php if ($page == "dashboard.php") echo "hide" ?>" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center nav-link-app" href="/dashboard">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fab fa-watchman-monitoring"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SMOSWIP</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Sites -->
            <!-- <li class="nav-item <?php if ($page == "sites.php") echo "active" ?>">
                <a class="nav-link nav-link-app" href="/sites">
                    <i class="fas fa-fw fa-map-marker-alt"></i>
                    <span>Sites</span></a>
            </li> -->


            <!-- Nav Item - Site overview -->
            <li class="nav-item <?php if ($page == "site_overview.php") echo "active" ?> <?php if ($site["id"] == "0") echo "d-none" ?>">
                <a class="nav-link nav-link-app" href="/sites/<?php echo $site["id"] ?>/overview">
                    <i class="fas fa-fw fa-stream"></i>
                    <span>Overview</span></a>
            </li>
           

            <!-- Nav Item - Alerts -->
            <li class="nav-item <?php if ($page == "alerts.php") echo "active" ?>">
                <a class="nav-link nav-link-app" href="/alerts?site_id=<?php echo $site["id"] ?>">
                    <i class="fas fa-fw fa-bell"></i>
                    <span>Alerts</span></a>
            </li>

            <!-- Nav Item - Site detail -->
            <li class="nav-item <?php if ($page == "site_detail.php") echo "active" ?> <?php if ($site["id"] == "0") echo "d-none" ?>">
                <a class="nav-link nav-link-app" href="/sites/<?php echo $site["id"] ?>">
                    <i class="fas fa-fw fa-map-marker-alt"></i>
                    <span>Detail</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">



        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <select class="form-control" id="site">
                                <option value="0"></option>
                                <?php foreach ($sites as $s) {

                                    $selected = $s["id"] == $site["id"] ? "selected" : "";


                                    echo "<option value='" . $s["id"] . "' " . $selected . ">" . $s["name"] . "</option>";
                                }
                                ?>
                            </select>
                            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addSiteModal">
                                <i class="fas fa-plus fa-sm text-white-50"></i> Add Site
                            </button>
                            <?php if ($site["id"] != "0") { ?>
                                <button type="button" class="ml-2 d-none d-sm-inline-block btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addSensorModal">
                                    <i class="fas fa-plus fa-sm"></i> Add Sensor
                                </button>

                            <?php } ?>
                            <!--<input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>-->
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter alert-counter"></span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header alert-center">
                                    Alerts Center
                                </h6>
                                <div id="last-alert-container"></div>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $user_email ?></span>
                                <img class="img-profile rounded-circle" src="/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="/doc">
                                    <i class="fas fa-fw fa-book"></i>
                                    <span>Docs</span>
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <?php include($page); ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>2023 - SMOSWIP - Progetto di tesi Cristian Mudadu, relatore Prof. Luigi Fiorentini - Università degli Studi eCampus</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" onclick="doLogout()">Logout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1" aria-labelledby="addSiteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSiteModalLabel">Add new site</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-save-site" class="needs-validation" novalidate>
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="name" class="col-form-label">Name *:</label>
                            <input type="text" class="form-control" id="site_name" required>

                        </div>
                        <div class="form-group">
                            <label for="description" class="col-form-label">Description:</label>
                            <textarea class="form-control" id="site_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="lat" class="col-form-label">Latitude *:</label>
                            <input type="text" class="form-control" id="site_lat" required>
                        </div>
                        <div class="form-group">
                            <label for="lon" class="col-form-label">Longitude *:</label>
                            <input type="text" class="form-control" id="site_lon" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-site">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add sensor Modal -->
    <div class="modal fade" id="addSensorModal" tabindex="-1" aria-labelledby="addSensorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSensorModalLabel">Add new sensor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-save-sensor" class="needs-validation" novalidate>
                        <input type="hidden" id="site_id" value="<?php echo $site["id"] ?>">
                        <div class="form-group">
                            <label for="sensortype_id" class="col-form-label">Type *:</label>
                            <select class="form-control" id="sensortype_id" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-form-label">Name *:</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-form-label">Description:</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="lat" class="col-form-label">Latitude:</label>
                                <input type="text" class="form-control" id="lat">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="lon" class="col-form-label">Longitude:</label>
                                <input type="text" class="form-control" id="lon">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="alt" class="col-form-label">Altitude:</label>
                                <input type="text" class="form-control" id="alt">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save-sensor">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <!--<script src="/vendor/chart.js/Chart.min.js"></script>-->
    <script src="/vendor/moment/moment.min.js"></script>
    <script src="/vendor/chart.js/chart.umd.min.js"></script>
    <script src="/vendor/chart.js/chartjs-adapter-moment.min.js"></script>

    <script src="/vendor/leafletjs/leaflet.js"></script>
    <script src="/vendor/dropzone/dropzone.min.js"></script>
    <!-- <script src="/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/dataTables.bootstrap4.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/datatables.min.js"></script> -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-1.13.8/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="/js/common.js?ts=<?php echo $common_js ?>"></script>
    <script src="/js/common_app.js?ts=<?php echo $common_app_js ?>"></script>
    <?php if ($script != "") { ?>
        <script src="/js/<?php echo $script ?>?ts=<?php echo $script_js ?>"></script>
    <?php } ?>
</body>

</html>