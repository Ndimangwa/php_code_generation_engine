<?php
$__QUEUE = 1;
$__REPORT = 2;
$currentTab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : $__QUEUE;
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    TRIAGE / VITAL SIGNS
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=triage";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        //Tab Begins
                        $prefix = "__triage_assist_";
                    ?>
                        <div class="tab-container container">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#<?= $prefix ?><?= $__QUEUE ?>" class="nav-link active" tab-index="<?= $__QUEUE ?>" data-bs-toggle="tab">Current Queues</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#<?= $prefix ?><?= $__REPORT ?>" class="nav-link" tab-index="<?= $__REPORT ?>" data-bs-toggle="tab">Report for Patient</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="<?= $prefix ?><?= $__QUEUE ?>" class="tab-pane fade show active">
                                    <?php
                                    try {
                                        $query = "SELECT queueId, q.timeOfCreation as timeOfCreation, registrationNumber, surname, otherNames, visitCount FROM _patient_triage_queue as q, _patient as p, _patientVisit as v, _patientCase as c WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId)";
                                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                            array(
                                                "idColumn" => "queueId",
                                                "caption" => "Record Vital Signs",
                                                "href" => $thispage . "?page=triage_create&qid=",
                                                "appendId" => true
                                            )
                                        ), array(
                                            "timeOfCreation" => array("caption" => "Time"),
                                            "registrationNumber" => array("caption" => "Reg No"),
                                            "surname" => array("caption" => "Surname"),
                                            "otherNames" => array("caption" => "Given Names"),
                                            "visitCount" => array("caption" => "visit")
                                        ), array('queueId'), 3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function ($conn, $colname, $colval) {
                                            switch ($colname) {
                                                case "timeOfCreation":
                                                case "timeOfUpdation":
                                                    try {
                                                        $t1 = new DateAndTime($colval);
                                                        $colval = $t1->getDateAndTimeString();
                                                    } catch (Exception $e) {
                                                    }
                                                    break;
                                            }
                                            return $colval;
                                        });
                                        echo $windowToDisplay1;
                                    } catch (Exception $e)  {
                                        echo UICardView::getDangerReportCard("Traige/Vital Signs", $e->getMessage());
                                    }
                                    ?>
                                </div>
                                <div id="<?= $prefix ?><?= $__REPORT ?>" class="tab-pane fade">
                                    <?= UIView::wrap(Patient::getASearchUI($thispage, array('surname', 'otherNames', 'dob', 'sex', 'phone', 'registrationType'), 0, false, array(
                                        'external-link' => array(
                                            'caption' => 'View Vital Signs',
                                            'href' => ( $thispage . "?page=vitalsigns&report=1&patientId=" )
                                        )
                                    ))) ?>
                                </div>
                            </div>
                        </div>
                    <?php
                        //Tab ends
                    } catch (Exception $e) {
                        echo UICardView::getDangerReportCard("Triage/Vital Signs", $e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <!-- <i><a href="<?= $nextPage ?>" class="card-link">Back to Triage</a></i><br /> -->
                        <span class="text-muted"><i>Rule: triage</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        window.setTabbedNavigation($('div.tab-container'), <?= $currentTab ?>);
        $('button.btn-patient-search').on('click', function(e) {
            var $button1 = $(this);
            var $form1 = $button1.closest('form');
            var $error1 = $('#' + '<?= $errorName ?>');
            $form1.submit();
        });
        $('#txt-patient-search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "../server/getListOfPatientsInAFinanceQueue.php",
                    dataType: "json",
                    method: "POST",
                    data: {
                        patientName: request.term
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.code != 0) return false;
                        response($.map(data.rows, function(item) {
                            return {
                                surname: item.surname,
                                label: (item.surname + ', ' + item.otherNames)
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#txt-patient-search').val(ui.item.surname);
                //$('#txt-patient-search').val(ui.item.label);
                return false;
            },
            minLength: 3,
            open: function() {
                $(this).removeClass('ui-corner-all').addClass('ui-corner-top');
            },
            close: function() {
                $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            }
        });
    })(jQuery);
</script>