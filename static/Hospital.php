<?php
class Hospital
{
    public static function generateRegistrationNumber($conn, $mode = 1, $rollback = true)
    {
        //Once Issue the pointer has to advance
        $hospital1 = new Hospital("Ndimangwa", Hospital::$__INIT_ID, $conn);
        $regNumber = null;
        if ($mode == (PatientRegistrationType::$__PATIENT_FULL_REGISTRATION)) {
            $n = $hospital1->getNextRegistrationNumber();
            $regNumber = "";
            $width = $hospital1->getRegistrationNumberWidth();
            $separator = $hospital1->getRegistrationNumberBlockSeparatorCharacter();
            $blockWidth = $hospital1->getRegistrationNumberBlockWidth();
            for ($i = 0; $i < $width; $i++) {
                $val = 0;
                if ($n != 0)    {
                    $val = $n % 10;
                    $n = floor($n / 10);
                }
                if ($i != 0 && ($i % $blockWidth == 0))   {
                    $regNumber = $separator.$regNumber;
                }
                $regNumber = "".$val.$regNumber;
            }
            $hospital1->setNextRegistrationNumber($hospital1->getNextRegistrationNumber() + 1);
        } else {
            $regNumber = $hospital1->getTemporaryRegistrationNumberPrefix();
            $tnumber = __object__::fixLength($hospital1->getTemporaryNextRegistrationNumber(), $hospital1->getTemporaryRegistrationNumberWidth(), "0");
            $regNumber .= $tnumber;
            $hospital1->setTemporaryNextRegistrationNumber($hospital1->getTemporaryNextRegistrationNumber() + 1);
        }
        $hospital1->update($rollback);
        return $regNumber;
    }
    public static function generateInvoiceNumber($conn, $rollback = true)
    {
        //Once Issue the pointer has to advance
        $hospital1 = new Hospital("Ndimangwa", Hospital::$__INIT_ID, $conn);
        $regNumber = $hospital1->getInvoiceNumberPrefix();
        $tnumber = __object__::fixLength($hospital1->getNextInvoiceNumber(), $hospital1->getInvoiceNumberWidth(), "0");
        $regNumber .= $tnumber;
        $hospital1->setNextInvoiceNumber($hospital1->getNextInvoiceNumber() + 1);
        $hospital1->update($rollback);
        return $regNumber;
    }
    public static function generateReceiptNumber($conn, $rollback = true)
    {
        //Once Issue the pointer has to advance
        $hospital1 = new Hospital("Ndimangwa", Hospital::$__INIT_ID, $conn);
        $regNumber = $hospital1->getReceiptNumberPrefix();
        $tnumber = __object__::fixLength($hospital1->getNextReceiptNumber(), $hospital1->getReceiptNumberWidth(), "0");
        $regNumber .= $tnumber;
        $hospital1->setNextReceiptNumber($hospital1->getNextReceiptNumber() + 1);
        $hospital1->update($rollback);
        return $regNumber;
    }
}
