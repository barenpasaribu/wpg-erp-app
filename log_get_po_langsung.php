<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';

if (isset($_POST['kdorg'])) {
	$kodeorg = trim($_POST['kdorg']);

	if ($_POST['kdorg'] == '') {
		echo 'warning:Kode Organisasi Inconsistent';
		exit();
	}
	else {
		$tgl = date('Ymd');
		$bln = substr($tgl, 4, 2);
		$thn = substr($tgl, 0, 4);
		$nopl = '/' . date('Y') . '/PL/' . $kodeorg;
		$ql = 'select `nopl` from ' . $dbname . '.`log_pol_ht` where nopl like \'%' . $nopl . '%\' order by `nopl` desc limit 0,1';

		#exit(mysql_error());
		($qr = mysql_query($ql)) || true;
		$rp = mysql_fetch_object($qr);
		$awal = substr($rp->nopl, 0, 3);
		$awal = intval($awal);
		$cekbln = substr($rp->nopl, 4, 2);
		$cekthn = substr($rp->nopl, 7, 4);

		if ($thn != $cekthn) {
			$awal = 1;
		}
		else {
			++$awal;
		}

		$counter = addZero($awal, 3);
		$nopl = $counter . '/' . $bln . '/' . $thn . '/PL/' . $kodeorg;
		echo $nopl;
	}
}

?>
