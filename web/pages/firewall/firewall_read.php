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
                    FIREWALL MONITOR
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
                        $prefix = "alt_829_bin_";
                        //01 -Display Search UI
                        $searchName = $prefix . "_search_text_";
                        $errorName = $prefix . "_error_";
                    ?>
                        <div class="firewall_search">
                            <form method="GET">
                                <input type="hidden" name="page" value="firewall_read" />
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
                        <?php
                        if (isset($_GET[$searchName])) {
                            $value = $_GET[$searchName];
                            //02 -Display Results 
                            $ds1 = Authorize::getAuthorizationGraphDataStructure($conn, $object1, $value);
                            if (sizeof($ds1['rows']) == 0) throw new Exception('Could not find results');
                            $maximumRecordsPerPage = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
                            $datasize = sizeof($ds1['class']);
                        ?>
                            <div class="tabular-results ui-sys-pagination">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <th scope="col"></th>
                                        <th>Rule</th>
                                        <?php
                                        for ($i = 0; $i < sizeof($ds1['class']); $i++) {
                                            $classname = $ds1['class'][$i];
                                            $caption = $ds1['caption'][$i];
                                        ?>
                                            <th scope="col" data-toggle="tooltip" title="<?= $classname ?>"><?= $caption ?></th>
                                        <?php
                                        }
                                        ?>
                                        <th scope="col"></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        $pageCount = 0;
                                        //echo json_encode($ds1);
                                        foreach ($ds1['rows'] as $cId => $row) {
                                            $context1 = new ContextPosition("Ndimangwa", $cId, $conn);
                                            $rule = $row['rule'];
                                            $ruleText = "Deny";
                                            $ruleTextColor = "text-danger";
                                            $bgColor = "bg-danger";
                                            $length = $row['length']; //zero-based
                                            $actionclass = $ds1['class'][$length];
                                            $actioncaption = $ds1['caption'][$length];
                                            $captionMessage = "";
                                            if ($rule == ContextPosition::$__ALLOW) {
                                                $ruleText = "Allow";
                                                $ruleTextColor = "text-primary";
                                                $bgColor = "bg-primary";
                                                if ($actionclass == "System" && $actioncaption == "System") {
                                                    $captionMessage = "Allowed by the System ( ".$profile1->getSystemName()." )";
                                                } else {
                                                    if ($actionclass == "Login") $actionclass = "User Account";
                                                    $captionMessage = "Allowed by $actioncaption ( $actionclass )";
                                                }
                                            } else {
                                                if ($actionclass == "System" && $actioncaption == "System") {
                                                    $captionMessage = "Denied by the System ( ".$profile1->getSystemName()." )";
                                                } else {
                                                    if ($actionclass == "Login") $actionclass = "User Account";
                                                    $captionMessage = "Denied by $actioncaption ( $actionclass )";
                                                }
                                            }
                                            if (($count != 0) && ($count % $maximumRecordsPerPage == 0)) {
                                                echo "</tbody><tbody class=\"ui-sys-hidden\">";
                                                $pageCount++;
                                            }
                                        ?>
                                            <tr style="font-size: 0.8em; height: 12px; padding-top: 0; padding-bottom: 0; margin-top: 2px; margin-bottom: 2px;">
                                                <th scope="row"><?= $count + 1 ?></th>
                                                <td class="<?= $ruleTextColor ?>" data-toggle="tooltip" title="<?= $context1->getCaption() ?>"><?= $context1->getContextName() ?></td>
                                                <?php
    ?>
        <td colspan="<?= $length + 1?>">
            <div data-toggle="tooltip" title="<?= $captionMessage ?>" class="progress">
                <div class="progress-bar <?= $bgColor ?>" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-vauemin="0" aria-valuemax="100"></div>
            </div>
        </td>
    <?php
                                                for ($i = $length + 1; $i < $datasize; $i++) {
                                                    echo "<td></td>";
                                                }
                                                ?>
                                                <td class="<?= $ruleTextColor ?>"><?= $ruleText ?></td>
                                            </tr>
                                        <?php
                                            $count++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
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
                        <span class="text-muted"><i>Rule: firewall_read</i></span>
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
    })(jQuery);
</script>