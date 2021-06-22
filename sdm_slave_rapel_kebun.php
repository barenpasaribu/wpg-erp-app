<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$periodegaji = $_POST['periodegaji'];
$idkaryawan = $_POST['idkaryawan'];
$upahpremi = $_POST['upahpremi'];
$komponenpayroll = $_POST['komponenpayroll'];
$method = $_POST['method'];
$str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$periodegaji."' and \r\n             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    $aktif = false;
} else {
    $aktif = true;
}

switch ($method) {
    case 'show':
        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_gaji \r\n\t      (kodeorg,periodegaji,karyawanid,idkomponen,jumlah,pengali)\r\n\t      values('".$_SESSION['empl']['lokasitugas']."','".$periodegaji."','".$idkaryawan."','".$komponenpayroll."','".$upahpremi."','1')";
        if ($aktif) {
            if (mysql_query($str)) {
                break;
            }

            echo ' Gagal,'.mysql_error($conn);
            exit();
        }

        exit('Error:Periode sudah tutup buku');
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_gaji\r\n\twhere kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periodegaji."' and karyawanid='".$idkaryawan."' and idkomponen='".$komponenpayroll."'";
        if ($aktif) {
            if (mysql_query($str)) {
                break;
            }

            echo ' Gagal,'.mysql_error($conn);
            exit();
        }

        exit('Error:Periode sudah tutup buku');
    default:
        break;
}
if ('HOLDING' == $_SESSION['org'][tipelokasitugas]) {
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
$strRes = 'select a.*, b.kodejabatan, b.lokasitugas from '.$dbname.".sdm_gaji a \r\n\tleft join ".$dbname.".datakaryawan b\r\n\ton a.karyawanid = b.karyawanid\r\n\twhere a.idkomponen in ('14','24') and  a.periodegaji ='".$periodegaji."' and b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'\r\n\torder by a.karyawanid";
$resRes = mysql_query($strRes);
while ($bar1 = mysql_fetch_object($resRes)) {
    echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$nama[$bar1->karyawanid]."</td>\r\n\t\t\t\t   <td>".$jab[$bar1->kodejabatan]."</td>\r\n\t\t           <td align=center>".$bar1->periodegaji."</td>\r\n\t\t\t\t   <td align=right width=100>".number_format($bar1->jumlah, 2)."</td>\r\n\t\t\t\t   <td><img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delPremi('".$bar1->periodegaji."','".$bar1->karyawanid."','".$bar1->jumlah."','".$bar1->idkomponen."');\"></td></tr>";
}

?>