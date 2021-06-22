<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodekegiatan=$_POST['kodekegiatan'];
$namakegiatan=$_POST['namakegiatan'];
$noakun=$_POST['noakun'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".vhc_kegiatan set namakegiatan='".$namakegiatan."',
              noakun='".$noakun."'
	       where kodekegiatan='".$kodekegiatan."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".vhc_kegiatan (kodekegiatan,namakegiatan,noakun)
	      values('".$kodekegiatan ."','".$namakegiatan."','".$noakun."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".vhc_kegiatan 
	where kodekegiatan='".$kodekegiatan."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".vhc_kegiatan order by kodekegiatan";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
	 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>
             <td>".$_SESSION['lang']['namakegiatan']."</td>
              <td>".$_SESSION['lang']['noakun']."</td> 
             <td  style='width:30px;'>*</td></tr>
	 </thead>
	 <tbody>";
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodekegiatan."</td>                 
                <td>".$bar1->namakegiatan."</td>
                <td>".$bar1->noakun."</td>  
               <td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kodekegiatan."','".$bar1->namakegiatan."','".$bar1->noakun."');\"></td></tr>";
}	 
echo"	 
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>";
}
?>
