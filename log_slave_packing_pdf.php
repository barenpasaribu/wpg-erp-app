<?php


class PDF extends FPDF
{
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
$notran = $tmp[0];
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
$i = 'select * from ' . $dbname . '.log_packinght where notransaksi=\'' . $notran . '\' ';

#exit(mysql_error($conn));
($n = mysql_query($i)) || true;
$d = mysql_fetch_assoc($n);
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(200, 5, $nmOrg[$d['kodept']], 0, 1, 'C');
$pdf->Cell(200, 5, 'PACKING LIST', 0, 1, 'C');
$pdf->ln();
$thn = substr($d['tanggal'], 0, 4);
$bln = numToMonth(substr($d['tanggal'], 5, 2), 'I', 'long');
$hari = substr($d['tanggal'], 8, 2);
$isiTgl = $hari . ' ' . $bln . ' ' . $thn;
$pdf->SetFont('Arial', '', 7);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetX(125);
$pdf->Cell(10, $height, 'PL NO', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(20, $height, $d['notransaksi'], 0, 1, 'L');
$pdf->Cell(20, $height, 'NO. PETI / KOLI', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(20, $height, $d['keterangan'], 0, 0, 'L');
$pdf->SetX(125);
$pdf->Cell(10, $height, 'DATE', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(20, $height, $isiTgl, 0, 1, 'L');
$yAkhir = $pdf->GetY();
$pdf->Line(10, $yAkhir - 0.5, 205, $yAkhir - 0.5);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(5, $height * 2, 'NO', TB, 0, 'C', 1);
$pdf->Cell(20, $height * 2, strtoupper($_SESSION['lang']['kodebarang']), TB, 0, 'C', 1);
$pdf->Cell(50, $height * 2, 'MATERIAL NAME & STESIFIKASI', TB, 0, 'C', 1);
$pdf->Cell(15, $height * 2, strtoupper($_SESSION['lang']['unit']), TB, 0, 'C', 1);
$pdf->Cell(30, $height, strtoupper($_SESSION['lang']['jumlah']), T, 0, 'C', 1);
$pdf->Cell(35, $height * 2, 'NOPO', TB, 0, 'C', 1);
$pdf->Cell(40, $height * 2, strtoupper($_SESSION['lang']['nopp']), TB, 1, 'C', 1);
$pdf->Ln(-$height);
$pdf->SetX(100);
$pdf->Cell(15, $height, strtoupper($_SESSION['lang']['kirim']), B, 0, 'C', 1);
$pdf->Cell(15, $height, strtoupper($_SESSION['lang']['diterima']), B, 1, 'C', 1);
$yAkhir = $pdf->GetY();
$pdf->Line(10, $yAkhir + 0.5, 205, $yAkhir + 0.5);
$pdf->Ln(3);
$pdf->SetFillColor(255, 255, 255);
$x = 'select * from ' . $dbname . '.log_packingdt where notransaksi=\'' . $notran . '\' ';

#exit(mysql_error($conn));
($y = mysql_query($x)) || true;

while ($z = mysql_fetch_assoc($y)) {
	$no += 1;
	$pdf->Cell(5, $height, $no, 0, 0, 'C');
	$pdf->Cell(20, $height, $z['kodebarang'], 0, 0, 'L');
	$pdf->Cell(50, $height, $nmBarang[$z['kodebarang']], 0, 0, 'L');
	$pdf->Cell(15, $height, $z['satuanpo'], 0, 0, 'C');
	$pdf->Cell(15, $height, $z['jumlah'], 1, 0, 'R');
	$pdf->Cell(15, $height, '', 1, 0, 'C');
	$pdf->Cell(35, $height, $z['nopo'], 0, 0, 'C');
	$pdf->Cell(40, $height, $z['nopp'], 0, 1, 'C');
}

$pdf->Ln(3);
$yAkhir = $pdf->GetY();
$pdf->Line(10, $yAkhir, 205, $yAkhir);
$pdf->Line(10, $yAkhir + 0.5, 205, $yAkhir + 0.5);
$pdf->ln();
$pdf->SetX(50);
$pdf->Cell(15, $height, 'Yg Menyerahkan', 0, 0, 'L');
$pdf->SetX(125);
$pdf->Cell(15, $height, 'Yg Menerima', 0, 0, 'L');
$pdf->SetX(175);
$pdf->Ln(10);
$pdf->Cell(15, $height, '', 0, 0, 'L');
$pdf->SetX(50);
$pdf->Cell(15, $height, $namakar[$d['menyerahkan']], 0, 0, 'L');
$pdf->SetX(125);
$pdf->Cell(15, $height, $d['menerima'], 0, 0, 'L');
$pdf->SetX(175);
$pdf->Cell(15, $height, "\t", 0, 1, 'L');
$pdf->SetX(50);
$pdf->Cell(15, $height, 'Date :', 0, 0, 'L');
$pdf->SetX(125);
$pdf->Cell(15, $height, 'Date :', 0, 1, 'L');
$yAkhir = $pdf->GetY();
$pdf->Line(10, $yAkhir, 205, $yAkhir);
$pdf->Line(10, $yAkhir + 0.5, 205, $yAkhir + 0.5);
$pdf->Ln();
$pdf->Output();

?>
