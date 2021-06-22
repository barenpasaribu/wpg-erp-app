<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$parent = $_POST['parent'];
$child = $_POST['child'];
$str = 'select max(urut) from ' . $dbname . '.menu where parent=' . $parent;
$res = mysql_query($str);

while ($bar = mysql_fetch_array($res)) {
	$urut = $bar[0];
}

$urut += 1;
$str1 = 'update ' . $dbname . '.menu' . "\r\n" . '      set parent=' . $parent . ', urut=' . $urut . ',lastuser=\'' . $_SESSION['standard']['username'] . '\'' . "\r\n\t" . '  where id=' . $child;

if (mysql_query($str1)) {
	$str2 = 'update ' . $dbname . '.menu' . "\r\n" . '      ' . "\t\t" . '   set type=\'parent\' where id=' . $parent . ' and type!=\'master\'';

	if (mysql_query($str2)) {
	}
	else {
		echo ' Gagal, Parent type not modified: ' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
