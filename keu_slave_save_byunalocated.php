<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$bylistrik = $_POST['bylistrik'];
$byair = $_POST['byair'];
$byklinik = $_POST['byklinik'];
$bysosial = $_POST['bysosial'];
$perumahan = $_POST['perumahan'];
$natura = $_POST['natura'];
$jms = $_POST['jms'];
$karyawanid = $_POST['karyawanid'];
$subbagian = $_POST['subbagian'];
$periode = $_POST['periode'];
$kodeorg = $_SESSION['empl']['lokasitugas'];
$method = $_POST['method'];
$namakaryawan = $_POST['namakaryawan'];
$bylistrik == '' ? $bylistrik = 0 : '';
$byair == '' ? $byair = 0 : '';
$byklinik == '' ? $byklinik = 0 : '';
$bysosial == '' ? $bysosial = 0 : '';
$perumahan == '' ? $perumahan = 0 : '';
$natura == '' ? $natura = 0 : '';
$jms == '' ? $jms = 0 : '';
$tanggal = $periode . '-28';
$str = 'select * from ' . $dbname . '.setup_periodeakuntansi where periode=\'' . $periode . '\' ' . "\r\n" . '           and kodeorg=\'' . $kodeorg . '\' and tutupbuku=0';

if (0 < mysql_num_rows(mysql_query($str))) {
}
else {
	exit('Error: Period is closed');
}

$notransaksi = $periode . '-' . $karyawanid;

switch ($method) {
case 'save':
	$str = ' delete from ' . $dbname . '.keu_byunalocated where notransaksi=\'' . $notransaksi . '\'';

	if (mysql_query($str)) {
		$str = 'insert into ' . $dbname . '.keu_byunalocated(periode, karyawanid, listrik, air, klinik, perumahan,natura,jms,sosial, posting, updateby, kodeorg, subbagian,notransaksi)' . "\r\n" . '              values(\'' . $periode . '\',' . $karyawanid . ',' . $bylistrik . ',' . $byair . ',' . $byklinik . ',' . $perumahan . ',' . $natura . ',' . $jms . ',' . $bysosial . ',0,' . $_SESSION['standard']['userid'] . ',\'' . $kodeorg . '\',\'' . $subbagian . '\',\'' . $notransaksi . '\'' . "\r\n" . '              )';
		if ((0 < $bylistrik) || (0 < $byair) || (0 < $byklinik) || (0 < $bysosial) || (0 < $perumahan) || (0 < $natura) || (0 < $jms)) {
			if (!mysql_query($str)) {
				echo 'Gagal:' . mysql_error($conn);
			}
		}
		else {
			echo 'deleted';
		}
	}
	else {
		echo ' Error:' . mysql_error($conn);
	}

	break;

case 'post':
	$str = 'select * from ' . $dbname . '.keu_byunalocated where notransaksi=\'' . $notransaksi . '\'';
	$res = mysql_query($str);
	$airlistrik = 0;

	while ($bar = mysql_fetch_object($res)) {
		$bylistrik = $bar->listrik;
		$byair = $bar->air;
		$airlistrik = $bylistri + $byair;
		$perumahan = $bar->perumahan;
		$byklinik = $bar->klinik;
		$bysosial = $bar->sosial;
		$natura = $bar->natura;
		$jms = $bar->jms;
	}

	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.sdm_5periodegaji ' . "\r\n" . '              where periode=\'' . $periode . '\' and jenisgaji=\'H\' and kodeorg=\'' . $kodeorg . '\'';
	$rex = mysql_query($str);

	if (0 < mysql_num_rows($rex)) {
		while ($bax = mysql_fetch_object($rex)) {
			$dari = $bax->tanggalmulai;
			$sampai = $bax->tanggalsampai;
		}
	}
	else {
		exit('Error: The payroll period has not been setup in that period');
	}

	$str1 = 'SELECT distinct b.kodekegiatan,b.kodeorg,c.noakun FROM ' . $dbname . '.kebun_kehadiran_vw a' . "\r\n" . '            left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '            left join ' . $dbname . '.setup_kegiatan c on b.kodekegiatan=c.kodekegiatan' . "\r\n" . '            where a.karyawanid=' . $karyawanid . ' and tanggal between \'' . $dari . '\' and \'' . $sampai . '\'' . "\r\n" . '            and unit=\'' . $kodeorg . '\'';
	$resu = mysql_query($str1);

	while ($baru = mysql_fetch_object($resu)) {
		$blok['debet'][] = $baru->noakun;
		$blok['blok'][] = $baru->kodeorg;
	}

	$strp = 'select noakundebet from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'PNN01\'';
	$resp = mysql_query($strp);
	$akunpanen = '';

	while ($barp = mysql_fetch_object($resp)) {
		$akunpanen = $barp->noakundebet;
	}

	$str1 = 'SELECT kodeorg FROM ' . $dbname . '.kebun_prestasi_vw' . "\r\n" . '            where karyawanid=' . $karyawanid . ' and tanggal between \'' . $dari . '\' and \'' . $sampai . '\'' . "\r\n" . '            and unit=\'' . $kodeorg . '\'';
	$resu = mysql_query($str1);

	while ($baru = mysql_fetch_object($resu)) {
		if ($akunpanen == '') {
			exit('Error: PNN01 has not been registred in journal parameter');
		}
		else {
			$blok['debet'][] = $akunpanen;
			$blok['blok'][] = $baru->kodeorg;
		}
	}

	$str1 = 'select distinct a.tipetransaksi,b.kodeorg from ' . $dbname . '.kebun_aktifitas a left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '         where ' . "\r\n" . '         (nikmandor=\'' . $karyawanid . '\' or nikmandor1=\'' . $karyawanid . '\'' . "\r\n" . '          or keranimuat=\'' . $karyawanid . '\' or nikasisten=\'' . $karyawanid . '\')' . "\r\n" . '          and a.tanggal >=\'' . $dari . '\' and tanggal <=\'' . $sampai . '\' having kodeorg is not null';
	$resu = mysql_query($str1);

	while ($baru = mysql_fetch_object($resu)) {
		if (str_replace(' ', '', $baru->tipetransaksi) == 'BBT') {
			$group = 'KBNL0';
		}
		else if (str_replace(' ', '', $baru->tipetransaksi) == 'TBM') {
			$group = 'KBNL1';
		}
		else if (str_replace(' ', '', $baru->tipetransaksi) == 'TM') {
			$group = 'KBNL2';
		}
		else if (str_replace(' ', '', $baru->tipetransaksi) == 'PNN') {
			$group = 'KBNL3';
		}

		$strp = 'select noakundebet from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\'';
		$resp = mysql_query($strp);
		$akund = '';

		while ($barp = mysql_fetch_object($resp)) {
			$akund = $barp->noakundebet;
		}

		if ($akund == '') {
			exit('Error:  ' . $group . ' has not been registred in journal parameter');
		}

		$blok['debet'][] = $akund;
		$blok['blok'][] = $baru->kodeorg;
	}

	$jumlahblok = count($blok['blok']);

	if (0 < $jumlahblok) {
		$perumahan = $perumahan / $jumlahblok;
		$byklinik = $byklinik / $jumlahblok;
		$bysosial = $bysosial / $jumlahblok;
		$natura = $natura / $jumlahblok;
		$jms = $jms / $jumlahblok;
		$airlistrik = $airlistrik / $jumlahblok;
	}
	else {
		$blok['debet'][0] = '';
		$blok['blok'][0] = '';
	}

	$strf = 'select noakundebet,noakunkredit,jurnalid from ' . $dbname . '.keu_5parameterjurnal ' . "\r\n" . '             where jurnalid in(\'BUN01\',\'BUN02\',\'BUN03\',\'BUN04\',\'BUN05\',\'BUN06\')';
	$resf = mysql_query($strf);

	while ($barf = mysql_fetch_object($resf)) {
		if ($barf->jurnalid == 'BUN01') {
			$BUN01['debet'] = $barf->noakundebet;
			$BUN01['kredit'] = $barf->noakunkredit;
		}

		if ($barf->jurnalid == 'BUN02') {
			$BUN02['debet'] = $barf->noakundebet;
			$BUN02['kredit'] = $barf->noakunkredit;
		}

		if ($barf->jurnalid == 'BUN03') {
			$BUN03['debet'] = $barf->noakundebet;
			$BUN03['kredit'] = $barf->noakunkredit;
		}

		if ($barf->jurnalid == 'BUN04') {
			$BUN04['debet'] = $barf->noakundebet;
			$BUN04['kredit'] = $barf->noakunkredit;
		}

		if ($barf->jurnalid == 'BUN05') {
			$BUN05['debet'] = $barf->noakundebet;
			$BUN05['kredit'] = $barf->noakunkredit;
		}

		if ($barf->jurnalid == 'BUN06') {
			$BUN06['debet'] = $barf->noakundebet;
			$BUN06['kredit'] = $barf->noakunkredit;
		}
	}

	if (($BUN01['debet'] == '') || ($BUN02['debet'] == '') || ($BUN03['debet'] == '') || ($BUN04['debet'] == '') || ($BUN05['debet'] == '') || ($BUN06['debet'] == '')) {
		exit('Error: setup parameter jurnal belum lengkap BUN01 - BUN06');
	}

	if (($BUN01['kredit'] == '') || ($BUN02['kredit'] == '') || ($BUN03['kredit'] == '') || ($BUN04['kredit'] == '') || ($BUN05['kredit'] == '') || ($BUN06['kredit'] == '')) {
		exit('Error: setup parameter jurnal belum lengkap BUN01 - BUN06');
	}

	$kodeJurnal = 'BUN01';

	if (1 < $perumahan) {
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $perumahan * $jumlahblok : $perumahan, 'totalkredit' => -1 * (0 < $jumlahblok ? $perumahan * $jumlahblok : $perumahan), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN01['debet'], 'keterangan' => 'Alokasi Perumahan ' . $namakaryawan . ' (BUN01-D)', 'jumlah' => $perumahan, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN01['kredit'], 'keterangan' => 'Alokasi Perumahan ' . $namakaryawan . ' (BUN01-C)', 'jumlah' => -1 * $perumahan, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	$kodeJurnal = 'BUN02';

	if (1 < $byklinik) {
		unset($dataRes);
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $byklinik * $jumlahblok : $byklinik, 'totalkredit' => -1 * (0 < $jumlahblok ? $byklinik * $jumlahblok : $byklinik), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN02['debet'], 'keterangan' => 'Alokasi Klinik ' . $namakaryawan . ' (BUN02-D)', 'jumlah' => $byklinik, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN02['kredit'], 'keterangan' => 'Alokasi Klinik ' . $namakaryawan . ' (BUN02-C)', 'jumlah' => -1 * $byklinik, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	$kodeJurnal = 'BUN03';

	if (1 < $bysosial) {
		unset($dataRes);
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $bysosial * $jumlahblok : $bysosial, 'totalkredit' => -1 * (0 < $jumlahblok ? $bysosial * $jumlahblok : $bysosial), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN03['debet'], 'keterangan' => 'Alokasi BySosial ' . $namakaryawan . ' (BUN03-D)', 'jumlah' => $bysosial, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN03['kredit'], 'keterangan' => 'Alokasi BySosial ' . $namakaryawan . ' (BUN03-C)', 'jumlah' => -1 * $bysosial, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	$kodeJurnal = 'BUN04';

	if (1 < $natura) {
		unset($dataRes);
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $natura * $jumlahblok : $natura, 'totalkredit' => -1 * (0 < $jumlahblok ? $natura * $jumlahblok : $natura), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN04['debet'], 'keterangan' => 'Alokasi Natura ' . $namakaryawan . ' (BUN04-D)', 'jumlah' => $natura, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN04['kredit'], 'keterangan' => 'Alokasi Natura ' . $namakaryawan . ' (BUN04-C)', 'jumlah' => -1 * $natura, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	$kodeJurnal = 'BUN05';

	if (1 < $airlistrik) {
		unset($dataRes);
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $airlistrik * $jumlahblok : $airlistrik, 'totalkredit' => -1 * (0 < $jumlahblok ? $airlistrik * $jumlahblok : $airlistrik), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN05['debet'], 'keterangan' => 'Alokasi AirListrik ' . $namakaryawan . ' (BUN05-D)', 'jumlah' => $airlistrik, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN05['kredit'], 'keterangan' => 'Alokasi AirListrik ' . $namakaryawan . ' (BUN05-C)', 'jumlah' => -1 * $airlistrik, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	$kodeJurnal = 'BUN06';

	if (1 < $jms) {
		unset($dataRes);
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 0 < $jumlahblok ? $jms * $jumlahblok : $jms, 'totalkredit' => -1 * (0 < $jumlahblok ? $jms * $jumlahblok : $jms), 'amountkoreksi' => '0', 'noreferensi' => $notransaksi, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 0;

		foreach ($blok['blok'] as $key => $val) {
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $blok['debet'][$key] != '' ? $blok['debet'][$key] : $BUN06['debet'], 'keterangan' => 'Alokasi Jamsostek ' . $namakaryawan . ' (BUN06-D)', 'jumlah' => $jms, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $BUN06['kredit'], 'keterangan' => 'Alokasi Jamsostek ' . $namakaryawan . ' (BUN06-C)', 'jumlah' => -1 * $jms, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karyawanid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $notransaksi, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $val, 'revisi' => '0');
		}

		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header unalocated Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Unalocated Error : ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr;
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr;
			exit();
		}
	}

	if (($headErr == '') && ($detailErr == '')) {
		$str = 'update ' . $dbname . '.keu_byunalocated set posting=1 where notransaksi=\'' . $notransaksi . '\'';
		mysql_query($str);
	}

	break;
}

?>
