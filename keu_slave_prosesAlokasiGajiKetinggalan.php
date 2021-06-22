<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = $param['sampai'];
$param['karyawanid'] = str_replace('#', '\\'', $param['karyawanid']);
$str = 'select distinct b.kodekegiatan,b.kodeorg,c.noakun from ' . $dbname . '.kebun_kehadiran_vw a ' . "\r\n" . '      left join ' . $dbname . '.kebun_perawatan_vw b on a.notransaksi=b.notransaksi ' . "\r\n" . '      left join ' . $dbname . '.setup_kegiatan c on b.kodekegiatan=c.kodekegiatan    ' . "\r\n" . '      where a.tanggal between \'' . $param['dari'] . '\' and \'' . $param['sampai'] . '\'' . "\r\n" . '      and a.karyawanid in(' . $param['karyawanid'] . ') and a.unit=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . "\r\n" . '      having noakun!=\'\'';
$res = mysql_query($str);
$str2 = 'select distinct kodeorg from ' . $dbname . '.kebun_prestasi_vw a   ' . "\r\n" . '      where tanggal between \'' . $param['dari'] . '\' and \'' . $param['sampai'] . '\'' . "\r\n" . '      and karyawanid in(' . $param['karyawanid'] . ') and unit=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
$res2 = mysql_query($str2);
$jum = mysql_num_rows($res) + mysql_num_rows($res2);
$param['jumlah'] = $param['jumlah'] / $jum;

if ($param['row'] == '1') {
	$nr = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/M0/';
	$stw = 'delete from ' . $dbname . '.keu_jurnalht where nojurnal like \'' . $nr . '%\' and noreferensi=\'ALK_GAJI_LBR\'';
	mysql_query($stw);
}

if (0 < mysql_num_rows($res)) {
	$kodeJurnal = 'M0';
	$queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', ' jurnalid=\'' . $kodeJurnal . '\'');
	$resParam = fetchData($queryParam);
	$akunkredit = $resParam[0]['noakunkredit'];
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataResPerawatan['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_LBR', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;

	while ($bar = mysql_fetch_object($res)) {
		if ($param['jumlah'] < 0) {
			$akundebet = $akunkredit;
			$param['jumlah'] = $param['jumlah'] * -1;
		}
		else {
			$akundebet = $bar->noakun;
		}

		$dataResPerawatan['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'Alokasi Gaji(Unalocated) ' . $tanggal, 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $bar->kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_LBR', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0');
		++$noUrut;
		$dataResPerawatan['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'Alokasi Gaji(Unalocated) ' . $tanggal, 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $bar->kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_LBR', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0');
		++$noUrut;
	}

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataResPerawatan['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataResPerawatan['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Perawatan Error : ' . mysql_error() . "\n";
			break;
		}

		if ($detailErr == '') {
			$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

			if (!mysql_query($updJurnal)) {
				echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header BTL Error : ' . mysql_error() . "\n";
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

if (0 < mysql_num_rows($res2)) {
	$kodeJurnal = 'PNN01';
	$queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit,noakundebet', ' jurnalid=\'' . $kodeJurnal . '\'');
	$resParam = fetchData($queryParam);
	$akunkredit = $resParam[0]['noakunkredit'];
	$akundebet = $resParam[0]['noakundebet'];
	$kegpanen = $akundebet . '01';
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataResPanen['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_LBR', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;

	while ($bar2 = mysql_fetch_object($res2)) {
		if ($param['jumlah'] < 0) {
			$x = $akundebet;
			$akundebet = $akunkredit;
			$akunkredit = $x;
			$param['jumlah'] = $param['jumlah'] * -1;
		}

		$dataResPanen['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'Alokasi Gaji(Unalocated) ' . $tanggal, 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegpanen, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_LBR', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar2->kodeorg, 'revisi' => '0');
		++$noUrut;
		$dataResPanen['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'Alokasi Gaji(Unalocated) ' . $tanggal, 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegpanen, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_LBR', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar2->kodeorg, 'revisi' => '0');
		++$noUrut;
	}

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataResPanen['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataResPanen['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail panen Error : ' . mysql_error() . "\n";
			break;
		}

		if ($detailErr == '') {
			$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

			if (!mysql_query($updJurnal)) {
				echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header BTL Error : ' . mysql_error() . "\n";
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

?>
