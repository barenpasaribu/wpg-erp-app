<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zMysql.php';
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$period = date('Y-m');
$strx = 'select * from '.$dbname.".kebun_pengirimanbbt where tanggal like '%".$period."%' order by tanggal desc";
$stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td colspan=7 align=center>".$_SESSION['lang']['pengirimanBibit']."</td></tr>\r\n\t\t\t<tr><td colspan=3>&nbsp;</td></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaorganisasi']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nmcust']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['OrgTujuan']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisbibit']."</td>\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>\t\r\n\t\t\t\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakegiatan']."</td>\t\t\t\t\t\t\t  \r\n\t\t\t\t\t\t</tr>";
$resx = mysql_query($strx);
$row = mysql_fetch_row($resx);
if ($row < 1) {
    $stream .= "\t<tr class=rowcontent>\r\n\t\t\t<td colspan=8 align=center>Not Avaliable</td></tr>\r\n\t\t\t";
} else {
    $no = 0;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_assoc($resx)) {
        ++$no;
        $sKdOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$barx['kodeorg']."'";
        $qKdOrg = mysql_query($sKdOrg) ;
        $rKdOrg = mysql_fetch_assoc($qKdOrg);
        $sKeg = 'select kelompok,namakegiatan from '.$dbname.".setup_kegiatan where kodekegiatan='".$barx['kodekegiatan']."'";
        $qKeg = mysql_query($sKeg) ;
        $rKeg = mysql_fetch_assoc($qKeg);
        $sCust = 'select namacustomer from '.$dbname.".pmn_4customer where kodecustomer='".$barx['pembeliluar']."'";
        $qCust = mysql_query($sCust) ;
        $rCust = mysql_fetch_assoc($qCust);
        $sKdOrg2 = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$barx['orgtujuan']."'";
        $qKdOrg2 = mysql_query($sKdOrg2) ;
        $rKdOrg2 = mysql_fetch_assoc($qKdOrg2);
        $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t\t\t<td>".$barx['notransaksi']."</td>\r\n\t\t\t\t\t\t\t<td>".$rKdOrg['namaorganisasi']."</td>\r\n\t\t\t\t\t\t\t<td>".$rCust['namacustomer']."</td>\r\n\t\t\t\t\t\t\t<td>".$rKdOrg2['namaorganisasi']."</td>\r\n\t\t\t\t\t\t\t<td>".tanggalnormal($barx['tanggal'])."</td>\r\n\t\t\t\t\t\t\t<td>".$barx['jenisbibit']."</td>\r\n\t\t\t\t\t\t\t<td>".$barx['jumlah']."</td>\t\r\n\t\t\t\t\t\t\t<td>".$rKeg['kelompok'].'-'.$rKeg['namakegiatan']."</td>\t\r\n\t\t\t\t\t</tr>";
    }
}

$stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
$nop_ = 'PengirimanBibit';
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