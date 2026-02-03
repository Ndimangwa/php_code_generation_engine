<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("DiseaseAnalysis", 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_DISEASE_ANALYSIS))) {
    $conn->beginTransaction();
    $dbTransactionON = true;
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getDiseaseAnalysisFilter()) throw new Exception("Multiple Submission for same queue detected");
    $medicalDoctorQueue1->setDiseaseAnalysisFilter($bundleCode)->update(false);
    //Get reference Examination for the aim of extraction fields
    $examinationQueue1 = new PatientExaminationQueue("Delta", $_POST['examinationQueue'], $conn);
    //Extract visit, patientCase, patient and bundleCode from referenceExamination
    $mainColArray1 = $examinationQueue1->getMyPayload(array('visit', 'patientCase', 'patient', 'bundleCode'));
    $mainColArray1 = array_merge($mainColArray1, array(
        'timeOfCreation' => $systemTime1->getTimestamp(),
        'timeOfUpdation' => $systemTime1->getTimestamp()
    ));
    //Check if comments not null and insert to MedicalComment
    $comments = trim($_POST['comments']);
    $comment1 = null;
    if ($comments != "") {
        $colArray1 = array_merge($mainColArray1, array(
            'comments' => $comments
        ));
        $comment1 = new MedicalComment("Comment", __data__::insert($conn, "MedicalComment", $colArray1, !$dbTransactionON, Constant::$default_select_empty_value), $conn);
    }
    //Update MedicalDoctorExaminedDisease
    $colArray1 = array_merge($mainColArray1, array(
        'listOfICD10Diseases' => implode(",", $_POST['listOfICD10Diseases']),
        'examinationQueue' => $examinationQueue1->getQueueId()
    ));
    if (!is_null($comment1)) {
        $colArray1 = array_merge($colArray1, array(
            'medicalComment' => $comment1->getCommentId()
        ));
    }
    $medicalDoctorExaminedDisease1 = new MedicalDoctorExaminedDisease("Delta", __data__::insert($conn, "MedicalDoctorExaminedDisease", $colArray1, !$dbTransactionON, Constant::$default_select_empty_value), $conn);
    //Update PatientFile 
    PatientFile::addMedicalDoctorExaminedDiseaseLog($conn, $systemTime1, $medicalDoctorExaminedDisease1->getVisit(), $login1, $medicalDoctorExaminedDisease1, $medicalDoctorExaminedDisease1->getBundleCode(), !$dbTransactionON);
    $conn->commit();
    $dbTransactionON = false;
    //Successful report
    echo UICardView::getSuccesfulReportCard("Disease Updated", "You have successful update the disease(s) for the patient");
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setDiseaseAnalysisFilter($bundleCode)->update(true);
?>
    <div class="disease-analysis-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4>Disease Analysis</h4>
        </div>
        <div class="disease-analysis-content">
            <?php
            //If any previous disease list , list here
            $diseaseListUI = MedicalDoctorExaminedDisease::getDiseaseAnalysisUIForMedicalConsultationQueue($conn, $medicalDoctorQueue1->getQueueId());
            if ($diseaseListUI != "") {
                echo UIView::wrap($diseaseListUI);
            }
            //Begin -- Record Other Diseases            
            echo UICardView::wrap(__data__::createDataCaptureForm($thispage, "MedicalDoctorExaminedDisease", array(
                array('pname' => 'examinationQueue', 'caption' => 'Reference Examination', 'required' => true, 'filter' => array('patientCase' => array($medicalDoctorQueue1->getPatientCase()->getCaseId()), 'patient' => array($medicalDoctorQueue1->getPatient()->getPatientId()))),
                array('pname' => 'listOfICD10Diseases', 'caption' => 'ICD10 Disease', 'required' => true, 'items-count' => array('minimum' => 1, 'maximum' => 100), 'include-columns' => array('icd10Code' => array('caption' => 'ICD 10 Code'), 'whoFullDescription' => array('caption' => 'Description (WHO)'))),
                array('pname' => 'comments', 'caption' => 'Comments', 'use-class' => 'MedicalComment', 'required' => false)
            ), "Record Disease(s)", "create", $conn, 0, array(
                "page" => $page,
                "qid" => $medicalDoctorQueue1->getQueueId(),
                "submit" => 1,
                "qtype" => (MedicalDoctorConsultationQueue::$__TAB_DISEASE_ANALYSIS),
                "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_DISEASE_ANALYSIS),
                "efilter" => $medicalDoctorQueue1->getDiseaseAnalysisFilter()
            ), null, null, "btn-disease-analysis", $thispage, true));
            //End -- Record Other Diseases
            ?>
        </div>
    </div>
<?php
}
?>
