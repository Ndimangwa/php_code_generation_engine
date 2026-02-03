<?php 
class SimpleQueryBuilder    {
    //give a JSON equivalent of simple-queries 
    //no need to complicate your-self 
    public static function buildDelete($table, $where)  {
        if (is_null($where) || ! is_array($where)) throw new Exception("SimpleQueryBuilder::buildDelete >> Criteria not set");
        $queryArray1['query'] = "delete";
        $queryArray1['table'] = $table;
        $queryArray1['where'] = array();
        foreach ($where as $column => $value)   {
            $queryArray1['where'][$column] = $value;
        }
        return json_encode($queryArray1);
    }
    public static function buildUpdate($table, $columns, $where = null) {
        if (! is_array($columns)) throw new Exception("SimpleQueryBuilder::buildUpdate >> Could not decode column list");
        if (is_null($where)) throw new Exception("SimpleQueryBuilder::buildUpdate() >> The system does not allow bulk update");
        if (! ( is_null($where) || is_array($where) )) throw new Exception("SimpleQueryBuilder::buildUpdate >> Could not decode where list");
        $queryArray1 = array();
        $queryArray1['query'] = "update";
        $queryArray1['table'] = $table;
        $queryArray1['cols'] = array();
        foreach ($columns as $col => $val)  {
            $index = sizeof($queryArray1['cols']);
            $queryArray1['cols'][$index] = array();
            $queryArray1['cols'][$index][$col] = $val;
        }
        if (! is_null($where))  {
            $queryArray1['where'] = array();
            foreach ($where as $column => $value)   {
                $queryArray1['where'][$column] = $value;
            }
        }
        return json_encode($queryArray1);
    }
    public static function buildInsert($table /* tablename */, $columns /* array('col1' => 'val1', 'col2' => 'val2') */)    {
        if (! is_array($columns)) throw new Exception("SimpleQueryBuilder::buildInsert >> Could not decode column list");
        if (! is_array($columns)) throw new Exception("SimpleQueryBuilder::buildInsert >> Could not decode column list");
        $queryArray1 = array();
        $queryArray1['query'] = "insert";
        $queryArray1['table'] = $table;
        $queryArray1['cols'] = array();
        foreach ($columns as $col => $val)  {
            $index = sizeof($queryArray1['cols']);
            $queryArray1['cols'][$index] = array();
            $queryArray1['cols'][$index][$col] = $val;
        }
        return json_encode($queryArray1);
    }
    public static function buildSelect($tables /* ['table1', 'table2'] */, $columns /* ['col1', 'col2'] */, $where = null /* array('col1'=>'val1', 'col2' => 'val2') */)    {
        if (! is_array($tables)) throw new Exception("SimpleQueryBuilder::buildSelect >> Could not decode table list");
        if (! is_array($columns)) throw new Exception("SimpleQueryBuilder::buildSelect >> Could not decode column list");
        if (! ( is_null($where) || is_array($where) )) throw new Exception("SimpleQueryBuilder::buildSelect >> Could not decode where list");
        $queryArray1 = array();
        $queryArray1['query'] = "select";
        $queryArray1['tables'] = array();
        foreach ($tables as $table) {
            $index = sizeof($queryArray1['tables']);
            $queryArray1['tables'][$index] = array();
            $queryArray1['tables'][$index]['table'] = $table;
        }
        $queryArray1['cols'] = array();
        foreach ($columns as $column)   {
            $index = sizeof($queryArray1['cols']);
            $queryArray1['cols'][$index] = array();
            $queryArray1['cols'][$index]['col'] = $column;
        }
        if (! is_null($where))  {
            $queryArray1['where'] = array();
            foreach ($where as $column => $value)   {
                $queryArray1['where'][$column] = $value;
            }
        }
        return json_encode($queryArray1);
    }
}
?>