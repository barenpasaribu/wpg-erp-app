<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$tglPosting = date('Y-m-d');
$noinvoice = $_POST['noinvoice'];
$str = 'update ' . $dbname . '.keu_tagihanht set posting=1,' . "\r\n\t" . 'postingby=' . $_SESSION['standard']['userid'] . ',' . "\r\n\t" . 'tanggalposting=\'' . $tglPosting . '\'' . "\r\n" . '    where noinvoice=\'' . $noinvoice . '\'';
mysql_query($str);

if (mysql_affected_rows($conn) == 0) {
	echo 'Error: None Updated' . $str;
}
else {
	echo tanggalnormal($tglPosting);
}

?>
