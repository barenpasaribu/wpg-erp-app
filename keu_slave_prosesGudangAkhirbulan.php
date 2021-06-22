<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$param['hartot'] = str_replace(',', '', $param['hartot']);

if ($_POST['hartot'] < 0.90000000000000002) {
	exit('Masih Ada harga barang yang belum ada harganya, mohon diperiksa transaksi anda\\natau hubungi departement terkait');
}

$str = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $_POST['kodebarang'] . '\'';
$res = mysql_query($str);
$namabarang = '';

while ($bar = mysql_fetch_object($res)) {
	$namabarang = $bar->namabarang;
}

if ($namabarang == '') {
	$namabaran = $_POST['kodebarang'];
}

if ($_POST['tipetransaksi'] == 1) {
	$kodekl = substr($_POST['idsupplier'], 0, 4);
	$str = 'select noakun from ' . $dbname . '.log_5klsupplier where kode=\'' . $kodekl . '\'';
	$res = mysql_query($str);
	$akunspl = '';

	while ($bar = mysql_fetch_object($res)) {
		$akunspl = $bar->noakun;
	}

	$klbarang = substr($_POST['kodebarang'], 0, 3);
	$str = 'select noakun from ' . $dbname . '.log_5klbarang where kode=\'' . $klbarang . '\'';
	$res = mysql_query($str);
	$akunbarang = '';

	while ($bar = mysql_fetch_object($res)) {
		$akunbarang = $bar->noakun;
	}

	if (($akunbarang == '') || ($akunspl == '')) {
		exit('Error: Noakun  Noakun barang atau supplier  belum ada untuk transaksi' . $_POST['notransaksi']);
	}
	else {
		$kodeJurnal = 'INVM1';
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $_POST['tanggal']) . '/' . substr($_POST['kodegudang'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $_POST['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => 'Pembelian barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => 'Pembelian barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
	}

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
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
			else {
				$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

				if (!mysql_query($updTrans)) {
					echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

					if (!mysql_query($RBJurnal)) {
						echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
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
else if ($_POST['tipetransaksi'] == 3) {
	$pengirim = substr($_POST['gudangx'], 0, 4);
	$ptPengirim = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pengirim . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptPengirim = $bar->induk;
	}

	$ptGudang = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($_POST['kodegudang'], 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptGudang = $bar->induk;
	}

	$akunspl = '';

	if ($ptGudang != $ptPengirim) {
		$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $pengirim . '\' and jenis=\'inter\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunhutang;
		}
	}
	else {
		$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $pengirim . '\' and jenis=\'intra\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunhutang;
		}
	}

	$klbarang = substr($_POST['kodebarang'], 0, 3);
	$str = 'select noakun from ' . $dbname . '.log_5klbarang where kode=\'' . $klbarang . '\'';
	$res = mysql_query($str);
	$akunbarang = '';

	while ($bar = mysql_fetch_object($res)) {
		$akunbarang = $bar->noakun;
	}

	if (($akunbarang == '') || ($akunspl == '')) {
		exit('Error: Noakun barang atau intra/interco belum ada untuk transaksi ' . $_POST['notransaksi']);
	}
	else {
		$kodeJurnal = 'INVM1';
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $_POST['tanggal']) . '/' . substr($_POST['kodegudang'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $_POST['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => 'Mutasi barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => 'Mutasi barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
	}

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
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
			else {
				$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

				if (!mysql_query($updTrans)) {
					echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

					if (!mysql_query($RBJurnal)) {
						echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
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
else if ($_POST['tipetransaksi'] == 5) {
	$pengguna = substr($_POST['untukunit'], 0, 4);
	$ptpengguna = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pengguna . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptpengguna = $bar->induk;
	}

	$str = 'select akunpiutang,jenis from ' . $dbname . '.keu_5caco where ' . "\r\n" . '           kodeorg=\'' . $pengguna . '\'';
	$res = mysql_query($str);
	$intraco = '';
	$interco = '';

	while ($bar = mysql_fetch_object($res)) {
		if ($bar->jenis == 'intra') {
			$intraco = $bar->akunpiutang;
		}
		else {
			$interco = $bar->akunpiutang;
		}
	}

	$ptGudang = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($_POST['kodegudang'], 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptGudang = $bar->induk;
	}

	$akunspl = '';

	if ($ptGudang != $ptpengguna) {
		$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . substr($_POST['kodegudang'], 0, 4) . '\' and jenis=\'inter\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunhutang;
		}

		$inter = $interco;

		if ($akunspl == '') {
			exit('Error: Akun intraco  atau interco belum ada untuk unit ' . $pengguna);
		}
	}
	else if ($pengguna != substr($_POST['kodegudang'], 0, 4)) {
		$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . substr($_POST['kodegudang'], 0, 4) . '\' and jenis=\'intra\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunhutang;
		}

		$inter = $intraco;

		if ($akunspl == '') {
			exit('Error: Akun intraco  atau interco belum ada untuk unit ' . $pengguna);
		}
	}

	$statustm = '';
	$str = 'select statusblok from ' . $dbname . '.setup_blok where kodeorg=\'' . $_POST['kodeblok'] . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$statustm = $bar->statusblok;
	}

	if (($statustm != '') && ($_POST['kodemesin'] == '')) {
		$str = 'select noakun from ' . $dbname . '.setup_kegiatan where ' . "\r\n" . '                kodekegiatan=\'' . $_POST['kodekegiatan'] . '\'';
	}
	else {
		$str = 'select noakun from ' . $dbname . '.setup_kegiatan where ' . "\r\n" . '                kodekegiatan=\'' . $_POST['kodekegiatan'] . '\'';
	}

	$akunpekerjaan = '';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$akunpekerjaan = $bar->noakun;
	}

	if ($akunpekerjaan == '') {
		exit('Error: Akun pekerjaan belum ada untuk kegiatan ' . $_POST['kodekegiatan']);
	}

	$klbarang = substr($_POST['kodebarang'], 0, 3);
	$str = 'select noakun from ' . $dbname . '.log_5klbarang where kode=\'' . $klbarang . '\'';
	$res = mysql_query($str);
	$akunbarang = '';

	while ($bar = mysql_fetch_object($res)) {
		$akunbarang = $bar->noakun;
	}

	if ($akunbarang == '') {
		exit('Error: Noakun barang belum ada untuk transaksi' . $_POST['notransaksi']);
	}
	else if ($pengguna == substr($_POST['kodegudang'], 0, 4)) {
		$kodeJurnal = 'INVK1';
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $_POST['tanggal']) . '/' . substr($_POST['kodegudang'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $_POST['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 1 * $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$keterangan = 'Pemakaian barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'] . ' ' . $_POST['keterangan'];
		$keterangan = substr($keterangan, 0, 150);
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => $_POST['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => $_POST['kodemesin'], 'nodok' => '', 'kodeblok' => $_POST['kodeblok'], 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => $_POST['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => $_POST['kodemesin'], 'nodok' => '', 'kodeblok' => $_POST['kodeblok'], 'revisi' => '0');
		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
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
				else {
					$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

					if (!mysql_query($updTrans)) {
						echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
						$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

						if (!mysql_query($RBDet)) {
							echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
							exit();
						}

						$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

						if (!mysql_query($RBJurnal)) {
							echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
							exit();
						}

						exit();
					}
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
	else {
		$kodeJurnal = 'INVK1';
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $_POST['tanggal']) . '/' . substr($_POST['kodegudang'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $_POST['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => 1 * $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$keterangan = 'Pemakaian barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'] . ' ' . $_POST['keterangan'];
		$keterangan = substr($keterangan, 0, 150);
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $inter, 'keterangan' => $keterangan, 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Error sisi pemilik : ' . mysql_error() . "\n";
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
				else {
					$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

					if (!mysql_query($updTrans)) {
						echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
						$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

						if (!mysql_query($RBDet)) {
							echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
							exit();
						}

						$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

						if (!mysql_query($RBJurnal)) {
							echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
							exit();
						}

						exit();
					}
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

		$kodeJurnal = 'INVK1';
		$stri = 'select tanggalmulai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '                           where kodeorg=\'' . $pengguna . '\' and tutupbuku=0';
		$tanggalsana = '';
		$resi = mysql_query($stri);

		while ($bari = mysql_fetch_object($resi)) {
			$tanggalsana = $bari->tanggalmulai;
		}

		if (($tanggalsana == '') || (substr($tanggalsana, 0, 7) == substr($_POST['tanggal'], 0, 7))) {
			$tanggalsana = $_POST['tanggal'];
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $ptpengguna . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggalsana) . '/' . $pengguna . '/' . $kodeJurnal . '/' . $konter;
		unset($dataRes['header']);
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggalsana, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$keterangan = 'Pemakaian barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'] . ' ' . substr($_POST['tanggal'], 0, 7) . ' ' . $_POST['keterangan'];
		$keterangan = substr($keterangan, 0, 150);
		$noUrut = 1;
		unset($dataRes['detail']);
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $keterangan, 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $_POST['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => $_POST['kodemesin'], 'nodok' => '', 'kodeblok' => $_POST['kodeblok'], 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggalsana, 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => $keterangan, 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $_POST['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => $_POST['kodemesin'], 'nodok' => '', 'kodeblok' => $_POST['kodeblok'], 'revisi' => '0');
		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Error sisi pengguna: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr == '') {
				$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $ptpengguna . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

				if (!mysql_query($updJurnal)) {
					echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
				else {
					$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

					if (!mysql_query($updTrans)) {
						echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
						$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

						if (!mysql_query($RBDet)) {
							echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
							exit();
						}

						$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $ptpengguna . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

						if (!mysql_query($RBJurnal)) {
							echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
							exit();
						}

						exit();
					}
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
}
else if ($_POST['tipetransaksi'] == 7) {
	$penerima = substr($_POST['gudangx'], 0, 4);
	$ptPenerima = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $penerima . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptPenerima = $bar->induk;
	}

	$ptGudang = '';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($_POST['kodegudang'], 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptGudang = $bar->induk;
	}

	$akunspl = '';

	if ($ptGudang != $ptPenerima) {
		$str = 'select akunpiutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $penerima . '\' and jenis=\'inter\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunpiutang;
		}
	}
	else {
		$str = 'select akunpiutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $penerima . '\' and jenis=\'intra\'';
		$res = mysql_query($str);
		$akunspl = '';

		while ($bar = mysql_fetch_object($res)) {
			$akunspl = $bar->akunpiutang;
		}
	}

	$klbarang = substr($_POST['kodebarang'], 0, 3);
	$str = 'select noakun from ' . $dbname . '.log_5klbarang where kode=\'' . $klbarang . '\'';
	$res = mysql_query($str);
	$akunbarang = '';

	while ($bar = mysql_fetch_object($res)) {
		$akunbarang = $bar->noakun;
	}

	if (($akunbarang == '') || ($akunspl == '')) {
		exit('Error: Noakun barang atau intra/interco belum ada untuk transaksi ' . $_POST['notransaksi']);
	}
	else {
		$kodeJurnal = 'INVK1';
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $_POST['tanggal']) . '/' . substr($_POST['kodegudang'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
		$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $_POST['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $_POST['hartot'], 'totalkredit' => -1 * $_POST['hartot'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunspl, 'keterangan' => 'Mutasi barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $_POST['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => 'Mutasi barang ' . $namabarang . ' ' . $_POST['jumlah'] . ' ' . $_POST['satuan'], 'jumlah' => -1 * $_POST['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($_POST['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $_POST['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $_POST['idsupplier'], 'noreferensi' => $_POST['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => $_POST['nopo'], 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
	}

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
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
			else {
				$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

				if (!mysql_query($updTrans)) {
					echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
					$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

					if (!mysql_query($RBDet)) {
						echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
						exit();
					}

					$RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

					if (!mysql_query($RBJurnal)) {
						echo 'Rollback Update Jurnal Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
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
else {
	$updTrans = updateQuery($dbname, 'log_transaksiht', array('statusjurnal' => 1), 'notransaksi=\'' . $_POST['notransaksi'] . '\'');

	if (!mysql_query($updTrans)) {
		echo 'Update Status Jurnal Error : ' . mysql_error() . "\n";
	}
}

?>
