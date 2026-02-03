<?php
$objectName = "";
$classname = $_GET['class'];
$id = $_GET['id'];
$lclass = strtolower($classname);
$nextPage = $thispage . "?page=$lclass" . "_read&id=$id";
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    FIREWALL UPDATE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    try {
                        if (!in_array($classname, array("Login", "JobTitle", "Group"))) throw new Exception("Supplied class is not in the list");
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $object1 = Registry::getObjectReference("Ndimangwa", $conn, $classname, $id);
                        $objectName = $object1->getName0();
                        if ($classname == "Login" && $object1->isRoot()) throw new Exception("This is a Root User Account, System Rules NOT APPLICABLE");
                        $contextString1 = $object1->getContext();
                        $prefix = "alt_829_bin_";
                        //01 -Display Search UI
                        $searchName = $prefix . "_search_text_";
                        $errorName = $prefix . "_error_";
                    ?>
                        <div class="firewall_search">
                            <form method="GET">
                                <input type="hidden" name="page" value="firewall_update" />
                                <input type="hidden" name="class" value="<?= $classname ?>" />
                                <input type="hidden" name="id" value="<?= $id ?>" />
                                <div class="input-group mb-3">
                                    <input name="<?= $searchName ?>" type="search" class="form-control" data-min-length="3" placeholder="Search" />
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-firewall-search btn-click-default" type="button" data-toggle="tooltip">Search</button>
                                    </div>
                                </div>
                                <div class="ui-sys-error-message" id="<?= $errorName ?>"></div>
                            </form>
                        </div>
                        <div class="firewall-update-all border border-danger rounded p-1 m-1">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="p-2">
                                            <form method="POST" id="firewall_update_allow_all">
                                                <input type="hidden" name="__command__" value="<?= ContextPosition::$__ALLOW_ALL ?>" />
                                                <input type="hidden" name="__id__" value="<?= $object1->getId0() ?>" />
                                                <input type="hidden" name="__class__" value="<?= $classname ?>" />
                                                <input type="hidden" name="__modal_title__" value="Firewall Update Report" />
                                                <button data-toggle="tooltip" title="Will Allow All Action for for <?= $objectName ?>" class="btn btn-primary btn-block btn-firewall-update" data-form-submit="firewall_update_allow_all" data-form-error="firewall_update_all_error" data-next-page="<?= $nextPage ?>">ALLOW ALL</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2">
                                            <form method="POST" id="firewall_update_deny_all">
                                                <input type="hidden" name="__command__" value="<?= ContextPosition::$__DENY_ALL ?>" />
                                                <input type="hidden" name="__id__" value="<?= $object1->getId0() ?>" />
                                                <input type="hidden" name="__class__" value="<?= $classname ?>" />
                                                <input type="hidden" name="__modal_title__" value="Firewall Update Report" />
                                                <button data-toggle="tooltip" title="Will Deny All Action for for <?= $objectName ?>" class="btn btn-danger btn-block btn-firewall-update" data-form-submit="firewall_update_deny_all" data-form-error="firewall_update_all_error" data-next-page="<?= $nextPage ?>">DENY ALL</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2">
                                            <form method="POST" id="firewall_update_donotcare_all">
                                                <input type="hidden" name="__command__" value="<?= ContextPosition::$__DONOTCARE_ALL ?>" />
                                                <input type="hidden" name="__id__" value="<?= $object1->getId0() ?>" />
                                                <input type="hidden" name="__class__" value="<?= $classname ?>" />
                                                <input type="hidden" name="__modal_title__" value="Firewall Update Report" />
                                                <button data-toggle="tooltip" title="Will Ignore All Action for for <?= $objectName ?>" class="btn btn-warning btn-block btn-firewall-update" data-form-submit="firewall_update_donotcare_all" data-form-error="firewall_update_all_error" data-next-page="<?= $nextPage ?>">DO NOT CARE ALL</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <span id="firewall_update_all_error" class="ui-sys-error-message"></span>
                            </div>
                        </div>
                        <?php
                        if (isset($_GET[$searchName])) {
                            $value = $_GET[$searchName];
                            //02 -Display Results 
                            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
                                array('_contextPosition'),
                                array('cId', 'cName', 'caption'),
                                array((JSON2SQL::$__OP_LIKE) => array('cName' => "%$value%"))
                            ), $conn);
                            if (is_null($jresult1)) throw new Exception("Could not pull results");
                            $jArray1 = json_decode($jresult1, true);
                            if (is_null($jArray1)) throw new Exception("Malformed Result Sets");
                            if ($jArray1['count'] == 0) throw new Exception("The Search returned an Empty results");
                            $maximumRecordsPerPage = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
                        ?>
                            <div class="tabular-results ui-sys-pagination">
                                <form id="__firewall_update_form__" method="POST">
                                    <input type="hidden" name="__command__" value="<?= ContextPosition::$__CUSTOMIZE ?>" />
                                    <input type="hidden" name="__id__" value="<?= $object1->getId0() ?>" />
                                    <input type="hidden" name="__class__" value="<?= $classname ?>" />
                                    <input type="hidden" name="__modal_title__" value="Firewall Update Report" />
                                    <table class="table">
                                        <thead class="thead-dark">
                                            <th scope="col"></th>
                                            <th>RULE</th>
                                            <th>ALLOW</th>
                                            <th>DENY</th>
                                            <th>DONT CARE</th>
                                        </thead>
                                        <?php
                                        $count = 0;
                                        $pageCount = 0;
                                        echo "<tbody>";
                                        foreach ($jArray1['rows'] as $row) {
                                            if (($count != 0) && ($count % $maximumRecordsPerPage) == 0) {
                                                $pageCount++;
                                                echo "</tbody><tbody class=\"ui-sys-hidden\">";
                                            }
                                            $sn = $count + 1;
                                            $context1 = new ContextPosition("dbname", $row['cId'], $conn);
                                            $pos = $context1->getCharacterPosition();
                                            $char1 = Authorize::getContextCharacter("dbname", $conn, $contextString1, $pos);
                                            $chkAllow = "";
                                            $chkDeny = "";
                                            $chkDoNotCare = "";
                                            if ($char1 == ContextPosition::$__ALLOW) {
                                                $chkAllow = "checked";
                                            } else if ($char1 == ContextPosition::$__DENY) {
                                                $chkDeny = "checked";
                                            } else if ($char1 == ContextPosition::$__DONOTCARE) {
                                                $chkDoNotCare = "checked";
                                            }
                                            $cname = $row['cName'];
                                            $caption = $row['caption'];
                                            $textStyle = "style = \"font-size: 0.9em; font-style: italic;\"";
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $sn ?></th>
                                                <td data-toggle="tooltip" title="<?= $caption ?>"><?= $cname ?></td>
                                                <td><input <?= $chkAllow ?> value="<?= ContextPosition::$__ALLOW ?>" class="form-check-input" type="radio" name="position[<?= $context1->getCharacterPosition() ?>]" id="radio_allow_<?= $count ?>" />
                                                    <label class="form-check-label text-primary" for="radio_allow_<?= $count ?>" <?= $textStyle ?>>Allow</label>
                                                </td>
                                                <td><input <?= $chkDeny ?> value="<?= ContextPosition::$__DENY ?>" class="form-check-input" type="radio" name="position[<?= $context1->getCharacterPosition() ?>]" id="radio_deny_<?= $count ?>" />
                                                    <label class="form-check-label text-danger" for="radio_deny_<?= $count ?>" <?= $textStyle ?>>Deny</label>
                                                </td>
                                                <td><input <?= $chkDoNotCare ?> value="<?= ContextPosition::$__DONOTCARE ?>" class="form-check-input" type="radio" name="position[<?= $context1->getCharacterPosition() ?>]" id="radio_donotcare_<?= $count ?>" />
                                                    <label class="form-check-label text-warning" for="radio_donotcare_<?= $count ?>" <?= $textStyle ?>>Don't Care</label>
                                                </td>
                                            </tr>
                                        <?php
                                            $count++;
                                        }
                                        echo "</tbody>";
                                        ?>
                                    </table>
                                    <div id="__firewall_update_error__" class="p-2 ui-sys-error-message"></div>
                                    <div class="text-center text-md-right mb-2">
                                        <button class="btn btn-primary btn-form-submit btn-firewall-update" data-form-submit="__firewall_update_form__" data-form-error="__firewall_update_error__" data-next-page="<?= $nextPage ?>">Save Rules</button>
                                    </div>
                                </form>
                                <span class="ui-sys-datastore" data-pages="<?= $pageCount ?>"></span>
                                <?php
                                //03 -Diplaying Pages
                                include("../general/pagination.php");
                                ?>
                            </div>
                    <?php
                        } //End of Search results
                        //04 - Show All
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to <?= $objectName ?></a></i><br />
                        <span class="text-muted"><i>Rule: firewall_update</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $('button.btn-firewall-search').on('click', function(e) {
            var $button1 = $(this);
            var $form1 = $button1.closest('form');
            var $error1 = $('#' + '<?= $errorName ?>');
            generalFormSubmission($button1, $form1, $error1, Constant);
        });
        $('button.btn-firewall-update').on('click', function(e) {
            e.preventDefault();
            var $button1 = $(this);
            var $form1 = $button1.closest('form');
            var $dialog1 = $('#__status_query_modal__');
            var errorTarget = $button1.data('formError');
            $errorTarget1 = $('#' + errorTarget);
            if ($errorTarget1.length && !generalFormValidation($button1, $form1, $errorTarget1, Constant)) return false;
            var dataToSend = $form1.serializeObject();
            var nextPage = "<?= $thispage ?>?page=<?= $page ?>";
            if ($button1.attr('data-next-page') !== undefined) nextPage = $button1.attr('data-next-page');
            sendAjaxDialog(
                $button1,
                $dialog1,
                '../server/serviceFirewallUpdate.php',
                dataToSend,
                nextPage,
                null,
                "POST",
                true,
                false,
                "Saving ...",
                "Saved",
                "Retry ..."
            );
        });
    })(jQuery);
</script>