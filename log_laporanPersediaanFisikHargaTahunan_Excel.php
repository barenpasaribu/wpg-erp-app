<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$stream = '';

if ($gudang == '') {
	$str = 'select a.kodebarang, b.satuan, b.namabarang from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '    left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '    where a.kodeorg=\'' . $pt . '\' ' . "\r\n" . '    and a.periode like \'' . $periode . '%\'' . "\r\n" . '    order by a.kodebarang';
}
else {
	$str = 'select a.kodebarang, b.satuan, b.namabarang from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '    left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '    where a.kodeorg=\'' . $pt . '\' and kodegudang = \'' . $gudang . '\'' . "\r\n" . '    and a.periode like \'' . $periode . '%\'' . "\r\n" . '    order by a.kodebarang';
}

#exit(mysql_error());
($res = mysql_query($str)) || true;

while ($bar = mysql_fetch_object($res)) {
	$arrBarang[$bar->kodebarang] = $bar->kodebarang;
	$kamussatuan[$bar->kodebarang] = $bar->satuan;
	$kamusnamabarang[$bar->kodebarang] = $bar->namabarang;
}

if ($gudang == '') {
	$str = 'select kodebarang, sum(saldoawalqty) as saldoawalqty , sum(nilaisaldoawal) as nilaisaldoawal from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '    where kodeorg=\'' . $pt . '\' ' . "\r\n" . '    and periode like \'' . $periode . '-01\'' . "\r\n" . '    group by kodebarang order by kodebarang';
}
else {
	$str = 'select kodebarang, saldoawalqty, hargaratasaldoawal, nilaisaldoawal from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '    where kodeorg=\'' . $pt . '\' and kodegudang = \'' . $gudang . '\'' . "\r\n" . '    and periode like \'' . $periode . '-01\'' . "\r\n" . '    order by kodebarang';
}

#exit(mysql_error());
($res = mysql_query($str)) || true;

while ($bar = mysql_fetch_object($res)) {
	$arrAwal[$bar->kodebarang]['saldoawalqty'] = $bar->saldoawalqty;
	@$arrAwal[$bar->kodebarang]['hargaratasaldoawal'] = $bar->nilaisaldoawal / $bar->saldoawalqty;
	$arrAwal[$bar->kodebarang]['nilaisaldoawal'] = $bar->nilaisaldoawal;
}

if ($gudang == '') {
	$str = 'select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga ' . "\r\n" . '    from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '    where kodeorg=\'' . $pt . '\' ' . "\r\n" . '    and periode like \'' . $periode . '%\'' . "\r\n" . '    group by kodebarang' . "\r\n" . '    order by kodebarang';
}
else {
	$str = 'select kodebarang, sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga, sum(qtykeluarxharga) as qtykeluarxharga ' . "\r\n" . '    from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . '    where kodeorg=\'' . $pt . '\' and kodegudang = \'' . $gudang . '\'' . "\r\n" . '    and periode like \'' . $periode . '%\'' . "\r\n" . '    group by kodebarang' . "\r\n" . '    order by kodebarang';
}

#exit(mysql_error());
($res = mysql_query($str)) || true;

while ($bar = mysql_fetch_object($res)) {
	$arrAwal[$bar->kodebarang]['qtymasuk'] = $bar->qtymasuk;
	$arrAwal[$bar->kodebarang]['qtykeluar'] = $bar->qtykeluar;
	$arrAwal[$bar->kodebarang]['qtymasukxharga'] = $bar->qtymasukxharga;
	$arrAwal[$bar->kodebarang]['qtykeluarxharga'] = $bar->qtykeluarxharga;
}

$stream .= $_SESSION['lang']['persediaanfisikharga'] . ': ' . $pt . ' ' . $gudang . ' : ' . $periode . '<br>    ' . "\r\n" . '        <table border=1>' . "\r\n" . '                <tr>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                </tr>';
$no = 0;

if (!empty($arrBarang)) {
	foreach ($arrBarang as $barang) {
		$no += 1;
		@$hargamasuk = $arrAwal[$barang]['qtymasukxharga'] / $arrAwal[$barang]['qtymasuk'];
		@$hargakeluar = $arrAwal[$barang]['qtykeluarxharga'] / $arrAwal[$barang]['qtykeluar'];
		@$salakqty = ($arrAwal[$barang]['saldoawalqty'] + $arrAwal[$barang]['qtymasuk']) - $arrAwal[$barang]['qtykeluar'];
		@$salakrp = ($arrAwal[$barang]['nilaisaldoawal'] + $arrAwal[$barang]['qtymasukxharga']) - $arrAwal[$barang]['qtykeluarxharga'];
		@$salakhar = $salakrp / $salakqty;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td>' . $no . '</td>' . "\r\n" . '        <td>' . $periode . '</td>' . "\r\n" . '        <td>\'' . $barang . '</td>' . "\r\n" . '        <td>' . $kamusnamabarang[$barang] . '</td>' . "\r\n" . '        <td>' . $kamussatuan[$barang] . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['saldoawalqty'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['hargaratasaldoawal'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['nilaisaldoawal'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['qtymasuk'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($hargamasuk, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['qtymasukxharga'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['qtykeluar'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($hargakeluar, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['qtykeluarxharga'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($salakqty, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($salakhar, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($salakrp, 2) . '</td>' . "\r\n" . '    </tr>';
	}
}

if (empty($arrBarang)) {
	echo 'No data.';
	exit();
}

$stream .= '</table>';
$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'MaterialBalanceWPrice';

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
