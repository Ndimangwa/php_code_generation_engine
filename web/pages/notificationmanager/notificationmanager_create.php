<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW NOTIFICATION MANAGER
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=notificationmanager";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "NotificationManager", array(
            array('pname' => 'managerName', 'caption' => 'Manager Title', 'required' => true, 'placeholder' => 'Manager Title'),
            array('pname' => 'operationName', 'caption' => 'Operation Name', 'required' => true, 'placeholder' => 'i.e patient_create'),
            array('pname' => 'targetReference', 'caption' => 'Target Reference', 'required' => true, 'placeholder' => 'ie JobTitle.7', 'title' => 'Must be the name of the Class followed by dot then the id, Class can be Login for User Accounts, JobTitle for JobTitles and Group for Groups; id can be checked on the url when you open a page of a respective User Accounts, JobTitle or Group'),
            array('pname' => 'category', 'caption' => 'Notification Category', 'required' => true),
            array('pname' => 'numberOfValidDays', 'caption' => 'Valid Days', 'required' => true, 'placeholder' => '100'),
            array('pname' => 'URLArguments', 'caption' => 'URL Arguments', 'required' => false, 'placeholder' => 'page=yyyyy', 'title' => 'Arguments to be appended to the URL where the notification would be forwarded to')
        ), "Create Notification Manager","create", $conn, 0, array('__modal_title__' => 'Notification Manager Creation Report', 'timeOfCreation' => $systemTime1->getTimestamp(), 'timeOfUpdation' =>$systemTime1->getTimestamp()), null);
        echo $formToDisplay;
    } catch (Exception $e)  {
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Notification Manager</a></i><br/>
                        <span class="text-muted"><i>Rule: notificationmanager_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>