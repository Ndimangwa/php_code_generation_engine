<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Patient Admission (View Details)
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=$page";
                    $erollback = false;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $patientAdmission1 = new PatientAdmission("Delta", $_REQUEST['id'], $conn);
                        if ($patientAdmission1->isCompleted()) throw new Exception("Patient is no longer available in admission list");
                        echo __data__::createDetailsPage($thispage, "PatientAdmission", array(
                            "timeOfCreation", "timeOfUpdation", "patient", "patientCase", "visit", "listOfServices", "numberOfDays", "medicalComment", "discharged", "completed"
                        ), $conn, $patientAdmission1->getAdmissionId());        
                    } catch (Exception $e) {
                        if ($erollback) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to List</a></i><br />
                        <span class="text-muted"><i>Rule: admission_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>