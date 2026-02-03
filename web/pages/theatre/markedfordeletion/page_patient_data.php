<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Patient Data" . ($systemTime1->getTimestamp()), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (Theatre::$__TAB_PATIENT_DATA))) {
    $conn->beginTransaction();
    $erollback = true;
   
    $conn->commit();
    $erollback = false;
} else {
    $admissionQueue1 = $queue1->getAdmissionQueue();//We need Admission
?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="4">Patient Summary</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Registration Number</td>
                    <td><?= $patient1->getRegistrationNumber() ?></td>
                    <td>Sex</td>
                    <td><?= $patient1->getSex()->getSexName() ?></td>
                </tr>
                <tr>
                    <td>Surname</td>
                    <td><?= $patient1->getSurname() ?></td>
                    <td>Weight</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Other Names</td>
                    <td><?= $patient1->getOtherNames() ?></td>
                    <td>Ward</td>
                    <td></td>
                </tr>
                <tr>
                    <td>D.O.B</td>
                    <td><?= $patient1->getDob()->getDateAndTimeString() ?></td>
                    <td>Blood Group</td>
                    <td><?=  is_null($patient1->getBloodGroup()) ? "---" : ( $patient1->getBloodGroup()->getCode() ) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php
}
?>