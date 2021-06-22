<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodegolongan = $_POST['kodegolongan'];
$namagolongan = $_POST['namagolongan'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5golongan set namagolongan='".$namagolongan."'\r\n\t       where kodegolongan='".$kodegolongan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5golongan (kodegolongan,namagolongan)\r\n\t      values('".$kodegolongan."','".$namagolongan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5golongan \r\n\twhere kodegolongan='".$kodegolongan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_5golongan order by kodegolongan';
if ($res1 = mysql_query($str1)) {
    echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['levelcode'].'</td><td>'.$_SESSION['lang']['levelname']."</td><td  style='width:30px;'>*</td></tr>\r\n\t </thead>\r\n\t <tbody>";
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kodegolongan.'</td><td>'.$bar1->namagolongan."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodegolongan."','".$bar1->namagolongan."');\">&nbsp;<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"DeleteField('".$bar1->kodegolongan."','".$bar1->namagolongan."');\"></td></tr>";
    }
    echo "\t \r\n\t </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>";
}

?>