<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodejabatan = $_POST['kode'];
$namajabatan = $_POST['nama'];
$namajabatan1 = $_POST['nama1'];
$satuan = $_POST['satuan'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".pabrik_5fraksi set keterangan='".$namajabatan."',keterangan1='".$namajabatan1."',\r\n\t      type='".$satuan."'\r\n\t       where kode='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".pabrik_5fraksi (kode,keterangan,keterangan1,type)\r\n\t      values('".$kodejabatan."','".$namajabatan."','".$namajabatan1."','".$satuan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".pabrik_5fraksi\r\n\twhere kode='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.pabrik_5fraksi order by kode';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kode.'</td><td>'.$bar1->keterangan.'</td><td>'.$bar1->keterangan1.'</td><td>'.$bar1->type."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->keterangan."','".$bar1->type."','".$bar1->keterangan1."');\"></td></tr>";
    }
}

?>