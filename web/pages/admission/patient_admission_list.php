
    <div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Patient Admission (View)
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage /*. "?page=$page"*/;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT a.admissionId as admissionId, a.timeOfCreation as timeOfCreation, registrationNumber, surname, otherNames, visitCount FROM _patient_admission as a, _patient as p, _patientVisit as v, _patientCase as c WHERE (a.visitId = v.visitId) AND (a.caseId = c.caseId) AND (a.patientId = p.patientId) AND (a.completed = 0)";
                        $caption = "View Details";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "admissionId",
                                "caption" => $caption,
                                "href" => $thispage."?page=$page&id=",
                                "appendId" => true)
                            ), array(
                                "timeOfCreation" => array("caption" => "Admitted Since"),
                                "registrationNumber" => array("caption" => "Reg No"),
                                "surname" => array("caption" => "Surname"),
                                "otherNames" => array("caption" => "Given Names"),
                                "visitCount" => array("caption" => "visit")
                        ), array('admissionId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
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
                        <span class="text-muted"><i>Rule: admission_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
