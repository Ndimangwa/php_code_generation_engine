<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['login'][0]['id'])) {
    header("Location: system/");
    exit();
}
require "vendor/autoload.php";
require_once("sys/__autoload__.php");
$conn = null;
$profile1 = null;
try {
    $config1 = new ConfigurationData("config.php");
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
} catch (PDOException $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $profile1->getProfileName() ?></title>
    <link rel="stylesheet" href="cdns/default/css/fontawesome.css"  crossorigin="anonymous"/>
    <link rel="stylesheet" href="cdns/default/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="css/page.css" rel="stylesheet" crossorigin="anonymous"/>
</head>
<body id="home" data-spy="scroll" data-target="#main-nav">
       <!-- <div class="container"> -->
<!--START HERE-->

<!--NAVBAR-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark p-0">
    <div class="container">
        <a href="index.html" class="navbar-brand"><?= $profile1->getProfileName() ?></a>
    </div>
</nav>

<!--HEADER-->
<header id="main-header" class="py-2 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>
                    <i class="fas fa-user"></i>&nbsp;&nbsp;<?= $profile1->getSystemName() ?>
                </h1>
            </div>
        </div>
    </div>
</header>

<!--ACTIONS-->
<section id="actions" class="py-4 mb-4 bg-light">
    <div class="container">
        <div class="row"></div>
    </div>
</section>

<!--LOGIN-->
<section id="login">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Account Login</h4>
                    </div>
                    <div class="card-body">
                        <form id="form1" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control" autocomplete="username" required <?= Registry::getUIControlValidations('Login', 'loginName', 'text') ?>/>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required <?= Registry::getUIControlValidations('Login', 'password', 'text') ?>/>
                            </div>
                            <div id="error1" class="p-2 ui-sys-error-message"></div>
                            <!-- <input id="loginButton" type="button" value="Log On System" class="btn btn-primary btn-block btn-click-default" data-form-submit="form1" data-form-error="error1"/> -->
                            <button id="loginButton" type="button" class="btn btn-primary btn-block btn-click-default" data-form-submit="form1" data-form-error="error1">Log On System</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--FOOTER-->
<footer id="main-footer" class="bg-dark text-white mt-5 p-5">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="p lead text-center">
                    Copyright &copy; <span id="year"></span> <?= $profile1->getProfileName() ?>
                    <div><i class="text-muted" style="font-size: 0.9em;">Software Version: <?= SystemSettings::getVersion() ?></i></div>
                </div>
            </div>
        </div>
    </div>
</footer>


<!--END HERE-->
        <!-- </div> -->
    <script src="cdns/default/js/jquery.min.js" crossorigin="anonymous"></script>
    <script src="cdns/default/js/popper.min.js" crossorigin="anonymous"></script>
    <script src="cdns/default/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/page.js"></script>
    <script type="text/javascript">
        $('#year').text(new Date().getFullYear());
        //Login
        $('#loginButton').on('click', function(event)	{
            event.preventDefault();
            var $button1 = $(this);
            var $form1 = $button1.closest('form');
            var $errorTarget1 = $('#' + $button1.data('formError'));
		    if (! generalFormValidation($button1, $form1, $errorTarget1)) {
                return false;
            }
            //Proceed 
            var dataToSend = $form1.serializeObject();
            sendAjax(
                $button1,
                $errorTarget1,
                'server/serviceAuthentication.php',
                dataToSend,
                'system/',
                null,
                'POST',
                true,
                false
            );
	    });
    </script>
</body>
</html>
