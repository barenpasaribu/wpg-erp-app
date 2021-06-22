<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$str = 'select distinct tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      where kodeorg = \'' . $gudang . '\' and periode = \'' . $periode . '\'';
$res = mysql_query($str);

if ($periode == '') {
	echo 'Warning: silakan mengisi periode';
	exit();
}

while ($bar = mysql_fetch_object($res)) {
	$tanggalmulai = $bar->tanggalmulai;
	$tanggalsampai = $bar->tanggalsampai;
}

$str = 'select distinct kodebarang, namabarang from ' . $dbname . '.log_5masterbarang';
$res = mysql_query($str);
$optper = '';

while ($bar = mysql_fetch_object($res)) {
	$barang[$bar->kodebarang] = $bar->namabarang;
}

if ($periode == '') {
	$str = 'select a.notransaksi, a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan ' . "\r\n\t\t" . '  from ' . $dbname . '.log_transaksi_vw a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5supplier b on a.idsupplier=b.supplierid' . "\r\n\t\t" . '  where a.kodegudang=\'' . $gudang . '\' and a.tipetransaksi=1 ' . "\r\n\t\t" . '  order by a.tanggal';
}
else {
	$str = 'select a.notransaksi, a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan ' . "\r\n\t\t" . '  from ' . $dbname . '.log_transaksi_vw a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5supplier b on a.idsupplier=b.supplierid' . "\r\n\t\t" . '  where a.kodegudang=\'' . $gudang . '\' and a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and a.tipetransaksi=1 ' . "\r\n\t\t" . '  order by a.tanggal';
}

$res = mysql_query($str);
$no = 0;

if (mysql_num_rows($res) < 1) {
	echo '<tr class=rowcontent><td colspan=17>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';
}
else {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$total = 0;
		$total = $bar->jumlah * $bar->hargasatuan;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '  <td align=right>' . $no . '</td><td>' . $bar->notransaksi . '</td><td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $barang[$bar->kodebarang] . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($bar->jumlah) . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . $bar->idsupplier . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namasupplier . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($bar->hargasatuan) . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($total) . '</td>' . "\r\n\t\t\t\t" . '</tr>';
		$gtotal += $total;
	}

	echo '<tr class=rowheader>' . "\r\n\t\t\t\t" . '  <td colspan=10 align=right>TOTAL</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($gtotal) . '</td>' . "\r\n\t\t\t\t" . '</tr>';
}

?>
