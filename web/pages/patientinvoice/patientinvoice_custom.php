<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    BUILD CUSTOM INVOICE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patientinvoice";
                    $__SEQ_CATEGORIES = 1;
                    $__SEQ_SERVICES = 2;
                    $__SEQ_SUBMIT = 3;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $erollback = false;
                        if (isset($_REQUEST['seq']) && isset($_REQUEST['id']))    {
                            $patient1 = new Patient("Delta", $_REQUEST['id'], $conn);
                            $patientName = $patient1->getPatientName();
                            switch ($_REQUEST['seq'])  {
                                case $__SEQ_CATEGORIES:
                                    include("patientinvoice_custom_seq_categories.php");
                                    break;
                                case $__SEQ_SERVICES:
                                    include("patientinvoice_custom_seq_services.php");
                                    break;
                               case $__SEQ_SUBMIT:
                                    include("patientinvoice_custom_seq_submit.php");
                                    break;
                                default:
                                    throw new Exception("Could not decode sequence instruction");
                            }
                        } else {
                            //Default Landing Page
                            //Put Search UI
                            echo "<div class=\"text-center mb-1\"><h4>Search a Patient who is targeted by this invoice</h4></div>";
                            echo Patient::getASearchUI($thispage, array(
                                'surname', 'otherNames','dob', 'sex', 'phone', 'registrationType'
                            ), 0, false, array(
                                'external-link' => array(
                                    'caption' => 'Proceed',
                                    'href' => ( $thispage . "?page=$page&seq=$__SEQ_CATEGORIES&id=" )
                                )
                            ));
                        }
                    } catch (Exception $e) {
                        if ($erollback) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Invoice</a></i><br />
                        <span class="text-muted"><i>Rule: patientinvoice_custom</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>