<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tanggal	  =date('Y-m-d', strtotime($_POST['tanggal']));
$nostokopname =$_POST['nostokopname'];
$note		  =$_POST['note'];
$kdunit		  =$_POST['kdunit'];	
$kdgudang	  =$_POST['kdgudang'];
$periode	  =date('Y-m-d', strtotime($_POST['periode']));
$reffno = explode('-',$periode);
$RNT = $reffno[0];
$RNB = $reffno[1];
$usrapv	  	  ='';
$usrapvdt	  ='';
$usrdt		  =date("Y-m-d H:i:s");
$method       =$_POST['method'];
#echo json_decode($_POST['details'][0]['seqno']);
#pre(json_decode($_POST['details']));exit();
switch($method){
	case 'insert':
	#showerror();
	#pre($_SESSION);exit();
	$CekReffNo = "SELECT COUNT(*) FROM ".$dbname.".log_5stokopnameht WHERE reffno LIKE '%".$RNB."/".$RNT."%'";
	$RCRN = mysql_query($CekReffNo);
	$ACRN = mysql_fetch_row($RCRN);
	$NCRN = $ACRN[0]+1;
	#echo $CekReffNo; exit();
	if($NCRN < 10) {
		$NCRN = '00'.$NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	} elseif( $NCRN > 10 && $NCRN < 100){
		$NCRN = '0'.$NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	} else {
		$NCRN = $NCRN.'/'.$RNB.'/'.$RNT.'/SO/'.$kdgudang;
	}

	$QInsert = "INSERT INTO ".$dbname.".log_5stokopnameht 
		(`reffno`, `tanggal`, `note`, `kdunit`, `kdgudang`, `periode`, `usrapv`, `usrapvdt`, `usrcrt`, `usrdt`, `nostokopname`) 
		VALUES 
		('".$NCRN."', '".$tanggal."', '".$note."', '".$kdunit."', '".$kdgudang."', '".$periode."', '', '', '".$_SESSION['standard']['username']."', '".$usrdt."', '".$nostokopname."')";			  
	if(!mysql_query($QInsert)) {
		echo "<script>alert('Failed');</script>";
		die(mysql_error());
	} else {
		foreach(json_decode($_POST['details']) as $AR) {
			mysql_query("INSERT INTO ".$dbname.".log_5stokopnamedt 
			(`reffno`, `seqno`, `kdbarang`, `kdsatuan`, `qtysaldo`, `qtyso`, `qtybalance`) 
			VALUES 
			('".$NCRN."', '".$AR->seqno."', '".$AR->kdbarang."', '".$AR->kdsatuan."', '".$AR->qtysaldo."', '".$AR->qtyso."', '".$AR->qtybalance."')");
		}
	}
	break;
	default:
	  $strx="select 1=1";
	break;	
}
?>
