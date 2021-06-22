<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/pabrik_2pengolahan.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$notransaksi = $_GET['notransaksi'];
$str = 'select a.karyawanid as karyawanid, a.namakaryawan as namakaryawan, b.namajabatan as namajabatan from '.$dbname.".datakaryawan a\r\nleft join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n      where a.karyawanid in (select nik from ".$dbname.".kebun_kehadiran where notransaksi='".$notransaksi."')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kary[$bar->karyawanid][nm] = $bar->namakaryawan;
    $kary[$bar->karyawanid][jb] = $bar->namajabatan;
}
echo "<fieldset><legend>Print Excel</legend>\r\n     <img onclick=\"detailExcel(event,'kebun_slave_2pemeliharaandetail.php?type=excel&notransaksi=".$notransaksi."')\" src=images/excel.jpg class=resicon title='MS.Excel'>\r\n     </fieldset>";
if ('excel' !== $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
} else {
    $stream = '<table class=sortable border=1 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>No</td>\r\n          <td>Nama Karyawan</td>\r\n          <td>Jabatan</td>\r\n          <td>Upah</td>\r\n          <td>Premi</td>";
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
$str = 'select * from '.$dbname.".kebun_kehadiran\r\n              where notransaksi = '".$notransaksi."'";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n           <td align=right>".$no."</td>\r\n           <td>".$kary[$bar->nik][nm]."</td>    \r\n           <td>".$kary[$bar->nik][jb]."</td>    \r\n           <td align=right>".number_format($bar->umr)."</td>               \r\n           <td align=right>".number_format($bar->insentif).'</td>';
    $stream .= '</tr>';
}
$stream .= '</tbody></table>';
if ('excel' === $_GET['type']) {
    $nop_ = 'Detail_pemeliharaan_'.$notransaksi;
    if (0 < strlen($stream)) {
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ('.' !== $file && '..' !== $file) {
                    @unlink('tempExcel/'.$file);
                }
            }
            closedir($handle);
        }

        $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
        if (!fwrite($handle, $stream)) {
            echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
            exit();
        }

        echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
        closedir($handle);
    }
} else {
    echo $stream;
}

?>