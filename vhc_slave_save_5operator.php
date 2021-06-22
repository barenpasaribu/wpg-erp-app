<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdKry=$_POST['kdKry'];
$stat=$_POST['status'];
$kodeOrg=$_POST['kodeOrg'];
$kdVhc=$_POST['kdVhc'];

switch($proses)
{
	case'insert_karyawan':
	if($kdKry=='')
	{
		echo"warning:Please Select Karyawan";
		exit();
	}
/*
	$sqlCek="select * from ".$dbname.".vhc_5operator where karyawanid='".$kdKry."'";
	$queryCek=mysql_query($sqlCek) or die(mysql_error());
	$rowCek=mysql_fetch_row($queryCek);
	if($rowCek<1)
	{
*/
		$skry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
		$qkry=mysql_query($skry) or die(mysql_error());
		$rkry=mysql_fetch_assoc($qkry);
		$sqlIns="insert into ".$dbname.".vhc_5operator (`karyawanid`,`nama`,`aktif`,`vhc`) values ('".$kdKry."','".$rkry['namakaryawan']."','".$stat."','".$kdVhc."')";
		if(mysql_query($sqlIns))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
/*	
	}
	else
	{
		echo"warning:Already Insert";
                exit();
	}
*/	
	break;
	case'deleteKry':
	$sdel="delete from ".$dbname.".vhc_5operator where karyawanid='".$kdKry."'";
	if(mysql_query($sdel))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);
	break;
	case'load_new_data':
           // exit("Error:masuk");
	$limit=25;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	$optLtgs=makeOption($dbname, 'datakaryawan','karyawanid,lokasitugas');
        
	$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by nama asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	$arrPos=array("NonAktif","Aktif");
	$str="select * from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by nama asc limit ".$offset.",".$limit."";
        //echo $str;
	if($res=mysql_query($str))
	{
	while($bar=mysql_fetch_object($res))
            {

            $no+=1;
            //echo $minute_selesai; exit();
            
                echo"<tr class=rowcontent id='tr_".$no."'>
                <td>".$no."</td>
                <td>".$bar->karyawanid."</td>
                <td>".$bar->nama."</td>
                <td>".$arrPos[$bar->aktif]."</td>
                <td>".$bar->vhc."</td>
                <td>
                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->karyawanid."','".$bar->aktif."','".$bar->vhc."');\">		
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delOpt('".$bar->karyawanid."');\">
                </td>
                </tr>";
            
          }
	echo" <tr><td colspan=5 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
        }	
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	break;
	case'update_karyawan':
	$sql="update ".$dbname.".vhc_5operator set aktif='".$stat."',vhc='".$kdVhc."' where karyawanid='".$kdKry."'";
	if(mysql_query($sql))
	echo"";
	else
	echo " Gagal,".(mysql_error($conn));
	break;
	case'getKrywan':
	$sDtkry="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$kodeOrg."'";
	$qDtkry=mysql_query($sDtkry) or die(mysql_error());
	while($rDtkry=mysql_fetch_assoc($qDtkry))
	{
		$optKry.="<option value=".$rDtkry['karyawanid']." ".($rDtkry['karyawanid']==$kdKry?'selected':'').">".$rDtkry['namakaryawan']."</option>";
	}
	echo $optKry;
	break;
	default:
	break;
}

?>