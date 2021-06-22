<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
//include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$keyCode=$_POST['keycode'];
$nmr=$_POST['nomor'];
$jrkDr=$_POST['jrkDari'];
$jrkSmp=$_POST['jrkSmp'];
$jmlBasis=$_POST['jmlhBasis'];
$tpeKerja=$_POST['tipeKerja'];
$userOnline=$_SESSION['standard']['userid'];
$posisi=$_POST['posisi'];
$premiLbh=$_POST['premiLbh'];

switch($proses)
{
	case'loadData':
	$limit=10;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_5ratetransport where jumlahtrip='0'  order by keycode desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
		$arrPos=array("Sopir","Kondektur");
	$sql="select * from ".$dbname.".kebun_5ratetransport where jumlahtrip='0' order by keycode desc limit ".$offset.",".$limit."";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$res['keycode']."</td>
		<td align=center>".$res['nomor']."</td>
		<td align=center>".$res['jarakdari']."</td>
		<td align=center>".$res['jaraksampai']."</td>
		<td align=center>".$res['tipeangkutan']."</td>
		<td align=center>".$res['rate']."</td>
		<td align=center>".$arrPos[$res['jobposition']]."</td>
		<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
		onclick=\"fillField('". $res['keycode']."','". $res['nomor']."','". $res['jarakdari']."','".$res['jaraksampai']."','". $res['tipeangkutan']."','". $res['jobposition']."','". $res['rate']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delHead('". $res['keycode']."','". $res['nomor']."');\" >	
		</td>"	;
	}
	echo" </tr><tr class=rowheader><td colspan=11 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
	break;
	case'insert_header':
	if(($keyCode=='')||($nmr=='')||($jrkDr=='')||($jrkSmp=='')||($tpeKerja==''))
	{
		echo"warning:Please Complete The Form";
		exit();
	}
	$sCek="select keycode from ".$dbname.".kebun_5ratetransport where keycode='".$keyCode."' and nomor='".$nmr."'";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_row($qCek);
	if($rCek<1)
	{
		//echo "warning:".$sCek;
		$thisDay=date("Y-m-d");
		$sIns="insert into ".$dbname.".kebun_5ratetransport 
		(keycode, nomor, jarakdari, jaraksampai, tipeangkutan, rate, createdby,createddate,jobposition) values 
		('".$keyCode."','".$nmr."','".$jrkDr."','".$jrkSmp."','".$tpeKerja."','".$premiLbh."','".$userOnline."','".$thisDay."','".$posisi."')";
		//echo "warning:".$sIns;
		if(mysql_query($sIns))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{
		echo"warning:This Keycode and Number Already Input, Please Try Other Number";
		exit();
	}
	break;
	
	case'updateHeader':
	//echo"warning:masuk";
	if(($keyCode=='')||($nmr=='')||($jrkDr=='')||($jrkSmp=='')||($tpeKerja==''))
	{
		echo"warning:Please Complete The Form";
		exit();
	}
	$sUpd="update ".$dbname.".kebun_5ratetransport set jarakdari='".$jrkDr."', jaraksampai='".$jrkSmp."', tipeangkutan='".$tpeKerja."', rate='".$premiLbh."', modifyby='".$userOnline."', jobposition='".$posisi."' where keycode='".$keyCode."' and nomor='".$nmr."'";
	if(mysql_query($sUpd))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);
	break;
	
	case'delData':
	
	$sDel="delete from ".$dbname.".kebun_5ratetransport where keycode='".$keyCode."' and nomor='".$nmr."'";
	//echo"warning:".$sDel;
	if(mysql_query($sDel))
	{
		echo"";
		/*$sDel2="delete from ".$dbname.".kebun_5ratetransport2 where keycode='".$keyCode."'";
		if(mysql_query($sDel2))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);*/
	}
	else
	echo "DB Error : ".mysql_error($conn);
	break;
	case'cekNmr':
	$sCek="select nomor from ".$dbname.".kebun_5ratetransport where keycode='".$keyCode."' order by nomor desc limit 0,1"; //echo "warning".$sCek;
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_assoc($qCek);
	$nilaiAwal=intval($rCek['nomor']);
	if($nilaiAwal==0)
	{
		$nilaiAwal+=1;
	}
	else
	{
		$nilaiAwal+=1;
	}
	echo $nilaiAwal;
	break;
	default:
	break;
}

?>