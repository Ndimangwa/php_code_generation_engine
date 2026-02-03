<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    NURSE STATION
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=nursestation";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT queueId, s.serviceName as serviceName, registrationNumber, surname, otherNames, currentAttendanceSequence as curr, maxNumberOfAttendance as max FROM _nurse_station_queue as q, _patient as p, _patientVisit as v, _patientCase as c, _service as s WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (q.serviceId = s.serviceId) AND (q.pendingPayment = 0)";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                                array("idColumn" => "queueId",
                                "caption" => "Record",
                                "href" => $thispage."?page=nursestation_record&qid=",
                                "appendId" => true)
                            ), array(
                                "timeOfCreation" => array("caption" => "Time"),
                                "registrationNumber" => array("caption" => "Reg No"),
                                "surname" => array("caption" => "Surname"),
                                "otherNames" => array("caption" => "Given Names"),
                                "visitCount" => array("caption" => "visit")
                        ), array('queueId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
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
                        <span class="text-muted"><i>Rule: nursestation</i></span>
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
