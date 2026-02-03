<div class="container data-container mt-2 mb-2">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    SERVICE CATEGORY
                </div>
                <div class="card-body">
<?=
    ServiceCategory::getASearchUI($thispage, array('categoryName'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=servicecategory_create" data-toggle="tooltip" title="Add A New Record" class="btn btn-primary mr-2 add-record">Add New
                            Record</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: servicecategory</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>