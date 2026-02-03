<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW MEDICAL
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=medicaldoctor";
    $transactionIsON = false;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        if (isset($_POST['submit']))    {
            $conn->beginTransaction();
            $transactionIsON = true;
            $colArray1 = $_POST;
            $colArray1['userReference'] = "userReference";
            $login1 = new Login("Default", __data__::insert($conn, "Login", $colArray1, false, Constant::$default_select_empty_value), $conn);
            $colArray1 = array("login" => $login1->getLoginId());
            $colArray1['specialist'] = isset($_POST['specialist']) ? 1 : 0;
            $doctor1 = new MedicalDoctor("Default", __data__::insert($conn, "MedicalDoctor", $colArray1, false, null), $conn);
            $login1->setUserReference($doctor1->getObjectReferenceString());
            $login1->update(false);
            $conn->commit();
            $transactionIsON = false;
            //We need to Display a successful Message 
            $loginName = $_POST['loginName'];
?>
            <div class="document-creator m-2">
                <?= __data__::showPrimaryAlert("You have successful created a Medical Doctor $loginName") ?>
            </div>
<?php
        } else {
            $formToDisplay = __data__::createDataCaptureForm($nextPage, "MedicalDoctor", array(
                array('pname' => 'loginName', 'caption' => 'Username', 'required' => true, 'placeholder' => 'Username/Login Name', 'use-class' => 'Login', 'title' => 'Username/Login Name is used for login into the system'),
                array('pname' => 'email', 'caption' => 'Email', 'required' => true, 'placeholder' => 'info@mbwambo.org', 'use-class' => 'Login', 'title' => 'Email can also be used as an alternative of username in login onto the system'),
                array('pname' => 'phone', 'caption' => 'Phone', 'required' => true, 'placeholder' => '0xxxxxxxxx', 'use-class' => 'Login'),
                array('pname' => 'fullName', 'caption' => 'Full Name', 'required' => true, 'placeholder' => 'Said Mbwambo', 'use-class' => 'Login'),
                array('pname' => 'sex', 'caption' => 'Sex', 'required' => true, 'placeholder' => 'Male/Female', 'use-class' => 'Login'),
                array('pname' => 'group', 'caption' => 'Group', 'required' => true, 'placeholder' => 'Group', 'use-class' => 'Login'),
                array('pname' => 'jobTitle', 'caption' => 'Job Title', 'required' => true, 'placeholder' => 'Job Title', 'use-class' => 'Login'),
                array('pname' => 'specialist', 'caption' => 'Specialist Doctor', 'title' => 'Check the box if a Doctor is a Specialist, otherwise leave unchecked')
            ), "Create System User", "create", $conn, 0, array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "password" => sha1("12345678"),
                "page" => $page,
                "submit" => true
            ), null, null, null, $thispage, true);
            echo $formToDisplay;
        }
    } catch (Exception $e)  {
        if ($transactionIsON) $conn->rollBack();
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Medical Doctor</a></i><br/>
                        <span class="text-muted"><i>Rule: medicaldoctor_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>