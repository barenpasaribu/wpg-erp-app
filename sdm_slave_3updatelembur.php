<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
('' == $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
('' == $_POST['thnId'] ? ($thnId = $_GET['thnId']) : ($thnId = $_POST['thnId']));
('' == $_POST['kdProj'] ? ($kdProj = $_GET['kdProj']) : ($kdProj = $_POST['kdProj']));
$tipe = 'PNN';
$unitId = $_SESSION['lang']['all'];
$dktlmpk = $_SESSION['lang']['all'];
if ('preview' == $proses) {
    if ($_POST['tanggal2'] < $_POST['tanggal1']) {
        exit('error: Tolong gunakan urutan tanggal yang benar');
    }

    $tglPP = explode('-', $_POST['tanggal1']);
    list($date1, $month1, $year1) = $tglPP;
    $tgl2 = $_POST['tanggal2'];
    $pecah2 = explode('-', $tgl2);
    list($date2, $month2, $year2) = $pecah2;
    $jd1 = gregoriantojd($month1, $date1, $year1);
    $jd2 = gregoriantojd($month2, $date2, $year2);
    $jmlHari = $jd2 - $jd1;
    if ('' == $_POST['tanggal1'] || '' == $_POST['tanggal2']) {
        exit('error: '.$_SESSION['lang']['tanggal'].'1 dan '.$_SESSION['lang']['tanggal'].' 2 tidak boleh kosong');
    }
}

('' == $_POST['tanggal1'] ? ($tanggal1 = $_GET['tanggal1']) : ($tanggal1 = $_POST['tanggal1']));
('' == $_POST['tanggal2'] ? ($tanggal2 = $_GET['tanggal2']) : ($tanggal2 = $_POST['tanggal2']));
$tangsys1 = putertanggal($tanggal1);
$tangsys2 = putertanggal($tanggal2);
$wheretang = " b.tanggal like '%%' ";
if ('' != $tanggal1) {
    $wheretang = " b.tanggal = '".$tangsys1."' ";
    if ('' != $tanggal2) {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

if ('' != $tanggal2) {
    $wheretang = " b.tanggal = '".$tangsys2."' ";
    if ('' != $tanggal1) {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

$arr = '##kdOrg##tanggal1##tanggal2';
if ('preview' == $proses || 'excel' == $proses) {
    $brdr = 0;
    $bgcoloraja = '';
    if ('excel' == $proses) {
        $brdr = 1;
        $bgcoloraja = 'green';
    }

    $sData = 'select * from '.$dbname.".sdm_lemburdt where left(kodeorg,4)='".$kdOrg."' and tanggal between '".$tangsys1."' and '".$tangsys2."' order by tanggal,kodeorg asc";
    $qData = mysql_query($sData);
    $rowdt = mysql_num_rows($qData);
    if ('HO_ITGS' == $_SESSION['empl']['bagian']) {
        $tab .= '<button class=mybutton onclick=postingDat('.$rowdt.")  id=revTmbl>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
    } else {
        $tab .= "<button class=mybutton onclick=zExcel(event,'sdm_slave_3updatelembur.php','".$arr."')>Excel</button>";
    }

    $optTpLembur = ['normal', 'minggu', 'hari libur bukan minggu', 'hari raya'];
    $regData = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
    $tab .= '<table cellspacing=1 border='.$brdr." class=sortable>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n        <td ".$bgcoloraja." align=center rowspan=2>No.</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['karyawanid']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['nik']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['namakaryawan']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tipelembur']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['jamaktual']."</td>\r\n        <td ".$bgcoloraja." align=center rowspan=2>Jam Lembur</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tipekaryawan']."</td>\r\n        \r\n        <td  align=center>Sebelum</td>\r\n        <td align=center>Sesudah</td></tr>\r\n        <tr><td ".$bgcoloraja.'>'.$_SESSION['lang']['uangkelebihanjam']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['uangkelebihanjam']."</td>\r\n        </tr>";
    $tab .= '</tr></thead><tbody>';
    while ($rData = mysql_fetch_assoc($qData)) {
        ++$nor;
        $whr = "karyawanid='".$rData['karyawanid']."'";
        $whrlm = "kodeorg='".substr($rData['kodeorg'], 0, 4)."' and tipelembur='".$rData['tipelembur']."' and jamaktual='".$rData['jamaktual']."'";
        $optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whr);
        $optNikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $whr);
        $optTipekary = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $whr);
        $optJamLembur = makeOption($dbname, 'sdm_5lembur', 'jamaktual,jamlembur', $whrlm);
        $tab .= '<tr class=rowcontent id=rowDt_'.$nor.'><td align=center>'.$nor.'</td>';
        $tab .= '<td><input type=hidden  id=karyawanid_'.$nor." value='".$rData['karyawanid']."'>'".$rData['karyawanid'].'</td>';
        $tab .= "<td>'".$optNikKar[$rData['karyawanid']].'</td>';
        $tab .= '<td>'.$optNmKar[$rData['karyawanid']].'</td>';
        $tab .= '<td id=tanggal_'.$nor.'>'.$rData['tanggal'].'</td>';
        $tab .= '<td id=tipelembur_'.$nor.'>'.$rData['tipelembur'].'</td>';
        $tab .= '<td align=right id=jamaktual_'.$nor.'>'.$rData['jamaktual'].'</td>';
        $tab .= '<td align=right>'.$optJamLembur[$rData['jamaktual']].'</td>';
        $tab .= '<td>'.$optTipekary[$rData['karyawanid']].'</td>';
        $tab .= '<td align=right>'.$rData['uangkelebihanjam'].'</td>';
        $sGt = 'select sum(jumlah) as gapTun from '.$dbname.".sdm_5gajipokok where karyawanid='".$rData['karyawanid']."' and idkomponen in (31,1,2) and tahun='".substr($rData['tanggal'], 0, 4)."'";
        $qGt = mysql_query($sGt);
        $rGt = mysql_fetch_assoc($qGt);
        $uangLembur = 0;
        if ('KALTENG' == $regData[$kdOrg]) {
            if (3 < $optTipekary[$rData['karyawanid']]) {
                $uangLembur = 0.15 * ($rGt['gapTun'] * $optJamLembur[$rData['jamaktual']]) / 173;
            } else {
                $uangLembur = ($rGt['gapTun'] * $optJamLembur[$rData['jamaktual']]) / 173;
            }
        } else {
            $uangLembur = ($rGt['gapTun'] * $optJamLembur[$rData['jamaktual']]) / 173;
        }

        $tab .= '<td align=right id=kelebihanjam_'.$nor.'>'.(int) $uangLembur.'</td></tr>';
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriodeAkut = 'select distinct periode from '.$dbname.".setup_periodeakuntansi \r\n                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
        $qPeriodeCari = mysql_query($sPeriodeAkut);
        while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
            $optPeriode .= "<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    case 'updateData':
        $scek = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".setup_periodeakuntansi where periode='".substr($_POST['tanggal'], 0, 7)."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        if ($rcek['tanggalmulai'] <= $_POST['tanggal'] && $_POST['tanggal'] <= $rcek['tanggalsampai']) {
            $supdate = 'update '.$dbname.".sdm_lemburdt set uangkelebihanjam='".$_POST['klbhanjam']."'"."where karyawanid='".$_POST['karyId']."' and jamaktual='".$_POST['jmaktual']."' and tanggal='".$_POST['tanggal']."'  and tipelembur='".$_POST['tplembur']."'";
            if (!mysql_query($supdate)) {
                exit('error: db bermasalah '.mysql_error($conn).'___'.$supdate);
            }

            break;
        }

        exit('error: tanggal di luar periode aktif');
    case 'excel':
        $thisDate = date('YmdHms');
        $nop_ = 'laporanUpdateBjr_'.$thisDate;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                       window.location='tempExcel/".$nop_.".xls.gz';\r\n                       </script>";

        break;
    default:
        break;
}
function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

?>