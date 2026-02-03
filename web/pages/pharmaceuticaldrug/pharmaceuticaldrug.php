<div class="container data-container mt-2 mb-2">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    PHARMACEUTICAL DRUG
                </div>
                <div class="card-body">
<?=
    PharmaceuticalDrug::getASearchUI($thispage, array('drugName', 'unitOfMeasurement'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=pharmaceuticaldrug_create" data-toggle="tooltip" title="Create a New Drug" class="btn btn-primary mr-2 add-record">Create a New Drug</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: pharmaceuticaldrug</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>