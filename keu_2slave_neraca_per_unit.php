<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
('' === $_POST['pt'] ? ($pt = $_GET['pt']) : ($pt = $_POST['pt']));
('' === $_POST['gudang'] ? ($gudang = $_GET['gudang']) : ($gudang = $_POST['gudang']));
('' === $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
('' === $_POST['periode1'] ? ($periode1 = $_GET['periode1']) : ($periode1 = $_POST['periode1']));
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
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
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where \r\n      kodeorganisasi in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' and tipe='KEBUN')";
$namagudang = $_SESSION['lang']['all'];
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $dtUnit[] = $bar->kodeorganisasi;
    $dtNama[$bar->kodeorganisasi] = $bar->namaorganisasi;
}
$CLM = '';
$str = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where kodeaplikasi='CLM'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $CLM = $bar->noakundebet;
}
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$lmperiode = mktime(0, 0, 0, substr($periode, 5, 2) - 1, 4, substr($periode, 0, 4));
$lmperiode = date('Y-m', $lmperiode);
$str = 'select distinct noakun,'.$zz.' from '.$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
$res = mysql_query($str);
$TAB = [];
while ($bar = mysql_fetch_object($res)) {
    if ('' !== $bar->noakun) {
        $TAB[$bar->noakun]['noakun'] = $bar->noakun;
        $TAB[$bar->noakun]['namaakun'] = $bar->namaakun;
    }
}
if ('' !== $pt) {
    $where = ' and kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."' and tipe='KEBUN')";
}

$str = 'select kodeorg,sum(awal'.substr(str_replace('-', '', $periode), 4, 2).') as sawal,noakun from '.$dbname.".keu_saldobulanan \r\n          where periode ='".str_replace('-', '', $periode)."'  and  noakun!='".$CLM."' ".$where.' group by kodeorg,noakun order by noakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('0' !== $bar->debet || '0' !== $bar->kredit) {
        $dt[$bar->noakun]['noakun'] = $bar->noakun;
        $dt[$bar->noakun.$bar->kodeorg]['sawal'] = $bar->sawal;
        $dt[$bar->noakun.$bar->kodeorg]['salak'] = $bar->sawal;
    }
}
if ('' !== $pt) {
    $str = 'select kodeorg,sum(debet) as debet,sum(kredit) as kredit,noakun from '.$dbname.".keu_jurnalsum_vw\r\n              where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in (select kodeorganisasi \r\n              from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4 and tipe='KEBUN')\r\n              and noakun!='".$CLM."' group by kodeorg,noakun";
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('0' !== $bar->debet || '0' !== $bar->kredit) {
        $dt[$bar->noakun]['noakun'] = $bar->noakun;
        $dt[$bar->noakun.$bar->kodeorg]['debet'] = $bar->debet;
        $dt[$bar->noakun.$bar->kodeorg]['kredit'] = $bar->kredit;
        $dt[$bar->noakun.$bar->kodeorg]['salak'] = ($dt[$bar->noakun.$bar->kodeorg]['sawal'] + $bar->debet) - $bar->kredit;
    }
}
$brd = 0;
$bgcolor = ' align=center';
if ('excel' === $proses) {
    $brd = 1;
    $bgcolor = ' align=center bgcolor=#DEDEDE';
}

$tab .= '<table cellpaddin=1 cellspacing=1 border='.$brd.' class=sortable><thead>';
$tab .= '<tr><td rowspan=2 '.$bgcolor.'>'.$_SESSION['lang']['noakun'].'</td><td rowspan=2 '.$bgcolor.'>'.$_SESSION['lang']['namaakun'].'</td>';
foreach ($dtUnit as $lsUnit) {
    $tab .= '<td colspan=4 '.$bgcolor.'>'.$dtNama[$lsUnit].'</td>';
}
$tab .= '</tr></tr>';
foreach ($dtUnit as $lsUnit) {
    $tab .= '<td '.$bgcolor.'>'.$_SESSION['lang']['saldoawal'].'</td>';
    $tab .= '<td '.$bgcolor.'>'.$_SESSION['lang']['debet'].'</td>';
    $tab .= '<td '.$bgcolor.'>'.$_SESSION['lang']['kredit'].'</td>';
    $tab .= '<td '.$bgcolor.'>'.$_SESSION['lang']['saldoakhir'].'</td>';
}
$tab .= '</tr></thead><tbody>';
$no = 0;
foreach ($TAB as $baris => $data) {
    ++$no;
    $tab .= "<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' >\r\n    <td style='width:80px;'>".$data['noakun']."</td>    \r\n    <td style='width:430px;'>".$data['namaakun'].'</td>';
    foreach ($dtUnit as $lsUnit) {
        $tab .= "\r\n    <td align=right onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$lsUnit."',event);\">".number_format($dt[$data['noakun'].$lsUnit]['sawal'])."</td>\r\n    <td align=right onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$lsUnit."',event);\">".number_format($dt[$data['noakun'].$lsUnit]['debet'])."</td>\r\n    <td align=right onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$lsUnit."',event);\">".number_format($dt[$data['noakun'].$lsUnit]['kredit'])."</td>   \r\n    <td align=right onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$lsUnit."',event);\">".number_format($dt[$data['noakun'].$lsUnit]['salak']).'</td>    ';
        $sal_awal[$lsUnit] += $dt[$data['noakun'].$lsUnit]['sawal'];
        $sal_debet[$lsUnit] += $dt[$data['noakun'].$lsUnit]['debet'];
        $sal_kredit[$lsUnit] += $dt[$data['noakun'].$lsUnit]['kredit'];
        $sal_salak[$lsUnit] += $dt[$data['noakun'].$lsUnit]['salak'];
    }
    $tab .= '</tr>';
}
$tab .= "<tr class=rowcontent>\r\n <td colspan=2 align=center>TOTAL</td>";
foreach ($dtUnit as $lsUnit) {
    $tab .= '<td align=right>'.number_format($sal_awal[$lsUnit])."</td>\r\n    <td align=right>".number_format($sal_debet[$lsUnit])."</td>\r\n    <td align=right>".number_format($sal_kredit[$lsUnit])."</td>   \r\n    <td align=right>".number_format($sal_salak[$lsUnit]).'</td>';
}
$tab .= '</tr>';
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'neraca_per_unit_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                            window.location='tempExcel/".$nop_.".xls.gz';\r\n                            </script>";

        break;
    default:
        break;
}

?>