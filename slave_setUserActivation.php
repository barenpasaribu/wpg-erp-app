<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
$setstatus = $_POST['setstatus'];
$str = 'update ' . $dbname . '.user' . "\r\n\t" . '      set status=' . $setstatus . ',' . "\r\n\t\t" . '  lastuser=\'' . $_SESSION['standard']['username'] . '\' ' . "\r\n\t\t" . '  where namauser=\'' . $uname . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
