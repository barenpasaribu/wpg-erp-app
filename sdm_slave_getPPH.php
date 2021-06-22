<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$lokasitugas		=$_POST['lokasitugas'];



   $strx="select isPPHGrossUp from ".$dbname.".organisasi where kodeorganisasi='".$lokasitugas."'";
   //echo "warning: ".$strx;
	$res = mysql_query($strx);
	   while($bar=mysql_fetch_object($res))
        {
			echo $bar->isPPHGrossUp;
		}
    /*if(mysql_query($strx))
	{
	   $res = mysql_query($strx);
	   while($bar=mysql_fetch_object($res))
        {
			echo "warning: ".$bar->isPPHGrossUp;
		}
	}
	else
	{
		echo " Gagal:".addslashes(mysql_error($conn)).$strx;
	}*/

?>
