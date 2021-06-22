<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/fpdf.php';
$nodok = $_GET['notransaksi'];
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
		global $kodegudang;
		global $kegudang;
		global $catatan;
		$pt = '';
		$namapt = '';
		$alamatpt = '';
		$telp = '';
		$kodegudang = '';
		$status = 0;
		$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $_GET['notransaksi'] . '\'';
		$res = mysql_query($str);

		if ($bar = mysql_fetch_object($res)) {
			$kodept = $bar->kodept;
			$kodegudang = $bar->kodegudang;
			$userid = $bar->user;
			$posted = $bar->postedby;
			$status = $bar->post;
			$tanggal = $bar->tanggal;
			$kegudang = $bar->gudangx;
			$catatan = $bar->keterangan;

			if ($status == 0) {
				$status = 'Not Confirm';
			}
			else {
				$status = 'Confirmed';
			}

			$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodept . '\'';
			$res1 = mysql_query($str1);
			$res2=mysql_fetch_object($res1);
			$namapt = $res2->namaorganisasi;
			$alamatpt = $res2->alamat . ', ' . $res2->wilayahkota;
			$telp = $res2->telepon;
		}

		$path = 'images/SSP_logo.jpg';

		if($path!=''){
		$this->Image($path, 15, 5, 35, 20);
		}
		$this->SetFont('Arial', 'B', 8);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(55);
		$this->Cell(60, 5, $namapt, 0, 1, 'L');
		$this->SetX(55);
		$this->Cell(60, 5, $alamatpt, 0, 1, 'L');
		$this->SetX(55);
		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
		$this->SetFont('Arial', '', 10);
		$this->SetY(30);
		$this->Cell(190, 5, strtoupper($_SESSION['lang']['dokmutasi']), 0, 1, 'C');
		$this->SetFont('Arial', '', 6);
		$this->SetY(27);
		$this->SetX(163);
		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');
		$this->Line(10, 27, 200, 27);
		$this->SetY(35);
		$this->SetFont('Arial', '', 9);
		$this->Cell(30, 4, $_SESSION['lang']['sloc'], 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $kodegudang, 0, 1, 'L');
		$this->Cell(30, 4, 'Nomor Mutasi', 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $nodok, 0, 1, 'L');
		$this->Cell(30, 4, $_SESSION['lang']['docstatus'], 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $status, 0, 1, 'L');
		$tanggal = tanggalnormal($tanggal);
		$this->Cell(30, 4, $_SESSION['lang']['tanggal'], 0, 0, 'L');
		$this->Cell(40, 4, ': ' . $tanggal, 0, 1, 'L');
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
$hari = hari($tanggal, $_SESSION['language']);
$pdf->MultiCell(170, 5, $resc, 0, 'L');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(30, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
$pdf->Cell(85, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
$pdf->Cell(18, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
$pdf->Cell(20, 5, $_SESSION['lang']['kuantitas'], 1, 0, 'C', 1);
$pdf->Cell(25, 5, $_SESSION['lang']['tujuan'], 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $_GET['notransaksi'] . '\'';
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
	$pdf->Cell(85, 5, $namabarang, 1, 0, 'L', 1);
	$pdf->Cell(18, 5, $satuan, 1, 0, 'L', 1);
	$pdf->Cell(20, 5, number_format($jumlah, 2, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(25, 5, $kegudang, 1, 1, 'C', 1);
}

$pdf->MultiCell(170, 5, 'Note: ' . $catatan, 0, 'L');
$pdf->Ln();

if ($posted != '') {
	$posted = namakaryawan($dbname, $conn, $posted);
}

$brsAkhir = $pdf->GetY();
$pdf->SetY($brsAkhir + 10);
$pdf->Cell(10, 4, '', 0, 0, 'L');

if (substr($kodegudang,0,4)== 'SSPH' || substr($kodegudang,0,4)== 'LSPH' || substr($kodegudang,0,4)== 'TSPH' || substr($kodegudang,0,4)== 'SPSH'){
	$pdf->Cell(37, 4, 'Purchasing', 0, 0, 'C');
} else {
	$pdf->Cell(37, 4, 'Ka. Logistik', 0, 0, 'C');
}

$pdf->Cell(25, 4, '', 0, 0, 'L');
$pdf->Cell(37, 4, $_SESSION['lang']['petugasgudang'], 0, 0, 'C');
$pdf->Cell(25, 4, '', 0, 0, 'L');
$pdf->Cell(37, 4, 'Diterima oleh', 0, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Ln(20);
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Cell(37, 4, '', T, 0, 'C');
$pdf->Cell(25, 4, '', 0, 0, 'L');
$pdf->Cell(37, 4, '', T, 0, 'C');
$pdf->Cell(25, 4, '', 0, 0, 'L');
$pdf->Cell(37, 4, '', T, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Output();

?>
