<div>
<?php 
//Begin of dashboard_registered_patients
if (Authorize::isAllowable($config1, "dashboard_registered_patients")) {
    $type = "DASH_REG_PAT";
    $listCustom = "LCRP01";
    $customDateRange = "CRP01";
?>
    <div class="p-1 m-1 my-2 border border-info">
        <div class="text-center mb-1">
            <h4>Registered Patients</h4>
        </div>
        <?php
        if (isset($_REQUEST['type']) && ($_REQUEST['type'] == $type) && isset($_REQUEST[$listCustom])) {
            var_dump($_REQUEST);
            //$listOfRecords = Patient::getListOfRecordsCreated($conn, (new DateAndTime($_REQUEST['t1'])), (new DateAndTime($_REQUEST['t2'])));
            $listOfRecords = null;
            if (is_null($listOfRecords))    {
                echo UICardView::getDangerReportCard("No Record Found", "There were no records found within the time interval");
            } else {
                //echo UITabularView::
            }
        } else {
        ?>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= Patient::getNumberOfRecordsCreated($conn, $today1->getBeginOfADay(), $today1->getEndOfADay()) ?></h3>
                            <p>Today</p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bars"></i></div>
                        <a href="<?= $thispage ?>?<?= $listCustom ?>=1&t1=<?= $today1->getBeginOfADay()->getTimestamp() ?>&t2=<?= $today1->getEndOfADay()->getTimestamp() ?>&type=<?= $type ?>" class="small-box-footer">More Info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= Patient::getNumberOfRecordsCreated($conn, $yesterday1->getBeginOfADay(), $yesterday1->getEndOfADay()) ?></h3>
                            <p>Yesterday</p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bars"></i></div>
                        <a href="<?= $thispage ?>?<?= $listCustom ?>=1&t1=<?= $yesterday1->getBeginOfADay()->getTimestamp() ?>&t2=<?= $yesterday1->getEndOfADay()->getTimestamp() ?>&type=<?= $type ?>" class="small-box-footer">More Info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= Patient::getNumberOfRecordsCreated($conn, $dt7DaysAgo1->getBeginOfADay(), $today1->getEndOfADay()) ?></h3>
                            <p>Past 7 Days</p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bars"></i></div>
                        <a href="<?= $thispage ?>?<?= $listCustom ?>=1&t1=<?= $dt7DaysAgo1->getBeginOfADay()->getTimestamp() ?>&t2=<?= $today1->getEndOfADay()->getTimestamp() ?>&type=<?= $type ?>" class="small-box-footer">More Info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <?php
                $caption = "Past 30 Days";
                $timeFrom1 = $dt30DaysAgo1;
                $timeTo1 = $today1;
                if (isset($_REQUEST['type']) && ($_REQUEST['type'] == $type) && isset($_REQUEST[$customDateRange])) {
                    $caption = "Custom Days";
                    $timeFrom1 = DateAndTime::createDateAndTimeFromGUIDate($_REQUEST['timeOfCreation']);
                    $timeTo1 = DateAndTime::createDateAndTimeFromGUIDate($_REQUEST['timeOfUpdation']);
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= Patient::getNumberOfRecordsCreated($conn, $timeFrom1->getBeginOfADay(), $timeTo1->getEndOfADay()) ?></h3>
                            <p><?= $caption ?></p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bar"></i></div>
                        <div class="small-box-footer"><a data-args="<?= $__MODAL_IS_ENABLED ?>=1&<?= $customDateRange ?>=1" data-toggle="modal" data-target="#<?= $modalId ?>" class="text-left" href="<?= $thispage ?>?<?= $__MODAL_IS_ENABLED ?>=1&<?= $customDateRange ?>=1&type=<?= $type ?>" style="text-decoration: none; color: white;">Custom</a>&nbsp;&nbsp;<a class="text-right" style="text-decoration: none; color: white;" href="<?= $thispage ?>?<?= $listCustom ?>=1&t1=<?= $timeFrom1->getBeginOfADay()->getTimestamp() ?>&t2=<?= $timeTo1->getEndOfADay()->getTimestamp() ?>&type=<?= $type ?>">More Info <i class="fas fa-arrow-circle-right"></i></a></div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>
</div>