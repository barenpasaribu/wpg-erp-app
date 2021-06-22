<?php

require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zLib.php';

$ngapain = $_POST['ngapain'];

$noakun = $_POST['noakun'];
$tanggal = $_POST['tanggal'];

//$tanggal = tanggalsystem($_POST['tanggal']);



$data=$param;

            
//$codeOrg=$param['kodeorg'];
$codeOrg="PDSM";

$tgl = date('Ymd');

$bln = substr($tgl, 4, 2);

$thn = substr($tgl, 0, 4);

$dt = substr($tgl, 6, 2);

$notransaksi = $codeOrg.date('Y').date('m').date('d');

$ql = 'select `notrans_tbsolah` from '.$dbname.".`log_transaksi_mill` where notrans_tbsolah like '%".$notransaksi."%' order by `notrans_tbsolah` desc limit 0,1";


$qr = mysql_query($ql);

$rp = mysql_fetch_object($qr);

$awal = substr($rp->notrans_tbsolah, -4, 4);

$awal = (int) $awal;

$cekdt = substr($rp->notrans_tbsolah, -5, 2);
$cekbln = substr($rp->notrans_tbsolah, -7, 2);
$cekthn = substr($rp->notrans_tbsolah, -11, 4);

if ($cekbln=$bln && $cekthn= $thn) {
	$xx=$awal+1;
	$counter = addZero($xx, 4);		
	$notransaksi = $codeOrg.$thn.$bln.$dt.$counter;
	echo $notransaksi;

} else {

	++$awal;
	$counter = addZero($awal, 4);
	$notransaksi = $codeOrg.$thn.$bln.$dt.$counter;
	echo $notransaksi;
}


?>

