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
	$CPOPrice=$_POST['CPOPrice'];
	$pkPrice=$_POST['pkPrice'];
	$cangkangPrice=$_POST['cangkangPrice'];
	$biayaCPOdt=$_POST['biayaCPOdt'];
	$biayaPKdt=$_POST['biayaPKdt'];
	$biayaCKdt=$_POST['biayaCKdt'];
	$transCPOdt=$_POST['transCPOdt'];
	$transPKdt=$_POST['transPKdt'];
	$transCKdt=$_POST['transCKdt'];
	$oerCPOdt=$_POST['oerCPOdt'];
	$oerPKdt=$_POST['oerPKdt'];
	$oerCKdt=$_POST['oerCKdt'];
	$actCPO=$_POST['actCPO'];
	$actPK=$_POST['actPK'];
	$actCK=$_POST['actCK'];
	$dppCPO=$_POST['dppCPO'];
	$dppPK=$_POST['dppPK'];
	$dppCK=$_POST['dppCK'];
	$biayaCPO=$_POST['biayaCPO'];
	$biayaPK=$_POST['biayaPK'];
	$biayaCK=$_POST['biayaCK'];		
	$biayakandir=$_POST['biayakandir'];	
	$transCPO=$_POST['transCPO'];
	$transPK=$_POST['transPK'];
	$transCK=$_POST['transCK'];
	$decreas=$_POST['decreas'];
	$totalBiaya=$_POST['totalBiaya'];
	$totalResult=$_POST['totalResult'];
	$totalPurch=$_POST['totalPurch'];
	$labaRugi=$_POST['labaRugi'];
	$tonase=$_POST['tonase'];
	$pricekg=$_POST['pricekg'];
	$ppncpo=$_POST['ppncpo'];
	$ppnck=$_POST['ppnck'];
	$marginper=$_POST['margin'];
	$ppnpk=$_POST['ppnpk'];
	$genKode= $tgl.$org;	
	$strSvht = "UPDATE  pmn_rendemenht SET totalbiaya=". $totalBiaya .", totalhasil='". $totalResult ."', totalpurchase='". $totalPurch ."', labarugi='". $labaRugi ."', tonase='". $tonase ."', pricekg='". $pricekg ."', decreas='".$decreas."', kandir='".$biayakandir."',margin= '".$marginper."' WHERE koderendemen='".$genKode."'";
	//echo "<script> alert('". $strSvht . "');</script>";
	if(mysql_query($strSvht)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaCPOdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='001';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$CPOPrice."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='000';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$pkPrice."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='000';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$cangkangPrice."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='000';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaPKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='001';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaCKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='001';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transCPOdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='002';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transPKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='002';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transCKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='002';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$oerCPOdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='003';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$oerPKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='003';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$oerCKdt."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='003';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
		$strSvdt = "UPDATE pmn_rendemendt SET amount='".$ppncpo."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='004';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$ppnpk."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='004';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$ppnck."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='004';";
		if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaCPO."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='201';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaPK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='201';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$biayaCK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='201';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transCPO."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='202';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transPK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='202';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$transCK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='202';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$actCPO."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='301';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$actPK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='301';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$actCK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='301';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
		$strSvdt = "UPDATE pmn_rendemendt SET amount='".$dppCPO."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000001' AND kodelist='302';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$dppPK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000002' AND kodelist='302';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	$strSvdt = "UPDATE pmn_rendemendt SET amount='".$dppCK."'
		WHERE koderendemen='".$genKode."' AND kodebarang='40000004' AND kodelist='302';";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
	break;

case 'insert':
	$CPOPrice=$_POST['CPOPrice'];
	$pkPrice=$_POST['pkPrice'];
	$cangkangPrice=$_POST['cangkangPrice'];
	$ppncpo=$_POST['ppncpo'];
	$ppnck=$_POST['ppnck'];
	$ppnpk=$_POST['ppnpk'];
	$biayaCPOdt=$_POST['biayaCPOdt'];
	$biayaPKdt=$_POST['biayaPKdt'];
	$biayaCKdt=$_POST['biayaCKdt'];
	$transCPOdt=$_POST['transCPOdt'];
	$transPKdt=$_POST['transPKdt'];
	$transCKdt=$_POST['transCKdt'];
	$oerCPOdt=$_POST['oerCPOdt'];
	$oerPKdt=$_POST['oerPKdt'];
	$oerCKdt=$_POST['oerCKdt'];
	$actCPO=$_POST['actCPO'];
	$actPK=$_POST['actPK'];
	$actCK=$_POST['actCK'];
	$dppCPO=$_POST['dppCPO'];
	$dppPK=$_POST['dppPK'];
	$dppCK=$_POST['dppCK'];
	$biayaCPO=$_POST['biayaCPO'];
	$biayaPK=$_POST['biayaPK'];
	$biayaCK=$_POST['biayaCK'];		
	$biayakandir=$_POST['biayakandir'];	
	$transCPO=$_POST['transCPO'];
	$transPK=$_POST['transPK'];
	$transCK=$_POST['transCK'];
	$decreas=$_POST['decreas'];
	$totalBiaya=$_POST['totalBiaya'];
	$totalResult=$_POST['totalResult'];
	$totalPurch=$_POST['totalPurch'];
	$labaRugi=$_POST['labaRugi'];
	$tonase=$_POST['tonase'];
	$marginper=$_POST['margin'];
	$pricekg=$_POST['pricekg'];
	$genKode= $tgl.$org;
	$strSvht="INSERT INTO pmn_rendemenht
		(koderendemen, kodeorg, tglrendemen, totalbiaya, totalhasil, totalpurchase, labarugi, tonase, pricekg, decreas, kandir, margin)	VALUES('".$genKode."','".$org."','".$tgl."','".$totalBiaya."','".$totalResult."','".$totalPurch."','".$labaRugi."','".$tonase."','".$pricekg."','".$decreas."','".$biayakandir."', '".$marginper."')";
	if(mysql_query($strSvht)){}else{echo " Gagal,".addslashes(mysql_error($conn));}	
 	$strSvdt = "INSERT INTO pmn_rendemendt (koderendemen, kodebarang, kodelist, amount)
				VALUES('".$genKode."','40000001','000','".$CPOPrice."'),
						('".$genKode."','40000002','000','".$pkPrice."'),
						('".$genKode."','40000004','000','".$cangkangPrice."'),
						('".$genKode."','40000001','001','".$biayaCPOdt."'),
						('".$genKode."','40000002','001','".$biayaPKdt."'),
						('".$genKode."','40000004','001','".$biayaCKdt."'),
						('".$genKode."','40000001','002','".$transCPOdt."'),
						('".$genKode."','40000002','002','".$transPKdt."'),
						('".$genKode."','40000004','002','".$transCKdt."'),
						('".$genKode."','40000001','003','".$oerCPOdt."'),
						('".$genKode."','40000002','003','".$oerPKdt."'),
						('".$genKode."','40000004','003','".$oerCKdt."'),
						('".$genKode."','40000001','004','".$ppncpo."'),
						('".$genKode."','40000002','004','".$ppnpk."'),
						('".$genKode."','40000004','004','".$ppnck."'),
						('".$genKode."','40000001','301','".$actCPO."'),
						('".$genKode."','40000002','301','".$actPK."'),
						('".$genKode."','40000004','301','".$actCK."'),
						('".$genKode."','40000001','302','".$dppCPO."'),
						('".$genKode."','40000002','302','".$dppPK."'),
						('".$genKode."','40000004','302','".$dppCK."'),
						('".$genKode."','40000001','201','".$biayaCPO."'),
						('".$genKode."','40000002','201','".$biayaPK."'),
						('".$genKode."','40000004','201','".$biayaCK."'),
						('".$genKode."','40000001','202','".$transCPO."'),
						('".$genKode."','40000002','202','".$transPK."'),
						('".$genKode."','40000004','202','".$transCK."')";
	if(mysql_query($strSvdt)){}else{echo " Gagal,".addslashes(mysql_error($conn));}
?>
<?php
	break;

default:
   break;
}
echo '';   
?>
<input type='image' src=images/excel.jpg onload="cleardata(2)" />


