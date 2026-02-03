<?php
$initialTabIndex = -1;
$dbTransactionON = false;
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->

        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    PATIENT MONITOR (MEDICAL CONSULTANT PORTAL)
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=medicaldoctor_consult";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $queue1 = new MedicalDoctorConsultationQueue("Delta", $_REQUEST['qid'], $conn);
                        if ($queue1->getPatientCase()->isClosed()) throw new Exception("<i>The current case with the patient is closed. Kindly open a new case for this patient to continuing issuing services</i>");
                        $tabPrefix = "__alt_tab_prefex_today__";
                    ?>
                        <div class="text-center">
                            <h3><i><?= $queue1->getPatient()->getPatientName() ?></i></h3> 
                        </div>
                        <!--We assume case is open -->
                        <div class="tab-container container">
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_HISTORY ?>" class="nav-link" href="#<?= $tabPrefix ?>patient_history" data-bs-toggle="tab">History</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_VITAL_SIGNS ?>" class="nav-link active" href="#<?= $tabPrefix ?>vital_signs" data-bs-toggle="tab">Vital Signs</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_MEDICAL_EXAMINATION ?>" class="nav-link" href="#<?= $tabPrefix ?>medical_examination" data-bs-toggle="tab">Medical Examination</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_DISEASE_ANALYSIS ?>" class="nav-link" href="#<?= $tabPrefix ?>disease_analysis" data-bs-toggle="tab">Disease Analysis</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_DRUGS_MANAGEMENT ?>" class="nav-link" href="#<?= $tabPrefix ?>drugs_management">Drugs Management</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_ADMISSION ?>" class="nav-link" href="#<?= $tabPrefix ?>admission" data-bs-toggle="tab">Admission</a></li>
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_NURSE_STATION ?>" class="nav-link" href="#<?= $tabPrefix ?>nurse_station">Nurse Station</a></li>
                                <!-- <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_CONSULTANT_COMMENTS ?>" class="nav-link" href="#<?= $tabPrefix ?>consultant_comments" data-bs-toggle="tab">Consultant Comments</a></li> -->
                                <li class="nav-item"><a tab-index="<?= MedicalDoctorConsultationQueue::$__TAB_CASE_MANAGEMENT ?>" class="nav-link" href="#<?= $tabPrefix ?>case_management">Case Management</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="<?= $tabPrefix ?>patient_history" class="tab-pane fade">
                                    <h3>Patient History</h3>
                                    <!-- Step -- 00 : Patient History File -->
                                    <?= PatientFile::getPatientHistory($conn, $queue1->getPatient(), $profile1, $login1) ?>
                                </div>
                                <div id="<?= $tabPrefix ?>vital_signs" class="tab-pane fade show active">
                                    <h3>Vital Signs</h3>
                                    <?php
                                    //Step -- 01 : Working with Vital Signs 
                                    $triage1 = Triage::getTriageForVisit($conn, $queue1->getVisit()->getVisitId());
                                    if (is_null($triage1)) throw new Exception("Could not get vital signs readings");
                                    echo UIView::wrap($triage1->getMyStatusTable(), "ui-triage-status");
                                    ?>
                                </div>
                                <div id="<?= $tabPrefix ?>medical_examination" class="tab-pane fade">
                                    <h3>Medical Examination</h3>
                                    <!-- Step -- 02 : Medical Examination -->
                                    <?php include("medicaldoctor_consult_ui_request_medical_examination.php"); ?>
                                </div>
                               <!-- <div id="<?= $tabPrefix ?>consultant_comments" class="tab-pane fade">
                                    <h3>Consultant Comments</h3>
                                </div> -->
                                <div id="<?= $tabPrefix ?>disease_analysis" class="tab-pane fade">
                                    <h3>Disease Management</h3>
                                    <!--Step XX: Disease Management-->
                                    <?php  include("medicaldoctor_disease_analysis.php"); ?>
                                </div>
                                <div id="<?= $tabPrefix ?>drugs_management" class="tab-pane fade">
                                    <h3>Drugs Management</h3>
                                    <!-- Step -- 04 : Drugs Management -->
                                    <?php include("medicaldoctor_consult_ui_request_drugs.php"); ?>
                                </div>
                                <div id="<?= $tabPrefix ?>admission" class="tab-pane fade">
                                    <h3>Admission</h3>
                                    <!-- Step -- 05 : Admission -->
                                    <?php include("medicaldoctor_consult_ui_request_admission.php"); ?>
                                </div>
                                <div id="<?= $tabPrefix ?>nurse_station" class="tab-pane fade">
                                    <h3>Nurse Station</h3>
                                    <!--Step 06 Nurse Station-->
                                    <?php include("medicaldoctor_consult_ui_request_nurse_station.php"); ?>
                                </div>
                                <div id="<?= $tabPrefix ?>case_management" class="tab-pane fade">
                                    <h3>Case Management</h3>
                                    <!-- Step -- 06 : Case Management -->
                                    <?php include("medicaldoctor_consult_ui_close_case.php"); ?>
                                </div>
                            </div>

                        </div>
                    <?php
                        //Step -- 02 : Medical Examinations
                        //Step -- 03 : Admission
                        //Step -- 04 : Case Management
                    } catch (Exception $e) {
                        if ($dbTransactionON) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Patient List</a></i><br />
                        <span class="text-muted"><i>Rule: medicaldoctor_consult</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $(function() {
            window.setTabbedNavigation($('div.tab-container'), <?= $initialTabIndex ?>);
        });
    })(jQuery);
</script>