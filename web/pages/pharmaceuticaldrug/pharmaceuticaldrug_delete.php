<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div> -->
        <div class="offset-md-1 col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    DELETING DRUG
                </div>
                <div class="card-body text-dark" style="font-size: 1.2em;">
<?php
    $nextPage = $thispage."?page=pharmaceuticaldrug_delete";
    $cname = "pharmaceuticaldrug_delete";
    $conn = null;
    $isrollback = false;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        if (isset($_GET['submit']))    {
            $conn->beginTransaction();
            $isrollback = true;
            $drug1 = new PharmaceuticalDrug("Hello", $_GET['id'], $conn);
            //Delete drug 1st 
            $drug1->delete(! $isrollback);
            //Delete the corresponding service
            $drug1->getService()->delete(! $isrollback);
            //Log logs
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $drug1->getDrugName(), ! $isrollback);
            $conn->commit();
            $isrollback = false;
            echo UICardView::getSuccesfulReportCard("Deletion Report", "You have successful deleted the drug from the system");
        } else {
            $id = $_REQUEST['id'];
            echo UICardView::getDeleteConfirmationReportCard(
                $thispage."?page=$page&id=$id&submit=1",
                "Confirm Deletion",
                "You are About to Delete Drug; Note: This action can not be reversed",
                "Confirm Deletion"
            );
        }
    } catch (Exception $e)  {
        if ($isrollback) $conn->rollBack();
        echo __data__::showDangerAlert($e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Pharmaceutical Drug</a></i><br/>
                        <span class="text-muted"><i>Rule: <?= $cname ?></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>