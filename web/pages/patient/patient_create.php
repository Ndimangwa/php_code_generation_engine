<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    REGISTER A PATIENT
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=patient";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $patientType = null;
        if (isset($_REQUEST['patient_type'])) $patientType = $_REQUEST['patient_type'];
        if ($patientType == PatientRegistrationType::$__PATIENT_FULL_REGISTRATION)  {
            $formToDisplay = __data__::createDataCaptureForm($nextPage, "Patient", array(
                array('pname' => 'registrationNumber', 'caption' => 'Registration Number', 'required' => true, 'placeholder' => 'Registration Number', 'type' => 'switch-text', 'title' => 'Registration Number', 'disabled' => true),
                array('pname' => 'surname', 'caption' => 'Surname', 'required' => true, 'placeholder' => 'Surname'),
                array('pname' => 'otherNames', 'caption' => 'Other Names', 'required' => true, 'placeholder' => 'Other Names'),
                array('pname' => 'dob', 'type' => 'date', 'caption' => 'Date of Birth', 'required' => true),
                array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Sex'),
                array('pname' => 'medicalDoctor', 'caption' => 'Consultant', 'required' => true),
                array('pname' => 'insurance', 'type' => 'switch-select', 'caption' => 'Medical Insurance', 'required' => true, 'use-class' => 'PatientCase', 'title' => 'If you have a Medical Insurance Select', 'disabled' => true, 'placeholder' => 'Medical Insurance'),
                array('pname' => 'address', 'caption' => 'Address', 'required' => true, 'placeholder' => 'Address'),
                array('pname' => 'tribe', 'caption' => 'Tribe', 'required' => true, 'placeholder' => 'Patient Tribe'),
                array('pname' => 'religion', 'caption' => 'Religion', 'required' => true, 'placeholder' => 'Religion'),
                array('pname' => 'fatherName', 'caption' => 'Father Name', 'required' => true, 'placeholder' => 'Father Name'),
                array('pname' => 'occupation', 'caption' => 'Occupation', 'required' => false, 'placeholder' => 'Occupation'),
                array('pname' => 'tencellLeader', 'caption' => 'Balozi', 'required' => false, 'placeholder' => 'Balozi/Tencell Leader'),
                array('pname' => 'phone', 'caption' => 'Phone', 'required' => false, 'placeholder' => '07XXXXXXXX')
            ), "Register Patient", "create", $conn, 0, array('registrationType' => $patientType,'__modal_title__' => 'PATIENT FULL REGISTRATION REPORT', 'timeOfCreation' => $systemTime1->getGUIDateOnlyFormat(), 'timeOfUpdation' => $systemTime1->getGUIDateOnlyFormat()), null, "../server/servicePatientProcessor");
            echo $formToDisplay;
        } else if ($patientType == PatientRegistrationType::$__PATIENT_MIN_REGISTRATION)    {
            $formToDisplay = __data__::createDataCaptureForm($nextPage, "Patient", array(
                array('pname' => 'registrationNumber', 'caption' => 'Registration Number', 'required' => true, 'placeholder' => 'Registration Number', 'type' => 'switch-text', 'title' => 'Registration Number', 'disabled' => true),
                array('pname' => 'surname', 'caption' => 'Surname', 'required' => true, 'placeholder' => 'Surname'),
                array('pname' => 'otherNames', 'caption' => 'Other Names', 'required' => true, 'placeholder' => 'Other Names'),
                array('pname' => 'dob', 'type' => 'date', 'caption' => 'Date of Birth', 'required' => true),
                array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Sex'),
            array('pname' => 'medicalDoctor', 'caption' => 'Consultant', 'required' => true/*, 'filter-op' => 'not', 'filter' => array('specialist' => array('1'))*/),
                array('pname' => 'insurance', 'type' => 'switch-select', 'caption' => 'Medical Insurance', 'required' => true, 'use-class' => 'PatientCase', 'title' => 'If you have a Medical Insurance Select', 'disabled' => true, 'placeholder' => 'Medical Insurance'),
                array('pname' => 'address', 'caption' => 'Address', 'required' => true, 'placeholder' => 'Address')
            ), "Register Patient", "create", $conn, 0, array('registrationType' => $patientType,'__modal_title__' => 'PATIENT MIN REGISTRATION REPORT', 'timeOfCreation' => $systemTime1->getGUIDateOnlyFormat(), 'timeOfUpdation' => $systemTime1->getGUIDateOnlyFormat()), null, "../server/servicePatientProcessor");
            echo $formToDisplay;
        } else if ($patientType == PatientRegistrationType::$__PATIENT_TRANSFER_IN) {
            $formToDisplay = __data__::createDataCaptureForm($nextPage, "Patient", array(
                array('pname' => 'registrationNumber', 'caption' => 'Registration Number', 'required' => true, 'placeholder' => 'Registration Number', 'type' => 'switch-text', 'title' => 'Registration Number', 'disabled' => true),
                array('pname' => 'surname', 'caption' => 'Surname', 'required' => true, 'placeholder' => 'Surname'),
                array('pname' => 'otherNames', 'caption' => 'Other Names', 'required' => true, 'placeholder' => 'Other Names'),
                array('pname' => 'dob', 'type' => 'date', 'caption' => 'Date of Birth', 'required' => true),
                array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Sex'),
                array('pname' => 'medicalDoctor', 'caption' => 'Consultant', 'required' => false),
                array('pname' => 'insurance', 'type' => 'switch-select', 'caption' => 'Medical Insurance', 'required' => true, 'use-class' => 'PatientCase', 'title' => 'If you have a Medical Insurance Select', 'disabled' => true, 'placeholder' => 'Medical Insurance'),
                array('pname' => 'address', 'caption' => 'Address', 'required' => true, 'placeholder' => 'Address')
            ), "Register Patient", "create", $conn, 0, array('registrationType' => $patientType,'__modal_title__' => 'PATIENT TRANSFER REGISTRATION REPORT', 'timeOfCreation' => $systemTime1->getGUIDateOnlyFormat(), 'timeOfUpdation' => $systemTime1->getGUIDateOnlyFormat()), null, "../server/servicePatientProcessor");
            echo $formToDisplay;
        } else {
?>
    <div class="container">
            <div class="row">
                <div class="col-md-4"><a class="btn btn-block btn-outline-primary mb-2" data-toggle="tooltip" title="Register a Patient who has been transfered from other Hospital" href="<?= $thispage ?>?page=patient_create&patient_type=<?= PatientRegistrationType::$__PATIENT_TRANSFER_IN ?>">TRANSFER IN</a></div>
                <div class="col-md-4"><a class="btn btn-block btn-outline-primary mb-2" data-toggle="tooltip" title="Register a Patient who is temporary" href="<?= $thispage ?>?page=patient_create&patient_type=<?= PatientRegistrationType::$__PATIENT_MIN_REGISTRATION ?>">MIN REGISTRATION</a></div>
                <div class="col-md-4"><a class="btn btn-block btn-outline-primary mb-2" data-toggle="tooltip" title="Register a Patient who is permanent" href="<?= $thispage ?>?page=patient_create&patient_type=<?= PatientRegistrationType::$__PATIENT_FULL_REGISTRATION ?>">FULL REGISTRATION</a></div>
            </div>
    </div>
<?php
        }
        /*$formToDisplay = __data__::createDataCaptureForm($nextPage, "JobTitle", array(
            array('pname' => 'jobName', 'caption' => 'Job Title', 'required' => true, 'placeholder' => 'Name of Job Title')
        ), "create", $conn, 0, array('__modal_title__' => 'Job Title Creation Report'), null);
        echo $formToDisplay;*/
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Patient</a></i><br/>
                        <span class="text-muted"><i>Rule: patient_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>