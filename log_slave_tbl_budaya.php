<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$kodeorg = $_POST['kodeorg'];
$budidaya = $_POST['budidaya'];
$method = $_POST['method'];

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.kebun_5budidaya where kode=\'' . $kode . '\'';
	break;

case 'update':
	$strx = 'update ' . $dbname . '.kebun_5budidaya set kodeorg=\'' . $kodeorg . '\',budidaya=\'' . $budidaya . '\' where kode=\'' . $kode . '\'';
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.kebun_5budidaya(' . "\r\n\t\t\t\t\t" . '   kode,kodeorg,budidaya)' . "\r\n\t\t\t\t" . 'values(\'' . $kode . '\',\'' . $kodeorg . '\',\'' . $budidaya . '\')';
	break;
}

if (($strx)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$srt = 'select * from ' . $dbname . '.kebun_5budidaya order by kode desc';

if ($rep = mysql_query($srt)) {
	while ($bar = mysql_fetch_object($rep)) {
		$spr = 'select * from  ' . $dbname . '.organisasi where `kodeorganisasi`=\'' . $bar->kodeorg . '\'';

		#exit(mysql_error($conn));
		($rej = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rej);
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bas->kodeorganisasi . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->kode . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->budidaya . '</td>' . "\r\n\t\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kode . '\',\'' . $bar->kodeorg . '\',\'' . $bar->budidaya . '\');"></td>' . "\r\n\t\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delTbldya(\'' . $bar->kode . '\');"></td>' . "\r\n\t\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

?>
