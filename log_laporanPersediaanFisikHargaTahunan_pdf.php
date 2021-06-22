<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
$namapt = 'COMPANY NAME';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namapt = strtoupper($bar->namaorganisasi);
}

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
class PDF extends FPDF
{
	public function Header()
	{
		global $namapt;
		global $pt;
		global $periode;
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 5, $namapt, '', 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(190, 5, strtoupper($_SESSION['lang']['persediaanfisikharga']) . ' ' . $periode, 0, 1, 'C');
		$this->SetFont('Arial', '', 8);
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
		$this->Cell(140, 5, 'UNIT:' . $pt . '-' . $gudang, '', 0, 'L');
		$this->Cell(15, 5, 'User', '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
		$this->SetFont('Arial', '', 4);
		$this->Cell(5, 8, 'No.', 1, 0, 'C');
		$this->Cell(15, 8, $_SESSION['lang']['periode'], 1, 0, 'C');
		$this->Cell(15, 8, $_SESSION['lang']['kodebarang'], 1, 0, 'C');
		$this->Cell(40, 8, substr($_SESSION['lang']['namabarang'], 0, 30), 1, 0, 'C');
		$this->Cell(5, 8, $_SESSION['lang']['satuan'], 1, 0, 'C');
		$this->Cell(27, 4, $_SESSION['lang']['saldoawal'], 1, 0, 'C');
		$this->Cell(27, 4, $_SESSION['lang']['masuk'], 1, 0, 'C');
		$this->Cell(27, 4, $_SESSION['lang']['keluar'], 1, 0, 'C');
		$this->Cell(27, 4, $_SESSION['lang']['saldo'], 1, 1, 'C');
		$this->SetX(90);
		$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['kuantitas'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
		$this->Cell(9, 4, $_SESSION['lang']['totalharga'], 1, 1, 'C');
	}
}


$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$no = 0;

if (empty($arrBarang)) {
	echo 'No data.';
	exit();
}
else {
	foreach ($arrBarang as $barang) {
		$no += 1;
		@$hargamasuk = $arrAwal[$barang]['qtymasukxharga'] / $arrAwal[$barang]['qtymasuk'];
		@$hargakeluar = $arrAwal[$barang]['qtykeluarxharga'] / $arrAwal[$barang]['qtykeluar'];
		@$salakqty = ($arrAwal[$barang]['saldoawalqty'] + $arrAwal[$barang]['qtymasuk']) - $arrAwal[$barang]['qtykeluar'];
		@$salakrp = ($arrAwal[$barang]['nilaisaldoawal'] + $arrAwal[$barang]['qtymasukxharga']) - $arrAwal[$barang]['qtykeluarxharga'];
		@$salakhar = $salakrp / $salakqty;
		$pdf->Cell(5, 4, $no, 0, 0, 'C');
		$pdf->Cell(15, 4, $periode, 0, 0, 'C');
		$pdf->Cell(15, 4, $barang, 0, 0, 'L');
		$pdf->Cell(40, 4, $kamusnamabarang[$barang], 0, 0, 'L');
		$pdf->Cell(5, 4, $kamussatuan[$barang], 0, 0, 'L');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['saldoawalqty'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['hargaratasaldoawal'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['nilaisaldoawal'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['qtymasuk'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($hargamasuk, 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['qtymasukxharga'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['qtykeluar'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($hargakeluar, 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($arrAwal[$barang]['qtykeluarxharga'], 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($salakqty, 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($salakhar, 2), 0, 0, 'R');
		$pdf->Cell(9, 4, number_format($salakrp, 2), 0, 1, 'R');
	}
}

$pdf->Output();

?>
