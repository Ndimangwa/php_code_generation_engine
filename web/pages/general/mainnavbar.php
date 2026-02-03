<nav class="navbar navbar-expand-sm navbar-dark bg-dark p-0">
        <div class="container">
            <a data-toggle="tooltip" title="Welcome, <?= $login1->getFullName() ?>" href="<?= $thispage ?>" class="navbar-brand"><?= $login1->getLoginName() ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav">
                    <li class="nav-item px-2">
                        <a href="<?= $thispage ?>?page=dashboard" class="nav-link active">Dashboard</a>
                    </li>
<?php 
    if (Authorize::isAllowable($config1, "menu_users", "normal", "donotsetlog", null, null)) {
?>
                    <li class="nav-item dropdown px-2">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle">
                            <i></i>Users
                        </a>
                        <div class="dropdown-menu">
<?php 
        if (Authorize::isAllowable($config1, "login", "normal", "donotsetlog", null, null)) {
?>
                            <a href="<?= $thispage ?>?page=login" class="dropdown-item">
                                <i></i>User Accounts
                            </a>
<?php 
        }
        if (Authorize::isAllowable($config1, "jobtitle", "normal", "donotsetlog", null, null))  {
?>
                            <a href="<?= $thispage ?>?page=jobtitle" class="dropdown-item">
                                <i></i>Job Title
                            </a>
<?php 
        }
        if (Authorize::isAllowable($config1, "group", "normal", "donotsetlog", null, null)) {
?>
                            <a href="<?= $thispage ?>?page=group" class="dropdown-item">
                                <i></i>Groups
                            </a>
<?php 
        }
?>
                        </div>
                    </li>
<?php 
    }
?>
                </ul>
                <ul class="navbar-nav ml-auto">
<?php 
    if (Authorize::isAllowable($config1, "menu_mysystem", "normal", "donotsetlog", null, null)) {
?>
                    <li class="nav-item dropdown mr-3">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle">
                            <i class="fas fa-user"></i> My System
                        </a>
                        <div class="dropdown-menu">
<?php 
        if (Authorize::isAllowable($config1, "update_my_login", "normal", "donotsetlog", null, null))   {
?>
                            <a href="<?= $thispage ?>?page=update_my_login" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>My Profile
                            </a>
<?php 
        } 
        if ($login1->isRoot())  {
?>
                            <a href="<?= $thispage ?>?page=profile_update" class="dropdown-item">
                                <i class="fas fa-cog"></i>System Settings
                            </a>
                            <a href="<?= $thispage ?>?page=lastresortdonotcare" class="dropdown-item">
                                <i class="fas fa-lock"></i>Last Resort (ALLOW/DENY)
                            </a>
                            <a href="<?= $thispage ?>?page=systemlogs" class="dropdown-item">
                                <i></i>System Logs
                            </a>
<?php 
        }
?>
                        </div>
                    </li>
<?php 
    }
?>
                    <li class="nav-item">
                        <a id="logoutButton" data-toggle="tooltip" title="Logout <?= $login1->getLoginName() ?>" href="#" class="nav-link">
                            <i class="fas fa-user-times"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>