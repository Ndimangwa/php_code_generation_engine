<?php 
/*
Example of input in select query
{
    "tables": [
        {"table": "table1"},
        {"table": "table2"}
    ], 
    "query": "select",
    "cols": [
        {"col": "col1"},
        {"col", "col2"}
    ], 
    "where":    {
        "table1.col1": "table2.col1",
        "col3": "'hello'",
        "__or__": [
            {}, {}, {}
        ],
        "__and__": [
            {}, {}, {}
        ],
        "__not__": {}
    } 
}

OUTPUT
queryArray
  ['old-query'] = "SELECT * FROM table0001 WHERE col1='val1' AND col2 < 'val2'"
  ['pdo-query'] = "SELECT * FROM table0001 WHERE col1= ? AND col2 < ?"
  ['query-values'][0] = 'val1'
                  [1] = 'val2'
*/
class JSON2SQL {
    public static $columnInstruction = "_?:";
    public static $__OP_OR = "__or__";
    public static $__OP_AND = "__and__";
    public static $__OP_NOT = "__not__";
    public static $__OP_EQ = "__eq__";
    public static $__OP_LT = "__lt__";
    public static $__OP_GT = "__gt__";
    public static $__OP_LTE = "__lte__";
    public static $__OP_GTE = "__gte__";
    public static $__OP_NEQ = "__neq__";
    public static $__OP_IN = "__in__";
    public static $__OP_BETWEEN = "__between__";
    public static $__OP_ISNULL = "__isnull__";
    public static $__OP_LIKE = "__like__";
    //Where clause 
    private static function notCondition($bArr1, $queryArray1 = null) {
        //$query for consistency 
        $lineQueryArray1 = self::condition($bArr1, null);
        if (is_null($lineQueryArray1)) return null;
        //$lineQuery = " NOT ".$lineQuery;
        $lineQueryArray1['old-query'] = "NOT ".$lineQueryArray1['old-query'];
        $lineQueryArray1['pdo-query'] = "NOT ".$lineQueryArray1['pdo-query'];
        return $lineQueryArray1;
    }
    private static function isNullOperator($colname, $queryArray1 = null)    {
        // $isnull: colname ==> colname is NULL
        $queryArray1 = array();
        $queryArray1['old-query'] = "$colname IS NULL";
        $queryArray1['pdo-query'] = "$colname IS NULL";
        $queryArray1['query-values'] = array();
        return $queryArray1;
    }
    private static function binaryOperators($bArr1, $op, $queryArray1 = null)   {
        //$query for consistency 
        $lineQueryArray1 = null; 
        foreach ($bArr1 as $condition1) {
            $dt = self::condition($condition1, null);
            if (is_null($dt)) continue;
            if (is_null($lineQueryArray1)) $lineQueryArray1 = $dt;
            else {
                //$lineQuery .= " OR ".$dt;
                $query = " $op";
                $lineQueryArray1['old-query'] .= $query;
                $lineQueryArray1['pdo-query'] .= $query;
                $lineQueryArray1 = self::joinQueryArrays($lineQueryArray1, $dt);
            }
        }
        if (is_null($lineQueryArray1)) return null;
        if (sizeof($bArr1) == 1) return $lineQueryArray1;
        $lineQueryArray1['old-query'] = "(".$lineQueryArray1['old-query'].")";
        $lineQueryArray1['pdo-query'] = "(".$lineQueryArray1['pdo-query'].")";
        return $lineQueryArray1;
    }
    private static function andCondition($bArr1, $queryArray1 = null)   {
        return self::binaryOperators($bArr1, "AND", $queryArray1);
    }
    private static function orCondition($bArr1, $queryArray1 = null)  {
        return self::binaryOperators($bArr1, "OR", $queryArray1);
    }
    private static function colValOperators($opArr1, $op, $queryArray1 = null)    {
        if (! is_array($opArr1) && sizeof($opArr1) != 1) return null;
        $instrLen = strlen(self::$columnInstruction);
        $enableInstr = false; 
        $colname = key($opArr1);
        $colval = $opArr1[$colname];
        if (substr($colval, 0, $instrLen) == self::$columnInstruction)  {
            $enableInstr = true;
            $colval = substr($colval, $instrLen);
        }
        $queryArray1 = array();
        $queryArray1['old-query'] = "$colname $op $colval";
        $queryArray1['pdo-query'] = "$colname $op ?";
        if ($enableInstr) $queryArray1['pdo-query'] = "$colname $op $colval";
        $queryArray1['query-values'] = array();
        $queryArray1['query-values'][0] = $colval;
        return $queryArray1;
    }
    private static function eqOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, "=", $queryArray1);
    }
    private static function ltOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, "<", $queryArray1);
    }
    private static function gtOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, ">", $queryArray1);
    }
    private static function lteOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, "<=", $queryArray1);
    }
    private static function gteOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, ">=", $queryArray1);
    }
    private static function neqOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, "<>", $queryArray1);
    }
    private static function likeOperator($opArr1, $queryArray1 = null)    {
        return self::colValOperators($opArr1, "LIKE", $queryArray1);
    }
    private static function inOperator($opArr1, $queryArray1 = null)  {
        if (! is_array($opArr1) && sizeof($opArr1) != 1) return null;
        $columnname = key($opArr1);
        if (! is_array($opArr1[$columnname])) return null;
        $colvalArray1 = array();
        $placeholderList = "";
        $colkey = null; //Should be consistency
        $query = null;
        $instrLen = strlen(self::$columnInstruction);
        foreach ($opArr1[$columnname] as $coldata)  {
            $enableInstr = false;
            if (! is_array($coldata) || sizeof($coldata) != 1) continue;
            if (is_null($colkey)) $colkey = key($coldata);
            if (! isset($coldata[$colkey])) continue;
            $tval = $coldata[$colkey];
            if (substr($tval, 0, $instrLen) == self::$columnInstruction)  {
                $enableInstr = true;
                $tval = substr($tval, $instrLen);
            }
            if (is_null($query)) {
                $query = $tval;
                if ($enableInstr) $placeholderList = $tval;
                else $placeholderList = "?";
            } else {
                $query .= ", ".$tval;
                if ($enableInstr) $placeholderList .= ", $tval";
                else $placeholderList .= ", ?";
            }
            if (! $enableInstr) $colvalArray1[sizeof($colvalArray1)] = $coldata[$colkey];
        }
        if (! is_null($query)) {
            //$query = "$columnname IN ($query)";
            $tquery = "$columnname IN";
            $queryArray1 = array();
            $queryArray1['old-query'] = $tquery." ($query)";
            $queryArray1['pdo-query'] = $tquery." ($placeholderList)";
            $queryArray1['query-values'] = $colvalArray1;
        }
        return $queryArray1;
    }
    private static function betweenOperator($opArr1, $queryArray1 = null) {
        if (! is_array($opArr1) && sizeof($opArr1) != 1) return null;
        $instrLen = strlen(self::$columnInstruction);
        $enableInstr = false; 
        $instrPlaceholder = "";
        $columnname = key($opArr1);
        if (! is_array($opArr1[$columnname]) && sizeof($opArr1[$columnname]) != 2) return null;
        $colvalArray1 = array();
        $row1 = $opArr1[$columnname][0];
        if (! is_array($row1) && sizeof($row1) != 1) return null;
        $colkey = key($row1);
        $query = $row1[$colkey];
        if (substr($query, 0, $instrLen) == self::$columnInstruction)  {
            $enableInstr = true;
            $query = substr($query, $instrLen);
        }
        if ($enableInstr) {
            $instrPlaceholder = $query;
        } else { 
            $colvalArray1[sizeof($colvalArray1)] = $query;
            $instrPlaceholder = "?";
        }
        $enableInstr = false;
        $row1 = $opArr1[$columnname][1];
        if (! is_array($row1) && sizeof($row1) != 1 && ! isset($row1[$colkey])) return null;
        $tval = $row1[$colkey];
        if (substr($tval, 0, $instrLen) == self::$columnInstruction)  {
            $enableInstr = true;
            $tval = substr($tval, $instrLen);
        }
        if ($enableInstr)   {
            $instrPlaceholder .= " AND $tval";
        } else {
            $colvalArray1[sizeof($colvalArray1)] = $tval;
            $instrPlaceholder .= " AND ?";
        }
        $query = "$query AND ".$tval;
        $queryArray1 = array();
        $tquery = "$columnname BETWEEN";
        $queryArray1['old-query'] = "$tquery ($query)";
        $queryArray1['pdo-query'] = "$tquery ($instrPlaceholder)";
        $queryArray1['query-values'] = $colvalArray1;
        return $queryArray1;
    }
    private static function condition($bArr1, $queryArray1 = null) {
        foreach ($bArr1 as $key => $bdata)  {
            $lineQueryArray1 = null;
            switch ($key)   {
                case (self::$__OP_OR): 
                    if (is_array($bdata)) $lineQueryArray1 = self::orCondition($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_AND):
                    if (is_array($bdata)) $lineQueryArray1 = self::andCondition($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_NOT):
                    if (is_array($bdata)) $lineQueryArray1 = self::notCondition($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_EQ):
                    if (is_array($bdata)) $lineQueryArray1 = self::eqOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_LT):
                    if (is_array($bdata)) $lineQueryArray1 = self::ltOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_GT):
                    if (is_array($bdata)) $lineQueryArray1 = self::gtOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_LTE):
                    if (is_array($bdata)) $lineQueryArray1 = self::lteOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_GTE):
                    if (is_array($bdata)) $lineQueryArray1 = self::gteOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_NEQ):
                    if (is_array($bdata)) $lineQueryArray1 = self::neqOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_IN):
                    if (is_array($bdata)) $lineQueryArray1 = self::inOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_BETWEEN):
                    if (is_array($bdata)) $lineQueryArray1 = self::betweenOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_ISNULL):
                    if (! is_array($bdata)) $lineQueryArray1 = self::isNullOperator($bdata, $lineQueryArray1);
                    break;
                case (self::$__OP_LIKE):
                    if (is_array($bdata)) $lineQueryArray1 = self::likeOperator($bdata, $lineQueryArray1);
                    break;
                default: 
                    if (! is_array($bdata)) {
                        //$lineQuery = "$key = $bdata";
                        $instrLen = strlen(self::$columnInstruction);
                        $enableInstr = false;
                        $lineQueryArray1 = array();
                        if (substr($bdata, 0, $instrLen) == self::$columnInstruction)  {
                            $enableInstr = true;
                            $bdata = substr($bdata, $instrLen);
                        }
                        $lineQueryArray1['old-query'] = "$key = $bdata";
                        $lineQueryArray1['pdo-query'] = "$key = ?";
                        $lineQueryArray1['query-values'] = array();
                        if ($enableInstr) $lineQueryArray1['pdo-query'] = "$key = $bdata";
                        else $lineQueryArray1['query-values'][0] = $bdata;
                    }
            }
            if (! is_null($lineQueryArray1))  {
                if (is_null($queryArray1)) {
                    $queryArray1 = $lineQueryArray1;
                } else {
                    //$queryArray1 .= " AND ".$lineQuery;
                    $query = " AND";
                    $queryArray1['old-query'] .= $query;
                    $queryArray1['pdo-query'] .= $query;
                    $queryArray1 = self::joinQueryArrays($queryArray1, $lineQueryArray1);
                }
            }
        }
        if (is_null($queryArray1)) return null;
        if (sizeof($bArr1) == 1) return $queryArray1; //Get rid of redundant brackets
        $queryArray1['old-query'] = "(".$queryArray1['old-query'].")";
        $queryArray1['pdo-query'] = "(".$queryArray1['pdo-query'].")";
        return $queryArray1;
    }
    //Where -- main
    private static function where($whereArr)   {
        if (is_null($whereArr)) return null;
        return self::condition($whereArr);
    }
    //Columns to select [{"col": "val"}, {"col": "val2"}]
    private static function __gen_select_in_array__($colsArr, $_key = "col")  {
        if (is_null($colsArr) || ! is_array($colsArr)) return null;
        $query = null;
        foreach ($colsArr as $colArr)   {
            if (isset($colArr[$_key]))  {
                if (is_null($query)) $query = $colArr[$_key];
                else $query .= ", ".$colArr[$_key];
            }
        }
        return $query;
    }
    private static function cols($colsArr)  {
        return self::__gen_select_in_array__($colsArr, "col");
    }
    //Tables to select
    private static function tables($tablesArr)  {
        return self::__gen_select_in_array__($tablesArr, "table");
    }
    //Now generate
    private static function joinQueryArrays($queryArray1, $queryArray2) {
        if (is_null($queryArray1)) return $queryArray2;
        if (is_null($queryArray2)) return $queryArray1;
        //Append $queryArray2 to $queryArray1
        foreach (array('old-query', 'pdo-query', 'query-values') as $index) 
            if (! isset($queryArray1[$index]) || ! isset($queryArray2[$index])) return $queryArray1;
        $queryArray1['old-query'] .= " ".$queryArray2['old-query'];
        $queryArray1['pdo-query'] .= " ".$queryArray2['pdo-query'];
        if (! is_array($queryArray1['query-values']) || ! is_array($queryArray2['query-values'])) return $queryArray1;
        foreach ($queryArray2['query-values'] as $val)  {
            $queryArray1['query-values'][sizeof($queryArray1['query-values'])] = $val;
        }
        return $queryArray1;
    }
    private static function buildSelectQuery($jArray1)  {
        if (! isset($jArray1['tables']) || ! isset($jArray1['cols'])) return null;
        $tableList = self::tables($jArray1['tables']);
        if (is_null($tableList)) return null;
        $columnList = self::cols($jArray1['cols']);
        if (is_null($columnList)) return null;
        $query = "SELECT $columnList FROM $tableList";
        $queryArray1 = array();
        $queryArray1['old-query'] = $query;
        $queryArray1['pdo-query'] = $query;
        $queryArray1['query-values'] = array();
        $whereClause = null; if (isset($jArray1['where'])) $whereClause = self::where($jArray1['where']);
        if (! is_null($whereClause)) {
            $query = " WHERE";
            $queryArray1['old-query'] .= $query;
            $queryArray1['pdo-query'] .= $query;
            //Now join
            $queryArray1 = self::joinQueryArrays($queryArray1, $whereClause);
        }
        return $queryArray1;
    } 
    private static function buildUpdateQuery($jArray1)  {
        if (! isset($jArray1['table']) || ! isset($jArray1['cols'])) return null;
        $tablename = null; if (! is_array($jArray1['table'])) $tablename = $jArray1['table'];
        if (is_null($tablename)) return null;
        if (! isset($jArray1['cols'])) return null;
        $colList = null;
        $placeholderList = "";
        $colvalArray1 = array();
        $instrLen = strlen(self::$columnInstruction);
        foreach ($jArray1['cols'] as $colArr1)  {
            $enableInstr = false;
            if (! is_array($jArray1['cols']) || ! sizeof($jArray1['cols']) == 2) continue;
            $colname = key($colArr1);
            $tval = $colArr1[$colname];
            if (substr($tval, 0, $instrLen) == self::$columnInstruction)  {
                $enableInstr = true;
                $tval = substr($tval, $instrLen);
            }
            $colval = "'".$tval."'";
            $pcolval = $tval;
            $colval = $tval;
            //if (in_array(gettype($tval), array("integer"))) $colval = $tval;
            //if (is_numeric($tval)) { $colval = intval($tval); $pcolval = intval($tval); }
            if (is_double($tval)) { $colval = doubleval($tval); $pcolval = doubleval($tval); }
            else if (is_float($tval)) { $colval = floatval($tval); $pcolval = floatval($tval); }
            else if (is_int($tval)) { $colval = intval($tval); $pcolval = intval($tval); }
            $dt = $colname." = ".$colval;
            $dtp = $colname." = ?";
            if ($enableInstr) $dtp = $colname." = ".$colval;
            if (is_null($colList)) {
                $colList = $dt;
                $placeholderList = $dtp;
            }   else {
                $colList .= ", ".$dt;
                $placeholderList .= ", ".$dtp;
            }
            if (! $enableInstr) $colvalArray1[sizeof($colvalArray1)] = $pcolval;
        }
        if (is_null($colList)) return null;
        $query = "UPDATE $tablename SET $colList";
        $query = "UPDATE $tablename SET";
        $queryArray1['old-query'] = $query." $colList";
        $queryArray1['pdo-query'] = $query." $placeholderList";
        $queryArray1['query-values'] = $colvalArray1;
        $whereClause = null; if (isset($jArray1['where'])) $whereClause = self::where($jArray1['where']);
        if (! is_null($whereClause)) {
            $query = " WHERE";
            $queryArray1['old-query'] .= $query;
            $queryArray1['pdo-query'] .= $query;
            //Now join
            $queryArray1 = self::joinQueryArrays($queryArray1, $whereClause);
        }
        return $queryArray1;
    }
    private static function buildInsertQuery($jArray1)  {
        if (! isset($jArray1['table']) || ! isset($jArray1['cols'])) return null;
        $tablename = null; if (! is_array($jArray1['table'])) $tablename = $jArray1['table'];
        if (is_null($tablename)) return null;
        if (! isset($jArray1['cols'])) return null;
        $colnameList = null;
        $colvalList = null;
        $placeholderList = ""; //?
        $colvalArray1 = array();
        $instrLen = strlen(self::$columnInstruction);
        foreach ($jArray1['cols'] as $colArr1)  {
            $enableInstr = false; 
            if (! is_array($colArr1)) continue;
            $colname = key($colArr1);
            $tval = $colArr1[$colname];
            if (substr($tval, 0, $instrLen) == self::$columnInstruction)  {
                $enableInstr = true;
                $tval = substr($tval, $instrLen);
            }
            $colval = "'".$tval."'";
            $pcolval = $tval;
            //if (in_array(gettype($tval), array("integer"))) $colval = $tval;
            //if (is_numeric($tval)) { $colval = intval($tval); $pcolval = intval($tval); }
            if (is_double($tval)) { $colval = doubleval($tval); $pcolval = doubleval($tval); }
            else if (is_float($tval)) { $colval = floatval($tval); $pcolval = floatval($tval); }
            else if (is_int($tval)) { $colval = intval($tval); $pcolval = intval($tval); }
            if (is_null($colnameList)) $colnameList = $colname;
            else $colnameList .= ", ".$colname;
            $t_placeholderList = "?";
            if ($enableInstr) $t_placeholderList = $colval;
            if (is_null($colvalList)) {
                $colvalList = $colval;
                $placeholderList = $t_placeholderList;
            } else {
                $colvalList .= ", ".$colval;
                $placeholderList .= ", $t_placeholderList";
            }
            if (! $enableInstr) $colvalArray1[sizeof($colvalArray1)] = $pcolval;
        }
        if (is_null($colnameList) || is_null($colvalList)) return null;
        $query = "INSERT INTO $tablename ($colnameList) VALUES";
        $queryArray1 = array();
        $queryArray1['old-query'] = $query." ($colvalList)";
        $queryArray1['pdo-query'] = $query." ($placeholderList)";
        $queryArray1['query-values'] = $colvalArray1;
        return $queryArray1;
    }
    private static function buildDeleteQuery($jArray1)  {
        if (! isset($jArray1['table'])) return null;
        $tablename = null; if (! is_array($jArray1['table'])) $tablename = $jArray1['table'];
        if (is_null($tablename)) return null;
        $query = "DELETE FROM $tablename";
        $queryArray1 = array();
        $queryArray1['old-query'] = $query;
        $queryArray1['pdo-query'] = $query;
        $queryArray1['query-values'] = array();
        $whereClause = null; if (isset($jArray1['where'])) $whereClause = self::where($jArray1['where']);
        if (! is_null($whereClause)) {
            $query = " WHERE";
            $queryArray1['old-query'] .= $query;
            $queryArray1['pdo-query'] .= $query;
            //Now join
            $queryArray1 = self::joinQueryArrays($queryArray1, $whereClause);
        }
        return $queryArray1;
    }
    public static function buildQuery($jArray1)  {
        /*
        Now return the PDO's format as follows
        $queryArray1
            -['old-query'] ie SELECT * FROM table1 WHERE col1='va1' AND col2='val2'
            -['pdo-query'] ie SELECT * FROM table1 WHERE col1=? AND col2=?
            -['query-values'][0] = 'val'
                           [1] = 'val2'
        */
        $queryArray1 = null;
        switch ($jArray1['query'])  {
            case "select":
                $queryArray1 = self::buildSelectQuery($jArray1);
                break;
            case "insert":
                $queryArray1 = self::buildInsertQuery($jArray1);
                break;
            case "update":
                $queryArray1 = self::buildUpdateQuery($jArray1);
                break;
            case "delete":
                $queryArray1 = self::buildDeleteQuery($jArray1);
                break; 
            default:;
        }
        return $queryArray1;
    }
}
?>