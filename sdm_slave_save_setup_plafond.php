<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodegolongan = $_POST['kodegolongan'];
$persen = $_POST['persen'];
$method = $_POST['method'];
$jenisbiaya = $_POST['jenisbiaya'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_pengobatanplafond set persen='".$persen."'\r\n\t       where kodegolongan='".$kodegolongan."'\r\n\t\t   and kodejenisbiaya='".$jenisbiaya."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_pengobatanplafond (kodegolongan,persen,kodejenisbiaya)\r\n\t      values('".$kodegolongan."','".$persen."','".$jenisbiaya."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_pengobatanplafond \r\n\twhere kodegolongan='".$kodegolongan."' and kodejenisbiaya='".$jenisbiaya."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_pengobatanplafond order by kodegolongan';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kodegolongan."</td>\r\n\t\t\t<td align=center>".$bar1->kodejenisbiaya."</td>\r\n\t\t\t<td  align=right>".$bar1->persen."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodegolongan."','".$bar1->persen."','".$bar1->kodejenisbiaya."');\"></td></tr>";
    }
}

?>