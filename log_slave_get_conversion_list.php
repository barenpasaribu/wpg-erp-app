<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kelompok = $_POST['kelompok'];
$kode = $_POST['kode'];
$satuan = $_POST['satuan'];
$str = ' select * from ' . $dbname . '.log_5stkonversi where kodebarang=\'' . $kode . '\' order by jumlah';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->darisatuan . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->satuankonversi . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->jumlah . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->keterangan . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delConversi(\'' . $bar->kodebarang . '\',\'' . $bar->darisatuan . '\',\'' . $bar->satuankonversi . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
