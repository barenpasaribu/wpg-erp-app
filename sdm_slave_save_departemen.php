<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$nama = $_POST['nama'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5departemen set nama='".$nama."'\r\n\t       where kode='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5departemen (kode,nama)\r\n\t      values('".$kode."','".$nama."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5departemen \r\n\twhere kode='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_5departemen order by kode';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kode.'</td><td>'.$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->nama."');\">&nbsp;<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"DeleteField('".$bar1->kode."','".$bar1->nama."');\"></td></tr>";
    }
}

?>