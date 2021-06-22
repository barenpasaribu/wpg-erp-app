<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/sdm_2rekapabsen.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$karyawanid = $_GET['karyawanid'];
$tanggal = $_GET['tanggal'];
$str = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$karyawanid."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namakaryawan[$bar->karyawanid] = $bar->namakaryawan;
}
$strz = 'select notransaksi, tanggal, karyawanid,(upahpremi-rupiahpenalty) as upahpremi from '.$dbname.".kebun_prestasi_vw\r\n     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'\r\n     order by notransaksi";
$resz = mysql_query($strz);
while ($barz = mysql_fetch_object($resz)) {
    $notran['BKM:'.$barz->notransaksi] .= 'BKM:'.$barz->notransaksi;
    $premi['BKM:'.$barz->notransaksi] = $barz->upahpremi;
}
$strx = 'select notransaksi,karyawanid,tanggal,(insentif) as upahpremi from '.$dbname.".kebun_kehadiran_vw\r\n     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'\r\n     order by notransaksi";
$resx = mysql_query($strx);
while ($barx = mysql_fetch_object($resx)) {
    $notran['BKM:'.$barx->notransaksi] = 'BKM:'.$barx->notransaksi;
    $premi['BKM:'.$barx->notransaksi] = $barx->upahpremi;
}
$stry = 'select karyawanid,tanggal,(premiinput) as upahpremi from '.$dbname.".kebun_premikemandoran \r\n     where tanggal like '".$tanggal."%' and karyawanid = '".$karyawanid."'\r\n     order by tanggal";
$resy = mysql_query($stry);
while ($bary = mysql_fetch_object($resy)) {
    $notran['PREMI KEMANDORAN:'.$bary->tanggal] = 'PREMI KEMANDORAN:'.$bary->tanggal;
    $premi['PREMI KEMANDORAN:'.$bary->tanggal] = $bary->upahpremi;
}
$strv = 'select notransaksi,idkaryawan as karyawanid,tanggal,(premi-penalty) as upahpremi from '.$dbname.".vhc_runhk \r\n     where tanggal like '".$tanggal."%' and idkaryawan = '".$karyawanid."'\r\n     order by notransaksi";
$resv = mysql_query($strv);
while ($barv = mysql_fetch_object($resv)) {
    $notran['TRAKSI:'.$barv->notransaksi] = 'TRAKSI:'.$barv->notransaksi;
    $premi['TRAKSI:'.$barv->notransaksi] = $barv->upahpremi;
}
if ('excel' != $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>Karyawan</td>\r\n          <td>No. Transaksi</td>\r\n          <td>Tanggal</td>\r\n          <td>Premi</td>";
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
if (empty($notran)) {
    $stream .= '<tr class=rowcontent>';
    $stream .= '<td colspan=4>Abensce</td>';
    $stream .= '</tr>';
} else {
    foreach ($notran as $kyu) {
        $stream .= '<tr class=rowcontent>';
        $stream .= '<td align=left>'.$namakaryawan[$karyawanid].'</td>';
        $stream .= '<td align=left>'.$kyu.'</td>';
        $stream .= '<td align=center>'.$tanggal.'</td>';
        $stream .= '<td align=right>'.number_format($premi[$kyu]).'</td>';
        $stream .= '</tr>';
    }
}

$stream .= '</tbody></table>';
echo $stream;

?>