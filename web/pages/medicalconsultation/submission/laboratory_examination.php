<div>
    <?php
    $laboratoryExaminationColumn = "listOfExaminations";
    $laboratoryExaminationFieldArray1 = array($laboratoryExaminationColumn);
    $laboratoryExaminationLookupArray1 = array($laboratoryExaminationColumn => "listOfServices");
    $laboratoryExaminationNewAddedServices = null;
    $laboratoryExaminationSubmittedServices = (isset($_POST[$laboratoryExaminationColumn])) ? $_POST[$laboratoryExaminationColumn] : null;
    if (is_null($examinationQueue1)) {
        $dataArray1 = $colArray1;
        $enableUpdate = false;
        foreach ($laboratoryExaminationFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                if ($colname == $laboratoryExaminationColumn) {
                    //New One
                    $enableUpdate = true;
                    $examinationQueue1 = new PatientExaminationQueue("Delta", __data__::insert($conn, "PatientExaminationQueue", array_merge($colArray1, array(
                        "pendingPayment" => 1,
                        "completed" => 0,
                        "listOfServices" => (is_null($laboratoryExaminationSubmittedServices) ? "" : (implode(",", $laboratoryExaminationSubmittedServices))),
                        "temporaryObjectHolder" => ($consultationQueue1->getObjectReferenceString()),
                        "temporaryStringHolder" => ($consultationQueue1->getBundleCode()),
                        "bundleCode" => ($consultationQueue1->getBundleCode()),
                        "consultationQueue" => ($consultationQueue1->getQueueId()),
                        "requestedBy" => ( $login1->getLoginId() )
                    )), !$erollback, null), $conn);
                    $laboratoryExaminationNewAddedServices = $laboratoryExaminationSubmittedServices;
                }
            }
        }
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($laboratoryExaminationFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                if ($colname == $laboratoryExaminationColumn) {
                    //
                    //1. Existing Services
                    $existingServices = __data__::convertListObjectsToArray($examinationQueue1->getListOfServices());
                    //2. 
                    //We need to make sure all-existing list is in submitted list
                    if (!__data__::isArrayASubsetOfAnotherArray($existingServices, $laboratoryExaminationSubmittedServices)) throw new Exception("All services existed before must be present");
                    //3.
                    //Need to Update services and
                    $laboratoryExaminationNewAddedServices = __data__::substractArray($laboratoryExaminationSubmittedServices, $existingServices);
                    $laboratoryExaminationNewAddedServices = (sizeof($laboratoryExaminationNewAddedServices) == 0) ? null : $laboratoryExaminationNewAddedServices;
                    //4. Update if there is new service 
                    if (!is_null($laboratoryExaminationNewAddedServices)) {
                        //This is Where we need to update
                        if ($examinationQueue1->isPendingPayment()) throw new Exception("Can not Add New Examination while there is previous unpaid ones");
                        $examinationQueue1->setUpdateList(array(
                            "timeOfUpdation" => ($systemTime1->getTimestamp()),
                            "listOfServices" => implode(",", $laboratoryExaminationSubmittedServices),
                            "pendingPayment" => 1,
                            "completed" => 0,
                            "requestedBy" => ( $login1->getLoginId() )
                        ))->update(!$erollback);
                        //Need to reload since we have used updateList
                        $enableUpdate = true;
                        $examinationQueue1 = new PatientExaminationQueue("Delta", $examinationQueue1->getQueueId(), $conn);
                    }
                }
            }
        }
    }
    //Post ExaminationQueue
    if (!is_null($laboratoryExaminationNewAddedServices)) {
        //1. Work on sub-queue
        foreach ($laboratoryExaminationNewAddedServices as $serviceId) {
            $service1 = new Service("Delta", $serviceId, $conn);
            $dataArray1 = array_merge($colArray1, array(
                "examinationQueue" => ($examinationQueue1->getQueueId()),
                "service" => ($service1->getServiceId())
            ));
            switch ($service1->getCategory()->getCategoryId()) {
                case (ServiceCategory::$__LABORATORY_EXAMINATION):
                    __data__::insert($conn, "QueueNotifyWetLab", $dataArray1, !$erollback);
                    break;
                case (ServiceCategory::$__ULTRA_SOUND):
                    __data__::insert($conn, "QueueNotifyUltrasound", $dataArray1, !$erollback);
                    break;
                case (ServiceCategory::$__PLAIN_CONVENTION_X_RAY):
                    __data__::insert($conn, "QueueNotifyPlainXRAY", $dataArray1, !$erollback);
                    break;
                case (ServiceCategory::$__CONTRAST_STUDIES):
                    break;
            }
        }
        //2. Build Monitor
        $monitor1 = new PatientMovementStageMonitor("Delta", __data__::insert($conn, "PatientMovementStageMonitor", array_merge($colArray1, array(
            "stage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
            "temporaryObjectHolder" => ($examinationQueue1->getObjectReferenceString()),
            "_group" => ( $consultationQueue1->getQueueId() )
        )), !$erollback), $conn);
        //3. Build PatientFinanceQueue (charge only those service previous not on the queue, incase of added other services)
        __data__::insert($conn, "PatientFinanceQueue", array_merge($colArray1, array(
            "listOfServices" => implode(",", $laboratoryExaminationNewAddedServices),
            "actionStage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
            "temporaryObjectHolder" => ($examinationQueue1->getObjectReferenceString()),
            "trackMonitor" => ($examinationQueue1->getBundleCode()),
            "monitorReference" => ( $monitor1->getObjectReferenceString() )
        )), !$erollback);
        //4.Put Flags properly
        $consultationQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_LABORATORY_EXAMINATION_PENDING_PAYMENT)->setOnMedicalExamination(true)->update(!$erollback);
        //5. Update PatientFile
        PatientFile::addExaminationQueueLog($conn, $systemTime1, $examinationQueue1->getVisit(), $login1, $examinationQueue1, $examinationQueue1->getBundleCode(), !$erollback);
    }
    ?>
</div>