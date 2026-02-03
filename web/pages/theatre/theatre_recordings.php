<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    RECORDINGS
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=theatre_read_list";
    $erollback = false;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $patientOperationQueue1 = new PatientOperationQueue("Delta", $_REQUEST['qid'], $conn);
        $service1 = new Service("Delta", $_REQUEST['serviceId'], $conn);
        $listOfPatientOperations = PatientOperation::filterRecords($conn, array(
            'operationQueue' => ( $patientOperationQueue1->getQueueId() ),
            'service' => ( $service1->getServiceId() )
        ));
        if (! is_null($listOfPatientOperations) && ( sizeof($listOfPatientOperations) > 1 )) throw new Exception("Can have zero or only operation for the service");
        $patientOperation1 = is_null($listOfPatientOperations) ? null : $listOfPatientOperations[0];
        $patientName = $patientOperationQueue1->getPatient()->getPatientName();
        $serviceName = $service1->getServiceName();
        echo "<h5 class=\"text-center bg-secondary\">Name : <span>$patientName</span>; Service : <span>$serviceName</span></h5>";
        if (isset($_POST['submit']) && isset($_POST['efilter']))    {
            if ($_POST['efilter'] != $patientOperationQueue1->getFirstFilter()) throw new Exception("Perhaps the page has replayed");
            $patientOperationQueue1->setFirstFilter(__object__::getMD5CodedString("Hamnazo Akili", 32))->update(! $erollback);
            $conn->beginTransaction();
            $erollback = true;
            //Begin Update
            $colArray1 = $patientOperationQueue1->getMyPayload(array('visit', 'patientCase', 'patient', 'bundleCode'));
            $colArray1 = array_merge($colArray1, array(
                'timeOfCreation' => ( $systemTime1->getTimestamp() ),
                'timeOfUpdation' => ( $systemTime1->getTimestamp() ),
                'operationQueue' => ( $patientOperationQueue1->getQueueId() ),
                'service' => ( $service1->getServiceId() ),
                'attendedBy' => ( $login1->getLoginId() )
            ));
            include("theatre_recordings_update.php");
            //End Update
            $conn->commit();
            $erollback = false;
            //Assume everything went well
            echo UICardView::getSuccesfulReportCard("Theatre Recordings", "You have successful updated the theatre data");
        }  else {
            $patientOperationQueue1->setFirstFilter(__object__::getMD5CodedString("Patient Operation Queue", 32))->update(! $erollback);
            include("theatre_recordings_ui.php");
        }
    } catch (Exception $e)  {
        if ($erollback) $conn->rollBack();
        echo UICardView::getDangerReportCard("Theatre Recordings", $e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to List</a></i><br/>
                        <span class="text-muted"><i>Rule: theatre_read_list</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>