<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$user = $_SESSION['standard']['userid'];
$period = $_POST['periode'];
$pt = $_POST['pt'];
$gudang = $pt;
$kodebarang = $_POST['kodebarang'];
$awal = $_POST['awal'];
$akhir = $_POST['akhir'];
$str = 'select sum(saldoawalqty) as sawal,sum(nilaisaldoawal) as sawalrp ' . "\r\n\t\t" . '      from ' . $dbname . '.log_5saldobulanan' . "\r\n\t\t" . '      where kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t" . '  and kodebarang=\'' . $kodebarang . '\' and periode=\'' . $period . '\'';
$res = mysql_query($str);
$sawal = 0;
$nilaisaldoawal = 0;

while ($bar = mysql_fetch_object($res)) {
	$sawal = $bar->sawal;
	$nilaisaldoawal = $bar->sawalrp;
}

if ($sawal == '') {
	$sawal = 0;
}

if ($nilaisaldoawal == '') {
	$nilaisaldoawal = 0;
}

if (($sawal == 0) || ($nilaisaldoawal == 0)) {
	$haratsawal = 0;
}
else {
	$haratsawal = $nilaisaldoawal / $sawal;
}

$str = 'select sum(a.jumlah) as jumlah,sum(a.hargasatuan*a.jumlah) as hartot from ' . $dbname . '.log_transaksidt a' . "\r\n" . '       left join ' . $dbname . '.log_transaksiht b on' . "\r\n\t" . '   a.notransaksi=b.notransaksi' . "\r\n" . '      where b.kodegudang=\'' . $gudang . '\'' . "\r\n\t" . '  and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n\t" . '  and b.tanggal>=' . $awal . ' and b.tanggal<=' . $akhir . ' ' . "\r\n\t" . '  and b.tipetransaksi<5 and b.post=1';
$masuk = 0;
$hartotmasuk = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$masuk = $bar->jumlah;
	$hartotmasuk = $bar->hartot;
}

if ($masuk == '') {
	$masuk = 0;
}

if ($hartotmasuk == '') {
	$hartotmasuk = 0;
}

if ($masuk <= 0) {
	$haratmasuk = 0;
}
else {
	$haratmasuk = $hartotmasuk / $masuk;
}

if (($sawal + $masuk) <= 0) {
	$haratbaru = 0;
}
else {
	$haratbaru = ($hartotmasuk + $nilaisaldoawal) / ($sawal + $masuk);
}

if ($haratbaru == 0) {
	$haratbaru = $haratmasuk;
}

if ($haratbaru == 0) {
	$haratbaru = $haratsawal;
}

if ($haratbaru == 0) {
	$str = 'select hargarata from ' . $dbname . '.log_5saldobulanan where kodebarang=\'' . $kodebarang . '\' and hargarata>0' . "\r\n" . '          order by lastupdate desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$haratbaru = $bar->hargarata;
	}
}

if ($haratbaru == '') {
	$haratbaru = 1;
}

$str = 'update ' . $dbname . '.log_transaksidt set hargarata=' . $haratbaru . ' ' . "\r\n" . '        where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '         and notransaksi in(select notransaksi from ' . $dbname . '.log_transaksiht b' . "\r\n\t\t" . 'where b.kodegudang=\'' . $gudang . '\'  ' . "\r\n\t\t" . 'and b.tanggal>=' . $awal . ' and b.tanggal<=' . $akhir . "\r\n\t" . '    and b.post=1)';

if (mysql_query($str)) {
	$str = 'update ' . $dbname . '.log_5saldobulanan ' . "\r\n\t" . '      set hargarata=' . $haratbaru . ',' . "\r\n\t\t" . '  nilaisaldoakhir=saldoakhirqty*' . $haratbaru . ',' . "\r\n\t\t" . '  qtymasukxharga=qtymasuk*' . $haratmasuk . ',' . "\r\n\t\t" . '  qtykeluarxharga=qtykeluar*' . $haratbaru . ' ' . "\r\n\t" . '      where kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $gudang . '\' ' . "\r\n\t\t" . '  and periode=\'' . $period . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
