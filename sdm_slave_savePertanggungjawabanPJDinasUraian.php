<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$uraian = $_POST['uraian'];
$str = 'update ' . $dbname . '.sdm_pjdinasht ' . "\r\n\t" . '      set hasilkerja=\'' . $uraian . '\'' . "\r\n\t\t" . '  where notransaksi=\'' . $notransaksi . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal:' . addslashes(mysql_error($conn));
	exit(0);
}

?>
