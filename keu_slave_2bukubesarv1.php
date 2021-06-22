<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$tanggal1 = $_POST['tanggal1'];
$tanggal2 = $_POST['tanggal2'];
$akundari = $_POST['akundari'];
$akunsampai = $_POST['akunsampai'];
if ('' == $tanggal1) {
    echo 'WARNING: silakan mengisi tanggal.';
    exit();
}

if ('' == $tanggal2) {
    echo 'WARNING: silakan mengisi tanggal.';
    exit();
}

if ('' == $akundari) {
    echo 'WARNING: silakan memilih akun.';
    exit();
}

if ('' == $akunsampai) {
    echo 'WARNING: silakan memilih akun.';
    exit();
}

$qwe = explode('-', $tanggal1);
$periode = $qwe[2].$qwe[1];
$bulan = $qwe[1];
$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$qwe = explode('-', $tanggal2);
$tanggal2 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
if ('' == $gudang) {
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

$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where kodeaplikasi = 'CLM'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $clm = $bar->noakundebet;
}
$str = 'select * from '.$dbname.".keu_saldobulanan where noakun != '".$clm."' and periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang.' order by noakun';

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = 'awal'.$bulan;
    $saldoawal[$bar->noakun] += $bar->$qwe;
    $aqun[$bar->noakun] = $bar->noakun;
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n    where level = '5' OR level = '7' and noakun!='".$clm."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
$aresta = 'SELECT kodeorg, tahuntanam FROM '.$dbname.".setup_blok\r\n    ";
$query = mysql_query($aresta);
while ($res = mysql_fetch_assoc($query)) {
    $tahuntanam[$res['kodeorg']] = $res['tahuntanam'];
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
    $isidata[$qwe][kodeb] = $bar->kodeblok;
    $isidata[$qwe][kodevhc] = $bar->kodevhc;
    if ('' == $bar->kodeblok) {
        $org = $bar->kodeorg;
    } else {
        $org = substr($bar->kodeblok, 0, 6);
    }

    $isidata[$qwe][organ] = $org;
    $aqun[$bar->noakun] = $bar->noakun;
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

$no = 0;
if (!empty($aqun)) {
    foreach ($aqun as $akyun) {
        $subsalwal = $saldoawal[$akyun];
        $totaldebet = 0;
        $totalkredit = 0;
        $subsalak = $subsalwal;
        
        $salwal = $subsalwal;
        $grandsalwal += $subsalwal;
        echo '<tr class=rowcontent><td align=right colspan=3></td>';
        echo '<td>'.$akyun.'</td>';
        echo '<td colspan=3>'.$namaakun[$akyun].'</td>';

        if($salwal<0){
            echo "<td align=right>"."(".number_format($salwal * -1,2).")".'</td>';
        }else{
            echo '<td align=right>'.number_format($salwal,2).'</td>';
        }
        echo '<td colspan=3></td></tr>';
        if (!empty($isidata)) {
            foreach ($isidata as $baris) {
                if ($baris[noaku] == $akyun) {
                    ++$no;
                    echo '<tr class=rowcontent>';
                    echo "<td style='width:40px;'>".$no.'</td>';
                    echo "<td style='width:100px;'>".$baris[nojur].'</td>';
                    echo "<td style='width:80px;'>".tanggalnormal($baris[tangg]).'</td>';
                    echo "<td style='width:100px;'>".$baris[noaku].'</td>';
                    echo "<td style='width:250px;'>".$baris[keter].'</td>';
                    echo "<td align=right style='width:100px;'>".number_format($baris[debet],2).'</td>';
                    $totaldebet += $baris[debet];
                    $grandtotaldebet += $baris[debet];
                    echo "<td align=right style='width:100px;'>".number_format($baris[kredi],2).'</td>';
                    $totalkredit += $baris[kredi];
                    $grandtotalkredit += $baris[kredi];

                    $cekakun=substr($baris[noaku],0,1);
                    if($cekakun=='2'){
                        $salwal = ($salwal + $baris[debet]) - $baris[kredi];
                    } else{
                        $salwal = ($salwal + $baris[debet]) - $baris[kredi];
                    }
                    if ($salwal < 0) {
//                        echo "<td align=right style='width:100px;'><strong style=color:red;>".number_format($salwal * -1).'</strong></td>';
                        echo "<td align=right style='width:100px;'>"."(".number_format($salwal * -1,2).")".'</td>';
                    } else {
                        echo "<td align=right style='width:100px;'>".number_format($salwal).'</td>';
                    }

                    echo "<td style='width:50px;'>".$baris[organ].'</td>';
                    echo "<td style='width:100px;'>".$baris[kodeb]. " | " .$baris[kodevhc].'</td>';
                    echo "<td style='width:40px;'>".$tahuntanam[$baris[kodeb]].'</td>';
                    echo '</tr>';
                    $subsalak = $salwal;
                }
            }
        }

        echo '<tr class=rowtitle><td align=right colspan=5>SubTotal</td>';
        echo "<td align=right style='width:100px;'>".number_format($totaldebet,2).'</td>';
        echo "<td align=right style='width:100px;'>".number_format($totalkredit,2).'</td>';
        if ($subsalak < 0) {
//            echo "<td align=right style='width:100px;'><strong style=color:red;><b>".number_format($subsalak * -1).'</b></strong></td>';
            echo "<td align=right style='width:100px;'>"."(".number_format($salwal * -1,2).")".'</td>';
        } else {
            echo "<td align=right style='width:100px;'><b>".number_format($subsalak,2).'</b></td>';
        }

        echo '<td colspan=3></td></tr>';
    }
}

$grandsalak = ($grandsalwal + $grandtotaldebet) - $grandtotalkredit;
echo '<tr class=rowtitle><td align=right colspan=5>GrandTotal</td>';
echo "<td align=right style='width:100px;'>".number_format($grandtotaldebet,2).'</td>';
echo "<td align=right style='width:100px;'>".number_format($grandtotalkredit,2).'</td>';
if ($grandsalak < 0) {
//    echo "<td align=right style='width:100px;'><strong style=color:red;><b>".number_format($grandsalak * -1).'</b></strong></td>';
 //   echo "<td align=right style='width:100px;'><b>"."(".number_format($grandsalak * -1).")".'</b></td>';
} else {
 //   echo "<td align=right style='width:100px;'><b>".number_format($grandsalak).'</b></td>';
}

echo '<td colspan=3></td></tr>';

?>