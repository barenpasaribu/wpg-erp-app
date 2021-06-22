<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

$golongan=$_POST['golongan'];
$departemen=$_POST['departemen'];
$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//tambah filter per organisasi ==Jo 19-06-2017==
if ($golongan!='' && $departemen==''){
	$sJenis="select * from ".$dbname.".sdm_5matriktraining where isdelete=0 and kodegolongan='".$golongan."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kategori, topik asc";
}
else if ($golongan=='' && $departemen!=''){
	$sJenis="select * from ".$dbname.".sdm_5matriktraining where isdelete=0 and kodedepartemen='".$departemen."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kategori, topik asc";
}
else if ($golongan!='' && $departemen!=''){
	$sJenis="select * from ".$dbname.".sdm_5matriktraining where isdelete=0 and kodegolongan='".$golongan."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodedepartemen='".$departemen."' order by kategori, topik asc";
}
else if ($golongan=='' && $departemen==''){
	$sJenis="select * from ".$dbname.". sdm_5matriktraining where isdelete=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kategori, topik asc";
}
$qJenis=mysql_query($sJenis) or die(mysql_error());
while($rJenis=mysql_fetch_assoc($qJenis))
{
//    $jenis=$rJenis['kategori'].'##'.$rJenis['topik'];
    $optJenis.="<option value='".$rJenis['matrixid']."'>".$rJenis['kategori'].' - '.$rJenis['topik']."</option>";
}
 
echo $optJenis;

?>