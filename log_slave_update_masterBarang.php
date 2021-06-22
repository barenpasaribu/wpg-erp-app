<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodebarang = $_POST['kodebarang'];
$status = $_POST['status'];
$str = 'update ' . $dbname . '.log_5masterbarang set inactive=' . $status . ' where kodebarang=' . $kodebarang;

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
