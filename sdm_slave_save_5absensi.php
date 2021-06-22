<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$keterangan = $_POST['keterangan'];
$jumlahhk = $_POST['jumlahhk'];
$group = $_POST['grup'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5absensi set keterangan='".$keterangan."',\r\n\t       kelompok=".$group.',nilaihk='.$jumlahhk."\r\n\t       where kodeabsen='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5absensi \r\n\t      (kodeabsen,keterangan,kelompok,nilaihk)\r\n\t      values('".$kode."','".$keterangan."',".$group.','.$jumlahhk.')';
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5absensi\r\n\twhere kodeabsen='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = "select *,\r\n\t     case kelompok when 1 then '".$_SESSION['lang']['dibayar']."'\r\n\t\t when 0 then '".$_SESSION['lang']['tidakdibayar']."'\r\n\t\t end as ketgroup \r\n\t     from ".$dbname.'.sdm_5absensi order by kodeabsen';
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$bar1->kodeabsen."</td>\r\n\t\t\t\t   <td>".$bar1->keterangan."</td>\r\n\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t   <td>".$bar1->nilaihk."</td>\r\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeabsen."','".$bar1->keterangan."','".$bar1->kelompok."','".$bar1->nilaihk."');\"></td></tr>";
    }
}

?>