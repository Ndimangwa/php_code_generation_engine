<?php 
class SystemRules {
    public final static function isLoopDetected($conn, $pId, $ppId)	{
		/* If anywhere in the ladder pId is a parent of ppId scream */
		if (is_null($ppId)) return false;
        if ($ppId==$pId) return true;
        $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
            array('_groups'),
            array('pId'),
            array('groupId' => $ppId)
        ), $conn);
        if (is_null($jresult1)) throw new Exception("Could not pull records");
        $jArray1 = json_decode($jresult1, true);
        if (is_null($jArray1)) throw new Exception("Could not decode results");
        if ($jArray1['count'] != 1) throw new Exception("Loop (STP) : Duplicate or no results");
		$ppId = $jArray1['rows'][0]['pId'];
		return self::isLoopDetected($conn, $pId, $ppId);
	}
    public static function onlyRootCan($currentLogin1, $classname, $contextName)  {
        $a = in_array($contextName, array('update_my_login'));
        $b = $currentLogin1->isRoot();
        $c = in_array($classname, array('Login', 'JobTitle', 'Group'));
        return (! $a && ! $c || $a && $c || ! $a && $b);
    }
    public static function canUpdateSelf($currentLogin1, $contextName/* login_update */, $loginId)   {
        return (in_array($contextName, array('login_update', 'login_delete')) && $currentLogin1->getLoginId() == $loginId);
    }
    public static function evaluate($conn, $profile1, $currentLogin1, $classname, $query, $contextName, $args /*$_POST*/)   {
        //1.0 Make sure only Root Access, Login, Group & JobTitle
        if (! self::onlyRootCan($currentLogin1, $classname, $contextName)) throw new Exception("Root Access Denied [ $contextName ] ; Kindly Consult your Administrator");
        //2.0 Make sure you do not update self
        if (isset($args['__id__']) && self::canUpdateSelf($currentLogin1, $contextName, $args['__id__'])) throw new Exception('Can Not Update Self Account');
        //3.0 Can not delete, update rootGroup
        //4.0 Make sure you do not form loop 
        if ($contextName == "group_update" && isset($args['__id__']) && isset($args['parentGroup']) && self::isLoopDetected($conn, $args['__id__'], $args['parentGroup'])) throw new Exception("Loop Detection , You are trying to make self parent or child parent");
    }
}
?>