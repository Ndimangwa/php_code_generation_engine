<?php 
class PageMovement {
    public static function getDisplayText($config1, $page, $defaultText = "My Text")    {
        $homeText = "Dashboard";
        $dbname = $config1->getDatabase();
        $host = $config1->getHostname();
        $displayArray1 = array();
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
            $jresults1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
                array('_contextPosition'),
                array('cName', 'caption'),
                null
            ), $conn);
            if (is_null($jresults1)) throw new Exception("Could not get results");
            $jArray1 = json_decode($jresults1, true);
            if (is_null($jArray1)) throw new Exception("Could not decode the results");
            if ($jArray1['code'] != 0) throw new Exception($jArray1['message']);
            foreach ($jArray1['rows'] as $row) {
                $key = $row['cName']; $val = $row['caption'];
                $displayArray1[$key] = $val;
            }
            $conn = null;
        } catch (Exception $e)  {
            return $defaultText;
        }
        return is_null($page) ? $homeText : (isset($displayArray1[$page]) ? $displayArray1[$page] : $defaultText);
    }
    public static function getBreadCrumbs($thispage, $page, $usepages = array(""))  {
        /*
        usepages = array('update_my_login' => array('page' => 'login_create', 'href' => 'val'))
        */
        $homeText = "Dashboard";
        $list = "<ol class=\"breadcrumb float-sm-right\">";
        if ($page == null)  {
            $list .= "<li class=\"breadcrumb-item active\">$homeText</li>";
        } else {
            $list .= "<li class=\"breadcrumb-item\"><a href=\"$thispage\">$homeText</a></li>";
            if (isset($usepages[$page]))    {
                $pg = $usepages[$page];
                if (isset($pg['page'])) $page = $pg['page'];
            }
            $prefix = null;
            foreach (explode("_", $page) as $frg)   {
                if (is_null($prefix)) $prefix = $frg;
                else $prefix = $prefix .= "_$frg";
                $caption = __object__::property2Caption($frg);
                if ($prefix == $page)   {
                    $list .= "<li class=\"breadcrumb-item active\">$caption</li>";
                } else {
                    $href = $prefix;
                    if (isset($usepages[$prefix]))  {
                        $pg = $usepages[$prefix];
                        if (isset($pg['page'])) $prefix = $pg['page'];
                        if (isset($pg['href'])) $href = $pg['href'];
                    }
                    $href = $thispage."?page=$href";
                    $list .= "<li class=\"breadcrumb-item\"><a href=\"$href\">$caption</a></li>";
                }
            }
        }
        $list .= "</ol>";
        return $list;
    }
    private static function filepath($defaultPagePath, $actualpage, $submittedpage) {
        $filepath = $defaultPagePath.DIRECTORY_SEPARATOR.$actualpage.DIRECTORY_SEPARATOR.$submittedpage.".php";
        return str_replace("//", "/", $filepath);
    }
    public static function check($config1, $submittedpage, $listoflookuppages, $defaultPagePath = "../pages/")  {
        foreach ($listoflookuppages as $actualpage) {
            echo self::filepath($defaultPagePath, $actualpage, $submittedpage)."<br/>";
            if ($submittedpage == $actualpage && Authorize::isAllowable($config1, $submittedpage, "normal", "setlog", null, null))  {
                include(self::filepath($defaultPagePath, $actualpage, $submittedpage));
                echo "<br/>DEFAULT<br/>";
                return true;
            } else if ($submittedpage ==  "servicecategory_create"/*$actualpage."_create"*//* && Authorize::isAllowable($config1, $submittedpage, "normal", "setlog", null, null)*/) {
                include(self::filepath($defaultPagePath, $actualpage, $submittedpage));
                echo "<br/>CREATE<br/>";
                return true;
            } else if ($submittedpage == $actualpage."_read" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $submittedpage, "normal", "setlog", null, null))   {
                include(self::filepath($defaultPagePath, $actualpage, $submittedpage));
                echo "<br/>READ<br/>";
                return true;
            } else if ($submittedpage == $actualpage."_update" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $submittedpage, "normal", "setlog", null, null)) {
                include(self::filepath($defaultPagePath, $actualpage, $submittedpage));
                echo "<br/>UPDATE<br/>";
                return true;
            } else if ($submittedpage == $actualpage."_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config1, $submittedpage, "normal", "setlog", null, null)) {
                include(self::filepath($defaultPagePath, $actualpage, $submittedpage));
                echo "<br/>DELETE<br/>";
                return true;
            }
        }
        return false;
    }
}
?>