<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = $_POST['kodeorg'];
$kodegolongan = $_POST['kodegolongan'];
$upah = $_POST['upah'];
$sql='SELECT kodeorg FROM bgt_upah WHERE tahunbudget=\'' . $tahunbudget . '\' AND kodeorg=\'' . $kodeorg . '\' AND golongan=\'' . $kodegolongan . '\'';
$getData = mysql_query($sql);
$row= mysql_num_rows($getData)
if($row => 1){
$str = 'DELETE FROM ' . $dbname . '.bgt_upah WHERE tahunbudget=\'' . $tahunbudget . '\' AND kodeorg=\'' . $kodeorg . '\' AND golongan=\'' . $kodegolongan . '\'';
	if (mysql_query($str)) {
	} else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
$str = 'INSERT INTO ' . $dbname . '.`bgt_upah` (' . "\r\n" . '`tahunbudget` ,' . "\r\n" . '`kodeorg` ,' . "\r\n" . '`golongan` ,' . "\r\n" . '`jumlah` ,' . "\r\n" . '`updateby` ,' . "\r\n" . '`lastupdate`' . "\r\n" . ')' . "\r\n" . 'VALUES (' . "\r\n" . '\'' . $tahunbudget . '\', \'' . $kodeorg . '\', \'' . $kodegolongan . '\', \'' . $upah . '\' , \'' . $_SESSION['standard']['userid'] . '\',' . "\r\n" . 'CURRENT_TIMESTAMP ' . "\r\n" . ')';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
