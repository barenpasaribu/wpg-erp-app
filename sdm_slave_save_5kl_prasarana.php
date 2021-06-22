<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodejabatan = $_POST['kodejabatan'];
$namajabatan = $_POST['namajabatan'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5kl_prasarana set nama='".$namajabatan."'\r\n\t       where kode='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5kl_prasarana (kode,nama)\r\n\t      values('".$kodejabatan."','".$namajabatan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5kl_prasarana\r\n\twhere kode='".$kodejabatan."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = 'select * from '.$dbname.'.sdm_5kl_prasarana order by kode';
if ($res1 = mysql_query($str1)) {
    echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodekelompok'].'</td><td>'.$_SESSION['lang']['namakelompok']."</td><td  style='width:30px;'>*</td></tr>\r\n\t </thead>\r\n\t <tbody>";
    while ($bar1 = mysql_fetch_object($res1)) {
        echo '<tr class=rowcontent><td align=center>'.$bar1->kode.'</td><td>'.$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->nama."');\"></td></tr>";
    }
    echo "\t \r\n\t </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>";
}

?>