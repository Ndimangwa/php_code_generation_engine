<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!--<div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--> <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPDATING SYSTEM USER
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=systemuser";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $user1 = new SystemUser("Default", $_REQUEST['id'], $conn);
        if ($_POST['submit'])   {
            if ($_POST['efilter'] != $user1->getExtraFilter())  throw new Exception("System Detected a Browser Replay");
            $user1->setExtraFilter(__object__::getCodeString(32))->update();
            $login1 = $user1->getLogin();
            $login1->updateList($_POST);
            $login1->update();
?>
        <div class="document-creator m-2">
            <?= __data__::showPrimaryAlert("You have successful updated the System User") ?>
        </div>
<?php
        } else {
            $formToDisplay = __data__::createDataCaptureForm($nextPage, "SystemUser", array(
                array('pname' => 'email', 'caption' => 'Email', 'required' => true, 'placeholder' => 'info@mbwambo.org', 'use-class' => 'Login', 'title' => 'Email can also be used as an alternative of username in login onto the system'),
                array('pname' => 'phone', 'caption' => 'Phone', 'required' => true, 'placeholder' => '0xxxxxxxxx', 'use-class' => 'Login'),
                array('pname' => 'fullName', 'caption' => 'Full Name', 'required' => true, 'placeholder' => 'Said Mbwambo', 'use-class' => 'Login'),
                array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Male/Female', 'use-class' => 'Login'),
                array('pname' => 'group', 'caption' => 'Group', 'required' => true, 'placeholder' => 'Group', 'use-class' => 'Login'),
                array('pname' => 'jobTitle', 'caption' => 'Job Title', 'required' => true, 'placeholder' => 'Job Title', 'use-class' => 'Login') 
            ), "Update System User", "update", $conn, $_GET['id'], array(
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "page" => $page,
                "id" => $_GET['id'],
                "efilter" => $user1->getExtraFilter(),
                "submit" => true
            ), null, null, null, $thispage, true);
            echo $formToDisplay;
        }
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to System User</a></i><br/>
                        <span class="text-muted"><i>Rule: systemuser_update</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>