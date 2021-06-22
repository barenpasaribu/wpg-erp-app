<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$persen = $_POST['persen'];
$max = $_POST['max'];
$str = 'delete from ' . $dbname . '.sdm_ho_pph21jabatan';
$res = mysql_query($str, $conn);
$str1 = 'insert into ' . $dbname . '.sdm_ho_pph21jabatan(`persen`,`max`) ' . "\r\n\t\t" . '       values(' . $persen . ',' . $max . ')';

if (mysql_query($str1, $conn)) {
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
