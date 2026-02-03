<div>
    <?php
    $fieldArray1 = array(
        "surgeon",
        "anaesthetist", 
        "typeOfAnaesthetist",
        "theatre",
        "surgeryTime",
        "surgeryDuration",
        "startCuttingTime",
        "endCuttingTime",
        "position",
        "incision",
        "status",
        "procedureDescriptionAndClosure",
        "identificationOfProsthesis",
        "estimatedBloodLoss",
        "problemsAndComplications",
        "drain",
        "technicalComments"
    );
    //We need to set general items like completed
    $colArray1['completed'] = ( isset($_POST['status']) && ( $_POST['status'] == ( SurgeryStatus::$__DONE ) ) ) ? 1 : 0;
    if (is_null($patientOperation1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($fieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname], array(Constant::$default_select_empty_value)))) {
               $refClassname = PatientOperation::getReferenceClass($colname);
               //$type = PatientOperation::getColumnType($colname);
               if ($refClassname == "DateAndTime")  {
                    try {
                        $t1 = new DateAndTime($_POST[$colname]);
                        $colArray1[$colname] = $t1->getTimestamp();
                        $enableUpdate = true;
                    } catch (Exception $e)  {

                    }
               } else if ($refClassname == "MedicalComment")    {
                    $colArray1[$colname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                        'comments' => $_POST[$colname]
                    )), ! $erollback);
                    $enableUpdate = true;
               } else {
                   //Here available is text-data and integer-data
                    $colArray1[$colname] = $_POST[$colname];
                    $enableUpdate = true;
               }
            }
        }
        //New One 
        if ($enableUpdate) {
            $patientOperation1 = new PatientOperation("Delta", __data__::insert($conn, "PatientOperation", $colArray1, !$erollback), $conn);
        }
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp()),
            "completed" => $colArray1["completed"]
        );
        $enableUpdate = false;
        foreach ($fieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname], array(Constant::$default_select_empty_value)))) {
                $refClassname = PatientOperation::getReferenceClass($colname);
                $propertyValue1 = $patientOperation1->getMyPropertyValue($colname);
                $type = PatientOperation::getColumnType($colname);
                if ($refClassname == "DateAndTime") {
                    try {
                        $t1 = DateAndTime::createDateAndTimeFromGUIDate($_POST[$colname]);
                        if (is_null($propertyValue1) || ( ( $propertyValue1->getTimestamp() ) != ( $t1->getTimestamp() ) )) {
                            $updateArray1[$colname] = $t1->getTimestamp();
                            $enableUpdate = true;
                        }
                    } catch (Exception $e)  {

                    }
                } else if ($refClassname == "MedicalComment")   {
                    if (is_null($propertyValue1))   {
                        $updateArray1[$colname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                            'comments' => $_POST[$colname]
                        )), ! $erollback);
                        $enableUpdate = true;
                    } else {
                        if ($propertyValue1->getComments() != $_POST[$colname]) {
                            $propertyValue1->setComments($_POST[$colname])->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
                            $enableUpdate = true; //Dummy update
                        }
                    }
                } else if ($type == "object") {
                    //Now working with other general-objects
                    if (is_null($propertyValue1))   {
                        $updateArray1[$colname] = $_POST[$colname];
                        $enableUpdate = true;
                    } else {
                        if ($_POST[$colname] != $propertyValue1->getId0())  {
                            $updateArray1[$colname] = $_POST[$colname];
                            $enableUpdate = true;
                        }
                    }   
                } else {
                    //string, integer, text
                    if ($_POST[$colname] != $propertyValue1)    {
                        $updateArray1[$colname] = $_POST[$colname];
                        $enableUpdate = true;
                    }
                }
            }
        }
       if ($enableUpdate) {
            $patientOperation1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>