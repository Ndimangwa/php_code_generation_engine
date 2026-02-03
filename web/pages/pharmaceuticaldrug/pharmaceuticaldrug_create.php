<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW DRUG
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $isrollback = false;
    $nextPage = $thispage."?page=pharmaceuticaldrug";
    $cname = "pharmaceuticaldrug_create";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        if (isset($_POST['submit']))    {
            $conn->beginTransaction();
            $isrollback = true;
            $colArray1 = $_POST;
            $colArray1['timeOfCreation'] = $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
            $colArray1['serviceName'] = $colArray1['drugName'];
            $colArray1['category'] = ServiceCategory::$__PHARMACEUTICAL;
            //Updating Services 1st
            $service1 = new Service("Hello", __data__::insert($conn, "Service", $colArray1, ! $isrollback, Constant::$default_select_empty_value), $conn);
            $colArray1['temporaryObjectHolder'] = $service1->getObjectReferenceString();
            $colArray1['service'] = $service1->getServiceId();
            //Updating Drug
            $drug1 = new PharmaceuticalDrug("Hello", __data__::insert($conn, "PharmaceuticalDrug", $colArray1, ! $isrollback, Constant::$default_select_empty_value), $conn);
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $drug1->getDrugName(), ! $isrollback);
            $conn->commit();
            $isrollback = false;
            echo UICardView::getSuccesfulReportCard("Drug Created", "You have successful created a new Drug");
        } else {
            echo __data__::createDataCaptureForm($thispage, "PharmaceuticalDrug", array(
                array("pname" => "drugName", "caption" => "Drug Name", "placeholder" => "Ampiciline", "required" => true),
                array("pname" => "amount", "caption" => "Amount", "placeholder" => "1500", "required" => true, "use-class" => "Service"),
                array("pname" => "currency", "caption" => "Currency", "required" => true, "use-class" => "Service"),
                array("pname" => "unitOfMeasurement", "caption" => "Unit", "required" => true)
            ), "Create a Drug", "create", $conn, 0, array(
                "page" => "pharmaceuticaldrug_create",
                "submit" => 1
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