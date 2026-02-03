<?php 
$__INDEX_DRUG_NAME = 0;
$__INDEX_UNIT_OF_MEASUREMENT = 1;
$__INDEX_QUANTITY = 2;
$__INDEX_USAGE = 3;
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Dispense Drug
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=$page";
                    $erollback = false;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $queue1 = new PatientDrugQueue("Delta", $_REQUEST['qid'], $conn);
                        if ($queue1->isCompleted()) throw new Exception("This Queue is Already Completed!!!");
                        if (isset($_POST['submit'])) {
                            $conn->beginTransaction();
                            $erollback = true;
                            if ($_POST['efilter'] != $queue1->getExtraFilter()) throw new Exception("Page Multiple Submission has been detected");
                            $queue1->setExtraFilter(__object__::getMD5CodedString("Hello World"))->update(! $erollback);
                            //Step 1 : Merge the payload from queue [ patient, patientCase, patientVisit, bundleCode ]
                            $colArray1 = array_merge($queue1->getMyPayload(array("patient", "patientCase", "visit", "bundleCode")), array(
                                "timeOfCreation" => ( $systemTime1->getTimestamp() ),
                                "timeOfUpdation" => ( $systemTime1->getTimestamp() )
                            ));
                            //Step 2 : Insert into DispensedPatientDrug
                            $listOfDispensedDrugs = array();
                            foreach ($_POST['managementId'] as $index => $managementId) {
                                $drugName = (isset($_POST['drugName']) && isset($_POST['drugName'][$index])) ? ( $_POST['drugName'][$index] ) : null;
                                $unitName = (isset($_POST['unitName']) && isset($_POST['unitName'][$index])) ? ( $_POST['unitName'][$index] ) : null;
                                $quantity = (isset($_POST['quantity']) && isset($_POST['quantity'][$index])) ? ( $_POST['quantity'][$index] ) : null;
                                $usage = (isset($_POST['usage']) && isset($_POST['usage'][$index])) ? ( $_POST['usage'][$index] ) : null;
                                if (is_null($drugName) || is_null($unitName) || is_null($quantity) || is_null($usage)) throw new Exception("Either Drug Name or Unit or Quantity or Usage is not specified at index [ $index ]");
                                $listOfDispensedDrugs[sizeof($listOfDispensedDrugs)] = __data__::insert($conn, "DispensedPatientDrug", array_merge($colArray1, array(
                                    "drugManagement" => $managementId,
                                    "drugQueue" => ( $queue1->getQueueId() ),
                                    "usage" => $usage,
                                    "quantity" => $quantity
                                )), ! $erollback, Constant::$default_select_empty_value);    
                            }
                            //Step 3 : Insert into DispensedPatientDrugManager
                            $dispensedDrugManager1 = new DispensedPatientDrugManager("Delta", __data__::insert($conn, "DispensedPatientDrugManager", array_merge($colArray1, array(
                                "listOfDispensedDrugs" => implode(",", $listOfDispensedDrugs),
                                "completed" => 1
                            )), ! $erollback, Constant::$default_select_empty_value), $conn);
                            //Step 4 : Update PatientFile
                            PatientFile::addPatientDrugDispensedLog($conn, $systemTime1, $queue1->getVisit(), $login1, $dispensedDrugManager1, $queue1->getBundleCode(), ! $erollback);
                            //Step 5 : Queue Completed and resetFlag on MedicalDoctorQueue
                            $queue1->setCompleted(true)->update(! $erollback);
                            $conn->commit();
                            $erollback = false;
                            echo UICardView::getSuccesfulReportCard("Drug Dispensed", "You have successfully dispensed drugs");
                        } else {
                            //Preparing Payload 
                            $payload = array(
                                "settings" => array(
                                    "serial-number" => array(
                                        "enable" => true,
                                        "start-at" => 1,
                                        "step" => 1
                                    ),
                                    "row-id" => array(
                                        "name" => "managementId"
                                    )
                                )
                            );
                            //$listOfDrugManagements = $queue1->getListOfDrugManagements();
                            $listOfDrugManagements = PatientDrugQueue::getListOfNotYetDispensedDrugManagement($conn, $queue1->getQueueId());
                            if (is_null($listOfDrugManagements)) throw new Exception("There is no yet drug management");
                            $payload["colnames"] = array(
                                ( $__INDEX_DRUG_NAME ) => array(
                                    "pname" => "drugName",
                                    "caption" => "Name of Drug",
                                    "use-class" => "PharmaceuticalDrug",
                                    "class-id" => null,
                                    "required" => true,
                                    "readonly" => true
                                ), 
                                ( $__INDEX_UNIT_OF_MEASUREMENT ) => array(
                                    "pname" => "unitName",
                                    "caption" => "Unit",
                                    "use-class" => "UnitOfMeasurement",
                                    "class-id" => null,
                                    "required" => true,
                                    "readonly" => true
                                ),
                                ( $__INDEX_QUANTITY ) => array(
                                    "pname" => "quantity",
                                    "caption" => "Quantity",
                                    "use-class" => "PatientDrugManagement",
                                    "readonly" => true,
                                    "required" => true
                                ),
                                ( $__INDEX_USAGE ) => array(
                                    "pname" => "usage",
                                    "use-class" => "PatientDrugManagement"
                                )
                            );
                            $payload["row-ids"] = array();
                            $payload["rows"] = array();
                            foreach ($listOfDrugManagements as $management1)    {
                                $row_index = sizeof($payload["rows"]);
                                $payload["row-ids"][$row_index] = $management1->getManagementId();
                                $payload["rows"][$row_index] = array();
                                //Now working with columns 
                                //1-Drug Name 
                                $tArray1 = array(
                                    "value" => ( $management1->getPharmaceuticalDrug()->getDrugName() )
                                );
                                $payload["rows"][$row_index][$__INDEX_DRUG_NAME] = $tArray1;
                                //2-Units
                                $tArray1 = array(
                                    "value" => ( $management1->getPharmaceuticalDrug()->getUnitOfMeasurement()->getUnitName() )
                                );
                                $payload["rows"][$row_index][$__INDEX_UNIT_OF_MEASUREMENT] = $tArray1;
                                //3-Quantity
                                $tArray1 = array(
                                    "value" => ( $management1->getQuantity() )
                                );
                                $payload["rows"][$row_index][$__INDEX_QUANTITY] = $tArray1;
                                //4-Usage
                                $tArray1 = array(
                                    "value" => ( $management1->getUsage() ) 
                                );
                                $payload["rows"][$row_index][$__INDEX_USAGE] = $tArray1;
                            }
                            $queue1->setExtraFilter(__object__::getMD5CodedString("Hello World"))->update(! $erollback);
                            //Now Display
                            echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientDrugQueue", array(), "Dispense Drug", "custom-tabular", $conn, $queue1->getQueueId(), array(
                                "page" => $page,
                                "qid" => ( $queue1->getQueueId() ),
                                "submit" => true,
                                "efilter" => ( $queue1->getExtraFilter() )
                            ), null, null, "btn-send-me", $thispage, true, $payload));
                        }
                    } catch (Exception $e) {
                        if ($erollback) $conn->rollBack();
                        //echo __data__::showDangerAlert($e->getMessage());
                        echo UICardView::getDangerReportCard("Dispense Drugs", $e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to List</a></i><br />
                        <span class="text-muted"><i>Rule: dispense_drug</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
