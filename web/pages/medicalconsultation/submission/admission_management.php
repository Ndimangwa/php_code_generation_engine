<div>
    <?php
    function isArrayAllArrayKeysAvailable($listOfKeys, $targetArray1)  {
        //No need to false all key to be present
        $atLeastSurgeonArray1 = array("surgeon", "otherSurgeon");
        $atLeastAnaesthetistArray1 = array("anaesthetist", "otherAnaesthetist");
        if (is_null($listOfKeys)) return true;
        if (is_null($targetArray1)) return false;
        $available = true;
        $surgeonAvailable = false;
        $anaesthetistAvailable = false;
        foreach ($listOfKeys as $key)   {
            if (! isset($targetArray1[$key]))   {
                //We need to work on surgeon and anaesthetist
                if (in_array($key, $atLeastSurgeonArray1))  {
                    //Continue
                } else if (in_array($key, $atLeastAnaesthetistArray1))  {
                    //Continue
                } else {
                    $available = false;
                    break;
                }
            } else if (! $surgeonAvailable && in_array($key, $atLeastSurgeonArray1))   {
                $surgeonAvailable = true;
            } else if (! $anaesthetistAvailable && in_array($key, $atLeastAnaesthetistArray1))  {
                $anaesthetistAvailable = true;
            }
        }
        return ($available && $surgeonAvailable && $anaesthetistAvailable);
    }
    function _isArrayAllArrayKeysAvailable($listOfKeys, $targetArray1)   {
        if (is_null($listOfKeys)) return true;
        if (is_null($targetArray1)) return false;
        $available = true;
        foreach ($listOfKeys as $key)   {
            if (! isset($targetArray1[$key]))   {
                $available = false;
                break;
            }
        }
        return $available;
    }
    $fieldArray1 = array("listOfOperations", "numberOfDays", "theatre", "surgeon", "otherSurgeon", "anaesthetist", "otherAnaesthetist", "timeOfAppointment", "sponsorName");
    $lookupArray1 = array("listOfOperations" => "listOfServices");
    $admissionArray1 = $operationArray1 = array();
    $newServiceList = null;
    $submittedServices = isset($_POST["listOfOperations"]) ? $_POST["listOfOperations"] : null;
    if (is_null($patientAdmissionQueue1)) {
        //We need to make sure all columns are submitted
        $enableUpdate = isArrayAllArrayKeysAvailable($fieldArray1, $_POST);
        if ($enableUpdate) {
            $newServiceList = $submittedServices;
            foreach ($fieldArray1 as $colname) {
                if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname], array(Constant::$default_select_empty_value)))) {
                    $pname = isset($lookupArray1[$colname]) ? $lookupArray1[$colname] : $colname;
                    if ($colname == "timeOfAppointment") {
                        try {
                            if (trim($_POST[$colname]) != "") {
                                $t1 = DateAndTime::createDateAndTimeFromGUIDate($_POST[$colname]);
                                $operationArray1[$pname] = $t1->getTimestamp();
                            }
                        } catch (Exception $e) {
                        }
                    } else if ($colname == "listOfOperations") {
                        $listOfValues = implode(",", $submittedServices);
                        $admissionArray1[$pname] = $listOfValues;
                        $operationArray1[$pname] = $listOfValues;
                    } else {
                        $admissionArray1[$pname] = $_POST[$colname];
                        $operationArray1[$pname] = $_POST[$colname];
                        if ($colname == "numberOfDays") $operationArray1["duration"] = $_POST[$colname];
                    }
                }
            }
            //New One
            //Now Adding more flags to queues
            foreach (array("pendingPayment" => 1, "completed" => 0) as $colname => $value) {
                $admissionArray1[$colname] = $value;
                $operationArray1[$colname] = $value;
            }
            $patientAdmissionQueue1 = new PatientAdmissionQueue("Delta", __data__::insert($conn, "PatientAdmissionQueue", array_merge($colArray1, $admissionArray1), !$erollback, Constant::$default_select_empty_value), $conn);
            $patientOperationQueue1 = new PatientOperationQueue("Delta", __data__::insert($conn, "PatientOperationQueue", array_merge($colArray1, $operationArray1, array(
                "admissionQueue" => ($patientAdmissionQueue1->getQueueId()),
                "admissionQueueReference" => ($patientAdmissionQueue1->getObjectReferenceString())
            )), !$erollback, Constant::$default_select_empty_value), $conn);
            //Building Flags
            $patientAdmissionQueue1->setInOperation(true)->setOperationQueueReference($patientOperationQueue1->getObjectReferenceString())->update(!$erollback);
        }
    } else {
        $enableUpdate = false;
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        //Working with newServiceList
        //1. Get Existing Services
        $listOfExistingServices = __data__::convertListObjectsToArray($patientAdmissionQueue1->getListOfServices());
        //2. Subset Check
        if (!__data__::isArrayASubsetOfAnotherArray($listOfExistingServices, $submittedServices)) throw new Exception("All Services existed before, must be present");

        //3. Extract newService
        $newServiceList = __data__::substractArray($submittedServices, $listOfExistingServices);
        $newServiceList = (sizeof($newServiceList) == 0) ? null : $newServiceList;

        $enableUpdate = !is_null($newServiceList);
        if ($enableUpdate) {
            foreach ($fieldArray1 as $colname) {
                if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname], array(Constant::$default_select_empty_value)))) {
                    $pname = isset($lookupArray1[$colname]) ? $lookupArray1[$colname] : $colname;
                    $fieldValue = $patientAdmissionQueue1->getMyPropertyValue($pname);
                    //
                    if ($colname == "timeOfAppointment") {
                        try {
                            if (trim($_POST[$colname]) != "") {
                                $t1 = DateAndTime::createDateAndTimeFromGUIDate($_POST[$colname]);
                                $operationArray1[$pname] = $t1->getTimestamp();
                            }
                        } catch (Exception $e) {
                        }
                    } else if ($colname == "listOfOperations") {
                        $listOfValues = implode(",", $submittedServices);
                        $admissionArray1[$pname] = $listOfValues;
                        $operationArray1[$pname] = $listOfValues;
                    } else {
                        if ($fieldValue != $_POST[$colname]) {
                            $admissionArray1[$pname] = $_POST[$colname];
                            $operationArray1[$pname] = $_POST[$colname];
                            if ($colname == "numberOfDays") $operationArray1["duration"] = $_POST[$colname];
                        }
                    }
                    //
                }
            }
            //Need to perform update
            //Now Adding more flags to queues
            foreach (array("pendingPayment" => 1, "completed" => 0) as $colname => $value) {
                $admissionArray1[$colname] = $value;
                $operationArray1[$colname] = $value;
            }
            $patientAdmissionQueue1->setUpdateList(array_merge($updateArray1, $admissionArray1))->update(!$erollback);
            $patientOperationQueue1->setUpdateList(array_merge($updateArray1, $operationArray1))->update(!$erollback);
        }
    }
    //Now Post-Insert/Update
    if (!is_null($newServiceList)) {
        //1. PatientMovementStageMonitor
        $monitor1 = new PatientMovementStageMonitor("Delta", __data__::insert($conn, "PatientMovementStageMonitor", array_merge($colArray1, array(
            "stage" => (PatientMovementStage::$__ADMISSION),
            "temporaryObjectHolder" => ($patientAdmissionQueue1->getObjectReferenceString()),
            "_group" => ( $consultationQueue1->getQueueId() )
        )), !$erollback), $conn);
        //2. FinanceQueue
        __data__::insert($conn, "PatientFinanceQueue", array_merge($colArray1, array(
            "listOfServices" => implode(",", $newServiceList),
            "actionStage" => (PatientMovementStage::$__ADMISSION),
            "temporaryObjectHolder" => ($patientAdmissionQueue1->getObjectReferenceString()),
            "trackMonitor" => ($patientAdmissionQueue1->getBundleCode()),
            "monitorReference" => ( $monitor1->getObjectReferenceString() )
        )), !$erollback);
        //3. Setting ConsultationQueue generalFlags
        $consultationQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_ADMISSION_PENDING_PAYMENT)->setOnAdmission(true)->update(!$erollback);
        //4. Write PatientFile
        PatientFile::addPatientAdmissionLog($conn, $systemTime1, $patientAdmissionQueue1->getVisit(), $login1, $patientAdmissionQueue1, $patientAdmissionQueue1->getBundleCode(), ! $erollback);
    }
    ?>
</div>
