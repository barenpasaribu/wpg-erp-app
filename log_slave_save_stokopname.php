<?
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');

$method = isset($_POST['method']) ? $_POST['method'] : null;
#showerror();
function GetNamaOrg3($kodeorg){
	global $dbname;
	
	#if($kodeorg == ''){
		$WKdorg = "kodeorganisasi ='".$kodeorg."'";
	#} else {
	#	$String2 = "SELECT induk FROM organisasi WHERE kodeorganisasi='".$kodeorg."'";
	#	$Result2 = fetchData($String2);
	#	$WKdorg = "kodeorganisasi ='".$Result2[0]['induk']."'";
	#}
	$String = "SELECT namaorganisasi FROM organisasi 
	WHERE ".$WKdorg."";
	
	$Result = fetchData($String);
	return $Result[0]['namaorganisasi'];
}
#echo json_decode($_POST['details'][0]['seqno']);
#pre(json_decode($_POST['details']));exit();
switch($method){
	case 'insert':
	#pre($_POST['details']); exit();
	$tanggal	  = date('Y-m-d', strtotime($_POST['tanggal']));
	$nostokopname = $_POST['nostokopname'];
	$note		  = $_POST['note'];
	$kdunit		  = $_POST['kdunit'];	
	$kdgudang	  = $_POST['kdgudang'];
	$periode	  = date('Y-m-d', strtotime($_POST['periode']));
	$periode2	  = date('Y-m', strtotime($_POST['periode']));
	$reffno = explode('-',$periode);
	$RNT = $reffno[0];
	$RNB = $reffno[1];
	$usrapv	  	  = '';
	$usrapvdt	  = '';
	$usrdt		  = date("Y-m-d H:i:s");
	$CekReffNo = "SELECT COUNT(*) FROM ".$dbname.".log_5stokopnameht WHERE reffno LIKE '%".$RNB."/".$RNT."%'";
	$RCRN = mysql_query($CekReffNo);
	$ACRN = mysql_fetch_row($RCRN);
	$NCRN = $ACRN[0]+1;

	if($NCRN < 10) {
		$NCRN = '00'.$NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	} elseif( $NCRN > 10 && $NCRN < 100){
		$NCRN = '0'.$NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	} else {
		$NCRN = $NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	}
	
	$sStokOpnameHt = "SELECT * FROM ".$dbname.".log_5stokopnameht WHERE kdunit = '".$kdunit."' and kdgudang = '".$kdgudang."' and periode like '".$periode2."%' and status = 0 order by id desc;";
	#echo $sStokOpnameHt;
	$rStokOpnameHt = fetchData($sStokOpnameHt);
	if(isset($rStokOpnameHt[0]) || !empty($rStokOpnameHt[0])){
		echo 'ERROR: No Stockopname : '.$rStokOpnameHt[0]['nostokopname'].' Belum di posting.';
		exit();
	}
	
	$SPeriodeAkun = "SELECT * FROM ".$dbname.".setup_periodeakuntansi WHERE kodeorg = '".$kdgudang."' and tutupbuku = 0 order by periode desc;";
	$RPeriodeAkun = fetchData($SPeriodeAkun);
	
	/*$PeriodeAwal = $RPeriodeAkun[0]['tanggalmulai'];
	$PeriodeAkahir = $RPeriodeAkun[0]['tanggalsampai'];
	
	#echo $PeriodeAwal.' '.$PeriodeAkahir.' '.$tanggal;exit();
	if(strtotime($PeriodeAwal)>strtotime($tanggal) || strtotime($PeriodeAkahir)<strtotime($tanggal)){
		echo 'ERROR: Tanggal dengan periode aktif gudang/unit tidak sama.';
		exit();
	}*/
	#echo $PeriodeAwal.' '.$PeriodeAkahir.' '.$tanggal;exit();
	
	$QInsert = "INSERT INTO ".$dbname.".log_5stokopnameht 
		(`reffno`, `tanggal`, `note`, `kdunit`, `kdgudang`, `periode`, `usrapv`, `usrapvdt`, `usrcrt`, `usrdt`, `nostokopname`) 
		VALUES 
		('".$NCRN."', '".$tanggal."', '".$note."', '".$kdunit."', '".$kdgudang."', '".$periode."', '', '', '".$_SESSION['empl']['name']."', '".$usrdt."', '".$nostokopname."')";			  
	if(!mysql_query($QInsert)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	} else {
		foreach(json_decode($_POST['details']) as $AR) {
			mysql_query("INSERT INTO ".$dbname.".log_5stokopnamedt 
			(`reffno`, `seqno`, `kdbarang`, `kdsatuan`, `qtysaldo`, `qtyso`, `qtybalance`, `nmbarang`) 
			VALUES 
			('".$NCRN."', '".$AR->seqno."', '".$AR->kdbarang."', '".$AR->kdsatuan."', '".$AR->qtysaldo."', '".$AR->qtyso."', '".$AR->qtybalance."', '".$AR->nmbarang."')");
		}
	}
	break;
	case 'update':
	#pre($_POST['details']); exit();
	$idso 		  = $_POST['idso'];
	$tanggal	  = date('Y-m-d', strtotime($_POST['tanggal']));
	$reffno 	  = $_POST['reffno'];
	#pre($reffno); exit();
	$nostokopname = $_POST['nostokopname'];
	$note		  = $_POST['note'];
	$kdunit		  = $_POST['kdunit'];	
	$kdgudang	  = $_POST['kdgudang'];
	$periode	  = date('Y-m-d', strtotime($_POST['periode']));
	$usrapv	  	  = '';
	$usrapvdt	  = '';
	$usrdt		  = date("Y-m-d H:i:s");
	
	mysql_query("DELETE FROM ".$dbname.".log_5stokopnameht WHERE (`reffno`='".$reffno."')");
	$QInsert = "INSERT INTO ".$dbname.".log_5stokopnameht 
		(`reffno`, `tanggal`, `note`, `kdunit`, `kdgudang`, `periode`, `usrapv`, `usrapvdt`, `usrcrt`, `usrdt`, `nostokopname`) 
		VALUES 
		('".$reffno."', '".$tanggal."', '".$note."', '".$kdunit."', '".$kdgudang."', '".$periode."', '', '', '".$_SESSION['empl']['name']."', '".$usrdt."', '".$nostokopname."')";			  
	if(!mysql_query($QInsert)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	} else {
		mysql_query("DELETE FROM ".$dbname.".log_5stokopnamedt WHERE (`reffno`='".$reffno."')");
		foreach(json_decode($_POST['details']) as $AR) {
			mysql_query("INSERT INTO ".$dbname.".log_5stokopnamedt 
			(`reffno`, `seqno`, `kdbarang`, `kdsatuan`, `qtysaldo`, `qtyso`, `qtybalance`, `nmbarang`) 
			VALUES 
			('".$reffno."', '".$AR->seqno."', '".$AR->kdbarang."', '".$AR->kdsatuan."', '".$AR->qtysaldo."', '".$AR->qtyso."', '".$AR->qtybalance."', '".$AR->nmbarang."')");
		}
	}
	break;
	case 'GetDataSO':
	#showerror();
		$IdSO = isset($_POST['id']) ? $_POST['id'] : '';
		$QSo = mysql_query("SELECT * FROM ".$dbname.". log_5stokopnameht WHERE id='".$IdSO."'") or die(mysql_error());
		while($row = mysql_fetch_assoc($QSo)){
			 $RSo['ht'][0]['reffno'] = $row['reffno'];
			 $RSo['ht'][0]['tanggal'] = $row['tanggal'];
			 $RSo['ht'][0]['note'] = $row['note'];
			 $RSo['ht'][0]['kdunit'] = GetNamaOrg3($row['kdunit']);
			 $RSo['ht'][0]['kdgudang'] = GetNamaOrg3($row['kdgudang']);
			 $RSo['ht'][0]['periode'] = $row['periode'];
			 $RSo['ht'][0]['usrapv'] = $row['usrapv'];
			 $RSo['ht'][0]['usrapvdt'] = $row['usrapvdt'];
			 $RSo['ht'][0]['usrcrt'] = $row['usrcrt'];
			 $RSo['ht'][0]['usrdt'] = $row['usrdt'];
			 $RSo['ht'][0]['nostokopname'] = $row['nostokopname'];
			 $RSo['ht'][0]['status'] = $row['status'];
		}
		
		$QSodt = mysql_query("SELECT * FROM ".$dbname.". log_5stokopnamedt WHERE reffno='".$RSo['ht']['0']['reffno']."'") or die(mysql_error());
		while($row = mysql_fetch_assoc($QSodt)){
			 $RSo['dt'][] = $row;
		}
		echo json_encode($RSo);
		#pre($RSo);exit();
	break;
	default:
	  $strx="select 1=1";
	break;	
}
?>
