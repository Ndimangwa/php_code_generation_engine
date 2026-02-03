<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPDATE DRUG
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $isrollback = false;
    $nextPage = $thispage."?page=pharmaceuticaldrug";
    $cname = "pharmaceuticaldrug_update";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $drug1 = new PharmaceuticalDrug("Hello", $_REQUEST['id'], $conn);
        if (isset($_POST['submit']))    {
            if ($_POST['efilter'] != $drug1->getExtraFilter()) throw new Exception("Perhaps a browser replay, or someone else is editing");
            $conn->beginTransaction();
            $isrollback = true;
            $colArray1 = $_POST;
            $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
            $colArray1['serviceName'] = $colArray1['drugName'];
            $colArray1['extraFilter'] = __object__::getCodeString(32);
            //Updating Services 1st
            $drug1->getService()->updateList($colArray1, Constant::$default_select_empty_value)->update(! $isrollback);
            //Updating Drug
            $drug1->updateList($colArray1, Constant::$default_select_empty_value)->update(! $isrollback);
            //Update to reflect new name
            $drug1 = new PharmaceuticalDrug("Dell", $drug1->getDrugId(), $conn);
            //Common Log 
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $drug1->getDrugName(), ! $isrollback);
           $conn->commit();
            $isrollback = false;
            echo UICardView::getSuccesfulReportCard("Drug Updated", "You have successful updated an existing Drug");
        } else {
            $drug1->setExtraFilter(__object__::getCodeString(32))->update(! $isrollback);
            echo __data__::createDataCaptureForm($thispage, "PharmaceuticalDrug", array(
                array("pname" => "drugName", "caption" => "Drug Name", "placeholder" => "Ampiciline", "required" => true),
                array("pname" => "amount", "caption" => "Amount", "placeholder" => "1500", "required" => true, "use-class" => "Service"),
                array("pname" => "currency", "caption" => "Currency", "required" => true, "use-class" => "Service"),
                array("pname" => "unitOfMeasurement", "caption" => "Unit", "required" => true)
            ), "Update a Drug", "update", $conn, $_GET['id'], array(
                "page" => "pharmaceuticaldrug_update",
                "submit" => 1,
                "id" => $_GET['id'],
                "efilter" => ($drug1->getExtraFilter())
            ), null, null, "alt-hideee", $thispage, true);
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