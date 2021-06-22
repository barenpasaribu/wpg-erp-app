<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$tanggal = date('Ymd');
$sisa = $_POST['sisa'];
$bytiket = $_POST['bytiket'];

if ($sisa == '') {
	$sisa = 0;
}

$str = 'update ' . $dbname . '.sdm_pjdinasht' . "\r\n\t" . '       set sisa=' . $sisa . ',' . "\r\n" . '                            bytiket=' . $bytiket . ',' . "\r\n" . '                            tanggalsisa=' . $tanggal . ',' . "\r\n" . '                            lunas=1' . "\r\n\t" . '      where notransaksi=\'' . $notransaksi . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal:' . addslashes(mysql_error($conn));
	exit(0);
}

?>
