<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "<script language='javascript' src='js/pmn_tbsrendemen.js?v=".mt_rand()."'></script>";
echo "<script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>";      
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>";
$method = $_POST['method'];
$tgl = $_POST['tgl'];
$org = $_POST['org'];

switch($method){
case 'list':
break;
case 'update':
	$CPOPrice= $_POST['CPOPrice'];
	$CPOsoldPrice= $_POST['CPOsoldPrice'];
	$costCPO= $_POST['costCPO'];
	$realCPO= $_POST['realCPO'];
	$transCPO= $_POST['transCPO'];
	$oerCPO= $_POST['oerCPO'];
	$PKPrice= $_POST['PKPrice'];
	$PKsoldPrice= $_POST['PKsoldPrice'];
	$costPK= $_POST['costPK'];
	$realPK= $_POST['realPK'];
	$transPK= $_POST['transPK'];
	$oerPK= $_POST['oerPK'];
	$CkPrice= $_POST['CkPrice'];
	$CksoldPrice= $_POST['CksoldPrice'];
	$costCk= $_POST['costCk'];
	$realCk= $_POST['realCk'];
	$transCk= $_POST['transCk'];
	$oerCk= $_POST['oerCk'];
	$baris= $_POST['jmlhbaris'];
	$genKode= $tgl.$org;
	$baris=$baris;
	$persen = $_POST['brs'];
	$strSvdt = "DELETE FROM pmn_rendemendiff WHERE koderendemen='".$genKode."'";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}	
	for($x=1;$x<=$baris;$x++){
		//echo $x . 'baris='.$persen[$x];
		if($persen[$x] <> '' or $persen[$x] > 0){
		$strSvht="INSERT INTO pmn_rendemendiff (koderendemen, persen) VALUES ('".$genKode."', '".$persen[$x]."' )";
		//echo $strSvht;
		if(mysql_query($strSvht)){}else{echo " Gagal,".addslashes(mysql_error($conn));}	
		}
	}
	//echo "<script> alert('". $strSvht . "');</script>";
	$strSvdt = "DELETE FROM pmn_rendemendtall WHERE koderendemen='".$genKode."'";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
 	$strSvdt = "INSERT INTO pmn_rendemendtall (koderendemen, kodebarang, kodelist, amount)
				VALUES('".$genKode."','40000001','000','".$CPOPrice."'),
				('".$genKode."','40000001','302','".$CPOsoldPrice."'),
				('".$genKode."','40000001','001','".$costCPO."'),
				('".$genKode."','40000001','201','".$realCPO."'),
				('".$genKode."','40000001','002','".$transCPO."'),
				('".$genKode."','40000001','003','".$oerCPO."'),
				('".$genKode."','40000002','000','".$PKPrice."'),
				('".$genKode."','40000002','302','".$PKsoldPrice."'),
				('".$genKode."','40000002','001','".$costPK."'),
				('".$genKode."','40000002','201','".$realPK."'),
				('".$genKode."','40000002','002','".$transPK."'),
				('".$genKode."','40000002','003','".$oerPK."'),
				('".$genKode."','40000004','000','".$CkPrice."'),
				('".$genKode."','40000004','302','".$CksoldPrice."'),
				('".$genKode."','40000004','001','".$costCk."'),
				('".$genKode."','40000004','201','".$realCk."'),
				('".$genKode."','40000004','002','".$transCk."'),
				('".$genKode."','40000004','003','".$oerCk."')";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	break;

case 'insert':
	$CPOPrice= $_POST['CPOPrice'];
	$CPOsoldPrice= $_POST['CPOsoldPrice'];
	$costCPO= $_POST['costCPO'];
	$realCPO= $_POST['realCPO'];
	$transCPO= $_POST['transCPO'];
	$oerCPO= $_POST['oerCPO'];
	$PKPrice= $_POST['PKPrice'];
	$PKsoldPrice= $_POST['PKsoldPrice'];
	$costPK= $_POST['costPK'];
	$realPK= $_POST['realPK'];
	$transPK= $_POST['transPK'];
	$oerPK= $_POST['oerPK'];
	$CkPrice= $_POST['CkPrice'];
	$CksoldPrice= $_POST['CksoldPrice'];
	$costCk= $_POST['costCk'];
	$realCk= $_POST['realCk'];
	$transCk= $_POST['transCk'];
	$oerCk= $_POST['oerCk'];
	$baris= $_POST['jmlhbaris'];
	$genKode= $tgl.$org;
	$baris=$baris;
	$persen = $_POST['brs'];
	for($x=1;$x<=$baris;$x++){
		//echo $x . 'baris='.$persen[$x];
		if($persen[$x] <> '' or $persen[$x] > 0){
		$strSvht="INSERT INTO pmn_rendemendiff (koderendemen, persen) VALUES ('".$genKode."', '".$persen[$x]."' )";
		//echo $strSvht;
		if(mysql_query($strSvht)){}else{echo " Gagal,".addslashes(mysql_error($conn));}	
		}
	}
 	$strSvdt = "INSERT INTO pmn_rendemendtall (koderendemen, kodebarang, kodelist, amount)
				VALUES('".$genKode."','40000001','000','".$CPOPrice."'),
				('".$genKode."','40000001','302','".$CPOsoldPrice."'),
				('".$genKode."','40000001','001','".$costCPO."'),
				('".$genKode."','40000001','201','".$realCPO."'),
				('".$genKode."','40000001','002','".$transCPO."'),
				('".$genKode."','40000001','003','".$oerCPO."'),
				('".$genKode."','40000002','000','".$PKPrice."'),
				('".$genKode."','40000002','302','".$PKsoldPrice."'),
				('".$genKode."','40000002','001','".$costPK."'),
				('".$genKode."','40000002','201','".$realPK."'),
				('".$genKode."','40000002','002','".$transPK."'),
				('".$genKode."','40000002','003','".$oerPK."'),
				('".$genKode."','40000004','000','".$CkPrice."'),
				('".$genKode."','40000004','302','".$CksoldPrice."'),
				('".$genKode."','40000004','001','".$costCk."'),
				('".$genKode."','40000004','201','".$realCk."'),
				('".$genKode."','40000004','002','".$transCk."'),
				('".$genKode."','40000004','003','".$oerCk."')";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
?>
<?php
	break;

default:
   break;
}
echo '<input type="image" src=images/excel.jpg onload="cleardata2(2)" />';   
?>



