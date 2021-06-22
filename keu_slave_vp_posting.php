<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$qVp = 'select sum(jumlah) as total from ' . $dbname . '.keu_vpdt where novp=\'' . $param['novp'] . '\' group by novp';
$resVp = fetchData($qVp);

if (empty($resVp)) {
	exit('Warning: Detail Empty');
}

if ($resVp[0]['total'] != 0) {
	exit('Warning: Detail is not balance' . "\n" . 'Balance: ' . $resVp[0]['total']);
}

$queryH = selectQuery($dbname, 'keu_vpht', '*', 'novp=\'' . $param['novp'] . '\'');
$dataH = fetchData($queryH);
$queryD = selectQuery($dbname, 'keu_vpdt', '*', 'novp=\'' . $param['novp'] . '\'');
$dataD = fetchData($queryD);
$error0 = '';

if ($dataH[0]['posting'] == 1) {
	$error0 .= $_SESSION['lang']['errisposted'];
}

if ($error0 != '') {
	echo 'Warning :' . "\n" . $error0;
	exit();
}
$tgl = str_replace('-', '', $dataH[0]['tanggal']);
if (($tgl < $_SESSION['org']['period']['start']) || ($_SESSION['org']['period']['end'] < $tgl)) {
	exit('Warning: Date beyond active period');
}

$error1 = '';

if (count($dataH) == 0) {
	$error1 .= $_SESSION['lang']['errheadernotexist'] . "\n";
}

if (count($dataD) == 0) {
	$error1 .= $_SESSION['lang']['errdetailnotexist'] . "\n";
}

if ($error1 != '') {
	echo 'Data Error :' . "\n" . $error1;
	exit();
}

$dataRes['header'] = array();
$dataRes['detail'] = array();
$dataResoto['header'] = array();
$dataResoto['detail'] = array();
$kodeJurnal = 'VP';

$qPO = "SELECT kodesupplier FROM keu_vpht a left join keu_tagihanht b ON left(a.noinv1,14)=b.noinvoice WHERE novp='".$param['novp']."'";
$resPO = mysql_query($qPO);
$hasPO= mysql_fetch_assoc($resPO);
$supp = $hasPO['kodesupplier'];

$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$nojurnal = str_replace('-', '', $dataH[0]['tanggal']) . '/' . $dataH[0]['kodeorg'] . '/' . $kodeJurnal . '/' . $konter;
$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['novp'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
$noUrut = 1;
$totalDebet = 0;
$totalKredit = 0;

foreach ($dataD as $row) {
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $row['noakun'], 'keterangan' => $dataH[0]['penjelasan'], 'jumlah' => $row['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $dataH[0]['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $supp, 'noreferensi' => $dataH[0]['novp'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $row['novp'], 'kodeblok' => '', 'revisi' => '0');

	if ($row['jumlah'] < 0) {
		$totalKredit += $row['jumlah'];
	}
	else {
		$totalDebet += $row['jumlah'];
	}

	++$noUrut;
}

$dataRes['header']['totaldebet'] = $totalDebet;
$dataRes['header']['totalkredit'] = $totalKredit * -1;
$errorDB = '';
$queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
saveLog($queryH);
if (!mysql_query($queryH)) {
	$errorDB .= 'Header :' . mysql_error() . "\n";
	exit('error:' . var_dump($errorDB));
}


if ($errorDB == '') {
	foreach ($dataRes['detail'] as $key => $dataDet) {
		$queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
		saveLog($queryD);
		if (!mysql_query($queryD)) {
			$errorDB .= 'Detail ' . $key . ' :' . mysql_error() . "\n";
			exit('error:' . var_dump($errorDB));
		}
	}

	$queryJ = selectQuery($dbname, 'keu_vpht', 'posting', 'novp=\'' . $param['novp'] . '\'');
	$isJ = fetchData($queryJ);

	if ($isJ[0]['posting'] == 1) {
		$errorDB .= 'Data changed by other user';
		exit('error:' . var_dump($errorDB));
	}
	else {
		$queryToJ = updateQuery($dbname, 'keu_vpht', array('posting' => 1), 'novp=\'' . $dataH[0]['novp'] . '\'');

		if (!mysql_query($queryToJ)) {
			$errorDB .= 'Posting Flag Error :' . mysql_error() . "\n";
			exit('error:' . var_dump($errorDB));
		}
	}
}

if ($errorDB != '') {
	$where = 'nojurnal=\'' . $nojurnal . '\'';
	$queryRB = 'delete from `' . $dbname . '`.`keu_jurnalht` where ' . $where;
	$queryRB2 = updateQuery($dbname, 'keu_vpht', array('posting' => 0), 'novp=\'' . $dataH[0]['novp'] . '\'');

	if (!mysql_query($queryRB)) {
		$errorDB .= 'Rollback 1 Error :' . mysql_error() . "\n";
		exit('error:' . var_dump($errorDB));
	}

	if (!mysql_query($queryRB2)) {
		$errorDB .= 'Rollback 2 Error :' . mysql_error() . "\n";
		exit('error:' . var_dump($errorDB));
	}
}
else {
	$queryJ = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $tmpKonter[0]['nokounter'] + 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');
	$errCounter = '';

	if (!mysql_query($queryJ)) {
		$errCounter .= 'Update Counter Parameter Jurnal Error :' . mysql_error() . "\n";
		exit('error:' . var_dump($errCounter));
	}
	$errorJRB='';
	if ($errCounter != '') {
		$queryJRB = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $tmpKonter[0]['nokounter']), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $dataD[0]['kode'] . '\'');
		$errCounter = '';

		if (!mysql_query($queryJRB)) {
			$errorJRB .= 'Rollback Parameter Jurnal Error :' . mysql_error() . "\n";
			exit('error:' . var_dump($errorJRB));
		}

		echo 'DB Error :' . "\n" . $errorJRB;
		exit();
	}
}

?>
