<div>
    <?php
    if (__data__::isNotEmpty($_POST['medicalComment'], array(Constant::$default_select_empty_value)))   {
        //Working now to extract 
        $medicalComment1 = $consultationQueue1->getMedicalComment();
        $updateArray1 = array(
            "timeOfUpdation" => $systemTime1->getTimestamp()
        );
        $enableUpdate = false;
        if (is_null($medicalComment1))  {
            //Build one 
            $medicalComment1 = new MedicalComment("Delta", __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                "comments" => $_POST['medicalComment']
            )), ! $erollback), $conn);
            $enableUpdate = true;
        } else {
            //Update existing
            if ($medicalComment1->getComments() != $_POST['medicalComment'])    {
                $medicalComment1->setUpdateList(array_merge($updateArray1, array(
                    "comments" => $_POST['medicalComment']
                )))->update(! $erollback);
                $enableUpdate = true;
            }
        }
        //Now Update ConsultationQueue
        if ($enableUpdate)  {
            $consultationQueue1->setUpdateList(array_merge($updateArray1, array(
                "medicalComment" => ( $medicalComment1->getCommentId() )
            )))->update(! $erollback);
        }
    }
    ?>
</div>