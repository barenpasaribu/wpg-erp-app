<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/eksilib.php');

$ids=$_POST['ids'];
$tarif=$_POST['tarif'];
$method=$_POST['method'];
$org=$_POST['org'];
$gol=$_POST['gol'];

switch($method)
{
	case 'insert':
			$strcari="select id,isactive from sdm_5tariflembur where
			lokasitugas='".$org."' and kodegolongan = '".$gol."' and tarifdasar='".$tarif."'";
			$rescari = $eksi->sSQL($strcari);
			if ($eksi->sSQLnum($strcari)<1){
				$str="insert into sdm_5tariflembur
                  (lokasitugas,kodegolongan,tarifdasar)
                  values('".$org."','".$gol."','".$tarif."')";
				  
			}
			else {
				foreach($rescari as $barcari){
					$oldid = $barcari['id'];
					if($barcari['isactive']==0){
						$str="update sdm_5tariflembur set isactive=1 where id=".$oldid;
					}
					else {
						$str="";
						echo "warning: ".$_SESSION['lang']['datasudahada'];
					}
				}
			}
			$eksi->exc($str);
			
        break;
	case 'update':
		$str="update sdm_5tariflembur set lokasitugas='".$org."',kodegolongan='".$gol."',tarifdasar='".$tarif."'
			   where id=".$ids;
		$eksi->exc($str);
		
    break;
	case 'delete':
		$str="update sdm_5tariflembur set isactive=0 where id=".$ids;
		$eksi->exc($str);
	break;
/*case 'update':
        $str="update ".$dbname.".keu_5pengakuangaji set noakundebet='".$debet."',noakunkredit='".$kredit."',lokasitugas='".$org."',golongan='".$gol."',updateby=".$_SESSION['standard']['userid']."
               where id=".$ids;
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));}
    break;
case 'insert':
			$strcari="select id,isactive from ".$dbname.".keu_5pengakuangaji where idkomponen = '".$potongan."' and
			lokasitugas='".$org."' and golongan = '".$gol."' and kodekomponen='".$codename."'";
			$rescari = mysql_query($strcari);
			$countcari = mysql_num_rows($rescari);
			if ($countcari<1){
				$str="insert into ".$dbname.".keu_5pengakuangaji
                  (idkomponen,noakundebet,noakunkredit,lokasitugas,golongan,updateby,kodekomponen)
                  values('".$potongan."','".$debet."','".$kredit."','".$org."','".$gol."',".$_SESSION['standard']['userid'].",'".$codename."')";
			}
			else {
				while ($barcari=mysql_fetch_object($rescari)){
					$act = $barcari->isactive;
					$oldid = $barcari->id;
				}
				if ($act==0){
					$str="update ".$dbname.".keu_5pengakuangaji set isactive=1, updateby =".$_SESSION['standard']['userid']." where id=".$oldid;
				}
				else {
					$str="";
					echo "warning: ".$_SESSION['lang']['datasudahada'];
				}
			}
            
            if(mysql_query($str))
            {}
            else
            {}
        break;
case 'delete':
	$str="update ".$dbname.".keu_5pengakuangaji set isactive=0, updateby =".$_SESSION['standard']['userid']." where id=".$ids;
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;*/
		
	case'loadData':
	//tambah filter lokasi tugas ==Jo 06-05-2017==
	if($_SESSION['empl']['pusat']==1){
		$whrorg="";
	}
	else{
		$whrorg="and a.lokasitugas ='".$_SESSION['empl']['lokasitugas']."'";
	}
		$str1="select a.*,b.namagolongan,c.namaorganisasi from sdm_5tariflembur a
		left join sdm_5golongan b on a.kodegolongan=b.kodegolongan
		left join organisasi c on a.lokasitugas=c.kodeorganisasi
		where a.isactive=1 ".$whrorg." ";
		$res1=$eksi->sSQL($str1);
		foreach($res1 as $bar1){
			 echo"<tr class=rowcontent>
				<td align=center>".$bar1['namaorganisasi']."</td>
				<td>".$bar1['namagolongan']."</td>
				<td>".number_format($bar1['tarifdasar'],2,'.',',')."</td>                             
				                             
				 <td align=center>
				 <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['id']."','".$bar1['lokasitugas']."','".$bar1['kodegolongan']."','".$bar1['tarifdasar']."');\">
				 <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delField('".$bar1['id']."');\">
				 
				 </td> 
			  </tr>";
		}
        
        break;
default:
   break;					
}
?>
