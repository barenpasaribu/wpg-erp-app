<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/zLib.php';
$optBlokLm = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama');

if (isTransactionPeriod()) {
	$induk = $_POST['induk'];
	$untukunit = $_POST['untukunit'];
	$blehh = '<option value=\'\'></option>';
	$str = 'select distinct kodeorganisasi,namaorganisasi,tipe from ' . $dbname . '.organisasi where induk=\'' . $induk . '\' and tipe not like \'%gudang%\' order by kodeorganisasi';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		if (strlen($induk) == 6) {
			if ($_POST['afdeling'] != '') {
				$blehh .= '<option value=\'' . $bar->kodeorganisasi . '\' ' . ($bar->kodeorganisasi == $_POST['afdeling'] ? 'selected' : '') . '>' . $bar->kodeorganisasi . '-' . $optBlokLm[$bar->kodeorganisasi] . '-' . $bar->namaorganisasi . '</option>';
			}
			else {
				$blehh .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->kodeorganisasi . '-' . $optBlokLm[$bar->kodeorganisasi] . '-' . $bar->namaorganisasi . '</option>';
			}
		}
		else if ($_POST['afdeling'] != '') {
			$blehh .= '<option value=\'' . $bar->kodeorganisasi . '\' ' . ($bar->kodeorganisasi == $_POST['afdeling'] ? 'selected' : '') . '>' . $bar->namaorganisasi . '</option>';
		}
		else {
			$blehh .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
		}
	}

	if ((substr($induk, 0, 2) == 'AK') || (substr($induk, 0, 2) == 'PB')) {
		$blehh = '';
		$str = 'select kode,nama from ' . $dbname . '.project where kode=\'' . $induk . '\'';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$blehh .= '<option value=\'' . $bar->kode . '\'>Project:' . $bar->kode . '-' . $bar->nama . '</option>';
		}
	}
	else {
		$str = 'select kode,nama from ' . $dbname . '.project where kodeorg=\'' . $induk . '\' and posting=0';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$blehh .= '<option value=\'' . $bar->kode . '\'>Project:' . $bar->kode . '-' . $bar->nama . '</option>';
		}
	}

	$kdunit = $_POST['induk'];
	$whr = '';

	if (4 < strlen($_POST['induk'])) {
		if (substr($_POST['induk'], 0, 2) == 'AK') {
			$kdunit = 'SSRO';
			$whr = 'and subbagian=\'SSROTR\'';
		}
		else {
			$kdunit = substr($_POST['induk'], 0, 4);
			$whr = 'and subbagian=\'' . $_POST['induk'] . '\'';
		}
	}

	if ($kdunit == '') {
		$kdunit = $_POST['untukunit'];
	}

	if ($_POST['induk'] == '') {
		$kdunit = $untukunit;
	}

	$skary = 'select a.karyawanid as nikmandor,a.namakaryawan,a.nik,a.subbagian,b.namajabatan from ' . $dbname . '.datakaryawan a ' . 'left join ' . $dbname . '.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like \'%Mandor%\' or b.alias like \'%Asisten%\' or b.alias like \'%Kepala%\' or b.alias like \'%Admin%\' or b.alias like \'%KTU%\') and lokasitugas=\'' . $kdunit . '\' and (tanggalkeluar is NULL or tanggalkeluar > \'' . $_SESSION['org']['period']['start'] . '\' or tanggalkeluar=\'0000-00-00\') order by a.namakaryawan asc';

	#exit(mysql_error($conn));
	($qkary = mysql_query($skary)) || true;

	while ($rkary = mysql_fetch_assoc($qkary)) {
		if ($_POST['namapenerima'] == $rkary['karyawanid']) {
			$optKary .= '<option value=\'' . $rkary['nikmandor'] . '\'>' . $rkary['nik'] . '-' . $rkary['namakaryawan'] . '</option>';
		}
		else {
			$optKary .= '<option value=\'' . $rkary['nikmandor'] . '\'>' . $rkary['nik'] . '-' . $rkary['namakaryawan'] . '</option>';
		}
	}

	$optKary .= '<option value=\'NULL\'></option>';
	echo $blehh . '####' . $optKary;
}
else {
	echo ' Error: Transaction Period missing';
}

?>
