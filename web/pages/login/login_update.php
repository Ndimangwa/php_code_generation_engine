<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--> <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPDATING AN USER ACCOUNT
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=login";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "Login", array(
            array('pname' => 'fullName', 'caption' => 'Full Name', 'required' => true, 'placeholder' => 'Full Name'),
            array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Sex'),
            array('pname' => 'group', 'caption' => 'Group', 'required' => true, 'placeholder' => 'Group' ),
            array('pname' => 'jobTitle', 'caption' => 'Job Title', 'required' => true, 'placeholder' => 'Job Title' )
        ), "Updating a User Account", "update", $conn, $_GET['id'], array('__id__' => $_GET['id'], '__modal_title__' => 'User Accounts Updation Report'), null);
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to User Accounts</a></i><br/>
                        <span class="text-muted"><i>Rule: login_update</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>