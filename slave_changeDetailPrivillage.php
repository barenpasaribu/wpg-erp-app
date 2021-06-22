<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$menuid = trim($_POST['menuid']);
$uname = trim($_POST['uname']);
$action = $_POST['action'];
print_r($_POST['uname']);
$status = false;
$str = 'select * from ' . $dbname . '.auth' . "\r\n" . '         where namauser=\'' . $uname . '\'' . "\r\n\t\t" . ' and menuid=' . $menuid;
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	$status = true;
}
else {
	$status = false;
}

if (!$status && ($action == 'remove')) {
	$str = 'insert into ' . $dbname . '.auth' . "\r\n" . '            (namauser,menuid,status,lastuser) ' . "\r\n\t" . '        values(\'' . $uname . '\',' . $menuid . ',0,\'' . $_SESSION['standard']['username'] . '\')' . "\r\n\t\t\t";
	$s = 5;
}

if ($status && ($action == 'remove')) {
	$str = 'update ' . $dbname . '.auth' . "\r\n" . '            set status=0,' . "\r\n\t\t\t" . 'lastuser=\'' . $_SESSION['standard']['username'] . '\' ' . "\r\n\t" . '        where namauser=\'' . $uname . '\'' . "\r\n\t\t\t" . 'and menuid=' . $menuid;
	$s = 2;
}
else if (!$status && ($action == 'add')) {
	$str = 'insert into ' . $dbname . '.auth' . "\r\n" . '            (namauser,menuid,status,lastuser) ' . "\r\n\t" . '        values(\'' . $uname . '\',' . $menuid . ',1,\'' . $_SESSION['standard']['username'] . '\')' . "\r\n\t\t\t";
	$s = 3;
}
else {
	$str = 'update ' . $dbname . '.auth' . "\r\n" . '            set status=1,' . "\r\n\t\t\t" . 'lastuser=\'' . $_SESSION['standard']['username'] . '\'  ' . "\r\n\t" . '        where namauser=\'' . $uname . '\'' . "\r\n\t\t\t" . 'and menuid=' . $menuid;
	$s = 4;
}

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
