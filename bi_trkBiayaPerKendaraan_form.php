<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
$waktu = $param['tahun'];
$kodeorg = $param['pks'];
$akunkdari = '';
$akunksampai = '';
$strh = 'select distinct noakundebet,sampaidebet  from '.$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
$resh = mysql_query($strh);
while ($barh = mysql_fetch_object($resh)) {
    $akunkdari = $barh->noakundebet;
    $akunksampai = $barh->sampaidebet;
}
if ('' === $akunkdari || '' === $akunksampai) {
    exit('Error: Parameter jurnal untuk LPVHC(by kendaraan) belum dibuat');
}

echo "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n          <script language=javascript src='js/generic.js'></script>";
$str = 'select sum(debet-kredit) as jlh,kodevhc,left(tanggal,7) as periode from '.$dbname.".keu_jurnaldt_vw where\r\n       tanggal like '".$waktu."%' and (noakun between '".$akunkdari."' and '".$akunksampai."')   \r\n      and (noreferensi NOT LIKE '%ALK_KERJA_AB%' or noreferensi is NULL)  \r\n      group by kodevhc,left(tanggal,7)";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $real[$bar->periode][$bar->kodevhc] = $bar->jlh;
}
$str = 'select sum(a.jumlah) as km, b.kodevhc,left(b.tanggal,7) as periode from '.$dbname.".vhc_rundt a\r\n            left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi\r\n            and tanggal like '".$waktu."%' group by kodevhc,left(b.tanggal,7)";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $realHM[$bar->periode][$bar->kodevhc] = $bar->km;
}
$str = "SELECT a.kodevhc,\r\n   sum(rp01) as rp01,\r\n   sum(rp02) as rp02,\r\n   sum(rp03) as rp03,\r\n   sum(rp04) as rp04,\r\n   sum(rp05) as rp05,\r\n   sum(rp06) as rp06,\r\n   sum(rp07) as rp07,\r\n   sum(rp08) as rp08,\r\n   sum(rp09) as rp09,\r\n   sum(rp10) as rp10,\r\n   sum(rp11) as rp11,\r\n   sum(rp12) as rp12\r\n    FROM ".$dbname.".bgt_budget_detail a\r\n   where a.kodevhc is not null and tipebudget='TRK' and tahunbudget='".$waktu."'\r\n   group by kodevhc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $bgtrp[$waktu.'-01'][$bar->kodevhc] = $bar->rp01;
    $bgtrp[$waktu.'-02'][$bar->kodevhc] = $bar->rp02;
    $bgtrp[$waktu.'-03'][$bar->kodevhc] = $bar->rp03;
    $bgtrp[$waktu.'-04'][$bar->kodevhc] = $bar->rp04;
    $bgtrp[$waktu.'-05'][$bar->kodevhc] = $bar->rp05;
    $bgtrp[$waktu.'-06'][$bar->kodevhc] = $bar->rp06;
    $bgtrp[$waktu.'-07'][$bar->kodevhc] = $bar->rp07;
    $bgtrp[$waktu.'-08'][$bar->kodevhc] = $bar->rp08;
    $bgtrp[$waktu.'-09'][$bar->kodevhc] = $bar->rp09;
    $bgtrp[$waktu.'-10'][$bar->kodevhc] = $bar->rp10;
    $bgtrp[$waktu.'-11'][$bar->kodevhc] = $bar->rp11;
    $bgtrp[$waktu.'-12'][$bar->kodevhc] = $bar->rp12;
}
$str = "SELECT a.kodevhc,\r\n   sum(jam01) as jam01,\r\n   sum(jam02) as jam02,\r\n   sum(jam03) as jam03,\r\n   sum(jam04) as jam04,\r\n   sum(jam05) as jam05,\r\n   sum(jam06) as jam06,\r\n   sum(jam07) as jam07,\r\n   sum(jam08) as jam08,\r\n   sum(jam09) as jam09,\r\n   sum(jam10) as jam10,\r\n   sum(jam11) as jam11,\r\n   sum(jam12) as jam12\r\n    FROM ".$dbname.".bgt_vhc_jam a  where tahunbudget='".$waktu."'\r\n   group by a.kodevhc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $bgtjam[$waktu.'-01'][$bar->kodevhc] = $bar->jam01;
    $bgtjam[$waktu.'-02'][$bar->kodevhc] = $bar->jam02;
    $bgtjam[$waktu.'-03'][$bar->kodevhc] = $bar->jam03;
    $bgtjam[$waktu.'-04'][$bar->kodevhc] = $bar->jam04;
    $bgtjam[$waktu.'-05'][$bar->kodevhc] = $bar->jam05;
    $bgtjam[$waktu.'-06'][$bar->kodevhc] = $bar->jam06;
    $bgtjam[$waktu.'-07'][$bar->kodevhc] = $bar->jam07;
    $bgtjam[$waktu.'-08'][$bar->kodevhc] = $bar->jam08;
    $bgtjam[$waktu.'-09'][$bar->kodevhc] = $bar->jam09;
    $bgtjam[$waktu.'-10'][$bar->kodevhc] = $bar->jam10;
    $bgtjam[$waktu.'-11'][$bar->kodevhc] = $bar->jam11;
    $bgtjam[$waktu.'-12'][$bar->kodevhc] = $bar->jam12;
}
if ('' === $kodeorg) {
    $str = 'select kodevhc,jenisvhc from '.$dbname.'.vhc_5master';
} else {
    $str = 'select kodevhc,jenisvhc from '.$dbname.".vhc_5master where kodetraksi like '".$kodeorg."%'";
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kodevhc[] = $bar->kodevhc;
    $jenisvhc[$bar->kodevhc] = $bar->jenisvhc;
}
echo 'Biaya Kendaraan/Alat Berat/Mesin '.$kodeorg.' Periode:'.substr($waktu, 0, 4)."                   \r\n                <table class=sortable cellspacing=1 border=0>\r\n               <thead><tr class=rowheader>\r\n               <td rowspan=3>".$_SESSION['lang']['urut']."</td>\r\n               <td rowspan=3>".$_SESSION['lang']['kodevhc']."</td>\r\n                <td rowspan=3>".$_SESSION['lang']['jenis']."</td>\r\n               <td colspan=6 align=center>Jan ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Feb ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Mar ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Apr ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Mei ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Jun ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Jul ".substr($waktu, 0, 4)."</td>    \r\n               <td colspan=6 align=center>Aug ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Sep ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Okt ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Nop ".substr($waktu, 0, 4)."</td>\r\n               <td colspan=6 align=center>Des ".substr($waktu, 0, 4)."</td>    \r\n               <td colspan=6 align=center>Total ".substr($waktu, 0, 4)."</td>\r\n               </tr>\r\n               <tr class=rowheader>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>\r\n               </tr>\r\n               <tr class=rowheader>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n                 <td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td><td align=center>Rp</td><td align=center>Hm/Km</td><td align=center>Rp/Sat.</td>\r\n               </tr>\r\n               </thead>\r\n               <tbody>";
$no = 0;
foreach ($kodevhc as $key => $val) {
    ++$no;
    $treal = 0;
    $trealHM = 0;
    $tbgtrp = 0;
    $tbgtjam = 0;
    echo "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$val."</td>\r\n                <td>".$jenisvhc[$val].'</td>';
    for ($kk = 1; $kk <= 12; ++$kk) {
        if ($kk < 10) {
            $zz = $waktu.'-0'.$kk;
        } else {
            $zz = $waktu.'-'.$kk;
        }

        $color = 'bgcolor=green';
        if ($bgtrp[$zz][$val] / $bgtjam[$zz][$val] < $real[$zz][$val] / $realHM[$zz][$val]) {
            $color = 'bgcolor=red';
        }

        echo '<td align=right>'.number_format($real[$zz][$val], 2).'</td><td align=right>'.number_format($realHM[$zz][$val], 2).'</td><td '.$color.'  align=right>'.@number_format($real[$zz][$val] / $realHM[$zz][$val], 2).'</td><td align=right>'.number_format($bgtrp[$zz][$val], 2).'</td><td align=right>'.number_format($bgtjam[$zz][$val], 2).'</td><td align=right bgcolor=#dedede>'.@number_format($bgtrp[$zz][$val] / $bgtjam[$zz][$val], 2).'</td>';
        $treal += $real[$zz][$val];
        $trealHM += $realHM[$zz][$val];
        $tbgtrp += $bgtrp[$zz][$val];
        $tbgtjam += $bgtjam[$zz][$val];
    }
    $color = 'bgcolor=green';
    if ($tbgtrp / $tbgtjam < $treal / $trealHM) {
        $color = 'bgcolor=red';
    }

    echo '<td align=right>'.number_format($treal, 2).'</td><td align=right>'.number_format($trealHM, 2).'</td><td '.$color.'  align=right>'.@number_format($treal / $trealHM, 2).'</td><td align=right>'.number_format($tbgtrp, 2).'</td><td align=right>'.number_format($tbgtjam, 2).'</td><td align=right bgcolor=#dedede>'.@number_format($tbgtrp / $tbgtjam, 2).'</td>';
    echo '</tr>';
}
echo "</tbody><tfoot>\r\n                </tfoot></table><a href=javascript:history.back(-1)>Back</a>";

?>