
    <div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    List Operated
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage /*. "?page=$page"*/;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT o.operationId as operationId, o.timeOfUpdation as timeOfUpdation, registrationNumber, surname, otherNames, visitCount FROM _patient_operation as o, _patient as p, _patientVisit as v, _patientCase as c, _patient_admission_queue as qa WHERE (o.visitId = v.visitId) AND (o.caseId = c.caseId) AND (o.patientId = p.patientId) AND (o.admissionQueue = qa.queueId) AND (qa.completed = 1) AND (o.completed = 0)";
                        $caption = "View Details";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "operationId",
                                "caption" => $caption,
                                "href" => $thispage."?page=$page&id=",
                                "appendId" => true)
                            ), array(
                                "timeOfCreation" => array("caption" => "Admitted Since"),
                                "registrationNumber" => array("caption" => "Reg No"),
                                "surname" => array("caption" => "Surname"),
                                "otherNames" => array("caption" => "Given Names"),
                                "visitCount" => array("caption" => "visit")
                        ), array('operationId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
                            switch ($colname)   {
                                case "timeOfCreation":
                                case "timeOfUpdation":
                                    $dt1 = new DateAndTime($colval);
                                    $colval = $dt1->getDateAndTimeString();
                                    $colval = "<span style=\"font-size: 0.9em;\"><i><b>$colval</b></i></span>";
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
                        <span class="text-muted"><i>Rule: theatre_read_ready</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
