<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodetipe = $_POST['kodetipe'];
$namatipe = $_POST['namatipe'];
$namatipe1 = $_POST['namatipe1'];
$noakun = $_POST['noakun'];
$noakunak = $_POST['noakunak'];
$tppenyusutan = $_POST['tppenyusutan'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5tipeasset set namatipe='".$namatipe."',namatipe1='".$namatipe1."'\r\n\t       ,noakun='".$noakun."',akunak='".$noakunak."',metodepenyusutan='".$tppenyusutan."'\r\n\t       where kodetipe='".$kodetipe."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5tipeasset (kodetipe,namatipe,namatipe1,noakun,akunak,metodepenyusutan)\r\n\t      values('".$kodetipe."','".$namatipe."','".$namatipe1."','".$noakun."','".$noakunak."','".$tppenyusutan."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5tipeasset \r\n\twhere kodetipe='".$kodetipe."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$stru = 'select noakun,namaakun from '.$dbname.'.keu_5akun';
$res = mysql_query($stru);
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
}
$str1 = 'select * from '.$dbname.".sdm_5tipeasset\r\n\t\t   order by namatipe";
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo "<tr class=rowcontent>\r\n\t\t     <td align=center>".$bar1->kodetipe."</td>\r\n\t\t\t <td>".$bar1->namatipe."</td>\r\n             <td>".$bar1->namatipe1."</td>\r\n\t\t\t <td>".$namaakun[$bar1->noakun]."</td>\r\n             <td>".$namaakun[$bar1->akunak]."</td>\r\n\t\t\t <td>".ucfirst($bar1->metodepenyusutan)."</td>\r\n\t\t\t <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodetipe."','".$bar1->namatipe."','".$bar1->namatipe1."','".$bar1->noakun."','".$bar1->akunak."','".$bar1->metodepenyusutan."');\"></td></tr>";
    }
}

?>