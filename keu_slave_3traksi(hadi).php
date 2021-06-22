<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi where \r\n      kodeorg ='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
$tgmulai = '';
$tgsampai = '';
$periode = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tgsampai = $bar->tanggalsampai;
    $tgmulai = $bar->tanggalmulai;
    $periode = $bar->periode;
}
if ('' == $tgmulai || '' == $tgsampai) {
    exit('Error: Accounting period is not registered');
}

$str = 'select distinct a.notransaksi,a.jenispekerjaan from '.$dbname.".vhc_rundt a\r\n    left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi\r\n    where b.tanggal like '".$periode."%'\r\n    and a.jenispekerjaan not in (SELECT kodekegiatan FROM ".$dbname.'.vhc_kegiatan)';
$resf = mysql_query($str);
if (0 < mysql_num_rows($resf)) {
    echo "Error : There are Vehicle activity that do not have Account Number, Please contact administrator\n";
    while ($barf = mysql_fetch_object($resf)) {
        print_r($barf);
    }
    exit();
}

$str = 'select noakundebet,sampaidebet from '.$dbname.".keu_5parameterjurnal where kodeaplikasi='WS'";
$res = mysql_query($str);
$dariakun = '';
$sampaiakun = '';
while ($bar = mysql_fetch_object($res)) {
    $dariakun = $bar->noakundebet;
    $sampaiakun = $bar->sampaidebet;
}
if ('' == $dariakun || '' == $sampaiakun) {
    exit('Eror: Journalid for WS1 not found');
}

$str = 'select sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnaldt_vw  \r\n       where noakun >='".$dariakun."' and noakun<='".$sampaiakun."' \r\n       and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' \r\n       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."'\r\n       and (noreferensi not in('ALK_KERJA_AB','ALK_BY_WS') or noreferensi is NULL)";
$res = mysql_query($str);
$bybengkel = 0;
while ($bar = mysql_fetch_object($res)) {
    $bybengkel = $bar->jumlah;
}
$str = 'select * from '.$dbname.".msvhc_by_operator where posting=0 \r\n       and kodevhc in(select kodevhc from ".$dbname.".vhc_5master \r\n       where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')\r\n       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
$res = mysql_query($str);
$str1 = 'select * from '.$dbname.".vhc_runht where posting=0\r\n        and kodevhc in(select kodevhc from ".$dbname.".vhc_5master \r\n        where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')\r\n        and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
$res1 = mysql_query($str1);
if (0 < mysql_num_rows($res) || 0 < mysql_num_rows($res1)) {
    $t = 'Service:\\n';
    while ($bart = mysql_fetch_object($res)) {
        $t .= $bart->notransaksi."\n";
    }
    $t .= 'Pekerjaan:\\n';
    while ($bart = mysql_fetch_object($res1)) {
        $t .= $bart->notransaksi."\n";
    }
    exit("Error: there are transactions that have not posted:\n".$t);
}

$str = 'select sum(downtime) as dt,kodevhc from '.$dbname.".msvhc_by_operator \r\n       where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' \r\n       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and posting=1\r\n       group by kodevhc";
$res = mysql_query($str);
$kend = [];
$byrinci = [];
$totaljamservice = 0;
while ($bar = mysql_fetch_object($res)) {
    $totaljamservice += $bar->dt;
    $kend[$bar->kodevhc] = $bar->dt;
}
foreach ($kend as $key => $val) {
    $byrinci[$key] = $val / $totaljamservice * $bybengkel;
}
$biayattlkend = $byrinci;
$akunkdari = '';
$akunksampai = '';
$strh = 'select distinct noakundebet,sampaidebet  from '.$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
$resh = mysql_query($strh);
while ($barh = mysql_fetch_object($resh)) {
    $akunkdari = $barh->noakundebet;
    $akunksampai = $barh->sampaidebet;
}
if ('' == $akunkdari || '' == $akunksampai) {
    exit('Error: Journal parameter for LPVHC not found');
}

$str = 'select sum(debet-kredit) as jlh,kodevhc from '.$dbname.".keu_jurnaldt_vw where\r\n        kodevhc in(select kodevhc from ".$dbname.".vhc_5master \r\n        where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') \r\n        and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' \r\n        and nojurnal like '%".$_SESSION['empl']['lokasitugas']."%'\r\n        and (noakun between '".$akunkdari."' and '".$akunksampai."')   \r\n        and (noreferensi not in('ALK_KERJA_AB','ALK_BY_WS') or noreferensi is NULL) \r\n        group by kodevhc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $biayattlkend[$bar->kodevhc] += $bar->jlh;
}
$str = 'select sum(a.jumlah) as jlhjam,kodevhc from '.$dbname.".vhc_rundt a\r\n            left join ".$dbname.".vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan\r\n            left join ".$dbname.".vhc_runht c on a.notransaksi=c.notransaksi\r\n            where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' and alokasibiaya!='' \r\n            and jenispekerjaan!=''  and   \r\n            kodevhc in(select kodevhc from ".$dbname.".vhc_5master \r\n            where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')\r\n            group by kodevhc";
$res = mysql_query($str);
$biayaperjam = [];
while ($bar = mysql_fetch_object($res)) {
    $biayaperjam[$bar->kodevhc] = $biayattlkend[$bar->kodevhc] / $bar->jlhjam;
}
echo "<button  onclick=prosesAlokasi(1) id=btnproses>Process</button>
<font ><br>Note: If it does not work please reprocessing, the old data is automatically erased.</font>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td>No</td>
<td>Period</td>
<td>KodeVhc</td>
<td>Price/Hour</td>
<td>Type</td></tr></thead><tbody>";
$no = 0;
foreach ($byrinci as $key => $val) {
    ++$no;
    echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td>".$no."</td>\r\n                    <td id='periode".$no."'>".$_POST['periode']."</td>\r\n                    <td id='kodevhc".$no."'>".$key."</td>\r\n                    <td id='jumlah".$no."' align=right>".number_format($val, 2, '.', '')."</td>    \r\n                    <td id='jenis".$no."'>BYWS</td>\r\n                    </tr>";
}
foreach ($biayaperjam as $key => $jlh) {
    ++$no;
    echo "<tr class=rowcontent id='row".$no."'>\r\n                    <td>".$no."</td>\r\n                    <td id='periode".$no."'>".$_POST['periode']."</td>\r\n                    <td id='kodevhc".$no."'>".$key."</td>\r\n                    <td id='jumlah".$no."' align=right>".number_format($jlh, 2, '.', '')."</td>    \r\n                    <td id='jenis".$no."'>ALKJAM</td>\r\n                    </tr>";
}
echo '</tbody><tfoot></tfoot></table>';

?>