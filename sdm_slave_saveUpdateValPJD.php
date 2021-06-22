<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$newvalpjd = $_POST['newvalpjd'];
$notransaksi = $_POST['notransaksi'];
$strup = 'update ' . $dbname . '.sdm_pjdinasht set uangmuka=' . $newvalpjd . "\t\r\n\t\t" . '       where notransaksi=\'' . $notransaksi . '\'';

if (mysql_query($strup)) {
	echo number_format($newvalpjd, 2, '.', ',');
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
