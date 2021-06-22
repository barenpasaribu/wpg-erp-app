<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$edulevel = $_POST['edulevel'];
$eduname = $_POST['eduname'];
$edugroup = $_POST['edugroup'];
$eduid = $_POST['eduid'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5pendidikan set pendidikan='".$eduname."',\r\n\t      levelpendidikan=".$edulevel.",kelompok='".$edugroup."' \r\n\t       where idpendidikan=".$eduid;
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5pendidikan (levelpendidikan,pendidikan,kelompok)\r\n\t      values(".$edulevel.",'".$eduname."','".$edugroup."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5pendidikan\r\n\twhere idpendidikan=".$eduid;
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_5pendidikan order by levelpendidikan';
if ($res1 = mysql_query($str1)) {
    echo "<table class=sortable cellspacing=1 border=0 style='width:600px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['edulevel'].'</td><td>'.$_SESSION['lang']['eduname'].'</td><td>'.$_SESSION['lang']['edugroup']."</td><td style='width:70px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->levelpendidikan.'</td><td>'.$bar1->pendidikan.'</td><td>'.$bar1->kelompok."</td><td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->levelpendidikan."','".$bar1->pendidikan."','".$bar1->kelompok."',".$bar1->idpendidikan.");\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan(".$bar1->idpendidikan.');"></td></tr>';
    }
    echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
}

?>