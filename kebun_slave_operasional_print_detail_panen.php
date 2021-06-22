<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$tipe = $_GET['tipe'];
$param = $_GET;
$cols = [];
$col1 = 'tanggal,nik,a.kodeorg,a.umr,hasilkerja,norma,upahkerja,upahpremi,rupiahpenalty,hasilkerjakg,brondolan,jumlahlbhbasis';
$cols[] = explode(',', $col1);
$query = 'select '.$col1.' from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where a.notransaksi='".$param['notransaksi']."'";
$data[] = fetchData($query);
$align[] = explode(',', 'L,L,L,R,R,R,R,R,R,R,R,R');
$length[] = explode(',', '10,10,15,10,10,15,15,15,10,10,15,10');
$sDtKaryawn = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan order by namakaryawan asc';
$rData = fetchData($sDtKaryawn);
foreach ($rData as $brKary => $rNamakaryawan) {
    $RnamaKary[$rNamakaryawan['karyawanid']] = $rNamakaryawan['namakaryawan'];
}
$sOrg = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi order by namaorganisasi asc';
$rDataOrg = fetchData($sOrg);
foreach ($rDataOrg as $brOrg => $rNamaOrg) {
    $rNmOrg[$rNamaOrg['kodeorganisasi']] = $rNamaOrg['namaorganisasi'];
}
switch ($tipe) {
    case 'LC':
        $title = 'Land Clearing';

        break;
    case 'BBT':
        $title = $_SESSION['lang']['pembibitan'];

        break;
    case 'TBM':
        $title = 'UPKEEP-'.$_SESSION['lang']['tbm'];

        break;
    case 'TM':
        $title = 'UPKEEP-'.$_SESSION['lang']['tm'];

        break;
    case 'PNN':
        $title = $_SESSION['lang']['panen'];

        break;
    default:
        echo 'Error : Attribut not defined';
        exit();
}
$titleDetail = [$_SESSION['lang']['prestasi'], $_SESSION['lang']['absensi'], $_SESSION['lang']['material']];
switch ($proses) {
    case 'pdf':
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->_noThead = true;
        $pdf->setAttr1($title, $align, $length, []);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $_SESSION['lang']['notransaksi'].' : '.$param['notransaksi'], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $pdf->Cell(13 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['kodeorg2'], 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, 'Panen (KG)', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, $_SESSION['lang']['brondolan2'], 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, $_SESSION['lang']['basiskg'], 1, 0, 'C', 1);
        $pdf->Cell(7 / 100 * $width, $height, $_SESSION['lang']['umr'], 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, $_SESSION['lang']['totalpremi2'], 1, 0, 'C', 1);
        $pdf->Cell(9 / 100 * $width, $height, $_SESSION['lang']['totupprem2'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['rupiahpenalty'], 1, 0, 'C', 1);
        $pdf->Cell(12 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 8);
        $qData = mysql_query($query) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($rData['tanggal']), 1, 0, 'C', 1);
            $pdf->Cell(13 / 100 * $width, $height, $RnamaKary[$rData['nik']], 1, 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rData['kodeorg'], 1, 0, 'C', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format($rData['hasilkerjakg'], 0), 1, 0, 'R', 1);
            $pdf->Cell(5 / 100 * $width, $height, number_format($rData['brondolan'], 0), 1, 0, 'R', 1);
            $pdf->Cell(8 / 100 * $width, $height, $rData['norma'], 1, 0, 'R', 1);
            $pdf->Cell(7 / 100 * $width, $height, number_format($rData['umr'], 0), 1, 0, 'R', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format($rData['upahpremi'], 0), 1, 0, 'R', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($rData['upahkerja'], 0), 1, 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, number_format($rData['rupiahpenalty'], 0), 1, 0, 'R', 1);
            $sisa = $rData['upahkerja'] - $rData['rupiahpenalty'];
            $pdf->Cell(12 / 100 * $width, $height, number_format($sisa, 0), 1, 1, 'R', 1);
            $totJanjang += $rData['hasilkerjakg'];
            $totBrondol += $rData['brondolan'];
            $totbasis = $rData['norma'];
            $totumr += $rData['umr'];
            $totUpahKerja += $rData['upahkerja'];
            $totUpahPremi += $rData['upahpremi'];
            $totUpahDenda += $rData['rupiahpenalty'];
            $totSisa += $sisa;
        }
        $pdf->Cell(31 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($totJanjang, 0), 1, 0, 'R', 1);
        $pdf->Cell(5 / 100 * $width, $height, number_format($totBrondol, 0), 1, 0, 'R', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($totbasis, 0), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($totumr, 0), 1, 0, 'R', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($totUpahPremi, 0), 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, number_format($totUpahKerja, 0), 1, 0, 'R', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($totUpahDenda, 0), 1, 0, 'R', 1);
        $pdf->Cell(12 / 100 * $width, $height, number_format($totSisa, 0), 1, 1, 'R', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        $sAsis = 'select distinct nikmandor,nikmandor1,asistenpanen,nikasisten,keranimuat,tanggal,kodeorg from '.$dbname.".kebun_aktifitas where notransaksi='".$param['notransaksi']."'";
        $qAsis = mysql_query($sAsis) ;
        $rAsis = mysql_fetch_assoc($qAsis);
        $pdf->ln(10);
        $pdf->Cell(85 / 100 * $width, $height, $rAsis['kodeorg'].','.tanggalnormal($rAsis['tanggal']), 0, 1, 'R', 0);
        $pdf->ln(35);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['disetujui'], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['diperiksa'], 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $_SESSION['lang']['dbuat_oleh'], 0, 1, 'C', 0);
        $pdf->ln(65);
        $pdf->SetFont('Arial', 'U', 8);
//        $pdf->Cell(28 / 100 * $width, $height, $RnamaKary[$rAsis['nikmandor']], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $RnamaKary[$rAsis['asistenpanen']], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $RnamaKary[$rAsis['nikasisten']], 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $RnamaKary[$rAsis['keranimuat']], 0, 1, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
//      $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['mandor'], 0, 0, 'C', 0);
//      $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['nikasisten'], 0, 0, 'C', 0);
		$pdf->Cell(28 / 100 * $width, $height, 'Asisten', 0, 0, 'C', 0);
		$pdf->Cell(28 / 100 * $width, $height, 'Kerani Divisi/Estate', 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $_SESSION['lang']['keraniproduksi'], 0, 1, 'C', 0);
        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}

?>