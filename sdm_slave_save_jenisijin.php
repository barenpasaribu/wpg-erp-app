<?
require_once('master_validation.php');
require_once('config/connection.php');

$kode=$_POST['kode'];
$jenisijin=$_POST['jenisijin'];
$iscuti=$_POST['iscuti'];
$ispotong=$_POST['ispotong'];
$isdayoff=$_POST['isdayoff'];

$kdabsen=$_POST['kdabsen'];
$method=$_POST['method'];
$codes='';
//tambah insert jumlah hari ==Jo 31-03-2017==
if($_POST['jumlahhari']==''){
	$jumlahhari=0;
}
else{
	$jumlahhari=$_POST['jumlahhari'];
}

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_jenis_ijin_cuti set jenisizincuti='".$jenisijin."' , isCuti = '".$iscuti."', isPotong = '".$ispotong."', isDayOff='".$isdayoff."', jumlahhari='".$jumlahhari."', kodeabsen='".$kdabsen."' where id=".$kode."";
	if(mysql_query($str))
	{
		$isup=0;
	}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$strcslc = "select id from ".$dbname.".sdm_jenis_ijin_cuti where jenisizincuti='".$jenisijin."' and isCuti='".$iscuti."' and isPotong='".$ispotong."' and isDayOff='".$isdayoff."' and kodeabsen='".$kdabsen."'";
	$resslc = mysql_query($strcslc);
	if (mysql_num_rows($resslc)>0){
		
		while($barslc=mysql_fetch_object($resslc)){
			
			$str="update ".$dbname.".sdm_jenis_ijin_cuti set status= 1 where id=".$barslc->id."";
			if(mysql_query($str))
			{
				//echo "warning: ".$_SESSION['lang']['jenisicada']." ".$barslc->id;
				$isup=1;
				$codes=$barslc->id;
			}
			else
			{echo " Gagal,".addslashes(mysql_error($conn));}
		}
		
	}
	else {
		$str="insert into ".$dbname.".sdm_jenis_ijin_cuti (id,jenisizincuti,isCuti,isPotong,isDayOff,jumlahhari,kodeabsen)
	      values(".$kode.",'".$jenisijin."',".$iscuti.",".$ispotong.",".$isdayoff.",".$jumlahhari.",'".$kdabsen."')";
		if(mysql_query($str))
		{
			$isup=0;
		}
		else
		{echo " Gagal,".addslashes(mysql_error($conn));}
	}
	
	break;
/*case 'delete':
	$str="delete from ".$dbname.".sdm_jenis_ijin_cuti
	where id=".$kode."";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;*/
case 'deletes':
	$str="delete from ".$dbname.".sdm_jenis_ijin_cuti  where id=".$kode."";
	if(mysql_query($str))
	{
		$isup=0;
	}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;	
default:
   break;					
}
$stringic = $_SESSION['lang']['jenisicada'];
$stringic.=" ".$codes;
$str1="select * from ".$dbname.".sdm_jenis_ijin_cuti where status = 1 order by id";
if($res1=mysql_query($str1))
{
echo "<input type=hidden id=isup value=".$isup."></input>";
echo "<input type=hidden id=jenisicada value='".$stringic."'></input>";	
echo"<table class=sortable cellspacing=1 border=0 style='width:635px;'>
     <thead>
	 <tr class=rowheader><td style='width:50px;'>".$_SESSION['lang']['tipe']."</td>
	 <td>".$_SESSION['lang']['jenisijin']."</td>
	 <td style='width:50px;'>".$_SESSION['lang']['iscuti']."</td>
	<td style='width:50px;'>".$_SESSION['lang']['ispotong']."</td>
	<td style='width:50px;'>".$_SESSION['lang']['isdayoff']."</td>
	<td style='width:50px;'>".$_SESSION['lang']['jumlahhari']."</td>
		 <td style='width:50px;'>".$_SESSION['lang']['kodeabsen']."</td>
	 <td  style='width:50px;'>*</td>
	 </tr>
	 </thead>
	 <tbody>";
while($bar1=mysql_fetch_object($res1))
{		
		if ($bar1->isCuti){
			$ic = 'checked';
		}
		else {
			$ic = '';
		}
		
		if ($bar1->isPotong==1){
			$ip = 'checked';
		}
		else {
			$ip = '';
		}		
		if ($bar1->isDayOff==1){
			$idf = 'checked';
		}
		else {
			$idf = '';
		}
		echo"<tr class=rowcontent><td align=center>".$bar1->id."</td>
		<td style='width:150px;'>".$bar1->jenisizincuti."</td>
		<td style='width:50px;'><input type=checkbox  value=".$bar1->isCuti." ".$ic." disabled></td>
		<td style='width:50px;'><input type=checkbox  value=".$bar1->isPotong." ".$ip." disabled></td>
		<td style='width:50px;'><input type=checkbox  value=".$bar1->isDayOff." ".$idf." disabled></td>
		<td style='width:150px;'>".$bar1->jumlahhari."</td>
		<td style='width:50px;'>".$bar1->kodeabsen."</td>
		<td align=center style='width:50px;'>
		<img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->id."','".$bar1->jenisizincuti."',".$bar1->isCuti.",".$bar1->isPotong.",".$bar1->isDayOff.",".$bar1->jumlahhari.",'".$bar1->kodeabsen."');\">
		<img src=images/application/application_delete.png class=resicon  title='Hapus Data' onclick=\"hapusJI('".$bar1->id."');\">
		</td></tr>";
}	 
echo"	 
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>";
}
?>
