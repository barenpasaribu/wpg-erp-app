<?php


session_start();
require_once 'config/connection.php';
require_once 'master_validation.php';
include 'lib/eagrolib.php';
$str = 'update ' . $dbname . '.user set logged=0 where namauser=\'' . $_SESSION['standard']['username'] . '\'';
$res = mysql_query($str);

if (0 < mysql_affected_rows($conn)) {
	session_destroy();
}

echo '<script language=javascript1.2>' . "\r\n" . '     window.location=\'.\';' . "\r\n\t" . ' </script>';

?>
