<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

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
        global $periode;
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
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, ucfirst($_SESSION['standard']['username']), '', 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $_SESSION['lang']['rekomendasiPupuk'], 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(2 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tahunpupuk'], 1, 0, 'C', 1);
        $this->Cell(19 / 100 * $width, $height, $_SESSION['lang']['afdeling'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['tahuntanam'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['jenisPupuk'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['dosis'].' '.$_SESSION['lang']['rotasi'].' 1', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['dosis'].' '.$_SESSION['lang']['rotasi'].' 2', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['dosis'].' '.$_SESSION['lang']['rotasi'].' 3', 1, 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['jenisbibit'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);
$str = 'select * from '.$dbname.'.'.$_GET['table']." where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by periodepemupukan asc";
$re = mysql_query($str);
$no = 0;
while ($res = mysql_fetch_assoc($re)) {
    $skdBrg = 'select  namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
    $qkdBrg = mysql_query($skdBrg) ;
    $rBrg = mysql_fetch_assoc($qkdBrg);
    $sBibit = 'select jenisbibit  from '.$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'";
    $qBibit = mysql_query($sBibit) ;
    $rBibit = mysql_fetch_assoc($qBibit);
    $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$res['kodeorg']."'";
    $qOrg = mysql_query($sOrg) ;
    $rOrg = mysql_fetch_assoc($qOrg);
    ++$no;
    $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'L', 1);
    $pdf->Cell(10 / 100 * $width, $height, $res['periodepemupukan'], 1, 0, 'L', 1);
    $pdf->Cell(19 / 100 * $width, $height, $rOrg['namaorganisasi'], 1, 0, 'L', 1);
    $pdf->Cell(9 / 100 * $width, $height, $res['blok'], 1, 0, 'L', 1);
    $pdf->Cell(9 / 100 * $width, $height, $res['tahuntanam'], 1, 0, 'C', 1);
    $pdf->Cell(9 / 100 * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
    $pdf->Cell(10 / 100 * $width, $height, $res['dosis'], 1, 0, 'R', 1);
    $pdf->Cell(10 / 100 * $width, $height, $res['dosis2'], 1, 0, 'R', 1);
    $pdf->Cell(10 / 100 * $width, $height, $res['dosis3'], 1, 0, 'R', 1);
    $pdf->Cell(5 / 100 * $width, $height, $rBrg['satuan'], 1, 0, 'L', 1);
    $pdf->Cell(8 / 100 * $width, $height, $rBibit['jenisbibit'], 1, 1, 'L', 1);
}
$pdf->Output();

?>