<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    ISSUE A PAYMENT RECEIPT
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patientreceipt";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT invoiceId, c.currencyName as currency, amount, totalPaid, balance, surname, otherNames, insured, pi.extraFilter as efilter FROM _patient_invoice as pi, _patient as p, _currency as c WHERE (pi.patientId = p.patientId) AND (pi.currencyId = c.currencyId) AND (pi.closed = 0)";
                        echo UITabularView::query($conn, $query, array(
                            array("idColumn" => "invoiceId",
                            "caption" => "Make Payment",
                            "href" => $thispage."?page=patientreceipt_create&id=",
                            "appendId" =>true)
                        ), array(
                            "efilter" => array("urlArgsAppend" => true),
                            "insured" => array("values" => array(1 => "YES", 0 => "NO"))
                        ), array('invoiceId', 'efilter'), 3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval) {
                            return $colval;
                        }); 
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Receipt</a></i><br />
                        <span class="text-muted"><i>Rule: patientreceipt_create</i></span>
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
                    url: "../server/getListOfPatientsHavingOpenInvoices.php",
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