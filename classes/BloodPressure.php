<?php 
class BloodPressure {
    private $systolic;
    private $dystolic;
    private $separator = "/";
    public function getSystolicValue()  { return $this->systolic; }
    public function getDystolicValue()  { return $this->dystolic; }
    public function getBloodPressure()  {
        return ( ( $this->systolic ) . ( $this->separator ) . ( $this->dystolic ) );
    }
    public function __construct($blood_pressure_in_mmHg, $separator = "/")    {
        //120 / 80
        $this->separator = $separator;
        $bpArray1 = explode($this->separator, $blood_pressure_in_mmHg);
        if (sizeof($bpArray1) != 2) throw new Exception("[ $blood_pressure_in_mmHg ] :Blood Pressure Format Error");
        $systolic = floatval(trim($bpArray1[0]));
        $dystolic = floatval(trim($bpArray1[1]));
        if (! ($dystolic < $systolic)) throw new Exception("Dystolic [ $dystolic ] should be less than Systolic [ $systolic ]");
        $this->systolic = $systolic;
        $this->dystolic = $dystolic;
    }
    public static function createBloodPressure($systolic_in_mmHg, $dystolic_in_mmHg, $separator = "/")  {
        return (new BloodPressure(implode($separator, array($systolic_in_mmHg, $dystolic_in_mmHg)), $separator));
    }
}
?>
