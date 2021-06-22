<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$menuid = $_POST['menuid'];
$defaultMenu = $_POST['menuid'];
$str = 'select namauser from ' . $dbname . '.user where status=1';
$resus = mysql_query($str);

while ($barus = mysql_fetch_object($resus)) {
	$resuser[] = $barus->namauser;
}

$menu[] = $menuid;
$x = 0;

while ($x <= 7) {
	if ($menuid != '') {
		$str = 'select parent from ' . $dbname . '.menu where id=' . $menuid;
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			if ($bar->parent != 0) {
				$menu[] = $bar->parent;
				$menuid = $bar->parent;
			}
		}
	}

	++$x;
}

if ($_POST['aksi'] == 'remove') {
	$str = 'delete from ' . $dbname . '.auth where menuid=' . $defaultMenu;
	mysql_query($str);
}

if ($_POST['aksi'] == 'add') {
	foreach ($menu as $key => $val) {
		$str = 'delete from ' . $dbname . '.auth where menuid=' . $val;
		mysql_query($str);

		foreach ($resuser as $kunci => $namauser) {
			$str = 'insert into ' . $dbname . '.auth(namauser, menuid, status, lastuser, detail)' . "\r\n" . '                     values(\'' . $namauser . '\',' . $val . ',1,' . $_SESSION['standard']['userid'] . ',0)';
			mysql_query($str);
		}
	}
}

?>
