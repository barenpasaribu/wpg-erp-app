<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['cmpId'];
$periode = explode('-', $_GET['period']);
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td colspan=6 align=center>".$_SESSION['lang']['laporanCurahHujan']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['kebun'].':'.$namapt.'</td></tr>';
$stream .= '<tr><td colspan=3>'.$_SESSION['lang']['kodeorg'].':'.$_SESSION['empl']['lokasitugas'].'</td></tr>';
$stream .= '<tr><td colspan=3>'.$_SESSION['lang']['periode'].':'.$periode[1].'-'.$periode[0]."</td></tr>\r\n\t\t\t<tr><td colspan=3>&nbsp;</td></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pagi']."</td>\r\n\t\t\t\t\t\t  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sore']."</td>\r\n\t\t\t\t\t\t   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['note']."</td>\t\r\n\t\t\t\t\t\t</tr>";
$ts = mktime(0, 0, 0, $periode[1], 1, $periode[0]);
$jmlhHari = (int) (date('t', $ts));
for ($a = 1; $a <= $jmlhHari; ++$a) {
    ++$i;
    if (strlen($a) < 2) {
        $a = '0'.$a;
    }

    $tglProg = $a.'-'.$periode[1].'-'.$periode[0];
    $strx = 'select * from '.$dbname.".kebun_curahhujan where kodeorg='".$pt."' and tanggal='".tanggalsystem($tglProg)."'";
    $resx = mysql_query($strx);
    $barx = mysql_fetch_assoc($resx);
    ++$no;
    $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$tglProg."</td>\r\n\t\t\t\t<td>".$barx['pagi']."</td>\r\n\t\t\t\t<td>".$barx['sore']."</td>\r\n\t\t\t\t<td>".$barx['catatan']."</td>\r\n\t\t\t\t</tr>";
}
$stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
$nop_ = 'ReportCurahHujan';
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