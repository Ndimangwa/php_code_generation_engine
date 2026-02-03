<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    MEDICAL DOCTOR CONSULTATION
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=medicaldoctor_consult";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                    ?>
                        <div class="bg-dark p-1 mb-2">
                            <div class="bg-white p-1">
                                <form action="<?= $thispage ?>" method="POST">
                                    <input type="hidden" name="page" value="<?= $page ?>"/>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="my_or_all">Patients</label>
                                            <select name="my_or_all" id="my_or_all" class="form-control">
                                                <option selected value="<?= Patient::$__MY_PATIENTS ?>">My Patients</option>
                                                <option value="<?= Patient::$__ALL_PATIENTS ?>">All Patients</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="in_or_out">Inpatient / Outpatient</label>
                                            <select name="in_or_out" id="in_or_out" class="form-control">
                                                <option selected value="<?= Constant::$default_select_empty_value ?>">(--Select--)</option>
                                                <option value="<?= Patient::$__INPATIENT ?>">In-Patient</option>
                                                <option value="<?= Patient::$__OUTPATIENT ?>">Out-Patient</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input class="btn btn-primary btn-block" type="submit" value="Filter"/>
                                </form>
                            </div>
                        </div>
                    <?php
                        $loginId = $login1->getLoginId();
                        $query = null;
                        if (isset($_REQUEST['my_or_all']))  {
                            switch ($_REQUEST['my_or_all']) {
                                case (Patient::$__MY_PATIENTS):
                                    $query = "SELECT DISTINCT p.patientId as patientId,  m.managerId as managerId, surname, otherNames, sexName, dob, visitCount, loginName FROM _medical_doctor_consultation_queue as q, _consultation_queue_manager as m, _patientVisit as v, _patientCase as c, _patient as p, _sex as s, _medicalDoctor as d, _login as l WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (p.sexId = s.sexId) AND (q.doctorId = d.doctorId) AND (q.queueManager = m.managerId) AND (d.loginId = l.loginId) AND (d.loginId = '$loginId') AND (c.closed = 0)";
                                    break;
                                case (Patient::$__ALL_PATIENTS):
                                    $query = "SELECT DISTINCT p.patientId as patientId,  m.managerId as managerId, surname, otherNames, sexName, dob, visitCount, loginName FROM _medical_doctor_consultation_queue as q,  _consultation_queue_manager as m, _patientVisit as v, _patientCase as c, _patient as p, _sex as s, _medicalDoctor as d, _login as l WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (p.sexId = s.sexId) AND (q.doctorId = d.doctorId) AND (q.queueManager = m.managerId) AND (d.loginId = l.loginId) AND (c.closed = 0)";
                                    break;
                            }
                        }
                        if (is_null($query))  $query = "SELECT DISTINCT p.patientId as patientId,  m.managerId as managerId, surname, otherNames, sexName, dob, visitCount, loginName FROM _medical_doctor_consultation_queue as q,  _consultation_queue_manager as m, _patientVisit as v, _patientCase as c, _patient as p, _sex as s, _medicalDoctor as d, _login as l WHERE (q.visitId = v.visitId) AND (q.caseId = c.caseId) AND (q.patientId = p.patientId) AND (p.sexId = s.sexId) AND (q.doctorId = d.doctorId) AND (q.queueManager = m.managerId) AND (d.loginId = l.loginId) AND (d.loginId = '$loginId') AND (c.closed = 0)";
                        $windowToDisplay1 = UITabularView::query($conn, $query, array(
                            array(
                                "idColumn" => "managerId",
                                "caption" => "Manage Patient",
                                "href" => $thispage . "?page=medicaldoctor_consult&mid=",
                                "appendId" => true
                            )
                        ), array(
                            "surname" => array("caption" => "Surname"),
                            "otherNames" => array("caption" => "Given Names"),
                            "sexName" => array("caption" => "Sex"),
                            "dob" => array("caption" => "D.O.B"),
                            "loginName" => array("caption" => "Doctor")
                        ), array('patientId', 'managerId'), 3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function ($conn, $colname, $colval) {
                            if ($colname == "dob") {
                                //$colval = ~DateAndTime~::~convertFromSystemDateAndTimeFormatToGUIDateFormat($colval);
                                $dt1 = new DateAndTime($colval);
                                $colval = $dt1->getGUIDateOnlyFormat();
                            }
                            return $colval;
                        });
                        echo $windowToDisplay1;
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <!-- <i><a href="<?= $nextPage ?>" class="card-link">Back to Invoice</a></i><br /> -->
                        <span class="text-muted"><i>Rule: medicaldoctor_consult</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
