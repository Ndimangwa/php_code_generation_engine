<?php 
class Person {
    public function calculateAge($profile1)  {
        //You have to make sure you have a dob props 
        $currentDate1 = DateAndTime::getCurrentDateAndTime($profile1);
        return (DateAndTime::calculateTimeDifferenceByYears($this->dob, $currentDate1));
    }
}
?>