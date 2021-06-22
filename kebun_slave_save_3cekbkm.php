<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_POST['proses'];
$not = $_POST['not'];
$hk = $_POST['hk'];
$hs = str_replace(',', '', $_POST['hs']);

switch ($proses) {
case 'savedata':
	$str = 'update ' . $dbname . '.kebun_prestasi set jumlahhk=\'' . $hk . '\',hasilkerja=\'' . $hs . '\' where notransaksi=\'' . $not . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
