<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zMysql.php';
$pt = $_GET['pt'];
$periode = $_GET['periode'];

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        global $pt;
        global $periode;
        $noSpb = $_GET['column'];
        $nospb = substr($noSpb, 8, 6);
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
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

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $pt, '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $_SESSION['standard']['username'], 0, 0, 'L');
        $this->Ln();
        $query2 = selectQuery($dbname, 'organisasi', 'namaorganisasi', "kodeorganisasi='".$pt."'");
        $orgData2 = fetchData($query2);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['namaorganisasi'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $orgData2[0]['namaorganisasi'], '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $periode, '', 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $_SESSION['lang']['listSpb'], 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nospb'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['janjang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['bjr'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['brondolan'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['mentah'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['busuk'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['matang'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['lewatmatang'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
if (strlen($pt) < 6) {
    $kdOrg = 'substr(b.blok,1,4)';
} else {
    $kdOrg = 'substr(b.blok,1,6)';
}

$str = 'select a.tanggal,b.*, c.tahuntanam from '.$dbname.'.kebun_spbht a inner join '.$dbname.".kebun_spbdt b on a.nospb=b.nospb INNER JOIN setup_blok c ON b.blok=c.kodeorg \r\n\t\twhere a.tanggal like a.'%".$periode."%' and ".$kdOrg."='".$pt."' order by a.tanggal asc ";
$re = mysql_query($str);
$row = mysql_num_rows($re);
if (0 < $row) {
    $no = 0;
    while ($res = mysql_fetch_assoc($re)) {
        ++$no;
        $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'L', 1);
        $pdf->Cell(15 / 100 * $width, $height, $res['nospb'], 1, 0, 'L', 1);
        $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($res['tanggal']), 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, $res['blok']."-".$res['tahuntanam'], 1, 0, 'L', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($res['jjg'], 2), 1, 0, 'L', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($res['bjr'], 2), 1, 0, 'L', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($res['brondolan'], 2), 1, 0, 'L', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($res['mentah'], 2), 1, 0, 'L', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($res['busuk'], 2), 1, 0, 'L', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($res['matang'], 2), 1, 0, 'L', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($res['lewatmatang'], 2), 1, 1, 'L', 1);
    }
} else {
    $pdf->Cell(98 / 100 * $width, $height, 'Not Found', 1, 1, 'C', 1);
}

$pdf->Output();

?>