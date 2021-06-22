<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$kode = $_POST['kode'];
$nama = $_POST['nama'];
$method = $_POST['method'];
$arrEnum = getEnum($dbname, 'bgt_tipe', 'tipe,nama');
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".bgt_tipe set nama='".$nama."'\r\n\t       where tipe='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'select * from '.$dbname.".bgt_tipe \r\n    where tipe='".$kode."' \r\n            limit 0,1";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $sudahada = '1';
            $pesan = $bar->tipe.' - '.$bar->nama;
        }
        if ('1' === $sudahada) {
            echo ' Gagal, data sudah ada: '.$pesan;
            exit();
        }

        $str = 'insert into '.$dbname.".bgt_tipe (tipe,nama)\r\n\t      values('".$kode."','".$nama."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".bgt_tipe \r\n\twhere tipe='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.bgt_tipe order by tipe';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->tipe.'</td><td>'.$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->tipe."','".$bar1->nama."');\"></td></tr>";
    }
}

?>