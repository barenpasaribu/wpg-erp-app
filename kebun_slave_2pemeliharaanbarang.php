<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/kebun_2pemeliharaan.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$notransaksi = $_GET['notransaksi'];
$tanggal = $_GET['tanggal'];
$kdOrg = $_GET['kdOrg'];
$type = $_GET['type'];
$periode = substr($tanggal, 0, 7);
$str = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang \r\n      where kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $barang[$bar->kodebarang][kb] = $bar->kodebarang;
    $barang[$bar->kodebarang][nm] = $bar->namabarang;
    $barang[$bar->kodebarang][st] = $bar->satuan;
}
$temp = 0;
$str = 'select kodebarang, hargarata from '.$dbname.".log_5saldobulanan \r\n      where periode ='".$periode."' and kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $barang[$bar->kodebarang][hr] = $bar->hargarata;
    $temp = $bar->hargarata;
}
if (0 === $temp) {
    $str = 'select kodebarang, hargalastin from '.$dbname.".log_5masterbarangdt \r\n      where kodegudang like '".$kdOrg."%' and kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $barang[$bar->kodebarang][hr] = $bar->hargalastin;
        $temp = $bar->hargalastin;
    }
}

if (0 === $temp) {
    $str = 'select kodebarang, hargarata from '.$dbname.".log_5saldobulanan \r\n      where kodebarang in (select kodebarang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$notransaksi."')";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $barang[$bar->kodebarang][hr] = $bar->hargarata;
        $temp = $bar->hargarata;
    }
}

echo "<fieldset><legend>Print Excel</legend>\r\n     <img onclick=\"detailExcel(event,'kebun_slave_2pemeliharaanbarang.php?type=excel&notransaksi=".$notransaksi.'&tanggal='.$tanggal.'&kdOrg='.$kdOrg."')\" src=images/excel.jpg class=resicon title='MS.Excel'>\r\n     </fieldset>";
if ('excel' !== $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
} else {
    $stream = '<table class=sortable border=1 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>No</td>\r\n          <td>Nama Barang</td>\r\n          <td>Jumlah</td>\r\n          <td>Satuan</td>\r\n          <td>Total</td>";
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
$str = 'select * from '.$dbname.".kebun_pakaimaterial\r\n              where notransaksi = '".$notransaksi."'";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $total = $bar->kwantitas * $barang[$bar->kodebarang][hr];
    $stream .= "<tr class=rowcontent>\r\n           <td align=right>".$no."</td>\r\n           <td>".$barang[$bar->kodebarang][nm]."</td>    \r\n           <td align=right>".$bar->kwantitas."</td>    \r\n           <td>".$barang[$bar->kodebarang][st]."</td>    \r\n           <td align=right>".number_format($total).'</td>';
    $stream .= '</tr>';
}
$stream .= '</tbody></table>';
if ('excel' === $_GET['type']) {
    $nop_ = 'Detail_pemeliharaan_barang_'.$tanggal;
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