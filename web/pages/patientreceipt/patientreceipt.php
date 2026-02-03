<div class="container data-container mt-2 mb-2">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    PATIENT RECEIPT
                </div>
                <div class="card-body">
<?=
    PatientReceipt::getASearchUI($thispage, array('receiptNumber', 'invoice', 'amount'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=patientreceipt_create" data-toggle="tooltip" title="Generate Receipt" class="btn btn-primary mr-2 add-record">Generate Receipt</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: patientreceipt</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>