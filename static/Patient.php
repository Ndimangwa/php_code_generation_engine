<?php 
class Patient {
    public function getCurrentVisit()   {
        $caseId = $this->currentCase;
        if ($caseId == 0) return 0;
        $case1 = new PatientCase($this->database, $caseId, $this->conn);
        return ($case1->getCurrentVisit() == 0 ? 0 : ($case1->isClosed() ? 0 : $case1->getCurrentVisit()));
    }
    public function hasPendingBalance()   {
        return ! is_null($this->getTotalBalance());
    }
    public function getTotalBalance()   {
        $patientId  = $this->patientId;
        $query = "SELECT currencyId, SUM(balance) AS totalBalance FROM _patient_invoice WHERE patientId = '$patientId' GROUP BY currencyId";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($this->conn, $query, false);
        } catch (Exception $e)  {
           $records = null;
        }
        $totalBalanceArray1 = array();
        if (! is_null($records))    {
            foreach ($records['column'] as $record1)  {
                $currencyId = $record1['currencyId'];
                $totalBalance = $record1['totalBalance'];
                if ($totalBalance > 0) $totalBalanceArray1[$currencyId] = $totalBalance;
            }
        }
        if (sizeof($totalBalanceArray1) == 0) $totalBalanceArray1 = null;
        return $totalBalanceArray1;
    }
    public function getBalanceStatusScreen()    {
        $patient1 = $this;
        $window1 = "<div class=\"card\"><div class=\"card-header bg-primary\">Balance</div><div class=\"card-body\">No balance found</div><div class=\"card-footer\"></div></div>";
        try {
            /*$totalBalance = $this->getTotalBalance();
            $caption = "Balance : $totalBalance";
            if ($totalBalance == 0) {
                $window1 = "<div class=\"card\"><div class=\"card-header bg-primary\">Balance</div><div class=\"card-body\">$caption</div><div class=\"card-footer\"></div></div>";
            } else {
                $window1 = "<div class=\"card\"><div class=\"card-header bg-warning\">Balance</div><div class=\"card-body\">$caption</div><div class=\"card-footer\"></div></div>";
            }*/
            $totalBalanceArray1 = $this->getTotalBalance();
            if (is_null($totalBalanceArray1))   {
                $window1 = "<div class=\"card\"><div class=\"card-header bg-primary\">Balance</div><div class=\"card-body\">No Pending Balance</div><div class=\"card-footer\"></div></div>";
            } else {
                $window1 = "<div class=\"card\"><div class=\"card-header bg-warning\">Balance</div><div class=\"card-body\">";
                $window1 .= "<table class=\"table\"><thead><tr><th scope=\"col\"></th><th>Currency</th><th>Amount</th></tr></thead><tbody>";
                $count = 0;
                foreach ($totalBalanceArray1 as $currencyId => $totalBalance)   {
                    $currency1 = new Currency("Delta", $currencyId, $this->conn);
                    $currencyCode = $currency1->getCode();
                    $count++;
                    $window1 .= "<tr><th scope=\"row\">$count</th><td>$currencyCode</td><td>$totalBalance</td></tr>";
                }
                $window1 .= "</tbody></table>";
                $window1 .= "</div><div class=\"card-footer\"></div></div>";
            }
        } catch (Exception $e)  {}
        return $window1;
    }
    public function getCurrentVisitMedicalDoctor()  {
        $patient1 = $this;
        $medicalDoctor1 = null;
        $case1 = new PatientCase($this->database, $patient1->getCurrentCase(), $this->conn);
        if (! $case1->isClosed())   {
            $visit1 = $case1->getCurrentVisit();
            $medicalDoctor1 = $visit1->getMedicalDoctor();
        }
        return $medicalDoctor1;
    }
    public function getCaseStatusScreen()  {
        $patient1 = $this;
        $case1 = new PatientCase($this->database, $patient1->getCurrentCase(), $this->conn);
        $window1 = "<div class=\"card\"><div class=\"card-header bg-warning\">Patient Monitor</div><div class=\"card-bod\">Case is Closed</div><div class=\"card-footer\"></div></div>";
        if (! $case1->isClosed())   {
            $window1 = "<div class=\"card\"><div class=\"card-header bg-primary text-white\">Patient Monitor</div><div class=\"card-body\">";
            $window1 .= "<div>Case is OPEN</div>";
            if (! is_null($case1->getCurrentStage()))   {
                $caption = $case1->getCurrentStage()->getStageName();
                $window1 .= "<div>Current Stage : <b><i>$caption</i></b></div>";
            }
            if (! is_null($case1->getNextStage()))  {
                $caption = $case1->getNextStage()->getStageName();
                $window1 .= "<div>Next Stage    : <b><i>$caption</i></b></div>";
            }
            $visit1 = new PatientVisit($this->database, $case1->getCurrentVisit(), $this->conn);
            $medicalDoctor1 = $visit1->getMedicalDoctor();
            if (! is_null($medicalDoctor1)) {
                $fullName = $medicalDoctor1->getLogin()->getFullName();
                $fullName = "<u>$fullName</u>";
                $specialist = "Non - Specialist";
                if ($medicalDoctor1->isSpecialist()) $specialist = "Specialist";
                $fullName .= " ($specialist)";
                $window1 .= "<div>Assigned Consultant : <span>$fullName</span></div>";
            }
            $count = $visit1->getVisitCount();
            $window1 .= "</div><div class=\"card-footer text-muted\">Visit Count : <span>$count</span></div></div>";
        }
        return $window1;
    }
    public function getPatientName()    {
        $patientName = $this->surname;
        $patientName .= ", ".$this->otherNames;
        return $patientName;
    }
}
?>