<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

$kode="";
IF(ISSET($_POST['kode'])){
	$kode=$_POST['kode'];
}
$keterangan="";
IF(ISSET($_POST['keterangan'])){
	$keterangan=$_POST['keterangan'];
}
$jumlahhk="";
IF(ISSET($_POST['jumlahhk'])){
	$jumlahhk=$_POST['jumlahhk'];
}
$group="";
IF(ISSET($_POST['grup'])){
	$group=$_POST['grup'];
}
$sandibank="";
IF(ISSET($_POST['sandibank'])){
	$sandibank=$_POST['sandibank'];
}
$method="";
IF(ISSET($_POST['method'])){
	$method=$_POST['method'];
}


switch($method)
{
case 'update':
	/*
     $sCek="select distinct * from ".$dbname.".keu_5bank where namabank like '%".$jumlahhk."%'";
    $qCek=mysql_query($sCek) or die(mysql_error($conn));
    $rCek=mysql_num_rows($qCek);
    if($rCek!=0)
    {
        exit("Error:Data Sudah Ada");
    }
	*/
	$str="update ".$dbname.".keu_5bank set namabank='".$jumlahhk."',sandibank='".$sandibank."'
	       where noakun='".$group."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
/*
    $sCek="select distinct * from ".$dbname.".keu_5bank where namabank like '%".$jumlahhk."%'";
    $qCek=mysql_query($sCek) or die(mysql_error($conn));
    $rCek=mysql_num_rows($qCek);
    if($rCek!=0)
    {
        exit("Error:Data Sudah Ada");
    }
*/	
	$str="delete from ".$dbname.".keu_5bank
	where namabank='".$jumlahhk."' and noakun='".$group."'";
	mysql_query($str);
	$str="insert into ".$dbname.".keu_5bank
	      (noakun,namabank,sandibank)
	      values('".$group."','".$jumlahhk."','".$sandibank."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".keu_5bank
	where namabank='".$jumlahhk."' and noakun='".$group."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case'loadData':
$str1="select noakun,namabank,ifnull(sandibank,' ') as sandibank from ".$dbname.".keu_5bank order by namabank";
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
	echo"<tr class=rowcontent>
			   <td align=center>".$bar1->noakun."</td>
					   <td>".$bar1->namabank."</td>
					   <td>".$bar1->sandibank."</td>
					   <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->noakun."','".$bar1->namabank."','".$bar1->sandibank."');\"></td></tr>";
	}
}
break;
default:
   break;					
}


?>
