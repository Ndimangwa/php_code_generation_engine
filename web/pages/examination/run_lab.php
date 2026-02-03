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
                    $nextPage = $thispage /*. "?page=$page"*/;
                    try {
                        if (is_null($examinationBlock1)) throw new Exception("Could not decode examination type");
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT n.queueId as queueId, n.timeOfUpdation as timeOfUpdation, serviceName, registrationNumber, surname, otherNames, visitCount FROM $tablename as n, _patient_examination_queue as q, _patient as p, _patientVisit as v, _patientCase as c, _service as s WHERE (n.examinationQueueId = q.queueId) AND (n.serviceId = s.serviceId) AND (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (q.pendingPayment = 0)";
                        $caption = "Examine";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "queueId",
                                "caption" => $caption,
                                "href" => $thispage."?page=$page&qtype=$qtype&qid=",
                                "appendId" => true)
                            ), array(
                                "timeOfUpdation" => array("caption" => "Time"),
                                "registrationNumber" => array("caption" => "Reg No"),
                                "surname" => array("caption" => "Surname"),
                                "otherNames" => array("caption" => "Given Names"),
                                "visitCount" => array("caption" => "visit")
                        ), array('queueId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
                            switch ($colname)   {
                                case "serviceName":
                                    $colval = "<span style=\"font-size: 0.8em;\"><i><b>$colval</b></i></span>";
                                    break;
                                case "timeOfCreation":
                                case "timeOfUpdation":
                                    try {
                                        $t1 = new DateAndTime($colval);
                                        $colval = $t1->getDateAndTimeString();
                                    } catch (Exception $e)  {}
                                    break;
                            }
                            return $colval;
                        });
                        echo $windowToDisplay1;
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Dashboard</a></i><br />
                        <span class="text-muted"><i>Rule: <?= $cname ?></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
