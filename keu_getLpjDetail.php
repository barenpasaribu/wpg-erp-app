<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$param = $_GET;
$total = 0;
$stream = "\r\n        Periode:".$param['dari'].' S/d '.$param['sampai']." \r\n        <table class=sortable cellspacing=0 border=1>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td>".$_SESSION['lang']['keterangan']."</td>\r\n        <td>".$_SESSION['lang']['blok']."</td>\r\n        <td>".$_SESSION['lang']['jumlah']."</td>\r\n        </tr>\r\n        </thead>\r\n        <tbody>";
if ('' === $param['afd']) {
    $else = "kodeblok =''";
} else {
    $else = "kodeblok like '".$param['afd']."%'";
}

if ('PTM' !== $param['tipe']) {
    $str = 'select *  from '.$dbname.".keu_jurnaldt_vw\r\n         where kodeorg='".$param['unit']."'\r\n         and tanggal between ".$param['dari'].' and '.$param['sampai']."\r\n         and noakun >='".$param['akundari']."' and noakun<='".$param['akunsampai']."'\r\n         and ".$else;
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= "<tr class=rowcontent>\r\n                    <td>".$bar->noreferensi."</td>\r\n                    <td>".tanggalnormal($bar->tanggal)."</td>\r\n                    <td>".$bar->keterangan."</td>\r\n                    <td>".$bar->kodeblok."</td>    \r\n                    <td align=right>".number_format($bar->jumlah)."</td>\r\n                   </tr>";
        $total += $bar->jumlah;
    }
} else {
    $str = 'select *  from '.$dbname.".keu_jurnaldt_vw\r\n         where kodeorg='".$param['unit']."'\r\n         and tanggal between ".$param['dari'].' and '.$param['sampai']."\r\n         and ((noakun >='6510100' and noakun<'6510301') or    \r\n         (noakun >'6510311' and noakun<='6511003'))\r\n         and ".$else;
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= "<tr class=rowcontent>\r\n                    <td>".$bar->noreferensi."</td>\r\n                    <td>".tanggalnormal($bar->tanggal)."</td>\r\n                    <td>".$bar->keterangan."</td>\r\n                    <td>".$bar->kodeblok."</td>    \r\n                    <td align=right>".number_format($bar->jumlah)."</td>\r\n                   </tr>";
        $total += $bar->jumlah;
    }
}

$stream .= "<tr class=rowcontent>\r\n                    <td colspan=4>TOTAL</td>    \r\n                    <td align=right>".number_format($total)."</td>\r\n                   </tr>   \r\n          </tbody>\r\n          <tfoot></tfoot> \r\n          </table> \r\n        ";
echo $stream;

?>