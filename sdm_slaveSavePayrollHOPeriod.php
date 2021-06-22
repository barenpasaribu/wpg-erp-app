<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$period = $_POST['period'];
$_SESSION['pyperiode'] = $period;

if (strlen(trim($_SESSION['pyperiode'])) != 7) {
	$_SESSION['pyperiode'] = date('Y-m');
}

?>
