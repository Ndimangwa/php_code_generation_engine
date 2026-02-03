<?php
//General Commands
$modalId = "_kenge_wa_maji_ya_moto_0001";
$__MODAL_IS_ENABLED = "__modal_is_enabled__";
//Working with times
$today1 = $systemTime1;
$yesterday1 = $today1->getPreviousDateAndTimeByDays(1);
$dt7DaysAgo1 = $today1->getPreviousDateAndTimeByDays(7);
$dt30DaysAgo1 = $today1->getPreviousDateAndTimeByDays(30);
$conn = null;
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
} catch (Exception $e) {
    die($e->getMessage());
}
//Dialog Modal
?>
<div class="modal" tabindex="-1" role="dialog" id="<?= $modalId ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Dates</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $hiddenFields = array();
                foreach ($_REQUEST as $key => $value) {
                        $hiddenFields[$key] = $value;
                }
                echo UIView::wrap(__data__::createDataCaptureForm($thispage, "Profile", array(
                    array('pname' => 'timeOfCreation', 'type' => 'date', 'caption' => 'Date From : ', 'required' => true, 'placeholder' => '05/21/2019'),
                    array('pname' => 'timeOfUpdation', 'type' => 'date', 'caption' => 'Date To : ', 'required' => true, 'placeholder' => '07/19/2021')
                ), "Select Dates", "create", null, 0, $hiddenFields, null, null, "date-range-select", $thispage, true));
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $(function() {
            $('#<?= $modalId ?>').on('shown.bs.modal', function(e) {
                var $button1 = $(e.relatedTarget);
                if (!$button1.length) alert('Could not get Anchor');
                var $form1 = $('#<?= $modalId ?>').find('form');
                if (!$form1.length) alert('Could not get target form');
                var argList = $button1.data('args');
                argList.split("&").forEach(function(term) {
                    var list1 = term.split("=");
                    if (list1.length == 2) {
                        var key = list1[0];
                        var val = list1[1];
                        $('<input/>').attr('type', 'hidden').attr('name', key).val(val).appendTo($form1);
                    }
                });
            });
        });
    })(jQuery);
</script>
<?php
//Registered patients
include("ui_registered_patients.php");
$conn = null;
?>
