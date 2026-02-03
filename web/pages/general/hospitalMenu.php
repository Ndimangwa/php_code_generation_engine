<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <?= $profile1->getSystemName() ?>
                </div>
                <div class="card-body">
<!--MENU BEGIN--> 
<div>
<?php 
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $nextpage = $thispage."?menu=1";
        include("navigationMenu.php");
        $conn = null;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
?>
</div>
<!--MENU END-->
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <!-- <i><a href="<?= $nextPage ?>" class="card-link">Back to User Groups</a></i><br/> -->
                        <!-- <span class="text-muted"><i>Rule: no-rule</i></span> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>