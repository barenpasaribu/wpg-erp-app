<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$method = $_POST['method'];

switch ($method) {
case 'delete':
	$x = 'update ' . $dbname . '.log_transaksiht set notransaksireferensi=\'\' where notransaksireferensi=\'' . $notransaksi . '\'';

	if (mysql_query($x)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	$i = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $notransaksi . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
