<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$notransaksi = $_GET['notransaksi'];

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        //$this->Image($path, 15, 2, 40);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(22);
        $this->Cell(60, 5, strtoupper($namapt), 0, 1, 'C');
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
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$str = 'select * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $uraian = $bar->hasilkerja;
    $jabatan = '';
    $namakaryawan = '';
    $bagian = '';
    $karyawanid = '';
    $strc = "select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan \r\n                    from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b\r\n                        on a.kodejabatan=b.kodejabatan\r\n                        where a.karyawanid=".$bar->karyawanid;
    $resc = mysql_query($strc);
    while ($barc = mysql_fetch_object($resc)) {
        $jabatan = $barc->namajabatan;
        $namakaryawan = $barc->namakaryawan;
        $bagian = $barc->bagian;
        $karyawanid = $barc->karyawanid;
    }
    $strw = 'select a.namaorganisasi from '.$dbname.'.datakaryawan b left join '.$dbname.".organisasi a \r\n          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
    $resw = mysql_query($strw);
    while ($barw = mysql_fetch_object($resw)) {
        $namapt = $barw->namaorganisasi;
    }
    $kodeorg = $bar->kodeorg;
    $persetujuan = $bar->persetujuan;
    $hrd = $bar->hrd;
    $tujuan3 = $bar->tujuan3;
    $tujuan2 = $bar->tujuan2;
    $tujuan1 = $bar->tujuan1;
    $tanggalperjalanan = tanggalnormal($bar->tanggalperjalanan);
    $tanggalkembali = tanggalnormal($bar->tanggalkembali);
    $uangmuka = $bar->uangmuka;
    $tugas1 = $bar->tugas1;
    $tugas2 = $bar->tugas2;
    $tugas3 = $bar->tugas3;
    $tujuanlain = $bar->tujuanlain;
    $tugaslain = $bar->tugaslain;
    $pesawat = $bar->pesawat;
    $darat = $bar->darat;
    $laut = $bar->laut;
    $mess = $bar->mess;
    $hotel = $bar->hotel;
    $statushrd = $bar->statushrd;
    if (0 == $statushrd) {
        $statushrd = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statushrd) {
            $statushrd = $_SESSION['lang']['disetujui'];
        } else {
            $statushrd = $_SESSION['lang']['ditolak'];
        }
    }

    $statuspersetujuan = $bar->statuspersetujuan;
    if (0 == $statuspersetujuan) {
        $perstatus = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statuspersetujuan) {
            $perstatus = $_SESSION['lang']['disetujui'];
        } else {
            $perstatus = $_SESSION['lang']['ditolak'];
        }
    }

    $perjabatan = '';
    $perbagian = '';
    $pernama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n                   where karyawanid=".$persetujuan;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $perjabatan = $barf->namajabatan;
        $perbagian = $barf->bagian;
        $pernama = $barf->namakaryawan;
    }
    $hjabatan = '';
    $hbagian = '';
    $hnama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n                   where karyawanid=".$hrd;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $hjabatan = $barf->namajabatan;
        $hbagian = $barf->bagian;
        $hnama = $barf->namakaryawan;
    }
}
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetFont('Arial', 'B', 14);
$pdf->AddPage();
$pdf->SetY(40);
$pdf->SetX(20);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(175, 5, strtoupper($_SESSION['lang']['spdinas']), 0, 1, 'C');
$pdf->SetX(20);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(175, 5, 'NO : '.$notransaksi, 0, 1, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['nokaryawan'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$karyawanid, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$namakaryawan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['bagian'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$bagian, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['functionname'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$jabatan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['tanggaldinas'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$tanggalperjalanan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['tanggalkembali'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$tanggalkembali, 0, 1, 'L');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['hasiltugas'].':'), 'B', 1, 'L');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(172, 5, $uraian, 0, 'J');
$pdf->Ln();
$pdf->Output();

?>