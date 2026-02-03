<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW USER GROUP
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=group";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "Group", array(
            array('pname' => 'groupName', 'caption' => 'Group Name', 'required' => true, 'placeholder' => 'Group Name'),
            array('pname' => 'parentGroup', 'caption' => 'Parent Group', 'required' => true, 'placeholder' => 'Parent Group')
        ), "Create Group", "create", $conn, 0, array('__modal_title__' => 'User Groups Creation Report'), null);
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to User Groups</a></i><br/>
                        <span class="text-muted"><i>Rule: group_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>