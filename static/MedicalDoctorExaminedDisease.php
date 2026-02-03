<?php

use MedicalDoctorExaminedDisease as GlobalMedicalDoctorExaminedDisease;

class MedicalDoctorExaminedDisease
{
    public static function getExaminedDiseasesForExaminationQueue($conn, $examinationQueueId)
    {
        $tablename = self::getTablename();
        $list = array();
        try {
            $records = __data__::getSelectedRecords($conn, "SELECT diseaseId FROM $tablename WHERE examinationQueue = '$examinationQueueId'", false);
            foreach ($records['column'] as $row) {
                $list[sizeof($list)] = new MedicalDoctorExaminedDisease("Delta", $row['diseaseId'], $conn);
            }
        } catch (Exception $e) {
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public static function getDiseaseAnalysisUIForMedicalConsultationQueue($conn, $consultationQueueId)
    {
        $listOfExaminationQueues = null;
        $listOfExaminationQueues = PatientExaminationQueue::getExaminationQueuesForMedicalConsultationQueue($conn, $consultationQueueId);
        if (is_null($listOfExaminationQueues)) return "";
        $foundDisease = false;
        $window1 = "<div class=\"ui-disease-analysis my-2 card\">";
        $window1 .= "<div class=\"card-header bg-primary\">List of Diseases</div>";
        $window1 .= "<div class=\"card-body\">";
        foreach ($listOfExaminationQueues as $examination1) {
            $listOfExaminedDiseases = self::getExaminedDiseasesForExaminationQueue($conn, $examination1->getQueueId());
            if (is_null($listOfExaminationQueues)) continue;
            //Table defn
            $queueName = $examination1->getQueueName();
            $diseaseCount = 0;
            $table1 = "<div><div><h5>Examination: <i>$queueName</i></h5></div><div class=\"table-responsive mb-1\"><table class=\"table\"><thead><tr><th>S/N</th><th>Time</th><th>ICD 10 Code</th><th>Disease Name</th></tr></thead>";
            $enableBackground = false;
            foreach ($listOfExaminedDiseases as $disease1) {
                $styleInformation = "";
                if ($enableBackground) $styleInformation = "style=\"background-color: '#8c8c8c';\"";
                $table1 .= "<tbody $styleInformation>";
                $listOfICD10Diseases = $disease1->getListOfICD10Diseases();
                if (is_null($listOfICD10Diseases)) continue;
                //Must have disease 
                if (!$foundDisease) $foundDisease = true;
                foreach ($listOfICD10Diseases as $icd10) {
                    $sn = $diseaseCount + 1;
                    $time = $disease1->getTimeOfCreation()->getTimestamp();
                    $code = $icd10->getIcd10Code();
                    $dname = $icd10->getWhoFullDescription();
                    $table1 .= "<tr><th>$sn</th><td>$time</td><td>$code</td><td>$dname</td></tr>";
                    $diseaseCount++;
                }
                $table1 .= "</tbody>";
                $enableBackground = !$enableBackground;
            }
            $table1 .= "</table></div></div>";
            if ($diseaseCount > 0) $window1 .= $table1;
        }
        $window1 .= "</div>";
        $window1 .= "<div class=\"card-footer text-muted text-center\"></div>";
        $window1 .= "</div>";
        if (!$foundDisease) $window1 = "";
        return $window1;
    }
}
