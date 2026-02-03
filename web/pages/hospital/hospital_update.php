<?php
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
$conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
$hospital1 = new Hospital($dbname, Hospital::$__INIT_ID, $conn);
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div id="accordion" class="mt-2 mb-2">
                <div class="card border-dark">
                    <div class="card-header bg-dark" id="__registration_number_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__registration_number" aria-expanded="false" aria-controls="__registration_number">Patient Registration Numbers</button>
                        </h5>
                    </div>
                    <div id="__registration_number" class="collapse" aria-labelledby="__registration_number_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__registration_number_form__">
                                <input type="hidden" name="__classname__" value="Hospital" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $hospital1->getHospitalId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Patient Registration Number Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Patient Registration Number Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Patient Registration Number Settings Updated" />
                                <?= __data__::createFormTextInput("Hospital", "nextRegistrationNumber", "Next Registration Number", $hospital1->getNextRegistrationNumber(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "registrationNumberWidth", "Registration Number Width", $hospital1->getRegistrationNumberWidth(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "registrationNumberBlockWidth", "Block Width", $hospital1->getRegistrationNumberBlockWidth(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "registrationNumberBlockSeparatorCharacter", "Block Separator Character", $hospital1->getRegistrationNumberBlockSeparatorCharacter(), true) ?>
                                <div id="__registration_number_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__registration_number_form__" data-form-error="__registration_number_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark">
                    <div class="card-header bg-dark" id="__temporary_registration_number_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__temporary_registration_number" aria-expanded="false" aria-controls="__temporary_registration_number">Temporary Registration Numbers</button>
                        </h5>
                    </div>
                    <div id="__temporary_registration_number" class="collapse" aria-labelledby="__temporary_registration_number_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__temporary_registration_number_form__">
                                <input type="hidden" name="__classname__" value="Hospital" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $hospital1->getHospitalId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Patient Registration Number Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Patient Registration Number Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Patient Registration Number Settings Updated" />
                                <?= __data__::createFormTextInput("Hospital", "temporaryRegistrationNumberPrefix", "Registration Number Prefix", $hospital1->getTemporaryRegistrationNumberPrefix(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "temporaryNextRegistrationNumber", "Next Registration Number", $hospital1->getTemporaryNextRegistrationNumber(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "temporaryRegistrationNumberWidth", "Registration Number Width", $hospital1->getTemporaryRegistrationNumberWidth(), true) ?>
                                <div id="__temporary_registration_number_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__temporary_registration_number_form__" data-form-error="__temporary_registration_number_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark">
                    <div class="card-header bg-dark" id="__invoice_number_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__invoice_number" aria-expanded="false" aria-controls="__invoice_number">Invoice Numbers</button>
                        </h5>
                    </div>
                    <div id="__invoice_number" class="collapse" aria-labelledby="__invoice_number_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__invoice_number_form__">
                                <input type="hidden" name="__classname__" value="Hospital" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $hospital1->getHospitalId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Invoice Number Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Invoice Number Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Invoice Number Settings Updated" />
                                <?= __data__::createFormTextInput("Hospital", "invoiceNumberPrefix", "Invoice Number Prefix", $hospital1->getInvoiceNumberPrefix(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "nextInvoiceNumber", "Next Invoice Number", $hospital1->getNextInvoiceNumber(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "invoiceNumberWidth", "Invoice Number Width", $hospital1->getInvoiceNumberWidth(), true) ?>
                                <div id="__invoice_number_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__invoice_number_form__" data-form-error="__invoice_number_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark">
                    <div class="card-header bg-dark" id="__receipt_number_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__receipt_number" aria-expanded="false" aria-controls="__receipt_number">Receipt Numbers</button>
                        </h5>
                    </div>
                    <div id="__receipt_number" class="collapse" aria-labelledby="__receipt_number_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__receipt_number_form__">
                                <input type="hidden" name="__classname__" value="Hospital" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $hospital1->getHospitalId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Receipt Number Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Receipt Number Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Receipt Number Settings Updated" />
                                <?= __data__::createFormTextInput("Hospital", "receiptNumberPrefix", "Receipt Number Prefix", $hospital1->getReceiptNumberPrefix(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "nextReceiptNumber", "Next Receipt Number", $hospital1->getNextReceiptNumber(), true) ?>
                                <?= __data__::createFormTextInput("Hospital", "receiptNumberWidth", "Receipt Number Width", $hospital1->getReceiptNumberWidth(), true) ?>
                                <div id="__receipt_number_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__receipt_number_form__" data-form-error="__receipt_number_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$conn = null;
?>