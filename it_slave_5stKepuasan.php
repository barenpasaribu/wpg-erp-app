<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$ket = $_POST['ket'];
$nilKode = $_POST['nilKode'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".it_stkepuasan set keterangan='".$ket."',\r\n\t       where kode='".$kode."' and nilai='".$nilKode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".it_stkepuasan (kode,nilai,keterangan)\r\n\t      values('".$kode."','".$nilKode."','".$ket."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".it_stkepuasan\r\n\twhere kode='".$kode."' and nilai='".$nilKode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        $str1 = 'select * from '.$dbname.'.it_stkepuasan order by kode asc';
        if ($res1 = mysql_query($str1)) {
            echo "<table class=sortable cellspacing=1 border=0 style='width:650px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n                 <td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>\r\n                 <td>Nilai</td>\r\n                 <td>".$_SESSION['lang']['keterangan']."</td>\r\n                 <td style='width:70px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
            while ($bar1 = mysql_fetch_object($res1)) {
                echo "<tr class=rowcontent>\r\n                     <td align=center>".$bar1->kode."</td>\r\n                     <td>".$bar1->nilai.'</td><td>'.$bar1->keterangan."</td>\r\n                     <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->nilai."','".$bar1->keterangan."');\"> \r\n                         <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$bar1->kode."','".$bar1->nilai."');\"></td></tr>";
            }
            echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
        }

        break;
    default:
        break;
}

?>