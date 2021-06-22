<?php 
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/eagrolib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
require_once('lib/eksilib.php');
$kodeorg 	= $_POST['kodeorg'];
$app	 	= $_POST['app'];
$method	 	= $_POST['method'];
$karyawanid = $_POST['karyawanid'];
switch($method){
case 'update':	
	$str="update ".$dbname.".setup_approval_hrd set kodeunit='".$kodeorg."' where kode='".$kode."'";
	if(!mysql_query($str)){
		echo $_SESSION['lang']['alertfail']." ".addslashes(mysql_error($conn));	
	}
	break;
case 'insert':
	$str="insert into ".$dbname.".setup_approval_hrd(kodeunit,applikasi,karyawanid)values('".$kodeorg."','".$app."',".$karyawanid.")";
	if(!mysql_query($str)){
		echo $_SESSION['lang']['alertfail']." ".addslashes(mysql_error($conn));
	}	
	break;
case 'delete':
	$str="delete from ".$dbname.".setup_approval_hrd where kodeunit='".$kodeorg."' and karyawanid=".$karyawanid." and applikasi='".$app."'";
	if(!mysql_query($str)){
		echo $_SESSION['lang']['alertfail']." ".addslashes(mysql_error($conn));
	}
	break;
case 'refresh_data':
	$limit=10;
	$page=0;
	if(isset($_POST['page'])){
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;		

	if($_POST['filter']!='' && $_POST['keyword']!='') {
		$filter = "AND ".$_POST['filter']." LIKE '%".$_POST['keyword']."%'";
	}
	else {
		$filter = "";
	}
	//tambah filter hanya sesuai pt -> ho dan site	==Jo 19-07-2017==
	$str = "select a.*,b.namakaryawan from ".$dbname.".setup_approval_hrd a inner join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.kodeunit like '%".$_SESSION['empl']['kodeorganisasi']."%' ".$filter." order by namakaryawan asc limit ".$offset.",".$limit." ";
	$sql = "select count(namakaryawan) as jmlhrow from ".$dbname.".setup_approval_hrd a inner join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.kodeunit like '%".$_SESSION['empl']['kodeorganisasi']."%' ".$filter." order by namakaryawan asc";	

	$res=mysql_query($str);
	$no=($page*$limit)+1; 
	$jlhbrs = 0;
	$query=mysql_query($sql) or die(mysql_error());
	while($jsl=mysql_fetch_object($query)){
	$jlhbrs= $jsl->jmlhrow;
	}	
	
	//rubah hard code jadi panggil tabel parameter ==Jo 24-01-2017==
	$slapp="select kode,nama from setup_5parameter where flag='apprvh' ";
	$resapp=$eksi->sSQL($slapp);

	foreach($resapp as $barapp){
		$app[$barapp['kode']]=$_SESSION['lang'][$barapp['nama']];
	}
	/*$app = array(
	'PP'  => 'Approval 1',
	'PP2' => 'Approval 2',
	'PO'  => "Purchaser",
	'PO2' => "Approval ".$_SESSION['lang']['lokal']."",
	'SG'  => "Approval PO ".$_SESSION['lang']['ho']."",
	'CA'  => $_SESSION['lang']['atasan']." ".$_SESSION['lang']['cuti'],
	'CH'  => $_SESSION['lang']['hrd']." ".$_SESSION['lang']['cuti'],
	'PDA'  => $_SESSION['lang']['atasan']." ".$_SESSION['lang']['perjalanandinas'],
	'PDH'  => $_SESSION['lang']['hrd']." ".$_SESSION['lang']['perjalanandinas'],
	'PLTA'  => $_SESSION['lang']['atasan']." ".$_SESSION['lang']['kursus'],
	'PLTH'  => $_SESSION['lang']['hrd']." ".$_SESSION['lang']['kursus'],
	);*/	
	
	while($bar1=mysql_fetch_object($res)){
		echo "
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$app[$bar1->applikasi]."</td>
		<td align=center>".$bar1->kodeunit."</td>
		<td>".$bar1->namakaryawan."</td>
		<td>
        <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"dellField('".$bar1->kodeunit."','".$bar1->applikasi."','".$bar1->karyawanid."');\">     
		</td>
		</tr>";
		$no++;
	}	 
echo "<tr class='footercolor'>
	<td colspan=5 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td>
	</tr>";		

break;	
default:
break;					
}
?>
