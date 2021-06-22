<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$periode = $_SESSION['bonusperiode'];
$userid = $_POST['userid'];
$val = $_POST['val'];
$terbilang = $_POST['terbilang'];
$str = 'delete from '.$dbname.'.sdm_ho_detailmonthly where karyawanid='.$userid." \r\n         and periode='".$periode."' and type='jaspro'";
mysql_query($str);
$str = 'insert into '.$dbname.".sdm_ho_detailmonthly\r\n   (karyawanid,component,value,periode,plus,updatedby,type)\r\n   value(".$userid.',1,'.$val.",'".$periode."',1,'".$_SESSION['standard']['username']."','jaspro')";
if (mysql_query($str)) {
    $str1 = 'delete from '.$dbname.".sdm_ho_payrollterbilang where periode='".$periode."'\r\n\t\t      and userid=".$userid." and type='jaspro'";
    mysql_query($str1);
    $str2 = 'insert into '.$dbname.".sdm_ho_payrollterbilang (userid,periode,terbilang,type)\r\n\t\t       values(".$userid.",'".$periode."','".$terbilang."','jaspro')";
    mysql_query($str2);
} else {
    echo ' Error: '.addslashes(mysql_error($conn)).$str;
}

?>