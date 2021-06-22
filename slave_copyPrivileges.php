<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$dari = $_POST['dari'];
$pengguna = $_POST['pengguna'];
$str = 'delete from ' . $dbname . '.auth where namauser=\'' . $pengguna . '\'';
mysql_query($str);
$str = 'select menuid, status, lastuser, detail from ' . $dbname . '.auth where namauser=\'' . $dari . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$str1 = 'insert into ' . $dbname . '.auth(namauser,menuid, status, lastuser, detail)' . "\r\n" . '           values(\'' . $pengguna . '\',' . $bar->menuid . ',' . $bar->status . ',\'' . $_SESSION['standard']['username'] . '\',\'' . $bar->detail . '\');';

	if (mysql_query($str1)) {
	}
	else {
		exit('Error:' . addslashes(mysql_error($conn)));
	}
}

?>
