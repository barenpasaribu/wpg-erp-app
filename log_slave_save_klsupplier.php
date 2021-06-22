<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$tipe = $_POST['tipe'];
$kode = $_POST['kode'];
$kelompok = $_POST['kelompok'];
$noakun = $_POST['noakun'];
$method = $_POST['method'];
$strx = 'select 1=1';

switch ($method) {
case 'delete':
	$strx = "delete from $dbname.log_5klsupplier where kode='$kode'";
	break;

case 'update':
	$strx = "update $dbname.log_5klsupplier set tipe='$tipe',kelompok='$kelompok', noakun='$noakun' where kode='$kode'";
	break;

case 'insert':
	$strx = "insert into $dbname.log_5klsupplier(kode,kelompok,noakun,tipe) values('$kode','$kelompok','$noakun','$tipe')";
	break;
}


if (mysql_query($strx)) {
	echo 'OK';
} else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str = " select * from $dbname.log_5klsupplier where tipe='$tipe' order by kelompok";

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->kode . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->kelompok . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->tipe . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->noakun . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delKlSupplier(\'' . $bar->kode . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Update\' onclick="editKlSupplier(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->tipe . '\',\'' . $bar->noakun . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
