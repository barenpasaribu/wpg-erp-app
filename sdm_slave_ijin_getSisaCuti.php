<?php

	require_once 'master_validation.php';
	include_once 'config/connection.php';
	include_once 'lib/zLib.php';

	$periode = $_POST['periode'];
	$karyawanid = $_POST['karyawanid'];
	$tglAwal = tanggalsystem($_POST['tglAwal']);
	$tipe_ijin = $_POST['tipe_ijin'];

	if (empty($tglAwal)) {
		$tglAwal = date("Y-m-d");
	}else {
		$tglAwal = substr($tglAwal,0,4) . "-" . substr($tglAwal,4,2) . "-" . substr($tglAwal,6,2);
	}
	
	$strf = "	SELECT sisa FROM " . $dbname . ".sdm_cutiht 
				WHERE karyawanid=".$karyawanid." 
				AND periodecuti=".$periode." 
				AND (
				dari <= '".$tglAwal."'
				and
				sampai >= '".$tglAwal."'
				)";
	
	
	$res = mysql_query($strf);
	$sisa = 0;

	while ($barf = mysql_fetch_object($res)) {
		$sisa = $barf->sisa;
	}

	if ($sisa == '') {
		$sisa = 0;
	}

	$queryCekPengurang = "SELECT * FROM sdm_5absensi WHERE kodeabsen='".$tipe_ijin."'";
	$queryActCekPengurang = mysql_query($queryCekPengurang);
	$dataCekPengurang = mysql_fetch_object($queryActCekPengurang);

	$dataJson->sisaCuti = $sisa;
	$dataJson->pengurang = $dataCekPengurang->pengurang;
	$dataJson->jumlah_hk = $dataCekPengurang->nilaihk;

	echo json_encode($dataJson);

?>
