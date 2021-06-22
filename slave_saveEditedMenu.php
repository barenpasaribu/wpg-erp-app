<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$caption = $_POST['caption'];
$caption2 = $_POST['caption2'];
$caption3 = $_POST['caption3'];
$action = $_POST['action'];
$str = 'update ' . $dbname . '.menu set action=\'' . $action . '\',caption=\'' . $caption . '\',caption2=\'' . $caption2 . '\',caption3=\'' . $caption3 . '\'' . "\r\n\t" . '       where id=' . $id;

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
