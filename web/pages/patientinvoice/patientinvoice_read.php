<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    DETAILS OF INVOICE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patientinvoice";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $id = $_GET['id'];
                        echo __data__::createDetailsPage($nextPage, "PatientInvoice", array(
                            'timeOfCreation',
                            'invoiceNumber',
                            'currency',
                            'amountBeforeDiscount',
                            'discount',
                            'amount',
                            'balance',
                            'totalPaid',
                            'chargeTo',
                            'visit',
                            'closed',
                            'comments'
                        ), $conn, $id, null);
                        $pdfpage = ($profile1->getBaseURL())."/documents/pdf/__get_document__.php?id=$id&dtype=" . (Documents::$__PDF_INVOICE);
                        $pdfpage = str_replace("//", "/", $pdfpage);
                    ?>
                        <div class="document-controls-bottom-right">
                            <a target = "_blank" href="<?= $pdfpage ?>" class="btn btn-primary btn-control" data-toggle="tooltip" title="Click to Download an invoice in a pdf format"><i class="fa fa-file-pdf-o fa-2x"></i>Download Invoice</a>
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
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Patient Invoice</a></i><br />
                        <span class="text-muted"><i>Rule: patientinvoice_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
