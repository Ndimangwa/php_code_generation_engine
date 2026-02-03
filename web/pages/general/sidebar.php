<nav class="navbar navbar-inverse fixed-top" id="sidebar-wrapper" role="navigation">
    <ul class="nav sidebar-nav">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <a href="#"><?= $profile1->getSystemName() ?></a>
            </div>
        </div>
        <li><a href="<?= $thispage ?>">Home</a></li>
<?php 
    if ($login1->isRoot())  {
?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">User Accounts <span class="caret"></span></a>
            <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <li><a href="<?= $thispage ?>?page=group">User Groups</a></li>
                <li><a href="<?= $thispage ?>?page=jobtitle">Job Title</a></li>
                <li><a href="<?= $thispage ?>?page=login">User Accounts</a></li>
            </ul>
        </li>
<?php 
    }
    if (Authorize::isAllowable($config1, "patient", "normal", "donotsetlog", null, null))   {
?>
        <li><a href="<?= $thispage ?>?page=patient">Patients</a></li>
<?php 
    }
    if (Authorize::isAllowable($config1, "menu_finance", "normal", "donotsetlog", null, null))  {
?>
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">Finances <span class="caret"></span></a>
            <ul class="dropdown-menu animated fadeInLeft" role="menu">
<?php
        if (Authorize::isAllowable($config1, "servicecategory", "normal", "donotsetlog", null, null))   {
?>
            <li><a href="<?= $thispage ?>?page=servicecategory">Service Category</a></li>
<?php
        }
        if (Authorize::isAllowable($config1, "service", "normal", "donotsetlog", null, null))   {
?>
            <li><a href="<?= $thispage ?>?page=service">Service</a></li>
<?php
        }
?>               
            </ul>
        </li>
<?php        
    }
?>
        <li><a href="#contact">Laboratory</a></li>
        <li><a href="#followme">Admission</a></li>
        <li><a href="#followme">Pharmaceutical</a></li>
        <li><a href="#followme">Accomodation</a></li>
        <li></li>
<?php 
    if (Authorize::isAllowable($config1, "menu_mysystem", "normal", "donotsetlog", null, null))   {
?>    
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">My System <span class="caret"></span></a>
            <ul class="dropdown-menu animated fadeInLeft" role="menu">
<?php 
        if (Authorize::isAllowable($config1, "update_my_login", "normal", "donotsetlog", null, null))   {
?>                
                <li><a href="<?= $thispage ?>?page=update_my_login">My Profile <i class="fas fa-user-circle"></i></a></li>
<?php 
        }
        if ($login1->isRoot())  {
?>
                <li><a href="#">Patient Flow</a></li>
                <li><a href="<?= $thispage ?>?page=profile_update">System Settings <i class="fas fa-cog"></i></a></li>
                <li><a href="<?= $thispage ?>?page=lastresortdonotcare">Last Resort (ALLOW/DENY) <i class="fas fa-lock"></i></a></li>
                <li><a href="<?= $thispage ?>?page=systemlogs">System Logs <i></i></a></li>
<?php 
        }
?>
            </ul>
        </li>
<?php 
    }
?>
        <li><a id="logoutButton" href="#" data-toggle="tooltip" title="Logout <?= $login1->getLoginName() ?>">Logout</a></li>

    </ul>
</nav>