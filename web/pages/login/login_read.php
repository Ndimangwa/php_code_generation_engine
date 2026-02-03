<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--> <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    DETAILS
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=login";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $id = $_GET['id'];
        $formToDisplay = __data__::createDetailsPage($nextPage, "Login", array('loginName', 'fullName', 'group', 'sex', 'marital', 'jobTitle'), $conn, $id, array(
            array('pname' => 'context', 'href' => "$thispage?page=firewall_update&class=Login&id=$id", 'policy' => Authorize::isAllowable($config1, 'firewall_update', "normal", "donotsetlog", null, null), 'title' => 'Update firewall for User Account', 'icon-class' => 'fas fa-dungeon'),
            array('pname' => 'context', 'href' => "$thispage?page=firewall_read&class=Login&id=$id", 'policy' => Authorize::isAllowable($config1, 'firewall_read', "normal", "donotsetlog", null, null), 'title' => 'Graphical View of Existing Firewalls', 'icon-class' => 'fas fa-eye')
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
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to User Accounts</a></i><br/>
                        <span class="text-muted"><i>Rule: login_read</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>