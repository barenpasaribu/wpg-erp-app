<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$gudang = $_POST['gudang'];
	$user = $_SESSION['standard']['userid'];
	$awal = $_POST['awal'];
	$akhir = $_POST['akhir'];
	$period = $_SESSION['gudang'][$gudang]['tahun'] . '-' . $_SESSION['gudang'][$gudang]['bulan'];
	$tg = mktime(0, 0, 0, $_SESSION['gudang'][$gudang]['bulan'] + 1, 15, $_SESSION['gudang'][$gudang]['tahun']);
	$nextPeriod = date('Y-m', $tg);
	$tg = mktime(0, 0, 0, substr($akhir, 4, 2), intval(substr($akhir, 6, 2) + 1), $_SESSION['gudang'][$gudang]['tahun']);
	$nextAwal = date('Ymd', $tg);
	$tg = mktime(0, 0, 0, intval(substr($akhir, 4, 2)) + 1, date('t', $tg), $_SESSION['gudang'][$gudang]['tahun']);
	$nextAkhir = date('Ymd', $tg);
	$str = 'select tutupbuku from ' . $dbname . '.setup_periodeakuntansi where periode=\'' . $period . '\'' . "\r\n" . '      and kodeorg=\'' . $gudang . '\'';
	$res = mysql_query($str);
	$periode = 'benar';

	if (0 < mysql_num_rows($res)) {
		while ($bar = mysql_fetch_object($res)) {
			if ($bar->tutupbuku == 0) {
				$periode = 'benar';
			}
			else {
				$periode = 'salah';
			}
		}
	}
	else {
		$periode = 'salah';
	}

	$str = 'select count(tanggal) as tgl from ' . $dbname . '.log_transaksiht' . "\r\n" . '      where kodegudang=\'' . $gudang . '\' and tanggal>=' . $awal . ' and tanggal<=' . $akhir . "\r\n\t" . '  and post=0';
	$res = mysql_query($str);
	$jlhNotPost = 0;

	while ($bar = mysql_fetch_object($res)) {
		$jlhNotPost = $bar->tgl;
	}

	if (0 < $jlhNotPost) {
		echo ' Error: ' . $_SESSION['lang']['belumposting'] . ' > 0';
	}
	else if ($periode == 'salah') {
		echo ' Error: Transaction period not defined';
	}
	else {
		$str = 'update ' . $dbname . '.setup_periodeakuntansi set tutupbuku=1' . "\r\n" . '          where kodeorg=\'' . $gudang . '\' and periode=\'' . $period . '\'';

		if (mysql_query($str)) {
			$str = 'INSERT INTO `' . $dbname . '`.`setup_periodeakuntansi`' . "\r\n\t\t" . '(`kodeorg`,' . "\r\n\t\t" . '`periode`,' . "\r\n\t\t" . '`tanggalmulai`,' . "\r\n\t\t" . '`tanggalsampai`,' . "\r\n\t\t" . '`tutupbuku`)' . "\r\n\t\t" . 'VALUES' . "\r\n\t\t" . '(\'' . $gudang . '\',' . "\r\n\t\t" . ' \'' . $nextPeriod . '\',' . "\r\n\t\t" . ' ' . $nextAwal . ',' . "\r\n\t\t" . ' ' . $nextAkhir . ',' . "\r\n\t\t" . ' 0' . "\r\n\t\t" . ' )';

			if (mysql_query($str)) {
			}
			else {
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
