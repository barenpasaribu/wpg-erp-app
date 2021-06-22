<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$periode = $_GET['periode'];
$akundari = $_GET['akundari'];
$akunsampai = $_GET['akunsampai'];
if ('' === $akundari) {
    echo 'WARNING: silakan memilih akun.';
    exit();
}

if ('' === $akunsampai) {
    echo 'WARNING: silakan memilih akun.';
    exit();
}

$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal\r\n    where kodeaplikasi = 'CLM'\r\n    ";
$clm = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $clm = $bar->noakundebet;
}
$qwe = explode('-', $periode);
$periode = $qwe[0].$qwe[1];
$bulan = $qwe[1];
$periode2 = $qwe[1].'-'.$qwe[0];
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$str = 'select noakun,'.$zz.' from '.$dbname.".keu_5akun\r\n                        where level = '5'\r\n                        order by noakun\r\n                        ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
if ('' === $gudang) {
    $str = 'select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."'";
    $wheregudang = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $wheregudang .= "'".strtoupper($bar->kodeorganisasi)."',";
    }
    $wheregudang = 'and kodeorg in ('.substr($wheregudang, 0, -1).') ';
} else {
    $wheregudang = "and kodeorg = '".$gudang."' ";
}

$str = 'select * from '.$dbname.".keu_saldobulanan where periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' \r\n      and noakun !='".$clm."' ".$wheregudang.' order by noakun, kodeorg';
$lebarno = 6;
$lebarnoakun = 8;
$lebarnamaakun = 25;
$lebarkodeorg = 10;
$lebarawal = 11;
$lebardebet = 11;

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
        global $lebarno;
        global $lebarnoakun;
        global $lebarnamaakun;
        global $lebarkodeorg;
        global $lebarawal;
        global $lebardebet;
        global $periode2;
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
        $this->Cell(10 / 100 * $width - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(70 / 100 * $width, $height, $periode2, '', 0, 'L');
        $this->Cell(7 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $title = $_SESSION['lang']['catatanneraca'];
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $title, 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell($lebarno / 100 * $width, $height, $_SESSION['lang']['nomor'], 1, 0, 'C', 1);
        $this->Cell($lebarnoakun / 100 * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
        $this->Cell($lebarnamaakun / 100 * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
        $this->Cell($lebarkodeorg / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
        $this->Cell($lebarawal / 100 * $width, $height, $_SESSION['lang']['saldoawal'], 1, 0, 'C', 1);
        $this->Cell($lebardebet / 100 * $width, $height, $_SESSION['lang']['debet'], 1, 0, 'C', 1);
        $this->Cell($lebardebet / 100 * $width, $height, $_SESSION['lang']['kredit'], 1, 0, 'C', 1);
        $this->Cell($lebarawal / 100 * $width, $height, $_SESSION['lang']['saldoakhir'], 1, 1, 'C', 1);
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
$pdf->SetFont('Arial', '', 9);
$no = 0;
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $qweawal = 'awal'.$bulan;
    $qwedebet = 'debet'.$bulan;
    $qwekredit = 'kredit'.$bulan;
    $saldoawal = $bar->$qweawal;
    $totalawal += $saldoawal;
    $saldodebet = $bar->$qwedebet;
    $totaldebet += $saldodebet;
    $saldokredit = $bar->$qwekredit;
    $totalkredit += $saldokredit;
    $saldoakhir = ($saldoawal + $saldodebet) - $saldokredit;
    $totalakhir += $saldoakhir;
    $pdf->Cell($lebarno / 100 * $width, $height, $no, 1, 0, 'R', 1);
    $pdf->Cell($lebarnoakun / 100 * $width, $height, $bar->noakun, 1, 0, 'C', 1);
    $pdf->Cell($lebarnamaakun / 100 * $width, $height, $namaakun[$bar->noakun], 1, 0, 'L', 1);
    $pdf->Cell($lebarkodeorg / 100 * $width, $height, $bar->kodeorg, 1, 0, 'C', 1);
    $pdf->Cell($lebarawal / 100 * $width, $height, number_format($saldoawal), 1, 0, 'R', 1);
    $pdf->Cell($lebardebet / 100 * $width, $height, number_format($saldodebet), 1, 0, 'R', 1);
    $pdf->Cell($lebardebet / 100 * $width, $height, number_format($saldokredit), 1, 0, 'R', 1);
    $pdf->Cell($lebarawal / 100 * $width, $height, number_format($saldoakhir), 1, 1, 'R', 1);
}
$pdf->Cell(($lebarno + $lebarnoakun + $lebarnamaakun + $lebarkodeorg) / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
$pdf->Cell($lebarawal / 100 * $width, $height, number_format($totalawal), 1, 0, 'R', 1);
$pdf->Cell($lebardebet / 100 * $width, $height, number_format($totaldebet), 1, 0, 'R', 1);
$pdf->Cell($lebardebet / 100 * $width, $height, number_format($totalkredit), 1, 0, 'R', 1);
$pdf->Cell($lebarawal / 100 * $width, $height, number_format($totalakhir), 1, 1, 'R', 1);
$stream .= '<tr class=rowcontent>';
$stream .= '<td align=center colspan=4>Total</td>';
$stream .= '<td align=right>'.number_format($totalawal).'</td>';
$stream .= '<td align=right>'.number_format($totaldebet).'</td>';
$stream .= '<td align=right>'.number_format($totalkredit).'</td>';
$stream .= '<td align=right>'.number_format($totalakhir).'</td>';
$stream .= '</tr>';
$stream .= "</tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>";
$pdf->Output();
exit();

?>