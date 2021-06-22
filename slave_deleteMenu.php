<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$str = 'select * from ' . $dbname . '.menu where parent=' . $id;
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	echo ' Gagal, Hapus dari submenu paling dalam';
}
else {
	$str1 = 'delete from ' . $dbname . '.menu ' . "\r\n\t" . '        where id=' . $id;

	if (mysql_query($str1)) {
		$str2 = 'delete from ' . $dbname . '.auth' . "\r\n\t\t" . 'where menuid=' . $id;
		mysql_query($str2);
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
