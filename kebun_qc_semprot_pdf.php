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
        $this->Cell(200, 5, 'PEMERIKSAAN APLIKASI BAHAN KIMIA', 0, 0, 'C');
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
$i = 'select * from '.$dbname.".kebun_qc_semprot where blok='".$blok."' and tanggal='".$tgl."' ";
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
$pdf->Cell(5, $height, 'a', L, 0, 'L', 1);
$pdf->Cell(85, $height, $_SESSION['lang']['divisi'], R, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['blok'], 0, 4)], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'b', L, 0, 'L', 1);
$pdf->Cell(85, $height, $_SESSION['lang']['afdeling'], R, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[substr($d['blok'], 0, 6)], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'c', L, 0, 'L', 1);
$pdf->Cell(85, $height, $_SESSION['lang']['blok'], R, 0, 'L', 1);
$pdf->Cell(90, $height, $nmOrg[$d['blok']], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, 'd', L, 0, 'L', 1);
$pdf->Cell(85, $height, $_SESSION['lang']['luasareal'], R, 0, 'L', 1);
$pdf->Cell(90, $height, $luasAreal[$d['blok']], LR, 1, 'L', 1);
$pdf->Cell(10, $height, '', RLB, 0, 'C', 1);
$pdf->Cell(5, $height, 'e', LB, 0, 'L', 1);
$pdf->Cell(85, $height, $_SESSION['lang']['jumlahpokok'], RB, 0, 'L', 1);
$pdf->Cell(90, $height, $jumlahPkk[$d['blok']], LRB, 1, 'L', 1);
$pdf->Cell(10, $height, '3', 1, 0, 'C', 1);
$pdf->Cell(90, $height, $_SESSION['lang']['vhc_jenis_pekerjaan'], 1, 0, 'L', 1);
$pdf->Cell(90, $height, $nmKeg[$d['kodekegiatan']], 1, 1, 'L', 1);
$pdf->Cell(10, $height, '4', RLT, 0, 'C', 1);
$pdf->Cell(90, $height, 'Jenis bahan kimia (dosis per kep)', TRL, 0, 'L', 1);
$pdf->Cell(5, $height, '', TL, 0, 'L', 1);
$pdf->Cell(60, $height, $_SESSION['lang']['material'], T, 0, 'L', 1);
$pdf->Cell(25, $height, $_SESSION['lang']['jumlah'], RT, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial1']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['dosisjumlah1'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial2']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['dosisjumlah2'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', LB, 0, 'C', 1);
$pdf->Cell(90, $height, '', RLB, 0, 'L', 1);
$pdf->Cell(5, $height, '3', LB, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial3']], B, 0, 'L', 1);
$pdf->Cell(25, $height, $d['dosisjumlah3'], BR, 1, 'L', 1);
$pdf->Cell(10, $height, '5', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Takaran yang digunakan', 1, 0, 'L', 1);
$pdf->Cell(90, $height, $d['takaran'], 1, 1, 'L', 1);
$pdf->Cell(10, $height, '6', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Jenis gulma yang dominan', 1, 0, 'L', 1);
$pdf->Cell(90, $height, $d['jenisgulma'], 1, 1, 'L', 1);
$pdf->Cell(10, $height, '7', 1, 0, 'C', 1);
$pdf->Cell(90, $height, 'Kondisi Gulma', 1, 0, 'L', 1);
$pdf->Cell(90, $height, $d['kondisigulma'], 1, 1, 'L', 1);
$pdf->Cell(10, $height, '8', RLT, 0, 'C', 1);
$pdf->Cell(90, $height, 'Aplikasi', TRL, 0, 'L', 1);
$pdf->Cell(5, $height, '', TL, 0, 'L', 1);
$pdf->Cell(60, $height, $_SESSION['lang']['material'], T, 0, 'L', 1);
$pdf->Cell(25, $height, $_SESSION['lang']['jumlah'], RT, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(5, $height, 'a', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Jumlah bahan kimia yang diambil dari gudang', 0, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial1']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdiambil1'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial2']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdiambil2'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '3', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial3']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdiambil3'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(5, $height, 'b', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Jumlah bahan kimia yang dipakai', R, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial1']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai1'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial2']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai2'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '3', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial3']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai3'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(5, $height, 'c', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Sisa Bahan Kimia', R, 0, 'L', 1);
$pdf->Cell(5, $height, '1', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial1']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai1'] - $d['jumlahdiambil1'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '2', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial2']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai2'] - $d['jumlahdiambil2'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '', L, 0, 'C', 1);
$pdf->Cell(90, $height, '', RL, 0, 'L', 1);
$pdf->Cell(5, $height, '3', L, 0, 'L', 1);
$pdf->Cell(60, $height, $nmBarang[$d['dosismaterial3']], 0, 0, 'L', 1);
$pdf->Cell(25, $height, $d['jumlahdipakai3'] - $d['jumlahdiambil3'], R, 1, 'L', 1);
$pdf->Cell(10, $height, '9', TLR, 0, 'C', 1);
$pdf->Cell(180, $height, 'Hasil semprot', TRL, 1, 'L', 1);
$pdf->Cell(10, $height, '', RL, 0, 'C', 1);
$pdf->Cell(5, $height, '', L, 0, 'L', 1);
$pdf->Cell(85, $height, 'Nama Pekerja', 0, 0, 'L', 1);
$pdf->Cell(90, $height, 'Hasil (Pokok/ Kep)', R, 1, 'L', 1);
for ($i = 1; $i <= 14; ++$i) {
    $pdf->Cell(10, $height, '', RL, 0, 'C', 1);
    $pdf->Cell(5, $height, $i, L, 0, 'L', 1);
    $pdf->Cell(85, $height, $namakar[$d['karyawan'.$i]], 0, 0, 'L', 1);
    $pdf->Cell(90, $height, $d['hasilkaryawan'.$i], R, 1, 'L', 1);
}
$pdf->Cell(10, $height, '', RLB, 0, 'C', 1);
$pdf->Cell(5, $height, '15', BL, 0, 'L', 1);
$pdf->Cell(85, $height, $namakar[$d['karyawan15']], B, 0, 'L', 1);
$pdf->Cell(90, $height, $d['hasilkaryawan15'], BR, 1, 'L', 1);
$pdf->Ln();
$pdf->Cell(20, $height, 'Keterangan :', 0, 0, 'L', 1);
$pdf->MultiCell(180, $height, $d['keterangan'], 0, 'L', 1);
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