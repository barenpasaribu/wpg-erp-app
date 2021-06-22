<?
require_once('master_validation.php');
require_once('config/connection.php');

$idkry = $_POST['idkry'];
$tahunhc = $_POST['tahunhc'];
$periodehc1 = date('Y-m-d', strtotime($_POST['periodehc1']));
$periodehc2 = date('Y-m-d', strtotime($_POST['periodehc2']));
$jumlahhc= $_POST['jumlahhc'];
$method=$_POST['method'];

switch($method)
{
case 'generate':
	$inshc = "insert into ".$dbname.".sdm_cutiht (kodeorg, karyawanid, periodecuti, keterangan, dari, sampai, hakcuti, diambil, sisa) 
			values ('".$_SESSION['empl']['lokasitugas']."', '".$idkry."', '".$tahunhc."','', '".$periodehc1."', '".$periodehc2."', '".$jumlahhc."', 0, '".$jumlahhc."')";
			if(mysql_query($inshc))
			{}
			else
			{echo " Gagal,".addslashes(mysql_error($conn));}
	break;

/*case 'update':	
	$str="update ".$dbname.".sdm_jenis_ijin_cuti set jenisizin='".$jenisijin."'
	       where id=".$kode."";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_jenis_ijin_cuti (id,jenisizin)
	      values(".$kode.",'".$jenisijin."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_jenis_ijin_cuti
	where id=".$kode."";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'deletes':
	$str="update ".$dbname.".sdm_jenis_ijin_cuti set status= 0 where id=".$kode."";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;*/
default:
   break;					
}
/*$str1="select * from ".$dbname.".sdm_jenis_ijin_cuti where status = 1 order by id";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
	 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['tipe']."</td><td>".$_SESSION['lang']['jenisijin']."</td><td  style='width:50px;'>*</td></tr>
	 </thead>
	 <tbody>";
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->id."</td><td>".$bar1->jenisizin."</td>
		<td>
		<img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->id."','".$bar1->jenisizin."');\">
		<img src=images/application/application_delete.png class=resicon  title='Hapus Data' onclick=\"hapusJI('".$bar1->id."');\">
		</td></tr>";
}	 
echo"	 
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>";
}*/
?>