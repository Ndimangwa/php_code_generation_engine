<div>
    <?php
    $myDrugsColumnlist = "pharmaceuticalDrug";
    $myDrugsFieldArray1 = array($myDrugsColumnlist);
    $newAddedDrugs = null;
    $submittedDrugs = isset($_POST[$myDrugsColumnlist]) ? $_POST[$myDrugsColumnlist] : null;
    $listOfDrugsRelatedServices = array();
    $listOfDrugsRelatedServiceQuantities = array();
    if (is_null($patientDrugQueue1)) {
        $enableUpdate = false;
        foreach ($myDrugsFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                if ($colname == $myDrugsColumnlist) {
                    //
                    $newAddedDrugs = $submittedDrugs;
                    //We need to create drugManagement 
                    $listOfDrugManagements = array();
                    foreach ($_POST[$colname] as $index => $drugId) {
                        $quantity = isset($_POST['temporaryIntegerHolder'][$index]) ? intval($_POST['temporaryIntegerHolder'][$index]) : null;
                        $usage = isset($_POST['usage'][$index]) ? $_POST['usage'] : null;
                        if (is_null($quantity)) throw new Exception("Quantity at [ $index ] not understood");
                        if (is_null($usage)) throw new Exception("Usage at [ $index ] not understood");
                        //Pulling Services 
                        $drug1 = new PharmaceuticalDrug("Delta", $drugId, $conn);
                        $serviceId = $drug1->getService()->getServiceId();
                        $listOfDrugsRelatedServices[sizeof($listOfDrugsRelatedServices)] = $serviceId;
                        $listOfDrugsRelatedServiceQuantities[$serviceId] = $quantity;
                        $listOfDrugManagements[sizeof($listOfDrugManagements)] = __data__::insert($conn, "PatientDrugManagement", array_merge($colArray1, array(
                            "pharmaceuticalDrug" => ($drug1->getDrugId()),
                            "usage" => $usage,
                            "quantity" => $quantity
                        )), !$erollback);
                    }
                    $enableUpdate = (sizeof($listOfDrugManagements) > 0);
                    if ($enableUpdate) {
                        $patientDrugQueue1 = new PatientDrugQueue("Delta", __data__::insert($conn, "PatientDrugQueue", array_merge($colArray1, array(
                            "listOfDrugManagements" => implode(",", $listOfDrugManagements),
                            "pendingPayment" => 1,
                            "consultationQueue" => ($consultationQueue1->getQueueId())
                        )), !$erollback, null), $conn);
                    }
                }
            }
        }
    } else {
        //Get Drugs
        //We need to work on drugs
        $listOfExistingDrugs = array();
        $mapDrugToManager = array(); //[drugId] => managerId
        $enableUpdate = false;
        if (!is_null($patientDrugQueue1->getListOfDrugManagements())) {
            foreach ($patientDrugQueue1->getListOfDrugManagements() as $management1) {
                if (!is_null($management1->getPharmaceuticalDrug())) {
                    $listOfExistingDrugs[sizeof($listOfExistingDrugs)] = $management1->getPharmaceuticalDrug();
                    //Do mapping 
                    $mapDrugToManager[$management1->getPharmaceuticalDrug()->getDrugId()] = $management1->getManagementId();
                }
            }
        }
        if (sizeof($listOfExistingDrugs) == 0) $listOfExistingDrugs = null;

        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        foreach ($myDrugsFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                if ($colname == $myDrugsColumnlist) {
                    //1. Existing Services
                    $existingDrugs = __data__::convertListObjectsToArray($listOfExistingDrugs);
                    //2. We need to make sure all-existing list is in submitted list
                    if (!__data__::isArrayASubsetOfAnotherArray($existingDrugs, $submittedDrugs)) throw new Exception("All drugs existed before, must be present");
                    //3. Need to Update services and
                    $newAddedDrugs = __data__::substractArray($submittedDrugs, $existingDrugs);
                    $newAddedDrugs = (sizeof($newAddedDrugs) == 0) ? null : $newAddedDrugs;
                    //4. Update if exists new service
                    if (!is_null($newAddedDrugs)) {
                        //This is where you need to update
                        if ($patientDrugQueue1->isPendingPayment()) throw new Exception("Previous Drugs were not paid, kindly pay them first");
                        //We need to add Management for new services , however we need to append to the 
                        $listOfDrugManagements = array();
                        //Note $_POST[$colname] is same as submittedDrugs
                        foreach ($_POST[$colname] as $index => $drugId) {
                            $managementId = null;
                            if (isset($mapDrugToManager[$drugId])) {
                                $managementId = $mapDrugToManager[$drugId];
                            } else {
                                $quantity = isset($_POST['temporaryIntegerHolder'][$index]) ? intval($_POST['temporaryIntegerHolder'][$index]) : null;
                                $usage = isset($_POST['usage'][$index]) ? $_POST['usage'] : null;
                                if (is_null($quantity)) throw new Exception("Quantity at [ $index ] not understood");
                                if (is_null($usage)) throw new Exception("Usage at [ $index ] not understood");
                                //Pulling Services 
                                $drug1 = new PharmaceuticalDrug("Delta", $drugId, $conn);
                                $serviceId = $drug1->getService()->getServiceId();
                                $listOfDrugsRelatedServices[sizeof($listOfDrugsRelatedServices)] = $serviceId;
                                $listOfDrugsRelatedServiceQuantities[$serviceId] = $quantity;
                                $managementId = __data__::insert($conn, "PatientDrugManagement", array_merge($colArray1, array(
                                    "pharmaceuticalDrug" => ($drug1->getDrugId()),
                                    "usage" => $usage,
                                    "quantity" => $quantity
                                )), !$erollback);
                            }
                            $listOfDrugManagements[sizeof($listOfDrugManagements)] = $managementId;
                        }
                        //Need to perform update
                        $enableUpdate = (sizeof($listOfDrugManagements) > 0);
                        if ($enableUpdate) {
                            $patientDrugQueue1->setUpdateList(array(
                                "timeOfUpdation" => ($systemTime1->getTimestamp()),
                                "listOfDrugManagements" => implode(",", $listOfDrugManagements),
                                "pendingPayment" => 1,
                                "completed" => 0,
                                "requestedBy" => ( $login1->getLoginId() )
                            ))->update(!$erollback);
                            //We need to reload since we have used the setUpdateList
                            $patientDrugQueue1 = new PatientDrugQueue("Delta", $patientDrugQueue1->getQueueId(), $conn);
                        }
                    }
                }
            }
        }
    }
    //Post Drugs Management 
    if (!is_null($newAddedDrugs)) {
        //1. PatientMovementStageMonitor
        $monitor1 = new PatientMovementStageMonitor("Delta", __data__::insert($conn, "PatientMovementStageMonitor", array_merge($colArray1, array(
            "stage" => (PatientMovementStage::$__PHARMACY),
            "temporaryObjectHolder" => ($patientDrugQueue1->getObjectReferenceString()),
            "_group" => ( $consultationQueue1->getQueueId() )
        )), !$erollback), $conn);
        //2. FinanceQueue
        __data__::insert($conn, "PatientFinanceQueue", array_merge($colArray1, array(
            "listOfServices" => implode(",", $listOfDrugsRelatedServices),
            "quantityString" => __object__::array2String($listOfDrugsRelatedServiceQuantities, "1"),
            "actionStage" => (PatientMovementStage::$__PHARMACY),
            "trackMonitor" => ($patientDrugQueue1->getBundleCode()),
            "temporaryObjectHolder" => ($patientDrugQueue1->getObjectReferenceString()),
            "monitorReference" => ( $monitor1->getObjectReferenceString() )
        )), !$erollback);
        //3. Setting Pending Payments etc
        $consultationQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_PHARMACY_PENDING_PAYMENT)->setOnPharmacy(true)->update(!$erollback);
        //4. Write to PatientFile
        PatientFile::addPatientDrugQueueLog($conn, $systemTime1, $patientDrugQueue1->getVisit(), $login1, $patientDrugQueue1, $patientDrugQueue1->getBundleCode(), !$erollback);
    }
    ?>
</div>