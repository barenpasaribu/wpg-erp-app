<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
$_SESSION['tmp']['actStat'] = 'tbm';
include 'kebun_operasional.php';
echo close_body();

?>
