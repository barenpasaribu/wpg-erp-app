<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$uname = trim($_POST['uname']);
$p1 = $_POST['p1'];
$p2 = $_POST['p2'];
$str = 'select * from ' . $dbname . '.user where namauser=\'' . $uname . '\'' . "\r\n" . '      and password=MD5(\'' . $p1 . '\')';
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	echo ' Gagal:Old password doesn\'t match';
}
else {
	$str = 'update ' . $dbname . '.user' . "\r\n\t" . '      set password=MD5(\'' . $p2 . '\'),' . "\r\n\t\t" . '  lastuser=\'' . $_SESSION['standard']['username'] . '\' ' . "\r\n\t\t" . '  where namauser=\'' . $uname . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
