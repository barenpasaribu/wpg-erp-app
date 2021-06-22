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
		$nopp = '/'.date('m').'/' . date('Y') . '/PP/' . $kodeorg;
		$ql = 'select `nopp` from ' . $dbname . '.`log_prapoht` where nopp like \'%' . $nopp . '%\' order by `nopp` desc limit 0,1';

		#exit(mysql_error());
		($qr = mysql_query($ql)) || true;
		$rp = mysql_fetch_object($qr);
		$awal = substr($rp->nopp, 0, 3);
		$awal = intval($awal);
		$cekbln = substr($rp->nopp, 4, 2);
		$cekthn = substr($rp->nopp, 7, 4);

		//if ($thn != $cekthn) {
		if ($bln != $cekbln) {
			$awal = 1;
		}
		else {
			++$awal;
		}

		$counter = addZero($awal, 3);
		$nopp = $counter . '/' . $bln . '/' . $thn . '/PP/' . $kodeorg;
		echo $nopp;
	}
}

?>
