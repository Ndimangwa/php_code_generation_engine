<?php 
var_dump($_POST);
//Working Section by Section
//Section 1: Patient History
include("submission/patient_history.php");
//Section 2: General Examination
include("submission/general_examination.php");
//Section 3: Vital Signs
include("submission/vital_signs.php");
//Section 4: Local Examination
include("submission/local_examination.php");
//Section 5: Systemic Examination
include("submission/systemic_examination.php");
//Section 6: Provision Diagnosis
include("submission/provision_diagnosis.php");
//Section 7: Laboratory Examination
include("submission/laboratory_examination.php");
//Section 8: Working Diagnosis
include("submission/working_diagnosis.php");
//Section 9: Drugs Selection
include("submission/drugs_management.php");
//Section 10: Operation Management
include("submission/admission_management.php");
//Section 11: Comments
include("submission/comments_management.php");
//Section 12: Case Management
?>