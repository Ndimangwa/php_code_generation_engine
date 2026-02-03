<div>
    <?php
    $mycolumnlist = "listOfExaminations";
    $fieldArray1 = array($mycolumnlist);
    $lookupArray1 = array($mycolumnlist => "listOfServices");
    if (is_null($examinationQueue1)) {
        $enableUpdate = false;
        foreach ($fieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                $pname = isset($lookupArray1[$colname]) ? $lookupArray1[$colname] : $colname;
                if ($colname == $mycolumnlist) {
                    //Need to update enableUpdate
                }
            }
        }
        //New One
        if ($enableUpdate) {
            $examinationQueue1 = new PatientExaminationQueue("Delta", __data__::insert($conn, "PatientExaminationQueue", $colArray1, !$erollback, null), $conn);
        }
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($fieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                $pname = isset($lookupArray1[$colname]) ? $lookupArray1[$colname] : $colname;
                $fieldValue = $examinationQueue1->getMyPropertyValue($pname);
                if ($_POST[$colname] != $fieldValue) {
                    if ($colname == $mycolumnlist) {
                        //Need to update enableUpdate
                    }
                }
            }
        }
        //Need to perform update
        if ($enableUpdate) {
            $examinationQueue1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>