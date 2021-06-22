<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$pabrik = $_GET['pabrik'];
$statId = $_GET['statId'];
$periode = substr($_GET['periode'], 0, 7);
$kdBrg = $_GET['kdBrg'];
$msnId = $_GET['msnId'];
$optNmMsn = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pabrik."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$statId."'";
$qNm = mysql_query($sNm);
$rNm = mysql_fetch_assoc($qNm);
if ('0' === $periode) {
    $strx = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n\t\twhere a.pabrik='".$pabrik."' and a.statasiun='".$statId."' order by a.tanggal asc";
} else {
    if ('0' !== $periode) {
        $strx = 'select a.*,b.* from '.$dbname.'.pabrik_rawatmesinht a inner join '.$dbname.".pabrik_rawatmesindt b on a.notransaksi=b.notransaksi \r\n\t\twhere a.pabrik='".$pabrik."'and a.statasiun='".$statId."' and tanggal like '%".$periode."%'  order by a.tanggal asc";
    }
}

$stream .= '<table><tr><td colspan=11 align=center>'.$_SESSION['lang']['pemeliharaanMesinReport']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['pabrik'].':'.$namapt."</td><td colspan=2>&nbsp;</td>\r\n\t\t\t<td colspan=3>".$_SESSION['lang']['statasiun'].':'.$rNm['namaorganisasi']."</td></tr>\r\n\t\t\t";
if ('0' !== $periode) {
    $stream .= '<tr><td colspan=3>'.$_SESSION['lang']['periode'].':'.$periode.'</td></tr>';
}

$stream .= "</table>\r\n\t\t\t<table border=1>\r\n\t\t\t\t\t\t<tr>\r\n                                                <td bgcolor=#DEDEDE align=center>No.</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kegiatan']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammulai']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamselesai']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>\t\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['shift']."</td>\t\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mesin']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nmmesin']."</td>\r\n                                                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t\t\t\t</tr>";
$resx = mysql_query($strx);
$no = 0;
while ($barx = mysql_fetch_assoc($resx)) {
    ++$no;
    $sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";
    $qBrg = mysql_query($sBrg);
    $rBrg = mysql_fetch_assoc($qBrg);
    $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$barx['notransaksi']."</td>\r\n\t\t\t\t<td>".tanggalnormal($barx['tanggal'])."</td>\r\n                                <td>".$barx['kegiatan']."</td>\r\n                                <td>".tanggalnormald($barx['jammulai'])."</td>\r\n                                <td>".tanggalnormald($barx['jamselesai'])."</td>\r\n\t\t\t\t<td>".$barx['kodebarang']."</td>\r\n\t\t\t\t<td>".$rBrg['namabarang']."</td>\r\n\t\t\t\t<td>".$barx['satuan']."</td>\r\n\t\t\t\t<td>".$barx['jumlah']."</td>\r\n\t\t\t\t<td>".$barx['shift']."</td>\t\r\n\t\t\t\t<td>".$barx['mesin']."</td>\t\r\n\t\t\t\t<td>".$optNmMsn[$barx['mesin']]."</td>\r\n                                <td>".$barx['keterangan']."</td>\r\n\t\t\t\t</tr>";
}
$stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
$nop_ = 'ReportPemeliharaanMesin';
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