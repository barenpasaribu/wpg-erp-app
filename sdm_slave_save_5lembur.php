<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$tipelembur = $_POST['tipelembur'];
$jamaktual = $_POST['jamaktual'];
$jamlembur = $_POST['jamlembur'];
$method = $_POST['method'];
if ('' == $jamaktual) {
    $jamaktual = 0;
}

if ('' == $jamlembur) {
    $jamlembur = 0;
}

switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5lembur set jamlembur='".$jamlembur."'\r\n\t       where kodeorg='".$kodeorg."' and tipelembur='".$tipelembur."'\r\n\t\t   and jamaktual=".$jamaktual;
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5lembur \r\n\t      (kodeorg,tipelembur,jamaktual,jamlembur)\r\n\t      values('".$kodeorg."','".$tipelembur."',".$jamaktual.','.$jamlembur.')';
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5lembur\r\n\t where kodeorg='".$kodeorg."' and tipelembur='".$tipelembur."'\r\n\t and jamaktual=".$jamaktual;
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = "select *,\r\n\t     case tipelembur when '0' then '".$_SESSION['lang']['haribiasa']."'\r\n\t\t when '1' then '".$_SESSION['lang']['hariminggu']."'\r\n\t\t when '2' then '".$_SESSION['lang']['harilibur']."'\r\n\t\t when '3' then '".$_SESSION['lang']['hariraya']."'\r\n\t\t end as ketgroup \r\n\t     from ".$dbname.".sdm_5lembur \r\n\t\t where LEFT(kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t order by tipelembur,jamaktual";
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$bar1->kodeorg."</td>\r\n\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t   <td align=center>".$bar1->jamaktual."</td>\r\n\t\t\t\t   <td align=center>".$bar1->jamlembur."</td>\r\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."','".$bar1->jamlembur."');\"></td></tr>";
    }
}

?>