<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    MEDICAL DOCTOR
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $query = "SELECT doctorId, loginName, fullName, sexName, specialist, email, phone FROM _medicalDoctor as u, _login as l, _sex as s WHERE (u.loginId = l.loginId) AND (l.sexId = s.sexId)";
                        echo UITabularView::query($conn, $query, array(
                                array(
                                    "idColumn" => "doctorId",
                                    "nameColumn" => "fullName",
                                    "link-classes" => "cmd cmd-details",
                                    "link-icons" => "fas fa-eye",
                                    "title" => "View Details for '##REPLACE##'",
                                    "href" => $thispage."?page=medicaldoctor_read&id=",
                                    "appendId" => true
                                ), array(
                                    "link-classes" => "cmd cmd-update",
                                    "link-icons" => "fas fa-pencil-alt",
                                    "title" => "Update '##REPLACE##'",
                                    "href" => $thispage."?page=medicaldoctor_update&id=",
                                    "appendId" => true
                                ), array(
                                    "link-classes" => "cmd cmd-delete",
                                    "link-icons" => "fas fa-trash",
                                    "title" => "Deleting '##REPLACE##'",
                                    "href" => $thispage."?page=medicaldoctor_delete&id=",
                                    "appendId" => true
                                )
                            ), array(
                                "sexName" => array("caption" => "Sex"),
                                "email" => array("caption" => "Email"),
                                "phone" => array("caption" => "Phone"),
                                "specialist" => array("values" => array(0 => "No", 1 => "Yes"))
                        ), array('doctorId'),3, $profile1->getMaximumNumberOfDisplayedRowsPerPage(), $profile1->getMaximumNumberOfReturnedSearchRecords(), function($conn, $colname, $colval)    {
                            if ($colname == "dob")  {
                                //$colval = ~DateAndTime~::~convertFromSystemDateAndTimeFormatToGUIDateFormat($colval);
                                $dt1 = new DateAndTime($colval);
                                $colval = $dt1->getGUIDateOnlyFormat();
                            }
                            return $colval;
                        });
                        //Add the add button
?>
                        <div class="text-center text-md-right mt-2">
                            <a href="<?= $thispage ?>?page=medicaldoctor_create" class="btn btn-primary add-record">Add A New Medical Doctor</a>
                        </div>
<?php
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <span class="text-muted"><i>Rule: medicaldoctor_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>