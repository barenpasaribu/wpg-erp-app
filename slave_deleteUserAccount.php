<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
$userid = $_POST['userid'];
$str = 'delete from ' . $dbname . '.user' . "\r\n\t\t" . '  where namauser=\'' . $uname . '\'';

if (mysql_query($str)) {
	$stg = 'delete from ' . $dbname . '.auth where namauser=\'' . $uname . '\'';
	mysql_query($stg);
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
