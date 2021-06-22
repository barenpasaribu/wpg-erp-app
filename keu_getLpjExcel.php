<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$param = $_GET;
$stream = "\r\n        \r\n        Periode:".$param['dari'].' S/d '.$param['sampai']." \r\n        <table class=sortable cellspacing=0 border=1>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['biaya']."</td>\r\n        <td>".$_SESSION['lang']['afdeling']."</td>\r\n        <td>".$_SESSION['lang']['jumlah']."</td>\r\n        </tr>\r\n        </thead>\r\n        <tbody>";
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='6410100' and noakun<='6440100'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>BIAYA UMUM</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='6520101' and noakun<='6520204'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>BIAYA PANEN</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and ((noakun >='6510100' and noakun<'6510301') or    \r\n     (noakun >'6510311' and noakun<='6511003'))\r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>PEMELIHARAAN TM</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='6510301' and noakun<='6510311'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>PEMUPUKAN</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='1320101' and noakun<='1320501'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>LC</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='1310101' and noakun<='1310401'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>BIBITAN</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='1322001' and noakun<='1330200'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>PEMELIHARAAN TBM</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$str = 'select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from '.$dbname.".keu_jurnaldt_vw\r\n     where kodeorg='".$param['unit']."'\r\n     and tanggal between ".tanggalsystem($param['dari']).' and '.tanggalsystem($param['sampai'])."\r\n     and noakun >='1410100' and noakun<='1411000'    \r\n     group  by afdeling";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr>\r\n                <td>KAPITAL NON TANAMAN</td>\r\n                <td>".$bar->afdeling."</td>\r\n                <td align=right>".number_format($bar->jumlah)."</td>\r\n               </tr>";
}
$stream .= "</tbody>\r\n          <tfoot></tfoot> \r\n          </table> \r\n        ";
if (isset($_GET['excel'])) {
    $nop_ = 'LPJ_'.$param['unit'].'_'.$param['dari'].'_'.$_GET['sampai'];
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