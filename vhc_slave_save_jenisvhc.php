<?
require_once('master_validation.php');
require_once('config/connection.php');

$jenisvhc=$_POST['jenisvhc'];
$namajenisvhc=$_POST['namajenisvhc'];
$noakun=$_POST['noakun'];
$kelompok=$_POST['kelompok'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".vhc_5jenisvhc set namajenisvhc='".$namajenisvhc."'
	      ,noakun='".$noakun."'
		  ,kelompokvhc='".$kelompok."'
		   where jenisvhc='".$jenisvhc."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".vhc_5jenisvhc(jenisvhc,namajenisvhc,noakun,kelompokvhc)
	      values('".$jenisvhc."','".$namajenisvhc."','".$noakun."','".$kelompok."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".vhc_5jenisvhc 
	where jenisvhc='".$jenisvhc."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".vhc_5jenisvhc order by jenisvhc";
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
			echo"<tr class=rowcontent><td align=center>".$bar1->jenisvhc."</td>
		         <td>".$bar1->kelompokvhc."</td>			     
				 <td>".$bar1->namajenisvhc."</td>
				 <td>".$bar1->noakun."</td>
				 <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->jenisvhc."','".$bar1->namajenisvhc."','".$bar1->noakun."','".$bar1->kelompokvhc."');\"></td></tr>";
	}	 
}
?>
