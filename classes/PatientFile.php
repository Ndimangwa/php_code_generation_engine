<?php
class PatientFile
{
    static $__SUB_OP_DEFAULT = 1;
    static $__SUB_OP_INVOICE = 2;
    static $__SUB_OP_RECEIPT = 3;
    static $__SUB_OP_RESULTS = 4;
    static $__SUB_OP_ADMISSION = 5;
    static $__SUB_OP_DISCHARGE = 6;
    public static function getProgressMonitorDataStructure($conn, $systemTime1, $patient1)
    {
    }
    public static function deltaF($systemTime1, $visit1, $login1, $savingMode = true)
    {
        $t1 = array();
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function deltaF2($systemTime1, $visit1, $login1, $triage1, $savingMode = true)
    {
        $t1 = self::registerOperation("triage", $systemTime1, $visit1, $login1);
        $t1['triage'] = self::buildTriage($triage1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getCommonSubUI($conn, $stage1, $stageBlock1)
    {
        $stageName = $stage1->getStageName();
        $window1 = "<div class=\"my-1 card\"><div class=\"card-header text-center bg-primary\"><div class=\"card-title\">$stageName</div></div><div class=\"card-body\">";
        $bundledDispArray1 = array();
        $bundledIndices = array();
        $tcolnames = "<tr><th>S/N</th><th>Time</th><th>Activity</th><th>By</th></tr>";
        foreach ($stageBlock1 as $index => $fileId) {
            $file1 = new PatientFileObject("Delta", $fileId, $conn);
            $payload = __object__::overwriteArray(json_decode($file1->getPayload(), true), self::getInverseCodesLookupArray());
            //Every Stage has its UI of display
            $bundleCode = $file1->getBundleCode();
            if (!isset($bundleCode)) {
                $bundledDispArray1[$bundleCode] = "";
                $bundledIndices[$bundleCode] = 0;
            }
            $index = $bundledIndices[$bundleCode] + 1;
            $bundledIndices[$bundleCode]++;
            $subop = $payload['sub-op'];
            $attendedBy = $payload['attended-by']['username'];
            $currencyCode = "";
            $invoiceNumber = "";
            $invoiceAmount = 0;
            $invoiceBalance = 0;
            $receiptNumber = "";
            $receiptAmount = 0;
            $eventTime = $file1->getTimeOfCreation()->getDateAndTimeString();
            if (isset($payload['payment'])) {
                if (isset($payload['payment']['currency'])) {
                    $currencyCode = $payload['payment']['currency']['code'];
                }
                if (isset($payload['payment']['invoice'])) {
                    $invoiceNumber = $payload['payment']['invoice']['number'];
                    $invoiceAmount = $payload['payment']['invoice']['amount'];
                    $invoiceBalance = $payload['payment']['invoice']['balance'];
                }
                if (isset($payload['payment']['receipt'])) {
                    $receiptNumber = $payload['payment']['receipt']['number'];
                    $receiptAmount = $payload['payment']['receipt']['amount'];
                }
            }
            $invoiceAmount  = number_format($invoiceAmount, 2, '.', ',');
            $receiptAmount = number_format($receiptAmount, 2, '.', ',');
            $invoiceBalance = number_format($invoiceBalance, 2, '.', ',');
            switch ($stage1->getStageId()) {
                case (PatientMovementStage::$__NEW_REGISTRATION):
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated New Registration</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__CONTINUING_VISIT):
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated a Visit</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__TRIAGE):
                    $triage1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($triage1)) break;
                    $tcolnames = "<tr><th>S/N</th><th>Time</th><th>Values</th><th>By</th></tr>";
                    $payload = $triage1->getMyPayload();
                    //BMI
                    $line = "";
                    $weight = $payload['weight'];
                    $height = $payload['height'];
                    $bmi = $payload['bmi'];
                    $bmiStatus = $payload['bmiStatus'];
                    $bmiColor = $payload['bmiColor'];
                    if ($weight != -1) $line .= " <span>Weight: <b>$weight (Kg)</b>;</span>";
                    if ($height != -1) $line .= " <span>Height: <b>$height (cm)</b>;</span>";
                    if ($bmi != -1) {
                        $bmi = number_format($bmi, 2, '.', '');
                        $bmiStatus = $payload['bmiStatus'] == -1 ? "" : (Triage::decode_bmi_status($payload['bmiStatus']));
                        $bmiColor = $payload['bmiColor'];
                        $bmiColor = is_null($bmiColor) ? "" : "<span class=\"border border-dark rounded-circle $bmiColor p-1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                        $line .= " <span>BMI: <b>$bmi <i>$bmiStatus</i> $bmiColor</b></span> ";
                    }
                    //Temperature and Pressure
                    if ($line != "") $line .= "<br/>";
                    $temperature = $payload['temperature'];
                    $bloodPressure = $payload['bloodPressure'];
                    $bloodPressureStatus = $payload['bloodPressureStatus'];
                    $bloodPressureColor = $payload['bloodPressureColor'];
                    if ($temperature != -1) $line .= " <span>Temp : <b>$temperature (C)</b>;</span>";
                    if (!is_null($bloodPressure)) {
                        $bloodPressureStatus = $payload['bloodPressureStatus'] == -1 ? "" : (Triage::decode_bp_status($payload['bloodPressureStatus']));
                        $bloodPressureColor = $payload['bloodPressureColor'];
                        $bloodPressureColor = is_null($bloodPressureColor) ? "" : "<span class=\"border border-dark rounded-circle $bloodPressureColor p-1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                        $line .= " <span>BP : <b>$bloodPressure (mmHg) <i>$bloodPressureStatus</i> $bloodPressureColor</b></span> ";
                    }
                    //Pulse Rate, RespirationRate and Oxygen Level
                    if ($line != "") $line .= "<br/>";
                    $pulseRate = $payload['pulseRate'];
                    $respirationRate = $payload['respirationRate'];
                    $oxygenLevel = $payload['oxygenLevel'];
                    if ($pulseRate != -1) $line .= " <span>Pulse Rate : <b>$pulseRate</b>;</span>";
                    if ($respirationRate != -1) $line .= " <span>Respiration Rate : <b>$respirationRate</b>;</span>";
                    if ($oxygenLevel != -1) $line .= " <span>Oxygen Level : <b>$oxygenLevel (%)</b>;</span>";
                    $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$line</td><td>$attendedBy</td></tr>";
                    break;
                case (PatientMovementStage::$__MEDICAL_DOCTOR_CONSULTATION):
                    $consultationQueue1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($consultationQueue1)) break;
                    $tcolnames = "<tr><th>S/N</th><th>Time</th><th>Assigned Doctor</th><th>By</th></tr>";
                    $payload = $consultationQueue1->getMyPayload();
                    $medicalDoctor = is_null($consultationQueue1->getMedicalDoctor()) ? "" : ($consultationQueue1->getMedicalDoctor()->getLogin()->getLoginName());
                    $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$medicalDoctor</td><td>$attendedBy</td></tr>";
                    break;
                case (PatientMovementStage::$__PHARMACY):
                    $patientDrugQueue1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($patientDrugQueue1)) break;
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated Drugs Management</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RESULTS):
                            $dispensedPatientDrugManager1 = $patientDrugQueue1;
                            $bodyText = "<table style=\"font-style: italic;\" class=\"table\">";
                            $bcount = 0;
                            foreach ($dispensedPatientDrugManager1->getListOfDispensedDrugs() as $dispensedPatientDrug1) {
                                $bcount++;
                                $pharmaceuticalDrug1 = $dispensedPatientDrug1->getDrugManagement()->getPharmaceuticalDrug();
                                $drugName = $pharmaceuticalDrug1->getDrugName();
                                $unitName = $pharmaceuticalDrug1->getUnitOfMeasurement()->getUnitName();
                                $quantity = $dispensedPatientDrug1->getQuantity();
                                $usage = $dispensedPatientDrug1->getUsage();
                                $bodyText .= "<tr><td>$bcount</td><td>$drugName ( $quantity $unitName, usage = \"$usage\" )</td></tr>";
                            }
                            $bodyText .= "</table>";
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$bodyText</td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__LABORATORY_EXAMINATION):
                    //Putting header properly
                    $patientExaminationQueue1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($patientExaminationQueue1)) break; //In-Case of re-requested results
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated Laboratory Examination</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RESULTS):
                            $patientExaminationResults1 = $patientExaminationQueue1; //Now we are dealing with results object 
                            $standard1 = $patientExaminationResults1->getExaminationStandard();
                            $serviceName = $standard1->getServiceName();
                            $t1 = $standard1->getUnitOfMeasurement();
                            $results = is_null($t1) ? "" : $t1;
                            $results = ($patientExaminationResults1->getGeneralValue()) . " $results";
                            $bgcolor = ($patientExaminationResults1->isSafeValue()) ? "bg-primary" : "bg-danger";
                            $rangeText = "";
                            if ($standard1->isEnumerated()) {
                                $t1 = $standard1->getSafeEnumeratedValue();
                                $rangeText = "<span>Safe Value: <i>$t1</i></span>";
                            } else {
                                $t1 = $standard1->getMinimumSafeValue();
                                $t2 = $standard1->getMaximumSafeValue();
                                $rangeText = "<span>Range : <i>[ $t1, $t2 ]</i></span>";
                            }
                            //Building results
                            $ageCategory = null;
                            $sex = null;
                            $optionText = "";
                            $t1 = $standard1->getAgeCategory();
                            if (!is_null($t1)) {
                                $ageCategory = $t1->getCategoryName();
                                $min = $t1->getMinimumAge();
                                $max = $t1->getMaximumAge();
                                $ageCategory .= " <i>{ $min years, $max years }</i>";
                            }
                            $t1 = $standard1->getSex();
                            if (!is_null($t1)) $sex = $t1->getSexName();
                            if (!is_null($ageCategory)) $optionText = $ageCategory;
                            if (!is_null($sex)) $optionText .= " <span>$sex</span>";
                            $t1 = "<span class=\"text-center\">Results</span>";
                            $t1 .= "<span>Service : <b>$serviceName</b></span>";
                            $t1 .= "<br/><span>Results : <span class=\"border border-dark rounded-circle $bgcolor p-1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> $results</span>";
                            $t1 .= "<br/><span>$rangeText</span>";
                            if ($optionText != "") $t1 .= "<br/><span>$optionText</span>";
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$t1</td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__DISEASE_ANALYSIS):
                    $medicalDoctorExaminedDisease1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($medicalDoctorExaminedDisease1)) break;
                    //No need of sub procedure 
                    $tcolnames = "<tr><th>S/N</th><th>Time</th><th>ICD 10 Codes</th><th>Disease Name</th><th>By</th></tr>";
                    $listOfDiseases = $medicalDoctorExaminedDisease1->getListOfICD10Diseases();
                    if (is_null($listOfDiseases)) break;
                    $time = $medicalDoctorExaminedDisease1->getTimeOfCreation()->getDateAndTimeString();
                    $baseIndex = ($index - 1) * sizeof($listOfDiseases);
                    $i = 0;
                    foreach ($listOfDiseases as $disease1) {
                        $code = $disease1->getIcd10Code();
                        $dname = $disease1->getWhoFullDescription();
                        $currIndex = $baseIndex + $i + 1;
                        $bundledDispArray1[$bundleCode] .= "<tr><th>$currIndex</th><td>$time</td><td>$code</td><td>$dname</td><td>$attendedBy</td></tr>";
                        $i++;
                    }
                    break;
                case (PatientMovementStage::$__NURSE_STATION):
                    //Putting header properly
                    $nurseStationQueue1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($nurseStationQueue1)) break; //In-Case of re-requested results
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $serviceName = $nurseStationQueue1->getService()->getServiceName();
                            $limit = $nurseStationQueue1->getMaximumNumberOfAttendance();
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated <i>[ $serviceName ]</i>, do for <i>$limit</i> times</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RESULTS):
                            $nurseStationActivity1 = $nurseStationQueue1; //Now we are dealing with results object 
                            $serviceName = $nurseStationActivity1->getService()->getServiceName();
                            $seqno = $nurseStationActivity1->getSequenceNumber();
                            $progress1 = $nurseStationActivity1->getProgress();
                            $progressName = $progress1->getProgressName();
                            $progressId = $progress1->getProgressId();
                            $resultsText = $nurseStationActivity1->getResultsText();
                            if (is_null($resultsText)) $resultsText = "";
                            if ($resultsText != "") $resultsText = "<span>$resultsText</span>";
                            $requireMedicalAttention = $nurseStationActivity1->isRequireMedicalAttention() ? " , <span style=\"text-color: red; font-style: italic;\">Required Medical Attention</span>" : "";
                            $status = "";
                            switch ($progressId) {
                                case (NurseStationActivityProgress::$__EXCELLENT):
                                    $status = UIStatus::getPrimaryRoundedStatus();
                                    break;
                                case (NurseStationActivityProgress::$__GOOD):
                                    $status = UIStatus::getWarningRoundedStatus();
                                    break;
                                case (NurseStationActivityProgress::$__BAD):
                                    $status = UIStatus::getDarkRoundedStatus();
                                    break;
                                case (NurseStationActivityProgress::$__WORSE):
                                    $status = UIStatus::getDangerRoundedStatus();
                                    break;
                            }
                            $resultsText = "<span>Seq no: <b>$seqno</b></span> $resultsText <span>$status </span><span>Progress : <i>$progressName</i></span> $requireMedicalAttention";
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$resultsText</td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__ADMISSION):
                    $admissionObject1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($admissionObject1)) break;
                    //In-case you need to modify header 
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $patientAdmissionQueue1 = $admissionObject1;
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Initiated Admission Queue</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_INVOICE):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Invoice no : <b>$invoiceNumber <i>[ $currencyCode . $invoiceAmount ]</i></b> raised</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_RECEIPT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Receipt no : <b>$receiptNumber <i>[ $currencyCode . $receiptAmount ]</i></b> for invoice <i>[ $invoiceNumber ] </i> were received. Balance is <i>[ $currencyCode . $invoiceBalance ]</i></td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_ADMISSION):
                            $patientAdmission1 = $admissionObject1;
                            $content = "Admitted";
                            $bed1 = $patientAdmission1->getBed();
                            $bedNumber = $bed1->getBedNumber();
                            $content .= " <span>in bed no : <i>$bedNumber</i></span>";
                            $room1 = $bed1->getRoom();
                            $roomNumber = $room1->getRoomNumber();
                            $observationRoom = ($room1->isObservation()) ? ",<span>Observation Room</span>" : "";
                            $privateRoom = ($room1->isPrivate()) ? ",<span>Private Room</span>" : "";
                            $content .= "; <span>in room no : <i>$roomNumber</i> $observationRoom $privateRoom<span>";
                            $ward1 = $room1->getWard();
                            $wardNumber = $ward1->getWardNumber();
                            $content .= "; <span>in ward no : <i>$wardNumber</i></span>";
                            $content = "<span>$content</span><br/>";
                            $listOfServices = $patientAdmission1->getListOfServices();
                            $tcount = 0;
                            if (!is_null($listOfServices)) {
                                $content .= "<span style=\"font-style: italic;\">Services Admitted for : - <br/><span style=\"margin-left: 5px; position: relative;\">";
                                foreach ($listOfServices as $service1) {
                                    $sn = $tcount + 1;
                                    $serviceName = $service1->getServiceName();
                                    $tspan = "<span>( $sn ) : <span>$serviceName</span></span>";
                                    $content .= ($tcount == 0) ? $tspan : ("<br/>" . $tspan);
                                    $tcount++;
                                }
                                $content .= "</span></span>";
                            }
                            $content = "<span>$content</span>";
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>$content</td><td>$attendedBy</td></tr>";
                            break;
                        case (self::$__SUB_OP_DISCHARGE):
                            $patientDischargeQueue1 = $admissionObject1;
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Patient Discharged</td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
                case (PatientMovementStage::$__CASE_CLOSED):
                    $patientCase1 = Registry::getInstance("Delta", $conn, $file1->getTemporaryObjectHolder());
                    if (is_null($patientCase1)) break;
                    //In-case you need to modify header 
                    switch ($subop) {
                        case (self::$__SUB_OP_DEFAULT):
                            $bundledDispArray1[$bundleCode] .= "<tr><th>$index</th><td>$eventTime</td><td>Case is Closed</td><td>$attendedBy</td></tr>";
                            break;
                    }
                    break;
            }
        }
        foreach ($bundledDispArray1 as $bundleCode => $dataLine) {
            $window1 .= "<div style=\"font-size: 0.9em;\" class=\"mb-1 border border-dotted-secondary table-responsive\"><table class=\"table\"><thead>$tcolnames</thead><tbody>$dataLine</tbody></table></div>";
        }
        $window1 .= "</div><div class=\"card-footer text-muted text-center\"></div></div>";
        return $window1;
    }
    public static function getHistoryDataStructure($conn, $patient1, $sortSequence = null)
    {
        /*
        return ds[caseId][visitId][stageId][index] = fileId 

        index because the same stageId can have sub-stage like default, invoice, receipt etc
        */
        if (is_null($sortSequence)) $sortSequence = array((PatientMovementStage::$__TRIAGE), (PatientMovementStage::$__MEDICAL_DOCTOR_CONSULTATION));
        $ds1 = array();
        $patientId = $patient1->getPatientId();
        $query = "SELECT fileId FROM _patient_file as f, _patientVisit as v, _patientCase as c WHERE (f.visitId=v.visitId) AND (v.caseId=c.caseId) AND (c.patientId='$patientId')";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e) {
            $records = null;
        }
        if (is_null($records)) return null;
        foreach ($records['column'] as $row1) {
            $file1 = new PatientFileObject("Df", $row1['fileId'], $conn);
            $caseId = $file1->getVisit()->getPatientCase()->getCaseId();
            $visitId = $file1->getVisit()->getVisitId();
            $stageId = $file1->getActionStage()->getStageId();
            if (!isset($ds1[$caseId])) {
                $ds1[$caseId] = array();
            }
            if (!isset($ds1[$caseId][$visitId])) {
                $ds1[$caseId][$visitId] = array();
            }
            if (!isset($ds1[$caseId][$visitId][$stageId])) {
                $ds1[$caseId][$visitId][$stageId] = array();
            }
            //Now assign
            $ds1[$caseId][$visitId][$stageId][sizeof($ds1[$caseId][$visitId][$stageId])] = $file1->getFileId();
        }
        if (sizeof($ds1) == 0) $ds1 = null;
        /*foreach ($ds1 as $caseId => $caseBlock1)    {
            foreach ($caseBlock1 as $visitId => $visitBlock1)   {
                    $ds1[$caseId][$visitId] = __object__::customSortSequence($ds1[$caseId][$visitId], $sortSequence, true, function($dataArray1, $index)   {
                        return $index; //Aim is to return stageId
                    });
            }
        }*/
        return $ds1;
    }
    public static function getPatientHistory($conn, $patient1, $profile1, $login1/*, paperObject*/)
    {
        //Styles 
        $window1 = "<style type=\"text/style\">";
        $window1 .= ".document-display span { padding-left: 2px; padding-right: 2px; }";
        $window1 .= "</style>";
        //Main window
        $window1 .= "<div class=\"document-display my-2 mx-1 p-1\" style=\"background-color: white;\"><div class=\"p-2\" style=\"background-color: black;\"><div class=\"p-1\" style=\"background-color: white;\">";
        //Profile Information 
        $profileName = $profile1->getProfileName();
        $window1 .= "<div class=\"mb-1 text-center\" style=\"font-size: 1.2em;\">$profileName</div>";
        //Patient registration Information
        $patientName = $patient1->getPatientName();
        $registrationNumber = $patient1->getRegistrationNumber();
        $sex = $patient1->getSex()->getSexName();
        $timeOfCreation = $patient1->getTimeOfCreation()->getDateAndTimeString();
        $status = $patient1->getStatus()->getStatusName();
        $window1 .= "<div class=\"mb-1 ml-1\">";
        $window1 .= "<div style=\"display: flex; justify-content: space-between;\"><span>Patient Name: <b><i>$patientName</i></b></span><span>Time of Creation : <b><i>$timeOfCreation</i></b></span></div>";
        $window1 .= "<div style=\"display: flex; justify-content: space-between;\"><span>Reg Number : <b><i>$registrationNumber</i></b></span><span>Sex : <b><i>$sex</i></b></span><span>Status : <b><i>$status</i></b></span></div>";
        $window1 .= "</div>";
        //Now we need to traverse through the entire service list
        $ds1 = self::getHistoryDataStructure($conn, $patient1);
        if (!is_null($ds1)) {
            $window1 .= "<div class=\"all-cases-enclosure mb-1\">";
            $caseCount = 0;
            foreach ($ds1 as $caseId => $caseBlock1) {
                $caseCount++;
                $window1 .= "<div class=\"text-center\">******** Begin, Case( $caseCount )  ********</div>";
                $window1 .= "<div class=\"all-visits-enclosure mb-1\">";
                foreach ($caseBlock1 as $visitId => $visitBlock1) {
                    $visit1 = new PatientVisit("Delta", $visitId, $conn);
                    $visitCount = $visit1->getVisitCount();
                    $window1 .= "<div class=\"text-center\">******** Begin, Visit( $visitCount )  ********</div>";
                    $window1 .= "<div class=\"specific-visit-enclosure mb-1 border border-dotted-primary\">";
                    foreach ($visitBlock1 as $stageId => $stageBlock1) {
                        $stage1 = new PatientMovementStage("Delta", $stageId, $conn);
                        $window1 .= "<div class=\"specific-stage-enclosure\">";
                        $window1 .= self::getCommonSubUI($conn, $stage1, $stageBlock1);
                        //Here is where you have to add for each stage
                        $window1 .= "</div>"; //specific-stage-enclosure
                    }
                    $window1 .= "</div>"; //specific-visit-enclosure
                    $window1 .= "<div class=\"text-center\">******** End, Visit( $visitCount )  ********</div>";
                }
                $window1 .= "</div>"; //all-visits-enclosure
                $window1 .= "<div class=\"text-center\">******** End, Case( $caseCount )  ********</div>";
            }
            $window1 .= "</div>"; //all-cases-enclosure
        } else {
            $window1 .= "<div class=\"mb-1\">There is no data associated with this patient</div>";
        }
        //Close Outer Envelop
        $window1 .= "</div></div></div>";
        return $window1;
    }
    public static function getListOfFiles($conn, $classname, $id)
    {
        if (is_null($classname) || is_null($id)) return null;
        $criteria = "$classname.$id";
        $list = array();
        try {
            $query = "SELECT fileId FROM _patient_file WHERE temporaryObjectHolder='$criteria'";
            $records = __data__::getSelectedRecords($conn, $query, false);
            if (!is_null($records)) {
                foreach ($records['column'] as $record1) {
                    $list[sizeof($list)] = new PatientFileObject("Delta", $record1['fileId'], $conn);
                }
            }
        } catch (Exception $e) {
            return null;
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    //Main Top Functions
    public static function addPatientCaseClosedLog($conn, $systemTime1, $visit1, $login1, $patientCase1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__CASE_CLOSED,
            $systemTime1,
            $visit1,
            self::getPatientCaseClosedPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientCase1,
                true
            ),
            $patientCase1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientAdmissionBeingDischargedLog($conn, $systemTime1, $visit1, $login1, $patientDischargeQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__ADMISSION,
            $systemTime1,
            $visit1,
            self::getPatientAdmissionBeingDischargedPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientDischargeQueue1,
                true
            ),
            $patientDischargeQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientAdmissionBeingAdmittedLog($conn, $systemTime1, $visit1, $login1, $patientAdmission1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__ADMISSION,
            $systemTime1,
            $visit1,
            self::getPatientAdmissionBeingAdmittedPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientAdmission1,
                true
            ),
            $patientAdmission1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientAdmissionReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__ADMISSION,
            $systemTime1,
            $visit1,
            self::getPatientAdmissionReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $receipt1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientAdmissionInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__ADMISSION,
            $systemTime1,
            $visit1,
            self::getPatientAdmissionInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $invoice1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientAdmissionLog($conn, $systemTime1, $visit1, $login1, $patientAdmissionQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__ADMISSION,
            $systemTime1,
            $visit1,
            self::getPatientAdmissionPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientAdmissionQueue1,
                true
            ),
            $patientAdmissionQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientDrugDispensedLog($conn, $systemTime1, $visit1, $login1, $dispensedPatientDrugManager1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__PHARMACY,
            $systemTime1,
            $visit1,
            self::getPatientDrugDispensedPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $dispensedPatientDrugManager1,
                true
            ),
            $dispensedPatientDrugManager1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientDrugQueueReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__PHARMACY,
            $systemTime1,
            $visit1,
            self::getPatientDrugQueueReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $receipt1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientDrugQueueInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__PHARMACY,
            $systemTime1,
            $visit1,
            self::getPatientDrugQueueInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $invoice1,
            $bundleCode,
            $rollback
        );
    }
    public static function addPatientDrugQueueLog($conn, $systemTime1, $visit1, $login1, $patientDrugQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__PHARMACY,
            $systemTime1,
            $visit1,
            self::getPatientDrugQueuePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientDrugQueue1,
                true
            ),
            $patientDrugQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addMedicalDoctorExaminedDiseaseLog($conn, $systemTime1, $visit1, $login1, $medicalDoctorExaminedDisease1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__DISEASE_ANALYSIS,
            $systemTime1,
            $visit1,
            self::getMedicalDoctorExaminedDiseasesPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $medicalDoctorExaminedDisease1,
                true
            ),
            $medicalDoctorExaminedDisease1,
            $bundleCode,
            $rollback
        );
    }
    public static function addNurseStationActivityLog($conn, $systemTime1, $visit1, $login1, $nurseStationActivity1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NURSE_STATION,
            $systemTime1,
            $visit1,
            self::getNurseStationActivityPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $nurseStationActivity1,
                true
            ),
            $nurseStationActivity1,
            $bundleCode,
            $rollback
        );
    }
    public static function addNurseStationQueueReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NURSE_STATION,
            $systemTime1,
            $visit1,
            self::getNurseStationQueueReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $receipt1,
            $bundleCode,
            $rollback
        );
    }
    public static function addNurseStationQueueInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NURSE_STATION,
            $systemTime1,
            $visit1,
            self::getNurseStationQueueInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $invoice1,
            $bundleCode,
            $rollback
        );
    }
    public static function addNurseStationQueueLog($conn, $systemTime1, $visit1, $login1, $nurseStationQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NURSE_STATION,
            $systemTime1,
            $visit1,
            self::getNurseStationQueuePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $nurseStationQueue1,
                true
            ),
            $nurseStationQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addExaminationResultsLog($conn, $systemTime1, $visit1, $login1, $patientExaminationResults1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__LABORATORY_EXAMINATION,
            $systemTime1,
            $visit1,
            self::getExaminationResultsPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientExaminationResults1,
                true
            ),
            $patientExaminationResults1,
            $bundleCode,
            $rollback
        );
    }
    public static function addExaminationQueueReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__LABORATORY_EXAMINATION,
            $systemTime1,
            $visit1,
            self::getExaminationQueueReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $receipt1,
            $bundleCode,
            $rollback
        );
    }
    public static function addExaminationQueueInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__LABORATORY_EXAMINATION,
            $systemTime1,
            $visit1,
            self::getExaminationQueueInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $invoice1,
            $bundleCode,
            $rollback
        );
    }
    public static function addExaminationQueueLog($conn, $systemTime1, $visit1, $login1, $patientExaminationQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__LABORATORY_EXAMINATION,
            $systemTime1,
            $visit1,
            self::getExaminationQueuePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $patientExaminationQueue1,
                true
            ),
            $patientExaminationQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addMedicalConsultationLog($conn, $systemTime1, $visit1, $login1, $medicalDoctorConsultationQueue1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__MEDICAL_DOCTOR_CONSULTATION,
            $systemTime1,
            $visit1,
            self::getMedicalConsultationPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $medicalDoctorConsultationQueue1,
                true
            ),
            $medicalDoctorConsultationQueue1,
            $bundleCode,
            $rollback
        );
    }
    public static function addTriageLog($conn, $systemTime1, $visit1, $login1, $triage1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__TRIAGE,
            $systemTime1,
            $visit1,
            self::getTriagePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $triage1,
                true
            ),
            $triage1,
            $bundleCode,
            $rollback
        );
    }
    public static function addContinuingVisitReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__CONTINUING_VISIT,
            $systemTime1,
            $visit1,
            self::getPatientContinuingVisitReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $receipt1,
            $bundleCode,
            $rollback
        );
    }
    public static function addContinuingVisitInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__CONTINUING_VISIT,
            $systemTime1,
            $visit1,
            self::getPatientContinuingVisitInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $invoice1,
            $bundleCode,
            $rollback
        );
    }
    public static function addContinuingVisitLog($conn, $systemTime1, $visit1, $login1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__CONTINUING_VISIT,
            $systemTime1,
            $visit1,
            self::getPatientContinuingVisitPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                true
            ),
            $visit1->getPatientCase()->getPatient(),
            $bundleCode,
            $rollback
        );
    }
    public static function addNewRegistrationReceiptLog($conn, $systemTime1, $visit1, $login1, $receipt1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NEW_REGISTRATION,
            $systemTime1,
            $visit1,
            self::getNewPatientRegistrationReceiptPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $receipt1,
                true
            ),
            $visit1->getPatientCase()->getPatient(),
            $bundleCode,
            $rollback
        );
    }
    public static function addNewRegistrationInvoiceLog($conn, $systemTime1, $visit1, $login1, $invoice1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NEW_REGISTRATION,
            $systemTime1,
            $visit1,
            self::getNewPatientRegistrationInvoicePayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                $invoice1,
                true
            ),
            $visit1->getPatientCase()->getPatient(),
            $bundleCode,
            $rollback
        );
    }
    public static function addNewRegistrationLog($conn, $systemTime1, $visit1, $login1, $bundleCode, $rollback = true)
    {
        return self::__update_patient_file__(
            $conn,
            PatientMovementStage::$__NEW_REGISTRATION,
            $systemTime1,
            $visit1,
            self::getNewPatientRegistrationPayload(
                $conn,
                $systemTime1,
                $visit1,
                $login1,
                true
            ),
            $visit1->getPatientCase()->getPatient(),
            $bundleCode,
            $rollback
        );
    }
    private static function __update_patient_file__($conn, $stageId, $systemTime1, $visit1, $payload, $refobject1, $bundleCode, $rollback = true)
    {
        return __data__::insert($conn, "PatientFileObject", array(
            "timeOfCreation" => $systemTime1->getTimestamp(),
            "timeOfUpdation" => $systemTime1->getTimestamp(),
            "visit" => $visit1->getVisitId(),
            "actionStage" => $stageId,
            "payload" => json_encode($payload),
            "bundleCode" => $bundleCode,
            "temporaryObjectHolder" => ($refobject1->getObjectReferenceString())
        ), $rollback);
    }
    //Main Top Functions ended
    //Begin -- Top Level Functions
    public static function getPatientCaseClosedPayload($conn, $systemTime1, $visit1, $login1, $patientCase1, $savingMode = true)
    {
        $t1 = self::registerOperation("patient-case", $systemTime1, $visit1, $login1);
        $t1['patient-case'] = self::buildPatientCase($patientCase1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientAdmissionBeingDischargedPayload($conn, $systemTime1, $visit1, $login1, $patientDischargeQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("patient-discharge-queue", $systemTime1, $visit1, $login1);
        $t1['sub-op'] = self::$__SUB_OP_DISCHARGE;
        $t1['patient-discharge-queue'] = self::buildPatientBeingDischarged($patientDischargeQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientAdmissionBeingAdmittedPayload($conn, $systemTime1, $visit1, $login1, $patientAdmission1, $savingMode = true)
    {
        $t1 = self::registerOperation("patient-admission", $systemTime1, $visit1, $login1);
        $t1['sub-op'] = self::$__SUB_OP_ADMISSION;
        $t1['patient-admission'] = self::buildPatientBeingAdmitted($patientAdmission1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientAdmissionReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("patient-admission-queue", $receipt1, $systemTime1, $visit1, $login1);
        $t2 = is_null($receipt1->getTemporaryObjectHolder()) ? $receipt1->getInvoice()->getTemporaryObjectHolder() : $receipt1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ patient-admission-queue-receipt ] : Could not get Object reference");
        $t1['patient-admission-queue'] = self::buildPatientAdmission((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientAdmissionInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("patient-admission-queue", $invoice1, $systemTime1, $visit1, $login1);
        $t2 = $invoice1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ patient-admission-queue-invoice ] : Could not get Object reference");
        $t1['patient-admission-queue'] = self::buildPatientAdmission((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientAdmissionPayload($conn, $systemTime1, $visit1, $login1, $patientAdmissionQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("patient-admission-queue", $systemTime1, $visit1, $login1);
        $t1['patient-admission-queue'] = self::buildPatientAdmission($patientAdmissionQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientDrugDispensedPayload($conn, $systemTime1, $visit1, $login1, $dispensedPatientDrugManager1, $savingMode = true)
    {
        $t1 = self::registerOperation("dispensed-drugs", $systemTime1, $visit1, $login1);
        $t1['sub-op'] = self::$__SUB_OP_RESULTS;
        $t1['dispensed-drugs'] = self::buildDispensedDrugs($dispensedPatientDrugManager1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientDrugQueueReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("patient-drug-queue", $receipt1, $systemTime1, $visit1, $login1);
        $t2 = is_null($receipt1->getTemporaryObjectHolder()) ? $receipt1->getInvoice()->getTemporaryObjectHolder() : $receipt1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ patient-drug-queue-receipt ] : Could not get Object reference");
        $t1['patient-drug-queue'] = self::buildPatientDrugQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientDrugQueueInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("patient-drug-queue", $invoice1, $systemTime1, $visit1, $login1);
        $t2 = $invoice1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ patient-drug-queue-invoice ] : Could not get Object reference");
        $t1['patient-drug-queue'] = self::buildPatientDrugQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientDrugQueuePayload($conn, $systemTime1, $visit1, $login1, $patientDrugQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("patient-drug-queue", $systemTime1, $visit1, $login1);
        $t1['patient-drug-queue'] = self::buildPatientDrugQueue($patientDrugQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getMedicalDoctorExaminedDiseasesPayload($conn, $systemTime1, $visit1, $login1, $medicalDoctorExaminedDisease1, $savingMode = true)
    {
        $t1 = self::registerOperation("medical-doctor-examined-diseases", $systemTime1, $visit1, $login1);
        $t1['medical-doctor-examined-diseases'] = self::buildMedicalDoctorExaminedDiseases($medicalDoctorExaminedDisease1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNurseStationActivityPayload($conn, $systemTime1, $visit1, $login1, $nurseStationActivity1, $savingMode = true)
    {
        $t1 = self::registerOperation("nurse-station-activity", $systemTime1, $visit1, $login1);
        $t1['sub-op'] = self::$__SUB_OP_RESULTS;
        $t1['nurse-station-activity'] = self::buildNurseStationActivity($nurseStationActivity1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNurseStationQueueReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("nurse-station-queue", $receipt1, $systemTime1, $visit1, $login1);
        $t2 = is_null($receipt1->getTemporaryObjectHolder()) ? $receipt1->getInvoice()->getTemporaryObjectHolder() : $receipt1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ nurse-station-queue-receipt ] : Could not get Object reference");
        $t1['nurse-station-queue'] = self::buildNurseStationQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNurseStationQueueInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("nurse-station-queue", $invoice1, $systemTime1, $visit1, $login1);
        $t2 = $invoice1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ nurse-station-queue-invoice ] : Could not get Object reference");
        $t1['nurse-station-queue'] = self::buildNurseStationQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNurseStationQueuePayload($conn, $systemTime1, $visit1, $login1, $nurseStationQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("nurse-station-queue", $systemTime1, $visit1, $login1);
        $t1['nurse-station-queue'] = self::buildNurseStationQueue($nurseStationQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getExaminationResultsPayload($conn, $systemTime1, $visit1, $login1, $patientExaminationResults1, $savingMode = true)
    {
        $t1 = self::registerOperation("examination-results", $systemTime1, $visit1, $login1);
        $t1['sub-op'] = self::$__SUB_OP_RESULTS;
        $t1['examination-results'] = self::buildExaminationResults($patientExaminationResults1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getExaminationQueueReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("examination-queue", $receipt1, $systemTime1, $visit1, $login1);
        $t2 = is_null($receipt1->getTemporaryObjectHolder()) ? $receipt1->getInvoice()->getTemporaryObjectHolder() : $receipt1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ examination-queue-receipt ] : Could not get Object reference");
        $t1['examination-queue'] = self::buildExaminationQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getExaminationQueueInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("examination-queue", $invoice1, $systemTime1, $visit1, $login1);
        $t2 = $invoice1->getTemporaryObjectHolder();
        if (is_null($t2)) throw new Exception("[ examination-queue-invoice ] : Could not get Object reference");
        $t1['examination-queue'] = self::buildExaminationQueue((Registry::getInstance("Delta", $conn, $t2)));
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getExaminationQueuePayload($conn, $systemTime1, $visit1, $login1, $patientExaminationQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("examination-queue", $systemTime1, $visit1, $login1);
        $t1['examination-queue'] = self::buildExaminationQueue($patientExaminationQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getMedicalConsultationPayload($conn, $systemTime1, $visit1, $login1, $medicalDoctorConsultationQueue1, $savingMode = true)
    {
        $t1 = self::registerOperation("medical-consultation", $systemTime1, $visit1, $login1);
        $t1['medical-consultation'] = self::buildConsultationQueue($medicalDoctorConsultationQueue1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getTriagePayload($conn, $systemTime1, $visit1, $login1, $triage1, $savingMode = true)
    {
        $t1 = self::registerOperation("triage", $systemTime1, $visit1, $login1);
        $t1['triage'] = self::buildTriage($triage1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientContinuingVisitReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("continuing-visit", $receipt1, $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientContinuingVisitInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("continuing-visit", $invoice1, $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getPatientContinuingVisitPayload($conn, $systemTime1, $visit1, $login1, $savingMode = true)
    {
        $t1 = self::registerOperation("continuing-visit", $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNewPatientRegistrationReceiptPayload($conn, $systemTime1, $visit1, $login1, $receipt1, $savingMode = true)
    {
        $t1 = self::registerReceipt("new-registration", $receipt1, $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNewPatientRegistrationInvoicePayload($conn, $systemTime1, $visit1, $login1, $invoice1, $savingMode = true)
    {
        $t1 = self::registerInvoice("new-registration", $invoice1, $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    public static function getNewPatientRegistrationPayload($conn, $systemTime1, $visit1, $login1, $savingMode = true)
    {
        $t1 = self::registerOperation("new-registration", $systemTime1, $visit1, $login1);
        return ($savingMode ? (__object__::overwriteArray($t1, self::getCodesLookupArray())) : $t1);
    }
    //End -- Top Level Functions
    private static function registerValues($op, $values, $systemTime1, $visit1, $login1)
    {
        $t1 = self::registerOperation($op, $systemTime1, $visit1, $login1);
        $t1['values'] = $values;
        return $t1;
    }
    private static function registerReceipt($op, $receipt1, $systemTime1, $visit1, $login1)
    {
        $t1 = self::registerOperation($op, $systemTime1, $visit1, $login1);
        $t1['payment'] = self::buildReceipt($receipt1);
        $t1['sub-op'] = self::$__SUB_OP_RECEIPT;
        return $t1;
    }
    private static function registerInvoice($op, $invoice1, $systemTime1, $visit1, $login1)
    {
        $t1 = self::registerOperation($op, $systemTime1, $visit1, $login1);
        $t1['payment'] = self::buildInvoice($invoice1);
        $t1['sub-op'] = self::$__SUB_OP_INVOICE;
        return $t1;
    }
    private static function registerOperation($op, $systemTime1, $visit1, $login1)
    {
        return array(
            "main-op" => $op,
            "sub-op" => self::$__SUB_OP_DEFAULT,
            "time" => $systemTime1->getTimestamp(),
            "patient-visit" => (self::buildPatientVisit($visit1)),
            "attended-by" => (self::buildAttendedBy($login1))
        );
    }
    public static function buildPatientVisit($patientVisit1)
    {
        $visit1 = $patientVisit1;
        $case1 = $visit1->getPatientCase();
        $patient1 = $case1->getPatient();
        return array(
            'id' => $visit1->getVisitId(),
            'count' => $visit1->getVisitCount(),
            'patient-case' => array(
                'id' => $case1->getCaseId(),
                'patient' => array(
                    'id' => $patient1->getPatientId(),
                    'name' => $patient1->getPatientName()
                )
            )
        );
    }
    public static function buildInvoice($invoice1)
    {
        //1st time we might have only invoice 
        $currency1 = $invoice1->getCurrency();
        return array(
            "currency" => array(
                "id" => $currency1->getCurrencyId(),
                "code" => $currency1->getCode()
            ),
            "invoice" => array(
                "id" => $invoice1->getInvoiceId(),
                "number" => $invoice1->getInvoiceNumber(),
                "amount" => $invoice1->getAmount(),
                "total-paid" => $invoice1->getTotalPaid(),
                "balance" => $invoice1->getBalance()
            )
        );
    }
    public static function buildReceipt($receipt1)
    {
        //Accomodate only single snap-shot receipt
        $invoice1 = $receipt1->getInvoice();
        $currency1 = $invoice1->getCurrency();
        return array(
            "currency" => array(
                "id" => $currency1->getCurrencyId(),
                "code" => $currency1->getCode()
            ),
            "invoice" => array(
                "id" => $invoice1->getInvoiceId(),
                "number" => $invoice1->getInvoiceNumber(),
                "amount" => $invoice1->getAmount(),
                "total-paid" => $invoice1->getTotalPaid(),
                "balance" => $invoice1->getBalance()
            ),
            "receipt" => array(
                "id" => $receipt1->getReceiptId(),
                "number" => $receipt1->getReceiptNumber(),
                "amount" => $receipt1->getAmount()
            )
        );
    }
    public static function buildAttendedBy($login1)
    {
        return array(
            'id' => $login1->getLoginId(),
            'username' => $login1->getLoginName(),
            'name' => $login1->getFullName()
        );
    }
    public static function buildPatientCase($patientCase1)
    {
        return array(
            "id" => ($patientCase1->getCaseId()),
            "class" => ($patientCase1->getMyClassname())
        );
    }
    public static function buildPatientBeingDischarged($patientDischargeQueue1)
    {
        return array(
            "id" => ($patientDischargeQueue1->getQueueId()),
            "class" => ($patientDischargeQueue1->getMyClassname())
        );
    }
    public static function buildPatientBeingAdmitted($patientAdmission1)
    {
        return array(
            "id" => ($patientAdmission1->getAdmissionId()),
            "class" => ($patientAdmission1->getMyClassname())
        );
    }
    public static function buildPatientAdmission($patientAdmissionQueue1)
    {
        return array(
            "id" => ($patientAdmissionQueue1->getQueueId()),
            "class" => ($patientAdmissionQueue1->getMyClassname())
        );
    }
    public static function buildDispensedDrugs($dispensedPatientDrugManager1)
    {
        return array(
            "id" => $dispensedPatientDrugManager1->getManagerId(),
            "class" => $dispensedPatientDrugManager1->getMyClassname()
        );
    }
    public static function buildPatientDrugQueue($patientDrugQueue1)
    {
        return array(
            "id" => $patientDrugQueue1->getQueueId(),
            "class" => $patientDrugQueue1->getMyClassname()
        );
    }
    public static function buildMedicalDoctorExaminedDiseases($medicalDoctorExaminedDisease1)
    {
        return array(
            "id" => $medicalDoctorExaminedDisease1->getDiseaseId(),
            "class" => "MedicalDoctorExaminedDisease"
        );
    }
    public static function buildNurseStationActivity($nurseStationActivity1)
    {
        return array(
            "id" => $nurseStationActivity1->getActivityId(),
            "class" => "NurseStationActivity"
        );
    }
    public static function buildNurseStationQueue($nurseStationQueue1)
    {
        return array(
            "id" => $nurseStationQueue1->getQueueId(),
            "class" => "NurseStationQueue"
        );
    }
    public static function buildExaminationResults($patientExaminationResults1)
    {
        return array(
            "id" => $patientExaminationResults1->getResultsId(),
            "class" => "PatientExaminationResults"
        );
    }
    public static function buildExaminationQueue($patientExaminationQueue1)
    {
        return array(
            "id" => $patientExaminationQueue1->getQueueId(),
            "class" => "PatientExaminationQueue"
        );
    }
    public static function buildConsultationQueue($medicalDoctorConsultationQueue1)
    {
        return array(
            "id" => $medicalDoctorConsultationQueue1->getQueueId(),
            "class" => "MedicalDoctorConsultationQueue"
        );
    }
    public static function buildTriage($triage1)
    {
        return array(
            "id" => $triage1->getTriageId(),
            "class" => "Triage"
        );
    }
    public static function getCodesLookupArray()
    {
        return array(
            "main-op" => "a",
            "new-registration" => "b",
            "continuing-visit" => "c",
            "triage" => "d",
            "examination-request" => "e",
            "medical-consultation" => "f",
            "pharmacy" => "g",
            "scheduled-appointment" => "h",
            "referral-out" => "i",
            "payment" => "j",
            "invoice" => "k",
            "receipt" => "l",
            "id" => "m",
            "name" => "n",
            "number" => "o",
            "receipt" => "p",
            "values" => "q",
            "weight" => "r",
            "height" => "s",
            "systolic-blood-pressure" => "t",
            "dystolic-blood-pressure" => "u",
            "temperature" => "v",
            "pulse-rate" => "w",
            "respiration-rate" => "x",
            "oxygen-value" => "y",
            "examination-results" => "z",
            "service" => "aa",
            "standards" => "ab",
            "is-safe" => "ac",
            "Seen" => "ad",
            "Not Seen" => "ae",
            "Brownish" => "af",
            "Black" => "ag",
            "Dark Brown" => "ah",
            "Whitish" => "ai",
            "Green" => "aj",
            "Formed" => "ak",
            "Semi Formed" => "al",
            "Watery" => "am",
            "Yellow" => "an",
            "Yellowish" => "ao",
            "Cloud" => "ap",
            "Turbidity Deep Amber" => "aq",
            "Positive" => "ar",
            "Negative" => "as",
            "A Rh+" => "at",
            "A Rh-" => "au",
            "B Rh+" => "av",
            "B Rh-" => "aw",
            "O Rh+" => "ax",
            "O Rh-" => "ay",
            "amount" => "az",
            "total-paid" => "ba",
            "balance" => "bb",
            "patient-visit" => "bc",
            "count" => "bd",
            "patient-case" => "be",
            "patient" => "bf",
            "time" => "bg",
            "attended-by" => "bh",
            "class" => "bi",
            "consultation-queue" => "bj",
            "examination-queue" => "bk",
            "examination-results" => "bl",
            "MedicalDoctorConsultationQueue" => "bm",
            "PatientExaminationQueue" => "bn",
            "PatientExaminationResults" => "bo",
            "sub-op" => "bp",
            "default" => "bq",
            "triage" => "br",
            "Triage" => "bs",
            "username" => "bt",
            "NurseStationQueue" => "bu",
            "NurseStationActivity" => "bv",
            "nurse-station-queue" => "bw",
            "nurse-station-activity" => "bx",
            "currency" => "by",
            "code" => "bz",
            "medical-doctor-examined-diseases" => "ca",
            "MedicalDoctorExaminedDisease" => "cb",
            "patient-drug-queue" => "cc",
            "PatientDrugQueue" => "cd",
            "dispensed-drugs" => "ce",
            "DispensedPatientDrugManager" => "cf",
            "patient-admission-queue" => "cg",
            "PatientAdmissionQueue" => "ch",
            "patient-admission" => "ci",
            "PatientAdmission" => "cj",
            "patient-discharge-queue" => "ck",
            "PatientDischargeQueue" => "cl",
            "PatientCase" => "cm",
            "patient-case" => "cn"
        );
    }
    public static function getInverseCodesLookupArray()
    {
        return __object__::inverseArray(self::getCodesLookupArray());
    }
}
