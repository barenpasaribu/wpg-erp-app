<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdOrg = $_GET['cmpId'];
$tgl = explode('-', $_GET['period']);
$param = $_POST;
$cols = 'no,tanggal,pagi,sore,note';
$colArr = explode(',', $cols);
$title = $_SESSION['lang']['laporanCurahHujan'];
$align = explode(',', 'L,L,R,R,L');
$length = explode(',', '10,15,20,20,35');
switch ($proses) {
    case 'pdf':

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
        global $kdOrg;
        global $tgl;
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
        $this->Cell(45 / 100 * $width, $height, $_SESSION['empl']['lokasitugas'], '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $_SESSION['standard']['username'], 0, 0, 'L');
        $this->Ln();
        $query2 = selectQuery($dbname, 'organisasi', 'namaorganisasi', "kodeorganisasi='".$kdOrg."'");
        $orgData2 = fetchData($query2);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kebun'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $orgData2[0]['namaorganisasi'], '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $tgl[1].'-'.$tgl[0], '', 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $title, 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        foreach ($colArr as $key => $head) {
            $this->Cell($length[$key] / 100 * $width, $height, $_SESSION['lang'][$head], 1, 0, 'C', 1);
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

        $pdf = new PDF('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $ts = mktime(0, 0, 0, $tgl[1], 1, $tgl[0]);
        $jmlhHari = (int) (date('t', $ts));
        $tglDb = tanggalnormal($res['tanggal']);
        for ($a = 1; $a <= $jmlhHari; ++$a) {
            ++$i;
            if (strlen($a) < 2) {
                $a = '0'.$a;
            }

            $tglProg = $a.'-'.$tgl[1].'-'.$tgl[0];
            $sql = 'select * from '.$dbname.".kebun_curahhujan where kodeorg='".$kdOrg."' and tanggal='".tanggalsystem($tglProg)."'  ";
            $query = mysql_query($sql) ;
            $res = mysql_fetch_assoc($query);
            $pdf->Cell(10 / 100 * $width, $height, $i, 1, 0, 'L', 1);
            $pdf->Cell(15 / 100 * $width, $height, $tglProg, 1, 0, 'L', 1);
            $pdf->Cell(20 / 100 * $width, $height, $res['pagi'], 1, 0, 'R', 1);
            $pdf->Cell(20 / 100 * $width, $height, $res['sore'], 1, 0, 'R', 1);
            $pdf->Cell(35 / 100 * $width, $height, $res['catatan'], 1, 1, 'L', 1);
        }
        $pdf->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['ketCurahHUjan'], '', 0, 'L');
        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}

?>