<?php
if (session_status() == PHP_SESSION_NONE)   {
    session_start();
} 
require "../../vendor/autoload.php";
require_once("../../sys/__autoload__.php");

try {
    if (! (isset($_SESSION['login']) && isset($_SESSION['login'][0]) && isset($_SESSION['login'][0]['id']))) throw new Exception("Kindly make sure you are logged-in");
    //Must Have set invoice_id
    if (! isset($_REQUEST['id'])) throw new Exception("Could not receive an invoice id");
    //Must state document type dtype
    if (! isset($_REQUEST['dtype'])) throw new Exception("Document Type Not Set");
    $config1 = new ConfigurationData("../../config.php");
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($dbname, $_SESSION['login'][0]['id'], $conn);
    $pdf1 = PDFEngine::getADefaultEngine(array('font-family' => 'helvetica', 'font-size' => 11)); //Work on this 
    $pdf1->setLogoFile("../../assets/images/logo.png");
    switch ($_REQUEST['dtype']) {
        case (Documents::$__PDF_INVOICE):
            if (Authorize::isAllowable($config1, "patientinvoice_read", "normal", "setlog", null, null))    {
                $invoice1 = new PatientInvoice($dbname, $_REQUEST['id'], $conn);
                PatientInvoice::getPDFPrintOut($pdf1, $invoice1, $profile1, $login1);
            }
            break;
        case (Documents::$__PDF_RECEIPT):
            if (Authorize::isAllowable($config1, "patientreceipt_read", "normal", "setlog", null, null))   {
                $receipt1 = new PatientReceipt($dbname, $_REQUEST['id'], $conn);
                PatientReceipt::getPDFPrintOut($pdf1, $receipt1, $profile1, $login1);
            }
            break;
        case (Documents::$__MEDICAL_CONSULTATION_QUEUE):
            if (Authorize::isAllowable($config1, "medicaldoctor_consult", "normal", "setlog", null, null))    {
                $consultationQueue1 = new MedicalDoctorConsultationQueue("Delta", $_REQUEST['id'], $conn);
                $consultationQueue1->getPDFTabularSummary($pdf1, $profile1, $login1, null, null, null);
            }
            break;
        case (Documents::$__VITAL_SIGNS):
            if (isset($_REQUEST['class']) && Authorize::isAllowable($config1, "vitalsigns_read", "normal", "setlog", null, null))   {
                $vitalSigns1 = Registry::getObjectReference("Delta", $conn, $_REQUEST['class'], $_REQUEST['id']);
                if (! is_null($vitalSigns1))    {
                    __data__::createPDFTabularSummary($pdf1, $vitalSigns1, array(
                        "weight", "height", "bmi", "temperature", "bloodPressure", "pulseRate", "respirationRate", "oxygenLevel", "attendedBy"
                    ), array(
                        'header-title' => ( $profile1->getProfileName() ),
                        'footer-title' => ( $profile1->getProfileName() ),
                        'table-title' => 'Vital Signs'
                    ), null, true, true, true, null, array(
                        ( Triage::$__DEFAULT_NUMBER_VALUE ), ( Triage::$__DEFAULT_BP_VALUE )
                    ), null );
                }
            }
            break;
            //Default for testing only
        default:
            $examinationQueue1 = new PatientExaminationQueue("Delta", 1, $conn);
            $examinationQueue1->getPDFTabularSummary($pdf1, $profile1, $login1, null, true, true, true, null, null);
            break;
        }
    $pdf1->print();
    $conn = null;
} catch (Exception $e)  {
    die($e->getMessage());
}
?>