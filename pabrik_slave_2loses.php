<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

('' === $_POST['tgl1'] ? ($tgl_1 = $_GET['tgl1']) : ($tgl_1 = $_POST['tgl1']));
('' === $_POST['tgl2'] ? ($tgl_2 = $_GET['tgl2']) : ($tgl_2 = $_POST['tgl2']));
('' === $_POST['tglRe'] ? ($tanggl = $_GET['tglRe']) : ($tanggl = $_POST['tglRe']));
('' === $_POST['kdPabrik'] ? ($kdPabrik = $_GET['kdPabrik']) : ($kdPabrik = $_POST['kdPabrik']));
if ('' === $tgl_1 || '' === $tgl_2) {
    exit('Error:Date required');
}

if ('' === $kdPabrik) {
    exit('Error: Mill code required');
}

if (10 !== strlen($tgl_1) || 10 !== strlen($tgl_2)) {
    exit('Error: Invalid date format');
}

$tgl1 = $tgl_1;
$tgl22 = $tgl_2;
$optSupp = makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$tgl = explode('-', $tgl_1);
$tgl_1 = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
$tgl2 = explode('-', $tgl_2);
$tgl_2 = $tgl2[2].'-'.$tgl2[1].'-'.$tgl2[0];
$dzArr = [];
$kmrn = strtotime('-1 day', strtotime($tgl_1));
$kmrn = date('Y-m-d', $kmrn);
if ('' !== $tgl_1 && '' !== $tgl_2) {
    $tgl1 = tanggalsystem($tgl1);
    $tgl22 = tanggalsystem($tgl22);
}

$test = dates_inbetween($tgl1, $tgl22);
$sData = 'select distinct * from '.$dbname.".pabrik_produksi where \r\n        kodeorg='".$kdPabrik."' and tanggal between '".$tgl_1."' and '".$tgl_2."' order by tanggal asc";
$qData = mysql_query($sData);
while ($rData = mysql_fetch_assoc($qData)) {
    $dtTgl[$rData['tanggal']] = $rData['tanggal'];
    $dtTbsDiolah[$rData['tanggal']] = $rData['tbsdiolah'];
    $dtFruit[$rData['tanggal']] = $rData['fruitineb'];
    $dtFibre[$rData['tanggal']] = $rData['fibre'];
    $dtEbstalk[$rData['tanggal']] = $rData['ebstalk'];
    $dtNut[$rData['tanggal']] = $rData['nut'];
    $dtEffluent[$rData['tanggal']] = $rData['effluent'];
    $dtSolid[$rData['tanggal']] = $rData['soliddecanter'];
    $dtFruitinebker[$rData['tanggal']] = $rData['fruitinebker'];
    $dtCyclone[$rData['tanggal']] = $rData['cyclone'];
    $dtLtds[$rData['tanggal']] = $rData['ltds'];
    $dtClaybath[$rData['tanggal']] = $rData['claybath'];
    $dtFruitinebker[$rData['tanggal']] = $rData['fruitinebker'];
    $dtCyclone[$rData['tanggal']] = $rData['cyclone'];
    $dtLtds[$rData['tanggal']] = $rData['ltds'];
    $dtLtds1[$rData['tanggal']] = $rData['ltds1'];
    $dtClaybath[$rData['tanggal']] = $rData['claybath'];
    $dtoer[$rData['tanggal']] = $rData['oer'];
    $dtffa[$rData['tanggal']] = $rData['ffa'];
    $dtkadarkotoran[$rData['tanggal']] = $rData['kadarkotoran'];
    $dtkadarair[$rData['tanggal']] = $rData['kadarair'];
    $dtoerpk[$rData['tanggal']] = $rData['oerpk'];
    $dtffapk[$rData['tanggal']] = $rData['ffapk'];
    $dtkadarkotoranpk[$rData['tanggal']] = $rData['kadarkotoranpk'];
    $dtkadarairpk[$rData['tanggal']] = $rData['kadarairpk'];
}
$brdr = 0;
if ('excel' === $proses) {
    $bgclr = ' bgcolor=#DEDEDE';
    $brdr = 1;
}

if ('preview' === $proses || 'excel' === $proses) {
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brdr.' class=sortable><thead>';
    $tab .= '<tr>';
    $tab .= '<td rowspan=3 '.$bgclr.'>'.$_SESSION['lang']['tanggal'].'</td>';
    $tab .= '<td colspan=2 rowspan=2 align=center '.$bgclr.'>FFB Processing (Kg.) </td>';
    $tab .= '<td  colspan=5 align=center  '.$bgclr.'>CPO Quality</td>';
    $tab .= '<td  colspan=5 align=center  '.$bgclr.'>KER Quality</td>';
    $tab .= '<td  colspan=14 align=center  '.$bgclr.'>CPO Loses</td>';
    $tab .= '<td  colspan=12 align=center  '.$bgclr.'>Kernel Loses</td></tr><tr>';
    $tab .= '<td align=center rowspan=2   '.$bgclr.'>'.$_SESSION['lang']['cpo']." (Kg)</td>\r\n        <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['oer']." (%)</td>\r\n        <td align=center rowspan=2  ".$bgclr.">(FFa)(%)</td>\r\n        <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['kotoran']." (%)</td>\r\n        <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['kadarair'].' (%)</td>';
    $tab .= ' <td align=center rowspan=2  '.$bgclr.'>'.$_SESSION['lang']['kernel']." (Kg)</td>\r\n\t\t   <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['oer']." (%)</td>\r\n\t\t   <td align=center rowspan=2  ".$bgclr.">(FFa) (%)</td>\r\n\t\t   <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['kotoran']." (%)</td>\r\n\t\t   <td align=center rowspan=2  ".$bgclr.'>'.$_SESSION['lang']['kadarair'].' (%)</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>USB</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Empty Bunch</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Fibre Press</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Nut</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Effluent</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Fruit in EB.</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Total</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Fruit in EB</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Fibre Cyclone</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>LTDS 1</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>LTDS 2</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Claybath</td>';
    $tab .= '<td colspan=2 align=center '.$bgclr.'>Total</td>';
    $tab .= '</tr><tr>';
    for ($arre = 1; $arre <= 14; ++$arre) {
        $tab .= '<td align=center '.$bgclr.'>HI</td>';
        $tab .= '<td align=center '.$bgclr.'>S/D</td>';
    }
    $tab .= '</tr></thead><tbody>';
    $ared = 0;
    $tgl = 1;
    foreach ($test as $ar => $dtTanggal) {
        if (0 === $ared) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td align=left>'.$dtTanggal.'</td>';
            $tab .= '<td align=right>'.number_format($dtTbsDiolah[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtTbsDiolah[$dtTanggal], 2).'</td>';
            $oerprsn = $dtoer[$dtTanggal] / $dtTbsDiolah[$dtTanggal] * 100;
            $tab .= '<td align=right>'.number_format($dtoer[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($oerprsn, 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtffa[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarkotoran[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarair[$dtTanggal], 2).'</td>';
            $oerpkprsn = $dtoerpk[$dtTanggal] / $dtTbsDiolah[$dtTanggal] * 100;
            $tab .= '<td align=right>'.number_format($dtoerpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($oerpkprsn, 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtffapk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarkotoranpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarairpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruit[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruit[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEbstalk[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEbstalk[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFibre[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFibre[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtNut[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtNut[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEffluent[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEffluent[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtSolid[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtSolid[$dtTanggal], 3).'</td>';
            $total[$dtTanggal] = $dtFruit[$dtTanggal] + $dtEbstalk[$dtTanggal] + $dtFibre[$dtTanggal] + $dtNut[$dtTanggal] + $dtEffluent[$dtTanggal] + $dtSolid[$dtTanggal];
            $tab .= '<td align=right>'.number_format($total[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruitinebker[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruitinebker[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtCyclone[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtCyclone[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds1[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds1[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtClaybath[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtClaybath[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total2[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total2[$dtTanggal], 3).'</td>';
            $tab .= '</tr>';
        } else {
            $total[$dtTanggal] = $dtFruit[$dtTanggal] + $dtEbstalk[$dtTanggal] + $dtFibre[$dtTanggal] + $dtNut[$dtTanggal] + $dtEffluent[$dtTanggal] + $dtSolid[$dtTanggal];
            $total2[$dtTanggal] = $dtFruitinebker[$dtTanggal] + $dtCyclone[$dtTanggal] + $dtLtds[$dtTanggal] + $dtLtds1[$dtTanggal] + $dtClaybath[$dtTanggal];
            $tglkmrn = strtotime('-1 day', strtotime($dtTanggal));
            $tglkmrn2 = date('Y-m-d', $tglkmrn);
            $des[$dtTanggal] = $des[$tglkmrn2] + $dtTbsDiolah[$dtTanggal];
            $dFruitSi[$dtTanggal] = ($dtFruit[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dFruitSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtEbstalkSi[$dtTanggal] = ($dtEbstalk[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtEbstalkSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtFibreSi[$dtTanggal] = ($dtFibre[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtFibreSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtNutSi[$dtTanggal] = ($dtNut[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtNutSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtEffluentSi[$dtTanggal] = ($dtEffluent[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtEffluentSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtSolidSi[$dtTanggal] = ($dtSolid[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtSolidSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtFruitinebkerSi[$dtTanggal] = ($dtFruitinebker[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtFruitinebkerSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtCycloneSi[$dtTanggal] = ($dtCyclone[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtCycloneSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtLtdsSi[$dtTanggal] = ($dtLtds[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtLtdsSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtLtdsSi1[$dtTanggal] = ($dtLtds1[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtLtdsSi1[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $dtClaybathSi[$dtTanggal] = ($dtClaybath[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $dtClaybathSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $totalSi[$dtTanggal] = ($total[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $totalSi[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $total2Si[$dtTanggal] = ($total2[$dtTanggal] * $dtTbsDiolah[$dtTanggal] + $total2Si[$tglkmrn2] * $des[$tglkmrn2]) / $des[$dtTanggal];
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td align=left>'.$dtTanggal.'</td>';
            $tab .= '<td align=right>'.number_format($dtTbsDiolah[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($des[$dtTanggal], 2).'</td>';
            $oerprsn = $dtoer[$dtTanggal] / $dtTbsDiolah[$dtTanggal] * 100;
            $tab .= '<td align=right>'.number_format($dtoer[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($oerprsn, 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtffa[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarkotoran[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarair[$dtTanggal], 2).'</td>';
            $oerpkprsn = $dtoerpk[$dtTanggal] / $dtTbsDiolah[$dtTanggal] * 100;
            $tab .= '<td align=right>'.number_format($dtoerpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($oerpkprsn, 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtffapk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarkotoranpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtkadarairpk[$dtTanggal], 2).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruit[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dFruitSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEbstalk[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEbstalkSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFibre[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFibreSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtNut[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtNutSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEffluent[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtEffluentSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtSolid[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtSolidSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($totalSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruitinebker[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtFruitinebkerSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtCyclone[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtCycloneSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtdsSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtds1[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtLtdsSi1[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtClaybath[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($dtClaybathSi[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total2[$dtTanggal], 3).'</td>';
            $tab .= '<td align=right>'.number_format($total2Si[$dtTanggal], 3).'</td>';
            $tab .= '</tr>';
        }

        ++$ared;
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'cpo_kernel_loses';
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getKodeorg':
        $optorg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        if (1 === $tipeIntex) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk ='PMO' order by namaorganisasi asc";
        } else {
            if (0 === $tipeIntex) {
                $sOrg = 'SELECT namasupplier,`kodetimbangan` FROM '.$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan!='NULL' order by namasupplier asc";
            } else {
                if (2 === $tipeIntex) {
                    $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk <>'PMO' order by namaorganisasi asc";
                }
            }
        }

        if (3 !== $tipeIntex) {
            $qOrg = mysql_query($sOrg);
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                if (0 !== $tipeIntex) {
                    $optorg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
                } else {
                    $optorg .= '<option value='.$rOrg['kodetimbangan'].'>'.$rOrg['namasupplier'].'</option>';
                }
            }
        }

        echo $optorg;

        break;
    default:
        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);

    return $dates_array;
}

?>