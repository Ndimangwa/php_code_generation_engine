<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    MEDICAL DOCTOR CONSULTATION (APPROVE RESULTS)
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=medicaldoctor_consult";
                    $erollback = false;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $queue1 = new PatientExaminationQueue("Delta", $_REQUEST['qid'], $conn);
                        $service1 = new Service("Delta", $_REQUEST['id'], $conn);
                        $medicalDoctorConsultationQueue1 = $queue1->getMedicalDoctorConsultationQueue();
                        if (! is_null($medicalDoctorConsultationQueue1)) {
                            $nextPage .= "&qid=".($medicalDoctorConsultationQueue1->getQueueId());
                        }
                        $conn->beginTransaction();
                        $erollback = true;
                        $listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $queue1->getQueueId(), $service1->getServiceId(), false);
                        if (is_null($listOfResults)) throw new Exception("Could not get any of un-approved results");
                        foreach ($listOfResults as $results1)   {
                            $results1->setApproved(true)->update(! $erollback);
                        }
                        //Fetch Again to see if Completed
                        $listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $queue1->getQueueId(), $service1->getServiceId(), false);
                        if (is_null($listOfResults)) {
                            $medicalDoctorConsultationQueue1->setOnExaminationResultsVerification(false)->update(! $erollback);
                        }
                        $conn->commit();
                        $erollback = false;
                        echo UICardView::getSuccesfulReportCard("Approve Results", "You have successful Approved Results");
                    } catch (Exception $e) {
                        if ($erollback) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Medical Consultation</a></i><br />
                        <span class="text-muted"><i>Rules: [ medicaldoctor_consult, medicaldoctor_approve_results]</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>