<?php 
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
$conn = null;
$context1 = null;
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $context1 = new ContextPosition(
        $dbname,
        ContextPosition::getContextIdFromName($dbname, Authorize::getSessionValue(), $conn),
        $conn
    );
    $conn = null;
} catch (Exception $e) {
    $conn = null;
    $context1 = null;
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1 text-center align-middle">
            <div class="align-middle" style="top: 20%; margin-top: 12px; margin-bottom: 12px;">
                <div class="alert alert-danger alert-dismissible fade show my-5">
                    <strong style="font-size: 1.2rem; color: red">Operation Denied (<?= $context1->getContextName() ?>) !!</strong> <br />
                    You do not have enough rights to perform <b><?= $context1->getCaption() ?></b> operation
                    <button class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
Authorize::clearSession();
?>