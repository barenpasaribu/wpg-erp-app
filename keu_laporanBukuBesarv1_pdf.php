<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$tanggal1 = $_GET['tanggal1'];
$tanggal2 = $_GET['tanggal2'];
$akundari = $_GET['akundari'];
$akunsampai = $_GET['akunsampai'];
$qwe = explode('-', $tanggal1);
$periode = $qwe[2].$qwe[1];
$bulan = $qwe[1];
$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$qwe = explode('-', $tanggal2);
$tanggal2 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal\r\n    where kodeaplikasi = 'CLM'\r\n    ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $clm = $bar->noakundebet;
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

$str = 'select * from '.$dbname.".keu_saldobulanan where noakun != '".$clm."' and periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang.' order by noakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = 'awal'.$bulan;
    $saldoawal[$bar->noakun] += $bar->$qwe;
    $aqun[$bar->noakun] = $bar->noakun;
}
$isidata = [];
$str = 'select * from '.$dbname.".keu_jurnaldt_vw where noakun != '".$clm."' and tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang.' order by noakun, tanggal';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = $bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][nojur] = $bar->nojurnal;
    $isidata[$qwe][tangg] = $bar->tanggal;
    $isidata[$qwe][noaku] = $bar->noakun;
    $isidata[$qwe][keter] = $bar->keterangan;
    $isidata[$qwe][debet] = $bar->debet;
    $isidata[$qwe][kredi] = $bar->kredit;
    $aqun[$bar->noakun] = $bar->noakun;
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n    where level = '5' and noakun!='".$clm."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
if (!empty($isidata)) {
    foreach ($isidata as $c => $key) {
        $sort_noaku[] = $key['noaku'];
        $sort_tangg[] = $key['tangg'];
        $sort_debet[] = $key['debet'];
        $sort_nojur[] = $key['nojur'];
    }
}

if (!empty($isidata)) {
    array_multisort($sort_noaku, SORT_ASC, $sort_tangg, SORT_ASC, $sort_debet, SORT_DESC, $sort_nojur, SORT_ASC, $isidata);
}

if (!empty($aqun)) {
    asort($aqun);
}

class PDF extends FPDF
{
    public function Header()
    {
        global $pt;
        global $gudang;
        global $tanggal1;
        global $tanggal2;
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 3, $pt.' '.$gudang, '', 1, 'L');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(190, 3, strtoupper($_SESSION['lang']['laporanbukubesar']), 0, 1, 'C');
        $this->SetFont('Arial', '', 7);
        $this->Cell(150, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
        if ('' === $gudang) {
            $gudang = 'All';
        }

        $this->Cell(150, 3, 'UNIT : '.$gudang, '', 0, 'L');
        $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
        $this->Cell(150, 3, 'Tanggal : '.tanggalnormal($tanggal1).' sampai '.tanggalnormal($tanggal2), '', 0, 'L');
        $this->Cell(15, 3, 'User', '', 0, 'L');
        $this->Cell(2, 3, ' : ', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, $_SESSION['lang']['nomor'], 1, 0, 'C');
        $this->Cell(35, 5, $_SESSION['lang']['nojurnal'], 1, 0, 'C');
        $this->Cell(13, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C');
        $this->Cell(16, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
        $this->Cell(38, 5, $_SESSION['lang']['keterangan'], 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['debet'], 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['kredit'], 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['saldo'], 1, 0, 'C');
        $this->Ln();
        $this->Ln();
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
if (!empty($aqun)) {
    foreach ($aqun as $akyun) {
        $subsalwal = $saldoawal[$akyun];
        $totaldebet = 0;
        $totalkredit = 0;
        $subsalak = $subsalwal;
        $salwal = $subsalwal;
        $grandsalwal += $subsalwal;
        $pdf->Cell(10, 5, '', 0, 0, 'C');
        $pdf->Cell(35, 5, '', 0, 0, 'L');
        $pdf->Cell(15, 5, '', 0, 0, 'L');
        $pdf->Cell(12, 5, $akyun, 0, 0, 'L');
        $pdf->Cell(80, 5, $namaakun[$akyun], 0, 0, 'L');
        $pdf->Cell(20, 5, number_format($salwal), 0, 1, 'R');
        if (!empty($isidata)) {
            foreach ($isidata as $baris) {
                if ($baris[noaku] === $akyun) {
                    ++$no;
                    $pdf->Cell(10, 5, $no, 0, 0, 'C');
                    $pdf->Cell(35, 5, $baris[nojur], 0, 0, 'L');
                    $pdf->Cell(15, 5, tanggalnormal($baris[tangg]), 0, 0, 'L');
                    $pdf->Cell(12, 5, $baris[noaku], 0, 0, 'L');
                    $pdf->Cell(40, 5, substr($baris[keter], 0, 30), 0, 0, 'L');
                    $pdf->Cell(20, 5, number_format($baris[debet]), 0, 0, 'R');
                    $totaldebet += $baris[debet];
                    $grandtotaldebet += $baris[debet];
                    $pdf->Cell(20, 5, number_format($baris[kredi]), 0, 0, 'R');
                    $totalkredit += $baris[kredi];
                    $grandtotalkredit += $baris[kredi];

                    $cekakun=substr($baris[noaku],0,1);
                    
                    if($cekakun=='2'){
                        $salwal = ($salwal - $baris[debet]) + $baris[kredi];
                    } else{
                        $salwal = ($salwal + $baris[debet]) - $baris[kredi];
                    }

                    if ($salwal < 0) {
                        $pdf->SetTextColor(240, 53, 61);
                        $pdf->Cell(20, 5, number_format($salwal * -1), 0, 1, 'R');
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(20, 5, number_format($salwal), 0, 1, 'R');
                    }

                    $pdf->SetTextColor(0, 0, 0);
                    $subsalak = $salwal;
                }
            }
        }

        $pdf->Cell(112, 5, 'Sub Total', 0, 0, 'R');
        $pdf->Cell(20, 5, number_format($totaldebet), 0, 0, 'R');
        $pdf->Cell(20, 5, number_format($totalkredit), 0, 0, 'R');
        if ($subsalak < 0) {
            $pdf->SetTextColor(240, 53, 61);
            $pdf->Cell(20, 5, number_format($subsalak * -1), 0, 1, 'R');
        } else {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 5, number_format($subsalak), 0, 1, 'R');
        }

        $pdf->SetTextColor(0, 0, 0);
    }
}

$grandsalak = ($grandsalwal + $grandtotaldebet) - $grandtotalkredit;
$pdf->Cell(112, 5, 'Grand Total', 0, 0, 'R');
$pdf->Cell(20, 5, number_format($grandtotaldebet), 0, 0, 'R');
$pdf->Cell(20, 5, number_format($grandtotalkredit), 0, 0, 'R');
if ($grandsalak < 0) {
    $pdf->SetTextColor(240, 53, 61);
 //   $pdf->Cell(20, 5, number_format($grandsalak * -1), 0, 1, 'R');
} else {
    $pdf->SetTextColor(0, 0, 0);
 //   $pdf->Cell(20, 5, number_format($grandsalak), 0, 1, 'R');
}

$pdf->SetTextColor(0, 0, 0);
$pdf->Output();

?>