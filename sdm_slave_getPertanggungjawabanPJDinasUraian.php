<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$notransaksi = $_POST['notransaksi'];
$str = 'select hasilkerja from ' . $dbname . '.sdm_pjdinasht ' . "\r\n\t" . '      where notransaksi=\'' . $notransaksi . '\'';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		echo $bar->hasilkerja;
	}
}
else {
	echo ' Gagal:' . addslashes(mysql_error($conn));
	exit(0);
}

?>
