<?php
$typeOfExaminationArray1 = array(
    (PatientExaminationQueue::$__WET_LAB) => array(
        "header" => "CHEMISTRY (WET) LABORATORY",
        "tablename" => "_queue_notify_wet_lab",
        "classname" => "QueueNotifyWetLab",
        "cname" => "examination_wetlab"
    ),
    (PatientExaminationQueue::$__ULTRASOUND) => array(
        "header" => "ULTRASOUND EXAMINATION",
        "tablename" => "_queue_notify_ultrasound",
        "classname" => "QueueNotifyUltrasound",
        "cname" => "examination_ultrasound"
    ),
    (PatientExaminationQueue::$__PLAIN_XRAY) => array(
        "header" => "XRAY EXAMINATION",
        "tablename" => "_queue_notify_plain_xray",
        "classname" => "QueueNotifyPlainXRAY",
        "cname" => "examination_xray_plain"
    )
);
$examinationBlock1 = isset($typeOfExaminationArray1[$_REQUEST['qtype']]) ? $typeOfExaminationArray1[$_REQUEST['qtype']] : null;
$header = is_null($examinationBlock1) ? "UNKNOWN EXAMINATION" : $examinationBlock1['header'];
$tablename = is_null($examinationBlock1) ? null : $examinationBlock1['tablename'];
$classname = is_null($examinationBlock1) ? null : $examinationBlock1['classname'];
$cname = is_null($examinationBlock1) ? null : $examinationBlock1['cname'];
$qtype = $_REQUEST['qtype'];
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <?= $header ?>
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=$page";
                    $enableRollBack = false;
                    try {
                        if (is_null($examinationBlock1)) throw new Exception("Could not decode examination type");
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $queue1 = Registry::getObjectReference("Hello", $conn, $classname, $_REQUEST['qid']);
                        $examinationQueue1 = $queue1->getExaminationQueue();
                        if (is_null($queue1)) throw new Exception("[ $classname ] : Could not get Object Reference");
                        if (isset($_POST['submit'])) {
                            //Now make sure we have standard 
                            if (! isset($_POST['standard']) && isset($_FILES['standard']))    {
                                $_POST['standard'] = $_FILES['standard']['name'];
                            }
                            if (! isset($_POST['standard'])) throw new Exception("Standard Reference were not set");
                           /*var_dump($_POST);

                            throw new Exception("Could not upload results");*/
                            $conn->beginTransaction();
                            $enableRollBack = true;
                            //Now build Comment if any -- same comment will be referenced by Multiple Standards
                            $comment1 = null;
                            if (__data__::isNotEmpty($_POST['technicalComments']))  {
                                $comment1 = new MedicalComment("Delta", __data__::insert($conn, "MedicalComment", array_merge($examinationQueue1->getMyPayload(array("patientCase", "visit", "patient", "bundleCode")), array(
                                    "timeOfCreation" => $systemTime1->getTimestamp(),
                                    "timeOfUpdation" => $systemTime1->getTimestamp(),
                                    "comments" => $_POST['technicalComments']
                                )), ! $enableRollBack), $conn);
                            }
                            //Step 1: Populate Examination Result
                            foreach ($_POST['standard'] as $standardId => $value) {
                                $standard1 = new PatientExaminationStandards("Delta", $standardId, $conn);
                                $colArray1 = array(
                                    'timeOfCreation' => $systemTime1->getTimestamp(),
                                    'timeOfUpdation' => $systemTime1->getTimestamp(),
                                    'examinationQueue' => $queue1->getExaminationQueue()->getQueueId(),
                                    'examinationStandard' => $standard1->getStandardId(),
                                    'service' => $standard1->getService()->getServiceId(),
                                    'typeOfValue' => $standard1->getTypeOfValue(),
                                    'generalValue' => $value,
                                    'safeValue' => (PatientExaminationStandards::isValueSafe($standard1, $value) ? 1 : 0)
                                );
                                switch ($standard1->getTypeOfValue()) {
                                    case "float":
                                        $colArray1['floatValue'] = $value;
                                        break;
                                    case "integer":
                                        $colArray1['integerValue'] = $value;
                                        break;
                                    case "text":
                                        $colArray1['textValue'] = $value;
                                        break;
                                    case "file":
                                        //You need to correct value 
                                        $valueArray1 = array();
                                        foreach (array("name", "error", "size", "tmp_name", "type") as $opt)  {
                                            if (isset($_FILES['standard']) && isset($_FILES['standard'][$opt]) && isset($_FILES['standard'][$opt][$standardId]))    {
                                                $valueArray1[$opt] = $_FILES['standard'][$opt][$standardId];
                                            }
                                        }
                                        //Preparing base Folder 
                                        $baseFolder = join(DIRECTORY_SEPARATOR, [$profile1->getExternalBaseFolder(), "data", "files"]);
                                        $baseFolder = str_replace(( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR ), DIRECTORY_SEPARATOR, $baseFolder);
                                        $filename = $valueArray1['name'];
                                        //Saving to file-system
                                        $promise1 = SystemFiles::saveUploadedFile($valueArray1, $baseFolder, $filename);
                                        if (! $promise1->isPromising()) throw new Exception($promise1->getReason());
                                        //Now getting SystemFiles references 
                                        $colArray1['filename'] = __data__::insert($conn, "SystemFiles", array(
                                            "timeOfCreation" => ( $systemTime1->getTimestamp() ),
                                            "timeOfUpdation" => ( $systemTime1->getTimestamp() ),
                                            "type" => $valueArray1['type'],
                                            "size" => $valueArray1['size'],
                                            "filename" => $filename
                                        ), ! $enableRollBack);
                                        //GeneralValue should also point to file reference
                                        $colArray1['generalValue'] = $colArray1['filename'];
                                        break;
                                }
                                //Append technicalComments
                                $colArray1 = is_null($comment1) ? $colArray1 : array_merge($colArray1, array("technicalComments" => ( $comment1->getCommentId() )));
                                $patientExaminationResults1 = new PatientExaminationResults("Omi-Cron", __data__::insert($conn, "PatientExaminationResults", $colArray1, ! $enableRollBack, Constant::$default_select_empty_value), $conn);
                                PatientFile::addExaminationResultsLog($conn, $systemTime1, $examinationQueue1->getVisit(), $login1, $patientExaminationResults1, $examinationQueue1->getBundleCode(), ! $enableRollBack);
                            }
                            //Step 2: Add to Attendant List
                            $t1 = $examinationQueue1->getListOfAttendedServices();
                            $t1 = is_null($t1) ? array() : explode(",", __data__::convertListObjectsToCommaSeparatedValues($t1));
                            $serviceId = $standard1->getService()->getServiceId();
                            if (!in_array($serviceId, $t1)) $t1[sizeof($t1)] = $serviceId;
                            $examinationQueue1->setListOfAttendedServices(implode(",", $t1));
                            //Step 3: Delete this notification
                            $queue1->delete(! $enableRollBack);
                            //Step 4: Check if Queue is Completed 
                            if ($examinationQueue1->isQueueCompleted()) {
                                $examinationQueue1->setCompleted(true);
                            }
                            //Step 2 and 3 update
                            $examinationQueue1->update(! $enableRollBack);
                            //Step 5: Check MedicalDoctorExaminationQueue pendingPayment=0 and onMedicalExamination=0 (if Completed)
                            $medicalDoctorQueue1 = Registry::getInstance("OmniCrom", $conn, $examinationQueue1->getTemporaryObjectHolder());
                            if ($examinationQueue1->isCompleted()) {
                                $medicalDoctorQueue1->resetFlagAt(MedicalDoctorConsultationQueue::$__FLAG_LABORATORY_EXAMINATION_PENDING_PAYMENT)->setOnMedicalExamination("0")->setOnExaminationResultsVerification("1")->update(! $enableRollBack);
                                //Update PageMovementStage --- his time we do not have any movement stage to update
                                //$examinationQueue1->getPatientCase()->update(! $enableRollBack);
                            }
                            $conn->commit();
                            $enableRollBack = false;
                            echo UICardView::getSuccesfulReportCard("Laboratory Report", "You have successful saved the patient's laboratory results");
                        } else {
                            //Normal Page -- Need UI which will handle all type of examination, check standards table
                            echo PatientExaminationStandards::getUIForm($conn, $profile1, $queue1, $thispage, Constant::$default_select_empty_value, "Save Patient Data", array(
                                "page" => $page,
                                "qid" => $queue1->getQueueId(),
                                "qtype" => $_REQUEST['qtype'],
                                "submit" => 1
                            ), null, array("image/*"));
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
                        <i><a href="<?= $nextPage ?>&qtype=<?= $qtype ?>" class="card-link">Back to List</a></i><br />
                        <span class="text-muted"><i>Rule: <?= $cname ?></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
