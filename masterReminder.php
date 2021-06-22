<?php



session_start();
include 'config/connection.php';
include 'lib/eagrolib.php';
$userid = $_SESSION['standard']['userid'];
$str = " select persetujuan1,persetujuan2,persetujuan3,\r\n         persetujuan4,persetujuan5,\r\n\t\t hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3,\r\n\t\t hasilpersetujuan4,hasilpersetujuan5\r\n\t\t from ".$dbname.".log_prapoht \r\n\t\t where close<2 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))\r\n\t\t or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))\r\n\t\t or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3=''))\r\n\t\t or (persetujuan4=".$userid." and (hasilpersetujuan4 is null or hasilpersetujuan4=''))\r\n\t\t or (persetujuan5=".$userid." and (hasilpersetujuan5 is null or hasilpersetujuan5='')))";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "<hr>Purchase Request(s) need your approval<br><a href=# onclick=\"window.location='log_persetuuanPp.php'\">Click Here</a>";
}

$str = " select persetujuan1,persetujuan2,persetujuan3,\r\n\t\t hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3\r\n\t\t from ".$dbname.".log_poht \r\n\t\t where stat_release<1 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))\r\n\t\t or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))\r\n\t\t or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3='')))";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "<hr>Purchase Order(s) need your approval<br><a href=# onclick=\"window.location='log_persetujuan_po.php'\">Click Here</a>";
}

$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n        (persetujuan=".$_SESSION['standard']['userid']." and statuspersetujuan=0)\r\n\t\tor (hrd=".$_SESSION['standard']['userid'].' and statushrd=0)';
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo '<hr>'.$_SESSION['lang']['perjalanandinas']." need your approval<br><a href=# onclick=\"window.location='sdm_3persetujuanPJD.php'\">Click Here</a>";
}

?>