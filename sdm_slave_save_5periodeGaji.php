<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorg'];
$metodepenggajian = $_POST['metodepenggajian'];
$periode = $_POST['periode'];
$tanggalmulai = tanggalsystem($_POST['tanggalmulai']);
$tanggalsampai = tanggalsystem($_POST['tanggalsampai']);
$tutup = $_POST['tutup'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5periodegaji set\r\n\t       tanggalmulai=".$tanggalmulai.",\r\n\t\t   tanggalsampai=".$tanggalsampai.",\r\n\t\t   sudahproses=".$tutup."\r\n\t       where kodeorg='".$kodeorg."' and periode='".$periode."'\r\n\t\t   and jenisgaji='".$metodepenggajian."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".sdm_5periodegaji \r\n\t      (kodeorg,periode,tanggalmulai,tanggalsampai,sudahproses,jenisgaji)\r\n\t      values('".$kodeorg."','".$periode."',".$tanggalmulai.','.$tanggalsampai.','.$tutup.",'".$metodepenggajian."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5periodegaji\r\n\t      where kodeorg='".$kodeorg."' and periode='".$periode."'\r\n\t\t  and jenisgaji='".$metodepenggajian."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}
$str1 = "select *,\r\n\t     case jenisgaji when 'H' then '".$_SESSION['lang']['harian']."'\r\n\t\t when 'B' then '".$_SESSION['lang']['bulanan']."'\r\n\t\t end as ketgroup, \r\n\t\t case sudahproses when '1' then '".$_SESSION['lang']['yes']."'\r\n\t\t when '0' then '".$_SESSION['lang']['no']."'\r\n\t\t end as sts\r\n\t     from ".$dbname.".sdm_5periodegaji \r\n\t\t where LEFT(kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t order by periode desc";
if ($res1 = mysql_query($str1)) {
    while ($bar1 = mysql_fetch_object($res1)) {
        echo "<tr class=rowcontent>\r\n\t\t\t           <td align=center>".$bar1->kodeorg."</td>\r\n\t\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t\t   <td align=center>".$bar1->periode."</td>\r\n\t\t\t\t\t   <td align=center>".tanggalnormal($bar1->tanggalmulai)."</td>\r\n\t\t\t\t\t   <td align=center>".tanggalnormal($bar1->tanggalsampai)."</td>\r\n\t\t\t\t\t   <td align=center>".$bar1->sts."</td>\r\n\t\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenisgaji."','".$bar1->periode."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalsampai)."','".$bar1->sudahproses."');\"></td></tr>";
    }
}

?>