<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $noTrans;
        global $rtgl;
        global $shift;
        global $sts;
        global $msn;
        global $jmMulai;
        global $jmAkhir;
        global $kegiatan;
        global $posting;
        global $nmOrg;
        $noTrans = $_GET['column'];
        $sql = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$noTrans."' ";
        $res = mysql_query($sql);
        $bar = mysql_fetch_object($res);
        $rtgl = tanggalnormal($bar->tanggal);
        $shift = $bar->shift;
        $sts = $bar->statasiun;
        $msn = $bar->mesin;
        $jmMulai = tanggalnormald($bar->jammulai);
        $jmAkhir = tanggalnormald($bar->jamselesai);
        $kegiatan = $bar->kegiatan;
        $posting = $bar->statPost;
        $str1 = 'select * from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $nama = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
        }
        $kdDiv = substr($noTrans, 5, 6);
        $str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$kdDiv."'";
        $namapt = 'COMPANY NAME';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $namapt = strtoupper($bar->namaorganisasi);
        }
        if ('SSP' === $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' === $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' === $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' === $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, 15, 5, 35, 20);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(55);
        $this->Cell(60, 5, $nama, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, $alamatpt, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, 'Tel: '.$telp, 0, 1, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(190, 5, strtoupper($_SESSION['lang']['pemeliharaanMesin']), 0, 1, 'C');
        $this->Ln();
        if ($posting < 1) {
            $this->Cell(190, 5, $_SESSION['lang']['belumposting'], 0, 1, 'C');
        }

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, 'STATION : '.$namapt, '', 1, 'L');
        $this->Cell(20, 5, 'MESIN : '.$nmOrg[$msn], '', 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 5, $_SESSION['lang']['notransaksi'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $noTrans, '', 0, 'L');
        $this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $rtgl, 0, 1, 'L');
        $this->Cell(35, 5, $_SESSION['lang']['shift'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $shift, '', 0, 'L');
        $this->Cell(15, 5, $_SESSION['lang']['statasiun'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $sts, '', 1, 'L');
        $this->Cell(35, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $jmMulai, '', 0, 'L');
        $this->Cell(15, 5, $_SESSION['lang']['sampai'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $jmAkhir, '', 1, 'L');
        $this->Cell(35, 5, $_SESSION['lang']['mesin'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $msn, '', 0, 'L');
        $this->Cell(15, 5, 'User', '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Line(10, 30, 205, 30);
        $this->Cell(35, 5, $_SESSION['lang']['kegiatan'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $kegiatan, '', 0, 'L');
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->Ln();
$str = 'select * from '.$dbname.".pabrik_rawatmesindt   where notransaksi='".$noTrans."'";
$re = mysql_query($str);
$rBaris = mysql_num_rows($re);
if (0 !== $rBaris) {
    $pdf->SetFont('Arial', 'U', 15);
    $pdf->Cell(190, 5, $_SESSION['lang']['detail'], 0, 1, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
    $pdf->Cell(30, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
    $pdf->Cell(60, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
    $pdf->Cell(20, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
    $pdf->Cell(20, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
    $pdf->Cell(55, 5, $_SESSION['lang']['keterangan'], 1, 1, 'C', 1);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 9);
    $no = 0;
    while ($res = mysql_fetch_assoc($re)) {
        ++$no;
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $pdf->Cell(8, 5, $no, 1, 0, 'L', 1);
        $pdf->Cell(30, 5, $res['kodebarang'], 1, 0, 'L', 1);
        $pdf->Cell(60, 5, $rBrg['namabarang'], 1, 0, 'L', 1);
        $pdf->Cell(20, 5, $res['satuan'], 1, 0, 'L', 1);
        $pdf->Cell(20, 5, number_format($res['jumlah'], 2), 1, 0, 'R', 1);
        $pdf->Cell(55, 5, $res['keterangan'], 1, 1, 'L', 1);
    }
}

$pdf->Output();

?>