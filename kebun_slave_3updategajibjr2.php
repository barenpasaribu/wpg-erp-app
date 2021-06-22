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
('' === $_POST['kdOrgb'] ? ($kdOrg = $_GET['kdOrgb']) : ($kdOrg = $_POST['kdOrgb']));
('' === $_POST['thnId'] ? ($thnId = $_GET['thnId']) : ($thnId = $_POST['thnId']));
('' === $_POST['kdKegiatan'] ? ($kdKegiatan = $_GET['kdKegiatan']) : ($kdKegiatan = $_POST['kdKegiatan']));
$tipe = 'PNN';
$unitId = $_SESSION['lang']['all'];
$dktlmpk = $_SESSION['lang']['all'];
if ('preview' === $proses) {
    if ($_POST['tanggal2b'] < $_POST['tanggal1b']) {
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
    if ('' === $_POST['tanggal1b'] || '' === $_POST['tanggal2b']) {
        exit('error: '.$_SESSION['lang']['tanggal'].'1 dan '.$_SESSION['lang']['tanggal'].' 2 tidak boleh kosong');
    }
}

('' === $_POST['tanggal1b'] ? ($tanggal1 = $_GET['tanggal1b']) : ($tanggal1 = $_POST['tanggal1b']));
('' === $_POST['tanggal2b'] ? ($tanggal2 = $_GET['tanggal2b']) : ($tanggal2 = $_POST['tanggal2b']));
$tangsys1 = putertanggal($tanggal1);
$tangsys2 = putertanggal($tanggal2);
$wheretang = " c.tanggal like '%%' ";
if ('' !== $tanggal1) {
    $wheretang = " c.tanggal = '".$tangsys1."' ";
    if ('' !== $tanggal2) {
        $wheretang = " c.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

if ('' !== $tanggal2) {
    $wheretang = " b.tanggal = '".$tangsys2."' ";
    if ('' !== $tanggal1) {
        $wheretang = " c.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

$arr2 = '##kdOrgb##tanggal1b##tanggal2b##kdKegiatan';
if ('preview' === $proses || 'excel' === $proses) {
    $brdr = 0;
    $bgcoloraja = '';
    if ('excel' === $proses) {
        $brdr = 1;
        $bgcoloraja = 'green';
    }

    if ('' !== $_POST['tipeTrk']) {
        $whre = " and tipetransaksi='".$_POST['tipeTrk']."'";
    }

    $sData = 'select distinct a.notransaksi,a.nik,a.umr,a.hasilkerja,a.jjg,b.kodeorg,b.kodekegiatan,a.jhk'.',c.tanggal,b.jumlahhk,b.hasilkerja as hsilKg,b.jjg as jjgprest from '.$dbname.'.kebun_kehadiran '.'a left join '.$dbname.".kebun_prestasi b\r\n               on a.notransaksi=b.notransaksi".' left join '.$dbname.".kebun_aktifitas c\r\n               on a.notransaksi=c.notransaksi \r\n               where  left(b.kodeorg,4)='".$kdOrg."' and c.jurnal=0  and b.kodekegiatan='".$kdKegiatan."'\r\n               and ".$wheretang."\r\n               ".$whre.' order by a.notransaksi asc';
    $qData = mysql_query($sData) ;
    $rowdt = mysql_num_rows($qData);
    if ('HO_ITGS' === $_SESSION['empl']['bagian']) {
        $tab .= '<button class=mybutton onclick=postingDat2('.$rowdt.")  id=revTmbl2>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr2.php','".$arr2."')>Excel</button>";
    } else {
        $tab .= "<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr2.php','".$arr2."')>Excel</button>";
    }

    $tab .= '<table cellspacing=1 border='.$brdr." class=sortable>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n        <td ".$bgcoloraja." align=center rowspan=2>No.</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['notransaksi']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['kodeblok']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['nik']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['namakaryawan']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tarif']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['jhk']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['jjg']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['hasilkerja']."</td>\r\n            <td colspan=5 align=center>Sebelum</td>\r\n            <td colspan=5 align=center>Sesudah</td></tr>\r\n        <tr><td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['kg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['upah']."</td>\r\n        \r\n        <td ".$bgcoloraja." align=center>HK</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['kg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['upah']."</td>\r\n        \r\n        <td ".$bgcoloraja." align=center>HK</td>\r\n         </tr>";
    $tab .= '</tr></thead><tbody>';
    while ($rData = mysql_fetch_assoc($qData)) {
        ++$nor;
        $rData['bjraktual'] = $rData['hasilkerja'] / $rData['jjg'];
        $whr = "karyawanid='".$rData['nik']."'";
        $reg = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
        $regionalDt = $reg[$kdOrg];
        $whrtr = "kodekegiatan='".$kdKegiatan."' and regional='".$regionalDt."'";
        $optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whr);
        $optNikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $whr);
        $optTarif = makeOption($dbname, 'kebun_5psatuan', 'kodekegiatan,rupiah', $whrtr);
        $tab .= '<tr class=rowcontent id=rowDt2_'.$nor.'><td align=center>'.$nor.'</td>';
        $tab .= '<td id=notransaksi2_'.$nor.'>'.$rData['notransaksi'].'</td>';
        $tab .= '<td id=kodeblok2_'.$nor.'>'.$rData['kodeorg'].'</td>';
        $tab .= "<td id='tanggal2_".$nor."'>".$rData['tanggal'].'</td>';
        $tab .= '<td><input type=hidden id=karyawanid2_'.$nor.' value='.$rData['nik'].' />'.$optNikKar[$rData['nik']].'</td>';
        $tab .= '<td>'.$optNmKar[$rData['nik']].'</td>';
        $tab .= '<td>'.$optTarif[$rData['kodekegiatan']].'</td>';
        $tab .= '<td  align=right>'.number_format($rData['jumlahhk'], 2).'</td>';
        $tab .= '<td  align=right>'.$rData['jjgprest'].'</td>';
        $tab .= '<td  align=right>'.number_format($rData['hsilKg'], 2).'</td>';
        $tab .= '<td align=right>'.number_format($rData['bjraktual'], 2).'</td>';
        $tab .= '<td align=right>'.$rData['jjg'].'</td>';
        $tab .= '<td align=right>'.number_format($rData['hasilkerja'], 2).'</td>';
        $tab .= '<td align=right>'.number_format($rData['umr'], 2).'</td>';
        $tab .= '<td align=right>'.number_format($rData['jhk'], 2).'</td>';
        if (substr($rData['kodeorg'], 0, 6) !== $afdDet) {
            $afdDet = substr($rData['kodeorg'], 0, 6);
            $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal \r\n                       FROM ".$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on \r\n                       a.nospb=b.nospb where blok like '".$afdDet."%'\r\n                       and tanggal <= '".$rData['tanggal']."' group by tanggal order by tanggal desc limit 1";
            $qBjr = mysql_query($sBjr) ;
            $rBjr = mysql_fetch_assoc($qBjr);
        }

        $tab .= '<td align=right id=brjAktual_'.$nor.'>'.number_format($rBjr['bjr'], 2).'</td>';
        $tab .= '<td align=right>'.$rData['jjg'].'</td>';
        $hasilKg = 0;
        $hasilKg = $rData['jjg'] * $rBjr['bjr'];
        $tab .= '<td align=right><input type=hidden id=hasilKg2_'.$nor.' value='.$hasilKg.' />'.number_format($hasilKg, 2).'</td>';
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$rData['nik'].' and tahun='.substr($rData['tanggal'], 0, 4).' and idkomponen in (1,31)');
        $Umr = fetchData($qUMR);
        $uphHarian = $Umr[0]['nilai'] / 25;
        if (0 === $uphHarian) {
            $uphHarian = 0;
            $insentif = 0;
        }

        $hk = 0;
        $upah = $hasilKg * $optTarif[$rData['kodekegiatan']];
        if ($uphHarian < $upah) {
            $hk = 1;
        } else {
            $hk = $upah / $uphHarian;
        }

        $tab .= '<td align=right><input type=hidden id=updUpah2_'.$nor.' value='.$upah.' />'.number_format($upah, 2).'</td>';
        $tab .= '<td align=right><input type=hidden id=hkData_'.$nor.' value='.$hk.' />'.number_format($hk, 2).'</td>';
        $tab .= '</tr>';
        $upah = 0;
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
        $qPeriodeCari = mysql_query($sPeriodeAkut) ;
        while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
            $optPeriode .= "<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    case 'updateData':
        foreach ($_POST['notrans'] as $rowdt => $isiRow) {
            $scek = 'select distinct * from '.$dbname.".kebun_aktifitas where notransaksi='".$isiRow."' and jurnal=1";
            $qcek = mysql_query($scek) ;
            $rcek = mysql_num_rows($qcek);
            if (1 === $rcek) {
                continue;
            }

            if ('' === $_POST['kdorg'][$rowdt] && '' === $_POST['nik'][$rowdt]) {
                continue;
            }

            $jhk = number_format($_POST['updHk'][$rowdt], 2);
            $jhk = str_replace(',', '', $jhk);
            $hslKg = number_format($_POST['hasilKg2'][$rowdt], 2);
            $hslKg = str_replace(',', '', $hslKg);
            $updupah = number_format($_POST['updUpah'][$rowdt], 2);
            $updupah = str_replace(',', '', $updupah);
            $suphadir = 'update '.$dbname.".kebun_kehadiran set jhk='".$jhk."',hasilkerja='".$hslKg."'".",umr='".$updupah."' where notransaksi='".$isiRow."' and nik='".$_POST['nik'][$rowdt]."'";
            if (!mysql_query($suphadir)) {
                $hk[$isiRow] += $jhk;
                $hslkerja[$isiRow] += $hslKg;
                $supdate = 'update '.$dbname.".kebun_prestasi set hasilkerja='".$hslkerja[$isiRow]."',"."jumlahhk='".$hk[$isiRow]."'"."where kodeorg='".$_POST['kdorg'][$rowdt]."' and notransaksi='".$isiRow."' and kodeorg='".$_POST['kdorg'][$rowdt]."'";
                if (!mysql_query($supdate)) {
                    exit('error: db bermasalah '.mysql_error($conn).'___'.$supdate);
                }
            } else {
                $hk[$isiRow] += $jhk;
                $hslkerja[$isiRow] += $hslKg;
                $supdate = 'update '.$dbname.".kebun_prestasi set hasilkerja='".$hslkerja[$isiRow]."',"."jumlahhk='".$hk[$isiRow]."'"."where kodeorg='".$_POST['kdorg'][$rowdt]."' and notransaksi='".$isiRow."' and kodeorg='".$_POST['kdorg'][$rowdt]."'";
                if (!mysql_query($supdate)) {
                    exit('error: db bermasalah '.mysql_error($conn).'___'.$supdate);
                }
            }
        }

        break;
    case 'excel':
        $thisDate = date('YmdHms');
        $nop_ = 'laporanUpdatePerawatan_'.$thisDate;
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