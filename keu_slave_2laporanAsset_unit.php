<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$param = $_POST;
$optUnit = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['pt'] . '\'');
echo '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

foreach ($optUnit as $code => $val) {
	echo '<option value=\'' . $code . '\'>' . $val . '</option>';
}

?>
