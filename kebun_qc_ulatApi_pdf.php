<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$tmp = explode(',', $_GET['column']);
list($blok, $tgl) = $tmp;
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$namakar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$jumlahPkk = makeOption($dbname, 'setup_blok', 'kodeorg,jumlahpokok');
$luasAreal = makeOption($dbname, 'setup_blok', 'kodeorg,luasareaproduktif');

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
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->Cell(10, 5, 'QUALITY CONTROL', 0, 1, 'L');
        $this->SetFont('Arial', 'BU', 12);
        $this->SetFillColor(255, 255, 255);
        $this->Cell(200, 5, 'SENSUS HAMA ULAT API', 0, 0, 'C');
        $this->Ln(10);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 7;
$pdf->AddPage();
$i = 'select * from '.$dbname.".kebun_qc_ulatapiht where kodeblok='".$blok."' and tanggal='".$tgl."' ";
$n = mysql_query($i) ;
$d = mysql_fetch_assoc($n);
$pdf->SetFont('Arial', '', 8);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(30, $height, $_SESSION['lang']['divisi'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['kodeblok'], 0, 4)], 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['afdeling'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['kodeblok'], 0, 6)], 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['blok'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[$d['kodeblok']], 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['tglsensus'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, tanggalnormal($d['tanggal']), 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['tglPengendalian'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, tanggalnormal($d['tanggalpengendalian']), 0, 1, 'L', 1);
$pdf->Cell(30, $height, $_SESSION['lang']['jenis'], 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(90, $height, $d['jenissensus'], 0, 1, 'L', 1);
$pdf->Ln();
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(30, $height * 2, 'Pokok yang diamati', 1, 0, 'C', 1);
$pdf->Cell(20, $height * 2, 'Luasan (Ha)', 1, 0, 'C', 1);
$pdf->Cell(85, $height, 'Jumlah ulat perjenis', 1, 0, 'C', 1);
$pdf->Cell(60, $height * 2, 'Keterangan', 1, 1, 'C', 1);
$pdf->Ln(-7);
$pdf->Cell(50, $height * 2);
$pdf->Cell(20, $height, 'Darna Trima', 1, 0, 'C', 1);
$pdf->Cell(25, $height, 'Setothosea Asigna', 1, 0, 'C', 1);
$pdf->Cell(20, $height, 'Setora Nitens', 1, 0, 'C', 1);
$pdf->Cell(20, $height, 'Ulat Kantong', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$w = 'select * from '.$dbname.".kebun_qc_ulatapidt where kodeblok='".$d['kodeblok']."' and tanggal='".$d['tanggal']."' ";
$i = mysql_query($w) ;
while ($b = mysql_fetch_assoc($i)) {
    $pdf->Cell(30, $height, $b['pokokdiamati'], 1, 0, 'R', 1);
    $pdf->Cell(20, $height, $b['luasdiamati'], 1, 0, 'R', 1);
    $pdf->Cell(20, $height, $b['jlhdarnatrima'], 1, 0, 'R', 1);
    $pdf->Cell(25, $height, $b['jlhsetothosea'], 1, 0, 'R', 1);
    $pdf->Cell(20, $height, $b['jlhsetoranitens'], 1, 0, 'R', 1);
    $pdf->Cell(20, $height, $b['jlhulatkantong'], 1, 0, 'R', 1);
    $pdf->Cell(60, $height, $b['keterangan'], 1, 1, 'L', 1);
}
$pdf->Ln(5);
$pdf->Cell(20, $height, 'Catatan :', 0, 0, 'L', 1);
$pdf->MultiCell(180, $height, $d['catatan'], 0, 'L', 1);
$pdf->Ln();
$pdf->Cell(20, $height, 'Pelaksana Pemeriksaan', 0, 1, 'L', 1);
$pdf->Cell(65, $height, 'Quality Control', 0, 0, 'C', 1);
$pdf->Cell(65, $height, 'Pendamping', 0, 0, 'C', 1);
$pdf->Cell(65, $height, 'Mengetahui', 0, 0, 'C', 1);
$pdf->Ln(20);
$pdf->Cell(65, $height, '____________________________________', 0, 0, 'C', 1);
$pdf->Cell(65, $height, '____________________________________', 0, 0, 'C', 1);
$pdf->Cell(65, $height, '____________________________________', 0, 1, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['pengawas']], 0, 0, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['pendamping']], 0, 0, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['mengetahui']], 0, 1, 'C', 1);
$pdf->Ln(5);
$pdf->Cell(20, $height, 'Distribusi :', 0, 1, 'L', 1);
$pdf->Cell(20, $height, '1. GM Operational:', 0, 1, 'L', 1);
$pdf->Cell(20, $height, '2. Ka. Divisi', 0, 1, 'L', 1);
$pdf->Output();

?>