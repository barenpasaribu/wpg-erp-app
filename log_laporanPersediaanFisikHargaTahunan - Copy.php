<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];

if ($periode == '') {
	echo 'Error: Please choose Periode.';
	exit();
}

$arrBarang = array();
$arrAwal = array();
$kamussatuan = array();
$kamusnamabarang = array();

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
	$str = "select kodebarang, sum(saldoawalqty) as saldoawalqty , sum(nilaisaldoawal) as nilaisaldoawal ".
		"from $dbname.log_5saldobulanan ".
		"where kodeorg='". $pt ."' ".
		"and periode =  '" . $periode . "' group by kodebarang order by kodebarang";
}
else {
	$str = 'select kodebarang, saldoawalqty, hargaratasaldoawal, nilaisaldoawal '.
		'from ' . $dbname . '.log_5saldobulanan' . "\r\n" .
		'    where kodeorg=\'' . $pt . '\' and kodegudang = \'' . $gudang . '\'' . "\r\n" .
		'    and periode like \'' . $periode . '-01\'' . "\r\n" . '    order by kodebarang';
}

echoMessage("str ",$str);

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

echo '<table>';
$no = 0;

if (!empty($arrBarang)) {
	foreach ($arrBarang as $barang) {
		$no += 1;
		$hargamasuk = 0;
		$hargakeluar = 0;
		@$hargamasuk = $arrAwal[$barang]['qtymasukxharga'] / $arrAwal[$barang]['qtymasuk'];
		@$hargakeluar = $arrAwal[$barang]['qtykeluarxharga'] / $arrAwal[$barang]['qtykeluar'];
		@$salakqty = ($arrAwal[$barang]['saldoawalqty'] + $arrAwal[$barang]['qtymasuk']) - $arrAwal[$barang]['qtykeluar'];
		@$salakrp = ($arrAwal[$barang]['nilaisaldoawal'] + $arrAwal[$barang]['qtymasukxharga']) - $arrAwal[$barang]['qtykeluarxharga'];
		@$salakhar = $salakrp / $salakqty;
		echo '<tr class=rowcontent>' . "\r\n" . '        <td>' . $no . '</td>' . "\r\n" . '        <td>' . $periode . '</td>' . "\r\n" . '        <td>' . $barang . '</td>' . "\r\n" . '        <td>' . $kamusnamabarang[$barang] . '</td>' . "\r\n" . '        <td>' . $kamussatuan[$barang] . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['saldoawalqty'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['hargaratasaldoawal'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['nilaisaldoawal'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['qtymasuk'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($hargamasuk, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['qtymasukxharga'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($arrAwal[$barang]['qtykeluar'], 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($hargakeluar, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($arrAwal[$barang]['qtykeluarxharga'], 2) . '</td>' . "\r\n" . '        <td align=right class=firsttd>' . number_format($salakqty, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($salakhar, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($salakrp, 2) . '</td>' . "\r\n" . '    </tr>';
	}
}

if (empty($arrBarang)) {
	echo '<tr class=rowcontent>' . "\r\n" . '        <td colspan=17>no data.</td>' . "\r\n" . '    </tr>';
}

echo '</table>';

?>
