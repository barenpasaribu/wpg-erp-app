<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$periode = $_GET['periode'];
$kodeorg = $_GET['kodeorg'];
if ('' == $periode) {
    $periode = date('Y');
}

$str3 = 'select a.diagnosa, sum(jlhbayar) as klaim,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n          on a.diagnosa=d.id\r\n          left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid \r\n              where a.periode like '".$periode."%'\r\n              and c.lokasitugas like '".$kodeorg."%'\r\n        group by a.diagnosa order by klaim desc\r\n    ";
$stream = 'Laporan Ranking Biaya/Diagnosa '.$periode.' '.$kodeorg."\r\n<table border=1>\r\n<thead>\r\n<tr>\r\n    <td bgcolor=#dedede>Rank</td>\r\n    <td bgcolor=#dedede>Diagnose</td>\r\n    <td bgcolor=#dedede>".$_SESSION['lang']['jumlah']."</td>\r\n</tr>\r\n</thead>\r\n<tbody>";
$res3 = mysql_query($str3);
$no = 0;
while ($bar3 = mysql_fetch_object($res3)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->ketdiag."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n    </tr>";
}
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
$nop_ = 'LaporanRankingBiayaperDiagnosa-'.$periode.$kodeorg;
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Cant convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>