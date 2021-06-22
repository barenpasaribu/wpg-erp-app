<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$period = $_POST['period'];
$tglthr = $_POST['tglthr'];
$_SESSION['thrperiode'] = $period;
$_SESSION['tglthr'] = $tglthr;

if (strlen(trim($_SESSION['thrperiode'])) != 7) {
	$_SESSION['pyperiode'] = date('Y-m');
	$_SESSION['tglthr'] = date('Y-m-d');
}

?>
