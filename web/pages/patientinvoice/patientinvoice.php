<div class="container data-container mt-2 mb-2">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    PATIENT INVOICE
                </div>
                <div class="card-body">
<?=
    PatientInvoice::getASearchUI($thispage, array('invoiceNumber', 'currency', 'amount'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=patientinvoice_create" data-toggle="tooltip" title="Create a New Invoice" class="btn btn-primary mr-2 add-record">Create A New Invoice</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: patientinvoice</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>