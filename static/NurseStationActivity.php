<?php
class NurseStationActivity  {
    public static function getActivityUIForNurseStationQueue($thispage, $conn, $listOfNurseStationQueues)    {
        if (is_null($listOfNurseStationQueues)) return "";
        $profile1 = new Profile("Delta", __data__::$__PROFILE_INIT_ID, $conn);
        $queue0 = $listOfNurseStationQueues[0];
        $patient1 = $queue0->getPatient();
        $age = $patient1->calculateAge($profile1);
        $sex = $patient1->getSex()->getSexName();
        $window1 = "<div class=\"ui-nurse-station-activity\"><div><h5>Patient Examination Queue</h5><h5><span>Age : <b>$age</b></span> &nbsp; &nbsp; &nbsp; &nbsp;<span>Sex : <b>$sex</b></span></h5></div><div class=\"p-1 pl-2\">";
        $count = 0;
        foreach ($listOfNurseStationQueues as $queue1)  {
            $window1 .= "<div class=\"mb-2 queue-0001 border border-dotted-primary p-1\">";
            $serviceName = $queue1->getService()->getServiceName();
            $num = $queue1->getCurrentAttendanceSequence();
            $den = $queue1->getMaximumNumberOfAttendance();
            $status = UIStatus::getDangerRoundedStatus();
            $statusText = "Not Started";
            if ($num < $den && $num != 0) {
                $status = UIStatus::getWarningRoundedStatus();
                $statusText = "On Progress";
            }
            if ($num == $den  || $queue1->isCompleted()) {
                $status = UIStatus::getPrimaryRoundedStatus();
                $statusText = "Completed";
            }
            $time = $queue1->getTimeOfCreation()->getTimestamp();
            $requestedBy = $queue1->getRequestedBy()->getLoginName(); 
            $index = $count + 1;
            $window1 .= "<div><span>No: <b>$index</b></span>  => <span>Service: <b>$serviceName</b></span> <span>$status <b>$statusText</b></span> <span><b>( $num / $den )</b></span> <span>Requested by: <b>$requestedBy</b> on <b>$time</b></span></div>";   
            $listOfActivities = $queue1->getListOfActivities();
            if (! is_null($listOfActivities))   {
                $window1 .= "<div class=\"ml-5 pt-1 table-responsive\"><table class=\"table table-small\"><thead><tr><th>S/N</th><th>Time</th><th>Activity</th><th>By</th></tr></thead><tbody>";
                foreach ($listOfActivities as $nurseStationActivity1)   {
                    $sn = $nurseStationActivity1->getSequenceNumber();
                    $time = $nurseStationActivity1->getTimeOfCreation()->getTimestamp();
                    $resultText = "";
                    if (! is_null($nurseStationActivity1->getResultsText())) $resultText .= $nurseStationActivity1->getResultsText();
                    $progressName = $nurseStationActivity1->getProgress()->getProgressName();
                    $status = "";
                    switch ($nurseStationActivity1->getProgress()->progressId())    {
                        case (NurseStationActivityProgress::$__EXCELLENT):
                            $status = UIStatus::getPrimaryRoundedStatus();
                            break;
                        case (NurseStationActivityProgress::$__GOOD):
                            $status = UIStatus::getWarningRoundedStatus();
                            break;
                        case (NurseStationActivityProgress::$__BAD):
                            $status = UIStatus::getDarkRoundedStatus();
                            break;
                        case (NurseStationActivityProgress::$__WORSE):
                            $status = UIStatus::getDangerRoundedStatus();
                            break;
                    }
                    $resultText ." $status <i>$progressName</i>";
                    $attendedBy = $nurseStationActivity1->getAttendedBy()->getLoginName();
                    $window1 .= "<tr><th>$sn</th><td>$time</td><td>$resultText</td><td>$attendedBy</td></tr>";
                }
                $window1 .= "</tbody></table></div>";
            }
            $window1 .= "</div>"; //end of queue-0001
            $count++;
        } 
        $window1 .= "</div></div>";
        return $window1;
    }
}
?>