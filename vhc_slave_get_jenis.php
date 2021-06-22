<?
require_once('master_validation.php');
require_once('config/connection.php');

$kelompok=$_POST['kelompok'];
  $str="select * from ".$dbname.".vhc_5jenisvhc where kelompokvhc='".$kelompok."' order  by namajenisvhc";
  $res=mysql_query($str);
  $optjnsvhc="<option value=''></option>";;
  while($bar=mysql_fetch_object($res))
  {
  	$optjnsvhc.="<option value='".$bar->jenisvhc."'>".$bar->namajenisvhc."</option>";
  }
echo  $optjnsvhc; 
?>
