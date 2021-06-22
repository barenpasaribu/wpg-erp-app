<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$periode = $_GET['periode'];
$tampil = $_GET['tampil'];
$pabrik = $_GET['pabrik'];
if (4 === strlen($periode)) {
    $str = "select sum(tbsmasuk) as tbsmasuk,\r\n\t\t  sum(tbsdiolah) as tbsdiolah,\r\n\t\t  sum(oer)  as oer,\r\n\t\t  avg(ffa) as ffa,\r\n\t\t  avg(kadarair) as kadarair,\r\n\t\t  avg(kadarkotoran) as kadarkotoran,\r\n\t\t  sum(oerpk) as oerpk,\r\n\t\t  avg(ffapk) as ffapk,\r\n\t\t  avg(kadarairpk) as kadarairpk,\r\n\t\t  avg(kadarkotoranpk) as kadarkotoranpk,\r\n\t\t  sum(jumlahpk) as jumlahpk,\r\n\t\t  sum(jumlahck) as jumlahck,\r\n\t\t  sum(jumlahjakos) as jumlahjakos,\r\n\t\t  left(tanggal,7) as perio from ".$dbname.".pabrik_produksi\r\n\t\t  where kodeorg='".$pabrik."' and tanggal like '".$periode."%'\r\n\t\t  group by perio order by perio";
    $stsisa = 'select sisahariini from '.$dbname.".pabrik_produksi \r\n\t          where tanggal like '".$periode."%' order by tanggal desc limit 1";
    $ressisa = mysql_query($stsisa);
    $sisa = 0;
    while ($barsisa = mysql_fetch_object($ressisa)) {
        $sisa = $barsisa->sisahariini;
    }
    $stsedia = 'select sisahariini from '.$dbname.".pabrik_produksi \r\n\t          where tanggal like '".($periode - 1)."%' order by tanggal desc limit 1";
    $ressedia = mysql_query($stsedia);
    $tbskemarin = 0;
    while ($barsedia = mysql_fetch_object($ressedia)) {
        $tbskemarin = $barsedia->sisahariini;
    }
    $res = mysql_query($str);
    $stream = '';
    $stream .= "\r\n\t  <table border=1>\r\n\t    <thead>\r\n\t\t  <tr>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['bulan']."</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['tersedia']." (Kg.)</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>\r\n\t\t   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."\r\n\t\t   </td>\r\n\t\t   <td colspan=5 align=center>".$_SESSION['lang']['kernel']."\r\n\t\t   </td>\t  \r\n\t\t  </tr>  \r\n\t\t  <tr class=rowheader> \r\n\t\t   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['oer']." (%)</td>\r\n\t\t   <td align=center>(FFa)(%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>\r\n\t\t   \r\n\t\t   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['oer']." (%)</td>\r\n\t\t   <td align=center>(FFa) (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>\r\n\t\t   \r\n\t\t  </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
    while ($bar = mysql_fetch_object($res)) {
        $stream .= "<tr>\r\n\t\t   <td>".$pabrik."</td>\r\n\t\t   <td>".$bar->perio."</td>\r\n\t\t   <td align=right>".number_format($bar->tbsmasuk + $tbskemarin, 0)."</td>\r\n\t\t   <td align=right>".number_format($bar->tbsdiolah, 0)."</td>\r\n\t\t   <td align=right>".number_format(($bar->tbsmasuk + $tbskemarin) - $bar->tbsdiolah, 0)."</td>\t\t   \r\n\t\t   <td align=right>".number_format($bar->oer, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".@number_format($bar->oer / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->ffa, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarkotoran, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarair, 2, '.', ',')."</td>\r\n\t\t   \r\n\t\t   <td align=right>".number_format($bar->oerpk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".@number_format($bar->oerpk / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->ffapk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarkotoranpk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarairpk, 2, '.', ',')."</td>\r\n\t\t  </tr>";
        $tbskemarin = ($bar->tbsmasuk + $tbskemarin) - $bar->tbsdiolah;
    }
    $stream .= "\r\n\t\t</tbody>\r\n\t\t<tfoot>\r\n\t\t</tfoot>\r\n\t  </table>\r\n\t  </fieldset>";
} else {
    $str = 'select * from '.$dbname.".pabrik_produksi where tanggal like '".$periode."%'\r\n\t      and kodeorg='".$pabrik."'\r\n\t\t  order by tanggal desc";
    $res = mysql_query($str);
    $stream = '';
    $stream .= "\r\n      <table border=1>\r\n\t    <thead>\r\n\t\t  <tr>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['tersedia']." (Kg.)</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>\r\n\t\t   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>\r\n\t\t   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."\r\n\t\t   </td>\r\n\t\t   <td colspan=5 align=center>".$_SESSION['lang']['kernel']."\r\n\t\t   </td>\t  \r\n\t\t  </tr>  \r\n\t\t  <tr class=rowheader> \r\n\t\t   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['oer']." (%)</td>\r\n\t\t   <td align=center>(FFa)(%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>\r\n\t\t   \r\n\t\t   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['oer']." (%)</td>\r\n\t\t   <td align=center>(FFa) (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>\r\n\t\t   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>\r\n\t\t  </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
    while ($bar = mysql_fetch_object($res)) {
        $stream .= "<tr>\r\n\t\t   <td>".$bar->kodeorg."</td>\r\n\t\t   <td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t   <td align=right>".number_format($bar->tbsmasuk + $bar->sisatbskemarin, 0)."</td>\r\n\t\t   <td align=right>".number_format($bar->tbsdiolah, 0)."</td>\r\n\t\t   <td align=right>".number_format($bar->sisahariini, 0)."</td>\r\n\t\t   \r\n\t\t   <td align=right>".number_format($bar->oer, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".@number_format($bar->oer / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->ffa, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarkotoran, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarair, 2, '.', ',')."</td>\r\n\t\t   \r\n\t\t   <td align=right>".number_format($bar->oerpk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".@number_format($bar->oerpk / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->ffapk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarkotoranpk, 2, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarairpk, 2, '.', ',')."</td>\r\n\t\t  </tr>";
    }
    $stream .= "\r\n\t\t</tbody>\r\n\t\t<tfoot>\r\n\t\t</tfoot>\r\n\t  </table>\r\n\t  </fieldset>";
}

$nop_ = 'Produksi_'.$pabrik.'_'.$periode;
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>