<div>
    <?php
    $provisionlistcolumn = "listOfDifferentialDiseases";
    $provisionDiagnosisFieldArray1 = array($provisionlistcolumn);
    //Now we need to insert into provisionDiagnosis, otherwise we need to update the existing 
    if (is_null($provisionDiagnosis1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($provisionDiagnosisFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                if ($colname == $provisionlistcolumn) {
                    $tArray1 = $_POST[$colname];
                    $diffDiseaseArray1 = array();
                    for ($i = 0; $i < sizeof($tArray1); $i++) {
                        $t1 = $tArray1[$i];
                        if ($i == 0) {
                            $colArray1["mainDisease"] = $t1;
                        } else {
                            $diffDiseaseArray1[sizeof($diffDiseaseArray1)] = $t1;
                        }
                    }
                    if (sizeof($diffDiseaseArray1) > 0) {
                        $colArray1[$colname] = implode(",", $diffDiseaseArray1);
                    }
                    $enableUpdate = true;
                }
            }
        }
        //New One 
        if ($enableUpdate) {
            $provisionDiagnosis1 = new ProvisionDiagnosis("Delta", __data__::insert($conn, "ProvisionDiagnosis", $colArray1, !$erollback), $conn);
        }
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($provisionDiagnosisFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                //We need to check if exists link or we need to establish a new one
                //start do here
                if ($colname == $provisionlistcolumn) {
                    $tArray1 = $_POST[$colname];
                    $diffDiseaseArray1 = array();
                    for ($i = 0; $i < sizeof($tArray1); $i++) {
                        $t1 = $tArray1[$i];
                        if ($i == 0) {
                            $updateArray1["mainDisease"] = $t1;
                        } else {
                            $diffDiseaseArray1[sizeof($diffDiseaseArray1)] = $t1;
                        }
                    }
                    $updateArray1[$colname] = implode(",", $diffDiseaseArray1);
                    $enableUpdate = true;
                }
                //end do here
            }
        }
        //Update 
        //$provisionDiagnosis1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
        if ($enableUpdate) {
            $provisionDiagnosis1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>