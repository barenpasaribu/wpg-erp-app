<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$kelompok = $_POST['kelompok'];
$noakun = $_POST['noakun'];
$method = $_POST['method'];

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '. pmn_4klcustomer where kode=\'' . $kode . '\'';
	$hasil = mysql_query($strx);
	break;

case 'update':
	$strx = 'update ' . $dbname . '. pmn_4klcustomer set kelompok=\'' . $kelompok . '\',noakun=\'' . $noakun . '\' where kode=\'' . $kode . '\'';
	$hasil = mysql_query($strx);
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.pmn_4klcustomer(kode,kelompok,noakun) values (\'' . $kode . '\',\'' . $kelompok . '\',\'' . $noakun . '\')';
	$hasil = mysql_query($strx);
	break;
}

if (($strx)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str = 'select * from ' . $dbname . '.pmn_4klcustomer order by kode desc';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$noakun = $bar->noakun;
		$spr = 'select * from  ' . $dbname . '.keu_5akun where `noakun`=\'' . $noakun . '\'';

		#exit(mysql_error($conn));
		($rep = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rep);
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->kode . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->kelompok . '</td>' . "\r\n\t\t\t" . '   <td>' . $bar->noakun . '</td>' . "\r\n\t\t\t" . '  <td>' . $bas->namaakun . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->noakun . '\',\'' . $bas->namaakun . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delKlmpkplgn(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->noakun . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

?>
