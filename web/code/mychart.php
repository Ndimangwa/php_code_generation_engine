<?php 
require "../vendor/autoload.php";
require_once("../common/__autoload__.php");
require_once("../sys/__autoload__.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delta Init</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../cdns/AdminLTE-3.1.0/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
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
  <style type="text/css">
    .ui-documentation {
      font-size: 1.2em;
      position: relative;
    }

    .ui-documentation table {
      margin-left: 3px;
      padding: 1px;
    }
  </style>
  <!-- jQuery -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="../cdns/AdminLTE-3.1.0/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!--bootstrap - autocomplete -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.4.11/jquery.autocomplete.min.js" integrity="sha512-uxCwHf1pRwBJvURAMD/Gg0Kz2F2BymQyXDlTqnayuRyBFE7cisFCh2dSb1HIumZCRHuZikgeqXm8ruUoaxk5tA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.15.4/extensions/print/bootstrap-table-print.js"></script>
  <script src="../common/constants.js"></script>
  <script src="../js/page.js"></script>
  <script>
    (function($)  {
      $(function()  {
        $('#sname').on(Constant.default_event_name, function(e, param1) {
          const { value } = param1;
          var trace = $(this).data('trace');
          window.alert("[ value, trace ] : [ " + value + ", " + trace + " ]");
        });

        $("#scomment").on(Constant.default_event_name, function(event, param1)  {
          const { value } = param1;
          var trace = $(this).data('trace');
          window.alert("[ value, trace ] : [ " + value + ", " + trace + " ]");
        });

        $("#scount").on(Constant.default_event_name, function(event, param1)  {
          const { value } = param1;
          var trace = $(this).data('trace');
          window.alert("[ value, trace ] : [ " + value + ", " + trace + " ]");
        });

        $("#scategory").on(Constant.default_event_name, function(event, param1)  {
          const { values } = param1;
          var trace = $(this).data('trace');
          window.alert("[ values, trace ] : [ " + values + ", " + trace + " ]");
        });

        $("#sdiagnosis").on(Constant.default_event_name, function(event, param1)  {
          const { values } = param1;
          var trace = $(this).data('trace');
          window.alert("[ value, trace ] : [ " + values + ", " + trace + " ]");
        });
      });
    })(jQuery);
  </script>

</head>

<body>
  <div>

    <!--BEGIN: My Code is Starting Here-->
    <?php 
    $config1 = new ConfigurationData("../config.php");
    $dbname = $config1->getDatabase();
    $host = $config1->getHostname();
    try {
      $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
     
      echo ChartEngine::getChart(450, 250, array(
        'type' => 'line',
        'options' => array(
            'responsive' => true,
            'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
        )
    ), array(
      'labels' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
      'datasets' => array(
          array(
              'label' => 'Month Dataset',
              'data' => array(801, 701, 601, 501, 401, 301, 301, 401, 501, 601, 701, 801) 
          )
      )
  ), null, 3);
     
      $conn = null;
    } catch (Exception $e)  {
      die($e->getMessage());
    }
    ?>
    <!--END: My Code is Ending Here-->

  </div>
</body>

</html>