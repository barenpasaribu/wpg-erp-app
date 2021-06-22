<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

$departemen=$_POST['departemen'];//kueri atasan penyetuju P.Dinas sesuai setting ==JO 29-11-2016==

//$optAts="<option value=''></option>";

//$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if ($departemen==''){
	$skry="select a.karyawanid, b.namakaryawan from ".$dbname.".setup_approval_hrd a 
	left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
	where a.applikasi='PAHR' and a.kodeunit='".substr($_SESSION['empl']['lokasitugas']
	,0,4)."' order by b.namakaryawan";
}
else if ($departemen!=''){
	$skry="select a.karyawanid, b.namakaryawan from ".$dbname.".setup_approval_hrd a 
	left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
	where a.applikasi='PAHR' and a.kodeunit='".substr($_SESSION['empl']['lokasitugas']
	,0,4)."' and b.bagian='".$departemen."' order by b.namakaryawan";
}

$qkry=mysql_query($skry) or die(mysql_error());
while($rkry=mysql_fetch_assoc($qkry))
{
//    $jenis=$rJenis['kategori'].'##'.$rJenis['topik'];
    $optAts.="<option value='".$rkry['karyawanid']."'>".$rkry['namakaryawan']."</option>";
}
 
echo $optAts;

?>