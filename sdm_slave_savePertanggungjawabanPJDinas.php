<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$tanggal = tanggalsystem($_POST['tanggal']);
$jenisby = $_POST['jenisby'];
$keterangan = $_POST['keterangan'];
$jumlah = $_POST['jumlah'];
$method = $_POST['method'];
if ('' == $jumlah) {
    $jumlah = 0;
}

if ('insert' == $method) {
    $str = 'insert into '.$dbname.".sdm_pjdinasdt (\r\n\t\t  `notransaksi`,`jenisbiaya`,`keterangan`,\r\n\t\t  `tanggal`,`jumlah`\r\n\t\t  ) values(\r\n\t\t\t\t'".$notransaksi."',".$jenisby.",'".$keterangan."',\r\n\t\t\t\t".$tanggal.','.$jumlah." \r\n\t\t  )";
    if (mysql_query($str)) {
        $str = 'update '.$dbname.'.sdm_pjdinasht set tglpertanggungjawaban='.date('Ymd')."\r\n\t\t      where notransaksi='".$notransaksi."'";
        mysql_query($str);
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn));
        exit(0);
    }
} else {
    if ('delete' == $method) {
        $str = 'delete from '.$dbname.".sdm_pjdinasdt\r\n\t      where jenisbiaya=".$jenisby." and notransaksi='".$notransaksi."'\r\n\t\t  and tanggal=".$tanggal.' and jumlah='.$jumlah." and keterangan='".$keterangan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal:'.addslashes(mysql_error($conn));
            exit(0);
        }
    }
}

$str = 'select a.*,b.keterangan as jns from '.$dbname.".sdm_pjdinasdt a\r\n      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id\r\n\t  where a.notransaksi='".$notransaksi."'";
$res = mysql_query($str);
$no = 0;
$total = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t     \t<td>".$no."</td>\r\n                    <td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t    <td>".$bar->jns."</td>\r\n\t\t\t<td>".$bar->keterangan."</td>\r\n\t\t\t<td align=right>".number_format($bar->jumlah, 2, '.', '.')."</td>\r\n\t\t    <td><img src='images/close.png' class=resicon onclick=\"deleteDetail('".$bar->notransaksi."','".$bar->jenisbiaya."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$bar->keterangan."')\" title='delete'></td>\r\n\t\t\t</tr>";
    $total += $bar->jumlah;
}
echo "<tr class=rowcontent>\r\n\t     \t<td colspan=4>TOTAL</td>\r\n\t\t\t<td align=right>".number_format($total, 2, '.', '.')."</td>\r\n\t\t    <td></td>\r\n\t\t\t</tr>";

?>