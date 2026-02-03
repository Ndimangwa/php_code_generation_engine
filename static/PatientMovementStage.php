<?php

use PatientMovementStage as GlobalPatientMovementStage;

class PatientMovementStage
{
    public static function updatePatientStageAndQueue($conn, $systemTime1, $invoice1 /*can be null like in triage*/, $visit1, $login1, $rollback = false, $actionStageId = null, $bundleCode = null)
    {
        //We need to make sure we are pointed to the updated object
        $visit1 = new PatientVisit("Delta", $visit1->getVisitId(), $conn);
        $case1 = $visit1->getPatientCase();
        $nextStage1 = $case1->getNextStage();
        //We need a special case where invoice is custom and we need a label to label custom 
        if (!is_null($invoice1) && $invoice1->isCustom()) {
            //We simply do nothing 
        } else if (!is_null($nextStage1)) {
            switch ($nextStage1->getStageId()) {
                case (self::$__TRIAGE):
                    $bundleCode = __object__::getMD5CodedString("PatientTriageQueue", 32);
                    $case1->invalidateNextStage($rollback);
                    __data__::insert($conn, "PatientTriageQueue", array(
                        "timeOfCreation" => $systemTime1->getTimestamp(),
                        "timeOfUpdation" => $systemTime1->getTimestamp(),
                        "visit" => $visit1->getVisitId(),
                        "patientCase" => $visit1->getPatientCase()->getCaseId(),
                        "patient" => $visit1->getPatientCase()->getPatient()->getPatientId(),
                        "bundleCode" => $bundleCode
                    ), $rollback);
                    $case1->setCurrentStage($nextStage1->getStageId());
                    break;
                case (self::$__MEDICAL_DOCTOR_CONSULTATION):
                    $bundleCode = is_null($bundleCode) ? (__object__::getMD5CodedString("MedicalDoctorConsultationQueue", 32)) : $bundleCode;
                    //We need to initiate the ConsultationQueueManager
                    $consultationQueueManager1 = new ConsultationQueueManager("Delta", __data__::insert($conn, "ConsultationQueueManager", array(
                        "timeOfCreation" => $systemTime1->getTimestamp(),
                        "timeOfUpdation" => $systemTime1->getTimestamp(),
                        "managerName" => $case1->getPatient()->getPatientName(),
                        "visit" => $visit1->getVisitId(),
                        "patientCase" => $visit1->getPatientCase()->getCaseId(),
                        "patient" => $visit1->getPatientCase()->getPatient()->getPatientId(),
                        "bundleCode" => $bundleCode
                    ), $rollback), $conn);
                    $medicalConsultationQueue1 = new MedicalDoctorConsultationQueue("Hello", __data__::insert($conn, "MedicalDoctorConsultationQueue", array(
                        "timeOfCreation" => $systemTime1->getTimestamp(),
                        "timeOfUpdation" => $systemTime1->getTimestamp(),
                        "queueName" => $consultationQueueManager1->getManagerName(),
                        "visit" => $visit1->getVisitId(),
                        "patientCase" => $visit1->getPatientCase()->getCaseId(),
                        "patient" => $visit1->getPatientCase()->getPatient()->getPatientId(),
                        "medicalDoctor" => $visit1->getMedicalDoctor()->getDoctorId(),
                        "bundleCode" => $bundleCode,
                        "queueManager" => $consultationQueueManager1->getManagerId(),
                        "attendedBy" => $login1->getLoginId(),
                        "attended" => 0,
                        "active" => 1,
                        "applicationCounter" => (MedicalDoctorConsultationQueue::$__BLOCK_PATIENT_HISTORY)
                    ), $rollback), $conn);
                    //Need to correct attendedBy
                    $medicalConsultationQueue1->setAttendedBy($medicalConsultationQueue1->getMedicalDoctor()->getLogin()->getLoginId())->update($rollback);
                    PatientFile::addMedicalConsultationLog($conn, $systemTime1, $visit1, $login1, $medicalConsultationQueue1, __object__::getMD5CodedString("MedicalExaminationQueue"), $rollback);
                    $case1->setCurrentStage($nextStage1->getStageId());
                    $case1->setNextStage(self::$__LOOP_MEDICAL_DOCTOR_CONSULTATION);
                    break;
                case (self::$__LOOP_MEDICAL_DOCTOR_CONSULTATION):
                    if (is_null($invoice1)) throw new Exception("Reference Invoice were not found");
                    switch ($actionStageId) {
                        case (self::$__LABORATORY_EXAMINATION):
                            //$patientExaminationQueue1 = Registry::getInstance("Hello", $conn, $visit1->getTemporaryObjectHolder());
                            $monitor1 = null;
                            if (!is_null($invoice1->getMonitorReference())) {
                                $monitor1 = Registry::getInstance("Hello", $conn, $invoice1->getMonitorReference());
                            } else {
                                $monitor1 = PatientMovementStageMonitor::getMonitor($conn, $visit1->getVisitId(), self::$__LABORATORY_EXAMINATION, $invoice1->getTrackMonitor(), 0);
                            }
                            if (is_null($monitor1)) throw new Exception("There is no monitor associated with this visit");
                            $patientExaminationQueue1 = Registry::getInstance("Hello", $conn, $monitor1->getTemporaryObjectHolder());
                            if (is_null($patientExaminationQueue1)) throw new Exception("Could not retrieve the Examinatination Queue");
                            if ($patientExaminationQueue1->getPatientCase()->getCaseId() != $visit1->getPatientCase()->getCaseId()) throw new Exception("Case mismatch has been detected");
                            $patientExaminationQueue1->setPendingPayment(false)->update($rollback);
                            $monitor1->delete($rollback);
                            break;
                        case (self::$__NURSE_STATION):
                            $monitor1 = null;
                            if (!is_null($invoice1->getMonitorReference())) {
                                $monitor1 = Registry::getInstance("Hello", $conn, $invoice1->getMonitorReference());
                            } else {
                                $monitor1 = PatientMovementStageMonitor::getMonitor($conn, $visit1->getVisitId(), self::$__NURSE_STATION, $invoice1->getTrackMonitor(), 0);
                            }
                            if (is_null($monitor1)) throw new Exception("There is no monitor associated with this visit");
                            $nurseStationQueue1 = Registry::getInstance("Delta", $conn, $monitor1->getTemporaryObjectHolder());
                            if (is_null($nurseStationQueue1)) throw new Exception("Could not retrieve the Nurse Station Queue");
                            if ($nurseStationQueue1->getPatientCase()->getCaseId() != $visit1->getPatientCase()->getCaseId()) throw new Exception("Case mismatch has been detected");
                            $nurseStationQueue1->setPendingPayment(false)->update($rollback);
                            $monitor1->delete($rollback);
                            break;
                        case (self::$__PHARMACY):
                            $monitor1 = null;
                            if (!is_null($invoice1->getMonitorReference())) {
                                $monitor1 = Registry::getInstance("Hello", $conn, $invoice1->getMonitorReference());
                            } else {
                                $monitor1 = PatientMovementStageMonitor::getMonitor($conn, $visit1->getVisitId(), self::$__PHARMACY, $invoice1->getTrackMonitor(), 0);
                            }
                            if (is_null($monitor1)) throw new Exception("There is no monitor associated with this visit");
                            $patientDrugQueue1 = Registry::getInstance("Delta", $conn, $monitor1->getTemporaryObjectHolder());
                            if (is_null($patientDrugQueue1)) throw new Exception("Could not retrieve the Patient Drug Queue reference");
                            if ($patientDrugQueue1->getPatientCase()->getCaseId() != $visit1->getPatientCase()->getCaseId()) throw new Exception("Case mismatch has been detected");
                            $patientDrugQueue1->setPendingPayment(false)->update($rollback);
                            $monitor1->delete($rollback);
                            break;
                        case (self::$__ADMISSION):
                            $monitor1 = null;
                            if (!is_null($invoice1->getMonitorReference())) {
                                $monitor1 = Registry::getInstance("Hello", $conn, $invoice1->getMonitorReference());
                            } else {
                                $monitor1 = PatientMovementStageMonitor::getMonitor($conn, $visit1->getVisitId(), self::$__ADMISSION, $invoice1->getTrackMonitor(), 0);
                            }
                            if (is_null($monitor1)) throw new Exception("There is no monitor associated with this visit");
                            $patientAdmissionQueue1 = Registry::getInstance("Delta", $conn, $monitor1->getTemporaryObjectHolder());
                            if (is_null($patientAdmissionQueue1)) throw new Exception("Could not retrieve the Patient Admission Queue reference");
                            if ($patientAdmissionQueue1->getPatientCase()->getCaseId() != $visit1->getPatientCase()->getCaseId()) throw new Exception("Case mismatch has been detected");
                            $patientAdmissionQueue1->setPendingPayment(false)->update($rollback);
                            //Now we need to check if we have a PatientOperationQueue
                            $t1 = $patientAdmissionQueue1->getOperationQueueReference();
                            if (!is_null($t1)) {
                                $patientOperationQueue1 = Registry::getInstance("Delta", $conn, $t1);
                                if (!is_null($patientOperationQueue1)) {
                                    $patientOperationQueue1->setPendingPayment(false)->update($rollback);
                                }
                            }
                            $monitor1->delete($rollback);
                            break;
                        default:
                            throw new Exception("Could not decode instruction within a loop");
                    }
                    break;
                case (self::$__PHARMACY):
                    break;
                case (self::$__SCHEDULED_APPOINTMENT):
                    break;
                case (self::$__REFERRAL_OUT):
                    break;
            }
        }
        $case1->setExtraFilter(__object__::getMD5CodedString("Just to Make sure everytime there is an update", 32))->update($rollback);
    }
}
