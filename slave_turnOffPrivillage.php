<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$str = 'update  ' . $dbname . '.tipeakses set status =0 where status!=0';

if (mysql_query($str)) {
	if (mysql_affected_rows($conn) != -1) {
	}
	else {
		echo ' Gagal, security status is OFF';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
