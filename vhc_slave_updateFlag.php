<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$param = $_POST;
$periode=$param['periode'];
$tipe=$param['tipe'];
$kodeorg=$_SESSION['empl']['lokasitugas'];
$str="insert into ".$dbname.".flag_alokasi(kodeorg,periode,tipe) values('".$kodeorg."','".$periode."','".$tipe."')";
if(mysql_query($str))
{
    
}
 else {
     //let error pass 
     // echo " Error ".mysql_error($conn);    
}
?>