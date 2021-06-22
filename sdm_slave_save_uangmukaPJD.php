<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$bayar = $_POST['bayar'];
$tglbayar = tanggalsystem($_POST['tglbayar']);
$notransaksi = $_POST['notransaksi'];
$str = 'update ' . $dbname . '.sdm_pjdinasht set dibayar=' . $bayar . "\r\n\t" . '      ,tglbayar=' . $tglbayar . ' where notransaksi=\'' . $notransaksi . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
