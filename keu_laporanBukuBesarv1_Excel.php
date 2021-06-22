<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$tanggal1 = $_GET['tanggal1'];
$tanggal2 = $_GET['tanggal2'];
$akundari = $_GET['akundari'];
$akunsampai = $_GET['akunsampai'];
$qwe = explode('-', $tanggal1);
$periode = $qwe[2].$qwe[1];
$bulan = $qwe[1];
$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$qwe = explode('-', $tanggal2);
$tanggal2 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namagudang = strtoupper($bar->namaorganisasi);
}
$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal\r\n    where kodeaplikasi = 'CLM'\r\n    ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $clm = $bar->noakundebet;
}
if ('' === $gudang) {
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

$str = 'select * from '.$dbname.".keu_saldobulanan where noakun != '".$clm."' and periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang.' order by noakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = 'awal'.$bulan;
    $saldoawal[$bar->noakun] += $bar->$qwe;
    $aqun[$bar->noakun] = $bar->noakun;
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

    if ('' === $bar->kodeblok) {
        $org = $bar->kodeorg;
    } else {
        $org = substr($bar->kodeblok, 0, 6);
    }

    $isidata[$qwe][organ] = $org;
    $isidata[$qwe][noref] = $bar->noreferensi;
    $isidata[$qwe][kosup] = $bar->kodesupplier;
    $isidata[$qwe][nodok] = $bar->nodok;
    $aqun[$bar->noakun] = $bar->noakun;
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n    where level = '5' and noakun!='".$clm."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
$str = 'select supplierid, namasupplier from '.$dbname.".log_5supplier\r\n    ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namasupplier[$bar->supplierid] = $bar->namasupplier;
}
$aresta = 'SELECT kodeorg, tahuntanam FROM '.$dbname.".setup_blok\r\n    ";
$query = mysql_query($aresta);
while ($res = mysql_fetch_assoc($query)) {
    $tahuntanam[$res['kodeorg']] = $res['tahuntanam'];
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

$stream = strtoupper($_SESSION['lang']['laporanbukubesar']).' : '.$namapt.' '.$namagudang.'<br>'.strtoupper($_SESSION['lang']['tanggal']).' : '.tanggalnormal($tanggal1).' s/d '.tanggalnormal($tanggal2).'<br>'.strtoupper($_SESSION['lang']['noakun']).' : '.$akundari.' s/d '.$akunsampai."<br>\r\n    <table border=1>\r\n    <thead>\r\n    <tr bgcolor='#dedede'>\r\n        <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n        <td align=center>".$_SESSION['lang']['nojurnal']."</td>\r\n        <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n        <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n        <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n        <td align=center>".$_SESSION['lang']['debet']."</td>\r\n        <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n        <td align=center>".$_SESSION['lang']['saldo']."</td>\r\n        <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td align=center>".$_SESSION['lang']['kodeblok']."</td><td align=center>Kode Kendaraan</td>\r\n <td align=center>".$_SESSION['lang']['tahuntanam']."</td>\r\n        <td align=center>".$_SESSION['lang']['noreferensi']."</td>\r\n        <td align=center>".$_SESSION['lang']['namasupplier']."</td>\r\n        <td align=center>".$_SESSION['lang']['nodok']."</td>\r\n    </tr>  \r\n    </thead>\r\n    <tbody id=container>";
$no = 0;
if (!empty($aqun)) {
    foreach ($aqun as $akyun) {
        $subsalwal = $saldoawal[$akyun];
        $totaldebet = 0;
        $totalkredit = 0;
        $subsalak = $subsalwal;
        $salwal = $subsalwal;
        $grandsalwal += $subsalwal;
        $stream .= "<tr bgcolor='#dedede'>";
        $stream .= '<td align=right colspan=3></td>';
        $stream .= '<td>'.$akyun.'</td>';
        $stream .= '<td colspan=3>'.$namaakun[$akyun].'</td>';
        
        if ($salwal < 0) {
            $stream .= '<td align=right>'.number_format($salwal * -1,2).'</td>';
        } else {
            $stream .= '<td align=right>'.number_format($salwal,2).'</td>';
        }

        $stream .= '<td colspan=6></td>';
        $stream .= '</tr>';
        if (!empty($isidata)) {
            foreach ($isidata as $baris) {
                if ($baris[noaku] === $akyun) {
                    ++$no;
                    $stream .= '<tr>';
                    $stream .= '<td>'.$no.'</td>';
                    $stream .= '<td>'.substr($baris[nojur], 14, 8).'</td>';
                    $stream .= '<td>'.$baris[tangg].'</td>';
                    $stream .= '<td>'.$baris[noaku].'</td>';
                    $stream .= '<td>'.$baris[keter].'</td>';
                    $stream .= '<td align=right>'.number_format($baris[debet],2).'</td>';
                    $totaldebet += $baris[debet];
                    $grandtotaldebet += $baris[debet];
                    $stream .= '<td align=right>'.number_format($baris[kredi],2).'</td>';
                    $totalkredit += $baris[kredi];
                    $grandtotalkredit += $baris[kredi];
                    $cekakun=substr($baris[noaku],0,1);
                    
                    if($cekakun=='2'){
                        $salwal = ($salwal + $baris[debet]) - $baris[kredi];
                    } else{
                        $salwal = ($salwal + $baris[debet]) - $baris[kredi];
                    }

                    if ($salwal < 0) {
                        $stream .= '<td align=right><strong style=color:red;>'.number_format($salwal * -1,2).'</strong></td>';
                    } else {
                        $stream .= '<td align=right>'.number_format($salwal,2).'</td>';
                    }

                    $stream .= '<td>'.$baris[organ].'</td>';
                    $stream .= '<td>'.$baris[kodeb].'</td>';
                    $stream .= '<td>'.$baris[kodevhc].'</td>';
                    $stream .= '<td>'.$tahuntanam[$baris[kodeb]].'</td>';
                    $stream .= '<td>'.$baris[noref].'</td>';
                    $stream .= '<td>'.$namasupplier[$baris[kosup]].'</td>';
                    $stream .= '<td>'.$baris[nodok].'</td>';
                    $stream .= '</tr>';
                    $subsalak = $salwal;
                }
            }
        }

        $stream .= "<tr bgcolor='#dedede'>";
        $stream .= '<td align=right colspan=5>SubTotal</td>';
        $stream .= '<td align=right>'.number_format($totaldebet,2).'</td>';
        $stream .= '<td align=right>'.number_format($totalkredit,2).'</td>';
        if ($subsalak < 0) {
            $stream .= '<td align=right><strong style=color:red;>'.number_format($subsalak * -1,2).'</strong></td>';
        } else {
            $stream .= '<td align=right>'.number_format($subsalak,2).'</td>';
        }

        $stream .= '<td colspan=6></td>';
        $stream .= '</tr>';
    }
}

$grandsalak = ($grandsalwal + $grandtotaldebet) - $grandtotalkredit;
$stream .= "<tr bgcolor='#dedede'>";
$stream .= '<td align=right colspan=5>GrandTotal</td>';
$stream .= '<td align=right>'.number_format($grandtotaldebet,2).'</td>';
$stream .= '<td align=right>'.number_format($grandtotalkredit,2).'</td>';
if ($grandsalak < 0) {
 //   $stream .= '<td align=right><strong style=color:red;>'.number_format($grandsalak * -1).'</strong></td>';
} else {
 //   $stream .= '<td align=right>'.number_format($grandsalak).'</td>';
}

$stream .= '<td colspan=6></td>';
$stream .= '</tr>';
$stream .= "</tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>";
$stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'Laporan_BukuBesar_'.$pt.$gudang.' '.$qwe;
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>