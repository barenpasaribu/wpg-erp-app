<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$hideValue = $_POST['setHide'];
$str1 = 'update ' . $dbname . '.menu set hide=' . $hideValue . ',' . "\r\n\t" . '       lastuser=\'' . $_SESSION['standard']['username'] . '\'' . "\r\n\t\t" . '   where id=' . $id;

if (mysql_query($str1)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
