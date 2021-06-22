<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$revisi = $_GET['revisi'];
if ($periode1 < $periode) {
    $z = $periode;
    $periode = $periode1;
    $periode1 = $z;
}

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
$CLM = '';
$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where kodeaplikasi='CLM'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $CLM = $bar->noakundebet;
}
$lmperiode = mktime(0, 0, 0, substr($periode, 5, 2) - 1, 4, substr($periode, 0, 4));
$lmperiode = date('Y-m', $lmperiode);
if ('ID' == $_SESSION['language']) {
    $str = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
} else {
    $str = 'select distinct noakun,namaakun1 as namaakun from '.$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
}

$res = mysql_query($str);
$TAB = [];
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['noakun'] = $bar->noakun;
    $TAB[$bar->noakun]['namaakun'] = $bar->namaakun;
    $TAB[$bar->noakun]['sawal'] = 0;
    $TAB[$bar->noakun]['salak'] = 0;
}
if ('' == $gudang && '' != $pt) {
    $where = ' and kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    if ('' != $gudang) {
        $where = " and kodeorg ='".$gudang."'";
    } else {
        $where = '';
    }
}

$str = 'select sum(awal'.substr(str_replace('-', '', $periode), 4, 2).') as sawal,noakun from '.$dbname.".keu_saldobulanan \r\n      where periode ='".str_replace('-', '', $periode)."' and   noakun!='".$CLM."' ".$where.' group by noakun order by noakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['sawal'] = $bar->sawal;
    $TAB[$bar->noakun]['salak'] = $bar->sawal;
}
if ('' == $gudang && '' == $pt) {
    $str = 'select sum(debet) as debet,sum(kredit) as kredit,noakun from '.$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."'\r\n        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun";
} else {
    if ('' == $gudang && '' != $pt) {
        $str = 'select sum(debet) as debet,sum(kredit) as kredit,noakun from '.$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi \r\n        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)\r\n        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun";
    } else {
        $str = 'select sum(debet) as debet,sum(kredit) as kredit,noakun from '.$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'\r\n        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun";
    }
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $TAB[$bar->noakun]['debet'] = $bar->debet;
    $TAB[$bar->noakun]['kredit'] = $bar->kredit;
    $TAB[$bar->noakun]['salak'] = ($TAB[$bar->noakun]['sawal'] + $bar->debet) - $bar->kredit;
}
$no = 0;
$stream = strtoupper($_SESSION['lang']['neracasaldo']).' : '.$namapt.' '.$namagudang.'<br>'.$periode.' s/d '.$periode1."<table border=1>\r\n    <thead>\r\n    <tr bgcolor='#dedede'>\r\n        <td>".$_SESSION['lang']['nourut']."</td>\r\n        <td>".$_SESSION['lang']['noakun']."</td>\r\n        <td width=60px>".$_SESSION['lang']['namaakun']."</td>\r\n        <td>".$_SESSION['lang']['saldoawal']."</td>\r\n        <td>".$_SESSION['lang']['debet']."</td>\r\n        <td>".$_SESSION['lang']['kredit']."</td>\r\n        <td>".$_SESSION['lang']['saldoakhir']."</td>\r\n    </tr>  \r\n    </thead>\r\n    <tbody id=container>";
$sal_awal = 0;
$sal_debet = 0;
$sal_kredit = 0;
$sal_salak = 0;
foreach ($TAB as $baris => $data) {
    ++$no;
    $stream .= "<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$lmperiode."','".$pt."','".$gudang."',event);\">\r\n            <td>".$no."</td>\r\n            <td>".$data['noakun']."</td>    \r\n            <td>".$data['namaakun']."</td>\r\n            <td align=right>".$data['sawal']."</td>\r\n            <td align=right>".$data['debet']."</td>\r\n            <td align=right>".$data['kredit'].'</td>';
    if ($data['salak'] < 0) {
//        $stream .= "<td align=right style='width:130px;'><strong style=color:red;>".number_format($data['salak'] * -1).'</strong></td></tr>';
        $stream .= "<td align=right style='width:130px;'><strong style=color:red;>".$data['salak'].'</strong></td></tr>';
    } else {
        $stream .= "<td align=right style='width:130px;'>".$data['salak'].'</td></tr>';
    }

    $sal_awal += $data['sawal'];
    $sal_debet += $data['debet'];
    $sal_kredit += $data['kredit'];
    $sal_salak += $data['salak'];
}
$stream .= "<tr class=rowcontent>\r\n        <td colspan=3 align=center>TOTAL</td>\r\n        <td align=right>".$sal_awal."</td>\r\n        <td align=right>".$sal_debet."</td>\r\n        <td align=right>".$sal_kredit."</td>   \r\n        <td align=right>".$sal_salak."</td> \r\n    </tr>\r\n    </tbody>\r\n    <tfoot>\r\n    </tfoot>\t\t \r\n    </table>";
$qwe = date('YmdHms');
$nop_ = 'NeracaSaldo_'.$gudang.$periode.' '.$qwe;
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>