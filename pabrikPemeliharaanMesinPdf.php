<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$pabrik = $_GET['pabrik'];
$statId = $_GET['statId'];
$periode = substr($_GET['periode'], 0, 7);
$kdBrg = $_GET['kdBrg'];
$msnId = $_GET['msnId'];

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $pabrik;
        global $statId;
        global $periode;
        global $kdBrg;
        global $msnId;
        $str1 = 'select * from '.$dbname.".organisasi where induk='MHO' and tipe='PT'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $nama = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
        }
        $str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pabrik."'";
        $namapt = 'COMPANY NAME';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $namapt = strtoupper($bar->namaorganisasi);
        }
        $sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$statId."'";
        $qNm = mysql_query($sNm);
        $rNm = mysql_fetch_assoc($qNm);
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
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, $namapt, '', 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Line(10, 30, 290, 30);
        $this->Cell(35, 5, $_SESSION['lang']['pabrik'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(80, 5, $namapt, '', 0, 'L');
        $this->Cell(20, 5, $_SESSION['lang']['statasiun'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $rNm['namaorganisasi'], 0, 1, 'L');
        $this->Cell(35, 5, 'User', '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $_SESSION['standard']['username'], '', 1, 'L');
        if ('0' !== $periode) {
            $this->Cell(15, 5, $_SESSION['lang']['periode'], '', 0, 'L');
            $this->Cell(2, 5, ':', '', 0, 'L');
            $this->Cell(35, 5, $periode, '', 0, 'L');
        }

        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->Ln();
$pdf->SetFont('Arial', 'U', 15);
$pdf->SetY(60);
$pdf->Cell(250, 5, $_SESSION['lang']['pemeliharaanMesinReport'], 0, 1, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(40, 5, $_SESSION['lang']['notransaksi'], 1, 0, 'C', 1);
$pdf->Cell(20, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
$pdf->Cell(25, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
$pdf->Cell(60, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
$pdf->Cell(15, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
$pdf->Cell(15, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
$pdf->Cell(15, 5, $_SESSION['lang']['shift'], 1, 0, 'C', 1);
$pdf->Cell(25, 5, $_SESSION['lang']['mesin'], 1, 0, 'C', 1);
$pdf->Cell(30, 5, $_SESSION['lang']['jammulai'], 1, 0, 'C', 1);
$pdf->Cell(30, 5, $_SESSION['lang']['jamselesai'], 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
if ('0' === $periode) {
    $str = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n\t\twhere a.pabrik='".$pabrik."' and a.statasiun='".$statId."'  order by a.tanggal asc";
} else {
    if ('0' !== $periode) {
        $str = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n\t\twhere a.pabrik='".$pabrik."' and a.statasiun='".$statId."' and tanggal like '%".$periode."%' order by a.tanggal asc";
    }
}

$re = mysql_query($str);
$no = 0;
while ($res = mysql_fetch_assoc($re)) {
    ++$no;
    $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
    $qBrg = mysql_query($sBrg);
    $rBrg = mysql_fetch_assoc($qBrg);
    $pdf->Cell(8, 5, $no, 1, 0, 'L', 1);
    $pdf->Cell(40, 5, $res['notransaksi'], 1, 0, 'C', 1);
    $pdf->Cell(20, 5, tanggalnormal($res['tanggal']), 1, 0, 'C', 1);
    $pdf->Cell(25, 5, $res['kodebarang'], 1, 0, 'C', 1);
    $pdf->Cell(60, 5, $rBrg['namabarang'], 1, 0, 'L', 1);
    $pdf->Cell(15, 5, $res['satuan'], 1, 0, 'C', 1);
    $pdf->Cell(15, 5, $res['jumlah'], 1, 0, 'C', 1);
    $pdf->Cell(15, 5, $res['shift'], 1, 0, 'C', 1);
    $pdf->Cell(25, 5, $res['mesin'], 1, 0, 'C', 1);
    $pdf->Cell(30, 5, tanggalnormald($res['jammulai']), 1, 0, 'C', 1);
    $pdf->Cell(30, 5, tanggalnormald($res['jamselesai']), 1, 1, 'C', 1);
}
$pdf->Output();

?>