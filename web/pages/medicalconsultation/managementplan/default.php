<?php 
$prefix = "__alt_bin_";
$tabPharmacy = $prefix . "pharmacy";
$tabAdmission = $prefix . "admission";
$tabPharmacyValue = ( GeneralMedicalWorkingBlock::$__TAB_PHARMACY );
$tabAdmissionValue = ( GeneralMedicalWorkingBlock::$__TAB_ADMISSION );
$activetab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : ( GeneralMedicalWorkingBlock::$__TAB_PHARMACY );
?>
<div class="container tab-container">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a tab-index="<?= $tabPharmacyValue ?>" href="#<?= $tabPharmacy ?>" class="nav-link <?= ( $activetab == $tabPharmacyValue ) ? "active" : "" ?>">Pharmacy</a></li>
        <li class="nav-item"><a tab-index="<?= $tabAdmissionValue ?>" href="#<?= $tabAdmission ?>" class="nav-link <?= ( $activetab == $tabAdmissionValue ) ? "active" : "" ?>">Admission</a></li>
    </ul>
    <div class="tab-content">
        <div id="<?= $tabPharmacy ?>" class="tab-pane fade <?= ( $activetab == $tabPharmacyValue ) ? "active show" : "" ?>">
            <h3>Pharmacy</h3>
            <?php include("default_pharmacy.php"); ?>
        </div>
        <div id="<?= $tabAdmission ?>" class="tab-pane fade <?= ( $activetab == $tabAdmissionValue ) ? "active show" : "" ?>">
            <h3>Admission</h3>
            <?php include("default_admission.php"); ?>
        </div>
    </div>
</div>
<!--Initiating a script-->
<script type="text/javascript">
(function($)    {
    $(function()    {
        window.setTabbedNavigation($('div.tab-container'), <?= $activetab ?>);
    });
})(jQuery);
</script>