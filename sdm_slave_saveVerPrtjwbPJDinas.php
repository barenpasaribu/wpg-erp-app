<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$tanggal = tanggalsystem($_POST['tanggal']);
$jenisby = $_POST['jenisby'];
$jumlahhrd = $_POST['jumlahhrd'];
$method = $_POST['method'];
$jumlah = $_POST['jumlah'];
$keterangan = $_POST['keterangan'];
if ('' == $jumlahhrd) {
    $jumlahhrd = 0;
}

if ('update' == $method) {
    $str = 'update '.$dbname.".sdm_pjdinasdt\r\n\t       set jumlahhrd=".$jumlahhrd."\r\n\t      where jenisbiaya=".$jenisby." and notransaksi='".$notransaksi."'\r\n\t\t  and tanggal=".$tanggal." and jumlah='".$jumlah."' and keterangan='".$keterangan."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn));
        exit(0);
    }
}

if ('finish' == $method) {
    $str = 'update '.$dbname.".sdm_pjdinasht\r\n\t       set statuspertanggungjawaban=1\r\n\t      where  notransaksi='".$notransaksi."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn));
        exit(0);
    }
}

$str = 'select a.*,b.keterangan as jns,b.id as bid from '.$dbname.".sdm_pjdinasdt a\r\n      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id\r\n\t  where a.notransaksi='".$notransaksi."'";
$res = mysql_query($str);
$no = 0;
$total = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t     \t<td>".$no."</td>\r\n\t\t    <td>".$bar->jns."</td>\r\n                        <td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t\t<td>".$bar->keterangan."</td>\r\n\t\t\t<td align=right>".number_format($bar->jumlah, 2, '.', '.')."</td>\r\n\t\t\t<td align=right>\r\n\t\t\t<img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('jumlahhrd".$bar->bid.$no."').value='".$bar->jumlah."'\">\r\n\t\t\t<input type=text id='jumlahhrd".$bar->bid.$no."' class=myinputtextnumber size=15 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='".number_format($bar->jumlahhrd, 2, '.', ',')."'>\r\n\t\t\t<img src='images/save.png' title='Save' class=resicon onclick=\"saveApprvPJD('".$bar->bid."','".$bar->notransaksi."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$bar->keterangan."','".$no."')\"></td>\r\n\t\t\t</tr>";
    $total += $bar->jumlah;
}
echo "<tr class=rowcontent>\r\n\t     \t<td colspan=4 align=center>TOTAL</td>\r\n\t\t\t<td align=right>".number_format($total, 2, '.', '.')."</td>\r\n\t\t    <td></td>\r\n\t\t\t</tr>";

?>