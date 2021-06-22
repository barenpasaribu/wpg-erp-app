<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
$nosp = $_GET['nosp'];
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$indukOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');

class PDF extends FPDF
{
    
    public function Header()
    {
        $sInduk = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qInduk = mysql_query($sInduk);
        $rInduk = mysql_fetch_assoc($qInduk);
        $str1 = 'select * from '.$dbname.".organisasi where kodeorganisasi='".$rInduk['induk']."'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $nama = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
            $logo = $bar1->logo;
        }

        if (!empty($logo)) {
            $this->Image($logo, 30, 2, 20);
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(22);
        $this->Cell(60, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'C');
        $this->SetFont('Arial', '', 15);
        $this->Cell(190, 5, '', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $this->SetY(30);
        $this->SetX(163);
        $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
        $this->Line(10, 32, 200, 32);
    }
    

    public function Footer()
    {
    }
}

$str = 'select * from '.$dbname.".sdm_suratperingatan \r\n        where nomor='".$nosp."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $pt = $nmOrg[$indukOrg[$bar->kodeorg]];
    $namakaryawan = '';
    $strx = 'select a.namakaryawan,b.namajabatan,tipekaryawan from '.$dbname.".datakaryawan a \r\n          left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n          where karyawanid=".$bar->karyawanid;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
        $namakaryawan = $barx->namakaryawan;
        $jabatanybs = $barx->namajabatan;
        $tipex = $barx->tipekaryawan;
    }
    $tanggal = tanggalnormal($bar->tanggal);
    $sampai = tanggalnormal($bar->sampai);
    $tipesp = $bar->jenissp;
    $ketHal = '';
    $str = 'select keterangan from '.$dbname.".sdm_5jenissp where kode='".$tipesp."'";
    $rekx = mysql_query($str);
    while ($barkx = mysql_fetch_object($rekx)) {
        $ketHal = trim($barkx->keterangan);
    }
    $paragraf1 = $bar->paragraf1;
    $pelanggaran = $bar->pelanggaran;
    $paragraf3 = $bar->paragraf3;
    $paragraf4 = $bar->paragraf4;
    $karyawanid = $bar->karyawanid;
    $penandatangan = $bar->penandatangan;
    $jabatan = $bar->jabatan;
    $tembusan1 = $bar->tembusan1;
    $tembusan2 = $bar->tembusan2;
    $tembusan3 = $bar->tembusan3;
    $tembusan4 = $bar->tembusan4;
    $verifikasi = $bar->verifikasi;
    $dibuat = $bar->dibuat;
    $jabatandibuat = $bar->jabatandibuat;
    $jabatanverifikasi = $bar->jabatanverifikasi;
}
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetFont('Arial', 'B', 14);
$pdf->AddPage();
$pdf->SetY(40);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetX(20);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, 'No ', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, $nosp, 0, 0, 'L');
$pdf->Cell(40, 5, $_SESSION['lang']['tanggal'].' : '.$tanggal, 0, 1, 'R');
$pdf->SetX(20);
$pdf->Cell(20, 5, $_SESSION['lang']['hal1'], 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(115, 5, $ketHal, 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln();
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(20, 5, $_SESSION['lang']['kepada'], 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, $namakaryawan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(20, 5, $_SESSION['lang']['jabatan'], 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, $jabatanybs, 0, 1, 'L');
$pdf->SetX(20);
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(115, 5, $_SESSION['lang']['ditempat'], 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln();
$pdf->Ln();
$pdf->SetX(20);
$pdf->MultiCell(170, 5, $paragraf1, 0, 'J');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(170, 5, $pelanggaran, 0, 'J');
$pdf->Ln();
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(20);
$pdf->MultiCell(170, 5, $paragraf3, 0, 'J');
$pdf->Ln();
$pdf->SetX(20);
$pdf->MultiCell(170, 5, $paragraf4, 0, 'J');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
if ('0' == $tipex) {
    $pdf->SetX(20);
    $pdf->Cell(40, 5, $pt, 0, 1, 'L');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(20);
    $pdf->Cell(40, 5, ''.$penandatangan.' ', 'B', 1, 'L');
    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 5, ''.$jabatan.' ', 0, 1, 'L');
} else {
    $pdf->SetX(20);
    $pdf->Cell(40, 5, $_SESSION['lang']['dibuat'], 0, 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, $_SESSION['lang']['verifikasi'], 0, 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, $_SESSION['lang']['disetujui'], 0, 1, 'C');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(20);
    $pdf->Cell(40, 5, ''.$dibuat.' ', 'B', 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, ''.$verifikasi.' ', 'B', 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, ''.$penandatangan.' ', 'B', 1, 'C');
    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 5, ''.$jabatandibuat.' ', 0, 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, ''.$jabatanverifikasi.' ', 0, 0, 'C');
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->Cell(40, 5, ''.$jabatan.' ', 0, 1, 'C');
}

$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(40, 5, $_SESSION['lang']['tembusan'].'(i)   : '.$tembusan1, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(40, 5, $_SESSION['lang']['tembusan'].'(ii)  : '.$tembusan2, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(40, 5, $_SESSION['lang']['tembusan'].'(iii) : '.$tembusan3, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(40, 5, $_SESSION['lang']['tembusan'].'(iv) : '.$tembusan4, 0, 1, 'L');
$pdf->Ln();
$pdf->Output();

?>