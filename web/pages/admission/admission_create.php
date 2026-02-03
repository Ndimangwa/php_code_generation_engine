<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Patient Admission
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=$page";
                    $erollback = false;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $queue1 = new PatientAdmissionQueue("Delta", $_REQUEST['qid'], $conn);
                        if ($queue1->isCompleted()) throw new Exception("This Queue is Already Completed!!!");
                        if (isset($_POST['submit'])) {
                            $conn->beginTransaction();
                            $erollback = true;
                            if ($_POST['efilter'] != $queue1->getExtraFilter()) throw new Exception("Page Multiple Submission has been detected");
                            $queue1->setExtraFilter(__object__::getMD5CodedString("Hello World"))->update(!$erollback);
                            //We need to check bed if occupied
                            $bed1 = new Bed("Delta", $_POST['bed'], $conn);
                            if ($bed1->isOccupied()) throw new Exception("Bed is already occupied");
                            //Step 1: Merge payload 
                            $colArray1 = array_merge($queue1->getMyPayload(array("patient", "patientCase", "visit", "bundleCode")), array(
                                "timeOfCreation" => ( $systemTime1->getTimestamp() ),
                                "timeOfUpdation" => ( $systemTime1->getTimestamp() )
                            ));
                            //Step 2: Insert into PatientAdmission
                            $patientAdmission1 = new PatientAdmission("Delta", __data__::insert($conn, "PatientAdmission", array_merge($colArray1, array(
                                "admissionQueue" => ( $queue1->getQueueId() ),
                                "bed" => ( $bed1->getBedId() ),
                                "numberOfDays" => ( $queue1->getNumberOfDays() ),
                                "listOfServices" => ( __data__::convertListObjectsToCommaSeparatedValues($queue1->getListOfServices()) ),
                                "discharged" => 0,
                                "completed" => 0
                            )), ! $erollback), $conn);
                            //Step 3: Update PatientFile
                            PatientFile::addPatientAdmissionBeingAdmittedLog($conn, $systemTime1, $patientAdmission1->getVisit(), $login1, $patientAdmission1, $patientAdmission1->getBundleCode(), ! $erollback);
                            //Step 4: Queue Completed and reset/set corresponding flags 
                            $queue1->setCompleted(true)->update(! $erollback);
                            $bed1->setOccupied(true)->update(! $erollback);
                            $patientAdmission1->getPatient()->setAdmitted(true)->setAdmissionReference($patientAdmission1->getObjectReferenceString())->update(! $erollback);
                            $conn->commit();
                            $erollback = false;
                            //Step 5: Successful report
                            echo UICardView::getSuccesfulReportCard("Admission Report", "You have successful Admitted patient");
                        } else {
                            $queue1->setExtraFilter(__object__::getMD5CodedString("Hello World"))->update(!$erollback);
                            //Now Display
                            //We need to exclude all beds occupied
                            $listOfOccupiedBeds = explode(",", __data__::convertListObjectsToCommaSeparatedValues(Bed::getListOfOccupiedBeds($conn)));
                            echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientAdmission", array(
                                array('pname' => 'ward', 'caption' => 'Ward', 'required' => true, 'use-class' => 'Room', 'format' => 'Ward No : .{ wardNumber }.'),
                                array('pname' => 'room', 'caption' => 'Room', 'required' => true, 'use-class' => 'Bed', 'format' => 'Room No : .{ roomNumber }. , observation .{ observation }. , private .{ private }.', 'cascade' => array('parent' => 'ward')),
                                array('pname' => 'bed', 'caption' => 'Bed', 'required' => true, 'use-class' => 'PatientAdmission', 'filter-op' => 'not', 'filter' => array('bed' => $listOfOccupiedBeds), 'format' => 'Bed No : .{ bedNumber }.', 'cascade' => array('parent' => 'room'))
                            ), "Admit Patient", "create", $conn, 0, array(
                                "page" => $page,
                                "qid" => ($queue1->getQueueId()),
                                "submit" => true,
                                "efilter" => ($queue1->getExtraFilter())
                            ), null, null, "me-delta", $thispage, true));
                        }
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
                        <span class="text-muted"><i>Rule: admission_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>