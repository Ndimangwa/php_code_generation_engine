<?php
class MedicalDoctorConsultationQueue
{
    public function getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename, $settings = null, $shapingFunction = null)
    {
        $consultationQueue1 = $this;
        $conn = $this->conn;
        $horizontalRule = __object__::getCharacterStream(array('='), 78);
        //Pull Previous Saved
        $patientHistory1 = PatientHistory::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $generalExamination1 = GeneralExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $vitalSigns1 = VitalSigns::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $localExamination1 = LocalExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $systemicExamination1 = SystemicExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $provisionDiagnosis1 = ProvisionDiagnosis::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $examinationQueue1 = PatientExaminationQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $workingDiagnosis1 = WorkingDiagnosis::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $patientDrugQueue1 = PatientDrugQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $patientAdmissionQueue1 = PatientAdmissionQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        $patientOperationQueue1 = PatientOperationQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
        //Build Page 
        $isFirstPage = true;
        $captionArray1 = array(
            'header-title' => ( $profile1->getProfileName() ),
            'footer-title' => ( $profile1->getProfileName() )
        );
        //PatientHistory
        if (! is_null($patientHistory1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $patientHistory1, array(
                'chiefComplaints', 'reviewOfOtherServices', 'pastMedicalHistory', 'familyAndSocialHistory'
            ), array_merge($captionArray1, array(
                'table-title' => 'Patient History'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //General Examination
        if (! is_null($generalExamination1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $generalExamination1, array(
                'examination'
            ), array_merge($captionArray1, array(
                'table-title' => 'General Examination'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //Vital Signs
        if (! is_null($vitalSigns1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $vitalSigns1, array(
                'weight', 'height', 'temperature', 'bloodPressure', 'pulseRate', 'respirationRate', 'oxygenLevel'
            ), array_merge($captionArray1, array(
                'table-title' => 'Vital Signs'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //Local Examination
        if (! is_null($localExamination1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $localExamination1, array(
                'examination'
            ), array_merge($captionArray1, array(
                'table-title' => 'Local Examination'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //Systemic Examination
        if (! is_null($systemicExamination1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $systemicExamination1, array(
                'examination'
            ), array_merge($captionArray1, array(
                'table-title' => 'Systemic Examination'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        } 
        //Provision Diagnosis
        if (! is_null($provisionDiagnosis1))    {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $provisionDiagnosis1, array(
                'mainDisease', 'listOfDifferentialDiseases'
            ), array_merge($captionArray1, array(
                'table-title' => 'Provision Diagnosis'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //Examination Queue 
        if (! is_null($examinationQueue1))  {
            PDFLayout::writeBlock($pdf1, array($horizontalRule), 'L', $settings);
            $pdf1 = $examinationQueue1->getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            PDFLayout::writeBlock($pdf1, array($horizontalRule), 'L', $settings);
            $isFirstPage = false;
        }
        //Working Diagnosis
        if (! is_null($workingDiagnosis1))  {
            $pdf1 = __data__::createPDFTabularSummary($pdf1, $workingDiagnosis1, array(
                'listOfDiseases'
            ), array_merge($captionArray1, array(
                'table-title' => 'Working Diagnosis'
            )), $logofilename, $isFirstPage, true, $isFirstPage, $settings, null, null);
            $isFirstPage = false;
        }
         //Drug Queue 
         if (! is_null($patientDrugQueue1))  {
            PDFLayout::writeBlock($pdf1, array($horizontalRule), 'L', $settings);
            $pdf1 = $patientDrugQueue1->getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            PDFLayout::writeBlock($pdf1, array($horizontalRule), 'L', $settings);
            $isFirstPage = false;
        }
        //AdmissionQueue
        if (! is_null($patientAdmissionQueue1)) {
            //This use HTML to write PDF
            $pdf1 = $patientAdmissionQueue1->getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename, $isFirstPage, true, $isFirstPage, $settings, $shapingFunction);
            $isFirstPage = false;
        }
        //return PDF Object as usual
        return $pdf1;
    }
    public static function getConsultationQueuesForManager($conn, $managerId)
    {
        $query = "SELECT queueId FROM _medical_doctor_consultation_queue WHERE queueManager = '$managerId'";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e) {
        }
        $list = array();
        if (!is_null($records)) {
            foreach ($records['column'] as $record1) {
                $list[sizeof($list)] = new MedicalDoctorConsultationQueue("Delta", $record1['queueId'], $conn);
            }
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public static function getConsultationQueueForAccount($conn, $loginId, $managerId)
    {
        $queues = self::getConsultationQueuesForManager($conn, $managerId);
        if (is_null($queues)) return null;
        //Now we need to evaluate
        $myQueue1 = null;
        $nonAttendedQueue1 = null; //Should be one or empty
        foreach ($queues as $consultationQueue1) {
            //We are working with active queues only
            if ($consultationQueue1->isActive()) {
                if (($consultationQueue1->isAttended()) && ($consultationQueue1->getAttendedBy()->getLoginId() == $loginId)) {
                    $myQueue1 = $consultationQueue1;
                    break;
                } else if (!$consultationQueue1->isAttended()) {
                    if (!is_null($nonAttendedQueue1)) throw new Exception("You can have only one non-attended queue");
                    $nonAttendedQueue1 = $consultationQueue1;
                }
            }
        }
        //Now check case where we do not have attended-queue and it is only one 
        //$myQueue1 = (is_null($myQueue1) && (sizeof($queues) == 1) && ( ! $queues[0]->isAttended() )) ? $queues[0] : $myQueue1;
        $myQueue1 = is_null($myQueue1) ? $nonAttendedQueue1 : $myQueue1;
        return $myQueue1;
    }
    public static function canCreateNewConsultationQueue($conn, $managerId)
    {
        //You can create a consultationQueue, only if you have no any attended queue 
        $query = "SELECT COUNT(queueId) as recordCount FROM _medical_doctor_consultation_queue WHERE attended = 0";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
        } catch (Exception $e) {
            $records = null;
        }
        return ((!is_null($records) && ($records['column'][0]['recordCount'] == 0)) ? true : false);
    }
}
?>