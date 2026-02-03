<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    DELETING ROOM (CONFIRM)
                </div>
                <div class="card-body text-dark" style="font-size: 1.2em;">
<?php
    $nextPage = $thispage."?page=room";
    $conn = null;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createConfirmationForm($nextPage, "Room", "You are about to delete permanently, a Room; NOTE: This Action is irreversible","Delete a Room", "delete", $conn, $_GET['id'], array('__id__' => $_GET['id'], '__modal_title__' => 'Delete Confirmation Report'), null);
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Rooms Management</a></i><br/>
                        <span class="text-muted"><i>Rule: room_delete</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>