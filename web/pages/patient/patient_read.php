<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    DETAILS
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patient";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $patient1 = new Patient("Delta Init", $_GET['id'], $conn);
                    ?>
                        <div class="fluid-container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="p-1">
                                        <?= __data__::createDetailsPage($nextPage, "Patient", array(
                                            'timeOfCreation', 'timeOfUpdation', 'registrationNumber', 'registrationType', 'status', 'surname', 'otherNames', 'dob', 'sex', 'address', 'religion', 'fatherName', 'occupation', 'tencelLeader', 'bloodGroup', 'admitted'
                                        ), $conn, $patient1->getPatientId(), null) ?><div class="mb-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?= $patient1->getCaseStatusScreen() ?><div class="mb-2"></div>
                                    <?= $patient1->getBalanceStatusScreen() ?>
                                </div>
                            </div>
                            <br/>
                           <!-- <div>
                                <form action="<?= $thispage ?>" method="GET">
                                    <input type="hidden" name="page" value="patient_update"/>
                                    <input type="hidden" name="new_visit" value="1"/>
                                    <input type="hidden" name="id" value="<?= $patient1->getPatientId() ?>"/>
                                    <?php 
                                        $caption = "Coming for Another Visit";
                                        $title = "This Patient Has been Attended previously, and now s/he is coming for another visit. The patient has an Open Case";
                                        $flag = Patient::$__NEW_VISIT;
                                        if ((new PatientCase("PC", $patient1->getCurrentCase(),$conn))->isClosed())   {
                                            $caption = "Start a New Case";
                                            $title = "This Patient has his/her previous case closed";
                                            $flag = Patient::$__NEW_CASE;
                                        }
                                    ?>
                                    <input type="hidden" name="flag" value="<?= $flag ?>"/>
                                    <input class="btn btn-danger btn-block" type="submit" data-toggle="tooltip" title="<?= $title ?>" value="<?= $caption ?>"/>
                                </form>
                            </div> 
                            <br/> -->
                            <div><a href="<?= $thispage ?>?page=patient_read&id=<?= $patient1->getPatientId() ?>&history=1" class="btn btn-block btn-primary border border-dotted-danger rounded p-1 m-1">View Patient History</a></div>
                        </div>
                    <?php
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Patient</a></i><br />
                        <span class="text-muted"><i>Rule: patient_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
