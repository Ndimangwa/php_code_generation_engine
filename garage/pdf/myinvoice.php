<?php 
require "../../html/vendor/autoload.php";
require_once("../../html/sys/__autoload__.php");

try {
    $config1 = new ConfigurationData("../../html/config.php");
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($dbname, Login::$__LOGIN_INIT_ID, $conn);
    $invoice1 = new PatientInvoice($dbname, 1, $conn);
    $pdf1 = PDFEngine::getADefaultEngine(array('font-family' => 'helvetica'));
    PatientInvoice::getPDFPrintOut($pdf1, $invoice1, $profile1, $login1);
    $pdf1->print();
    $conn = null;
} catch (Exception $e)  {
    die($e->getMessage());
}
?>