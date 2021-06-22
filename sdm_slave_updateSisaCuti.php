<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorg'];
$karyawanid = $_POST['karyawanid'];
$periode = $_POST['periode'];
$sisa = $_POST['sisa'];
$str = 'update ' . $dbname . '.sdm_cutiht ' . "\r\n" . '      set sisa=' . $sisa . "\r\n" . '     where ' . "\r\n" . '      kodeorg=\'' . $kodeorg . '\'' . "\r\n\t" . '  and karyawanid=' . $karyawanid . "\r\n\t" . '  and periodecuti=\'' . $periode . '\'';
mysql_query($str);

?>
