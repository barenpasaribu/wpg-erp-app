<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$newlevel = $_POST['newlevel'];
$uname = $_POST['un'];
$str = 'update ' . $dbname . '.user set hak=' . $newlevel . ',' . "\r\n\t" . '      lastuser=\'' . $_SESSION['standard']['username'] . '\',' . "\r\n\t\t" . '  lastupdate=\'' . date('Y-m-d H:i:s') . '\'' . "\r\n\t" . '       where namauser=\'' . $uname . '\'';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
