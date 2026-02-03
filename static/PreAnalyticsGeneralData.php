<?php
class PreAnalyticsGeneralData
{
    public static function getChartByTimeOfCreation($conn, $width, $height, $type_of_chart, $title_of_chart, $sub_title_options = null, $group_by_type = null, $group_by_group = null, $group_by_object_reference = null, $group_by_login_id = null, $group_by_flags = null, $shapingFunction = null)
    {
        $__NULL_VALUE = "___this_item_is_nulled__";
        /*
        $sub_title_options = array
        Order of preparing DataSet 
        by group
        by type
        by object_reference
        by login_id
        by flags
        */
        $config = array(
            'type' => $type_of_chart,
            'options' => array(
                'responsive' => true,
                'plugins' => array(
                    'legend' => array('position' => 'top'),
                    'title' => array('display' => true, 'text' => $title_of_chart)
                )
            )
        );
        //Now working 
        $t1 = $group_by_group;
        $groupArray1 = is_null($t1) ? array($__NULL_VALUE) : (is_array($t1) ? $t1 : array($t1));
        $t1 = $group_by_type;
        $typeArray1 = is_null($t1) ? array($__NULL_VALUE) : (is_array($t1) ? $t1 : array($t1));
        $t1 = $group_by_object_reference;
        $objectReferenceArray1 = is_null($t1) ? array($__NULL_VALUE) : (is_array($t1) ? $t1 : array($t1));
        $t1 = $group_by_login_id;
        $loginIdArray1 = is_null($t1) ? array($__NULL_VALUE) : (is_array($t1) ? $t1 : array($t1));
        $t1 = $group_by_flags;
        $flagsArray1 = is_null($t1) ? array($__NULL_VALUE) : (is_array($t1) ? $t1 : array($t1));
        //Now build 
        $dataArray1 = array();
        $listOfTimeOfCreation = array();
        foreach ($groupArray1 as $group) {
            if ($group == $__NULL_VALUE) $group = null;
            foreach ($typeArray1 as $type) {
                if ($type == $__NULL_VALUE) $type = null;
                foreach ($objectReferenceArray1 as $objectReference) {
                    if ($objectReference == $__NULL_VALUE) $objectReference = null;
                    foreach ($loginIdArray1 as $loginId) {
                        if ($loginId == $__NULL_VALUE) $loginId = null;
                        foreach ($flagsArray1 as $flags) {
                            if ($flags == $__NULL_VALUE) $flags = null;
                            $list = self::getListOfDataObjects($conn, $type, $group, $objectReference, $loginId, $flags);
                            if (!is_null($list)) {
                                $index = sizeof($dataArray1);
                                $dataArray1[$index] = array();
                                foreach ($list as $data1) {
                                    $timeOfCreation = $data1->getTimeOfCreation()->getTimestamp();
                                    $dataArray1[$index][sizeof($dataArray1[$index])] = array(
                                        "timeOfCreation" => $timeOfCreation,
                                        "value" => $data1->getValue()
                                    );
                                    $listOfTimeOfCreation[sizeof($listOfTimeOfCreation)] = $timeOfCreation;
                                }
                            }
                        }
                    }
                }
            }
        }
        //Unique
        $listOfTimeOfCreation = array_unique($listOfTimeOfCreation);
        /*Now we have $dataArray1[i][j]['timeOfCreation' => timestamp, 'value' => value]
                Note each $dataArray1[i] is a separate dataset 
                -- need to form labels and work from there
        */
        $valueAtTimeOfCreation = array();
        foreach ($dataArray1 as $datablock1) {
            $index = sizeof($valueAtTimeOfCreation);
            $valueAtTimeOfCreation[$index] = array();
            foreach ($datablock1 as $block1) {
                $timeOfCreation = $block1['timeOfCreation'];
                $valueAtTimeOfCreation[$index][$timeOfCreation] = $block1['value'];
            }
        }
        $labels = array();
        $datasets = array();
        $count = 0;
        foreach ($valueAtTimeOfCreation as $datablock1) {
            $index = sizeof($datasets);
            $datasets[$index] = array();
            $datasets[$index]['label'] = is_null($sub_title_options) ? $title_of_chart : (is_array($sub_title_options) ? ( isset($sub_title_options[$index]) ? $sub_title_options[$index] : $title_of_chart ) : $title_of_chart);
            $datasets[$index]['fill'] = true;
            $datasets[$index]['data'] = array();
            foreach ($listOfTimeOfCreation as $timeOfCreation) {
                //Label match only 1st -loop
                if ($count == 0) {
                    $time1 = new DateAndTime($timeOfCreation);
                    $labels[sizeof($labels)] = $time1->getDateAndTimeString();
                }
                //Working with data
                $datasets[$index]['data'][sizeof($datasets[$index]['data'])] = (isset($datablock1[$timeOfCreation])) ? $datablock1[$timeOfCreation] : 0;
            }
            $count++;
        }
        $data = array('labels' => $labels, 'datasets' => $datasets);
        return ( ChartEngine::getChart($width, $height, $config, $data, null) );
    }
    public static function getListOfDataObjects($conn, $type = null, $group = null, $objectReferenceString = null, $loginId = null, $flags = null)
    {
        return (PreAnalyticsGeneralData::getListOfDataObjectsForAClass($conn, (self::getClassname()), $type, $group, $objectReferenceString, $loginId, $flags));
    }
    public static function getListOfDataObjectsForAClass($conn, $classname, $type = null, $group = null, $objectReferenceString = null, $loginId = null, $flags = null)
    {
        $tablename = Registry::getTablename($classname);
        if (is_null($tablename)) return null;
        $query = "SELECT dataId FROM $tablename";
        $appendAnd = false;
        if (!is_null($type)) {
            $query .= " WHERE _type = $type";
            $appendAnd = true;
        }
        if (!is_null($group)) {
            $dt = "_group = $group";
            $query = $appendAnd ? ($query . " AND $dt") : ($query . " WHERE $dt");
            $appendAnd = true;
        }
        if (!is_null($objectReferenceString)) {
            $dt = "referenceString = '$objectReferenceString'";
            $query = $appendAnd ? ($query . " AND $dt") : ($query . " WHERE $dt");
            $appendAnd = true;
        }
        if (!is_null($loginId)) {
            $dt = "loginId = $loginId";
            $query = $appendAnd ? ($query . " AND $dt") : ($query . " WHERE $dt");
            $appendAnd = true;
        }
        if (!is_null($flags)) {
            $dt = "flags = $flags";
            $query = $appendAnd ? ($query . " AND $dt") : ($query . " WHERE $dt");
            $appendAnd = true;
        }
        //records
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e) {
            $records = null;
        }
        if (is_null($records)) return null;
        $list = array();
        foreach ($records['column'] as $column1) {
            $myobj1 = Registry::getObjectReference("Delta", $conn, $classname, $column1['dataId']);
            if (!is_null($myobj1)) {
                $list[sizeof($list)] = $myobj1;
            }
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
}
