<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['login'][0]['id'])) {
  header("Location: ../");
  exit();
}
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
  <script src="../common/constants.js"></script>
  <script src="../js/page.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div id="wrapper">
      <!--BEGIN ADDING YOUR CUSTOM CONTENTS HERE-->
<?php 
  $dbname = $config1->getDatabase();
  $host = $config1->getHostname();
  $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
  $window1 = UITabularView::query($conn,"SELECT * FROM _patient_finance_queue", array(
    "idColumn" => "queueId",
    "caption" => "Manage Services",
    "href" => $thispage."?page=hello&cwd=",
    "appendId" => true
  ), array(
    'timeOfCreation' => array('caption' => 'Muda wa Kujisaliji'),
    'listOfServices' => array('values' => array(60 => 'HAL'))
  ));
  echo $window1;
  $conn = null;
?>
      <!--END ADDING YOUR CUSTOM CONTENTS HERE-->
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
    
    function UITabularViewSearch(searchText)  {
      var $container1 = $('#__ui_tabular_view__ctn__001__'); //You will update this later
      if (! $container1.length) return false;
      var $table1 = $container1.find('table.ui-tabular-view-table');
      if (! $table1.length) return false;
      var serialNumber = 0;
      $table1.find('tbody tr').each(function(i, tr) {
        var $tr1 = $(tr);
        var includeRow = false;
        $tr1.find('td.data-search').each(function(j, td)  {
          if (! includeRow) {
            var text1 = $(td).text();
            console.log(text1);
            //put maths here
            if (text1.toLowerCase().indexOf(searchText.toLowerCase()) !== -1) {
              includeRow = true;
            }
          }
        });
        if (includeRow) {
          serialNumber++;
          var $th1 = $tr1.find('th.data-serial');
          if ($th1.length) $th1.text(serialNumber);
          $tr1.show();
        }
        else $tr1.hide();
      });
    }
    $('input.ui-tabular-view-search').autocomplete({
      source: function(request, response) {
        UITabularViewSearch(request.term);
      },
      minLength: 3
    });
  </script>
</body>

</html>