<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$kode = $_POST['kode'];
$nama = $_POST['nama'];
$method = $_POST['method'];
$noakun = $_POST['noakun'];
$arrEnum = getEnum($dbname, 'bgt_tipe', 'tipe,nama');
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".bgt_kode set nama='".$nama."',noakun='".$noakun."'\r\n\t       where kodebudget='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'select * from '.$dbname.".bgt_kode \r\n    where kodebudget='".$kode."' \r\n            limit 0,1";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $sudahada = '1';
            $pesan = $bar->kodebudget.' - '.$bar->nama;
        }
        if ('1' === $sudahada) {
            echo ' Gagal, data sudah ada: '.$pesan;
            exit();
        }

        $str = 'insert into '.$dbname.".bgt_kode (kodebudget,nama,noakun)\r\n\t      values('".$kode."','".$nama."','".$noakun."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".bgt_kode \r\n\twhere kodebudget='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.bgt_kode order by kodebudget';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kodebudget.'</td><td>'.$bar1->nama.'</td><td>'.$bar1->noakun."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodebudget."','".$bar1->nama."','".$bar1->noakun."');\"></td></tr>";
    }
}

?>