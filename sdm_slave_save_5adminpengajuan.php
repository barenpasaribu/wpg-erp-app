<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/eksilib.php');

$ids=$_POST['ids'];

$method=$_POST['method'];
$org=$_POST['org'];
$kryw=$_POST['kryw'];
$user=$_POST['user'];

switch($method)
{
	case 'getkry':
		$optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sKry="select karyawanid,namakaryawan from datakaryawan where lokasitugas='".$org."' order by namakaryawan";
		$resKry=$eksi->sSQL($sKry);
		foreach($resKry as $barKry){
			$optKry.="<option value='".$barKry['karyawanid']."'>".$barKry['namakaryawan']."</option>";
		}
		echo $optKry;
	break;
	
	case 'getusr':
		$optUsr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sUsr="select a.namauser, a.karyawanid, b.namakaryawan from user a
		left join datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas ='".$org."' order by b.namakaryawan";
		$resUsr=$eksi->sSQL($sUsr);
		foreach($resUsr as $barUsr){
			$optUsr.="<option value='".$barUsr['karyawanid']."'>".$barUsr['namakaryawan']." (".$barUsr['namauser'].")</option>";
		}

		echo $optUsr;
	break;
	
	case 'insert':
			$strcari="select id,isactive from sdm_5admin_pengajuan where
			lokasitugas='".$org."' and karyawanid = '".$kryw."' and userloginid='".$user."'";
			$rescari = $eksi->sSQL($strcari);
			if ($eksi->sSQLnum($strcari)<1){
				$str="insert into sdm_5admin_pengajuan
                  (lokasitugas,karyawanid,userloginid)
                  values('".$org."','".$kryw."','".$user."')";
				  
			}
			else {
				foreach($rescari as $barcari){
					$oldid = $barcari['id'];
					if($barcari['isactive']==0){
						$str="update sdm_5admin_pengajuan set isactive=1 where id=".$oldid;
					}
					else {
						$str="";
						echo "warning: ".$_SESSION['lang']['datasudahada'];
					}
				}
			}
			if ($kryw==$user){
				echo "warning: ".$_SESSION['lang']['adminkaryawansama'];
			}
			else {
				$eksi->exc($str);
			}
			
			
        break;
	case 'update':
		$str="update sdm_5admin_pengajuan set lokasitugas='".$org."',karyawanid = '".$kryw."',userloginid='".$user."'
			   where id=".$ids;
		if ($kryw==$user){
				echo "warning: ".$_SESSION['lang']['adminkaryawansama'];
			}
			else {
				$eksi->exc($str);
			}
		
    break;
	case 'delete':
		$str="update sdm_5admin_pengajuan set isactive=0 where id=".$ids;
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
		//tambah filter lokasi tugas ==Jo 07-07-2017==
		if($_SESSION['empl']['pusat']==1){
			$whr="";
		}
		else{
			$whr="and a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
		}

		$str1="select a.*,b.namaorganisasi, c.namakaryawan as namakry, e.namakaryawan as useradmin, d.namauser from sdm_5admin_pengajuan a
		left join organisasi b on a.lokasitugas=b.kodeorganisasi
		left join datakaryawan c on a.karyawanid=c.karyawanid
		left join user d on a.userloginid = d.karyawanid
		left join datakaryawan e on d.karyawanid = e.karyawanid
		where a.isactive=1 ".$whr." order by b.namaorganisasi, d.namauser";
		//group by a.karyawanid,a.userloginid
		$res1=$eksi->sSQL($str1);
		foreach($res1 as $bar1){
			 echo"<tr class=rowcontent>
				<td>".$bar1['namaorganisasi']."</td>
				<td>".$bar1['namakry']."</td>
				<td>".$bar1['useradmin']."(".$bar1['namauser'].")</td>                             
				                             
				 <td align=center>
				 <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['id']."','".$bar1['lokasitugas']."','".$bar1['karyawanid']."','".$bar1['userloginid']."');\">
				 <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delField('".$bar1['id']."');\">
				 
				 </td> 
			  </tr>";
		}
        
        break;
	case 'refresh_data':
		if($_POST['filter']!='' && $_POST['keyword']!='') {
			
			switch($_POST['filter']){
				case 'namaorganisasi':
					$filter = "and b.".$_POST['filter']." like '%".$_POST['keyword']."%'";
				break;
				
				case 'namakaryawan':
					$filter = "and c.".$_POST['filter']." like '%".$_POST['keyword']."%'";
				break;
				case 'namakaryawan2':
					$filter = "and e.".substr($_POST['filter'],0,strlen($_POST['filter'])-1)." like '%".$_POST['keyword']."%'";
				break;
				
				default:
				break;
			}
		}
		else {
			$filter = "";
		}
		
		$str1="select a.*,b.namaorganisasi, c.namakaryawan as namakry, e.namakaryawan as useradmin, d.namauser from sdm_5admin_pengajuan a
		left join organisasi b on a.lokasitugas=b.kodeorganisasi
		left join datakaryawan c on a.karyawanid=c.karyawanid
		left join user d on a.userloginid = d.karyawanid
		left join datakaryawan e on d.karyawanid = e.karyawanid
		where a.isactive=1 ".$filter." order by b.namaorganisasi, d.namauser";
		//group by a.karyawanid,a.userloginid
		$res1=$eksi->sSQL($str1);
		foreach($res1 as $bar1){
			 echo"<tr class=rowcontent>
				<td>".$bar1['namaorganisasi']."</td>
				<td>".$bar1['namakry']."</td>
				<td>".$bar1['useradmin']."(".$bar1['namauser'].")</td>                             
				                             
				 <td align=center>
				 <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['id']."','".$bar1['lokasitugas']."','".$bar1['karyawanid']."','".$bar1['userloginid']."');\">
				 <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"delField('".$bar1['id']."');\">
				 
				 </td> 
			  </tr>";
		}
		break;
		
	default:
		break;					
}
?>
