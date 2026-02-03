<?php 
class AgeCategory {
    public static function getAgeCategory($conn, $age)  {
        $t1 = __data__::selectQuery($conn, self::getClassname(), array('categoryId', 'minimumAge', 'maximumAge'), null, false);
        $category1 = null;
        foreach ($t1['property'] as $row1)  {
            $minAge = $row1['minimumAge'];
            $maxAge = $row1['maximumAge'];
            if ($age >= $minAge && $age < $maxAge)  {
                $category1 = new AgeCategory("Delta", $row1['categoryId'], $conn);
                break;
            }
        }
        return $category1;
    }
}
?>