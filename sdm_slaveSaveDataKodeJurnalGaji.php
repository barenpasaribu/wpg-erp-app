<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $ids=$_POST['ids'];
  $kdjrn  =$_POST['kdjrn'];

   $stra="update ".$dbname.".sdm_ho_component set
	        jurnalcode='".$kdjrn."'		
			where id=".$ids;
			
		if(mysql_query($stra))
		{
			
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
?>
