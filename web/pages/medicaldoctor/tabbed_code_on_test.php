<div class="tab-container container">
                            <ul id="myTab" class="nav nav-tabs">
                                <li class="nav-item active"><a class="nav-link" href="#<?= $tabPrefix ?>patient_history" data-bs-toggle="tab">History</a></li>
                                <li class="nav-item"><a class="nav-link" href="#<?= $tabPrefix ?>vital_signs" data-bs-toggle="tab">Vital Signs</a></li>
                                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="tab">Medical Examination</a></li>
                                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="tab">Medical Report</a></li>
                                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="tab">Admission</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="<?= $tabPrefix ?>patient_history" class="tab-pane fade in active">
                                    <h3>Patient History</h3>
                                    <!-- Step -- 00 : Patient History File -->
                                </div>
                                <div id="<?= $tabPrefix ?>vital_signs" class="tab-pane fade">
                                    <h3>Vital Signs</h3>
                                    <?php
                                    //Step -- 01 : Working with Vital Signs 
                                    $triage1 = Triage::getTriageForVisit($conn, $queue1->getVisit()->getVisitId());
                                    if (is_null($triage1)) throw new Exception("Could not get vital signs readings");
                                    echo UIView::wrap($triage1->getMyStatusTable(), "ui-triage-status");
                                    ?>
                                </div>
                            </div>

                        </div>