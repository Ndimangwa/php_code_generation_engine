<?php
$initialTabIndex = -1;
$erollback = false;
$analyticsGroup = Theatre::$__POST_OPERATION;
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->

        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    POST - THEATRE PROCEDURES
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=$page";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $operation1 = new PatientOperation("Delta", $_REQUEST['id'], $conn);
                        $queue1 = $operation1->getOperationQueue();
                        if ($queue1->getPatientCase()->isClosed()) throw new Exception("<i>The current case with the patient is closed. Kindly open a new case for this patient to continuing issuing services</i>");
                        $tabPrefix = "__alt_tab_prefex_today__";
                        //Define All Tabs
                        $tabPatientData = $tabPrefix . "patient_data";
                        $tabAnaesthesia = $tabPrefix . "anaesthesia";
                        $tabVitalSignsAndMedication = $tabPrefix . "vital_signs_and_medication";
                        //$tabConsentForm = $tabPrefix . "consent_form";
                        //$tabMoveToOperation = $tabPrefix . "move_to_operation";
                        $patient1 = $queue1->getPatient();
                    ?>
                        <div class="text-center">
                            <h3><i><?= $patient1->getPatientName() ?></i></h3>
                        </div>
                        <!--We assume case is open -->
                        <div class="tab-container container">
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a tab-index="<?= Theatre::$__TAB_PATIENT_DATA ?>" class="nav-link active" href="#<?= $tabPatientData ?>" data-bs-toggle="tab">Patient Data</a></li>
                                <li class="nav-item"><a tab-index="<?= Theatre::$__TAB_ANAESTHESIA ?>" class="nav-link" href="#<?= $tabAnaesthesia ?>" data-bs-toggle="tab">Anaesthesia</a></li>
                                <li class="nav-item"><a tab-index="<?= Theatre::$__TAB_VITAL_SIGNS_AND_MEDICATION ?>" class="nav-link" href="#<?= $tabVitalSignsAndMedication ?>" data-bs-toggle="tab">Vital Signs and Medication</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="<?= $tabPatientData ?>" class="tab-pane fade show active">
                                    <h3>Patient Data</h3>
                                    <?php include("page_patient_data.php"); ?>
                                </div>
                                <div id="<?= $tabAnaesthesia ?>" class="tab-pane fade">
                                    <h3>Anaesthesia</h3>
                                    <!--Add or include file-->
                                    <?php include("page_anaesthesia_record.php"); ?>
                                </div>
                                <div id="<?= $tabVitalSignsAndMedication ?>" class="tab-pane fade">
                                    <h3>Vital Signs and Medication</h3>
                                    <!--Add or include file-->
                                </div>
                            </div>
                        </div>
                    <?php
                    } catch (Exception $e) {
                        if ($erollback) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to List</a></i><br />
                        <span class="text-muted"><i>Rule: theatre_read_waiting</i></span>
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