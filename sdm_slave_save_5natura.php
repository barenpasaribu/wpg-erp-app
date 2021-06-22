<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$kode = $_POST['kode'];
$tahun = $_POST['tahun'];
$jumlah = $_POST['jumlah'];
$jumlahuang = $_POST['jumlahuang'];
$keterangan = $_POST['keterangan'];
$method = $_POST['method'];
if ('' == $jumlah) {
    $jumlah = 0;
}

switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5catu set \r\n\t       jumlah=".$jumlah.",\r\n\t jumlahuang=".$jumlahuang.",\r\n\t        keterangan='".$keterangan."'\r\n\t       where kodeorg='".$kodeorg."' and kelompok='".$kode."'\r\n\t       and tahun='".$tahun."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5catu \r\n\t      (kodeorg, tahun, kelompok, keterangan, jumlah, jumlahuang)\r\n\t      values('".$kodeorg."',".$tahun.",'".$kode."','".$keterangan."',".$jumlah.",".$jumlahuang.')';
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn)).'__'.$str;
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5catu\r\n\t where kodeorg='".$kodeorg."' and kelompok='".$kode."'\r\n\t and tahun=".$tahun;
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = "select *\r\n\t     from ".$dbname.".sdm_5catu \r\n\t\t   where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t  order by tahun desc,kelompok";
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t        <td align=center>".$bar1->kodeorg."</td>\r\n                                        <td align=center>".$bar1->tahun."</td>\r\n                                        <td align=center>".$bar1->kelompok."</td>    \r\n                                         <td>".$bar1->keterangan."</td>    \r\n                                        <td align=right>".$bar1->jumlah."</td>\r\n <td align=right>".$bar1->jumlahuang."</td>\r\n                                       \r\n                                        <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tahun."','".$bar1->kelompok."','".$bar1->keterangan."','".$bar1->jumlah."','".$bar1->jumlahuang."');\"></td></tr>";
}

?>