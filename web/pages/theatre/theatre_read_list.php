<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Operation Waiting List
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage /*. "?page=$page"*/;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT q.queueId as queueId, q.timeOfUpdation as timeOfUpdation, registrationNumber, surname, otherNames, visitCount FROM _patient_operation_queue as q, _patient as p, _patientVisit as v, _patientCase as c, _patient_admission_queue as qa WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (q.admissionQueue = qa.queueId) AND (qa.completed = 1) AND (q.pendingPayment = 0) AND (q.completed = 0)";
                        $records = __data__::getSelectedRecords($conn, $query, false);
                    ?>
                        <div id="__ui_tabular_view_ctn__001__" class="ui-view ui-tabular-view">
                            <div class="bg-primary p-1">
                                <div class="bg-warning p-1">
                                    <div class="bg-primary p-1">
                                        <div class="bg-white p-1">
                                            <div>
                                                <form action="<?= $thispage ?>">
                                                    <div class="input-group mb-3">
                                                        <input type="search" class="ui-tabular-view-search form-control" data-min-length="3" placeholder="Search">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary ui-tabular-view-btn-search" type="button" data-toggle="tooltip" title="" data-original-title="Click to Search">Search</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="table-responsive" data-max-rows-per-page="<?= $profile1->getMaximumNumberOfDisplayedRowsPerPage() ?>">
                                                <table class="table ui-tabular-view-table">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Reg No</th>
                                                            <th>Name</th>
                                                            <th>Operation</th>
                                                            <th>Status</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sncount = 0;
                                                        foreach ($records['column'] as $record1) {
                                                            $patientOperationQueue1 = new PatientOperationQueue("Delta", $record1['queueId'], $conn);
                                                            $patient1 = $patientOperationQueue1->getPatient();
                                                            $regNumber = $patient1->getRegistrationNumber();
                                                            $patientName = $patient1->getPatientName();
                                                            $listOfServices = $patientOperationQueue1->getListOfServices();
                                                            if (is_null($listOfServices)) continue;
                                                            foreach ($listOfServices as $service1) {
                                                                $operationName = $service1->getServiceName();
                                                                $listOfCompletedOperationsForThisQueueAndService = PatientOperation::filterRecords($conn, array(
                                                                    'operationQueue' => ( $patientOperationQueue1->getQueueId() ),
                                                                    'service' => ( $service1->getServiceId() ),
                                                                    'completed' => 1
                                                                ));
                                                                $status = is_null($listOfCompletedOperationsForThisQueueAndService) ? "Not Done" : "Done";
                                                                $link1 = UIControls::getAnchorTag("Recordings", $thispage, array(
                                                                    'page' => $page,
                                                                    'qid' => ( $patientOperationQueue1->getQueueId() ),
                                                                    'serviceId' => ( $service1->getServiceId() )
                                                                ), array('card-link'));
                                                        ?>
                                                                <tr>
                                                                    <th class="data-serial"><?= ++$sncount ?></th>
                                                                    <td class="data-search"><?= $regNumber ?></td>
                                                                    <td class="data-search"><?= $patientName ?></td>
                                                                    <td class="data-search"><?= $operationName ?></td>
                                                                    <td class="data-search"><?= $status ?></td>
                                                                    <td><?= $link1 ?></td>
                                                                </tr>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        //echo $windowToDisplay1;
                    } catch (Exception $e) {
                        echo UICardView::getDangerReportCard("Operation Queue", $e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Dashboard</a></i><br />
                        <span class="text-muted"><i>Rule: theatre_read_list</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>