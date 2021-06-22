<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];

if (($periode == '') && ($gudang == '')) {
	$str = 'select a.kodebarang,sum(a.saldoqty) as kuan, ' . "\r\n\t" . '      b.namabarang,b.satuan,a.kodeorg from ' . $dbname . '.log_5masterbarangdt a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t" . '  where kodeorg=\'' . $pt . '\' group by a.kodeorg,a.kodebarang order by kodebarang';
}
else if (($periode == '') && ($gudang != '')) {
	$str = 'select a.kodebarang,sum(a.saldoqty) as kuan, ' . "\r\n\t" . '      b.namabarang,b.satuan from ' . $dbname . '.log_5masterbarangdt a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t" . '  and kodegudang=\'' . $gudang . '\'' . "\r\n\t\t" . '  group by a.kodeorg,a.kodebarang  order by kodebarang';
}
else if ($gudang == '') {
	$str = 'select ' . "\r\n\t\t\t" . '  a.kodeorg,' . "\r\n\t\t\t" . '  a.kodebarang,' . "\r\n\t\t\t" . '  sum(a.saldoakhirqty) as salakqty,' . "\r\n\t\t\t" . '  sum(a.qtymasuk) as masukqty,' . "\r\n\t\t\t" . '  sum(a.qtykeluar) as keluarqty,' . "\r\n\t\t\t" . '  sum(a.saldoawalqty) as sawalqty,' . "\r\n\t\t" . '      b.namabarang,b.satuan    ' . "\r\n\t\t" . '      from ' . $dbname . '.log_5saldobulanan a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t\t" . '  and periode=\'' . $periode . '\'' . "\r\n\t\t\t" . '  group by a.kodebarang order by a.kodebarang';
}
else {
	$str = 'select' . "\r\n\t\t\t" . '  a.kodeorg,' . "\r\n\t\t\t" . '  a.kodebarang,' . "\r\n\t\t\t" . '  sum(a.saldoakhirqty) as salakqty,' . "\r\n\t\t\t" . '  sum(a.qtymasuk) as masukqty,' . "\r\n\t\t\t" . '  sum(a.qtykeluar) as keluarqty,' . "\r\n\t\t\t" . '  sum(a.saldoawalqty) as sawalqty,' . "\r\n\t\t" . '      b.namabarang,b.satuan  ' . "\t\t" . ' ' . "\t\t" . '      ' . "\r\n\t\t\t" . '  from ' . $dbname . '.log_5saldobulanan a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t\t" . '  and periode=\'' . $periode . '\'' . "\r\n\t\t\t" . '  and kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t" . '  group by a.kodebarang order by a.kodebarang';
}

if ($periode == '') {
	$sawalQTY = '';
	$masukQTY = '';
	$keluarQTY = '';
	$kuantitas = 0;
	$res = mysql_query($str);
	$no = 0;

	if (mysql_num_rows($res) < 1) {
		echo '<tr class=rowcontent><td colspan=11>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';

		if ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$periode = date('Y-m-d H:i:s');
			$kodebarang = $bar->kodebarang;
			$namabarang = $bar->namabarang;
			$kuantitas = $bar->kuan;
			echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarang(event,\'' . $pt . '\',\'' . $periode . '\',\'' . $gudang . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $pt . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $gudang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $periode . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $sawalQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $masukQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $keluarQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right class=firsttd>' . number_format($kuantitas, 2, '.', ',') . '</td>' . "\t\t" . '   ' . "\r\n\t\t\t\t" . '</tr>';
		}
	}
	else {
		echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarang(event,\'' . $pt . '\',\'' . $periode . '\',\'' . $gudang . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $pt . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $gudang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $periode . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $sawalQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $masukQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right>' . $keluarQTY . '</td>' . "\r\n\t\t\t\t" . '   <td align=right class=firsttd>' . number_format($kuantitas, 2, '.', ',') . '</td>' . "\t\t" . '   ' . "\r\n\t\t\t\t" . '</tr>';
	}
}
else {
	$salakqty = 0;
	$masukqty = 0;
	$keluarqty = 0;
	$sawalQTY = 0;
	$res = mysql_query($str);
	$no = 0;

	if (mysql_num_rows($res) < 1) {
		echo '<tr class=rowcontent><td colspan=11>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';

		$no += 1;
		$kodebarang = $bar->kodebarang;
		$namabarang = $bar->namabarang;
		$salakqty = $bar->salakqty;
		$masukqty = $bar->masukqty;
		$keluarqty = $bar->keluarqty;
		$sawalQTY = $bar->sawalqty;
		echo '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarang(event,\'' . $pt . '\',\'' . $periode . '\',\'' . $gudang . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t" . '  <td>' . $pt . '</td>' . "\r\n\t\t\t" . '  <td>' . $gudang . '</td>' . "\r\n\t\t\t" . '  <td>' . $periode . '</td>' . "\r\n\t\t\t" . '  <td>' . $kodebarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $namabarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($sawalQTY, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($masukqty, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($keluarqty, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($salakqty, 2, '.', ',') . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '</tr>';
	}
	else {
		echo '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarang(event,\'' . $pt . '\',\'' . $periode . '\',\'' . $gudang . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t" . '  <td>' . $pt . '</td>' . "\r\n\t\t\t" . '  <td>' . $gudang . '</td>' . "\r\n\t\t\t" . '  <td>' . $periode . '</td>' . "\r\n\t\t\t" . '  <td>' . $kodebarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $namabarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($sawalQTY, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($masukqty, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($keluarqty, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '   <td align=right class=firsttd>' . number_format($salakqty, 2, '.', ',') . '</td>' . "\t" . '   ' . "\r\n\t\t\t" . '</tr>';
	}
}

?>
