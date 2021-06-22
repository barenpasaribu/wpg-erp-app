<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$tanggal = tanggalsystem($_POST['tanggal']);
$jlhbbm = $_POST['jlhbbm'];
$method = $_POST['method'];
$totalharga = $_POST['totalharga'];
if ('delete' == $method) {
    $tanggal = $_POST['tanggal'];
    $str = 'delete from '.$dbname.".sdm_penggantiantransportdt where\r\n\t notransaksi='".$notransaksi."' and tanggal='".$tanggal."'";
    $stru = 'select hargatotal from  '.$dbname.".sdm_penggantiantransportdt where\r\n\t notransaksi='".$notransaksi."' and tanggal='".$tanggal."'";
    $resf = mysql_query($stru);
    while ($baru = mysql_fetch_object($resf)) {
        $totalharga = $baru->hargatotal;
    }
    $str1 = 'update  '.$dbname.'.sdm_penggantiantransport set totalklaim=(totalklaim-'.$totalharga.") where\r\n\t notransaksi='".$notransaksi."'";
} else {
    if ('insert' == $method) {
        $str = 'insert into '.$dbname.".sdm_penggantiantransportdt \r\n\t      (`notransaksi`,`tanggal`,`jlhbbm`,`hargatotal`)\r\n\t\t  values(\r\n\t\t   '".$notransaksi."',".$tanggal.','.$jlhbbm.','.$totalharga.')';
        $str1 = 'update  '.$dbname.'.sdm_penggantiantransport set totalklaim=(totalklaim+'.$totalharga.") where\r\n\t notransaksi='".$notransaksi."'";
    } else {
        $str = 'select 1=1';
        $str1 = $str;
    }
}

if (mysql_query($str)) {
    if (mysql_query($str1)) {
        $str = 'select * from '.$dbname.".sdm_penggantiantransportdt\r\n\t      where notransaksi='".$notransaksi."'";
        $res = mysql_query($str);
        $no = 0;
        $tkuantitas = 0;
        $tharga = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n\t\t     <td>".$no."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t\t <td align=right>".number_format($bar->jlhbbm, 2, '.', ',')."</td>\r\n\t\t\t <td align=right id='x".$no."'>".number_format($bar->hargatotal, 2, '.', ',')."</td>\r\n\t\t\t <td><img src='images/application/application_delete.png' class=resicon onclick=\"deleteSolar('".$bar->notransaksi."','".$bar->tanggal."','x".$no."');\"></td>\r\n\t\t   </tr>";
            $tkuantitas += $bar->jlhbbm;
            $tharga += $bar->hargatotal;
        }
        echo "<tr class=rowcontent>\r\n     <td></td>\r\n\t <td>".$_SESSION['lang']['total']."</td>\r\n\t <td align=right>".number_format($tkuantitas, 2, '.', ',')."</td>\r\n\t <td align=right>".number_format($tharga, 2, '.', ',')."</td>\r\n\t <td>-</td>\r\n   </tr>";
    } else {
        $strx = 'delete from '.$dbname.".sdm_penggantian transportdt where\r\n\t notransaksi='".$notransaksi."'";
        mysql_query($strx);
        echo $str1;
        echo 'Error: Inconsistence calculation on Detail transaction, please re-input again';
        exit(0);
    }
} else {
    echo ' Gagal '.addslashes(mysql_error($conn));
}

?>