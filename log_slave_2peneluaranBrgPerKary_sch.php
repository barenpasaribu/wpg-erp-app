<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
	$schBarang = $_POST['schBarang'];
	$kdorg = $_POST['kdorg'];
}
else {
	$proses = $_GET['proses'];
	$schBarang = $_GET['schBarang'];
	$kdorg = $_GET['kdorg'];
}

//$proses = $_POST['proses'];
//$schBarang = $_POST['schBarang'];
//$kdorg = $_POST['kdorg'];
//$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
//$nikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');

switch ($proses) {
case 'getKar':
	$optKar = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
//	$j = 'select distinct(namapenerima) as namapenerima from ' . $dbname . '.log_transaksi_vw where untukunit=\'' . $kdorg . '\' ';
	$j = "select distinct karyawanid, nik,namakaryawan from log_transaksi_vw a inner join $dbname.datakaryawan b on a.namapenerima=b.karyawanid ".
		"where lokasitugas ='".$kdorg."' ".
		"order by namakaryawan";
	echoMessage(" str ",$j);
	#exit(mysql_error($conn));
	($k = mysql_query($j)) || true;

	while ($l = mysql_fetch_assoc($k)) {
		$optKar .= '<option value=\'' . $l['karyawanid'] . '\'>' .  $l['nik'] . ' - ' . $l['namakaryawan'] . '</option>';
	}

	echo $optKar;
	break;

case 'goCariBarang':
	echo "\r\n\t\t" . '<table cellspacing=1 border=0 class=data>' . "\r\n\t\t" . '<thead>' . "\r\n\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t" . '<td>No</td>' . "\r\n\t\t\t\t" . '<td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '</tbody>';
	$i = 'select * from ' . $dbname . '.log_5masterbarang where kodebarang like \'%' . $schBarang . '%\' or namabarang like \'%' . $schBarang . '%\'';

	#exit(mysql_error($conn));
	($n = mysql_query($i)) || true;

	while ($d = mysql_fetch_assoc($n)) {
		$no += 1;
		echo "\r\n\t\t" . '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' onclick="goPickBarang(\'' . $d['kodebarang'] . '\',\'' . $d['namabarang'] . '\');">' . "\r\n\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $d['kodebarang'] . '</td>' . "\r\n\t\t\t" . '<td>' . $d['namabarang'] . '</td>' . "\r\n\t\t" . '</tr>';
	}

	break;
}

?>
