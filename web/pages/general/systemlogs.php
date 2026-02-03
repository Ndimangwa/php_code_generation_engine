<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    SYSTEM LOGS
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $prefix = "alt_839_bin_";
                        //01 -Display Search UI
                        $searchName = $prefix . "_search_text_";
                        $errorName = $prefix . "_error_";
                    ?>
                        <div class="systemlogs_search">
                            <form method="GET">
                                <input type="hidden" name="page" value="systemlogs" />
                                <div class="input-group mb-3">
                                    <input required name="<?= $searchName ?>" type="search" class="form-control" data-min-length="3" placeholder="Search" />
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
                            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
                                array('_systemlogs'),
                                array('logDate', 'username', 'opname', 'target'),
                                array((JSON2SQL::$__OP_OR) => array(
                                    array((JSON2SQL::$__OP_LIKE) => array("logDate" => "%$value%")),
                                    array((JSON2SQL::$__OP_LIKE) => array("username" => "%$value%")),
                                    array((JSON2SQL::$__OP_LIKE) => array("opname" => "%$value%")),
                                    array((JSON2SQL::$__OP_LIKE) => array("target" => "%$value%"))
                                ))
                            ), $conn);
                            if (is_null($jresult1)) throw new Exception("Could not pull results");
                            $jArray1 = json_decode($jresult1, true);
                            if (is_null($jArray1)) throw new Exception("Malformed Result Sets");
                            if ($jArray1['count'] == 0) throw new Exception("The Search returned an Empty results");
                            $maximumRecordsPerPage = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
                        ?>
                            <div class="tabular-results ui-sys-pagination">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <th scope="col"></th>
                                        <th>Timestamp</th>
                                        <th>Username/Login</th>
                                        <th>Rule/Operation</th>
                                        <th>Affected Target</th>
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
                                        $timestamp = $row['logDate'];
                                        $username = $row['username'];
                                        $opname = $row['opname'];
                                        $target = $row['target'];
                                    ?>
                                        <tr>
                                            <th scope="row"><?= $sn ?></th>
                                            <td><?= $timestamp ?></td>
                                            <td><?= $username ?></td>
                                            <td><?= $opname ?></td>
                                            <td><?= $target ?></td>
                                        </tr>
                                    <?php
                                        $count++;
                                    }
                                    echo "</tbody>";
                                    ?>
                                </table>
                                <span class="ui-sys-datastore" data-pages="<?= $pageCount ?>"></span>
                                <?php
                                //03 -Diplaying Pages
                                include("pagination.php");
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
                        <i><a href="<?= $thispage ?>" class="card-link">Back to Dashboard</a></i><br />
                        <span class="text-muted"><i>Rule: systemlogs</i></span>
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