<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$periodegaji = $_POST['periodegaji'];
$idkaryawan = $_POST['idkaryawan'];
$upahpremi = $_POST['upahpremi'];
$komponenpayroll = $_POST['komponenpayroll'];
$method = $_POST['method'];
$str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$periodegaji."' and \r\n             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    $aktif = true;
} else {
    $aktif = false;
}

switch ($method) {
    case 'show':
        if ($aktif) {
            exit();
        } else {
            exit('Error:Periode sudah tutup buku');
        }
        break;

    case 'insert':
        $str = 'insert into '.$dbname.".sdm_gaji \r\n\t      (kodeorg,periodegaji,karyawanid,idkomponen,jumlah,pengali)\r\n\t      values('".$_SESSION['empl']['lokasitugas']."','".$periodegaji."','".$idkaryawan."','".$komponenpayroll."','".$upahpremi."','1')";
        if ($aktif) {
            if (mysql_query($str)) {
                break;
            }

            echo ' Gagal,'.mysql_error($conn);
            exit();
        } else {
            exit('Error:Periode sudah tutup buku');
        }
        break;

    case 'delete':
        $str = 'delete from '.$dbname.".sdm_gaji\r\n\twhere kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periodegaji."' and karyawanid='".$idkaryawan."' and idkomponen='".$komponenpayroll."'";
        if ($aktif) {
            if (mysql_query($str)) {
                break;
            }

            echo ' Gagal,'.mysql_error($conn);
            exit();
        } else {
            exit('Error:Periode sudah tutup buku');
        }
        break;

    default:
        break;
}
if ($_SESSION['org'][tipelokasitugas] == 'HOLDING') {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n\t  and tipekaryawan!=5 and lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n\t  order by namakaryawan";
} else {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n\t  and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t  order by namakaryawan";
}

$res1 = mysql_query($str1, $conn);
while ($bar1 = mysql_fetch_object($res1)) {
    $nama[$bar1->karyawanid] = $bar1->namakaryawan;
}
$strJ = 'select * from '.$dbname.'.sdm_5jabatan';
$resJ = mysql_query($strJ, $conn);
while ($barJ = mysql_fetch_object($resJ)) {
    $jab[$barJ->kodejabatan] = $barJ->namajabatan;
}
$strRes = 'select a.*, b.kodejabatan, b.lokasitugas from '.$dbname.".sdm_gaji a \r\n\tleft join ".$dbname.".datakaryawan b\r\n\ton a.karyawanid = b.karyawanid\r\n\twhere a.idkomponen in ('19','20') and  a.periodegaji ='".$periodegaji."' and b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'\r\n\torder by a.karyawanid";
$tot = 0;
$resRes = mysql_query($strRes);
while ($bar1 = mysql_fetch_object($resRes)) {
    echo "<tr class=rowcontent>\r\n                 <td>".$nama[$bar1->karyawanid]."</td>\r\n                 <td>".$jab[$bar1->kodejabatan]."</td>\r\n                 <td>".$bar1->periodegaji."</td>\r\n                 <td align=right width=100>".number_format($bar1->jumlah, 2)."</td>\r\n                 <td><img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delPremi('".$bar1->periodegaji."','".$bar1->karyawanid."','".$bar1->jumlah."','".$bar1->idkomponen."');\"></td></tr>";
    $tot += $bar1->jumlah;
}
echo "\t<tr class=rowheader>\r\n        <td colspan=3></td>\r\n        <td align=right>".number_format($tot, 2)."</td>    \r\n\t<td></td></tr>";

?>