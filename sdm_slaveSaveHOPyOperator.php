<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$userid = $_POST['userid'];
$operator = $_POST['operator'];
if ($operator != ''){
	$stra = 'update ' . $dbname . '.sdm_ho_employee set' . "\r\n\t\t\t" . 'operator=\'' . $operator . '\'' . "\r\n\t\t\t" . 'where karyawanid=' . $userid;
}
if (mysql_query($stra)) {
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
