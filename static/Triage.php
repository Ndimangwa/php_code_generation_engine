<?php 
class Triage {
    public function getMyStatusTable()  {
        $line = "<div class=\"table-responsive\"><table class=\"table\"><tbody>";
        //Working with variables 
        $time = $this->timeOfCreation->getDateAndTimeString();
        $visitNo = $this->visit->getVisitCount();
        $attendedBy = $this->attendedBy->getLoginName();
        $weight = $this->weight;
        $height = $this->height;
        $bmi = round($this->bmi, 2);
        $bmiStatusText = self::decode_bmi_status($this->bmiStatus);
        $bmiColor = $this->bmiColor;
        $bp = $this->bloodPressure;
        $bpStatusText = self::decode_bp_status($this->bloodPressureStatus);
        $bpColor = $this->bloodPressureColor;
        $temperature = $this->temperature;
        $pulseRate = $this->pulseRate;
        $respirationRate = $this->respirationRate;
        $oxygenLevel = $this->oxygenLevel;
        
        $line .= "<tr><td><b>Time</b> : $time</td><td><b>Visit</b> : $visitNo <br/><b>Temperature</b> : $temperature (deg C)</td><td><b>Attended By</b> : $attendedBy</td></tr>";
        $line .= "<tr><td><b>Height</b> : $height (cm)</td><td><b>Weight</b> : $weight (kg)</td><td><b>BMI</b> : $bmi <br/><i>( <span class=\"border border-dark rounded-circle p-1 $bmiColor\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> $bmiStatusText)</i></td></tr>";
        $line .= "<tr><td><b>BP</b> : $bp (mmHg)<br/><i>( <span class=\"border border-dark rounded-circle p-1 $bpColor\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> $bpStatusText)</i></td><td><b>Pulse Rate</b> : $pulseRate (bpm)<br/><b>Respiration Rate</b> : $respirationRate (bpm)</td><td><b>Oxygen Level</b> : $oxygenLevel (%)</td></tr>";
        $line .= "</tbody></table></div>";
        return $line;
    }
    public static function getTriageForVisit($conn, $visitId)  {
        $query = "SELECT triageId FROM _triage WHERE visitId = '$visitId'";
        $triage1 = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
            $triage1 = new Triage("Data", $records['column'][0]['triageId'], $conn);
        } catch (Exception $e)  {
            $triage1 = null;
        }
        return $triage1;
    }
    public function getMyVitalSignsTable()  {
        $line = "<table class=\"table\"><thead><tr><th colspan=\"Vital Signs Summary\"></th></tr></thead><tbody>";
        $t1 = self::get_bmi_row($this->weight, $this->height / 100);
        if (! is_null($t1)) $line .= $t1;
        $t1 = self::get_bp_row($this->systolicBloodPressure, $this->dystolicBloodPressure);
        if (! is_null($t1)) $line .= $t1;
        $line .= "</tbody></table>";
        return $line;
    }
    public static function get_bp_status($systolic_in_mmhg, $dystolic_in_mmhg)  {
        $bp_status = 0;
        if ($systolic_in_mmhg > 180 || $dystolic_in_mmhg > 120) $bp_status = self::$__BP_HYPERTENSIVE_CRISIS;
        else if ($systolic_in_mmhg >= 140 || $dystolic_in_mmhg >= 90) $bp_status = self::$__BP_HYPERTENSION_STAGE_2;
        else if ($systolic_in_mmhg >= 130 || $dystolic_in_mmhg >= 80) $bp_status = self::$__BP_HYPERTENSION_STAGE_1;
        else if ($systolic_in_mmhg >= 120 && $dystolic_in_mmhg < 80) $bp_status = self::$__BP_ELEVATED;
        else if ($systolic_in_mmhg < 120 && $dystolic_in_mmhg < 80) $bp_status = self::$__BP_NORMAL;
        return $bp_status;
    }
    private static function get_bp_row($systolic_in_mmhg, $dystolic_in_mmhg, $pretext = null)   {
        $bp_status = null; $bgcolor = "bg-primary";
        $t_bp_status = self::get_bp_status($systolic_in_mmhg, $dystolic_in_mmhg);
        $bp_status = self::decode_bp_status($t_bp_status);
        $bgcolor = self::get_bp_color($t_bp_status);
        if (is_null($bp_status)) return null;
        if (is_null($pretext)) $pretext = "";
        $bp_status = $pretext.$bp_status;
        return "<tr><td><span class=\"border border-dark rounded-circle $bgcolor p-1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td><span data-toggle=\"tooltip\" title=\"Blood Pressure\">BP ($systolic_in_mmhg / $dystolic_in_mmhg)</span></td><td>$bp_status</td></tr>";
    }
    public static function calculate_bmi($weight_in_kg, $height_in_m)  {
        if ($height_in_m == 0) return 0;
        return $weight_in_kg / ($height_in_m * $height_in_m);
    }
    public static function get_bmi_status($calculated_bmi)  {
        $bmi_status = 0;
        if ($calculated_bmi < 18.5) $bmi_status = self::$__BMI_UNDERWEIGHT;
        else if ($calculated_bmi >= 18.5 && $calculated_bmi < 25) $bmi_status = self::$__BMI_HEALTHIER;
        else if ($calculated_bmi >= 25 && $calculated_bmi < 30) $bmi_status = self::$__BMI_OVERWEIGHT;
        else if ($calculated_bmi >= 30) $bmi_status = self::$__BMI_OBESITY;
        return $bmi_status;
    }
    public static function get_bmi_color($bmi_status) {
        $colorArray1 = array(
            (self::$__BMI_UNDERWEIGHT) => (self::$__BMI_UNDERWEIGHT_COLOR),
            (self::$__BMI_HEALTHIER) => (self::$__BMI_HEALTHIER_COLOR),
            (self::$__BMI_OVERWEIGHT) => (self::$__BMI_OVERWEIGHT_COLOR),
            (self::$__BMI_OBESITY) => (self::$__BMI_OBESITY_COLOR)
        );
        $color = null;
        if (isset($colorArray1[$bmi_status])) $color = $colorArray1[$bmi_status];
        return $color;
    }
    public static function decode_bmi_status($bmi_status) {
        $statusArray1 = array(
            (self::$__BMI_UNDERWEIGHT) => "UNDERWEIGHT",
            (self::$__BMI_HEALTHIER) => "HEALTHIER",
            (self::$__BMI_OVERWEIGHT) => "OVERWEIGHT",
            (self::$__BMI_OBESITY) => "OBESITY"
        );
        $status = null;
        if (isset($statusArray1[$bmi_status])) $status = $statusArray1[$bmi_status];
        return $status;
    }
    public static function get_bp_color($bp_status)   {
        $colorArray1 = array(
            (self::$__BP_NORMAL) => (self::$__BP_NORMAL_COLOR),
            (self::$__BP_ELEVATED) => (self::$__BP_ELEVATED_COLOR),
            (self::$__BP_HYPERTENSION_STAGE_1) => (self::$__BP_HYPERTENSION_STAGE_1_COLOR),
            (self::$__BP_HYPERTENSION_STAGE_2) => (self::$__BP_HYPERTENSION_STAGE_2_COLOR),
            (self::$__BP_HYPERTENSIVE_CRISIS) => (self::$__BP_HYPERTENSIVE_CRISIS_COLOR)
        );
        $color = null;
        if (isset($colorArray1[$bp_status])) $color = $colorArray1[$bp_status];
        return $color;
    }
    public static function decode_bp_status($bp_status)   {
        $statusArray1 = array(
            (self::$__BP_NORMAL) => "NORMAL",
            (self::$__BP_ELEVATED) => "ELEVATED",
            (self::$__BP_HYPERTENSION_STAGE_1) => "HYPERTENSION STAGE ONE",
            (self::$__BP_HYPERTENSION_STAGE_2) => "HYPERTENSION STAGE TWO",
            (self::$__BP_HYPERTENSIVE_CRISIS) => "HYPERTENSIVE CRISIS"
        );
        $status = null;
        if (isset($statusArray1[$bp_status])) $status = $statusArray1[$bp_status];
        return $status;
    }
    private static function get_bmi_row($weight_in_kg, $height_in_m, $pretext = null)    {
        $bmi_status = null; $bgcolor = "bg-primary";
        $bmi = self::calculate_bmi($weight_in_kg, $height_in_m);
        $t_bmi_status = self::get_bmi_status($bmi);
        $bmi_status = self::decode_bmi_status($t_bmi_status);
        $bgcolor = self::get_bmi_color($t_bmi_status);
        if (is_null($bmi_status)) return null;
        if (is_null($pretext)) $pretext = "";
        $bmi_status = $pretext.$bmi_status;
        $bmi = round($bmi, 2);
        return "<tr><td><span class=\"border border-dark rounded-circle $bgcolor p-1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td><span data-toggle=\"tooltip\" title=\"Body Mass Index\">BMI ($bmi)</span></td><td>$bmi_status</td></tr>";
    }   
    public static function getTriageForPatient($conn, $patient1)    {
        $caseId = $patient1->getCurrentCase();
        $query = "SELECT triageId FROM _triage WHERE (caseId = '$caseId')";
        $triage1 = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
            if (sizeof($records['column']) > 0) {
                $triage1 = new Triage("Delta", $records['column'][sizeof($records['column']) - 1]['triageId'], $conn);
            }
        } catch (Exception $e)  {
            $triage1 = null;
        }
        return $triage1;
    }
    public static function getTriageForConsultationQueue($conn, $consultationQueue1) {
        $visitId = $consultationQueue1->getVisit()->getVisitId();
        $caseId = $consultationQueue1->getPatientCase()->getCaseId();
        $bundleCode = $consultationQueue1->getBundleCode();
        //For this specific visit
        $query = "SELECT triageId FROM _triage WHERE ( visitId = '$visitId' ) AND ( bundleCode = '$bundleCode' )";
        $triage1 = null;
        try {
            $record = __data__::getSelectedRecords($conn, $query, true);
            $triage1 = new Triage("Delta", $record['column'][0]['triageId'], $conn);
        } catch (Exception $e)  {
            $triage1 = null;
        }
        if (is_null($triage1))  {
            //Perhaps the case has moved , need to take the lattest readings 
            //We might have multiple ones
            $query = "SELECT triageId FROM _triage WHERE ( caseId = '$caseId' ) AND ( bundleCode = '$bundleCode' )";
            try {
                $records = __data__::getSelectedRecords($conn, $query, false);
                $triage1 = new Triage("Delta", $records['column'][sizeof($records) - 1]['triageId'], $conn);
            } catch (Exception $e)  {
                $triage1 = null;
            }
        }
        return $triage1;
    }
}
?>