<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    PATIENT MANAGEMENT SYSTEM
                </div>
                <div class="card-body">
<?=  
    Patient::getASearchUI($thispage, array('surname', 'otherNames', 'dob', 'sex', 'phone', 'registrationType'));
?>
                </div>
                <div class="card-footer">
                    <div class="text-center text-md-right mb-2">
                        <a href="<?= $thispage ?>?page=patient_create" data-toggle="tooltip" title="Register A New Patient" class="btn btn-primary mr-2 add-record">Register A New Patient</a>
                    </div>
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: patient</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>