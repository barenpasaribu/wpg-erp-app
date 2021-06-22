<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$tmp = explode(',', $_GET['column']);
list($tgl, $blok) = $tmp;
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
        $this->Cell(200, 5, 'PENGENDALIAN HAMA ULAT API/KANTONG', 0, 0, 'C');
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
$height = 4.3;
$pdf->AddPage();
$i = 'select * from '.$dbname.".kebun_qc_hama where blok='".$blok."' and tanggal='".$tgl."' ";
$n = mysql_query($i) ;
$d = mysql_fetch_assoc($n);
$pdf->SetFont('Arial', '', 8);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(10, $height, '1', 1, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['tanggal'], 1, 0, 'L', 1);
$pdf->Cell(90, $height, tanggalnormal($d['tanggal']), 1, 1, 'L', 1);
$pdf->Cell(10, $height, '2', RLT, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['lokasi'], TRL, 0, 'L', 1);
$pdf->Cell(90, $height, '', TRL, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['divisi'], RL, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['blok'], 0, 4)], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['afdeling'], RL, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['blok'], 0, 6)], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['blok'], RL, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[$d['blok']], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '3', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Tenaga Kerja', 1, 0, 'L', 1);
$pdf->Cell(90, $height, $d['tenagakerja'], 1, 1, 'L', 1);
$pdf->Cell(10, $height, '4', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Jam Kerja', 1, 0, 'L', 1);
$pdf->Cell(30, $height, Mulai.' : '.$d['mulaijam'], LTB, 0, 'L', 1);
$pdf->Cell(30, $height, Sampai.' : '.$d['sampaijam'], TB, 0, 'L', 1);
$totaljam = $d['sampaijam'] - $d['mulaijam'];
$pdf->Cell(30, $height, 'Total Jam :'.$totaljam, RTB, 1, 'L', 1);
$pdf->Cell(10, $height, '5', RLT, 0, 'C', 1);
$pdf->Cell(90, $height, 'Penanganan', TRL, 0, 'L', 1);
$pdf->Cell(5, $height, '', TL, 0, 'L', 1);
$pdf->Cell(60, $height, '', T, 0, 'L', 1);
$pdf->Cell(25, $height, '', RT, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'a', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Alat yang digunakan', R, 0, 'L', 1);
$pdf->Cell(90, $height, $d['alat'], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'b', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Bahan kimia yang digunakan', R, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(85, $height, $nmBarang[$d['bahan1']], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, '', L, 0, 'L', 1);
$pdf->Cell(85, $height, '', R, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(85, $height, $nmBarang[$d['bahan2']], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, '', L, 0, 'L', 1);
$pdf->Cell(85, $height, '', R, 0, 'L', 1);
$pdf->Cell(5, $height, '3', L, 0, 'L', 1);
$pdf->Cell(85, $height, $nmBarang[$d['bahan3']], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'c', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Dosis', R, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(85, $height, $d['dosis1'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, '', L, 0, 'L', 1);
$pdf->Cell(85, $height, '', R, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(85, $height, $d['dosis2'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, '', L, 0, 'L', 1);
$pdf->Cell(85, $height, '', R, 0, 'L', 1);
$pdf->Cell(5, $height, '3', L, 0, 'L', 1);
$pdf->Cell(85, $height, $d['dosis3'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '6', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Pokok yang dikendalikan', 1, 0, 'L', 1);
$pdf->Cell(90, $height, $d['pokok'], 1, 1, 'L', 1);
$pdf->Ln();
$pdf->Cell(20, $height, 'Keterangan :', 0, 1, 'L', 1);
$pdf->SetX(20);
$pdf->Cell(15, $height, 'Bensin', 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(5, $height, $d['bensin'].' Ltr', 0, 1, 'L', 1);
$pdf->SetX(20);
$pdf->Cell(15, $height, 'Oli', 0, 0, 'L', 1);
$pdf->Cell(5, $height, ':', 0, 0, 'L', 1);
$pdf->Cell(5, $height, $d['oli'].' Ltr', 0, 1, 'L', 1);
$pdf->MultiCell(180, $height, $d["catatan\t"], 0, 'L', 1);
$pdf->Ln();
$pdf->Cell(20, $height, 'Yang melakukan pemeriksaan', 0, 1, 'L', 1);
$pdf->Cell(65, $height, 'Quality Control', 0, 0, 'C', 1);
$pdf->Cell(65, $height, 'Divisi', 0, 0, 'C', 1);
$pdf->Cell(65, $height, 'Mengetahui', 0, 0, 'C', 1);
$pdf->Ln(20);
$pdf->Cell(65, $height, '____________________________________', 0, 0, 'C', 1);
$pdf->Cell(65, $height, '____________________________________', 0, 0, 'C', 1);
$pdf->Cell(65, $height, '____________________________________', 0, 1, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['pengawas']], 0, 0, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['asisten']], 0, 0, 'C', 1);
$pdf->Cell(65, $height, $namakar[$d['mengetahui']], 0, 1, 'C', 1);
$pdf->Ln(5);
$pdf->Cell(20, $height, 'Distribusi :', 0, 1, 'L', 1);
$pdf->Cell(20, $height, '1. GM Operational:', 0, 1, 'L', 1);
$pdf->Cell(20, $height, '2. Ka. Divisi', 0, 1, 'L', 1);
$pdf->Output();

?>