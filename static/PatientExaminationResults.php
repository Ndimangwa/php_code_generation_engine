<?php

use PatientExaminationResults as GlobalPatientExaminationResults;

class PatientExaminationResults
{
    public static function getListOfResultsForExaminationQueue($conn, $examinationQueueId)
    {
        $listOfResults = array();
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, "SELECT resultsId FROM _patient_examination_results WHERE queueId = '$examinationQueueId'", false);
        } catch (Exception $e)  {
            $records = null;
        }
        if (is_null($records)) return null;
        foreach ($records['column'] as $row) {
            $listOfResults[sizeof($listOfResults)] = new PatientExaminationResults("Delta", $row['resultsId'], $conn);
        }
        if (sizeof($listOfResults) == 0) $listOfResults = null;
        return $listOfResults;
    }
    public static function getListOfResultsForService($conn, $examinationQueueId, $serviceId)
    {
        $listOfResults = array();
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, "SELECT resultsId FROM _patient_examination_results WHERE queueId = '$examinationQueueId' AND serviceId = '$serviceId'", false);
        } catch (Exception $e)  {
            $records = null;
        }
        foreach ($records['column'] as $row) {
            $listOfResults[sizeof($listOfResults)] = new PatientExaminationResults("Delta", $row['resultsId'], $conn);
        }
        if (sizeof($listOfResults) == 0) $listOfResults = null;
        return $listOfResults;
    }
    public static function getListOfApprovedResultsForService($conn, $examinationQueueId, $serviceId, $approved = null) {
        $listOfResults = self::getListOfResultsForService($conn, $examinationQueueId, $serviceId);
        if (is_null($listOfResults) || is_null($approved)) return $listOfResults;
        $newList1 = array();
        foreach ($listOfResults as $results1)   {
            if ($results1->isApproved() == $approved)   {
                $newList1[sizeof($newList1)] = $results1;
            }
        }
        if (sizeof($newList1) == 0) $newList1 = null;
        return $newList1;
    }
    public static function getResultsUIForExaminationQueue($thispage, $conn, $examinationQueueId)
    {
        $profile1 = new Profile("Delta", __data__::$__PROFILE_INIT_ID, $conn);
        $queue1 = new PatientExaminationQueue("Delta", $examinationQueueId, $conn);
        $patient1 = $queue1->getPatient();
        $age = $patient1->calculateAge($profile1);
        $sex = $patient1->getSex()->getSexName();
        $window1 = "<div class=\"ui-examination-results\"><div><h5>Patient Examination Queue</h5><h5><span>Age : <b>$age</b></span> &nbsp; &nbsp; &nbsp; &nbsp;<span>Sex : <b>$sex</b></span></h5></div><div class=\"p-1 pl-2\">";
        $listOfServices = $queue1->getListOfServices();
        if (!is_null($listOfServices)) {
            $resultsNotYetTable = "<table class=\"table\"><thead class=\"thead-secondary\"><tr><th colspan=\"3\">Results Not Yet List</th></tr><tr><th>S/N</th><th></th><th>Service Name</th></tr></thead><tbody>";
            $showResultsNotYetTable = false;
            $resultsNotYetNextIndex = 1;
            //Working with Policies
            $cApproveResults = Authorize::isAllowable2($conn, "medicaldoctor_approve_results", "normal", "donotsetlog", null, null);
            $cRepeatExamination = Authorize::isAllowable2($conn, "medicaldoctor_repeat_examination", "normal", "donotsetlog", null, null);
            foreach ($listOfServices as $service1) {
                $serviceName = $service1->getServiceName();
                $listOfResults = null;
                try { $listOfResults = self::getListOfApprovedResultsForService($conn, $queue1->getQueueId(), $service1->getServiceId(), false);} catch (Exception $e)    { $listOfResults = null; }
                $listOfApprovedResults = null;
                try { $listOfApprovedResults = self::getListOfApprovedResultsForService($conn, $queue1->getQueueId(), $service1->getServiceId(), true); } catch (Exception $e) { $listOfApprovedResults = null; } 
                if (!is_null($listOfResults)) {
                    $window1 .= "<div class=\"ui-sys-service mb-2 p-1 border border-primary bg-light\"><h5><b><i>$serviceName</i></b></h5><div class=\"pl-8\">";
                    $count = 0;
                    $window1 .= "<div class=\"row\">";
                    foreach ($listOfResults as $results1) {
                        $standard1 = $results1->getExaminationStandard();
                        $index = $count + 1;
                        $sname = $standard1->getServiceName();
                        $value = $results1->getGeneralValue();
                        $unit = $standard1->getUnitOfMeasurement();
                        $unit = is_null($unit) ? "" : (trim($unit) == "" ? "" : ($unit));
                        $time = $results1->getTimeOfCreation()->getTimestamp();
                        $type = $standard1->getTypeOfValue();
                        $status = "Problem";
                        $bgcolor = "bg-danger";
                        if ($results1->isSafeValue())   {
                            $status = "Okay";
                            $bgcolor = "bg-primary";
                        }
                        $window1 .= "<div class=\"col-md-6\"><div class=\"card m-1 p-1\"><div class=\"card-header $bgcolor\"><div class=\"card-title\">$sname</div></div><div class=\"card-body\">";
                        $window1 .= "<div class=\"mb-1\"><span><b>Results : </b><b style=\"font-size: 1.1em;\"><u>$value $unit</u></b></span>, &nbsp; &nbsp; <span><b>type : </b>$type</span></div>";
                        if ($standard1->isEnumerated()) {
                            $enumeratedList = $standard1->getListOfEnumeratedValues();
                            $safeValue = $standard1->getSafeEnumeratedValue();
                            $safeValue = is_null($safeValue) ? "" : ($safeValue == "" ? "" : "(<b>Safe Value : </b> $safeValue)");
                            $window1 .= "<div style=\"font-size: 0.9em; font-style: italic; text-align: center;\"><b>Options : </b> $enumeratedList  &nbsp;&nbsp; $safeValue</div>";
                        } else {
                            $minValue = $standard1->getMinimumSafeValue();
                            $maxValue = $standard1->getMaximumSafeValue();
                            $window1 .= "<div style=\"font-size: 0.9em; font-style: italic; text-align: center;\"><b>Valid Range : </b>( <b>Min :</b> $minValue , <b>Max :</b> $maxValue )</div>";
                        }
                        //AgeCategory and Sex
                        $sex1 = $standard1->getSex();
                        $ageCategory1 = $standard1->getAgeCategory();
                        if (! (is_null($sex1) && is_null($ageCategory1)))   {
                            $window1 .= "<div style=\"font-size: 0.9em; font-style: italic;\">";
                            if (! is_null($sex1))   {
                                $sex = $sex1->getSexName();
                                $window1 .= " <b>Sex : </b> $sex";
                            }
                            if (! is_null($ageCategory1))   {
                                $categoryName = $ageCategory1->getCategoryName();
                                $minAge = $ageCategory1->getMinimumAge();
                                $maxAge = $ageCategory1->getMaximumAge();
                                $window1 .= " <b>Age Category : $categoryName ( MinAge: $minAge, MaxAge : $maxAge )</b>";
                            }
                            $window1.= "</div>";
                        }
                        $window1 .= "</div><div class=\"card-footer\"></div></div></div>";
                        $count++;    
                    }
                    $window1 .= "</div>";
                    //Add Approve List Here, this is per service
                    if ($cApproveResults || $cRepeatExamination)    {
                        $window1 .= "<div class=\"border border-dotted-secondary\"><div class=\"my-1 row\">";
                        $page = $thispage."?page=medicaldoctor_consult&repeat=1&qid=".($queue1->getQueueId())."&id=".($service1->getServiceId());
                        $page .= "&counter=".( MedicalDoctorConsultationQueue::$__BLOCK_INVESTIGATION );
                        if ($cRepeatExamination) $window1 .= "<div class=\"col-md-6\"><a data-toggle=\"tooltip\" title=\"Request for Laboratory Examination for $serviceName to be repeated\" href=\"$page\" class=\"mx-1 btn btn-danger btn-block\">REPEAT EXAMINATION</a></div>";
                        $page = $thispage."?page=medicaldoctor_consult&approve=1&qid=".($queue1->getQueueId())."&id=".($service1->getServiceId());
                        $page .= "&counter=".( MedicalDoctorConsultationQueue::$__BLOCK_INVESTIGATION );
                        if ($cApproveResults) $window1 .= "<div class=\"col-md-6\"><a data-toggle=\"tooltip\" title=\"Approve Results for $serviceName\" class=\"mx-1 btn btn-primary btn-block\" href=\"$page\">APPROVE RESULTS</a></div>";
                        $window1 .= "</div></div>";
                    }
                    $window1 .= "</div></div>"; //Closing .ui-sys-service
                } else if (! is_null($listOfApprovedResults))    {
                    //Just Do Nothing to avoid Service with Approved Results to be displayed on the else part
                } else {
                    //This Service has just submitted and no specific results
                    //$window1 .= "<div class=\"mt-1\" style=\"font-size: 1.0em; font-style: italic; display: flex; justify-content: space-between;\"><span>$index</span><span class=\"ml-1\">$serviceName</span><span class=\"ml-1\">(Results Not Yet)</span></div>";
                    $showResultsNotYetTable = true;
                    $resultsNotYetTable .= "<tr><th>$resultsNotYetNextIndex</th><td><span class=\"border border-dark rounded-circle p-1 bg-secondary\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td>$serviceName</td></tr>";
                    $resultsNotYetNextIndex++;
                }
            }
            $resultsNotYetTable .= "</tbody></table>";
            if ($showResultsNotYetTable) $window1 .= $resultsNotYetTable;
        }
        $window1 .= "</div></div>";
        return $window1;
    }
}
