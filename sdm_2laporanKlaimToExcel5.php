<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$periode = $_GET['periode'];
$karyawanid = $_GET['karyawanid'];
if ('' == $periode) {
    $periode = date('Y');
}

if ('' == $_GET['karyawanid']) {
    $str3 = "select  sum(jasars) as rs, \r\n               sum(jasadr) as dr, sum(jasalab) as lab, \r\n               sum(byobat) as obat, \r\n               sum(bypendaftaran) administrasi, \r\n               a.periode, sum(a.totalklaim) as klaim, sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a \r\n               left join ".$dbname.".datakaryawan c\r\n               on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n             group by periode order by periode";
} else {
    $str3 = "select  sum(jasars) as rs, \r\n               sum(jasadr) as dr, sum(jasalab) as lab, \r\n               sum(byobat) as obat, \r\n               sum(bypendaftaran) administrasi, \r\n               a.periode, sum(a.totalklaim) as klaim, sum(a.jlhbayar) as bayar ".$dbname.".sdm_pengobatanht a \r\n               left join ".$dbname.".datakaryawan c\r\n               on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n               and c.karyawanid=".$_GET['karyawanid']."\r\n             group by periode order by periode";
}

$x = ('' == $_GET['karyawanid'] ? 'ALL' : $_GET['nama']);
$stream .= ' Trend Biaya Pengobatan per Jenis Biaya periode:'.$periode."<br>\r\n                      Nama Karyawan:".$x."<br>\r\n      <table border=1>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td>Period</td>\r\n        <td>".$_SESSION['lang']['biayars']."</td>\r\n        <td>".$_SESSION['lang']['biayadr']."</td>\r\n        <td>".$_SESSION['lang']['biayalab']."</td>\r\n        <td>".$_SESSION['lang']['biayaobat']."</td>\r\n        <td>".$_SESSION['lang']['biayapendaftaran']."</td>\r\n        <td>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['dibayar']."</td>\r\n    </tr>\r\n    </thead><tbody>";
$res3 = mysql_query($str3);
$no = 0;
$trs = 0;
$tdr = 0;
$tlb = 0;
$tob = 0;
$tad = 0;
$ttl = 0;
while ($bar3 = mysql_fetch_object($res3)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->periode."</td>\r\n            <td align=right>".number_format($bar3->rs)."</td>\r\n            <td align=right>".number_format($bar3->dr)."</td>\r\n            <td align=right>".number_format($bar3->lab)."</td>\r\n            <td align=right>".number_format($bar3->obat)."</td>\r\n            <td align=right>".number_format($bar3->administrasi)."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n            <td align=right>".number_format($bar3->bayar)."</td>    \r\n        </tr>";
    $trs += $bar3->rs;
    $tdr += $bar3->dr;
    $tlb += $bar3->lab;
    $tob += $bar3->obat;
    $tad += $bar3->administrasi;
    $ttl += $bar3->klaim;
    $byr += $bar3->bayar;
}
$stream .= "<tr class=rowcontent>\r\n            <td></td>\r\n            <td>".$_SESSION['lang']['total']."</td>\r\n            <td align=right>".number_format($trs)."</td>\r\n            <td align=right>".number_format($tdr)."</td>\r\n            <td align=right>".number_format($tlb)."</td>\r\n            <td align=right>".number_format($tob)."</td>\r\n            <td align=right>".number_format($tad)."</td>\r\n            <td align=right>".number_format($ttl)."</td>\r\n            <td align=right>".number_format($byr)."</td>    \r\n        </tr>";
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
$nop_ = 'TrendBiayaperDiagnosa-'.$periode.$kodeorg;
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