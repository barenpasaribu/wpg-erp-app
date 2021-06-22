<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$newcap = $_POST['newcap'];
$newloc = $_POST['newloc'];
$langname = $_POST['langname'];
$idx = $_POST['idx'];
$str = 'update ' . $dbname . '.bahasa set location=\'' . $newloc . '\',' . "\r\n" . '        ' . $langname . '=\'' . $newcap . '\' where idx=' . $idx;

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
