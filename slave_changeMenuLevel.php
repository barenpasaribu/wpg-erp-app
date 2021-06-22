<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$acname = $_POST['acname'];
$str = 'update ' . $dbname . '.tipeakses set status=0';
$str1 = 'update ' . $dbname . '.tipeakses set status=1 where access_name=\'' . $acname . '\'';

if (mysql_query($str)) {
	if (mysql_query($str1)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
