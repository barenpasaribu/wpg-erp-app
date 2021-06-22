<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$kodeorg = $_GET['kodeorg'];
$periode = $_GET['periode'];
$str1 = "select a.*,b.namakaryawan,b.tanggalmasuk\r\n\t       from ".$dbname.".sdm_cutiht a\r\n\t\t   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n\t       where lokasitugas='".$kodeorg."'\r\n\t\t   and periodecuti='".$periode."'";
$res1 = mysql_query($str1);
$stream .= "\r\n\t      Rekap ".$_SESSION['lang']['cuti'].':'.$periode."\r\n\t     <table border=1>\r\n\t     <thead>\r\n\t\t <tr>\r\n\t\t    <td bgcolor='#dedede'>No</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['kodeorganisasi']."</td>\t\t \r\n\t\t    <td bgcolor='#dedede'>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t    <td bgcolor='#dedede'>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['tanggalmasuk']."</td>\t\t\t\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['periode']."</td>\t\t\t\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['dari']."</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['hakcuti']."</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['diambil']."</td>\r\n\t\t\t<td bgcolor='#dedede'>".$_SESSION['lang']['sisa']."</td>\r\n\t\t\t</tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    $stream .= "<tr>\r\n\t\t           <td>".$no."</td>\r\n\t\t\t\t   <td>".substr($bar1->kodeorg, 0, 4)."</td>\r\n\t\t           <td>'".$bar1->karyawanid."</td>\r\n\t\t\t\t   <td>".$bar1->namakaryawan."</td>\r\n\t\t\t\t   <td>".tanggalnormal($bar1->tanggalmasuk)."</td>\r\n\t\t\t\t   <td>".$periode."</td>\t\t\t\t   \r\n\t\t\t\t   <td>".tanggalnormal($bar1->dari)."</td>\r\n\t\t\t\t   <td>".tanggalnormal($bar1->sampai)."</td>\r\n\t\t\t\t   <td>".$bar1->hakcuti."</td>\r\n\t\t\t\t   <td>".$bar1->diambil."</td>\r\n\t\t\t\t   <td>".$bar1->sisa."</td>\r\n\t\t\t</tr>\t   \r\n\t\t\t\t   ";
}
$stream .= "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
$nop_ = 'Rekap_Cuti_Periode'.$periode;
if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
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