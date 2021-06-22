<?php


class PDF extends FPDF
{
	public function Header()
	{
		global $conn;
		global $dbname;
		global $userid;
		global $tgl;
		global $blok;
		global $namakar;
		global $tahuntanam;
		global $namaafd;
		global $namakebun;
		global $tipe;
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;
		$this->SetFont('Arial', 'BU', 12);
		$this->SetFillColor(255, 255, 255);
		$this->Cell(200, 5, 'CHAT', 0, 0, 'C');
		$this->Ln(10);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 10);
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$tmp = explode(',', $_GET['column']);
$nopp = $tmp[0];
$tgl = $tmp[1];
$kdBrg = $tmp[2];
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$namakar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$jumlahPkk = makeOption($dbname, 'setup_blok', 'kodeorg,jumlahpokok');
$luasAreal = makeOption($dbname, 'setup_blok', 'kodeorg,luasareaproduktif');
$pdf = new PDF('P', 'mm', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 5;
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(30, $height, $_SESSION['lang']['nopp'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $nopp, 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['tanggal'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, tanggalnormal($tgl), 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['kodebarang'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $kdBrg, 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['namabarang'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $nmBarang[$kdBrg], 0, 1, 'L', 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 8);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(30, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
$pdf->Cell(20, $height, $_SESSION['lang']['karyawanid'], 1, 0, 'C', 1);
$pdf->Cell(135, $height, $_SESSION['lang']['chat'], 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$w = 'select * from ' . $dbname . '.log_pp_chat where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kdBrg . '\' ';

#exit(mysql_error($conn));
($i = mysql_query($w)) || true;

while ($b = mysql_fetch_assoc($i)) {
	$pdf->Cell(30, $height, $b['tanggal'], 1, 0, 'R', 1);
	$pdf->Cell(20, $height, $namakar[$b['karyawanid']], 1, 0, 'L', 1);
	$pdf->Cell(135, $height, $b['pesan'], 1, 1, 'L', 1);
}

$pdf->Ln(5);
$pdf->Output();

?>
