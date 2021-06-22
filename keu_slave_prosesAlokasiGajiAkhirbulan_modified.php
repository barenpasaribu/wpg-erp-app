<?php


function prosesGajiSipil()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$group = 'SIPL1';
	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_SIPL_GYMH', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By. Perumahan', 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_SIPL_GYMH', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By.Perumahan', 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_SIPL_GYMH', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header SIPIL Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail SIPIL Error : ' . mysql_error() . "\n";
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

function prosesGajiWs()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	if (($param['komponen'] == 1) || ($param['komponen'] == 54)) {
		$group = 'WSG0';
	}
	else {
		if (($param['komponen'] == 12) || ($param['komponen'] == 16) || ($param['komponen'] == 17)) {
			$group = 'WSG1';
		}
		else if ($param['komponen'] == 14) {
			$group = 'WSG3';
		}
		else if ($param['komponen'] == 13) {
			$group = 'WSG4';
		}
		else {
			$group = 'WSG2';
		}
	}

	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_WS_GYMH', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By.Bengkel', 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_WS_GYMH', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By.Bengkel', 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_WS_GYMH', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header WS Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail WS Error : ' . mysql_error() . "\n";
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

function prosesGajiTraksi()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;

	if ($param['komponen'] == 1) {
		$group = 'VHCG0';
	}
	else {
		if (($param['komponen'] == 12) || ($param['komponen'] == 16) || ($param['komponen'] == 17)) {
			$group = 'VHCG1';
		}
		else if ($param['komponen'] == 14) {
			$group = 'VHCG3';
		}
		else if ($param['komponen'] == 13) {
			$group = 'VHCG4';
		}
		else {
			$group = 'VHCG2';
		}
	}

	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_TRK_GYMH', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$str = 'select * from ' . $dbname . '.vhc_5operator where karyawanid=' . $param['karyawanid'];
	$res = mysql_query($str);
	$kodekend = '';

	while ($bas = mysql_fetch_object($res)) {
		$kodekend = $bas->vhc;
	}

	if ($kodekend != '') {
		$noUrut = 1;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By.Kendaraan', 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_TRK_GYMH', 'noaruskas' => '', 'kodevhc' => $kodekend, 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' By.Kendaraan', 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_TRK_GYMH', 'noaruskas' => '', 'kodevhc' => $kodekend, 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header Traksi Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr .= 'Insert Detail Traksi Error : ' . mysql_error() . "\n";
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
	else if ($param['tipeorganisasi'] == 'WORKSHOP') {
		prosesgajiws();
	}
	else {
		prosesGajiKebun();
	}
}

function prosesGajiAfdeling()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.sdm_5periodegaji' . "\r\n" . '          where periode=\'' . $param['periode'] . '\' and kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '          and jenisgaji=\'B\'';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: Belum ada periode gaji untuk unit ' . $_SESSION['empl']['lokasitugas']);
	}

	while ($bar = mysql_fetch_object($res)) {
		$tanggalmulai = $bar->tanggalmulai;
		$tanggalsampai = $bar->tanggalsampai;
	}

	$str = 'select distinct a.tipetransaksi,b.kodeorg from ' . $dbname . '.kebun_aktifitas a left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '         where' . "\r\n" . '         (nikmandor=\'' . $param['karyawanid'] . '\' or nikmandor1=\'' . $param['karyawanid'] . '\'' . "\r\n" . '         or keranimuat=\'' . $param['karyawanid'] . '\' or nikasisten=\'' . $param['karyawanid'] . '\')' . "\r\n" . '         and a.tanggal >=\'' . $tanggalmulai . '\' and tanggal <=\'' . $tanggalsampai . '\' having kodeorg is not null';
	$res = mysql_query($str);
	$numblk = mysql_num_rows($res);
	$porsi = 0;

	if (0 < $numblk) {
		$porsi = $param['jumlah'] / $numblk;
		$noUrut = 0;

		while ($bar = mysql_fetch_object($res)) {
			$dataRes['header'] = '';
			$dataRes['detail'] = '';
			$dataRes1['detail'] = '';

			if ($bar->tipetransaksi == 'BBT') {
				$group = 'KBNL0';
			}
			else {
				if (($bar->tipetransaksi == 'TBM') || ($bar->tipetransaksi == 'TB')) {
					$group = 'KBNL1';
				}
				else if ($bar->tipetransaksi == 'TM') {
					$group = 'KBNL2';
				}
				else if ($bar->tipetransaksi == 'PNN') {
					$group = 'KBNL3';
				}
				else {
					prosesGajiKebun();
				}
			}

			if (($bar->tipetransaksi != 'BBT') && (strlen($bar->kodeorg) < 7)) {
			}

			$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '                  where jurnalid=\'' . $group . '\' limit 1';
			$res1 = mysql_query($str);

			if (mysql_num_rows($res1) < 1) {
				exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $group);
			}
			else {
				$akundebet = '';
				$akunkredit = '';
				$bar1 = mysql_fetch_object($res1);
				$akundebet = $bar1->noakundebet;
				$akunkredit = $bar1->noakunkredit;
			}

			$kodeJurnal = $group;
			$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
			$tmpKonter = fetchData($queryJ);
			$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
			$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
			$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $porsi, 'totalkredit' => -1 * $porsi, 'amountkoreksi' => '0', 'noreferensi' => 'ALK_WAS', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' (ALK)', 'jumlah' => $porsi, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_WAS', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'] . ' (ALK)', 'jumlah' => -1 * $porsi, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_WAS', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0');
			++$noUrut;
			$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

			if (!mysql_query($insHead)) {
				$headErr .= 'Insert Header AFD Error : ' . mysql_error() . "\n";
			}

			if ($headErr == '') {
				$detailErr = '';

				foreach ($dataRes['detail'] as $row) {
					$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

					$detailErr .= 'Insert Detail AFD Error : ' . mysql_error() . "\n";
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
	}
	else {
		prosesGajiKebun();
	}
}

function prosesGajiPabrik()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;

	if ($param['komponen'] == 17) {
		$group = 'PKS02';
	}
	else {
		$group = 'PKS01';
	}

	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $param['subbagian'], 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $param['subbagian'], 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
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

function prosesGajiKebun()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	if (($param['komponen'] == 1) || ($param['komponen'] == 54)) {
		$group = 'KBNB0';
	}
	else {
		if (($param['komponen'] == 12) || ($param['komponen'] == 16) || ($param['komponen'] == 17) || ($param['komponen'] == 21) || ($param['komponen'] == 22) || ($param['komponen'] == 23) || ($param['komponen'] == 29) || ($param['komponen'] == 30) || ($param['komponen'] == 32) || ($param['komponen'] == 33) || ($param['komponen'] == 35) || ($param['komponen'] == 36) || ($param['komponen'] == 37) || ($param['komponen'] == 38) || ($param['komponen'] == 39) || ($param['komponen'] == 40) || ($param['komponen'] == 41) || ($param['komponen'] == 42) || ($param['komponen'] == 43) || ($param['komponen'] == 44) || ($param['komponen'] == 45) || ($param['komponen'] == 46) || ($param['komponen'] == 47) || ($param['komponen'] == 48) || ($param['komponen'] == 49) || ($param['komponen'] == 50)) {
			$group = 'KBNB1';
		}
		else if ($param['komponen'] == 14) {
			$group = 'KBNB3';
		}
		else if ($param['komponen'] == 13) {
			$group = 'KBNB4';
		}
		else {
			$group = 'KBNB2';
		}
	}

	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $param['subbagian'], 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => $param['namakomponen'] . ' ' . $param['namakaryawan'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['karyawanid'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $param['subbagian'], 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
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

function prosesGajiHORO()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	if (($param['komponen'] == 1) || ($param['komponentunjjab'] == 2) || ($param['komponenrapel'] == 54)) {
		$group = 'KNTB0';
	}
	else {
		if (($param['komponenjkk'] == 6) || ($param['komponenjkm'] == 7)) {
			$group = 'KNTB1';
		}
		else {
			if (($param['komponennatone'] == 29) || ($param['komponennatdua'] == 30) || ($param['komponennattiga'] == 32) || ($param['komponennatnol'] == 33) || ($param['komponengolsku'] == 51) || ($param['komponentunjharian'] == 21) || ($param['komponentunjlain'] == 22) || ($param['komponentunjdinas'] == 23)) {
				$group = 'KNTB2';
			}
			else if ($param['komponenthr'] == 14) {
				$group = 'KNTB3';
			}
			else if ($param['komponenbonus'] == 13) {
				$group = 'KNTB4';
			}
			else {
				$group = 'KNTB2';
			}
		}
	}

	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes1['header'] = '';
	$dataRes2['header'] = '';
	$dataRes6['header'] = '';
	$dataRes7['header'] = '';
	$dataResTunjAll['header'] = '';
	$dataRes1['detail'] = '';
	$dataRes2['detail'] = '';
	$dataRes6['detail'] = '';
	$dataRes7['detail'] = '';
	$dataResTunjAll['detail'] = '';

	if ($param['komponen'] == 1) {
		$dataRes1['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totgapok'], 'totalkredit' => -1 * $param['totgapok'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
		$noUrut = 1;
		$dataRes1['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => $param['totgapok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$dataRes1['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => -1 * $param['totgapok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
		++$noUrut;
		$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes1['header']);

		if (!mysql_query($insHead)) {
			$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
		}

		if ($headErr == '') {
			$detailErr = '';

			foreach ($dataRes1['detail'] as $row) {
				$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

				if (!mysql_query($insDet)) {
					$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
					break;
				}

				if ($param['komponentunjjab'] == 2) {
					$dataRes2['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totunjjab'], 'totalkredit' => -1 * $param['totunjjab'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
					$noUrut = 1;
					$dataRes2['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => $param['totunjjab'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
					++$noUrut;
					$dataRes2['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => -1 * $param['totunjjab'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
					++$noUrut;
					$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

					if (!mysql_query($insHead)) {
						$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
					}

					if ($headErr == '') {
						$detailErr = '';

						foreach ($dataRes2['detail'] as $row) {
							$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

							if (!mysql_query($insDet)) {
								$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
								break;
							}

							if ($param['komponenjkk'] == 6) {
								$kodeJurnal = $group;
								$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
								$tmpKonter = fetchData($queryJ);
								$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
								$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
								$dataRes6['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totjkk'], 'totalkredit' => -1 * $param['totjkk'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
								$noUrut = 1;
								$dataRes6['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => $param['totjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
								++$noUrut;
								$dataRes6['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => -1 * $param['totjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
								++$noUrut;
								$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes6['header']);

								if (!mysql_query($insHead)) {
									$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
								}

								if ($headErr == '') {
									$detailErr = '';

									foreach ($dataRes6['detail'] as $row) {
										$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

										if (!mysql_query($insDet)) {
											$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
											break;
										}

										if ($param['komponenjkm'] == 7) {
											$kodeJurnal = $group;
											$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
											$tmpKonter = fetchData($queryJ);
											$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
											$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
											$dataRes7['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totjkm'], 'totalkredit' => -1 * $param['totjkm'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
											$noUrut = 1;
											$dataRes7['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => $param['totjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
											++$noUrut;
											$dataRes7['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => -1 * $param['totjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
											++$noUrut;
											$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes7['header']);

											if (!mysql_query($insHead)) {
												$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
											}

											if ($headErr == '') {
												$detailErr = '';

												foreach ($dataRes7['detail'] as $row) {
													$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

													if (!mysql_query($insDet)) {
														$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
														break;
													}

													if (($param['komponennatone'] == 29) || ($param['komponennatdua'] == 30) || ($param['komponennattiga'] == 32) || ($param['komponennatnol'] == 33) || ($param['komponengolsku'] == 51) || ($param['komponentunjharian'] == 21) || ($param['komponentunjlain'] == 22) || ($param['komponentunjdinas'] == 23)) {
														$kodeJurnal = $group;
														$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
														$tmpKonter = fetchData($queryJ);
														$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
														$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
														$dataResTunjAll['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totnatone'] + $param['totnatdua'] + $param['totnattiga'] + $param['totnatnol'] + $param['totgolsku'], 'totalkredit' => (-1 * $param['totnatone']) + $param['totnatdua'] + $param['totnattiga'] + $param['totnatnol'] + $param['totgolsku'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
														$noUrut = 1;
														$dataResTunjAll['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => $param['totnatone'] + $param['totnatdua'] + $param['totnattiga'] + $param['totnatnol'] + $param['totgolsku'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
														++$noUrut;
														$dataResTunjAll['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'ALK_GAJI_STAFF_HORO', 'jumlah' => (-1 * $param['totnatone']) + $param['totnatdua'] + $param['totnattiga'] + $param['totnatnol'] + $param['totgolsku'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_GAJI_STAFF_HORO', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
														++$noUrut;
														$insHead = insertQuery($dbname, 'keu_jurnalht', $dataResTunjAll['header']);

														if (!mysql_query($insHead)) {
															$headErr .= 'Insert Header BTL Error : ' . mysql_error() . "\n";
														}

														if ($headErr == '') {
															$detailErr = '';

															foreach ($dataResTunjAll['detail'] as $row) {
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
}

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = $param['periode'] . '-28';

if ($param['row'] == '1') {
	$str = 'delete from ' . $dbname . '.keu_jurnalht where kodejurnal in (\'KBNB0\',\'KBNB1\',\'KBNB2\',\'KBNB3\',\'KBNB4\',\'KBNB5\',' . "\r\n" . '             \'KBNL0\',\'KBNL1\',\'KBNL2\',\'KBNL3\',\'M6\',\'PKS01\',\'PKS02\',\'PNN01\',\'SIPL1\',\'VHCG0\',\'VHCG1\',\'VHCG2\',\'VHCG3\',\'VHCG4\',' . "\r\n" . '             \'VHCG5\',\'WSG0\',\'WSG1\',\'WSG2\',\'WSG3\',\'WSG4\',\'WSG5\') and tanggal=\'' . $tanggal . '\'' . "\r\n" . '    and nojurnal like \'%/' . $_SESSION['empl']['lokasitugas'] . '/%\'';
	mysql_query($str);
}

$str = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
$res = mysql_query($str);
$tip = '';

while ($bar = mysql_fetch_object($res)) {
	$tip = $bar->tipe;
}

$param['subbagian'] = str_replace(' ', '', $param['subbagian']);

if (($tip == 'PABRIK') && ($param['subbagian'] != '')) {
	prosesGajiPabrik();
}
else if ($param['tipeorganisasi'] == 'WORKSHOP') {
	prosesGajiWs();
}
else if ($param['tipeorganisasi'] == 'SIPIL') {
	prosesGajiSipil();
}
else {
	if (($param['tipeorganisasi'] == 'AFDELING') || ($param['tipeorganisasi'] == 'BIBITAN')) {
		prosesGajiAfdeling();
	}
	else if ($param['tipeorganisasi'] == 'TRAKSI') {
		prosesGajiTraksi();
	}
	else if ($tip == 'TRAKSI') {
		prosesGajiTraksi();
	}
	else if ($tip == 'WORKSHOP') {
		prosesGajiWs();
	}
	else {
		if (($tip == 'HOLDING') || ($tip == 'KANWIL')) {
			prosesGajiHORO();
		}
		else {
			prosesGajiKebun();
		}
	}
}

?>
