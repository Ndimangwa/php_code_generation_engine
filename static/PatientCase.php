<?php 
class PatientCase {
    public function invalidateNextStage($rollback = true)   {
        $caseId = $this->caseId;
        $conn = $this->conn;
        $query = "UPDATE _patientCase SET nextStage = NULL WHERE caseId = '$caseId'";
        try {
            $stmt = $conn->prepare($query);
            if ($rollback) $conn->beginTransaction();
            if (! $stmt->execute([])) throw new Exception("Could not execute query");
            if ($rollback) $conn->commit();
        } catch (Exception $e)  {
            if ($rollback) $conn->rollBack();
        }
    }
}
?>
