<!-- Main Sidebar Container -->
<?php
$pageprefix = $thispage; //We will update /system/
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="../assests/images/mbwambo.png" alt="<?= $profile1->getProfileName() ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $profile1->getProfileName() ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!--<div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../cdns/AdminLTE-3.1.0/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div> -->

        <!-- SidebarSearch Form -->
        <!--<div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= $pageprefix ?>" class="nav-link active">
                        <i class="nav-icon fas fa-home"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                <?php
                if ($login1->isRoot()) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>User Accounts
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= $pageprefix ?>?page=group" class="nav-link">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>User Groups</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $pageprefix ?>?page=jobtitle" class="nav-link">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>Job Title</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $pageprefix ?>?page=systemuser" class="nav-link">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>System User</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $pageprefix ?>?page=medicaldoctor" class="nav-link">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>Medical Doctor</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "patient", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="<?= $pageprefix ?>?page=patient" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Patients</p>
                        </a>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_finance", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Finance
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "patientinvoice", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Create invoice from the list of system initiated list" href="<?= $pageprefix ?>?page=patientinvoice" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Default Invoice</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "patientinvoice_custom", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Initiate your own invoice" href="<?= $pageprefix ?>?page=patientinvoice_custom" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Custom Invoice</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "patientreceipt", "donotsetlog", "normal", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=patientreceipt" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Payments & Receipts</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "servicecategory", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=servicecategory" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Service Categories</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "service", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=service" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Services</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_nursestation", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Nurse Station
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "triage", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Record Vital Signs" href="<?= $thispage ?>?page=triage" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Triage</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "nursestation", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $thispage ?>?page=nursestation" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        Nurse Station
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "medicaldoctor_consult", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a data-toggle="tooltip" title="Medical Doctor Consultation" href="<?= $thispage ?>?page=medicaldoctor_consult" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Doctor Consultation</p>
                        </a>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "examination", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Laboratory Examination
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "examination_wetlab", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=examination_wetlab&qtype=<?= PatientExaminationQueue::$__WET_LAB ?>" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p data-toggle="tooltip" title="Wet/Chemical Laboratory">Chemistry Lab</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "examination_xray_plain", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=examination_xray_plain&qtype=<?= PatientExaminationQueue::$__PLAIN_XRAY ?>" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p data-toggle="tooltip" title="Plain X-RAY">Plain X-RAY</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "examination_ultrasound", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=examination_ultrasound&qtype=<?= PatientExaminationQueue::$__ULTRASOUND ?>" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p data-toggle="tooltip" title="Ultra Sound Examination">Ultra Sound</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_pharmaceutical", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Pharmacy
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "pharmaceuticaldrug", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Pharmaceutical Drug" href="<?= $pageprefix ?>?page=pharmaceuticaldrug" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Pharmaceutical</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "dispense_drug", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Dispense Drug" href="<?= $pageprefix ?>?page=dispense_drug" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Issue Drug</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_admission", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Admission <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "ward", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=ward" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Wards</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "room", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=room" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Rooms</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "bed", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=bed" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Beds</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "admission_create", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=admission_create" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Create Admission</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "admission_read", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=admission_read" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>View Admission</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_theatre", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>Theatre Management<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "theatre", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=theatre" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Theatre</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if (Authorize::isAllowable($config1, "theatre_read_list", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a data-toggle="tooltip" title="Working with Operation Queue" href="<?= $pageprefix ?>?page=theatre_read_list" class="nav-link">
                                        <i class="nav-icon fas fa-circle"></i>
                                        <p>Operation Queue</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                if (Authorize::isAllowable($config1, "menu_mysystem", "normal", "donotsetlog", null, null)) {
                ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon"></i>
                            <p>My System
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            if (Authorize::isAllowable($config1, "update_my_login", "normal", "donotsetlog", null, null)) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=update_my_login" class="nav-link">
                                        <i class="nav-icon fas fa-user-circle"></i>
                                        <p>My Profile</p>
                                    </a>
                                </li>
                            <?php
                            }
                            if ($login1->isRoot()) {
                            ?>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=profile_update" class="nav-link">
                                        <i class="nav-icon fas fa-cog"></i>
                                        <p>System Settings</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=notificationmanager" class="nav-link">
                                        <i class="nav-icon fas fa-cog"></i>
                                        <p>Notification Manager</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=hospital_update" class="nav-link">
                                        <i class="nav-icon fas fa-cog"></i>
                                        <p>Hospital Settings</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=lastresortdonotcare" class="nav-link">
                                        <i class="nav-icon fas fa-lock"></i>
                                        <p>Last Resort</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=systemlogs" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>System Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=uploadfiles" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Upload Files</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $pageprefix ?>?page=downloadfiles" class="nav-link">
                                        <i class="nav-icon"></i>
                                        <p>Download Files</p>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
                ?>
                <li class="nav-item">
                    <a id="logoutButton" href="#" class="nav-link">
                        <i class="nav-icon"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<!--End Main Sidebar Container-->
<script>
    (function($) {
        function updateUI($button1) {
            var $navTree1 = $button1.closest('ul.nav-sidebar');
            $navTree1.find('a.nav-link.active').removeClass('active');
            $button1.addClass('active');
            var $treeView1 = $button1.closest('ul.nav-treeview');
            //console.log('updateUI ==> Global');
            if ($treeView1.length) {
                var $link1 = $treeView1.closest('li.nav-item');
                //console.log("updateUI ==> treeView1.length");
                if ($link1.length) {
                    $link1 = $link1.find('a.nav-link').first();
                    //console.log("updateUI ==> link1.length");
                    if ($link1.length) {
                        //console.log("updateUI(2) ==> link1.length");
                        $link1.addClass('active');
                    }
                }
            }
        }
        $(function() {
            var cookieVariable = "__cookie_save_sidebar_id__";
            //Assigning Id 
            var index = 0;
            $('a.nav-link').each(function(i, ele) {
                var $button1 = $(ele);
                var id0 = "__button__" + index;
                $button1.attr('id', id0);
                index++;
            });
            //Now pulling session 
            var id = $.cookie(cookieVariable);
            //window.alert('Cookie is ' + id);
            if (id != null) {
                var $button1 = $('#' + id);
                if ($button1.length) {
                    updateUI($button1);
                }
            }
            $('a.nav-link').on('click', function(e) {
                //e.preventDefault();
                var $button1 = $(this);
                //Saving this id 
                $.cookie(cookieVariable, $button1.attr('id'));
                //updateUI($button1);
            });
        });
    })(jQuery);
</script>