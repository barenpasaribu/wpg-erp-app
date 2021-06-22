<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zMysql.php';
$pt = $_GET['pt'];
$periode = $_GET['periode'];
$query2 = selectQuery($dbname, 'organisasi', 'namaorganisasi', "kodeorganisasi='".$pt."'");
$orgData2 = fetchData($query2);
if (strlen($pt) < 6) {
    $kdOrg = 'substr(b.blok,1,4)';
} else {
    $kdOrg = 'substr(b.blok,1,6)';
}

$strx = 'select a.tanggal,b.* from '.$dbname.'.kebun_spbht a inner join '.$dbname.".kebun_spbdt b on a.nospb=b.nospb \r\n\t\twhere a.tanggal like '%".$periode."%' and ".$kdOrg."='".$pt."' order by a.tanggal asc ";
$stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td colspan=11 align=center>".$_SESSION['lang']['listSpb']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['periode'].':'.$periode."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['unit'].':'.$orgData2[0]['namaorganisasi']."</td></tr>\r\n\t\t\t<tr><td colspan=3>&nbsp;</td></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nospb']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['janjang']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bjr']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mentah']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['busuk']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['matang']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lewatmatang']."</td>\t\r\n\t\t\t\t\t\t</tr>";
$resx = mysql_query($strx);
$row = mysql_fetch_row($resx);
if ($row < 1) {
    $stream .= "\t<tr class=rowcontent>\r\n\t\t\t<td colspan=11 align=center>Not Found</td></tr>\r\n\t\t\t";
} else {
    $no = 0;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_assoc($resx)) {
        ++$no;
        $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t<td>".$barx['nospb']."</td>\r\n\t\t\t\t\t<td>".$barx['tanggal']."</td>\r\n\t\t\t\t\t<td>".$barx['blok']."</td>\r\n\t\t\t\t\t<td>".number_format($barx['jjg'], 2)."</td>\t\r\n\t\t\t\t\t<td>".number_format($barx['bjr'], 2)."</td>\t\r\n\t\t\t\t\t<td>".number_format($barx['brondolan'], 2)."</td>\t\r\n\t\t\t\t\t<td>".number_format($barx['mentah'], 2)."</td>\t\r\n\t\t\t\t\t<td>".number_format($barx['busuk'], 2)."</td>\r\n\t\t\t\t\t<td>".number_format(${$barx['matang']}, 2)."</td>\t\r\n\t\t\t\t\t<td>".number_format($barx['lewatmatang'], 2)."</td>\t\r\n\t\t\t\t\t</tr>";
    }
}

$stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
$nop_ = ''.$_SESSION['lang']['listSpb'].'';
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