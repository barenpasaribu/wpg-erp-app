<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/eagrolib.php';
$tampil = $_GET['tampil'];
$pabrik = $_GET['pabrik'];
$periode = $_GET['periode'];

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        global $periode;
        global $pabrik;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, $namapt, '', 1, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(275, 5, strtoupper($_SESSION['lang']['rprodksiPabrik']), 0, 1, 'C');
        $this->Cell(275, 5, $_SESSION['lang']['periode'].' : '.substr($periode, 5, 2).'-'.substr($periode, 0, 4), 0, 1, 'C');
        $this->Cell(275, 5, $_SESSION['lang']['pabrik'].' : '.$pabrik, 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(230, 5, $_SESSION['lang']['tanggal'], 0, 0, 'R');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
        $this->Cell(230, 5, $_SESSION['lang']['page'], '', 0, 'R');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
        $this->Cell(230, 3, 'User', '', 0, 'R');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 7);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(5, 10, 'No.', 1, 0, 'C');
        $this->Cell(20, 10, $_SESSION['lang']['tanggal'], 1, 0, 'C');
        $this->Cell(25, 10, $_SESSION['lang']['tersedia'], 1, 0, 'C');
        $this->Cell(40, 5, $_SESSION['lang']['tbsdiolah'], 1, 0, 'C');
        $this->Cell(100, 5, $_SESSION['lang']['cpo'], 1, 0, 'C');
        $this->Cell(100, 5, $_SESSION['lang']['kernel'], 1, 1, 'C');
        $this->setX(60);
        $this->SetFont('Arial', '', 6);
        $this->Cell(20, 5, 'HI', 1, 0, 'C');
        $this->Cell(20, 5, 'S/D', 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['cpo'].'(Kg) HI', 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['cpo'].'(Kg) S/D', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['oer'].'(%)', 1, 0, 'C');
        $this->Cell(15, 5, '(FFa)(%)', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['kotoran'].'(%)', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['kadarair'].'(%)', 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['kernel'].'(Kg) HI', 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['kernel'].'(Kg) S/D', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['oer'].'(%)', 1, 0, 'C');
        $this->Cell(15, 5, '(FFa)(%)', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['kotoran'].'(%)', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['kadarair'].'(%)', 1, 0, 'C');
        $y = $this->getY() - 5;
        $x = $this->GetX();
        $this->SetY($y);
        $this->SetX($x);
        $this->Cell(20, 10, $_SESSION['lang']['jampengolahan'], 1, 0, 'C');
        $this->Cell(20, 10, $_SESSION['lang']['jamstagnasi'], 1, 0, 'C');
        $this->Cell(15, 10, $_SESSION['lang']['sisa'].'(Kg) ', 1, 1, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'LEGAL');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);
$str = 'select * from '.$dbname.".pabrik_produksi where tanggal like '".$periode."%'\r\n\t      and kodeorg='".$pabrik."'\r\n\t\t  order by tanggal asc";
$res2 = mysql_query($str);
$res = mysql_query($str);
while ($datArr = mysql_fetch_assoc($res2)) {
    $tbs[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['tbsdiolah'];
    $jmOer[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['oer'];
    $jmOerPk[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['oerpk'];
}
$no = 0;
$tgl = 1;
while ($bar = mysql_fetch_object($res)) {
    if (1 === strlen($tgl)) {
        $agl = '0'.$tgl;
    }

    $tbsSd = $tbs[$bar->kodeorg][$tglServ.$agl + 1];
    $tbsSd2 = $tbs[$bar->kodeorg][$bar->tanggal];
    $tbsTot = $tbsSd2 + $tbsSd;
    $des += $tbsTot;
    $oerSd = $jmOer[$bar->kodeorg][$tglServ.$agl + 1];
    $oerSd2 = $jmOer[$bar->kodeorg][$bar->tanggal];
    $oerTot = $oerSd2 + $oerSd;
    $oerTotal += $oerTot;
    $oerpkSd = $jmOerPk[$bar->kodeorg][$tglServ.$agl + 1];
    $oerpkSd2 = $jmOerPk[$bar->kodeorg][$bar->tanggal];
    $oerpkTot = $oerpkSd + $oerpkSd2;
    $oerpkTotal += $oerpkTot;
    $sPengolahan = 'select jamdinasbruto as jampengolahan, jamstagnasi as jamstagnasi from '.$dbname.".pabrik_pengolahan \r\n               where kodeorg='".$bar->kodeorg."' and tanggal='".$bar->tanggal."'";
    $qPengolahan = mysql_query($sPengolahan);
    unset($jamP, $menitP, $jamS, $menitS);

    while ($res2 = mysql_fetch_object($qPengolahan)) {
        $dd = preg_split('/\\./D', $res2->jampengolahan);
        $ee = preg_split('/\\./D', $res2->jamstagnasi);
        list($jamP[], $menitP[]) = $dd;
        list($jamS[], $menitS[]) = $ee;
    }
    $rPengolahan['jampengolahan'] = @array_sum($jamP) + @array_sum($menitP) / 60;
    $rPengolahan['jamstagnasi'] = @array_sum($jamS) + @array_sum($menitS) / 60;
    ++$no;
    $pdf->Cell(5, 5, $no, 1, 0, 'C');
    $pdf->Cell(20, 5, tanggalnormal($bar->tanggal), 1, 0, 'C');
    $pdf->Cell(25, 5, number_format($bar->tbsmasuk + $bar->sisatbskemarin, 0, '.', ','), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($bar->tbsdiolah, 0, '.', ',.'), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($des, 0, '.', ',.'), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($bar->oer, 0, '.', ','), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($oerTotal, 0, '.', ','), 1, 0, 'R');
    $pdf->Cell(15, 5, @number_format($bar->oer / $bar->tbsdiolah * 100, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->ffa, 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->kadarkotoran, 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->kadarair, 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($bar->oerpk, 0, '.', ','), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($oerpkTotal, 0, '.', ','), 1, 0, 'R');
    $pdf->Cell(15, 5, @number_format($bar->oerpk / $bar->tbsdiolah * 100, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->ffapk, 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->kadarkotoranpk, 1, 0, 'R');
    $pdf->Cell(15, 5, $bar->kadarairpk, 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($rPengolahan['jampengolahan'], 2, '.', '.'), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($rPengolahan['jamstagnasi'], 2, '.', '.'), 1, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->sisahariini, 2, '.', ','), 1, 1, 'R');
}
$pdf->Output();

?>