<div>
<?php 
echo __data__::createDataCaptureForm($thispage, "PatientOperation", array(
    array('pname' => 'surgeon', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getSurgeon() )), 'caption' => 'Surgeon', 'required' => true, 'filter' => array('specialist' => array('1'))),
    array('pname' => 'anaesthetist', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getAnaesthetist() )), 'caption' => 'Anaesthetist', 'required' => true, 'filter' => array('specialist' => array('1'))),
    array('pname' => 'typeOfAnaesthetist', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getTypeOfAnaesthetist() )), 'caption' => 'Type of Anaesthetist', 'required' => true),
    array('pname' => 'theatre', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getTheatre() )), 'caption' => 'Theatre', 'required' => true),
    array('pname' => 'surgeryTime', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getSurgeryTime() )), 'caption' => 'Surgery Date', 'required' => false, 'type' => 'date'),
    array('pname' => 'surgeryDuration', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getSurgeryDuration() )), 'caption' => 'Surgery Duration', 'required' => false),
    array('pname' => 'startCuttingTime', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getStartCuttingTime() )), 'caption' => 'Start Cutting Time', 'required' => false, 'type' => 'date'),
    array('pname' => 'endCuttingTime', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getEndCuttingTime() )), 'caption' => 'End Cutting Time', 'required' => false, 'type' => 'date'),
    array('pname' => 'position', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getPosition() )), 'caption' => 'Position', 'required' => false),
    array('pname' => 'incision', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getIncision() )), 'caption' => 'Incision', 'required' => false),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'procedureDescriptionAndClosure', 'caption' => 'Procedure Description And Closure', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getProcedureDescriptionAndClosure())) ? ( $patientOperation1->getProcedureDescriptionAndClosure()->getComments() ) : null )),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'identificationOfProsthesis', 'caption' => 'Identification Of Prosthesis', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getIdentificationOfProsthesis())) ? ( $patientOperation1->getIdentificationOfProsthesis()->getComments() ) : null )),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'estimatedBloodLoss', 'caption' => 'Estimated Blood Loss', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getEstimatedBloodLoss())) ? ( $patientOperation1->getEstimatedBloodLoss()->getComments() ) : null )),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'problemsAndComplications', 'caption' => 'Problems and Complications', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getProblemsAndComplications())) ? ( $patientOperation1->getProblemsAndComplications()->getComments() ) : null )),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'drain', 'caption' => 'Drain', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getDrain())) ? ( $patientOperation1->getDrain()->getComments() ) : null )),
    array('pname' => 'comments', 'use-class' => 'MedicalComment', 'use-name' => 'technicalComments', 'caption' => 'Technical Comments (Any)', 'required' => false, 'type' => 'textarea', 'value' => ( (! is_null($patientOperation1) && ! is_null($patientOperation1->getTechnicalComments())) ? ( $patientOperation1->getTechnicalComments()->getComments() ) : null )),
    array('pname' => 'status', 'value' => (is_null($patientOperation1) ? null : ( $patientOperation1->getStatus() )), 'caption' => 'Surgery Status', 'required' => true)
), "Record Data", "create", $conn, 0, array(
    'page' => $page,
    'qid' => ( $patientOperationQueue1->getQueueId() ),
    'serviceId' => ( $service1->getServiceId() ),
    'submit' => 1,
    'efilter' => ( $patientOperationQueue1->getFirstFilter() )
), null, null, "me-cnt", $thispage, true);
?>
</div>