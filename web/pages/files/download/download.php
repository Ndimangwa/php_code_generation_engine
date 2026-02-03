<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPLOAD FILE
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "JobTitle", array(
            array('pname' => 'jobName', 'caption' => 'Job Title', 'required' => true, 'placeholder' => 'Name of Job Title')
        ), "Create Job Title", "create", $conn, 0, array('__modal_title__' => 'Job Title Creation Report'), null);
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Dashboard</a></i><br/>
                        <span class="text-muted"><i>Rule: (Root)</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>