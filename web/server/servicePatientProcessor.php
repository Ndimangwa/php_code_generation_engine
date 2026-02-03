<?php
//Standard; return [code, query, message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
$conn = null;
$profile1 = null;
$login1 = null;
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($config1->getDatabase(), __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($config1->getDatabase(), $_SESSION['login'][0]['id'], $conn);
} catch (PDOException $e) {
    die(json_encode(array('code' => 5, 'query' => 'Init', 'message' => $e->getMessage())));
}
$conn = null; //You need to close connection while calling Authorization::isAllowable
$bundleCode = __object__::getMD5CodedString("service_patient_processor", 32);
date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
if (!isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'query' => 'Error', 'message' => 'Kindy log-in first')));
if (!isset($_POST['__query__'])) die(json_encode(array('code' => 2, 'query' => 'Error', 'message' => 'Query Parameter was not specified')));
if (!isset($_POST['__classname__'])) die(json_encode(array('code' => 3, 'query' => 'Error', 'message' => 'Classname was not specified')));
$query = $_POST['__query__'];
$classname = $_POST['__classname__'];
//We need to work for context position $cname 
//$actionLookup = array('select' => 'read', 'insert' => 'create', 'update' => 'update', 'delete' => 'delete');
//if (! in_array($query, $actionLookup)) die(json_encode(array('code' => 4, 'query' => $query, 'message' => '[ $query ] => Could not decode the query operation')));
//$cname = strtolower($classname."_".$actionLookup[$query]); //We need to allow custom_cname $_POST['custom_context_name']//ContextPosition.cName
$cname = strtolower($classname . "_$query");
if (isset($_POST['__custom_context_name__'])) $cname = $_POST['__custom_context_name__'];
try {
    SystemRules::evaluate($conn, $profile1, $login1, $classname, $query, $cname, $_POST);
} catch (Exception $e) {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
if (!Authorize::isAllowable($config1, $cname, "normal", "donotsetlog", null, null)) die(json_encode(array('code' => 8, 'query' => $query, 'message' => '[ $cname ] => You are not authorized to carry this operation!!')));
$classid = null;
if (isset($_POST['__id__'])) $classid = $_POST['__id__'];
$successfulMessage = "Successful Operation";
if (isset($_POST['__modal_success_message__'])) $successfulMessage = $_POST['__modal_success_message__'];
$logMessage = "Default Message";
$queryArray1 = null;
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
} catch (PDOException $e) {
    die(json_encode(array('code' => 5, 'query' => $query, 'message' => $e->getMessage())));
}
//We need to try to fetch $affectedObject 
$affectedObject = "";
$colWithVal = Registry::getValue0Columnname($classname);
if (!is_null($colWithVal) && $colWithVal != "") {
    $pname = Registry::column2Property($classname, $colWithVal);
    if (!is_null($pname) && isset($_POST[$pname])) {
        $affectedObject = "[ " . $_POST[$pname] . " ] ";
    }
}
//Now working  -- begin
switch ($cname) {
    case "patient_create":
        $colArray1 = $_POST;
        $mapServiceArray1 = array(
            (PatientRegistrationType::$__PATIENT_FULL_REGISTRATION) => (Service::$__OPEN_FILE),
            (PatientRegistrationType::$__PATIENT_MIN_REGISTRATION) => (Service::$__OPEN_CARD),
            (PatientRegistrationType::$__PATIENT_TRANSFER_IN) => (Service::$__OPEN_CARD)
        );
        //Step 1: You need to check for registration number 
        try {
            $conn->beginTransaction();
            if (!isset($colArray1['registrationType'])) throw new Exception("Registration Type Information Not Found");
            $registrationType = $colArray1['registrationType'];
            $regnumber = Hospital::generateRegistrationNumber($conn, $registrationType, false);
            if (is_null($regnumber)) throw new Exception("Registration Number System returned null");
            if (!isset($colArray1['registrationNumber'])) $colArray1['registrationNumber'] = $regnumber;
            //Need to extra service-info
            if (!isset($mapServiceArray1[$registrationType])) throw new Exception("[ $registrationType ] Registration Type not known");
            $serviceId = $mapServiceArray1[$registrationType];
            $colArray1['timeOfCreation'] = $systemTime1->getTimestamp();
            $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
            //Step 2: You need to register the patient
            $patient1 = new Patient("delta", __data__::insert($conn, "Patient", $colArray1, false, Constant::$default_select_empty_value), $conn);
            //Step 3: You need to register patient case  , in case of fail, you need to rollback
            $colArray1 = array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "patient" => $patient1->getPatientId(),
                "caseType" => PatientCaseType::$__OPEN,
                "currentStage" => PatientMovementStage::$__NEW_REGISTRATION,
                "nextStage" => PatientMovementStage::$__TRIAGE,
                "medicalDoctor" => $_POST['medicalDoctor'],
                "currentVisit" => 0,
                "closed" => 0,
                "insured" => 0
            );
            if (isset($_POST['insurance'])) {
                $colArray1["insured"] = 1;
                $colArray1["insurance"] = $_POST['insurance'];
            }
            $case1 = new PatientCase($dbname, __data__::insert($conn, "PatientCase", $colArray1, false, Constant::$default_select_empty_value), $conn);
            $patient1->setCurrentCase($case1->getCaseId());
            //Step 4; You need to initialize visit 
            $colArray1 = array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "patientCase" => $case1->getCaseId(),
                "medicalDoctor" => $_POST['medicalDoctor'],
                "temporaryStringHolder" => (__object__::getMD5CodedString("NewRegistration")),
                "visitCount" => 1,
                "insured" => 0
            );
            if (isset($_POST['insurance'])) {
                $colArray1["insured"] = 1;
                $colArray1["insurance"] = $_POST['insurance'];
            }
            $visit1 = new PatientVisit("delta", __data__::insert($conn, "PatientVisit", $colArray1, false, Constant::$default_select_empty_value), $conn);
            $case1->setCurrentVisit($visit1->getVisitId());
            $case1->update(false);
            $patient1->update(false);
            //Step 5: You need to notify Finance -- We need to build a notification sequence
            //We require listOfServices
            $listOfServices = array();
            $listOfServices[sizeof($listOfServices)] = $serviceId;
            /*if (isset($_POST['medicalDoctor']) && ($_POST['medicalDoctor'] != Constant::$default_select_empty_value))   {
                $medicalDoctor1 = new MedicalDoctor("Delta", $_POST['medicalDoctor'], $conn);
                $consultationServiceId = Service::$__NON_SPECIALIST_CONSULTATION;
                if ($medicalDoctor1->isSpecialist()) $consultationServiceId = Service::$__SPECIALIST_CONSULTATION;
                $listOfServices[sizeof($listOfServices)] = $consultationServiceId;
            }*/
            //You need to update financeQueue
            //Now financeQuenue
            //Since the PatientMovementStage of NEW_REGISTRATION requirePayment, just update PatientFinanceQueue
            __data__::insert($conn, "PatientFinanceQueue", array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "visit" => $visit1->getVisitId(),
                "patientCase" => $case1->getCaseId(),
                "patient" => $patient1->getPatientId(),
                "listOfServices" => implode(",", $listOfServices),
                "actionStage" => (PatientMovementStage::$__NEW_REGISTRATION),
                "temporaryObjectHolder" => $patient1->getObjectReferenceString(),
                "bundleCode" => $bundleCode
            ), false);
            //Add New Registration Log 
            PatientFile::addNewRegistrationLog($conn, $systemTime1, $visit1, $login1, $visit1->getTemporaryStringHolder(), false);
            $conn->commit();
            $logMessage = $affectedObject . "Created Successful";
            $queryArray1 = array("code" => 0, "query" => $query, "message" => "Succesful");
        } catch (PDOException $e) {
            $conn->rollBack();
            die(json_encode(array("code" => 1, "query" => $query, "message" => $e->getMessage())));
        } catch (Exception $e) {
            $conn->rollBack();
            die(json_encode(array("code" => 1, "query" => $query, "message" => $e->getMessage())));
        }
        break;
    case "patient_update":
        try {
            $colArray1 = $_POST;
            $patient1 = new Patient("Delta", $_POST['id'], $conn);
            $patient1->updateList($colArray1, Constant::$default_select_empty_value)->update(true);
            $logMessage = $affectedObject . "Updated Successful";
            $queryArray1 = array("code" => 0, "query" => $query, "message" => "Succesful");
        } catch (Exception $e)  {
            die(json_encode(array("code" => 1, "query" => $query, "message" => $e->getMessage())));
        }
        break;
}
//Now working -- end
$conn = null; //You need to close connection prior configuring system-logs
if (is_null($queryArray1)) die(json_encode(array('code' => 6, 'query' => $query, 'message' => 'Could not get data from the database')));
if (intval($queryArray1['code']) !== 0) die(json_encode(array('code' => 7, 'query' => $query, 'message' => $queryArray1['message'])));
//Activate Logs
//Working with Logs
try {
    if (isset($_POST['__log_message__'])) $logMessage = $_POST['__log_message__'];
    SystemLogs::addLog($config1, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage);
} catch (Exception $e) {
    $message = "System Logs Failed to register : [ " . $e->getMessage() . " ]";
    die(json_encode(array("code" => 5, "query" => $query, "message" => $message)));
}
echo json_encode(array('code' => 0, 'query' => $query, 'message' => $successfulMessage));
