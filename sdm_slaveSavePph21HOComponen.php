<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$to = $_POST['to'];
$idx = $_POST['idx'];
$str1 = 'update ' . $dbname . '.sdm_ho_component ' . "\r\n\t\t" . '       set `pph21`=' . $to . "\r\n\t\t" . '       where id=' . $idx;

if (mysql_query($str1, $conn)) {
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
