<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$level = trim($_POST['level']);
$id = $_POST['id'];
$str = 'update ' . $dbname . '.menu' . "\r\n\t" . '      set access_level=' . $level . ' ' . "\r\n\t\t" . '  where id=' . $id;

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
