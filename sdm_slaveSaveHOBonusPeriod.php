<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$period = $_POST['period'];
$periodegaji = $_POST['periodegaji'];
$_SESSION['bonusperiode'] = $period;
$_SESSION['periodegaji'] = $periodegaji;

if (strlen(trim($_SESSION['bonusperiode'])) != 7) {
	$_SESSION['bonusperiode'] = date('Y-m');
	$_SESSION['periodegaji'] = date('Y-m');
}

?>
