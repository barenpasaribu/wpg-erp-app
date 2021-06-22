<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$tanggal = $_GET['tanggal'];
$kodeblok = $_GET['kodeblok'];

class PDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(10);
        $this->SetX(15);
        $this->Cell(0, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(15);
        $this->Cell(0, 5, 'QUALITY CONTROL', 0, 1, 'L');
        $this->Cell(0, 4, '', 0, 1, 'C');
        $this->Cell(0, 5, 'CHECKLIST PEMUPUKAN', 0, 1, 'C');
        $this->Line(82.5, 29, 128, 29);
        $this->SetFont('Arial', '', 8);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$a = 'select * from '.$dbname.".kebun_qc_pemupukanht where kodeblok='".$kodeblok."' and tanggal='".$tanggal."'  ";
$b = mysql_query($a) ;
while ($c = mysql_fetch_array($b)) {
    $tanggal1 = tanggalnormal($c['tanggal']);
    $divisi = $c['divisi'];
    $afdeling = substr($c['kodeblok'], 0, 6);
    $blok = $c['kodeblok'];
    $idpengawas = $c['pengawas'];
    $jammulai = $c['darijam'];
    $jamselesai = $c['sampaijam'];
    $jmlhk = $c['jumlahhk'];
    $dosis = $c['dosis'];
    $teraplikasi = $c['teraplikasi'];
    $kondisilahan = $c['kondisilahan'];
    $idqc = $c['idqc'];
    $mengetahui = $c['mengetahui'];
    $comment = $c['comment'];
    $barang = $c['kodebarang'];
}
$str_sql_topo = 'select*from '.$dbname.".setup_blok where kodeorg='".$kodeblok."'";
$query_topo = mysql_query($str_sql_topo) ;
while ($b = mysql_fetch_array($query_topo)) {
    $topo = $b['topografi'];
}
$sql_kary = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$idpengawas."'";
$query_kary = mysql_query($sql_kary) ;
while ($kary = mysql_fetch_array($query_kary)) {
    $nmpengawas = $kary['namakaryawan'];
}
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetFont('Arial', 'B', 8);
$pdf->AddPage();
$pdf->SetY(35);
$pdf->SetX(15);
$pdf->Cell(7, 5, 'Tanggal', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $tanggal1, 0, 0, 'L');
$pdf->SetX(124);
$pdf->Cell(9, 5, 'Jam Kerja', 0, 0, 'L');
$pdf->SetX(157);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(159);
$pdf->Cell(1, 5, $jammulai.' s.d '.$jamselesai, 0, 0, 'L');
$pdf->SetY(39);
$pdf->SetX(15);
$pdf->Cell(6, 5, 'Divisi', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $divisi, 0, 0, 'L');
$pdf->SetX(124);
$pdf->Cell(14, 5, 'Jumlah Pekerja', 0, 0, 'L');
$pdf->SetX(157);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(159);
$pdf->Cell(1, 5, $jmlhk, 0, 0, 'L');
$pdf->SetY(43);
$pdf->SetX(15);
$pdf->Cell(8, 5, 'Afdeling', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $afdeling, 0, 0, 'L');
$pdf->SetX(124);
$pdf->Cell(5, 5, 'Pupuk & Dosis', 0, 0, 'L');
$pdf->SetX(157);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(159);
$pdf->Cell(1, 5, $nmBrg[$barang].', Dosis : '.$dosis, 0, 0, 'L');
$pdf->SetY(47);
$pdf->SetX(15);
$pdf->Cell(4, 5, 'Blok', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $blok, 0, 0, 'L');
$pdf->SetX(124);
$pdf->Cell(23, 5, 'Total pupuk teraplikasi', 0, 0, 'L');
$pdf->SetX(157);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(159);
$pdf->Cell(1, 5, $teraplikasi.' Sak', 0, 0, 'L');
$pdf->SetY(51);
$pdf->SetX(15);
$pdf->Cell(4, 5, 'Topo', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $topo, 0, 0, 'L');
$pdf->SetX(124);
$pdf->Cell(13, 5, 'Kondisi Lahan', 0, 0, 'L');
$pdf->SetX(157);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(159);
$pdf->Cell(1, 5, $kondisilahan, 0, 0, 'L');
$pdf->SetY(55);
$pdf->SetX(15);
$pdf->Cell(13, 5, 'Nama Pengawas', 0, 0, 'L');
$pdf->SetX(39);
$pdf->Cell(1, 5, ':', 0, 0, 'L');
$pdf->SetX(41);
$pdf->Cell(10, 5, $nmpengawas, 0, 1, 'L');
$pdf->SetFillColor(220, 220, 220);
$pdf->SetY(63);
$pdf->SetX(16);
$pdf->Cell(21, 5, 'No.Jalur', 'TRL', 0, 'C', 1);
$pdf->Cell(25, 5, 'Jumlah Pokok', 'TRL', 0, 'C', 1);
$pdf->Cell(46, 5, 'Missed Out Palms', 'TRL', 0, 'C', 1);
$pdf->Cell(26, 5, 'Aplikasi', 'TRL', 0, 'C', 1);
$pdf->Cell(54, 5, 'Keterangan', 'TRL', 1, 'C', 1);
$pdf->SetY(67);
$pdf->SetX(16);
$pdf->Cell(21, 5, 'diperiksa', 'BLR', 0, 'C', 1);
$pdf->Cell(25, 5, 'dipupuk', 'BLR', 0, 'C', 1);
$pdf->Cell(46, 5, '(Jumlah pokok tidak dipupuk)', 'BLR', 0, 'C', 1);
$pdf->Cell(26, 5, 'Tidak Standar', 'BLR', 0, 'C', 1);
$pdf->Cell(54, 5, '', 'BLR', 1, 'C', 1);
$x = 'select * from '.$dbname.".kebun_qc_pemupukandt where kodeblok='".$kodeblok."' and tanggal='".$tanggal."'  ";
$y = mysql_query($x) ;
while ($z = mysql_fetch_array($y)) {
    $nojalur = $z['nojalur'];
    $pkkdipupuk = $z['pkkdipupuk'];
    $pkktdkdipupuk = $z['pkktdkdipupuk'];
    $apltdkstandar = $z['apltdkstandar'];
    $keterangan = $z['keterangan'];
    $totpkkdipupuk += $z['pkkdipupuk'];
    $totpkktdkdipupuk += $z['pkktdkdipupuk'];
    $totapltdkstandar += $z['apltdkstandar'];
    $pdf->SetX(16);
    $pdf->Cell(21, 5, $nojalur, 1, 0, 'C');
    $pdf->Cell(25, 5, $pkkdipupuk, 1, 0, 'C');
    $pdf->Cell(46, 5, $pkktdkdipupuk, 1, 0, 'C');
    $pdf->Cell(26, 5, $apltdkstandar, 1, 0, 'C');
    $pdf->Cell(54, 5, $keterangan, 1, 1, 'L');
}
$totket = round($totpkktdkdipupuk / ($totpkkdipupuk + $totpkktdkdipupuk) * 100, 0);
$pdf->SetX(16);
$pdf->Cell(21, 5, 'TOTAL :', 1, 0, 'C');
$pdf->Cell(25, 5, $totpkkdipupuk, 1, 0, 'C');
$pdf->Cell(46, 5, $totpkktdkdipupuk, 1, 0, 'C');
$pdf->Cell(26, 5, $totapltdkstandar, 1, 0, 'C');
$pdf->Cell(54, 5, $totket.'%', 1, 1, 'C');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$pdf->SetX(16);
$pdf->Cell(21, 5, 'Comment :', 0, 0, 'L');
$pdf->Cell(151, 5, $comment, 0, 1, 'LR');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$sql_qc = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$idqc."'";
$query_qc = mysql_query($sql_kary) ;
while ($qc = mysql_fetch_array($query_qc)) {
    $nmqc = $qc['namakaryawan'];
}
$sql_mengetahui = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$mengetahui."'";
$query_mengetahui = mysql_query($sql_mengetahui) ;
while ($nmmengetahui = mysql_fetch_array($query_mengetahui)) {
    $namamengetahui = $nmmengetahui['namakaryawan'];
}
$pdf->SetX(16);
$pdf->Cell(44, 5, 'Yang melakukan pemeriksaan', 0, 0, 'L');
$pdf->Cell(128, 5, '', 0, 1, 'L');
$pdf->SetX(16);
$pdf->Cell(57, 5, 'Quality Control', 0, 0, 'L');
$pdf->Cell(57, 5, 'Divisi', 0, 0, 'L');
$pdf->Cell(58, 5, 'Mengetahui', 0, 1, 'L');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$pdf->SetFont('', 'U');
$pdf->SetX(16);
$pdf->Cell(57, 5, $nmqc, 0, 0, 'L');
$pdf->SetFont('');
$pdf->Cell(57, 5, '_______________', 0, 0, 'L');
$pdf->SetFont('', 'U');
$pdf->Cell(58, 5, $namamengetahui, 0, 1, 'L');
$pdf->SetFont('');
$pdf->SetX(16);
$pdf->Cell(172, 5, '', 0, 1, 'C');
$pdf->SetFont('', 'U');
$pdf->SetX(16);
$pdf->Cell(172, 5, 'Distribusi :', 0, 1, 'L');
$pdf->SetFont('');
$pdf->SetX(16);
$pdf->Cell(172, 5, '1.GM Operational', 0, 1, 'L');
$pdf->SetX(16);
$pdf->Cell(172, 5, '2.Ka.Divisi', 0, 1, 'L');
$pdf->Output();

?>