<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'BALANCE SHEET';
$periodesaldo = str_replace('-', '', $periode);
$tahun = substr($periodesaldo, 0, 4);
$tahunlalu = $tahun - 1;
$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$lebarketerangan = 10;
$lebarbulan = 7;

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        global $periode;
        global $unit;
        global $lebarketerangan;
        global $lebarbulan;
        global $dbname;
        global $tahun;
        global $tahunlalu;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 4;
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

        $this->Image($path, $this->lMargin, $this->tMargin, 25);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(40);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(40);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(40);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(10 / 100 * $width - 5, $height, $_SESSION['lang']['pt'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(70 / 100 * $width, $height, $namapt, '', 0, 'L');
        $this->Cell(7 / 100 * $width - 5, $height, 'Printed By', '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
        $this->Cell(10 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        if ('' === $unit) {
            $unit2 = $_SESSION['lang']['all'];
        } else {
            $unit2 = $unit;
        }

        $this->Cell(70 / 100 * $width, $height, $unit2, '', 0, 'L');
        $this->Cell(7 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $title = $_SESSION['lang']['neraca'].' Periodik '.$tahun;
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $title, 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell($lebarketerangan / 100 * $width, $height, 'Keterangan', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Dec '.$tahunlalu, 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Jan', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Feb', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Mar', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Apr', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'May', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Jun', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Jul', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Aug', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Sep', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Oct', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Nov', 1, 0, 'C', 1);
        $this->Cell($lebarbulan / 100 * $width, $height, 'Dec', 1, 1, 'C', 1);
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
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 4;
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 6);
while ($bar = mysql_fetch_object($res)) {
    $tampildari = $bar->variableoutput;
    if ('Header' === $bar->tipe) {
        if ('ID' === $_SESSION['language']) {
            $pdf->Cell(($lebarketerangan + $lebarbulan * 13) / 100 * $width, $height, $bar->keterangandisplay, 1, 1, 'L', 1);
        } else {
            $pdf->Cell(($lebarketerangan + $lebarbulan * 13) / 100 * $width, $height, $bar->keterangandisplay1, 1, 1, 'L', 1);
        }
    } else {
        $st12 = "select sum(awal01) as awal01, sum(awal02) as awal02, sum(awal03) as awal03, sum(awal04) as awal04,\r\n            sum(awal05) as awal05, sum(awal06) as awal06, sum(awal07) as awal07, sum(awal08) as awal08,\r\n                sum(awal09) as awal09, sum(awal10) as awal10, sum(awal11) as awal11, sum(awal12) as awal12\r\n               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n               and '".$bar->noakunsampai."' and ".$where." and periode like '".$tahun."%'";
        $res12 = mysql_query($st12);
        $awal01 = 0;
        $awal02 = 0;
        $awal03 = 0;
        $awal04 = 0;
        $awal05 = 0;
        $awal06 = 0;
        $awal07 = 0;
        $awal08 = 0;
        $awal09 = 0;
        $awal10 = 0;
        $awal11 = 0;
        $awal12 = 0;
        while ($ba12 = mysql_fetch_object($res12)) {
            $awal01 = $ba12->awal01;
            $awal02 = $ba12->awal02;
            $awal03 = $ba12->awal03;
            $awal04 = $ba12->awal04;
            $awal05 = $ba12->awal05;
            $awal06 = $ba12->awal06;
            $awal07 = $ba12->awal07;
            $awal08 = $ba12->awal08;
            $awal09 = $ba12->awal09;
            $awal10 = $ba12->awal10;
            $awal11 = $ba12->awal11;
            $awal12 = $ba12->awal12;
        }
        if ('Total' === $bar->tipe) {
            $pdf->SetFillColor(220, 220, 220);
            if ('ID' === $_SESSION['language']) {
                $pdf->Cell($lebarketerangan / 100 * $width, $height, $bar->keterangandisplay, 1, 0, 'L', 1);
            } else {
                $pdf->Cell($lebarketerangan / 100 * $width, $height, $bar->keterangandisplay1, 1, 0, 'L', 1);
            }

            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal01), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal02), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal03), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal04), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal05), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal06), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal07), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal08), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal09), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal10), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal11), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal12), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format(0), 1, 1, 'R', 1);
            $pdf->SetFillColor(255, 255, 255);
        } else {
            if ('ID' === $_SESSION['language']) {
                $pdf->Cell($lebarketerangan / 100 * $width, $height, $bar->keterangandisplay, 1, 0, 'L', 1);
            } else {
                $pdf->Cell($lebarketerangan / 100 * $width, $height, $bar->keterangandisplay1, 1, 0, 'L', 1);
            }

            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal01), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal02), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal03), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal04), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal05), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal06), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal07), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal08), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal09), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal10), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal11), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format($awal12), 1, 0, 'R', 1);
            $pdf->Cell($lebarbulan / 100 * $width, $height, number_format(0), 1, 1, 'R', 1);
        }
    }
}
$pdf->Output();

?>