<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$unit = $_GET['unit'];
$periode = $_GET['periode'];
$jurnaldari = $_GET['jurnaldari'];
$jurnalsampai = $_GET['jurnalsampai'];
if ('EN' === $_SESSION['language']) {
    $str = 'select noakun,namaakun1 as namaakun from '.$dbname.".keu_5akun\r\n    where level = '5'\r\n    order by noakun";
} else {
    $str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n    where level = '5'\r\n    order by noakun";
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
$whereunit = '';
$isidata = [];
$str = 'select * from '.$dbname.".keu_jurnaldt_vw where nojurnal not like '%CLSM%' and kodeorg = '".$unit."' and periode = '".$periode."' and nojurnal >= '".$jurnaldari."' and nojurnal <= '".$jurnalsampai."' ".$whereunit.' order by nojurnal';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = $bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][nojur] = $bar->nojurnal;
    $isidata[$qwe][nouru] = $bar->nourut;
    $isidata[$qwe][noaku] = $bar->noakun;
    $isidata[$qwe][keter] = $bar->keterangan;
    $isidata[$qwe][jumla] = $bar->jumlah;
}
if (!empty($isidata)) {
    foreach ($isidata as $c => $key) {
        $sort_nojur[] = $key['nojur'];
        $sort_nouru[] = $key['nouru'];
    }
    array_multisort($sort_nojur, SORT_ASC, $sort_nouru, SORT_ASC, $isidata);

    class PDF extends FPDF
    {
        public function Header()
        {
            global $unit;
            global $periode;
            global $jurnaldari;
            global $jurnalsampai;
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(190, 3, strtoupper($_SESSION['lang']['laporanperiksajurnal']), 0, 1, 'C');
            $this->SetFont('Arial', '', 9);
            $this->Cell(150, 3, ' ', '', 0, 'R');
            $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
            $this->Cell(2, 3, ':', '', 0, 'L');
            $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
            $this->Cell(150, 3, 'UNIT : '.$unit, '', 0, 'L');
            $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
            $this->Cell(2, 3, ':', '', 0, 'L');
            $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
            $this->Cell(150, 3, 'Periode : '.$periode, '', 0, 'L');
            $this->Cell(15, 3, 'User', '', 0, 'L');
            $this->Cell(2, 3, ' : ', '', 0, 'L');
            $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
            $this->Ln();
            $this->SetFont('Arial', '', 7);
            $this->Cell(35, 5, $_SESSION['lang']['nojurnal'], 1, 0, 'C');
            $this->Cell(16, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
            $this->Cell(38, 5, $_SESSION['lang']['namaakun'], 1, 0, 'C');
            $this->Cell(38, 5, $_SESSION['lang']['keterangan'], 1, 0, 'C');
            $this->Cell(20, 5, $_SESSION['lang']['debet'], 1, 0, 'C');
            $this->Cell(20, 5, $_SESSION['lang']['kredit'], 1, 0, 'C');
            $this->Cell(20, 5, $_SESSION['lang']['selisih'], 1, 0, 'C');
            $this->Ln();
            $this->Ln();
        }
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $no = 0;
    $totaldebet = 0;
    $totalkredit = 0;
    $grandtotaldebet = 0;
    $grandtotalkredit = 0;
    $grandtotalselisih = 0;
    foreach ($isidata as $baris) {
        if ($jurnalaktif !== $baris[nojur] && '' !== $jurnalaktif) {
            $pdf->Cell(127, 5, 'Total', 0, 0, 'R');
            $pdf->Cell(20, 5, number_format($totaldebet), T, 0, 'R');
            $pdf->Cell(20, 5, number_format(-1 * $totalkredit), T, 0, 'R');
            $pdf->Cell(20, 5, number_format($selisih), T, 1, 'R');
            $grandtotaldebet += $totaldebet;
            $grandtotalkredit += $totalkredit;
            $grandtotalselisih += $selisih;
            $totaldebet = 0;
            $totalkredit = 0;
            $selisih = 0;
        }

        $pdf->Cell(35, 5, $baris[nojur], 0, 0, 'L');
        $pdf->Cell(16, 5, $baris[noaku], 0, 0, 'L');
        $pdf->Cell(38, 5, substr($namaakun[$baris[noaku]], 0, 30), 0, 0, 'L');
        $pdf->Cell(38, 5, substr($baris[keter], 0, 30), 0, 0, 'L');
        if (0 < $baris[jumla]) {
            $pdf->Cell(20, 5, number_format($baris[jumla]), 0, 0, 'R');
            $pdf->Cell(20, 5, '', 0, 0, 'R');
            $totaldebet += $baris[jumla];
        } else {
            $pdf->Cell(20, 5, '', 0, 0, 'R');
            $pdf->Cell(20, 5, number_format(-1 * $baris[jumla]), 0, 0, 'R');
            $totalkredit -= $baris[jumla];
        }

        $selisih += $baris[jumla];
        $pdf->Cell(20, 5, number_format($selisih), 0, 1, 'R');
        $jurnalaktif = $baris[nojur];
    }
    if ('' !== $jurnalaktif) {
        $pdf->Cell(127, 5, 'Total', 0, 0, 'R');
        $pdf->Cell(20, 5, number_format($totaldebet), T, 0, 'R');
        $pdf->Cell(20, 5, number_format(-1 * $totalkredit), T, 0, 'R');
        $pdf->Cell(20, 5, number_format($selisih), T, 1, 'R');
        $grandtotaldebet += $totaldebet;
        $grandtotalkredit += $totalkredit;
        $grandtotalselisih += $selisih;
    }

    $pdf->Cell(127, 5, 'Grand Total', 0, 0, 'R');
    $pdf->Cell(20, 5, number_format($grandtotaldebet), T, 0, 'R');
    $pdf->Cell(20, 5, number_format(-1 * $grandtotalkredit), T, 0, 'R');
    $pdf->Cell(20, 5, number_format($grandtotalselisih), T, 1, 'R');
    $pdf->Output();
} else {
    echo 'Data tidak ditemukan.';
    exit();
}

?>