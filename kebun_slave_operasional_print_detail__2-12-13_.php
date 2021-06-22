<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$tipe = $_GET['tipe'];
$param = $_GET;
if ('EN' === $_SESSION['language']) {
    $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1');
} else {
    $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
}

$optSatKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optNamaKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNamaBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optGudang = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$cols = [];
$col1 = 'tanggal,kodekegiatan,a.kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$cols[] = explode(',', $col1);
$query = 'select '.$col1.' from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where a.notransaksi='".$param['notransaksi']."'";
$data[] = fetchData($query);
$align[] = explode(',', 'L,L,L,R,R,R,R,R');
$length[] = explode(',', '10,10,15,10,10,15,15,15');
$col2 = 'nik,absensi,jhk,umr,insentif';
$cols[] = explode(',', $col2);
$query = selectQuery($dbname, 'kebun_kehadiran', $col2, "notransaksi='".$param['notransaksi']."'");
$data[] = fetchData($query);
$align[] = explode(',', 'L,L,R,R,R');
$length[] = explode(',', '20,20,20,20,20');
$col3 = 'kodeorg,kodebarang,kwantitas,kwantitasha,hargasatuan';
$cols[] = explode(',', $col3);
$query = selectQuery($dbname, 'kebun_pakaimaterial', $col3, "notransaksi='".$param['notransaksi']."'");
$data[] = fetchData($query);
$align[] = explode(',', 'L,L,R,R,R');
$length[] = explode(',', '20,20,20,20,20');
$sDtKaryawn = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan order by namakaryawan asc';
$rData = fetchData($sDtKaryawn);
foreach ($rData as $brKary => $rNamakaryawan) {
    $RnamaKary[$rNamakaryawan['karyawanid']] = $rNamakaryawan['namakaryawan'];
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
    case 'TB':
        $title = 'UPKEEP-'.$_SESSION['lang']['tbm'];

        break;
    default:
        echo 'Error : Atribut not Defined';
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
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln();
        $pdf->Cell($width, $height, $_SESSION['lang']['notransaksi'].' : '.$param['notransaksi'], 0, 1, 'L', 1);
        $sPres = "select distinct sum(a.insentif) as upahpremi, sum(a.umr) as umr,sum(a.jhk) as jumlahhk,kodekegiatan,\r\n                tanggal,b.kodeorg,b.hasilkerja from ".$dbname.'.kebun_kehadiran a left join '.$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n                left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi where a.notransaksi='".$param['notransaksi']."' group by a.notransaksi";
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($width, $height, $titleDetail[0], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $pdf->Cell(25 / 100 * $width, $height, $_SESSION['lang']['kodekegiatan'], 1, 0, 'C', 1);
        $pdf->Cell(13 / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['hasilkerjad'], 1, 0, 'C', 1);
        $pdf->Cell(6 / 100 * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, $_SESSION['lang']['upahpremi'], 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, $_SESSION['lang']['umr'], 1, 1, 'C', 1);
        $qPres = mysql_query($sPres) ;
        $rPres = mysql_fetch_assoc($qPres);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(10 / 100 * $width, $height, tanggalnormal($rPres['tanggal']), 1, 0, 'C', 1);
        $pdf->Cell(25 / 100 * $width, $height, $optKegiatan[$rPres['kodekegiatan']], 1, 0, 'L', 1);
        $pdf->Cell(13 / 100 * $width, $height, $rPres['kodeorg'], 1, 0, 'L', 1);
        $pdf->Cell(10 / 100 * $width, $height, $rPres['hasilkerja'], 1, 0, 'R', 1);
        $pdf->Cell(6 / 100 * $width, $height, $optSatKegiatan[$rPres['kodekegiatan']], 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, number_format($rPres['upahpremi'], 0), 1, 0, 'R', 1);
        $pdf->Cell(15 / 100 * $width, $height, number_format($rPres['umr'], 0), 1, 1, 'R', 1);
        $sKhdrn = 'select distinct * from '.$dbname.".kebun_kehadiran where notransaksi='".$param['notransaksi']."'";
        $qKhdrn = mysql_query($sKhdrn) ;
        $pdf->Ln(30);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $titleDetail[1], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(5 / 100 * $width, $height, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(20 / 100 * $width, $height, $_SESSION['lang']['nik'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['absensi'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['jhk'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['umr'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['insentif'], 1, 1, 'C', 1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetFillColor(255, 255, 255);
        while ($rKhdrn = mysql_fetch_assoc($qKhdrn)) {
            ++$no;
            $pdf->Cell(5 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(20 / 100 * $width, $height, $optNamaKary[$rKhdrn['nik']], 1, 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rKhdrn['absensi'], 1, 0, 'C', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rKhdrn['jhk'], 1, 0, 'C', 1);
            $pdf->Cell(10 / 100 * $width, $height, number_format($rKhdrn['umr'], 0), 1, 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, number_format($rKhdrn['insentif'], 0), 1, 1, 'R', 1);
            $totHk += $rKhdrn['jhk'];
            $totUmr += $rKhdrn['umr'];
            $totIns += $rKhdrn['insentif'];
        }
        $pdf->Cell(35 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $totHk, 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($totUmr, 0), 1, 0, 'R', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($totIns, 0), 1, 1, 'R', 1);
        $pdf->Ln(30);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $titleDetail[2], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 8);
        $sMat = 'select distinct * from '.$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."'";
        $qMat = mysql_query($sMat) ;
        $pdf->Cell(5 / 100 * $width, $height, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(13 / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['kwantitas'], 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, $_SESSION['lang']['kwantitasha'], 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);
        $pdf->Cell(30 / 100 * $width, $height, $_SESSION['lang']['sloc'], 1, 1, 'C', 1);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetFillColor(255, 255, 255);
        while ($rMat = mysql_fetch_assoc($qMat)) {
            ++$no3;
            $pdf->Cell(5 / 100 * $width, $height, $no3, 1, 0, 'C', 1);
            $pdf->Cell(13 / 100 * $width, $height, $rMat['kodeorg'], 1, 0, 'C', 1);
            $pdf->Cell(10 / 100 * $width, $height, $optNamaBrg[$rMat['kodebarang']], 1, 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rMat['kwantitas'], 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $rMat['kwantitasha'], 1, 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rMat['hargasatuan'], 1, 0, 'R', 1);
            $pdf->Cell(30 / 100 * $width, $height, $optGudang[$rMat['kodegudang']], 1, 1, 'L', 1);
        }
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        $sAsis = 'select distinct nikmandor,nikmandor1,nikasisten,keranimuat,tanggal,kodeorg from '.$dbname.".kebun_aktifitas where notransaksi='".$param['notransaksi']."'";
        $qAsis = mysql_query($sAsis) ;
        $rAsis = mysql_fetch_assoc($qAsis);
        $pdf->ln(35);
        $pdf->Cell(85 / 100 * $width, $height, $rAsis['kodeorg'].','.tanggalnormal($rAsis['tanggal']), 0, 1, 'R', 0);
        $pdf->ln(35);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['dstujui_oleh'], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['diperiksa'], 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $_SESSION['lang']['dibuatoleh'], 0, 1, 'C', 0);
        $pdf->ln(65);
        $pdf->SetFont('Arial', 'U', 8);
        $pdf->Cell(28 / 100 * $width, $height, $RnamaKary[$rAsis['nikasisten']], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $RnamaKary[$rAsis['nikmandor1']], 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $RnamaKary[$rAsis['nikmandor']], 0, 1, 'C', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['asisten'], 0, 0, 'C', 0);
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['nikmandor1'], 0, 0, 'C', 0);
        $pdf->Cell(29 / 100 * $width, $height, $_SESSION['lang']['nikmandor'], 0, 1, 'C', 0);
        $pdf->Output();

        break;
    case 'excel':
        break;
    case 'html':
        $tab .= '<link rel=stylesheet type=text/css href=style/generic.css>';
        $tab .= '<fieldset><legend>'.$title.'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=65% class=sortable><tbody class=rowcontent>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeorganisasi'].'</td><td> :</td><td> '.$_SESSION['empl']['lokasitugas'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['notransaksi'].'</td><td> :</td><td> '.$param['notransaksi'].'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= '<br />'.$titleDetail[0].'<br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakegiatan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hasilkerjad'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['satuan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['upahpremi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['umr'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $sPres = "select distinct sum(a.insentif) as upahpremi, sum(a.umr) as umr,sum(a.jhk) as jumlahhk,kodekegiatan,\r\n                tanggal,b.kodeorg,b.hasilkerja from ".$dbname.'.kebun_kehadiran a left join '.$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n                left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi where a.notransaksi='".$param['notransaksi']."' group by a.notransaksi";
        $qPres = mysql_query($sPres) ;
        $rPres = mysql_fetch_assoc($qPres);
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.tanggalnormal($rPres['tanggal']).'</td>';
        $tab .= '<td>'.$rPres['kodeorg'].'</td>';
        $tab .= '<td>'.$optKegiatan[$rPres['kodekegiatan']].'</td>';
        $tab .= '<td>'.$rPres['hasilkerja'].'</td>';
        $tab .= '<td>'.$optSatKegiatan[$rPres['kodekegiatan']].'</td>';
        $tab .= '<td align=right>'.number_format($rPres['upahpremi'], 0).'</td>';
        $tab .= '<td align=right>'.number_format($rPres['umr'], 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</table>';
        $tab .= '<br />'.$titleDetail[1].'<br />';
        $sKhdrn = 'select distinct * from '.$dbname.".kebun_kehadiran where notransaksi='".$param['notransaksi']."'";
        $qKhdrn = mysql_query($sKhdrn) ;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['absensi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jhk'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['umr'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['insentif'].'</td>';
        $tab .= '</tr></thead><tbody>';
        while ($rKhdrn = mysql_fetch_assoc($qKhdrn)) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$optNamaKary[$rKhdrn['nik']].'</td>';
            $tab .= '<td>'.$rKhdrn['absensi'].'</td>';
            $tab .= '<td>'.$rKhdrn['jhk'].'</td>';
            $tab .= '<td  align=right>'.number_format($rKhdrn['umr'], 0).'</td>';
            $tab .= '<td  align=right>'.number_format($rKhdrn['insentif'], 0).'</td>';
            $tab .= '</tr>';
            $totJhk += $rKhdrn['jhk'];
            $totUmr += $rKhdrn['umr'];
            $totInsentif += $rKhdrn['insentif'];
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td  align=right>'.$totJhk.'</td>';
        $tab .= '<td  align=right>'.number_format($totUmr, 0).'</td>';
        $tab .= '<td  align=right>'.number_format($totInsentif, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</table><br />';
        $sMat = 'select distinct * from '.$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."'";
        $qMat = mysql_query($sMat) ;
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kwantitas'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kwantitasha'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['hargasatuan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['sloc'].'</td>';
        $tab .= '</tr></thead><tbody>';
        while ($rMat = mysql_fetch_assoc($qMat)) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$rMat['kodeorg'].'</td>';
            $tab .= '<td>'.$optNamaBrg[$rMat['kodebarang']].'</td>';
            $tab .= '<td>'.$rMat['kwantitas'].'</td>';
            $tab .= '<td>'.$rMat['kwantitasha'].'</td>';
            $tab .= '<td>'.$rMat['hargasatuan'].'</td>';
            $tab .= '<td>'.$optGudang[$rMat['kodegudang']].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</table><br />';
        echo $tab;

        break;
    default:
        break;
}

?>