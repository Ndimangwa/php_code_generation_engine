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
                    <div class="card-header bg-dark" id="__login_and_contacts_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__login_and_contacts" aria-expanded="true" aria-controls="__login_and_contacts">Login and Contacts Settings</button>
                        </h5>
                    </div>
                    <div id="__login_and_contacts" class="collapse show" aria-labelledby="__login_and_contacts_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__login_and_contacts_form__">
                                <input type="hidden" name="__classname__" value="Login" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $login1->getLoginId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Login and Contacts Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Login and Contacts Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Login and Contacts Settings Updated" />
                                <input type="hidden" name="__custom_context_name__" value="update_my_login"/>
                                <?= __data__::createFormEmailInput("Login", "email", "Email", $login1->getEmail(), true) ?>
                                <?= __data__::createFormTextInput("Login", "phone", "Phone", $login1->getPhone(), true) ?>
                                <div id="__login_and_contacts_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__login_and_contacts_form__" data-form-error="__login_and_contacts_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__change_password_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__change_password" aria-expanded="true" aria-controls="__change_password">Change Password Settings</button>
                        </h5>
                    </div>
                    <div id="__change_password" class="collapse" aria-labelledby="__change_password_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__change_password_form__">
                                <input type="hidden" name="__classname__" value="Login" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $login1->getLoginId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Change Password Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Change Password Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Change Password Settings Updated" />
                                <input type="hidden" name="__custom_context_name__" value="update_my_login"/>
                                <?= __data__::createFormPasswordInput("Login", "oldPassword", "Old Password", null, true, "password") ?>
                                <?= __data__::createFormPasswordInput("Login", "newPassword", "New Password", null, true, "password") ?>
                                <?= __data__::createFormPasswordInput("Login", "confirmNewPassword", "Confirm New Password", null, true, "password") ?>
                               <div id="__change_password_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-change-password-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__change_password_form__" data-form-error="__change_password_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__security_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__security" aria-expanded="true" aria-controls="__security">Security Settings</button>
                        </h5>
                    </div>
                    <div id="__security" class="collapse" aria-labelledby="__security_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__security_form__">
                                <input type="hidden" name="__classname__" value="Login" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $login1->getLoginId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Security Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Security Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Security Settings Updated" />
                                <input type="hidden" name="__custom_context_name__" value="update_my_login"/>
                                <?= __data__::createFormSelectInput($conn, "Login", "firstSecurityQuestion", "First Security Question", $login1->getFirstSecurityQuestion(), true) ?>
                                <?= __data__::createFormTextInput("Login", "firstSecurityAnswer", "First Security Answer", $login1->getFirstSecurityAnswer(), true) ?>
                                <?= __data__::createFormSelectInput($conn, "Login", "secondSecurityQuestion", "Second Security Question", $login1->getSecondSecurityQuestion(), true) ?>
                                <?= __data__::createFormTextInput("Login", "secondSecurityAnswer", "Second Security Answer", $login1->getSecondSecurityAnswer(), true) ?>
                                <div id="__security_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__security_form__" data-form-error="__security_error__">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card border-dark mb-2">
                    <div class="card-header bg-dark" id="__social_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#__social" aria-expanded="true" aria-controls="__social">Social Settings</button>
                        </h5>
                    </div>
                    <div id="__social" class="collapse" aria-labelledby="__social_header" data-parent="#accordion">
                        <div class="card-body">
                            <form method="POST" id="__social_form__">
                                <input type="hidden" name="__classname__" value="Login" />
                                <input type="hidden" name="__query__" value="update" />
                                <input type="hidden" name="__id__" value="<?= $login1->getLoginId() ?>" />
                                <input type="hidden" name="__modal_title__" value="Social Settings" />
                                <input type="hidden" name="__modal_success_message__" value="Social Settings were saved successful" />
                                <input type="hidden" name="__log_message__" value="Social Settings Updated" />
                                <input type="hidden" name="__custom_context_name__" value="update_my_login"/>
                                <?= __data__::createFormSelectInput($conn, "Login", "marital", "Marital Status", $login1->getMarital(), true) ?>
                                <?= __data__::createFormSelectInput($conn, "Login", "firstDayOfAWeek", "First Day of A Week", $login1->getFirstDayOfAWeek(), true) ?>
                                <div id="__social_error__" class="p-2 ui-sys-error-message"></div>
                                <button type="button" class="btn-send-dialog-ajax btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__social_form__" data-form-error="__social_error__">Save</button>
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