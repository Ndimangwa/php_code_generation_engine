<div class="container data-container mt-2 mb-2">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    WARDS MANAGEMENT
                </div>
                <div class="card-body">
<?=
    Ward::getASearchUI($thispage, array('timeOfCreation', 'wardNumber', 'comments'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=ward_create" data-toggle="tooltip" title="Add A New Record" class="btn btn-primary mr-2 add-record">Add New
                            Record</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: ward</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>