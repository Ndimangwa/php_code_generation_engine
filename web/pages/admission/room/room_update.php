<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!--<div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--> <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPDATING ROOM
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=room";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "Room", array(
            array('pname' => 'roomNumber', 'caption' => 'Room Number', 'required' => true, 'placeholder' => '538'),
            array('pname' => 'ward', 'caption' => 'Ward', 'required' => true),
            array('pname' => 'observation', 'caption' => 'Observation Room'),
            array('pname' => 'private', 'caption' => 'Private Room'),
            array('pname' => 'comments', 'caption' => 'Comments', 'required' => false, 'placeholder' => 'Special Room')
        ), "Update a Room","update", $conn, $_GET['id'], array(
            '__id__' => $_GET['id'], 
            '__modal_title__' => 'Room Updation Report',
            'timeOfUpdation' => ($systemTime1->getTimestamp())
        ), null);
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
                        <span class="text-muted"><i>Rule: room_update</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>