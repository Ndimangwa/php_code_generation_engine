<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    RECORDS NURSE STATION ACTIVITIES
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=nursestation";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $queue1 = new NurseStationQueue("delta", $_REQUEST['qid'], $conn);
        $erollback = false;
        if (isset($_POST['submit']) && isset($_POST['efilter']))    {
            if (! ($_POST['efilter'] == $queue1->getExtraFilter())) throw new Exception("Browser display detected, or multiple entry in the same queue, kindly redo");
            $conn->beginTransaction();
            $erollback = true;
            $queue1->setExtraFilter(__object__::getMD5CodedString("NurseStationActivity", 32))->update(! $erollback);
            if ($queue1->isCompleted()) throw new Exception("You have completed all required sequences");
            //Perform your work here
            $currentSequenceNumber = $queue1->getCurrentAttendanceSequence() + 1;
            $colArray1 = array_merge($_POST, $queue1->getMyPayload(array(
                'service', 'visit', 'patientCase', 'patient', 'bundleCode'
            )) ,array(
                'timeOfCreation' => $systemTime1->getTimestamp(),
                'timeOfUpdation' => $systemTime1->getTimestamp(),
                'attendedBy' => $login1->getLoginId(),
                'sequenceNumber' => $currentSequenceNumber
            ));
            $nurseStationActivity1 = new NurseStationActivity("Delta", __data__::insert($conn, "NurseStationActivity", $colArray1, ! $erollback, Constant::$default_select_empty_value), $conn);
            //Now you need to update the queue According 
            $queue1->setCurrentAttendanceSequence($currentSequenceNumber);
            if ($queue1->getCurrentAttendanceSequence() == $queue1->getMaximumNumberOfAttendance()) {
                $queue1->setCompleted(true);
            }
            $queue1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
            if (is_null($colArray1['resultsText'])) $colArray1['resultsText'];
            //My Work has ended --- we need to update PatientFile file 
            PatientFile::addNurseStationActivityLog($conn, $systemTime1, $nurseStationActivity1->getVisit(), $login1, $nurseStationActivity1, $nurseStationActivity1->getBundleCode(), ! $erollback);

            $conn->commit();
            $erollback = false;
            echo UICardView::getSuccesfulReportCard("Recorded Patient Data", "You have successful recorded patient data");
        } else {
            //Record
            $queue1->setExtraFilter(__object__::getMD5CodedString("NurseStationActivity", 32))->update(! $erollback);
            echo __data__::createDataCaptureForm($thispage, "NurseStationActivity", array(
                array('pname' => 'resultsText', 'caption' => 'Activity', 'required' => false, 'placeholder' => 'Changed Bandage'),
                array('pname' => 'progress', 'caption' => 'Progress', 'required' => true),
                array('pname' => 'requireMedicalAttention', 'caption' => 'Need Medical Attention', 'required' => true),
                array('pname' => 'comments', 'caption' => 'Any Comment', 'required' => false)
            ), "Record Data", "create", $conn, 0, array(
                'page' => $page,
                'qid' => $queue1->getQueueId(),
                'submit' => 1,
                'efilter' => ($queue1->getExtraFilter())
            ), null, null, "record-data", $thispage, true);
        }
    } catch (Exception $e)  {
        if ($erollback) $conn->rollBack();
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Nurse Station</a></i><br/>
                        <span class="text-muted"><i>Rule: nursestation_record</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>