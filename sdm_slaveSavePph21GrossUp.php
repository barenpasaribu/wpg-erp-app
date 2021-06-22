<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
$a=1;
$vals=$_POST['vals'];
$str="update ".$dbname.".organisasi set isPPHGrossUp=".$vals." where kodeorganisasi='".$_SESSION['empl']['lokasitugas']
."'";
//echo "warning: ".$str;
if (mysql_query($str,$conn)){
	//$strs="update ".$dbname.".datakaryawan set isPPHGrossUp=".$vals." where lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
	//rubah jadi tidak per lokasi tugas ==Jo 15-03-2017==
	$strs="update ".$dbname.".datakaryawan set isPPHGrossUp=".$vals."";
	if (mysql_query($strs,$conn)){
	}
	else {
		echo " Error: ".addslashes(mysql_error($conn));
	}
}
else {
	echo " Error: ".addslashes(mysql_error($conn));
}
	/*$kdidk = "select induk from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
	if($residk=mysql_query($kdidk,$conn)) {
	 while($baridk=mysql_fetch_object($residk))
		{
			$kdorg = "select kodeorganisasi from ".$dbname.".organisasi where induk='".$baridk->induk."'";
			if($resorg=mysql_query($kdorg,$conn)) {
				while($barorg=mysql_fetch_object($resorg))
				{
					$str="update ".$dbname.".organisasi set isPPHGrossUp=".$vals." where kodeorganisasi='".$barorg->kodeorganisasi."'";
					//echo "warning: ".$str;
					if (mysql_query($str,$conn)){
						$strs="update ".$dbname.".datakaryawan set isPPHGrossUp=".$vals." where lokasitugas='".$barorg->kodeorganisasi."'";
						if (mysql_query($strs,$conn)){
						}
						else {
							echo " Error: ".addslashes(mysql_error($conn));
						}
					}
					else {
						echo " Error: ".addslashes(mysql_error($conn));
					}
				}
			}
		}
	}*/

	
?>
