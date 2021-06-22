<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$koderorg = $_POST['koderorg'];
$kapasitasolah = $_POST['kapasitasolah'];
$berlakusampai = tanggalsystemd($_POST['berlakusampai']);
$jammulai = tanggalsystemd($_POST['jam_mulai']);
$jamselesai = tanggalsystemd($_POST['jam_selesai']);
$method = $_POST['method'];

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.pabrik_5jampengolahan where koderorg=\'' . $koderorg . '\' ';
	break;

case 'update':
	$strx = 'update ' . $dbname . '.pabrik_5jampengolahan set kapasitasolah=\'' . $kapasitasolah . '\',jammulai=\'' . $jammulai . '\',jamselesai=\'' . $jamselesai . '\',berlakusampai=\'' . $berlakusampai . '\' where koderorg=\'' . $koderorg . '\'';
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.pabrik_5jampengolahan(' . "\r\n\t\t\t\t\t" . '   koderorg,kapasitasolah,jammulai,jamselesai,berlakusampai)' . "\r\n\t\t\t\t" . 'values(\'' . $koderorg . '\',\'' . $kapasitasolah . '\',\'' . $jammulai . '\',\'' . $jamselesai . '\',\'' . $berlakusampai . '\')';
	break;
}

if (($strx)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str = 'select * from ' . $dbname . '.pabrik_5jampengolahan order by koderorg desc';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$noakun = $bar->noakun;
		$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $bar->koderorg . '\'';

		#exit(mysql_error($conn));
		($rep = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rep);
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->kapasitasolah . '</td>' . "\r\n\t\t\t" . '  <td>' . tanggalnormald($bar->jammulai) . '</td>' . "\r\n\t\t\t" . '  <td>' . tanggalnormald($bar->jamselesai) . '</td>' . "\r\n\t\t\t" . '  <td>' . tanggalnormald($bar->berlakusampai) . '</td>' . "\r\n\t\t\t" . '  ' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->koderorg . '\',\'' . $bar->kapasitasolah . '\',\'' . tanggalnormald($bar->jammulai) . '\',\'' . tanggalnormald($bar->jamselesai) . '\',\'' . tanggalnormald($bar->berlakusampai) . '\',\'' . $bas->namaorganisasi . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delJampeng(\'' . $bar->koderorg . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

?>
