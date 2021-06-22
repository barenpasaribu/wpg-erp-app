<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kdkegiatan = $_POST['kdkegiatan'];
$ket = $_POST['ket'];
$satuan = $_POST['satuan'];
$nilsngtbaik = $_POST['nilsngtbaik'];
$nilbaik = $_POST['nilbaik'];
$nilckp = $_POST['nilckp'];
$nilkrg = $_POST['nilkrg'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".it_standard set keterangan='".$ket."',\r\n\t      satuan='".$satuan."',sangatbaik='".$nilsngtbaik."',baik='".$nilbaik."',cukup='".$nilckp."',kurang='".$nilkrg."'\r\n\t       where kodekegiatan='".$kdkegiatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".it_standard (kodekegiatan,keterangan,satuan,sangatbaik,baik,cukup,kurang)\r\n\t      values('".$kdkegiatan."','".$ket."','".$satuan."','".$nilsngtbaik."','".$nilbaik."','".$nilckp."','".$nilkrg."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".it_standard\r\n\twhere kodekegiatan='".$kdkegiatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        $str1 = 'select * from '.$dbname.'.it_standard order by kodekegiatan asc';
        if ($res1 = mysql_query($str1)) {
            echo "<table class=sortable cellspacing=1 border=0 style='width:800px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n                 <td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>\r\n                 <td>".$_SESSION['lang']['keterangan']."</td>\r\n                 <td>".$_SESSION['lang']['satuan']."</td>\r\n                <td>Sangat Baik</td>\r\n                <td>Baik</td>\r\n                <td>Cukup</td>\r\n                <td>Kurang</td>\r\n                 <td style='width:70px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
            while ($bar1 = mysql_fetch_object($res1)) {
                echo "<tr class=rowcontent>\r\n                     <td align=center>".$bar1->kodekegiatan."</td>\r\n                     <td>".$bar1->keterangan.'</td><td>'.$bar1->satuan."</td>\r\n                        <td>".$bar1->sangatbaik."</td>\r\n                        <td>".$bar1->baik."</td>\r\n                        <td>".$bar1->cukup."</td>\r\n                        <td>".$bar1->kurang."</td>\r\n                     <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodekegiatan."','".$bar1->keterangan."','".$bar1->satuan."','".$bar1->sangatbaik."','".$bar1->baik."','".$bar1->cukup."','".$bar1->kurang."');\"> \r\n                         <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan('".$bar1->kodekegiatan."');\"></td></tr>";
            }
            echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
        }

        break;
    default:
        break;
}

?>