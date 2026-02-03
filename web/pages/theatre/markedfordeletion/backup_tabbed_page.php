<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Admission" . ($systemTime1->getTimestamp()), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (Theatre::$__TAB_PATIENT_DATA))) {
    $conn->beginTransaction();
    $erollback = true;
   
    $conn->commit();
    $erollback = false;
} else {
    
}
?>