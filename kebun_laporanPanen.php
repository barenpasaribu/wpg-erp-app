<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$tgl1 = $_POST['tgl1'];
$tgl2 = $_POST['tgl2'];
if ('' === $gudang) {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n                    sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk from ".$dbname.".kebun_prestasi_vw a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal,a.kodeorg';
} else {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n                    sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n\t\twhere unit = '".$gudang."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal, a.kodeorg';
}

$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $periode = date('Y-m-d H:i:s');
        $notransaksi = $bar->notransaksi;
        $tanggal = $bar->tanggal;
        $kodeorg = $bar->kodeorg;
        $arr = 'tanggal##'.$tanggal.'##kodeorg##'.$kodeorg;
        echo "<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"zDetail(event,'kebun_slave_2panen.php','".$arr."');\">\r\n\t\t\t\t  <td align=center width=20>".$no."</td>\r\n\t\t\t\t \r\n\t\t\t\t  <td align=center>".tanggalnormal($tanggal)."</td>\r\n\t\t\t\t  <td align=center>".substr($kodeorg, 0, 6)."</td>\r\n\t\t\t\t  <td align=center>".$kodeorg."</td>\r\n                                  <td align=right>".$bar->tahuntanam."</td>    \r\n                                  <td align=right>".number_format($bar->jjg, 0)."</td>\r\n                                  <td align=right>".number_format($bar->berat, 2)."</td>    \r\n                                  <td align=right>".number_format($bar->upah, 2)."</td>\r\n                                  <td align=right>".number_format($bar->premi, 2)."</td>\r\n                                  <td align=right>".number_format($bar->jumlahhk, 0)."</td>\r\n                                  <td align=right>".number_format($bar->penalty, 2)."</td>\r\n\t\t\t</tr>";
        $totberat += $bar->berat;
        $totUpah += $bar->upah;
        $totJjg += $bar->jjg;
        $totPremi += $bar->premi;
        $totHk += $bar->jumlahhk;
        $totPenalty += $bar->penalty;
    }
    echo "<tr class=rowcontent >\r\n\t\t\t\t  <td align=center colspan=5>&nbsp;</td>\t\t \r\n\t\t\t\t  <td align=right>".number_format($totJjg, 0)."</td>\r\n                                  <td align=right>".number_format($totberat, 2)."</td>    \r\n                                  <td align=right>".number_format($totUpah, 2)."</td>\r\n                                  <td align=right>".number_format($totPremi, 2)."</td>\r\n                                  <td align=right>".number_format($totHk, 0)."</td>\r\n                                  <td align=right>".number_format($totPenalty, 2)."</td>\r\n                   </tr>\r\n                ";
}

?>