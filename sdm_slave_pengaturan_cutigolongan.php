<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/eksilib.php');
$ids=$_POST['ids'];
$hakcuti=$_POST['hakcuti'];
$sisacutibr=$_POST['sisacutibr'];
$masatunggu=$_POST['masatunggu'];
$org=$_POST['org'];
$gol=$_POST['gol'];

$method=$_POST['method'];



switch($method)
{
case 'update':
        $str="update ".$dbname.".sdm_5hak_cuti set kodegolongan='".$gol."',lokasitugas='".$org."',hakcuti=".$hakcuti.", sisacutiberlaku = ".$sisacutibr."
               where id=".$ids;
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));}
    break;
case 'insert':
			$strcari="select id from ".$dbname.".sdm_5hak_cuti where 
			lokasitugas='".$org."' and kodegolongan = '".$gol."' ";
			$rescari = mysql_query($strcari);
			$countcari = mysql_num_rows($rescari);
			if ($countcari<1){
				$str="insert into ".$dbname.".sdm_5hak_cuti
                  (kodegolongan,lokasitugas,hakcuti,sisacutiberlaku,masatunggu)
                  values('".$gol."','".$org."',".$hakcuti.",".$sisacutibr.",".$masatunggu.")";
			}
			else {
				//echo "warning: ".$_SESSION['lang']['datasudahada'];
				while($bar=mysql_fetch_object($rescari)){
					$str="update ".$dbname.".sdm_5hak_cuti set isactive=1 where id=".$bar->id;
				}
				if(mysql_query($str))
				{}
				else
				{echo " Gagal,".addslashes(mysql_error($conn));}
			}
            if(mysql_query($str))
            {}
            else
            {}//echo " Gagal,".addslashes(mysql_error($conn));
        break;
case 'delete':

	$str="update ".$dbname.".sdm_5hak_cuti set isactive=0 where id=".$ids;
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case'loadData':
$str1="select a.*, b.namaorganisasi, c.namagolongan from ".$dbname.".sdm_5hak_cuti a
left join ".$dbname.".organisasi b on a.lokasitugas = b.kodeorganisasi 
left join ".$dbname.".sdm_5golongan c on a.kodegolongan = c.kodegolongan where a.isactive=1";
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
			
		echo"<tr class=rowcontent>
				<td>".$bar1->namaorganisasi."</td>                             
				<td>".$bar1->namagolongan."</td> 
				<td>".$bar1->hakcuti."</td>                             
				<td>".$bar1->masatunggu."</td>                             				                         
				<td>".$bar1->sisacutiberlaku."</td>                             				                         
				 <td align=center>
				 <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->id."','".$bar1->lokasitugas."','".$bar1->kodegolongan."','".$bar1->hakcuti."','".$bar1->sisacutiberlaku."','".$bar1->masatunggu."');\">
				 <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delField('".$bar1->id."');\">
				 <!--<img src=images/application/application_double.png class=resicon  caption='Copy' onclick=\"insField('".$bar1->id."','".$bar1->lokasitugas."','".$bar1->kodegolongan."','".$bar1->hakcuti."','".$bar1->sisacutiberlaku."','".$bar1->masatunggu."');\">-->
				 </td> 
			  </tr>";
	}
}
break;
default:
   break;					
}
?>
