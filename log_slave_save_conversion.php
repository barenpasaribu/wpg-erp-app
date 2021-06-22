<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$jumlah = $_POST['jumlah'];
$kodebarang = $_POST['kodebarang'];
$dari = $_POST['dari'];
$ke = $_POST['ke'];
$method = $_POST['method'];
$keterangan = $_POST['keterangan'];

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.log_5stkonversi where kodebarang=\'' . $kodebarang . '\' ' . "\r\n\t\t\t" . '       and satuankonversi=\'' . $ke . '\'' . "\r\n\t\t\t\t" . '   and darisatuan=\'' . $dari . '\'';
	break;

case 'update':
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.log_5stkonversi(' . "\r\n\t\t\t" . '       kodebarang,satuankonversi,darisatuan,jumlah,keterangan)' . "\r\n\t\t\t" . 'values(\'' . $kodebarang . '\',\'' . $ke . '\',\'' . $dari . '\',' . $jumlah . ',\'' . $keterangan . '\')';
	break;
}

if (($strx)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str = ' select * from ' . $dbname . '.log_5stkonversi where kodebarang=\'' . $kodebarang . '\' order by jumlah';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->darisatuan . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->satuankonversi . '</td>' . "\r\n\t\t\t" . '  <td align=right>' . $bar->jumlah . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->keterangan . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delConversi(\'' . $bar->kodebarang . '\',\'' . $bar->darisatuan . '\',\'' . $bar->satuankonversi . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
