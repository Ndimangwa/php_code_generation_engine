<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['login'][0]['id'])) {
  header("Location: ../");
  exit();
}
require "../vendor/autoload.php";
require_once("../common/__autoload__.php"); //common for both ui and back-end
require_once("../sys/__autoload__.php");
$conn = null; //Since this is a multi-page environment, you have to close the connection immediately after using, due to static-methods which initiate connections themselves
$profile1 = null;
$login1 = null;
$config1 = new ConfigurationData("../config.php");
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
  $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
  $login1 = new Login($dbname, $_SESSION['login'][0]['id'], $conn);
} catch (Exception $e) {
  die($e->getMessage()); //Prepare a safer landing page
}
$conn = null;
date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
//page-navigation
$thispage = $_SERVER['PHP_SELF'];
$thisdir = dirname($thispage);
$page = null;
if (isset($_REQUEST['page'])) $page = $_REQUEST['page'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $profile1->getProfileName() ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="../cdns/google/fonts/google-fonts.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../cdns/ionicframework/ionic-framework.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!--Bootstrap Switch -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="../css/page.css">
  <!-- jQuery -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!--bootstrap - autocomplete -->
  <script src="../cdns/jquery-autocomplete/autocomplete.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/sparklines/sparkline.js"></script>
  <!--Bootstrap Switch -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
  <!-- JQVMap -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../cdns/AdminLTE-3.1.0/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/moment/moment.min.js"></script>
  <script src="../cdns/AdminLTE-3.1.0/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../cdns/AdminLTE-3.1.0/dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../cdns/AdminLTE-3.1.0/dist/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../cdns/AdminLTE-3.1.0/dist/js/pages/dashboard.js"></script>
  <script src="../cdns/ckeditor/ckeditor.js"></script>
  <script src="../cdns/jquery-cookie/jquery.cookie.js"></script>
  <script src="../common/constants.js"></script>
  <script src="../js/page.js"></script>
  <script src="../js/ontest.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../cdns/AdminLTE-3.1.0/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="<?= $thispage ?>" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-comments"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../cdns/AdminLTE-3.1.0/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Brad Diesel
                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">Call me whenever you can...</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../cdns/AdminLTE-3.1.0/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    John Pierce
                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">I got your message bro</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../cdns/AdminLTE-3.1.0/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Nora Silvester
                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">The subject goes here</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
          </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">15</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header">15 Notifications</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> 4 new messages
              <span class="float-right text-muted text-sm">3 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-users mr-2"></i> 8 friend requests
              <span class="float-right text-muted text-sm">12 hours</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-file mr-2"></i> 3 new reports
              <span class="float-right text-muted text-sm">2 days</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("../pages/general/gsidebar.php"); ?>
    <!--End Main Sidebar Container-->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><?= PageMovement::getDisplayText($config1, $page, $login1->getLoginName()) ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <?= PageMovement::getBreadCrumbs($thispage, $page, 
                array(
                  'update_my_login' => array('page' => 'login_update'),
                  'medicaldoctor_consult' => array('page' => 'consultation'),
                  'medicaldoctor_approve_results' => array('page' => 'medicaldoctor_consult_resultsapprove'),
                  'medicaldoctor_repeat_examination' => array('page' => 'medicaldoctor_consult_repeatexamination')
                  )) ?>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!--BEGIN :: PAGE HIERARCHY-->
          <?php
          if ($page == "downloadfiles" && $login1->isRoot())  {
            include("../pages/files/download/download.php");
          } else if ($page == "uploadfiles" && $login1->isRoot())  {
            include("../pages/files/upload/upload.php");
          } else if ($page == "examination_ultrasound" && isset($_REQUEST['qid']) && isset($_REQUEST['qtype']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab_ui.php");
          } else if ($page == "examination_ultrasound" && isset($_REQUEST['qtype']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab.php");
          } else if ($page == "examination_xray_plain" && isset($_REQUEST['qtype']) && isset($_REQUEST['qid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab_ui.php");
          } else if ($page == "examination_xray_plain" && isset($_REQUEST['qtype']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab.php");
          } else if ($page == "examination_wetlab" && isset($_REQUEST['qid']) && isset($_REQUEST['qtype']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab_ui.php");
          } else if ($page == "examination_wetlab" && isset($_REQUEST['qtype']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/examination/run_lab.php");
          } else if ($page == "medicaldoctor_consult" && isset($_REQUEST['mid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/medicalconsultation/consultation_main.php");
          } else if ($page == "medicaldoctor_consult" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/medicaldoctor/medicaldoctor_consult.php");
          } else if ($page == "nursestation_record" && isset($_REQUEST['qid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/nursestation/nursestation_record.php");
          } else if ($page == "nursestation" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/nursestation/nursestation.php");
          } else if ($page == "vitalsigns_create" && isset($_REQUEST['patientId']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/triage/vitalsigns_create.php");
          } else if ($page == "vitalsigns" && isset($_REQUEST['report']) && isset($_REQUEST['patientId']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/triage/vitalsigns_read.php");
          } else if ($page == "triage_create" && isset($_REQUEST['qid']) && isset($_REQUEST['report']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/triage/triage_create_report.php");
          } else if ($page == "triage_create" && isset($_REQUEST['qid']) /*qid is for triage_queue id*/ && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/triage/triage_create.php");
          } else if ($page == "triage" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/triage/triage.php");
          } else if ($page == "patientreceipt_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientreceipt/patientreceipt_read.php");
          } else if ($page == "patientreceipt_create" && isset($_REQUEST['efilter']) && isset($_REQUEST['id']) /*invoice_id*/ && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patientreceipt/patientreceipt_create_ui.php");
          } else if ($page == "patientreceipt_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patientreceipt/patientreceipt_create.php");
          } else if ($page == "patientreceipt" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientreceipt/patientreceipt.php");
          } else if ($page == "patientinvoice_custom" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientinvoice/patientinvoice_custom.php");
          } else if ($page == "patientinvoice_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patientinvoice/patientinvoice_read.php");
          } else if ($page == "patientinvoice_create" && isset($_REQUEST['qid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientinvoice/patientinvoice_create_ui.php");
          } else if ($page == "patientinvoice_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientinvoice/patientinvoice_create.php");
          } else if ($page == "patientinvoice" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patientinvoice/patientinvoice.php");
          } else if ($page == "notificationmanager_delete" && isset($_REQUEST['id']) && $login1->isRoot()) {
            include("../pages/notificationmanager/notificationmanager_delete.php");
          } else if ($page == "notificationmanager_update" && isset($_REQUEST['id']) && $login1->isRoot()) {
            include("../pages/notificationmanager/notificationmanager_update.php");
          } else if ($page == "notificationmanager_read" && isset($_REQUEST['id']) && $login1->isRoot()) {
            include("../pages/notificationmanager/notificationmanager_read.php");
          } else if ($page == "notificationmanager_create" && $login1->isRoot()) {
            include("../pages/notificationmanager/notificationmanager_create.php");
          } else if ($page == "notificationmanager" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/notificationmanager/notificationmanager.php");
          } else if ($page == "theatre_read_list" && isset($_REQUEST['qid']) && isset($_REQUEST['serviceId']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/theatre/theatre_recordings.php");
          } else if ($page == "theatre_read_list" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/theatre/theatre_read_list.php");
          } else if ($page == "theatre_delete" && isset($_GET['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/theatre/theatre_delete.php");
          } else if ($page == "theatre_read" && isset($_GET['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/theatre/theatre_read.php");
          } else if ($page == "theatre_update" && isset($_GET['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/theatre/theatre_update.php");
          } else if ($page == "theatre_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/theatre/theatre_create.php");
          } else if ($page == "theatre" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            //Note User-Data are carried in Login table
            include("../pages/theatre/theatre.php");
          } else if ($page == "admission_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/admission_read.php");
          } else if ($page == "admission_read" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/patient_admission_list.php");
          } else if ($page == "admission_create" && isset($_REQUEST['qid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/admission_create.php");
          } else if ($page == "admission_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/patient_admission_queue.php");
          } else if ($page == "bed_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/bed/bed_delete.php");
          } else if ($page == "bed_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/bed/bed_update.php");
          } else if ($page == "bed_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/bed/bed_read.php");
          } else if ($page == "bed_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/bed/bed_create.php");
          } else if ($page == "bed" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/bed/bed.php");
          } else if ($page == "room_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/room/room_delete.php");
          } else if ($page == "room_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/room/room_update.php");
          } else if ($page == "room_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/room/room_read.php");
          } else if ($page == "room_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/room/room_create.php");
          } else if ($page == "room" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/room/room.php");
          } else if ($page == "ward_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/ward/ward_delete.php");
          } else if ($page == "ward_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/ward/ward_update.php");
          } else if ($page == "ward_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/ward/ward_read.php");
          } else if ($page == "ward_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/ward/ward_create.php");
          } else if ($page == "ward" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/admission/ward/ward.php");
          } else if ($page == "dispense_drug" && isset($_REQUEST['qid']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/dispense_drug.php");
          } else if ($page == "dispense_drug" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/dispense_drug_queue.php");
          } else if ($page == "pharmaceuticaldrug_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/pharmaceuticaldrug_delete.php");
          } else if ($page == "pharmaceuticaldrug_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/pharmaceuticaldrug_update.php");
          } else if ($page == "pharmaceuticaldrug_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/pharmaceuticaldrug_read.php");
          } else if ($page == "pharmaceuticaldrug_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/pharmaceuticaldrug_create.php");
          } else if ($page == "pharmaceuticaldrug" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/pharmaceuticaldrug/pharmaceuticaldrug.php");
          } else if ($page == "service_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/service/service_create.php");
          } else if ($page == "service_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/service/service_read.php");
          } else if ($page == "service_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/service/service_update.php");
          } else if ($page == "service_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/service/service_delete.php");
          } else if ($page == "service" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/service/service.php");
          } else if ($page == "servicecategory_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/servicecategory/servicecategory_create.php");
          } else if ($page == "servicecategory_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/servicecategory/servicecategory_read.php");
          } else if ($page == "servicecategory_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/servicecategory/servicecategory_update.php");
          } else if ($page == "servicecategory_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/servicecategory/servicecategory_delete.php");
          } else if ($page == "servicecategory" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/servicecategory/servicecategory.php");
          } else if ($page == "patient_update" && isset($_REQUEST['new_visit']) && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patient/patient_new_visit_update.php");
          } else if ($page == "patient_read" && isset($_REQUEST['history']) && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal","setlog",null, null)) {
            include("../pages/patient/patient_history.php");
          } else  if ($page == "patient_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patient/patient_read.php");
          } else if ($page == "patient_update" && isset($_GET['id']) && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null))  {
            include("../pages/patient/patient_update.php");
          } else if ($page == "patient_create" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patient/patient_create.php");
          } else if ($page == "patient" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/patient/patient.php");
          } else if ($page == "firewall_read" && isset($_GET['class']) && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/firewall/firewall_read.php");
          } else if ($page == "firewall_update" && isset($_GET['class']) && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/firewall/firewall_update.php");
          } else if ($page == "group_delete" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/group/group_delete.php");
          } else if ($page == "group_read" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/group/group_read.php");
          } else if ($page == "group_update" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/group/group_update.php");
          } else if ($page == "group_create" && $login1->isRoot()) {
            include("../pages/group/group_create.php");
          } else if ($page == "group" && $login1->isRoot()) {
            //Note User-Data are carried in Login table
            include("../pages/group/group.php");
          } else if ($page == "jobtitle_delete" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/jobtitle/jobtitle_delete.php");
          } else if ($page == "jobtitle_read" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/jobtitle/jobtitle_read.php");
          } else if ($page == "jobtitle_update" && isset($_GET['id']) && $login1->isRoot()) {
            include("../pages/jobtitle/jobtitle_update.php");
          } else if ($page == "jobtitle_create" && $login1->isRoot()) {
            include("../pages/jobtitle/jobtitle_create.php");
          } else if ($page == "jobtitle" && $login1->isRoot()) {
            //Note User-Data are carried in Login table
            include("../pages/jobtitle/jobtitle.php");
          } else if ($page == "medicaldoctor_delete" && isset($_REQUEST['id']) && $login1->isRoot()) {
            include("../pages/medicaldoctor/medicaldoctor_delete.php");
          } else if ($page == "medicaldoctor_update" && isset($_REQUEST['id']) && $login1->isRoot())  {
            include("../pages/medicaldoctor/medicaldoctor_update.php");
          } else if ($page == "medicaldoctor_read" && isset($_REQUEST['id']) && $login1->isRoot())  {
            include("../pages/medicaldoctor/medicaldoctor_read.php");
          } else if ($page == "medicaldoctor_create" && $login1->isRoot())  {
            include("../pages/medicaldoctor/medicaldoctor_create.php");
          } else if ($page == "medicaldoctor" && $login1->isRoot())  {
            include("../pages/medicaldoctor/medicaldoctor.php");
          } else if ($page == "systemuser_delete" && isset($_REQUEST['id']) && $login1->isRoot())  {
            include("../pages/systemuser/systemuser_delete.php");
          } else if ($page == "systemuser_update" && isset($_REQUEST['id']) && $login1->isRoot())  {
            include("../pages/systemuser/systemuser_update.php");
          } else if ($page == "systemuser_read" && isset($_REQUEST['id']) && $login1->isRoot())  {
            include("../pages/systemuser/systemuser_read.php");
          } else if ($page == "systemuser_create" && $login1->isRoot())  {
            include("../pages/systemuser/systemuser_create.php");
          } else if ($page == "systemuser" && $login1->isRoot())  {
            include("../pages/systemuser/systemuser.php");
          } else if ($page == "systemlogs" && $login1->isRoot()) {
            include("../pages/general/systemlogs.php");
          } else if ($page == "lastresortdonotcare" && $login1->isRoot()) {
            include("../pages/context/lastresortdonotcare.php");
          } else if ($page == "update_my_login" && Authorize::isAllowable($config1, $page, "normal", "setlog", null, null)) {
            include("../pages/general/updatemylogin.php");
          } else if ($page == "hospital_update" && $login1->isRoot())  {
            include("../pages/hospital/hospital_update.php");
          } else if ($page == "profile_update" && $login1->isRoot()) {
            include("../pages/profile/profile_update.php");
          } else {
            if (Authorize::isSessionSet()) {
              include("../pages/general/operationDenied.php");
            }
            include("../pages/dashboard/main_dashboard.php");
          }
          ?>
          <!--END :: PAGE HIERARCHY-->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; <span id="year"></span> <?= $profile1->getProfileName() ?>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> <?= SystemSettings::getVersion() ?>
      </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!--BEGIN ADDED BY NDIMANGWA FADHILI NGOYA-->
  <div class="modal fade" id="__status_query_modal__" data-secondary-modal="__status_query_modal__02" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Default Modal Title</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button class="btn btn-primary dialog-save-button" type="button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="__status_query_modal__02" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Default Modal Title</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button class="btn btn-primary dialog-save-button" type="button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!--ADVANCED QUERY COLUMNS EXECUTOR-->
  <div class="modal fade" id="__dialog_search_container_01__" tabindex="-2" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Default Modal Title</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button class="btn btn-outline-warning" type="button" data-dismiss="modal">Close</button>
          &nbsp;&nbsp; <button class="btn btn-outline-primary btn-dialog-search" type="button" data-dismiss="modal">Search Data</button>
        </div>
      </div>
    </div>
  </div>
  <!--END ADDED BY NDIMANGWA FADHILI NGOYA-->

  <script type="text/javascript">
    $('#year').text(new Date().getFullYear());
    //switch-text
    /*$('#testing').on('change.bootstrapSwitch', function(e) {
      window.alert(e.target.checked);
    });*/
    //CKEDITOR.replace( 'editor1' );
    $('#logoutButton').on('click', function(event) {
      event.preventDefault();
      var $button1 = $(this);
      var $errorTarget1 = $('<span/>');
      dataToSend = {
        noAuthenticate: true
      };
      sendAjax(
        $button1,
        $errorTarget1,
        '../server/serviceNoAuthentication.php',
        dataToSend,
        '../',
        null,
        'POST',
        true,
        false
      );
    });
    //DialogAjax
    $('button.btn-send-dialog-ajax').on('click', function(e) {
      e.preventDefault();
      var $button1 = $(this);
      var $form1 = $button1.closest('form');
      var $dialog1 = $('#__status_query_modal__');
      var errorTarget = $button1.data('formError');
      $errorTarget1 = $('#' + errorTarget);
      if ($errorTarget1.length && !generalFormValidation($button1, $form1, $errorTarget1, Constant)) return false;
      var dataToSend = $form1.serializeObject();
      var nextPage = "<?= $thispage ?>?page=<?= $page ?>";
      if ($button1.attr('data-next-page') !== undefined) nextPage = $button1.attr('data-next-page');
      var serverScript = '../server/serviceQueryProcessor.php';
      if ($button1.attr('data-server-script') !== undefined) serverScript = "../server/" + $button1.attr('data-server-script') + '.php';
      sendAjaxDialog(
        $button1,
        $dialog1,
        serverScript,
        dataToSend,
        nextPage,
        null,
        "POST",
        true,
        false,
        "Saving ...",
        "Saved",
        "Retry ..."
      );
    });
    //Change Password
    $('button.btn-change-password-dialog-ajax').on('click', function(e) {
      e.preventDefault();
      var $button1 = $(this);
      var $form1 = $button1.closest('form');
      var $dialog1 = $('#__status_query_modal__');
      var errorTarget = $button1.data('formError');
      $errorTarget1 = $('#' + errorTarget);
      //We need to perform manual validation for Password
      var $oldPassword1 = $('#oldPassword');
      var $newPassword1 = $('#newPassword');
      var $confirmNewPassword1 = $('#confirmNewPassword');
      if (!($oldPassword1.length && $newPassword1.length && $confirmNewPassword1.length)) return false;
      if ($newPassword1.val() != $confirmNewPassword1.val()) {
        $errorTarget1.empty();
        if (!$newPassword1.hasClass('invalid-input')) $newPassword1.addClass('invalid-input');
        if (!$confirmNewPassword1.hasClass('invalid-input')) $confirmNewPassword1.addClass('invalid-input');
        $('<span/>').text('New Password and Confirm New Password does not match').appendTo($errorTarget1);
        return false;
      }
      if ($errorTarget1.length && !generalFormValidation($button1, $form1, $errorTarget1, Constant)) return false;
      var dataToSend = $form1.serializeObject();
      sendAjaxDialog(
        $button1,
        $dialog1,
        '../server/serviceChangePassword.php',
        dataToSend,
        '<?= $thispage ?>?page=<?= $page ?>',
        null,
        "POST",
        true,
        false,
        "Saving ...",
        "Saved",
        "Retry ..."
      );
    });
    //Working on Search
    $('body').on('click', 'button.btn-perform-search', function(e) {
      var $button1 = $(this);
      var $form1 = $('#' + $button1.data('formId'));
      var $errorTarget1 = $('#' + $button1.data('errorTarget'));
      if (generalFormValidation($button1, $form1, $errorTarget1, Constant)) showSearchTableSection($button1, Constant);
    });
    //General Form Submission 
    $('body').on('click', 'button.btn-general-submit', function(e)  {
      var $button1 = $(this);
      var $form1 = $('#' + $button1.data('formSubmit'));
      var $errorTarget1 = $('#' + $button1.data('formError'));
      if (! $form1.length) return false;
      if (! $errorTarget1.length) return false;
      generalFormSubmission($button1, $form1, $errorTarget1, Constant);
    });
    //Autocomplete
    setAutocomplete($('.ui-txt-search-input'), "../server/getListOfRecordsBasedOnCriteria.php", "POST");
  </script>
</body>

</html>
