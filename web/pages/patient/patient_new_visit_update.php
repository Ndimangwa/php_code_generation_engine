<?php
$flag = intval($_REQUEST['flag']);
$caption = "NEW CASE / ANOTHER VISIT";
if ($flag == Patient::$__NEW_VISIT) $caption = "COMING FOR ANOTHER VISIT";
if ($flag == Patient::$__NEW_CASE) $caption = "OPENING A NEW CASE";
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <?= $caption ?>
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $enableRollBack = false;
                    $nextPage = $thispage . "?page=patient";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $patient1 = new Patient("PC", $_REQUEST['id'], $conn);
                        $case1 = new PatientCase("PC", $patient1->getCurrentCase(), $conn);
                        $visit1 = new PatientVisit("PC", $case1->getCurrentVisit(), $conn);
                        $tflag = ((new PatientCase("PC", $patient1->getCurrentCase(), $conn))->isClosed()) ? Patient::$__NEW_CASE : Patient::$__NEW_VISIT;
                        if ($tflag != $flag) throw new Exception("URL Compromised has detected");
                        //We need to Check for Pending Balance 
                    ?>
                        <div class="mb-2 text-center">Patient: <?= $patient1->getPatientName() ?></div>
                        <?php
                        if (isset($_POST['submit_data'])) {
                            $bundleCode = __object__::getMD5CodedString("patient_new_visit", 32);
                            $conn->beginTransaction();
                            $enableRollBack = true;
                            if ($_POST['efilter'] != $patient1->getExtraFilter()) throw new Exception("This page can not be submitted more than once");
                            $patient1->setExtraFilter(__object__::getCodeString(32))->update(! $enableRollBack);
                            $colArray1 = $_POST;
                            $colArray1['timeOfCreation'] = $systemTime1->getTimestamp();
                            $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
                            $colArray1['currentStage'] = PatientMovementStage::$__CONTINUING_VISIT;
                            $colArray1['nextStage'] = PatientMovementStage::$__TRIAGE;
                            //You need to settle for nextStage
                            if ($flag == Patient::$__NEW_VISIT) {
                                $colArray1['patientCase'] = $case1->getCaseId();
                                $colArray1['visitCount'] = $visit1->getVisitCount() + 1;
                                $colArray1['insured'] = isset($_POST['insurance']) ? 1 : 0;
                                $visit1 = new PatientVisit("Data", __data__::insert($conn, "PatientVisit", $colArray1,! $enableRollBack, Constant::$default_select_empty_value), $conn);
                                $case1->setCurrentStage(PatientMovementStage::$__CONTINUING_VISIT)->setNextStage(PatientMovementStage::$__TRIAGE)->setCurrentVisit($visit1->getVisitId())->update(! $enableRollBack);
                            } else if ($flag == Patient::$__NEW_CASE) {
                                $colArray1['patient'] = $patient1->getPatientId();
                                $colArray1['insured'] = isset($_POST['insurance']) ? 1 : 0;
                                $colArray1['caseType'] = PatientCaseType::$__OPEN;
                                $colArray1['currentVisit'] = 0;
                                $colArray1['visitCount'] = 1;
                                $colArray1['closed'] = 0;
                                $case1 = new PatientCase("Delta", __data__::insert($conn, "PatientCase", $colArray1, ! $enableRollBack, Constant::$default_select_empty_value), $conn);
                                $patient1->setCurrentCase($case1->getCaseId())->update(! $enableRollBack);
                                $colArray1['patientCase'] = $case1->getCaseId();
                                $visit1 = new PatientVisit("Delta", __data__::insert($conn, "PatientVisit", $colArray1, ! $enableRollBack, Constant::$default_select_empty_value), $conn);
                                $case1->setCurrentVisit($visit1->getVisitId())->update(! $enableRollBack);
                            } else throw new Exception("Could not Decode the Operation");
                            $visit1->setTemporaryStringHolder(__object__::getMD5CodedString("ContinuingVisit"))->update(! $enableRollBack);
                            //We need to continue for setting financial queue , check Doctor Consultation 

                            $listOfServices = array();
                            if (isset($_POST['medicalDoctor']) && ($_POST['medicalDoctor'] != Constant::$default_select_empty_value)) {
                                $medicalDoctor1 = new MedicalDoctor("Delta", $_POST['medicalDoctor'], $conn);
                                $consultationServiceId = Service::$__NON_SPECIALIST_CONSULTATION;
                                if ($medicalDoctor1->isSpecialist()) $consultationServiceId = Service::$__SPECIALIST_CONSULTATION;
                                $listOfServices[sizeof($listOfServices)] = $consultationServiceId;
                            }
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
                                "actionStage" => (PatientMovementStage::$__CONTINUING_VISIT),
                                "temporaryObjectHolder" => $patient1->getObjectReferenceString(),
                                "bundleCode" => $bundleCode
                            ), ! $enableRollBack);
                            //Add ContinuingVisitLog
                            PatientFile::addContinuingVisitLog($conn, $systemTime1, $visit1, $login1, $visit1->getTemporaryStringHolder(), ! $enableRollBack);
                            $conn->commit();
                            $enableRollBack = false;
                            //and Success Window, do not forget 
                            //General Log
                            $caption = $caption . "[ " . $patient1->getPatientName() . " ]";
                            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $page, $caption);
                            //Successful Window
                            echo UICardView::getSuccesfulReportCard($caption, "You have successful Update the Patient Data");
                        } else {
                            $totalBalanceArray1 = $patient1->getTotalBalance();
                            if (!is_null($totalBalanceArray1)) {
                        ?>
                                <div>
                                    <div class="card">
                                        <div class="card-header bg-warning">Pending Balance</div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col"></th>
                                                        <th>Currency</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $count = 0;
                                                    foreach ($totalBalanceArray1 as $currencyId => $totalBalance) {
                                                        $currency1 = new Currency("Delta", $currencyId, $conn);
                                                        $count++;
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?= $count ?></th>
                                                            <td><?= $currency1->getCode() ?></td>
                                                            <td><?= $totalBalance ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer text-right"><a class="card-link" href="<?= $thispage ?>?page=patientreceipt_create">Clear Pending Balance</a></div>
                                    </div>
                                </div>

                            <?php
                            } //end -- of balance
                            //Loading Second Screen
                            ?>
                            <div class="mt-2">
                                <?php
                                //Pre-populate Data
                                $medicalDoctor1 = is_null($visit1->getMedicalDoctor()) ? (is_null($case1->getMedicalDoctor()) ? $patient1->getMedicalDoctor() : $case1->getMedicalDoctor()) : $visit1->getMedicalDoctor();
                                $insurance1 = is_null($visit1->getInsurance()) ? $case1->getInsurance() : $visit1->getInsurance();
                                $insuranceStatus = is_null($insurance1) ? false : true;
                                $patient1->setExtraFilter(__object__::getCodeString(32))->update(true);
                                $specialist = __data__::$__TRUE;
                                switch ($patient1->getRegistrationType()->getTypeId())  {
                                    case PatientRegistrationType::$__PATIENT_TRANSFER_IN: 
                                    case PatientRegistrationType::$__PATIENT_FULL_REGISTRATION: 
                                        $specialist = 99;
                                        break;
                                    case PatientRegistrationType::$__PATIENT_MIN_REGISTRATION: 
                                        $specialist = __data__::$__TRUE;
                                        break;
                                }
                                $formToDisplay = __data__::createDataCaptureForm($thispage, "Patient", array(
                                array('pname' => 'medicalDoctor', 'caption' => 'Consultant', 'required' => false, 'value' => $medicalDoctor1/*, 'filter-op' => 'not', 'filter' => array('specialist' => array($specialist))*/),
                                    array('pname' => 'insurance', 'type' => 'switch-select', 'caption' => 'Medical Insurance', 'required' => true, 'use-class' => 'PatientCase', 'title' => 'If you have a Medical Insurance Select', 'disabled' => true, 'placeholder' => 'Medical Insurance', 'value' => $insurance1, 'disabled' => !$insuranceStatus, 'checked' => $insuranceStatus),
                                    array('pname' => 'comments', 'caption' => 'Comments', 'required' => false)
                                ), "Update Patient", "create", $conn, 0, array(
                                    "page" => $page,
                                    "new_visit" => $_REQUEST['new_visit'],
                                    "id" => $patient1->getPatientId(),
                                    "efilter" => $patient1->getExtraFilter(),
                                    "flag" => $flag,
                                    "submit_data" => 1
                                ), "patient_update", null, "mode-init-ndimangwa", $thispage, true);
                                echo UIView::wrap($formToDisplay, "mt-2");
                                ?>
                            </div>
                    <?php
                        }
                    } catch (Exception $e) {
                        if ($enableRollBack) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Patient</a></i><br />
                        <span class="text-muted"><i>Rule: patient_update</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
