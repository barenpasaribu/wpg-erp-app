<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$bayar = $_POST['bayar'];
$tglbayar = tanggalsystem($_POST['tglbayar']);
$str = 'update ' . $dbname . '.sdm_penggantiantransport set dibayar=' . $bayar . ',' . "\r\n" . '      tanggalbayar=' . $tglbayar . ',' . "\r\n\t" . '  posting=1' . "\r\n\t" . '  where notransaksi=\'' . $notransaksi . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal ' . addslashes(mysql_error($conn));
}

?>
