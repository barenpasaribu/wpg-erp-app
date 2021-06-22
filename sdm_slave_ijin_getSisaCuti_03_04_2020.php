<?php


require_once 'master_validation.php';
include_once 'config/connection.php';
$periode = $_POST['periode'];
$karyawanid = $_POST['karyawanid'];
$strf = 'select sisa from ' . $dbname . '.sdm_cutiht where karyawanid=' . $_SESSION['standard']['userid'] . ' ' . "\r\n" . '       and periodecuti=' . $periode;
$res = mysql_query($strf);
$sisa = '';

while ($barf = mysql_fetch_object($res)) {
	$sisa = $barf->sisa;
}

if ($sisa == '') {
	$sisa = 0;
}

echo $sisa;

?>
