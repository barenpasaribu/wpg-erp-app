<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];
if ('' === $gudang) {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n                sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n            left join ".$dbname.".organisasi c\r\n            on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n            where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal,a.kodeorg';
} else {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n                sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n            where unit = '".$gudang."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal, a.kodeorg';
}

$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    $stream .= '<table border=0 cellpading=1 ><tr><td colspan=7 align=center>'.$_SESSION['lang']['laporanpanen']."</td></tr>\r\n        <tr><td colspan=3>".$_SESSION['lang']['periode'].'</td><td colspan=4 align=left>'.$tgl1.' S/d '.$tgl1."</td></tr>    \r\n        <tr><td colspan=3>".$_SESSION['lang']['unit'].'</td><td colspan=4 align=left>'.(('' !== $gudang ? $gudang : $_SESSION['lang']['all']))."</td></tr>\r\n        <tr><td colspan=3>".$_SESSION['lang']['pt'].'</td><td colspan=4 align=left>'.$pt."</td></tr>        \r\n        </table>\r\n        <br />\r\n        <table border=1>\r\n        <tr>\r\n            <td bgcolor=#DEDEDE align=center>No.</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['afdeling']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lokasi']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahuntanam']."</td>    \r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['janjang']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratnormal']." (Kg)</td>    \r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahkerja']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahpremi']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlahhk']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['penalti']."</td>\r\n        </tr>";
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $periode = date('Y-m-d H:i:s');
        $notransaksi = $bar->notransaksi;
        $tanggal = $bar->tanggal;
        $kodeorg = $bar->kodeorg;
        $stream .= "<tr>\r\n                <td align=center width=20>".$no."</td>\r\n\r\n                <td align=center>".$tanggal."</td>\r\n                <td align=center>".substr($kodeorg, 0, 6)."</td>\r\n                <td align=center>".$kodeorg."</td>\r\n                <td align=center>".$bar->tahuntanam."</td>\r\n                <td align=right ".$dbg.'>'.number_format($bar->jjg, 0)."</td>\r\n                <td align=right>".number_format($bar->berat, 2)."</td>    \r\n                <td align=right>".number_format($bar->upah, 2)."</td>\r\n                <td align=right>".number_format($bar->premi, 2)."</td>\r\n                <td align=right>".number_format($bar->jumlahhk, 0)."</td>\r\n                <td align=right>".number_format($bar->penalty, 2)."</td>\r\n            </tr>";
        $totberat += $bar->berat;
        $totUpah += $bar->upah;
        $totJjg += $bar->jjg;
        $totPremi += $bar->premi;
        $totHk += $bar->jumlahhk;
        $totPenalty += $bar->penalty;
    }
    $stream .= "<tr>\r\n            <td align=center width=20 colspan=5>&nbsp;</td>\t\t \r\n            <td align=right>".number_format($totJjg, 0)."</td>\r\n            <td align=right>".number_format($totberat, 2)."</td>     \r\n            <td align=right>".number_format($totUpah, 2)."</td>\r\n            <td align=right>".number_format($totPremi, 2)."</td>\r\n            <td align=right>".number_format($totHk, 0)."</td>\r\n            <td align=right>".number_format($totPenalty, 2)."</td>\r\n        </tr>";
    $stream .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
}

$tglSkrg = date('Ymd');
$nop_ = 'LaporanPanen'.$pt.'_'.$gudang.'_'.$tgl1;
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
        echo "<script language=javascript1.2>\r\n            parent.window.alert('Can't convert to excel format');\r\n            </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls';\r\n            </script>";
    closedir($handle);
}

?>