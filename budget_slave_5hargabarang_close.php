<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$regional = $_POST['regional'];
$kodebarang = $_POST['kodebarang'];
$str = 'UPDATE ' . $dbname . '.`bgt_masterbarang` ' . "\r\n" . 'SET `closed` = \'1\'' . "\r\n" . 'WHERE `regional` = \'' . $regional . '\' AND `tahunbudget` = \'' . $tahunbudget . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
