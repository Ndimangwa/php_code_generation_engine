<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    MEDICAL DOCTOR CONSULTATION (REPEAT EXAMINATION)
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
                        //Working with Results Steps 1 & 2 
                        $listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $queue1->getQueueId(), $service1->getServiceId(), false);
                        if (is_null($listOfResults)) throw new Exception("We do not have the corresponding results");
                        foreach ($listOfResults as $results1)   {
                            //Step 1: Remove from PatientFile
                            $listOfFiles = PatientFile::getListOfFiles($conn, $results1->getMyClassname(), $results1->getResultsId());
                            //I expect one entry but anyway just delete all 
                            if (! is_null($listOfFiles))    {
                                foreach ($listOfFiles as $patientFile1) {
                                    $patientFile1->delete(! $erollback);
                                }
                            }
                            //Step 2: Remove the file itself
                            $results1->delete(! $erollback);
                        }
                        //Step 3: remove it-service in a list of Attended in examinationQueue
                        $listOfAttendedServices = $queue1->getListOfAttendedServices();
                        if (! is_null($listOfAttendedServices)) {
                            //I expect not to be null 
                            $newListOfAttendedServices = array();
                            foreach ($listOfAttendedServices as $tservice1) {
                                if (($tservice1->getServiceId()) != ($service1->getServiceId()))    {
                                    $newListOfAttendedServices[sizeof($newListOfAttendedServices)] = $tservice1;
                                }
                            }
                            $queue1->setListOfAttendedServices(__data__::convertListObjectsToCommaSeparatedValues($newListOfAttendedServices));
                        }
                        //Step 4: Set completed = false,
                        $queue1->setCompleted(false)->setRequestedBy($login1->getLoginId())->update(! $erollback);
                        //Step 5: MedicalExaminationQue set onMedicalExamination
                        if (! is_null($medicalDoctorConsultationQueue1))    {
                            $medicalDoctorConsultationQueue1->setOnMedicalExamination(true)->update(! $erollback);
                        }
                        //Now you need to populate the service on the corresponding notification-queue
                        $colArray1 = array(
                            "timeOfCreation" => $systemTime1->getTimestamp(),
                            "timeOfUpdation" => $systemTime1->getTimestamp(),
                            "examinationQueue" => $queue1->getQueueId(),
                            "service" => $service1->getServiceId() 
                         );
                         switch ($service1->getCategory()->getCategoryId())  {
                             case (ServiceCategory::$__LABORATORY_EXAMINATION):
                                 __data__::insert($conn, "QueueNotifyWetLab", $colArray1, ! $erollback);
                                 break;
                             case (ServiceCategory::$__ULTRA_SOUND):
                                 __data__::insert($conn, "QueueNotifyUltrasound", $colArray1, ! $erollback);
                                 break;
                             case (ServiceCategory::$__PLAIN_CONVENTION_X_RAY):
                                 __data__::insert($conn, "QueueNotifyPlainXRAY", $colArray1, ! $erollback);
                                 break;
                             case (ServiceCategory::$__CONTRAST_STUDIES):
                                 break;
                         }
                        $conn->commit();
                        $erollback = false;
                        echo UICardView::getSuccesfulReportCard("Repeat Examination", "Your requested to re-examination has been granted");
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
                        <span class="text-muted"><i>Rules: [ medicaldoctor_consult, medicaldoctor_repeat_examination]</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>