<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodejabatan = $_POST['kodejabatan'];
$namajabatan = $_POST['namajabatan'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5jabatan set namajabatan='".$namajabatan."'\r\n\t       where kodejabatan='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5jabatan (kodejabatan,namajabatan)\r\n\t      values('".$kodejabatan."','".$namajabatan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5jabatan \r\n\twhere kodejabatan='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_5jabatan order by kodejabatan';
if ($res1 = mysql_query($str1)) {
    echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['functioncode'].'</td><td>'.$_SESSION['lang']['functionname']."</td><td  style='width:30px;'>*</td></tr>\r\n\t </thead>\r\n\t <tbody>";
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kodejabatan.'</td><td>'.$bar1->namajabatan."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->namajabatan."');\">&nbsp;<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"DeleteField('".$bar1->kodejabatan."','".$bar1->namajabatan."');\"></td></tr>";
    }
    echo "\t \r\n\t </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>";
}

?>