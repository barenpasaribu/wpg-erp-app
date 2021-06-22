<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_terima_barang.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['penerimaanbarang'] . ':</b> ';
	echo '</legend>';

	if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\' order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe=\'GUDANG\' order by namaorganisasi desc';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	$optKary = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sKary = 'select distinct karyawanid,namakaryawan from ' . $dbname . '.datakaryawan ' . "\r\n" . '        where lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' and bagian IN (\'PUR\',\'AGR\') and' . "\r\n" . '        (tanggalkeluar is NULL or tanggalkeluar > \'' . $_SESSION['org']['period']['start'] . '\') order by namakaryawan asc';

	exit(mysql_error($sKary));
	($qKary = mysql_query($sKary)) || true;

	while ($rKary = mysql_fetch_assoc($qKary)) {
		$optKary .= '<option value=\'' . $rKary['karyawanid'] . '\'>' . $rKary['namakaryawan'] . '</option>';
	}

	$frm .= 0;
	$frm .= 0;

	foreach ($_SESSION['gudang'] as $key => $val) {
		$frm .= 0;
	}

	$frm .= 0;
	$frm .= 1;
	$hfrm[0] = $_SESSION['lang']['penerimaanbarang'];
	$hfrm[1] = $_SESSION['lang']['list'];
	drawTab('FRM', $hfrm, $frm, 200, 900);
}
else {
	echo ' Error: Transaction period is missing';
}

CLOSE_BOX();
close_body();

?>
