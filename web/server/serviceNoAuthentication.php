<?php 
if (session_status() == PHP_SESSION_NONE)   {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
if (! isset($_POST['noAuthenticate'])) die(json_encode(array("code" => 1, "message" => "Required Parameters not set")));
//De-Authenticate
$_SESSION = array();
session_destroy();
echo json_encode(array("code" => 0, "message" => "Successful"));
?>
