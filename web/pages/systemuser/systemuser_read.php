<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    DETAILS FOR SYSTEM USER
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=systemuser";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $user1 = new SystemUser("Addd", $_GET['id'], $conn);
        $formToDisplay = $user1->getMyDetailView(array(
            array("pname" => "loginName", "caption" => "Username", "use-class" => "Login"),
            array("pname" => "group", "use-class" => "Login"),
            array("pname" => "sex", "use-class" => "Login"),
            array("pname" => "marital", "use-class" => "Login"),
            array("pname" => "jobTitle", "use-class" => "Login")
        ));
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to System User</a></i><br/>
                        <span class="text-muted"><i>Rule: systemuser_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>