<?php 
$parentMenu = null; if (isset($_REQUEST['parentMenu'])) $parentMenu = $_REQUEST['parentMenu'];
$contextName = null; if (isset($_REQUEST['contextName'])) $contextName = $_REQUEST['contextMenu'];
echo NavigationMenu::loadMenu($conn, $nextpage, $parentMenu, $contextName);
?>