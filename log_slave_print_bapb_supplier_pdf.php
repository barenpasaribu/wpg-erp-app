<?php


class PDF extends FPDF
{
	public function Header()
	{
		global $conn;
		global $dbname;
		global $nodok;
		global $userid;
		global $posted;
		global $tanggal;
		global $idsupplier;
		global $pengetahui;
		global $penerima;
		global $kodegudang;
		global $nopo;
		global $namasupplier;
		$pt = '';
		$namapt = '';
		$alamatpt = '';
		$telp = '';
		$kodegudang = '';
		$status = 0;
		$str = 'select * from ' . $dbname . '.log_lpbht where notransaksi=\'' . $_GET['notransaksi'] . '\'';
		$res = mysql_query($str);

		if ($bar = mysql_fetch_object($res)) {
			$kodept = $bar->kodept;
			$kodegudang = $bar->kodegudang;
			$userid = $bar->user;
			$penerima = $bar->namapenerima;
			$pengetahui = $bar->mengetahui;
			$posted = $bar->postedby;
			$status = $bar->tipetransaksi;
			$tanggal = $bar->tanggal;
			$idsupplier = $bar->idsupplier;
			$nosj = $bar->nosj;
			$nofaktur = $bar->nofaktur;
			$nopo = $bar->nopo;

			if ($status == 0) {
				$status = 'Diterima';
			}
			else {
				$status = 'Dikirim';
			}

			$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodept . '\'';
			$res1 = mysql_query($str1);

			$namapt = $bar1->namaorganisasi;
			$alamatpt = $bar1->alamat . ', ' . $bar1->wilayahkota;
			$telp = $bar1->telepon;
		}

		if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
			$path = 'images/SSP_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
			$path = 'images/MI_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
			$path = 'images/HS_logo.jpg';
		}
		else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
			$path = 'images/BM_logo.jpg';
		}

		$this->Image($path, 15, 5, 35, 20);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(55);
		$this->Cell(60, 5, $namapt, 0, 1, 'L');
		$this->SetX(55);
		$this->Cell(60, 5, $alamatpt, 0, 1, 'L');
		$this->SetX(55);
		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
		$this->SetFont('Arial', '', 15);
		$this->SetY(30);
		$this->Cell(190, 5, strtoupper($_SESSION['lang']['bapb']), 0, 1, 'C');
		$this->SetFont('Arial', '', 6);
		$this->SetY(27);
		$this->SetX(163);
		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');
		$this->Line(10, 27, 200, 27);
		$this->SetFont('Arial', '', 9);
		$this->Cell(30, 4, $_SESSION['lang']['docnum'], 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $nodok, 0, 1, 'L');
		$this->Cell(30, 4, $_SESSION['lang']['docstatus'], 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $status, 0, 1, 'L');
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/fpdf.php';
$nodok = $_GET['notransaksi'];
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$stry = 'select distinct namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $idsupplier . '\'';
$namasupplier = $idsupplier;
$resy = mysql_query($stry);

while ($bary = mysql_fetch_object($resy)) {
	$namasupplier = $bary->namasupplier;
}

$hari = hari($tanggal, $_SESSION['language']);
$tanggal = tanggalnormal($tanggal);
$resc = str_replace('#DATE_REPARAM#', $hari . ', ' . $tanggal, $_SESSION['lang']['prebapb']);
$resc = str_replace('#SLOC_PARAM#', $kodegudang, $resc);
$resc = str_replace('#VENDOR_PARAM#', $namasupplier, $resc);
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(170, 5, $resc, 0, 'L');
$pdf->Cell(30, 4, $_SESSION['lang']['nopo'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nopo, 0, 1, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(30, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
$pdf->Cell(100, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
$pdf->Cell(20, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
$pdf->Cell(20, 5, $_SESSION['lang']['kuantitas'], 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$str = 'select * from ' . $dbname . '.log_lpbdt where notransaksi=\'' . $_GET['notransaksi'] . '\'';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$kodebarang = $bar->kodebarang;
	$satuan = $bar->satuan;
	$jumlah = $bar->jumlah;
	$namabarang = '';
	$strv = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';
	$resv = mysql_query($strv);

	while ($barv = mysql_fetch_object($resv)) {
		$namabarang = $barv->namabarang;
	}

	$pdf->Cell(8, 5, $no, 1, 0, 'L', 1);
	$pdf->Cell(30, 5, $kodebarang, 1, 0, 'L', 1);
	$pdf->Cell(100, 5, $namabarang, 1, 0, 'L', 1);
	$pdf->Cell(20, 5, $satuan, 1, 0, 'L', 1);
	$pdf->Cell(20, 5, number_format($jumlah, 2, '.', ','), 1, 1, 'R', 1);
}

$dtpenerima = namakaryawan($dbname, $conn, $penerima);
$dtpengetahui = namakaryawan($dbname, $conn, $pengetahui);
$pdf->Ln();
$pdf->Cell(52, 10, $_SESSION['lang']['diperiksa'] . ',', 0, 0, 'C', 0);
$pdf->Cell(65, 10, ' ', 0, 0, 'C', 0);
$pdf->Cell(52, 10, $_SESSION['lang']['diterima'] . ',', 0, 1, 'C', 0);
$pdf->ln(10);
$pdf->Cell(52, 10, $dtpengetahui, 0, 0, 'C', 0);
$pdf->Cell(65, 10, ' ', 0, 0, 'C', 0);
$pdf->Cell(52, 10, $dtpenerima, 0, 1, 'C', 0);
$pdf->Output();

?>
