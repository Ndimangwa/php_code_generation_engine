<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW INVOICE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patientinvoice";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT queueId, surname, otherNames, sexName, dob, stageId FROM _patient_finance_queue as pfq, _patientCase as pc, _patient as p, _sex as s WHERE (pfq.caseId = pc.caseId) AND (pc.patientId = p.patientId) AND (p.sexId = s.sexId)";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "queueId",
                                "caption" => "Create Invoice",
                                "href" => $thispage."?page=patientinvoice_create&qid=",
                                "appendId" => true)
                            ), array(
                                "surname" => array("caption" => "Surname"),
                                "otherNames" => array("caption" => "Given Names"),
                                "sexName" => array("caption" => "Sex"),
                                "dob" => array("caption" => "D.O.B"),
                                "stageId" => array("caption" => "Action") 
                        ), array('queueId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
                            if ($colname == "dob")  {
                                //$colval = ~DateAndTime~::~convertFromSystemDateAndTimeFormatToGUIDateFormat($colval);
                                $dt1 = DateAndTime::createDateAndTimeFromGUIDate($colval);
                                $colval = $dt1->getTimestamp();
                            }
                            if ($colname == "stageId")  {
                                $stage1 = new PatientMovementStage("Delta", $colval, $conn);
                                $colval = $stage1->getStageName();
                                $colval = "<span style=\"font-size: 0.9em; font-style: italic;\">$colval</span>";
                            }
                            return $colval;
                        });
                        echo $windowToDisplay1;
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Invoice</a></i><br />
                        <span class="text-muted"><i>Rule: patientinvoice_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
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