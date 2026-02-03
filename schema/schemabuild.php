<?php 
class Schema {
    private $schemaArray1 = null;
    public function __construct($schemaArray1)  {
        $this->schemaArray1 = $schemaArray1;
    }
    public function getSchemaArray() {
        return $this->schemaArray1;
    }
    public function getListOfClasses()  {
        $list = array();
        foreach ($this->schemaArray1 as $classBlock1)   {
            if (isset($classBlock1['class'])) $list[sizeof($list)] = $classBlock1['class'];
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public function getListOfTables()   {
        $list = array();
        foreach ($this->schemaArray1 as $classBlock1)   {
            $list = array();
            if (isset($classBlock1['table'])) $list[sizeof($list)] = $classBlock1['table'];
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public function getClassListArrayFromAClassname($classname)  {
        $classArray1 = null;
        foreach ($this->schemaArray1 as $classBlock1)   {
            if (isset($classBlock1['class']) && $classBlock1['class'] == $classname)    {
                $classArray1 = $classBlock1;
                break;
            }
        }
        return $classArray1;
    }
    public function getTableListArrayFromATablename($tablename)  {
        $tableArray1 = null;
        foreach ($this->schemaArray1 as $classBlock1)   {
            if (isset($classBlock1['table']) && $classBlock1['table'] == $tablename)    {
                $tableArray1 = $classBlock1;
                break;
            }
        }
        return $tableArray1;
    }
    public function getTablenameFromAClassname($classname)  {
        $classArray1 = $this->getClassListArrayFromAClassname($classname);
        if (is_null($classArray1)) return null;
        if (! isset($classArray1['table'])) return null;
        return $classArray1['table'];
    }
    public function getClassnameFromATablename($tablename)  {
        $tableArray1 = $this->getTableListArrayFromATablename($tablename);
        if (is_null($tableArray1)) return null;
        if (! isset($tableArray1['class'])) return null;
        return $tableArray1['class'];
    }
    public function getColumnListArrayFromAClassname($classname)    {
        return self::getColumnListArrayFromAClassListArray($this->getClassListArrayFromAClassname($classname));
    }
    public function getColumnListArrayFromATablename($tablename)    {
        return self::getColumnListArrayFromAClassListArray($this->getTableListArrayFromATablename($tablename));
    }
    public function getColumnnameFromAPropertyname($classname, $propertyname)    {
        $columnArray1 = self::getColumnArrayFromClassAndProperty($this->schemaArray1, $classname, $propertyname);
        if (is_null($columnArray1)) return null;
        if (! isset($columnArray1['colname'])) return null;
        return $columnArray1['colname'];
    }
    public function getColumnnameWithAPrimaryRoleFromAPropertyname($classname, $propertyname)    {
        $columnArray1 = self::getColumnArrayFromClassAndProperty($this->schemaArray1, $classname, $propertyname);
        if (is_null($columnArray1)) return null;
        if (
            ! isset($columnArray1['colname']) ||
            ! isset($columnArray1['settings']) ||
            ! isset($columnArray1['settings']['data'])  ||
            ! isset($columnArray1['settings']['data']['role'])  ||
            ! $columnArray1['settings']['data']['role'] == "primary"
        ) return null;
        return $columnArray1['colname'];
    }
    public static function getColumnListArrayFromAClassListArray($classArray1)  {
        if (is_null($classArray1)) return null;
        $list = array();
        if (! isset($classArray1['columns'])) return null;
        foreach ($classArray1['columns'] as $columnBlock1)  {
            $list[sizeof($list)] = $columnBlock1;
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    //@Helper Function
    private static function getListOfColumnsFromColumnListArrayByRole($columnList1, $role)  {
        if (is_null($columnList1)) return null;
        $list = array();
        foreach ($columnList1 as $columnArray1) {
            if (! $columnArray1['colname'] || ! isset($columnArray1['settings']) || ! isset($columnArray1['settings']['data']) || ! isset($columnArray1['settings']['data']['role'])) continue;
            if ($columnArray1['settings']['data']['role'] == $role) $list[sizeof($list)] = $columnArray1['colname'];
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public static function getListOfPrimaryColumnsFromColumnListArray($columnList1) {
        return self::getListOfColumnsFromColumnListArrayByRole($columnList1, "primary");
    }
    public static function getCommaSeparatedListOfPrimaryColumnsFromColumnListArray($columnList1) {
        $listArray1 = self::getListOfPrimaryColumnsFromColumnListArray($columnList1);
        if (is_null($listArray1)) return null;
        $list = null;
        foreach ($listArray1 as $acol) {
            if (is_null($list)) $list = $acol;
            else $list .= ",".$acol;
        }
        return $list;
    }
    public static function getListOfUniqueColumnsFromColumnListArray($columnList1)  {
        return self::getListOfColumnsFromColumnListArrayByRole($columnList1, "unique");
    }
    public static function getColumnArrayFromClassAndProperty($schemaArray1, $classname, $propertyname)    {
        $columnArray1 = null;
        foreach ($schemaArray1 as $classBlock1) {
            if (isset($classBlock1['class']) && $classBlock1['class'] == $classname && isset($classBlock1['columns']))  {
                foreach ($classBlock1['columns'] as $column1)   {
                    if (isset($column1['property']) && isset($column1['property']['pname']))    {
                        if ($column1['property']['pname'] == $propertyname) {
                            $columnArray1 = $column1;
                            break;
                        }
                    }
                }
                break;
            }
        }
        return $columnArray1;
    }
}
?>