<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <span data-toggle="tooltip" title="If the decision is not made, at the User Account Level, at the Job Title Level, at the Group Level (begining with directly connected group to the to of group hierarchy) then the decision will be made based on the settings of the Last Resort of Do Not Care">LAST RESORT OF DO NOT CARE</span>
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    try {
                        $formid = "__lastresortdonotcare_form__";
                        $errorid = "__lastresortdonotcare_error__";
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        if (isset($_REQUEST['warnings'])) {
                            $oldvalue = $_REQUEST['oldvalue'];
                            //Show Warning Page , then only submit 
                            $lastresort = ContextPosition::$__DENY; if (isset($_REQUEST['lastresort'])) $lastresort = ContextPosition::$__ALLOW;
                            if ($oldvalue == $lastresort) throw new Exception("Nothing to Update");
                            $buttonText = "Default DENY";
                            $warningText = "You are about to push the system into CLOSED MODE. This is the most safiest form and the most tight form of the system security, which means all actions whose rules are not specified as ALLOW or DENY will be DENIED BY DEFAULT";
                            if ($lastresort == ContextPosition::$__ALLOW)   {
                                $buttonText = "Default ALLOW";
                                $warningText = "You are about to push the system into OPEN MODE. This will make all actions whore rules are not specified as ALLOW or DENY to be ALLOWED BY DEFAULT";
                            }
                    ?>
                            <div class="lastresortdonotcare">
                                <div class="alert alert-danger" role="alert"><?= $warningText ?></div>
                                <form action="POST" id="<?= $formid ?>">
                                    <input type="hidden" name="__modal_title__" value="Last Resort ALLOW/DENY"/>    
                                    <input type="hidden" name="__lastresort__" value="<?= $lastresort ?>"/>      
                                    <div id="<?= $errorid ?>" class="p-2 ui-sys-error-message"></div>
                                    <div class="text-center text-md-right mb-2">
                                        <button type="button" class="btn btn-danger btn-form-submit btn-send-dialog-ajax" data-server-script="serviceLastResortDoNotCare" data-form-submit="<?= $formid ?>" data-form-error="<?= $errorid ?>" data-next-page="<?= $thispage ?>"><?= $buttonText ?></button>
                                    </div>
                                </form>
                            </div>
                        <?php
                        } else {
                            //Show 1st Landing Page 
                            $jresults1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
                                array('_contextManager'),
                                array('defaultXValue'),
                                null
                            ), $conn);
                            if (is_null($jresults1)) throw new Exception("Could not pull records");
                            $jArray1 = json_decode($jresults1, true);
                            if (is_null($jArray1)) throw new Exception("Malformed returned results");
                            if ($jArray1['count'] != 1) throw new Exception("None or Duplicate Last Resort Settings");
                            $defaultValue = $jArray1['rows'][0]['defaultXValue'];
                            $chkDefaultValue = "";
                            if ($defaultValue == 1) $chkDefaultValue = "checked";
                        ?>
                            <div class="lastresortdonotcare">
                                <form id="<?= $formid ?>" method="POST">
                                    <input type="hidden" name="page" value="lastresortdonotcare" />
                                    <input type="hidden" name="warnings" value="show" />
                                    <input type="hidden" name="oldvalue" value="<?= $defaultValue ?>"/>
                                    <div class="form-check">
                                        <input <?= $chkDefaultValue ?> id="lastresort" name="lastresort" type="checkbox" class="form-check-input" value="1" />
                                        <label for="lastresort" class="form-check-label">TICK/CHECK TO ENABLE SYSTEM TO ALLOW ; UNTICK/UNCHECK TO ENABLE SYSTEM TO DENY</label>
                                    </div>
                                    <!--<div class="form-check">
                                        <input id="lastresort" name="lastresort" value="1" type="checkbox" class="btn-check" autocomplete="off"/>
                                        <label class="btn btn-outline-danger" for="lastresort">TICK/CHECK TO ENABLE SYSTEM TO ALLOW ; UNTICK/UNCHECK TO ENABLE SYSTEM TO DENY</label>
                                    </div> -->
                                    <div id="<?= $errorid ?>" class="p-2 ui-sys-error-message"></div>
                                    <div class="text-center text-md-right mb-2">
                                        <input type="submit" class="btn btn-danger btn-form-submit" value="Proceed" data-form-submit="<?= $formid ?>" data-form-error="<?= $errorid ?>" data-next-page="<?= $thispage ?>" />
                                    </div>
                                </form>
                            </div>
                    <?php
                        }
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $thispage ?>" class="card-link">Back to Dashboard</a></i><br />
                        <span class="text-muted"><i>Root Credentials</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>