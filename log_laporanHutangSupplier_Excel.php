<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
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
	echo '<tr class=rowcontent><td colspan=11>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';
}
else {
	$stream .= $_SESSION['lang']['hutangsupplierbpb'] . ': ' . $gudang . ' : ' . $periode . '<br>' . "\r\n\t\t" . '<table border=1>' . "\r\n\t\t\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>No.</td><td bgcolor=#DEDEDE align=center>No. Transaksi</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodesupplier'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n\t\t\t\t\t" . '</tr>';

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$total = 0;
		$total = $bar->jumlah * $bar->hargasatuan;
		$stream .= '<tr>' . "\r\n\t\t\t\t" . '  <td align=right>' . $no . '</td> <td>' . $bar->notransaksi . '</td> <td>' . $bar->tanggal . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $barang[$bar->kodebarang] . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($bar->jumlah) . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . $bar->idsupplier . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namasupplier . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($bar->hargasatuan) . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($total) . '</td>' . "\r\n\t\t\t" . '</tr>';
		$gtotal += $total;
	}

	$stream .= '<tr class=rowheader>' . "\r\n\t\t\t\t" . '  <td colspan=10 align=right>TOTAL</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($gtotal) . '</td>' . "\r\n\t\t\t\t" . '</tr>';
	$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
}

$nop_ = 'HutangSupplier';

if (0 < strlen($stream)) {
	if ($handle = opendir('tempExcel')) {
		while (false !== $file = readdir($handle)) {
			if (($file != '.') && ($file != '..')) {
				@unlink('tempExcel/' . $file);
			}
		}

		closedir($handle);
	}

	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

	if (!fwrite($handle, $stream)) {
		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
		exit();
	}
	else {
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
	}

	closedir($handle);
}

?>
