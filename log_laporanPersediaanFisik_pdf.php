<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
$namapt = 'COMPANY NAME';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namapt = strtoupper($bar->namaorganisasi);
}

if (($periode == '') && ($gudang == '')) {
	$str = 'select a.kodebarang,sum(a.saldoqty) as kuan, ' . "\r\n\t" . '      b.namabarang,b.satuan,a.kodeorg from ' . $dbname . '.log_5masterbarangdt a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t" . '  where kodeorg=\'' . $pt . '\' group by a.kodeorg,a.kodebarang order by kodebarang';
}
else if (($periode == '') && ($gudang != '')) {
	$str = 'select a.kodebarang,sum(a.saldoqty) as kuan, ' . "\r\n\t" . '      b.namabarang,b.satuan from ' . $dbname . '.log_5masterbarangdt a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t" . '  and kodegudang=\'' . $gudang . '\'' . "\r\n\t\t" . '  group by a.kodeorg,a.kodebarang  order by kodebarang';
}
else if ($gudang == '') {
	$str = 'select ' . "\r\n\t\t\t" . '  a.kodeorg,' . "\r\n\t\t\t" . '  a.kodebarang,' . "\r\n\t\t\t" . '  sum(a.saldoakhirqty) as salakqty,' . "\r\n\t\t\t" . '  sum(a.qtymasuk) as masukqty,' . "\r\n\t\t\t" . '  sum(a.qtykeluar) as keluarqty,' . "\r\n\t\t\t" . '  sum(a.saldoawalqty) as sawalqty,' . "\r\n\t\t" . '      b.namabarang,b.satuan   ' . "\r\n\t\t" . '      from ' . $dbname . '.log_5saldobulanan a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t\t" . '  and periode=\'' . $periode . '\'' . "\r\n\t\t\t" . '  group by a.kodebarang order by a.kodebarang';
}
else {
	$str = 'select' . "\r\n\t\t\t" . '  a.kodeorg,' . "\r\n\t\t\t" . '  a.kodebarang,' . "\r\n\t\t\t" . '  sum(a.saldoakhirqty) as salakqty,' . "\r\n\t\t\t" . '  sum(a.qtymasuk) as masukqty,' . "\r\n\t\t\t" . '  sum(a.qtykeluar) as keluarqty,' . "\r\n\t\t\t" . '  sum(a.saldoawalqty) as sawalqty,' . "\r\n\t\t" . '      b.namabarang,b.satuan  ' . "\t\t" . ' ' . "\t\t" . '      ' . "\r\n\t\t\t" . '  from ' . $dbname . '.log_5saldobulanan a' . "\r\n\t\t" . '      left join ' . $dbname . '.log_5masterbarang b' . "\r\n\t\t\t" . '  on a.kodebarang=b.kodebarang' . "\r\n\t\t\t" . '  where kodeorg=\'' . $pt . '\' ' . "\r\n\t\t\t" . '  and periode=\'' . $periode . '\'' . "\r\n\t\t\t" . '  and kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t" . '  group by a.kodebarang order by a.kodebarang';
}
class PDF extends FPDF
{
	public function Header()
	{
		global $namapt;
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 5, $namapt, '', 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(190, 5, strtoupper($_SESSION['lang']['laporanstok']), 0, 1, 'C');
		$this->SetFont('Arial', '', 8);
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, 'User', '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
		$this->Ln();
		$this->SetFont('Arial', '', 6);
		$this->Cell(5, 5, 'No.', 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['pt'], 1, 0, 'C');
		$this->Cell(20, 5, $_SESSION['lang']['sloc'], 1, 0, 'C');
		$this->Cell(17, 5, $_SESSION['lang']['periode'], 1, 0, 'C');
		$this->Cell(18, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C');
		$this->Cell(45, 5, substr($_SESSION['lang']['namabarang'], 0, 30), 1, 0, 'C');
		$this->Cell(5, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');
		$this->Cell(20, 5, $_SESSION['lang']['saldoawal'], 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['masuk'], 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['keluar'], 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['saldo'], 1, 0, 'C');
		$this->Ln();
	}
}


if ($periode == '') {
	$sawalQTY = '';
	$masukQTY = '';
	$keluarQTY = '';
	$kuantitas = 0;
	$res = mysql_query($str);
	$no = 0;

	if (mysql_num_rows($res) < 1) {
		echo $_SESSION['lang']['tidakditemukan'];
	}
	else {
		$pdf = new PDF('P', 'mm', 'A4');
		$pdf->AddPage();

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$periode = date('d-m-Y H:i:s');
			$kodebarang = $bar->kodebarang;
			$namabarang = $bar->namabarang;
			$kuantitas = $bar->kuan;
			$pdf->Cell(5, 4, $no, 0, 0, 'C');
			$pdf->Cell(15, 4, $pt, 0, 0, 'L');
			$pdf->Cell(20, 4, $gudang, 0, 0, 'L');
			$pdf->Cell(17, 4, substr($periode, 0, 16), 0, 0, 'C');
			$pdf->Cell(18, 4, $kodebarang, 0, 0, 'L');
			$pdf->Cell(45, 4, substr($namabarang, 0, 30), 0, 0, 'L');
			$pdf->Cell(7, 4, $bar->satuan, 0, 0, 'L');
			$pdf->Cell(18, 4, $sawalQTY, 0, 0, 'R');
			$pdf->Cell(15, 4, $masukQTY, 0, 0, 'R');
			$pdf->Cell(15, 4, $keluarQTY, 0, 0, 'R');
			$pdf->Cell(15, 4, number_format($kuantitas, 2, '.', ','), 0, 1, 'R');
		}

		$pdf->Output();
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
		echo $_SESSION['lang']['tidakditemukan'];
	}
	else {
		$pdf = new PDF('P', 'mm', 'A4');
		$pdf->AddPage();

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$kodebarang = $bar->kodebarang;
			$namabarang = $bar->namabarang;
			$salakqty = $bar->salakqty;
			$masukqty = $bar->masukqty;
			$keluarqty = $bar->keluarqty;
			$sawalQTY = $bar->sawalqty;
			$pdf->Cell(5, 4, $no, 0, 0, 'C');
			$pdf->Cell(15, 4, $pt, 0, 0, 'L');
			$pdf->Cell(20, 4, $gudang, 0, 0, 'L');
			$pdf->Cell(17, 4, $periode, 0, 0, 'C');
			$pdf->Cell(18, 4, $kodebarang, 0, 0, 'L');
			$pdf->Cell(45, 4, substr($namabarang, 0, 30), 0, 0, 'L');
			$pdf->Cell(7, 4, $bar->satuan, 0, 0, 'L');
			$pdf->Cell(18, 4, $sawalQTY, 0, 0, 'R');
			$pdf->Cell(15, 4, $masukqty, 0, 0, 'R');
			$pdf->Cell(15, 4, $keluarqty, 0, 0, 'R');
			$pdf->Cell(15, 4, number_format($salakqty, 2, '.', ','), 0, 1, 'R');
		}

		$pdf->Output();
	}
}

?>
