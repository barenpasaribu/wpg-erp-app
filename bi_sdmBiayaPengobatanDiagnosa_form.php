<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_bar.php';
$param = $_GET;
$waktu = $param['tahun'];
$kodeorg = $param['pks'];
echo "<link rel=stylesheet tyle=text href='style/generic.css'>\r\n          <script language=javascript src='js/generic.js'></script>";
if (!isset($param['diagnosa'])) {
    if ('' === $kodeorg) {
        $str = 'select sum(a.jlhbayar) as total,a.periode,b.id,b.diagnosa from '.$dbname.".sdm_pengobatanht a \r\n                   left join ".$dbname.".sdm_5diagnosa b on a.diagnosa=b.id \r\n                   where\r\n                   a.periode like '".substr($waktu, 0, 4)."%'\r\n                   group by a.periode,id order by total  desc,periode";
    } else {
        $str = 'select sum(a.jlhbayar) as total,a.periode,b.id,b.diagnosa from '.$dbname.".sdm_pengobatanht a \r\n                   left join ".$dbname.".sdm_5diagnosa b on a.diagnosa=b.id\r\n                   left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid    \r\n                   where\r\n                   a.periode like '".substr($waktu, 0, 4)."%' and c.lokasitugas='".$kodeorg."' \r\n                   group by a.periode,id order by total  desc,periode";
    }

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $diagnosa[$bar->id] = $bar->diagnosa;
        $nilai[$bar->id][$bar->periode] = $bar->total;
        $total[$bar->id] += $bar->total;
    }
    arsort($total);
    echo 'Biaya Pengobatan '.$kodeorg.' Periode:'.substr($waktu, 0, 4)." Berdasarkan Diagnosa:                    \r\n                <table class=sortable cellspacing=1 border=0>\r\n               <thead><tr class=rowheader>\r\n               <td>".$_SESSION['lang']['urut']."</td>\r\n               <td>".$_SESSION['lang']['diagnosa']."</td>\r\n               <td>Jan ".substr($waktu, 0, 4)."</td>\r\n               <td>Feb ".substr($waktu, 0, 4)."</td>\r\n               <td>Mar ".substr($waktu, 0, 4)."</td>\r\n               <td>Apr ".substr($waktu, 0, 4)."</td>\r\n               <td>Mei ".substr($waktu, 0, 4)."</td>\r\n               <td>Jun ".substr($waktu, 0, 4)."</td>\r\n               <td>Jul ".substr($waktu, 0, 4)."</td>    \r\n               <td>Aug ".substr($waktu, 0, 4)."</td>\r\n               <td>Sep ".substr($waktu, 0, 4)."</td>\r\n               <td>Okt ".substr($waktu, 0, 4)."</td>\r\n               <td>Nop ".substr($waktu, 0, 4)."</td>\r\n               <td>Des ".substr($waktu, 0, 4)."</td>    \r\n               <td>Total ".substr($waktu, 0, 4)."</td>\r\n               </tr></thead>\r\n               <tbody>";
    $no = 0;
    $GT = 0;
    foreach ($total as $key => $val) {
        ++$no;
        echo "<tr class=rowcontent style='cursor:pointer;' title='Click to Drill'  onclick=\"javascript:window.location='?pks=".$kodeorg.'&tahun='.$waktu.'&diagnosa='.$key.'&namadiagnosa='.$diagnosa[$key]."&jenis=rinci'\">\r\n               <td>".$no."</td>\r\n               <td>".$diagnosa[$key]."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-01'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-02'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-03'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-04'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-05'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-06'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-07'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-08'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-09'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-10'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-11'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-12'])."</td>  \r\n               <td align=right>".number_format($total[$key])."</td>\r\n               </tr>";
        $tt[substr($waktu, 0, 4).'-01'] += $nilai[$key][substr($waktu, 0, 4).'-01'];
        $tt[substr($waktu, 0, 4).'-02'] += $nilai[$key][substr($waktu, 0, 4).'-02'];
        $tt[substr($waktu, 0, 4).'-03'] += $nilai[$key][substr($waktu, 0, 4).'-03'];
        $tt[substr($waktu, 0, 4).'-04'] += $nilai[$key][substr($waktu, 0, 4).'-04'];
        $tt[substr($waktu, 0, 4).'-05'] += $nilai[$key][substr($waktu, 0, 4).'-05'];
        $tt[substr($waktu, 0, 4).'-06'] += $nilai[$key][substr($waktu, 0, 4).'-06'];
        $tt[substr($waktu, 0, 4).'-07'] += $nilai[$key][substr($waktu, 0, 4).'-07'];
        $tt[substr($waktu, 0, 4).'-08'] += $nilai[$key][substr($waktu, 0, 4).'-08'];
        $tt[substr($waktu, 0, 4).'-09'] += $nilai[$key][substr($waktu, 0, 4).'-09'];
        $tt[substr($waktu, 0, 4).'-10'] += $nilai[$key][substr($waktu, 0, 4).'-10'];
        $tt[substr($waktu, 0, 4).'-11'] += $nilai[$key][substr($waktu, 0, 4).'-11'];
        $tt[substr($waktu, 0, 4).'-12'] += $nilai[$key][substr($waktu, 0, 4).'-12'];
        $GT += $total[$key];
    }
    echo "</tbody><tfoot>\r\n                 <tr class=rowcontent>\r\n                     <td colspan=2>".$_SESSION['lang']['total']."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-01'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-02'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-03'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-04'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-05'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-06'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-07'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-08'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-09'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-10'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-11'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-12'])."</td>    \r\n                     <td align=right>".number_format($GT)."</td>\r\n                  </tr>\r\n                </tfoot></table>";
} else {
    if ('' === $kodeorg) {
        $str = 'select c.karyawanid as id,c.namakaryawan,sum(a.jlhbayar) as total,a.periode,c.lokasitugas from '.$dbname.".sdm_pengobatanht a \r\n                   left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid    \r\n                   where\r\n                   a.periode like '".substr($waktu, 0, 4)."%' and a.diagnosa=".$param['diagnosa']."\r\n                   group by a.periode,c.karyawanid order by total  desc,periode";
    } else {
        $str = 'select c.karyawanid as id,c.namakaryawan,sum(a.jlhbayar) as total,a.periode,c.lokasitugas from '.$dbname.".sdm_pengobatanht a \r\n                   left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid    \r\n                   where\r\n                   a.periode like '".substr($waktu, 0, 4)."%' and c.lokasitugas='".$kodeorg."' and a.diagnosa=".$param['diagnosa']."\r\n                   group by a.periode,c.karyawanid order by total  desc,periode";
    }

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $karyawanid[$bar->id] = $bar->namakaryawan;
        $lokasitugas[$bar->id] = $bar->lokasitugas;
        $nilai[$bar->id][$bar->periode] = $bar->total;
        $total[$bar->id] += $bar->total;
    }
    arsort($total);
    echo 'Biaya Pengobatan '.$kodeorg.' Periode:'.substr($waktu, 0, 4).' Berdasarkan Diagnosa: '.$param['namadiagnosa']."                   \r\n                <table class=sortable cellspacing=1 border=0>\r\n               <thead><tr class=rowheader>\r\n               <td>".$_SESSION['lang']['urut']."</td>\r\n               <td>".$_SESSION['lang']['nama']."</td>\r\n               <td>".$_SESSION['lang']['lokasitugas']."</td>\r\n               <td>Jan ".substr($waktu, 0, 4)."</td>\r\n               <td>Feb ".substr($waktu, 0, 4)."</td>\r\n               <td>Mar ".substr($waktu, 0, 4)."</td>\r\n               <td>Apr ".substr($waktu, 0, 4)."</td>\r\n               <td>Mei ".substr($waktu, 0, 4)."</td>\r\n               <td>Jun ".substr($waktu, 0, 4)."</td>\r\n               <td>Jul ".substr($waktu, 0, 4)."</td>    \r\n               <td>Aug ".substr($waktu, 0, 4)."</td>\r\n               <td>Sep ".substr($waktu, 0, 4)."</td>\r\n               <td>Okt ".substr($waktu, 0, 4)."</td>\r\n               <td>Nop ".substr($waktu, 0, 4)."</td>\r\n               <td>Des ".substr($waktu, 0, 4)."</td>    \r\n               <td>Total ".substr($waktu, 0, 4)."</td>\r\n               </tr></thead>\r\n               <tbody>";
    $no = 0;
    $GT = 0;
    foreach ($total as $key => $val) {
        ++$no;
        echo "<tr class=rowcontent>\r\n               <td>".$no."</td>\r\n               <td>".$karyawanid[$key].'</td><td>'.$lokasitugas[$key]."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-01'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-02'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-03'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-04'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-05'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-06'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-07'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-08'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-09'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-10'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-11'])."</td>\r\n                <td align=right>".number_format($nilai[$key][substr($waktu, 0, 4).'-12'])."</td>  \r\n               <td align=right>".number_format($total[$key])."</td>\r\n               </tr>";
        $tt[substr($waktu, 0, 4).'-01'] += $nilai[$key][substr($waktu, 0, 4).'-01'];
        $tt[substr($waktu, 0, 4).'-02'] += $nilai[$key][substr($waktu, 0, 4).'-02'];
        $tt[substr($waktu, 0, 4).'-03'] += $nilai[$key][substr($waktu, 0, 4).'-03'];
        $tt[substr($waktu, 0, 4).'-04'] += $nilai[$key][substr($waktu, 0, 4).'-04'];
        $tt[substr($waktu, 0, 4).'-05'] += $nilai[$key][substr($waktu, 0, 4).'-05'];
        $tt[substr($waktu, 0, 4).'-06'] += $nilai[$key][substr($waktu, 0, 4).'-06'];
        $tt[substr($waktu, 0, 4).'-07'] += $nilai[$key][substr($waktu, 0, 4).'-07'];
        $tt[substr($waktu, 0, 4).'-08'] += $nilai[$key][substr($waktu, 0, 4).'-08'];
        $tt[substr($waktu, 0, 4).'-09'] += $nilai[$key][substr($waktu, 0, 4).'-09'];
        $tt[substr($waktu, 0, 4).'-10'] += $nilai[$key][substr($waktu, 0, 4).'-10'];
        $tt[substr($waktu, 0, 4).'-11'] += $nilai[$key][substr($waktu, 0, 4).'-11'];
        $tt[substr($waktu, 0, 4).'-12'] += $nilai[$key][substr($waktu, 0, 4).'-12'];
        $GT += $total[$key];
    }
    echo "</tbody><tfoot>\r\n                 <tr class=rowcontent>\r\n                     <td colspan=3>".$_SESSION['lang']['total']."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-01'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-02'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-03'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-04'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-05'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-06'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-07'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-08'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-09'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-10'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-11'])."</td>\r\n                     <td align=right>".number_format($tt[substr($waktu, 0, 4).'-12'])."</td>    \r\n                     <td align=right>".number_format($GT)."</td>\r\n                  </tr>\r\n                </tfoot></table>";
    echo '<a href=javascript:history.back(-1)>Back</a>';
}

?>