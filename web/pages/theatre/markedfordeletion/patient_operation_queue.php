
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Operation Waiting List
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage /*. "?page=$page"*/;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT q.queueId as queueId, q.timeOfUpdation as timeOfUpdation, registrationNumber, surname, otherNames, visitCount FROM _patient_operation_queue as q, _patient as p, _patientVisit as v, _patientCase as c, _patient_admission_queue as qa WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (q.admissionQueue = qa.queueId) AND (qa.completed = 1) AND (q.pendingPayment = 0) AND (q.completed = 0)";
                        $caption = "Details & Recordings";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "queueId",
                                "caption" => $caption,
                                "href" => $thispage."?page=$page&qid=",
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
                                    $dt1 = new DateAndTime($colval);
                                    $colval = $dt1->getGUIDateOnlyFormat();
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
                        <span class="text-muted"><i>Rule: theatre_read_waiting</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
