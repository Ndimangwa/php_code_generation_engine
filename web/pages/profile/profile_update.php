<?php
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
$conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div id="accordion" class="mt-2 mb-2">
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__general_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__general" aria-expanded="true" aria-controls="__general">General Settings</button>
                        </h5>
                    </div>
                    <div id="__general" class="collapse" aria-labelledby="__general_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__general_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="General Settings" />
                                <input type="hidden" name="__modal_success_message__" value="General Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="General Settings Updated" />
                                <?= __data__::createFormTextInput("Profile", "profileName", "Name of the Organization", $profile1->getProfileName(), true) ?>
                                <?= __data__::createFormTextInput("Profile", "systemName", "Name of the System", $profile1->getSystemName(), true) ?>
                                <div id="__general_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__general_form__" data-form-error="__general_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__connection_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__connection" aria-expanded="false" aria-controls="__connection">Connection Settings</button>
                        </h5>
                    </div>
                    <div id="__connection" class="collapse" aria-labelledby="__connection_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__connection_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Connection Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Connection Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Connection Settings Updated" />
                                <?= __data__::createFormTextInput("Profile", "baseURL", "Base URL", $profile1->getBaseURL(), true, null, "http://localhost/hmis") ?>
                                <?= __data__::createFormTextInput("Profile", "baseFolder", "Base Folder", $profile1->getBaseFolder(), true, null, "/var/www/html/", null) ?>
                                <?= __data__::createFormTextInput("Profile", "externalBaseFolder", "External Base Folder", $profile1->getExternalBaseFolder(), true, null, "/var/www/external/", null) ?>
                                <div id="__connection_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__connection_form__" data-form-error="__connection_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__communication_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__communication" aria-expanded="false" aria-controls="__communication">Communication Settings</button>
                        </h5>
                    </div>
                    <div id="__communication" class="collapse" aria-labelledby="__communication_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__communication_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Communication Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Communication Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Communication Settings Updated" />
                                <?= __data__::createFormEmailInput("Profile", "email", "Email", $profile1->getEmail(), true) ?>
                                <div id="__communication_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__communication_form__" data-form-error="__communication_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__data_control_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__data_control" aria-expanded="false" aria-controls="__data_control">Data Control Settings</button>
                        </h5>
                    </div>
                    <div id="__data_control" class="collapse" aria-labelledby="__data_control_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__data_control_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Data Control Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Data Control Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Data Control Settings Updated" />
                                <?= __data__::createFormNumberInput("Profile", "maximumNumberOfReturnedSearchRecords", "Maximum Number of Returned Search Records", $profile1->getMaximumNumberOfReturnedSearchRecords(), true) ?>
                                <?= __data__::createFormNumberInput("Profile", "maximumNumberOfDisplayedRowsPerPage", "Maximum Number of Displayed Rows per Page", $profile1->getMaximumNumberOfDisplayedRowsPerPage(), true) ?>
                                <?= __data__::createFormNumberInput("Profile", "minimumAgeCriteriaForUsers", "Minimum Age Criteria for Users", $profile1->getMinimumAgeCriteriaForUsers(), true) ?>
                                <div id="__data_control_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__data_control_form__" data-form-error="__data_control_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__registration_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__registration" aria-expanded="false" aria-controls="__registration">Registration Settings</button>
                        </h5>
                    </div>
                    <div id="__registration" class="collapse" aria-labelledby="__registration_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__registration_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Registration Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Registration Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Registration Settings Updated" />
                                <?= __data__::createFormTextInput("Profile", "tinNumber", "TIN (Tax Identification Number)", $profile1->getTinNumber(), true) ?>
                                <div id="__registration_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__registration_form__" data-form-error="__registration_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark">
                    <div class="card-header bg-dark" id="__location_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__location" aria-expanded="false" aria-controls="__location">Location Settings</button>
                        </h5>
                    </div>
                    <div id="__location" class="collapse" aria-labelledby="__location_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__location_form__">
                                <input type="hidden" name="__classname__" value="Profile" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $profile1->getProfileId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Location Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Location Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Location Settings Updated" />
                                <?=  __data__::createFormSelectInput($conn, "Profile", "PHPTimezone", "Select Timezone", $profile1->getPHPTimezone(), true); ?>
                                <?= __data__::createFormSelectInput($conn, "Profile", "firstDayOfAWeek", "Select First Day of A Week", $profile1->getFirstDayOfAWeek(), true); ?>
                                <div id="__location_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__location_form__" data-form-error="__location_error__">Save</button>
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