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

function prosesGajiROTRAKSI()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	global $periode;
	$periode = $param['periode'];
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$group = 'KROB0';
	}
	else if ($param['komponenlembur'] == 17) {
		$group = 'KROB11';
	}
	else if ($param['komponentunjpremi'] == 16) {
		$group = 'KROB10';
	}
	else if ($param['komponentunjkom'] == 63) {
		$group = 'KROB43';
	}
	else if ($param['komponentunjlok'] == 58) {
		$group = 'KROB38';
	}
	else if ($param['komponentunjprt'] == 59) {
		$group = 'KROB39';
	}
	else if ($param['komponentunjbbm'] == 61) {
		$group = 'KROB41';
	}
	else if ($param['komponentunjair'] == 65) {
		$group = 'KROB44';
	}
	else if ($param['komponentunjspart'] == 60) {
		$group = 'KROB40';
	}
	else if ($param['komponentunjharian'] == 21) {
		$group = 'KROB12';
	}
	else if ($param['komponentunjdinas'] == 23) {
		$group = 'KROB14';
	}
	else if ($param['komponentunjcuti'] == 12) {
		$group = 'KROB6';
	}
	else if ($param['komponentunjlistrik'] == 62) {
		$group = 'KROB42';
	}
	else if ($param['komponentunjjkk'] == 6) {
		$group = 'KROB4';
	}
	else if ($param['komponentunjjkm'] == 7) {
		$group = 'KROB5';
	}
	else if ($param['komponentunjbpjskes'] == 57) {
		$group = 'KROB37';
	}
	else if ($param['komponenpotjhtkar'] == 5) {
		$group = 'KROB45';
	}
	else if ($param['komponenpotjpkar'] == 9) {
		$group = 'KROB47';
	}
	else if ($param['komponenpotpph21'] == 24) {
		$group = 'KROB54';
	}
	else if ($param['komponenpotkoperasi'] == 25) {
		$group = 'KROB55';
	}
	else if ($param['komponenpotvop'] == 52) {
		$group = 'KROB58';
	}
	else if ($param['komponenpotmotor'] == 10) {
		$group = 'KROB48';
	}
	else if ($param['komponenpotlaptop'] == 11) {
		$group = 'KROB49';
	}
	else if ($param['komponenpotdenda'] == 64) {
		$group = 'KROB62';
	}
	else if ($param['komponenpotbpjskes'] == 8) {
		$group = 'KROB46';
	}
	else if ($param['komponenpotdendapanen'] == 26) {
		$group = 'KROB56';
	}
	else {
		$group = 'KROB99';
	}

	$nojurnal = '';
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB0\' limit 1';
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

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB0/' . $konter;
		$kodeJurnal = 'KROB0';
	}

	$nojurnal17 = '';

	if ($param['komponenlembur'] == 17) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB11\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet17 = '';
			$akunkredit17 = '';
			$bar = mysql_fetch_object($res);
			$akundebet17 = $bar->noakundebet;
			$akunkredit17 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB11\' ');
		$tmpKonter = fetchData($queryJ);
		$konter17 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal17 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB11/' . $konter17;
		$kodeJurnal17 = 'KROB11';
	}

	$nojurnal16 = '';

	if ($param['komponenlembur'] == 16) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB10\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet16 = '';
			$akunkredit16 = '';
			$bar = mysql_fetch_object($res);
			$akundebet16 = $bar->noakundebet;
			$akunkredit16 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB10\' ');
		$tmpKonter = fetchData($queryJ);
		$konter16 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal16 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB10/' . $konter16;
		$kodeJurnal16 = 'KROB10';
	}

	$nojurnal29 = '';

	if ($param['komponentunjkom'] == 63) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB43\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet29 = '';
			$akunkredit29 = '';
			$bar = mysql_fetch_object($res);
			$akundebet29 = $bar->noakundebet;
			$akunkredit29 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB43\' ');
		$tmpKonter = fetchData($queryJ);
		$konter29 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal29 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB43/' . $konter29;
		$kodeJurnal29 = 'KROB43';
	}

	$nojurnal14 = '';

	if ($param['komponentunjlok'] == 58) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB38\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet14 = '';
			$akunkredit14 = '';
			$bar = mysql_fetch_object($res);
			$akundebet14 = $bar->noakundebet;
			$akunkredit14 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB38\' ');
		$tmpKonter = fetchData($queryJ);
		$konter14 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal14 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB38/' . $konter14;
		$kodeJurnal14 = 'KROB38';
	}

	$nojurnal13 = '';

	if ($param['komponentunjprt'] == 59) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB39\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet13 = '';
			$akunkredit13 = '';
			$bar = mysql_fetch_object($res);
			$akundebet13 = $bar->noakundebet;
			$akunkredit13 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB39\' ');
		$tmpKonter = fetchData($queryJ);
		$konter13 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal13 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB39/' . $konter13;
		$kodeJurnal13 = 'KROB39';
	}

	$nojurnal61 = '';

	if ($param['komponentunjbbm'] == 61) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB41\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet61 = '';
			$akunkredit61 = '';
			$bar = mysql_fetch_object($res);
			$akundebet61 = $bar->noakundebet;
			$akunkredit61 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB41\' ');
		$tmpKonter = fetchData($queryJ);
		$konter61 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal61 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB41/' . $konter61;
		$kodeJurnal61 = 'KROB41';
	}

	$nojurnal65 = '';

	if ($param['komponentunjair'] == 65) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB44\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet65 = '';
			$akunkredit65 = '';
			$bar = mysql_fetch_object($res);
			$akundebet65 = $bar->noakundebet;
			$akunkredit65 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB44\' ');
		$tmpKonter = fetchData($queryJ);
		$konter65 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal65 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB44/' . $konter65;
		$kodeJurnal65 = 'KROB44';
	}

	$nojurnal60 = '';

	if ($param['komponentunjspart'] == 60) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB40\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet60 = '';
			$akunkredit60 = '';
			$bar = mysql_fetch_object($res);
			$akundebet60 = $bar->noakundebet;
			$akunkredit60 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB40\' ');
		$tmpKonter = fetchData($queryJ);
		$konter60 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal60 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB40/' . $konter60;
		$kodeJurnal60 = 'KROB40';
	}

	$nojurnal21 = '';

	if ($param['komponentunjharian'] == 21) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB12\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet21 = '';
			$akunkredit21 = '';
			$bar = mysql_fetch_object($res);
			$akundebet21 = $bar->noakundebet;
			$akunkredit21 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB12\' ');
		$tmpKonter = fetchData($queryJ);
		$konter21 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal21 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB12/' . $konter21;
		$kodeJurnal21 = 'KROB12';
	}

	$nojurnal23 = '';

	if ($param['komponentunjdinas'] == 23) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB14\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet23 = '';
			$akunkredit23 = '';
			$bar = mysql_fetch_object($res);
			$akundebet23 = $bar->noakundebet;
			$akunkredit23 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB14\' ');
		$tmpKonter = fetchData($queryJ);
		$konter23 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal23 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB14/' . $konter23;
		$kodeJurnal23 = 'KROB14';
	}

	$nojurnal12 = '';

	if ($param['komponentunjcuti'] == 12) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB6\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet12 = '';
			$akunkredit12 = '';
			$bar = mysql_fetch_object($res);
			$akundebet12 = $bar->noakundebet;
			$akunkredit12 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB6\' ');
		$tmpKonter = fetchData($queryJ);
		$konter12 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal12 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB6/' . $konter12;
		$kodeJurnal12 = 'KROB6';
	}

	$nojurnal62 = '';

	if ($param['komponentunjlistrik'] == 62) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB42\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet62 = '';
			$akunkredit62 = '';
			$bar = mysql_fetch_object($res);
			$akundebet62 = $bar->noakundebet;
			$akunkredit62 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB42\' ');
		$tmpKonter = fetchData($queryJ);
		$konter62 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal62 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB42/' . $konter62;
		$kodeJurnal62 = 'KROB42';
	}

	$nojurnal22 = '';

	if ($param['komponentunjlain'] == 22) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet22 = '';
			$akunkredit22 = '';
			$bar = mysql_fetch_object($res);
			$akundebet22 = $bar->noakundebet;
			$akunkredit22 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter22 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal22 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB0/' . $konter22;
		$kodeJurnal22 = 'KROB0';
	}

	$nojurnal54 = '';

	if ($param['komponentunjrapel'] == 54) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet54 = '';
			$akunkredit54 = '';
			$bar = mysql_fetch_object($res);
			$akundebet54 = $bar->noakundebet;
			$akunkredit54 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter54 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal54 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB0/' . $konter54;
		$kodeJurnal54 = 'KROB0';
	}

	$nojurnal6 = '';

	if ($param['komponentunjjkk'] == 6) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB4\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet6 = '';
			$akunkredit6 = '';
			$bar = mysql_fetch_object($res);
			$akundebet6 = $bar->noakundebet;
			$akunkredit6 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB4\' ');
		$tmpKonter = fetchData($queryJ);
		$konter6 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal6 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB4/' . $konter6;
		$kodeJurnal6 = 'KROB4';
	}

	$nojurnal7 = '';

	if ($param['komponentunjjkm'] == 7) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB5\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet7 = '';
			$akunkredit7 = '';
			$bar = mysql_fetch_object($res);
			$akundebet7 = $bar->noakundebet;
			$akunkredit7 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB5\' ');
		$tmpKonter = fetchData($queryJ);
		$konter7 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal7 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB5/' . $konter7;
		$kodeJurnal7 = 'KROB5';
	}

	$nojurnal57 = '';

	if ($param['komponentunjbpjskes'] == 57) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB37\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet57 = '';
			$akunkredit57 = '';
			$bar = mysql_fetch_object($res);
			$akundebet57 = $bar->noakundebet;
			$akunkredit57 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB37\' ');
		$tmpKonter = fetchData($queryJ);
		$konter57 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal57 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB37/' . $konter57;
		$kodeJurnal57 = 'KROB37';
	}

	$nojurnal5 = '';

	if ($param['komponenpotjhtkar'] == 5) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB45\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet5 = '';
			$akunkredit5 = '';
			$bar = mysql_fetch_object($res);
			$akundebet5 = $bar->noakundebet;
			$akunkredit5 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB45\' ');
		$tmpKonter = fetchData($queryJ);
		$konter5 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal5 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB45/' . $konter5;
		$kodeJurnal5 = 'KROB45';
	}

	$nojurnal9 = '';

	if ($param['komponenpotjpkar'] == 9) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB47\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet9 = '';
			$akunkredit9 = '';
			$bar = mysql_fetch_object($res);
			$akundebet9 = $bar->noakundebet;
			$akunkredit9 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB47\' ');
		$tmpKonter = fetchData($queryJ);
		$konter9 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal9 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB47/' . $konter9;
		$kodeJurnal9 = 'KROB47';
	}

	$nojurnal24 = '';

	if ($param['komponenpotpph21'] == 24) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB54\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet24 = '';
			$akunkredit24 = '';
			$bar = mysql_fetch_object($res);
			$akundebet24 = $bar->noakundebet;
			$akunkredit24 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB54\' ');
		$tmpKonter = fetchData($queryJ);
		$konter24 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal24 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB54/' . $konter24;
		$kodeJurnal24 = 'KROB54';
	}

	$nojurnal25 = '';

	if ($param['komponenpotkoperasi'] == 25) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB55\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet25 = '';
			$akunkredit25 = '';
			$bar = mysql_fetch_object($res);
			$akundebet25 = $bar->noakundebet;
			$akunkredit25 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB55\' ');
		$tmpKonter = fetchData($queryJ);
		$konter25 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal25 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB55/' . $konter25;
		$kodeJurnal25 = 'KROB55';
	}

	$nojurnal52 = '';

	if ($param['komponenpotvop'] == 52) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB58\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet52 = '';
			$akunkredit52 = '';
			$bar = mysql_fetch_object($res);
			$akundebet52 = $bar->noakundebet;
			$akunkredit52 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB58\' ');
		$tmpKonter = fetchData($queryJ);
		$konter52 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal52 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB58/' . $konter52;
		$kodeJurnal52 = 'KROB58';
	}

	$nojurnal10 = '';

	if ($param['komponenpotmotor'] == 10) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB48\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet10 = '';
			$akunkredit10 = '';
			$bar = mysql_fetch_object($res);
			$akundebet10 = $bar->noakundebet;
			$akunkredit10 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB48\' ');
		$tmpKonter = fetchData($queryJ);
		$konter10 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal10 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB48/' . $konter10;
		$kodeJurnal10 = 'KROB48';
	}

	$nojurnal11 = '';

	if ($param['komponenpotlaptop'] == 11) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB49\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet11 = '';
			$akunkredit11 = '';
			$bar = mysql_fetch_object($res);
			$akundebet11 = $bar->noakundebet;
			$akunkredit11 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB49\' ');
		$tmpKonter = fetchData($queryJ);
		$konter11 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal11 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB49/' . $konter11;
		$kodeJurnal11 = 'KROB49';
	}

	$nojurnal64 = '';

	if ($param['komponenpotdenda'] == 64) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB62\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet64 = '';
			$akunkredit64 = '';
			$bar = mysql_fetch_object($res);
			$akundebet64 = $bar->noakundebet;
			$akunkredit64 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB62\' ');
		$tmpKonter = fetchData($queryJ);
		$konter64 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal64 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB62/' . $konter64;
		$kodeJurnal64 = 'KROB62';
	}

	$nojurnal8 = '';

	if ($param['komponenpotbpjskes'] == 8) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KROB46\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet8 = '';
			$akunkredit8 = '';
			$bar = mysql_fetch_object($res);
			$akundebet8 = $bar->noakundebet;
			$akunkredit8 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KROB46\' ');
		$tmpKonter = fetchData($queryJ);
		$konter8 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal8 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KROB46/' . $konter8;
		$kodeJurnal8 = 'KROB46';
	}

	$dataRes['header'] = '';
	$dataRes2['header'] = '';
	$dataRes29['header'] = '';
	$dataRes14['header'] = '';
	$dataRes13['header'] = '';
	$dataRes61['header'] = '';
	$dataRes65['header'] = '';
	$dataRes60['header'] = '';
	$dataRes21['header'] = '';
	$dataRes23['header'] = '';
	$dataRes12['header'] = '';
	$dataRes62['header'] = '';
	$dataRes22['header'] = '';
	$dataRes54['header'] = '';
	$dataRes6['header'] = '';
	$dataRes7['header'] = '';
	$dataRes57['header'] = '';
	$dataRes66['header'] = '';
	$dataRes5['header'] = '';
	$dataRes9['header'] = '';
	$dataRes24['header'] = '';
	$dataRes25['header'] = '';
	$dataRes52['header'] = '';
	$dataRes10['header'] = '';
	$dataRes11['header'] = '';
	$dataRes64['header'] = '';
	$dataRes8['header'] = '';
	$dataResTunjAll['header'] = '';
	$dataRes['detail'] = '';
	$dataRes2['detail'] = '';
	$dataRes29['detail'] = '';
	$dataRes14['detail'] = '';
	$dataRes13['detail'] = '';
	$dataRes61['detail'] = '';
	$dataRes65['detail'] = '';
	$dataRes60['detail'] = '';
	$dataRes21['detail'] = '';
	$dataRes23['detail'] = '';
	$dataRes12['detail'] = '';
	$dataRes62['detail'] = '';
	$dataRes22['detail'] = '';
	$dataRes54['detail'] = '';
	$dataRes6['detail'] = '';
	$dataRes7['detail'] = '';
	$dataRes57['detail'] = '';
	$dataRes66['detail'] = '';
	$dataRes5['detail'] = '';
	$dataRes9['detail'] = '';
	$dataRes24['detail'] = '';
	$dataRes25['detail'] = '';
	$dataRes52['detail'] = '';
	$dataRes10['detail'] = '';
	$dataRes11['detail'] = '';
	$dataRes64['detail'] = '';
	$dataRes8['detail'] = '';
	$dataResTunjAll['detail'] = '';
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => 'KROB0', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'totalkredit' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	if (($dataRes['header']['totaldebet'] == '') || ($dataRes['header']['totalkredit'] == '') || ($dataRes['header']['totaldebet'] == 0) || ($dataRes['header']['totalkredit'] == 0)) {
	}
	else {
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

	$dataRes2['header'] = array('nojurnal' => $nojurnal17, 'kodejurnal' => 'KROB11', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totlembur'], 'totalkredit' => -1 * $param['totlembur'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUruttunjab = 1;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akundebet17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akunkredit17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	if (($dataRes2['header']['totaldebet'] == '') || ($dataRes2['header']['totalkredit'] == '') || ($dataRes2['header']['totaldebet'] == 0) || ($dataRes2['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

		if (!mysql_query($insHead2)) {
			$headErr2 .= 'Insert Header BTL 67 Error : ' . mysql_error() . "\n";
		}

		if ($headErr2 == '') {
			$detailErr2 = '';

			foreach ($dataRes2['detail'] as $row) {
				$insDet2 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr2 .= 'Insert Detail Error 17: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr2 == '') {
				$updJurnal17 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter17), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal17 . '\'');

				if (!mysql_query($updJurnal17)) {
					echo 'Update Kode Jurnal 17 Error : ' . mysql_error() . "\n";
					$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

					if (!mysql_query($RBDet17)) {
						echo 'Rollback Delete Header BTL 17 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr2;
				$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

				if (!mysql_query($RBDet17)) {
					echo 'Rollback Delete Header 17 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr2;
			exit();
		}
	}

	$dataRes29['header'] = array('nojurnal' => $nojurnal29, 'kodejurnal' => 'KROB43', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjkom'], 'totalkredit' => -1 * $param['tottunjkom'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_KOMUNIKASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut29 = 1;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akundebet29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akunkredit29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	if (($dataRes29['header']['totaldebet'] == '') || ($dataRes29['header']['totalkredit'] == '') || ($dataRes29['header']['totaldebet'] == 0) || ($dataRes29['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead29 = insertQuery($dbname, 'keu_jurnalht', $dataRes29['header']);

		if (!mysql_query($insHead29)) {
			$headErr29 .= 'Insert Header BTL29 Error : ' . mysql_error() . "\n";
		}

		if ($headErr29 == '') {
			$detailErr29 = '';

			foreach ($dataRes29['detail'] as $row) {
				$insDet29 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr29 .= 'Insert Detail Error 29: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr29 == '') {
				$updJurnal29 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter29), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal29 . '\'');

				if (!mysql_query($updJurnal29)) {
					echo 'Update Kode Jurnal 29 Error : ' . mysql_error() . "\n";
					$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

					if (!mysql_query($RBDet29)) {
						echo 'Rollback Delete Header BTL 29 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr29;
				$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

				if (!mysql_query($RBDet29)) {
					echo 'Rollback Delete Header 29 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr29;
			exit();
		}
	}

	$dataRes14['header'] = array('nojurnal' => $nojurnal14, 'kodejurnal' => 'KROB38', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlok'], 'totalkredit' => -1 * $param['tottunjlok'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut14 = 1;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akundebet14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akunkredit14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	if (($dataRes14['header']['totaldebet'] == '') || ($dataRes14['header']['totalkredit'] == '') || ($dataRes14['header']['totaldebet'] == 0) || ($dataRes14['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead14 = insertQuery($dbname, 'keu_jurnalht', $dataRes14['header']);

		if (!mysql_query($insHead14)) {
			$headErr14 .= 'Insert Header BTL 14 Error : ' . mysql_error() . "\n";
		}

		if ($headErr14 == '') {
			$detailErr14 = '';

			foreach ($dataRes14['detail'] as $row) {
				$insDet14 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr14 .= 'Insert Detail Error 14: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr14 == '') {
				$updJurnal14 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter14), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal14 . '\'');

				if (!mysql_query($updJurnal14)) {
					echo 'Update Kode Jurnal 14 Error : ' . mysql_error() . "\n";
					$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

					if (!mysql_query($RBDet14)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr14;
				$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

				if (!mysql_query($RBDet14)) {
					echo 'Rollback Delete Header 14 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr14;
			exit();
		}
	}

	$dataRes13['header'] = array('nojurnal' => $nojurnal13, 'kodejurnal' => 'KROB39', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjprt'], 'totalkredit' => -1 * $param['tottunjprt'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut13 = 1;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akundebet13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akunkredit13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	if (($dataRes13['header']['totaldebet'] == '') || ($dataRes13['header']['totalkredit'] == '') || ($dataRes13['header']['totaldebet'] == 0) || ($dataRes13['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead13 = insertQuery($dbname, 'keu_jurnalht', $dataRes13['header']);

		if (!mysql_query($insHead13)) {
			$headErr13 .= 'Insert Header BTL 13 Error : ' . mysql_error() . "\n";
		}

		if ($headErr13 == '') {
			$detailErr13 = '';

			foreach ($dataRes13['detail'] as $row) {
				$insDet13 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr13 .= 'Insert Detail Error 13: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr13 == '') {
				$updJurnal13 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter13), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal13 . '\'');

				if (!mysql_query($updJurnal13)) {
					echo 'Update Kode Jurnal 13 Error : ' . mysql_error() . "\n";
					$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

					if (!mysql_query($RBDet13)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr13;
				$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

				if (!mysql_query($RBDet13)) {
					echo 'Rollback Delete Header 13 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr13;
			exit();
		}
	}

	$dataRes61['header'] = array('nojurnal' => $nojurnal61, 'kodejurnal' => 'KROB41', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbbm'], 'totalkredit' => -1 * $param['tottunjbbm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut61 = 1;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akundebet61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akunkredit61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	if (($dataRes61['header']['totaldebet'] == '') || ($dataRes61['header']['totalkredit'] == '') || ($dataRes61['header']['totaldebet'] == 0) || ($dataRes61['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead61 = insertQuery($dbname, 'keu_jurnalht', $dataRes61['header']);

		if (!mysql_query($insHead61)) {
			$headErr61 .= 'Insert Header BTL 61 Error : ' . mysql_error() . "\n";
		}

		if ($headErr61 == '') {
			$detailErr61 = '';

			foreach ($dataRes61['detail'] as $row) {
				$insDet61 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr61 .= 'Insert Detail Error 61: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr61 == '') {
				$updJurnal61 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter61), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal61 . '\'');

				if (!mysql_query($updJurnal61)) {
					echo 'Update Kode Jurnal 61 Error : ' . mysql_error() . "\n";
					$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

					if (!mysql_query($RBDet61)) {
						echo 'Rollback Delete Header BTL 61 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr61;
				$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

				if (!mysql_query($RBDet61)) {
					echo 'Rollback Delete Header 61  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr61;
			exit();
		}
	}

	$dataRes65['header'] = array('nojurnal' => $nojurnal65, 'kodejurnal' => 'KROB44', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjair'], 'totalkredit' => -1 * $param['tottunjair'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut65 = 1;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akundebet65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akunkredit65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	if (($dataRes65['header']['totaldebet'] == '') || ($dataRes65['header']['totalkredit'] == '') || ($dataRes65['header']['totaldebet'] == 0) || ($dataRes65['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead65 = insertQuery($dbname, 'keu_jurnalht', $dataRes65['header']);

		if (!mysql_query($insHead65)) {
			$headErr65 .= 'Insert Header BTL 65 Error : ' . mysql_error() . "\n";
		}

		if ($headErr65 == '') {
			$detailErr65 = '';

			foreach ($dataRes65['detail'] as $row) {
				$insDet65 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr65 .= 'Insert Detail Error 65: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr65 == '') {
				$updJurnal65 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter65), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal65 . '\'');

				if (!mysql_query($updJurnal65)) {
					echo 'Update Kode Jurnal 65 Error : ' . mysql_error() . "\n";
					$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

					if (!mysql_query($RBDet65)) {
						echo 'Rollback Delete Header BTL 65 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr65;
				$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

				if (!mysql_query($RBDet65)) {
					echo 'Rollback Delete Header 65  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr65;
			exit();
		}
	}

	$dataRes60['header'] = array('nojurnal' => $nojurnal60, 'kodejurnal' => 'KROB40', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjspart'], 'totalkredit' => -1 * $param['tottunjspart'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut60 = 1;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akundebet60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akunkredit60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	if (($dataRes60['header']['totaldebet'] == '') || ($dataRes60['header']['totalkredit'] == '') || ($dataRes60['header']['totaldebet'] == 0) || ($dataRes60['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead60 = insertQuery($dbname, 'keu_jurnalht', $dataRes60['header']);

		if (!mysql_query($insHead60)) {
			$headErr60 .= 'Insert Header BTL 60 Error : ' . mysql_error() . "\n";
		}

		if ($headErr60 == '') {
			$detailErr60 = '';

			foreach ($dataRes60['detail'] as $row) {
				$insDet60 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr60 .= 'Insert Detail Error 60: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr60 == '') {
				$updJurnal60 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter60), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal60 . '\'');

				if (!mysql_query($updJurnal60)) {
					echo 'Update Kode Jurnal 60 Error : ' . mysql_error() . "\n";
					$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

					if (!mysql_query($RBDet60)) {
						echo 'Rollback Delete Header BTL 60 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr60;
				$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

				if (!mysql_query($RBDet60)) {
					echo 'Rollback Delete Header 60  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr60;
			exit();
		}
	}

	$dataRes21['header'] = array('nojurnal' => $nojurnal21, 'kodejurnal' => 'KROB12', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjharian'], 'totalkredit' => -1 * $param['tottunjharian'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut21 = 1;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akundebet21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akunkredit21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	if (($dataRes21['header']['totaldebet'] == '') || ($dataRes21['header']['totalkredit'] == '') || ($dataRes21['header']['totaldebet'] == 0) || ($dataRes21['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead21 = insertQuery($dbname, 'keu_jurnalht', $dataRes21['header']);

		if (!mysql_query($insHead21)) {
			$headErr21 .= 'Insert Header BTL 21 Error : ' . mysql_error() . "\n";
		}

		if ($headErr21 == '') {
			$detailErr21 = '';

			foreach ($dataRes21['detail'] as $row) {
				$insDet21 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr21 .= 'Insert Detail Error 21: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr21 == '') {
				$updJurnal21 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter21), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal21 . '\'');

				if (!mysql_query($updJurnal21)) {
					echo 'Update Kode Jurnal 21 Error : ' . mysql_error() . "\n";
					$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

					if (!mysql_query($RBDet21)) {
						echo 'Rollback Delete Header BTL 21 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr21;
				$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

				if (!mysql_query($RBDet21)) {
					echo 'Rollback Delete Header 21  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr21;
			exit();
		}
	}

	$dataRes23['header'] = array('nojurnal' => $nojurnal23, 'kodejurnal' => 'KROB14', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjdinas'], 'totalkredit' => -1 * $param['tottunjdinas'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut23 = 1;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akundebet23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akunkredit23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	if (($dataRes23['header']['totaldebet'] == '') || ($dataRes23['header']['totalkredit'] == '') || ($dataRes23['header']['totaldebet'] == 0) || ($dataRes23['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead23 = insertQuery($dbname, 'keu_jurnalht', $dataRes23['header']);

		if (!mysql_query($insHead23)) {
			$headErr23 .= 'Insert Header BTL 23 Error : ' . mysql_error() . "\n";
		}

		if ($headErr23 == '') {
			$detailErr23 = '';

			foreach ($dataRes23['detail'] as $row) {
				$insDet23 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr23 .= 'Insert Detail Error 23: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr23 == '') {
				$updJurnal23 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter23), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal23 . '\'');

				if (!mysql_query($updJurnal23)) {
					echo 'Update Kode Jurnal 23 Error : ' . mysql_error() . "\n";
					$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

					if (!mysql_query($RBDet23)) {
						echo 'Rollback Delete Header BTL 23 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr23;
				$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

				if (!mysql_query($RBDet23)) {
					echo 'Rollback Delete Header 23  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr23;
			exit();
		}
	}

	$dataRes12['header'] = array('nojurnal' => $nojurnal12, 'kodejurnal' => 'KROB6', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjcuti'], 'totalkredit' => -1 * $param['tottunjcuti'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut12 = 1;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akundebet12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akunkredit12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	if (($dataRes12['header']['totaldebet'] == '') || ($dataRes12['header']['totalkredit'] == '') || ($dataRes12['header']['totaldebet'] == 0) || ($dataRes12['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead12 = insertQuery($dbname, 'keu_jurnalht', $dataRes12['header']);

		if (!mysql_query($insHead12)) {
			$headErr12 .= 'Insert Header BTL 12 Error : ' . mysql_error() . "\n";
		}

		if ($headErr12 == '') {
			$detailErr12 = '';

			foreach ($dataRes12['detail'] as $row) {
				$insDet12 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr12 .= 'Insert Detail Error 12: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr12 == '') {
				$updJurnal12 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter12), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal12 . '\'');

				if (!mysql_query($updJurnal12)) {
					echo 'Update Kode Jurnal 12 Error : ' . mysql_error() . "\n";
					$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

					if (!mysql_query($RBDet12)) {
						echo 'Rollback Delete Header BTL 12 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr12;
				$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

				if (!mysql_query($RBDet12)) {
					echo 'Rollback Delete Header 12  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr12;
			exit();
		}
	}

	$dataRes62['header'] = array('nojurnal' => $nojurnal62, 'kodejurnal' => 'KROB42', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlistrik'], 'totalkredit' => -1 * $param['tottunjlistrik'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut62 = 1;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akundebet62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akunkredit62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	if (($dataRes62['header']['totaldebet'] == '') || ($dataRes62['header']['totalkredit'] == '') || ($dataRes62['header']['totaldebet'] == 0) || ($dataRes62['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead62 = insertQuery($dbname, 'keu_jurnalht', $dataRes62['header']);

		if (!mysql_query($insHead62)) {
			$headErr62 .= 'Insert Header BTL 62 Error : ' . mysql_error() . "\n";
		}

		if ($headErr62 == '') {
			$detailErr62 = '';

			foreach ($dataRes62['detail'] as $row) {
				$insDet62 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr62 .= 'Insert Detail Error 62: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr62 == '') {
				$updJurnal62 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter62), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal62 . '\'');

				if (!mysql_query($updJurnal62)) {
					echo 'Update Kode Jurnal 62 Error : ' . mysql_error() . "\n";
					$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

					if (!mysql_query($RBDet62)) {
						echo 'Rollback Delete Header BTL 62 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr62;
				$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

				if (!mysql_query($RBDet62)) {
					echo 'Rollback Delete Header 62  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr62;
			exit();
		}
	}

	$dataRes6['header'] = array('nojurnal' => $nojurnal6, 'kodejurnal' => 'KROB4', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkk'], 'totalkredit' => -1 * $param['tottunjjkk'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut6 = 1;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akundebet6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akunkredit6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	if (($dataRes6['header']['totaldebet'] == '') || ($dataRes6['header']['totalkredit'] == '') || ($dataRes6['header']['totaldebet'] == 0) || ($dataRes6['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead6 = insertQuery($dbname, 'keu_jurnalht', $dataRes6['header']);

		if (!mysql_query($insHead6)) {
			$headErr6 .= 'Insert Header BTL 6 Error : ' . mysql_error() . "\n";
		}

		if ($headErr6 == '') {
			$detailErr6 = '';

			foreach ($dataRes6['detail'] as $row) {
				$insDet6 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr6 .= 'Insert Detail Error 6: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr6 == '') {
				$updJurnal6 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter6), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal6 . '\'');

				if (!mysql_query($updJurnal6)) {
					echo 'Update Kode Jurnal 6 Error : ' . mysql_error() . "\n";
					$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

					if (!mysql_query($RBDet6)) {
						echo 'Rollback Delete Header BTL 6 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr6;
				$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

				if (!mysql_query($RBDet6)) {
					echo 'Rollback Delete Header 6  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr6;
			exit();
		}
	}

	$dataRes7['header'] = array('nojurnal' => $nojurnal7, 'kodejurnal' => 'KROB5', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkm'], 'totalkredit' => -1 * $param['tottunjjkm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut7 = 1;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akundebet7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akunkredit7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	if (($dataRes7['header']['totaldebet'] == '') || ($dataRes7['header']['totalkredit'] == '') || ($dataRes7['header']['totaldebet'] == 0) || ($dataRes7['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead7 = insertQuery($dbname, 'keu_jurnalht', $dataRes7['header']);

		if (!mysql_query($insHead7)) {
			$headErr7 .= 'Insert Header BTL 7 Error : ' . mysql_error() . "\n";
		}

		if ($headErr7 == '') {
			$detailErr7 = '';

			foreach ($dataRes7['detail'] as $row) {
				$insDet7 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr7 .= 'Insert Detail Error 7: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr7 == '') {
				$updJurnal7 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter7), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal7 . '\'');

				if (!mysql_query($updJurnal7)) {
					echo 'Update Kode Jurnal 7 Error : ' . mysql_error() . "\n";
					$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

					if (!mysql_query($RBDet7)) {
						echo 'Rollback Delete Header BTL 7 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr7;
				$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

				if (!mysql_query($RBDet7)) {
					echo 'Rollback Delete Header 7  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr7;
			exit();
		}
	}

	$dataRes57['header'] = array('nojurnal' => $nojurnal57, 'kodejurnal' => 'KROB37', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbpjskes'], 'totalkredit' => -1 * $param['tottunjbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut57 = 1;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akundebet57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akunkredit57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	if (($dataRes57['header']['totaldebet'] == '') || ($dataRes57['header']['totalkredit'] == '') || ($dataRes57['header']['totaldebet'] == 0) || ($dataRes57['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead57 = insertQuery($dbname, 'keu_jurnalht', $dataRes57['header']);

		if (!mysql_query($insHead57)) {
			$headErr57 .= 'Insert Header BTL 57 Error : ' . mysql_error() . "\n";
		}

		if ($headErr57 == '') {
			$detailErr57 = '';

			foreach ($dataRes57['detail'] as $row) {
				$insDet57 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr57 .= 'Insert Detail Error 57: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr57 == '') {
				$updJurnal57 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter57), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal57 . '\'');

				if (!mysql_query($updJurnal57)) {
					echo 'Update Kode Jurnal 57 Error : ' . mysql_error() . "\n";
					$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

					if (!mysql_query($RBDet57)) {
						echo 'Rollback Delete Header BTL 57 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr57;
				$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

				if (!mysql_query($RBDet57)) {
					echo 'Rollback Delete Header 57  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr57;
			exit();
		}
	}

	$dataRes5['header'] = array('nojurnal' => $nojurnal5, 'kodejurnal' => 'KROB45', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjhtkar'], 'totalkredit' => -1 * $param['totpotjhtkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut5 = 1;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akundebet5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akunkredit5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	if (($dataRes5['header']['totaldebet'] == '') || ($dataRes5['header']['totalkredit'] == '') || ($dataRes5['header']['totaldebet'] == 0) || ($dataRes5['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead5 = insertQuery($dbname, 'keu_jurnalht', $dataRes5['header']);

		if (!mysql_query($insHead5)) {
			$headErr5 .= 'Insert Header BTL 5 Error : ' . mysql_error() . "\n";
		}

		if ($headErr5 == '') {
			$detailErr5 = '';

			foreach ($dataRes5['detail'] as $row) {
				$insDet5 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr5 .= 'Insert Detail Error 5: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr5 == '') {
				$updJurnal5 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter5), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal5 . '\'');

				if (!mysql_query($updJurnal5)) {
					echo 'Update Kode Jurnal 5 Error : ' . mysql_error() . "\n";
					$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

					if (!mysql_query($RBDet5)) {
						echo 'Rollback Delete Header BTL 5 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr5;
				$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

				if (!mysql_query($RBDet5)) {
					echo 'Rollback Delete Header 5  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr5;
			exit();
		}
	}

	$dataRes9['header'] = array('nojurnal' => $nojurnal9, 'kodejurnal' => 'KROB47', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjpkar'], 'totalkredit' => -1 * $param['totpotjpkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut9 = 1;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akundebet9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akunkredit9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	if (($dataRes9['header']['totaldebet'] == '') || ($dataRes9['header']['totalkredit'] == '') || ($dataRes9['header']['totaldebet'] == 0) || ($dataRes9['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead9 = insertQuery($dbname, 'keu_jurnalht', $dataRes9['header']);

		if (!mysql_query($insHead9)) {
			$headErr9 .= 'Insert Header BTL 9 Error : ' . mysql_error() . "\n";
		}

		if ($headErr9 == '') {
			$detailErr9 = '';

			foreach ($dataRes9['detail'] as $row) {
				$insDet9 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr9 .= 'Insert Detail Error 9: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr9 == '') {
				$updJurnal9 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter9), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal9 . '\'');

				if (!mysql_query($updJurnal9)) {
					echo 'Update Kode Jurnal 9 Error : ' . mysql_error() . "\n";
					$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

					if (!mysql_query($RBDet9)) {
						echo 'Rollback Delete Header BTL 9 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr9;
				$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

				if (!mysql_query($RBDet9)) {
					echo 'Rollback Delete Header 9  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr9;
			exit();
		}
	}

	$dataRes24['header'] = array('nojurnal' => $nojurnal24, 'kodejurnal' => 'KROB54', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotpph21'], 'totalkredit' => -1 * $param['totpotpph21'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut24 = 1;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akundebet24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akunkredit24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	if (($dataRes24['header']['totaldebet'] == '') || ($dataRes24['header']['totalkredit'] == '') || ($dataRes24['header']['totaldebet'] == 0) || ($dataRes24['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead24 = insertQuery($dbname, 'keu_jurnalht', $dataRes24['header']);

		if (!mysql_query($insHead24)) {
			$headErr24 .= 'Insert Header BTL 24 Error : ' . mysql_error() . "\n";
		}

		if ($headErr24 == '') {
			$detailErr24 = '';

			foreach ($dataRes24['detail'] as $row) {
				$insDet24 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr24 .= 'Insert Detail Error 24: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr24 == '') {
				$updJurnal24 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter24), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal24 . '\'');

				if (!mysql_query($updJurnal24)) {
					echo 'Update Kode Jurnal 24 Error : ' . mysql_error() . "\n";
					$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

					if (!mysql_query($RBDet24)) {
						echo 'Rollback Delete Header BTL 24 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr24;
				$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

				if (!mysql_query($RBDet24)) {
					echo 'Rollback Delete Header 24  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr24;
			exit();
		}
	}

	$dataRes25['header'] = array('nojurnal' => $nojurnal25, 'kodejurnal' => 'KROB55', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotkoperasi'], 'totalkredit' => -1 * $param['totpotkoperasi'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut25 = 1;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akundebet25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akunkredit25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	if (($dataRes25['header']['totaldebet'] == '') || ($dataRes25['header']['totalkredit'] == '') || ($dataRes25['header']['totaldebet'] == 0) || ($dataRes25['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead25 = insertQuery($dbname, 'keu_jurnalht', $dataRes25['header']);

		if (!mysql_query($insHead25)) {
			$headErr25 .= 'Insert Header BTL 25 Error : ' . mysql_error() . "\n";
		}

		if ($headErr25 == '') {
			$detailErr25 = '';

			foreach ($dataRes25['detail'] as $row) {
				$insDet25 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr25 .= 'Insert Detail Error 25: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr25 == '') {
				$updJurnal25 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter25), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal25 . '\'');

				if (!mysql_query($updJurnal25)) {
					echo 'Update Kode Jurnal 25 Error : ' . mysql_error() . "\n";
					$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

					if (!mysql_query($RBDet25)) {
						echo 'Rollback Delete Header BTL 25 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr25;
				$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

				if (!mysql_query($RBDet25)) {
					echo 'Rollback Delete Header 25  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr25;
			exit();
		}
	}

	$dataRes52['header'] = array('nojurnal' => $nojurnal52, 'kodejurnal' => 'KROB58', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotvop'], 'totalkredit' => -1 * $param['totpotvop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut52 = 1;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akundebet52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akunkredit52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	if (($dataRes52['header']['totaldebet'] == '') || ($dataRes52['header']['totalkredit'] == '') || ($dataRes52['header']['totaldebet'] == 0) || ($dataRes52['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead52 = insertQuery($dbname, 'keu_jurnalht', $dataRes52['header']);

		if (!mysql_query($insHead52)) {
			$headErr52 .= 'Insert Header BTL 52 Error : ' . mysql_error() . "\n";
		}

		if ($headErr52 == '') {
			$detailErr52 = '';

			foreach ($dataRes52['detail'] as $row) {
				$insDet52 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr52 .= 'Insert Detail Error 52: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr52 == '') {
				$updJurnal52 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter52), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal52 . '\'');

				if (!mysql_query($updJurnal52)) {
					echo 'Update Kode Jurnal 52 Error : ' . mysql_error() . "\n";
					$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

					if (!mysql_query($RBDet52)) {
						echo 'Rollback Delete Header BTL 52 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr52;
				$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

				if (!mysql_query($RBDet52)) {
					echo 'Rollback Delete Header 52  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr52;
			exit();
		}
	}

	$dataRes10['header'] = array('nojurnal' => $nojurnal10, 'kodejurnal' => 'KROB48', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotmotor'], 'totalkredit' => -1 * $param['totpotmotor'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut10 = 1;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akundebet10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akunkredit10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	if (($dataRes10['header']['totaldebet'] == '') || ($dataRes10['header']['totalkredit'] == '') || ($dataRes10['header']['totaldebet'] == 0) || ($dataRes10['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead10 = insertQuery($dbname, 'keu_jurnalht', $dataRes10['header']);

		if (!mysql_query($insHead10)) {
			$headErr10 .= 'Insert Header BTL 10 Error : ' . mysql_error() . "\n";
		}

		if ($headErr10 == '') {
			$detailErr10 = '';

			foreach ($dataRes10['detail'] as $row) {
				$insDet10 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr10 .= 'Insert Detail Error 10: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr10 == '') {
				$updJurnal10 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter10), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal10 . '\'');

				if (!mysql_query($updJurnal10)) {
					echo 'Update Kode Jurnal 10 Error : ' . mysql_error() . "\n";
					$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

					if (!mysql_query($RBDet10)) {
						echo 'Rollback Delete Header BTL 10 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr10;
				$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

				if (!mysql_query($RBDet10)) {
					echo 'Rollback Delete Header 10  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr10;
			exit();
		}
	}

	$dataRes11['header'] = array('nojurnal' => $nojurnal11, 'kodejurnal' => 'KROB49', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotlaptop'], 'totalkredit' => -1 * $param['totpotlaptop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut11 = 1;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akundebet11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akunkredit11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	if (($dataRes11['header']['totaldebet'] == '') || ($dataRes11['header']['totalkredit'] == '') || ($dataRes11['header']['totaldebet'] == 0) || ($dataRes11['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead11 = insertQuery($dbname, 'keu_jurnalht', $dataRes11['header']);

		if (!mysql_query($insHead11)) {
			$headErr11 .= 'Insert Header BTL 11 Error : ' . mysql_error() . "\n";
		}

		if ($headErr11 == '') {
			$detailErr11 = '';

			foreach ($dataRes11['detail'] as $row) {
				$insDet11 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr11 .= 'Insert Detail Error 11: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr11 == '') {
				$updJurnal11 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter11), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal11 . '\'');

				if (!mysql_query($updJurnal11)) {
					echo 'Update Kode Jurnal 11 Error : ' . mysql_error() . "\n";
					$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

					if (!mysql_query($RBDet11)) {
						echo 'Rollback Delete Header BTL 11 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr11;
				$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

				if (!mysql_query($RBDet11)) {
					echo 'Rollback Delete Header 11  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr11;
			exit();
		}
	}

	$dataRes64['header'] = array('nojurnal' => $nojurnal64, 'kodejurnal' => 'KROB62', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotdenda'], 'totalkredit' => -1 * $param['totpotdenda'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut64 = 1;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akundebet64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akunkredit64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	if (($dataRes64['header']['totaldebet'] == '') || ($dataRes64['header']['totalkredit'] == '') || ($dataRes64['header']['totaldebet'] == 0) || ($dataRes64['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead64 = insertQuery($dbname, 'keu_jurnalht', $dataRes64['header']);

		if (!mysql_query($insHead64)) {
			$headErr64 .= 'Insert Header BTL 64 Error : ' . mysql_error() . "\n";
		}

		if ($headErr64 == '') {
			$detailErr64 = '';

			foreach ($dataRes64['detail'] as $row) {
				$insDet64 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr64 .= 'Insert Detail Error 64: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr64 == '') {
				$updJurnal64 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter64), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal64 . '\'');

				if (!mysql_query($updJurnal64)) {
					echo 'Update Kode Jurnal 64 Error : ' . mysql_error() . "\n";
					$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

					if (!mysql_query($RBDet64)) {
						echo 'Rollback Delete Header BTL 64 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr64;
				$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

				if (!mysql_query($RBDet64)) {
					echo 'Rollback Delete Header 64  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr64;
			exit();
		}
	}

	$dataRes8['header'] = array('nojurnal' => $nojurnal8, 'kodejurnal' => 'KROB46', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotbpjskes'], 'totalkredit' => -1 * $param['totpotbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut8 = 1;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akundebet8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akunkredit8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	if (($dataRes8['header']['totaldebet'] == '') || ($dataRes8['header']['totalkredit'] == '') || ($dataRes8['header']['totaldebet'] == 0) || ($dataRes8['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead8 = insertQuery($dbname, 'keu_jurnalht', $dataRes8['header']);

		if (!mysql_query($insHead8)) {
			$headErr8 .= 'Insert Header BTL 8 Error : ' . mysql_error() . "\n";
		}

		if ($headErr8 == '') {
			$detailErr8 = '';

			foreach ($dataRes8['detail'] as $row) {
				$insDet8 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr8 .= 'Insert Detail Error 8: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr8 == '') {
				$updJurnal8 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter8), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal8 . '\'');

				if (!mysql_query($updJurnal8)) {
					echo 'Update Kode Jurnal 8 Error : ' . mysql_error() . "\n";
					$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

					if (!mysql_query($RBDet8)) {
						echo 'Rollback Delete Header BTL 8 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr8;
				$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

				if (!mysql_query($RBDet8)) {
					echo 'Rollback Delete Header 8  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr8;
			exit();
		}
	}
}

function prosesGajiPabrik()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	global $periode;
	$periode = $param['periode'];
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$group = 'PKSB0';
	}
	else if ($param['komponenlembur'] == 17) {
		$group = 'PKSB11';
	}
	else if ($param['komponentunjpremi'] == 16) {
		$group = 'PKSB10';
	}
	else if ($param['komponentunjkom'] == 63) {
		$group = 'PKSB43';
	}
	else if ($param['komponentunjlok'] == 58) {
		$group = 'PKSB38';
	}
	else if ($param['komponentunjprt'] == 59) {
		$group = 'PKSB39';
	}
	else if ($param['komponentunjbbm'] == 61) {
		$group = 'PKSB41';
	}
	else if ($param['komponentunjair'] == 65) {
		$group = 'PKSB44';
	}
	else if ($param['komponentunjspart'] == 60) {
		$group = 'PKSB40';
	}
	else if ($param['komponentunjharian'] == 21) {
		$group = 'PKSB12';
	}
	else if ($param['komponentunjdinas'] == 23) {
		$group = 'PKSB14';
	}
	else if ($param['komponentunjcuti'] == 12) {
		$group = 'PKSB6';
	}
	else if ($param['komponentunjlistrik'] == 62) {
		$group = 'PKSB42';
	}
	else if ($param['komponentunjjkk'] == 6) {
		$group = 'PKSB4';
	}
	else if ($param['komponentunjjkm'] == 7) {
		$group = 'PKSB5';
	}
	else if ($param['komponentunjbpjskes'] == 57) {
		$group = 'PKSB37';
	}
	else if ($param['komponenpotjhtkar'] == 5) {
		$group = 'PKSB45';
	}
	else if ($param['komponenpotjpkar'] == 9) {
		$group = 'PKSB47';
	}
	else if ($param['komponenpotpph21'] == 24) {
		$group = 'PKSB54';
	}
	else if ($param['komponenpotkoperasi'] == 25) {
		$group = 'PKSB55';
	}
	else if ($param['komponenpotvop'] == 52) {
		$group = 'PKSB58';
	}
	else if ($param['komponenpotmotor'] == 10) {
		$group = 'PKSB48';
	}
	else if ($param['komponenpotlaptop'] == 11) {
		$group = 'PKSB49';
	}
	else if ($param['komponenpotdenda'] == 64) {
		$group = 'PKSB62';
	}
	else if ($param['komponenpotbpjskes'] == 8) {
		$group = 'PKSB46';
	}
	else if ($param['komponenpotdendapanen'] == 26) {
		$group = 'PKSB56';
	}
	else {
		$group = 'PKSB99';
	}

	$nojurnal = '';
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB0\' limit 1';
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

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB0/' . $konter;
		$kodeJurnal = 'PKSB0';
	}

	$nojurnal17 = '';

	if ($param['komponenlembur'] == 17) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB11\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet17 = '';
			$akunkredit17 = '';
			$bar = mysql_fetch_object($res);
			$akundebet17 = $bar->noakundebet;
			$akunkredit17 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB11\' ');
		$tmpKonter = fetchData($queryJ);
		$konter17 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal17 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB11/' . $konter17;
		$kodeJurnal17 = 'PKSB11';
	}

	$nojurnal16 = '';

	if ($param['komponenlembur'] == 16) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB10\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet16 = '';
			$akunkredit16 = '';
			$bar = mysql_fetch_object($res);
			$akundebet16 = $bar->noakundebet;
			$akunkredit16 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB10\' ');
		$tmpKonter = fetchData($queryJ);
		$konter16 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal16 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB10/' . $konter16;
		$kodeJurnal16 = 'PKSB10';
	}

	$nojurnal29 = '';

	if ($param['komponentunjkom'] == 63) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB43\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet29 = '';
			$akunkredit29 = '';
			$bar = mysql_fetch_object($res);
			$akundebet29 = $bar->noakundebet;
			$akunkredit29 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB43\' ');
		$tmpKonter = fetchData($queryJ);
		$konter29 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal29 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB43/' . $konter29;
		$kodeJurnal29 = 'PKSB43';
	}

	$nojurnal14 = '';

	if ($param['komponentunjlok'] == 58) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB38\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet14 = '';
			$akunkredit14 = '';
			$bar = mysql_fetch_object($res);
			$akundebet14 = $bar->noakundebet;
			$akunkredit14 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB38\' ');
		$tmpKonter = fetchData($queryJ);
		$konter14 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal14 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB38/' . $konter14;
		$kodeJurnal14 = 'PKSB38';
	}

	$nojurnal13 = '';

	if ($param['komponentunjprt'] == 59) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB39\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet13 = '';
			$akunkredit13 = '';
			$bar = mysql_fetch_object($res);
			$akundebet13 = $bar->noakundebet;
			$akunkredit13 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB39\' ');
		$tmpKonter = fetchData($queryJ);
		$konter13 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal13 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB39/' . $konter13;
		$kodeJurnal13 = 'PKSB39';
	}

	$nojurnal61 = '';

	if ($param['komponentunjbbm'] == 61) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB41\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet61 = '';
			$akunkredit61 = '';
			$bar = mysql_fetch_object($res);
			$akundebet61 = $bar->noakundebet;
			$akunkredit61 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB41\' ');
		$tmpKonter = fetchData($queryJ);
		$konter61 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal61 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB41/' . $konter61;
		$kodeJurnal61 = 'PKSB41';
	}

	$nojurnal65 = '';

	if ($param['komponentunjair'] == 65) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB44\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet65 = '';
			$akunkredit65 = '';
			$bar = mysql_fetch_object($res);
			$akundebet65 = $bar->noakundebet;
			$akunkredit65 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB44\' ');
		$tmpKonter = fetchData($queryJ);
		$konter65 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal65 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB44/' . $konter65;
		$kodeJurnal65 = 'PKSB44';
	}

	$nojurnal60 = '';

	if ($param['komponentunjspart'] == 60) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB40\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet60 = '';
			$akunkredit60 = '';
			$bar = mysql_fetch_object($res);
			$akundebet60 = $bar->noakundebet;
			$akunkredit60 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB40\' ');
		$tmpKonter = fetchData($queryJ);
		$konter60 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal60 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB40/' . $konter60;
		$kodeJurnal60 = 'PKSB40';
	}

	$nojurnal21 = '';

	if ($param['komponentunjharian'] == 21) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB12\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet21 = '';
			$akunkredit21 = '';
			$bar = mysql_fetch_object($res);
			$akundebet21 = $bar->noakundebet;
			$akunkredit21 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB12\' ');
		$tmpKonter = fetchData($queryJ);
		$konter21 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal21 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB12/' . $konter21;
		$kodeJurnal21 = 'PKSB12';
	}

	$nojurnal23 = '';

	if ($param['komponentunjdinas'] == 23) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB14\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet23 = '';
			$akunkredit23 = '';
			$bar = mysql_fetch_object($res);
			$akundebet23 = $bar->noakundebet;
			$akunkredit23 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB14\' ');
		$tmpKonter = fetchData($queryJ);
		$konter23 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal23 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB14/' . $konter23;
		$kodeJurnal23 = 'PKSB14';
	}

	$nojurnal12 = '';

	if ($param['komponentunjcuti'] == 12) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB6\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet12 = '';
			$akunkredit12 = '';
			$bar = mysql_fetch_object($res);
			$akundebet12 = $bar->noakundebet;
			$akunkredit12 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB6\' ');
		$tmpKonter = fetchData($queryJ);
		$konter12 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal12 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB6/' . $konter12;
		$kodeJurnal12 = 'PKSB6';
	}

	$nojurnal62 = '';

	if ($param['komponentunjlistrik'] == 62) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB42\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet62 = '';
			$akunkredit62 = '';
			$bar = mysql_fetch_object($res);
			$akundebet62 = $bar->noakundebet;
			$akunkredit62 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB42\' ');
		$tmpKonter = fetchData($queryJ);
		$konter62 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal62 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB42/' . $konter62;
		$kodeJurnal62 = 'PKSB42';
	}

	$nojurnal22 = '';

	if ($param['komponentunjlain'] == 22) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet22 = '';
			$akunkredit22 = '';
			$bar = mysql_fetch_object($res);
			$akundebet22 = $bar->noakundebet;
			$akunkredit22 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter22 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal22 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB0/' . $konter22;
		$kodeJurnal22 = 'PKSB0';
	}

	$nojurnal54 = '';

	if ($param['komponentunjrapel'] == 54) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet54 = '';
			$akunkredit54 = '';
			$bar = mysql_fetch_object($res);
			$akundebet54 = $bar->noakundebet;
			$akunkredit54 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter54 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal54 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB0/' . $konter54;
		$kodeJurnal54 = 'PKSB0';
	}

	$nojurnal6 = '';

	if ($param['komponentunjjkk'] == 6) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB4\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet6 = '';
			$akunkredit6 = '';
			$bar = mysql_fetch_object($res);
			$akundebet6 = $bar->noakundebet;
			$akunkredit6 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB4\' ');
		$tmpKonter = fetchData($queryJ);
		$konter6 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal6 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB4/' . $konter6;
		$kodeJurnal6 = 'PKSB4';
	}

	$nojurnal7 = '';

	if ($param['komponentunjjkm'] == 7) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB5\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet7 = '';
			$akunkredit7 = '';
			$bar = mysql_fetch_object($res);
			$akundebet7 = $bar->noakundebet;
			$akunkredit7 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB5\' ');
		$tmpKonter = fetchData($queryJ);
		$konter7 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal7 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB5/' . $konter7;
		$kodeJurnal7 = 'PKSB5';
	}

	$nojurnal57 = '';

	if ($param['komponentunjbpjskes'] == 57) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB37\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet57 = '';
			$akunkredit57 = '';
			$bar = mysql_fetch_object($res);
			$akundebet57 = $bar->noakundebet;
			$akunkredit57 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB37\' ');
		$tmpKonter = fetchData($queryJ);
		$konter57 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal57 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB37/' . $konter57;
		$kodeJurnal57 = 'PKSB37';
	}

	$nojurnal5 = '';

	if ($param['komponenpotjhtkar'] == 5) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB45\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet5 = '';
			$akunkredit5 = '';
			$bar = mysql_fetch_object($res);
			$akundebet5 = $bar->noakundebet;
			$akunkredit5 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB45\' ');
		$tmpKonter = fetchData($queryJ);
		$konter5 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal5 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB45/' . $konter5;
		$kodeJurnal5 = 'PKSB45';
	}

	$nojurnal9 = '';

	if ($param['komponenpotjpkar'] == 9) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB47\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet9 = '';
			$akunkredit9 = '';
			$bar = mysql_fetch_object($res);
			$akundebet9 = $bar->noakundebet;
			$akunkredit9 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB47\' ');
		$tmpKonter = fetchData($queryJ);
		$konter9 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal9 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB47/' . $konter9;
		$kodeJurnal9 = 'PKSB47';
	}

	$nojurnal24 = '';

	if ($param['komponenpotpph21'] == 24) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB54\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet24 = '';
			$akunkredit24 = '';
			$bar = mysql_fetch_object($res);
			$akundebet24 = $bar->noakundebet;
			$akunkredit24 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB54\' ');
		$tmpKonter = fetchData($queryJ);
		$konter24 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal24 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB54/' . $konter24;
		$kodeJurnal24 = 'PKSB54';
	}

	$nojurnal25 = '';

	if ($param['komponenpotkoperasi'] == 25) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB55\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet25 = '';
			$akunkredit25 = '';
			$bar = mysql_fetch_object($res);
			$akundebet25 = $bar->noakundebet;
			$akunkredit25 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB55\' ');
		$tmpKonter = fetchData($queryJ);
		$konter25 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal25 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB55/' . $konter25;
		$kodeJurnal25 = 'PKSB55';
	}

	$nojurnal52 = '';

	if ($param['komponenpotvop'] == 52) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB58\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet52 = '';
			$akunkredit52 = '';
			$bar = mysql_fetch_object($res);
			$akundebet52 = $bar->noakundebet;
			$akunkredit52 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB58\' ');
		$tmpKonter = fetchData($queryJ);
		$konter52 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal52 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB58/' . $konter52;
		$kodeJurnal52 = 'PKSB58';
	}

	$nojurnal10 = '';

	if ($param['komponenpotmotor'] == 10) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB48\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet10 = '';
			$akunkredit10 = '';
			$bar = mysql_fetch_object($res);
			$akundebet10 = $bar->noakundebet;
			$akunkredit10 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB48\' ');
		$tmpKonter = fetchData($queryJ);
		$konter10 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal10 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB48/' . $konter10;
		$kodeJurnal10 = 'PKSB48';
	}

	$nojurnal11 = '';

	if ($param['komponenpotlaptop'] == 11) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB49\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet11 = '';
			$akunkredit11 = '';
			$bar = mysql_fetch_object($res);
			$akundebet11 = $bar->noakundebet;
			$akunkredit11 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB49\' ');
		$tmpKonter = fetchData($queryJ);
		$konter11 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal11 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB49/' . $konter11;
		$kodeJurnal11 = 'PKSB49';
	}

	$nojurnal64 = '';

	if ($param['komponenpotdenda'] == 64) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB62\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet64 = '';
			$akunkredit64 = '';
			$bar = mysql_fetch_object($res);
			$akundebet64 = $bar->noakundebet;
			$akunkredit64 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB62\' ');
		$tmpKonter = fetchData($queryJ);
		$konter64 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal64 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB62/' . $konter64;
		$kodeJurnal64 = 'PKSB62';
	}

	$nojurnal8 = '';

	if ($param['komponenpotbpjskes'] == 8) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'PKSB46\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet8 = '';
			$akunkredit8 = '';
			$bar = mysql_fetch_object($res);
			$akundebet8 = $bar->noakundebet;
			$akunkredit8 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'PKSB46\' ');
		$tmpKonter = fetchData($queryJ);
		$konter8 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal8 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/PKSB46/' . $konter8;
		$kodeJurnal8 = 'PKSB46';
	}

	$dataRes['header'] = '';
	$dataRes2['header'] = '';
	$dataRes29['header'] = '';
	$dataRes14['header'] = '';
	$dataRes13['header'] = '';
	$dataRes61['header'] = '';
	$dataRes65['header'] = '';
	$dataRes60['header'] = '';
	$dataRes21['header'] = '';
	$dataRes23['header'] = '';
	$dataRes12['header'] = '';
	$dataRes62['header'] = '';
	$dataRes22['header'] = '';
	$dataRes54['header'] = '';
	$dataRes6['header'] = '';
	$dataRes7['header'] = '';
	$dataRes57['header'] = '';
	$dataRes66['header'] = '';
	$dataRes5['header'] = '';
	$dataRes9['header'] = '';
	$dataRes24['header'] = '';
	$dataRes25['header'] = '';
	$dataRes52['header'] = '';
	$dataRes10['header'] = '';
	$dataRes11['header'] = '';
	$dataRes64['header'] = '';
	$dataRes8['header'] = '';
	$dataResTunjAll['header'] = '';
	$dataRes['detail'] = '';
	$dataRes2['detail'] = '';
	$dataRes29['detail'] = '';
	$dataRes14['detail'] = '';
	$dataRes13['detail'] = '';
	$dataRes61['detail'] = '';
	$dataRes65['detail'] = '';
	$dataRes60['detail'] = '';
	$dataRes21['detail'] = '';
	$dataRes23['detail'] = '';
	$dataRes12['detail'] = '';
	$dataRes62['detail'] = '';
	$dataRes22['detail'] = '';
	$dataRes54['detail'] = '';
	$dataRes6['detail'] = '';
	$dataRes7['detail'] = '';
	$dataRes57['detail'] = '';
	$dataRes66['detail'] = '';
	$dataRes5['detail'] = '';
	$dataRes9['detail'] = '';
	$dataRes24['detail'] = '';
	$dataRes25['detail'] = '';
	$dataRes52['detail'] = '';
	$dataRes10['detail'] = '';
	$dataRes11['detail'] = '';
	$dataRes64['detail'] = '';
	$dataRes8['detail'] = '';
	$dataResTunjAll['detail'] = '';
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => 'PKSB0', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'totalkredit' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	if (($dataRes['header']['totaldebet'] == '') || ($dataRes['header']['totalkredit'] == '') || ($dataRes['header']['totaldebet'] == 0) || ($dataRes['header']['totalkredit'] == 0)) {
	}
	else {
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

	$dataRes2['header'] = array('nojurnal' => $nojurnal17, 'kodejurnal' => 'PKSB11', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totlembur'], 'totalkredit' => -1 * $param['totlembur'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUruttunjab = 1;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akundebet17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akunkredit17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	if (($dataRes2['header']['totaldebet'] == '') || ($dataRes2['header']['totalkredit'] == '') || ($dataRes2['header']['totaldebet'] == 0) || ($dataRes2['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

		if (!mysql_query($insHead2)) {
			$headErr2 .= 'Insert Header BTL 67 Error : ' . mysql_error() . "\n";
		}

		if ($headErr2 == '') {
			$detailErr2 = '';

			foreach ($dataRes2['detail'] as $row) {
				$insDet2 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr2 .= 'Insert Detail Error 17: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr2 == '') {
				$updJurnal17 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter17), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal17 . '\'');

				if (!mysql_query($updJurnal17)) {
					echo 'Update Kode Jurnal 17 Error : ' . mysql_error() . "\n";
					$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

					if (!mysql_query($RBDet17)) {
						echo 'Rollback Delete Header BTL 17 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr2;
				$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

				if (!mysql_query($RBDet17)) {
					echo 'Rollback Delete Header 17 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr2;
			exit();
		}
	}

	$dataRes29['header'] = array('nojurnal' => $nojurnal29, 'kodejurnal' => 'PKSB43', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjkom'], 'totalkredit' => -1 * $param['tottunjkom'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_KOMUNIKASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut29 = 1;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akundebet29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akunkredit29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	if (($dataRes29['header']['totaldebet'] == '') || ($dataRes29['header']['totalkredit'] == '') || ($dataRes29['header']['totaldebet'] == 0) || ($dataRes29['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead29 = insertQuery($dbname, 'keu_jurnalht', $dataRes29['header']);

		if (!mysql_query($insHead29)) {
			$headErr29 .= 'Insert Header BTL29 Error : ' . mysql_error() . "\n";
		}

		if ($headErr29 == '') {
			$detailErr29 = '';

			foreach ($dataRes29['detail'] as $row) {
				$insDet29 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr29 .= 'Insert Detail Error 29: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr29 == '') {
				$updJurnal29 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter29), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal29 . '\'');

				if (!mysql_query($updJurnal29)) {
					echo 'Update Kode Jurnal 29 Error : ' . mysql_error() . "\n";
					$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

					if (!mysql_query($RBDet29)) {
						echo 'Rollback Delete Header BTL 29 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr29;
				$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

				if (!mysql_query($RBDet29)) {
					echo 'Rollback Delete Header 29 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr29;
			exit();
		}
	}

	$dataRes14['header'] = array('nojurnal' => $nojurnal14, 'kodejurnal' => 'PKSB38', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlok'], 'totalkredit' => -1 * $param['tottunjlok'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut14 = 1;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akundebet14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akunkredit14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	if (($dataRes14['header']['totaldebet'] == '') || ($dataRes14['header']['totalkredit'] == '') || ($dataRes14['header']['totaldebet'] == 0) || ($dataRes14['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead14 = insertQuery($dbname, 'keu_jurnalht', $dataRes14['header']);

		if (!mysql_query($insHead14)) {
			$headErr14 .= 'Insert Header BTL 14 Error : ' . mysql_error() . "\n";
		}

		if ($headErr14 == '') {
			$detailErr14 = '';

			foreach ($dataRes14['detail'] as $row) {
				$insDet14 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr14 .= 'Insert Detail Error 14: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr14 == '') {
				$updJurnal14 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter14), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal14 . '\'');

				if (!mysql_query($updJurnal14)) {
					echo 'Update Kode Jurnal 14 Error : ' . mysql_error() . "\n";
					$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

					if (!mysql_query($RBDet14)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr14;
				$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

				if (!mysql_query($RBDet14)) {
					echo 'Rollback Delete Header 14 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr14;
			exit();
		}
	}

	$dataRes13['header'] = array('nojurnal' => $nojurnal13, 'kodejurnal' => 'PKSB39', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjprt'], 'totalkredit' => -1 * $param['tottunjprt'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut13 = 1;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akundebet13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akunkredit13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	if (($dataRes13['header']['totaldebet'] == '') || ($dataRes13['header']['totalkredit'] == '') || ($dataRes13['header']['totaldebet'] == 0) || ($dataRes13['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead13 = insertQuery($dbname, 'keu_jurnalht', $dataRes13['header']);

		if (!mysql_query($insHead13)) {
			$headErr13 .= 'Insert Header BTL 13 Error : ' . mysql_error() . "\n";
		}

		if ($headErr13 == '') {
			$detailErr13 = '';

			foreach ($dataRes13['detail'] as $row) {
				$insDet13 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr13 .= 'Insert Detail Error 13: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr13 == '') {
				$updJurnal13 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter13), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal13 . '\'');

				if (!mysql_query($updJurnal13)) {
					echo 'Update Kode Jurnal 13 Error : ' . mysql_error() . "\n";
					$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

					if (!mysql_query($RBDet13)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr13;
				$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

				if (!mysql_query($RBDet13)) {
					echo 'Rollback Delete Header 13 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr13;
			exit();
		}
	}

	$dataRes61['header'] = array('nojurnal' => $nojurnal61, 'kodejurnal' => 'PKSB41', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbbm'], 'totalkredit' => -1 * $param['tottunjbbm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut61 = 1;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akundebet61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akunkredit61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	if (($dataRes61['header']['totaldebet'] == '') || ($dataRes61['header']['totalkredit'] == '') || ($dataRes61['header']['totaldebet'] == 0) || ($dataRes61['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead61 = insertQuery($dbname, 'keu_jurnalht', $dataRes61['header']);

		if (!mysql_query($insHead61)) {
			$headErr61 .= 'Insert Header BTL 61 Error : ' . mysql_error() . "\n";
		}

		if ($headErr61 == '') {
			$detailErr61 = '';

			foreach ($dataRes61['detail'] as $row) {
				$insDet61 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr61 .= 'Insert Detail Error 61: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr61 == '') {
				$updJurnal61 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter61), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal61 . '\'');

				if (!mysql_query($updJurnal61)) {
					echo 'Update Kode Jurnal 61 Error : ' . mysql_error() . "\n";
					$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

					if (!mysql_query($RBDet61)) {
						echo 'Rollback Delete Header BTL 61 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr61;
				$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

				if (!mysql_query($RBDet61)) {
					echo 'Rollback Delete Header 61  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr61;
			exit();
		}
	}

	$dataRes65['header'] = array('nojurnal' => $nojurnal65, 'kodejurnal' => 'PKSB44', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjair'], 'totalkredit' => -1 * $param['tottunjair'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut65 = 1;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akundebet65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akunkredit65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	if (($dataRes65['header']['totaldebet'] == '') || ($dataRes65['header']['totalkredit'] == '') || ($dataRes65['header']['totaldebet'] == 0) || ($dataRes65['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead65 = insertQuery($dbname, 'keu_jurnalht', $dataRes65['header']);

		if (!mysql_query($insHead65)) {
			$headErr65 .= 'Insert Header BTL 65 Error : ' . mysql_error() . "\n";
		}

		if ($headErr65 == '') {
			$detailErr65 = '';

			foreach ($dataRes65['detail'] as $row) {
				$insDet65 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr65 .= 'Insert Detail Error 65: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr65 == '') {
				$updJurnal65 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter65), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal65 . '\'');

				if (!mysql_query($updJurnal65)) {
					echo 'Update Kode Jurnal 65 Error : ' . mysql_error() . "\n";
					$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

					if (!mysql_query($RBDet65)) {
						echo 'Rollback Delete Header BTL 65 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr65;
				$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

				if (!mysql_query($RBDet65)) {
					echo 'Rollback Delete Header 65  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr65;
			exit();
		}
	}

	$dataRes60['header'] = array('nojurnal' => $nojurnal60, 'kodejurnal' => 'PKSB40', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjspart'], 'totalkredit' => -1 * $param['tottunjspart'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut60 = 1;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akundebet60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akunkredit60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	if (($dataRes60['header']['totaldebet'] == '') || ($dataRes60['header']['totalkredit'] == '') || ($dataRes60['header']['totaldebet'] == 0) || ($dataRes60['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead60 = insertQuery($dbname, 'keu_jurnalht', $dataRes60['header']);

		if (!mysql_query($insHead60)) {
			$headErr60 .= 'Insert Header BTL 60 Error : ' . mysql_error() . "\n";
		}

		if ($headErr60 == '') {
			$detailErr60 = '';

			foreach ($dataRes60['detail'] as $row) {
				$insDet60 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr60 .= 'Insert Detail Error 60: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr60 == '') {
				$updJurnal60 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter60), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal60 . '\'');

				if (!mysql_query($updJurnal60)) {
					echo 'Update Kode Jurnal 60 Error : ' . mysql_error() . "\n";
					$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

					if (!mysql_query($RBDet60)) {
						echo 'Rollback Delete Header BTL 60 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr60;
				$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

				if (!mysql_query($RBDet60)) {
					echo 'Rollback Delete Header 60  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr60;
			exit();
		}
	}

	$dataRes21['header'] = array('nojurnal' => $nojurnal21, 'kodejurnal' => 'PKSB12', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjharian'], 'totalkredit' => -1 * $param['tottunjharian'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut21 = 1;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akundebet21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akunkredit21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	if (($dataRes21['header']['totaldebet'] == '') || ($dataRes21['header']['totalkredit'] == '') || ($dataRes21['header']['totaldebet'] == 0) || ($dataRes21['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead21 = insertQuery($dbname, 'keu_jurnalht', $dataRes21['header']);

		if (!mysql_query($insHead21)) {
			$headErr21 .= 'Insert Header BTL 21 Error : ' . mysql_error() . "\n";
		}

		if ($headErr21 == '') {
			$detailErr21 = '';

			foreach ($dataRes21['detail'] as $row) {
				$insDet21 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr21 .= 'Insert Detail Error 21: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr21 == '') {
				$updJurnal21 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter21), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal21 . '\'');

				if (!mysql_query($updJurnal21)) {
					echo 'Update Kode Jurnal 21 Error : ' . mysql_error() . "\n";
					$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

					if (!mysql_query($RBDet21)) {
						echo 'Rollback Delete Header BTL 21 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr21;
				$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

				if (!mysql_query($RBDet21)) {
					echo 'Rollback Delete Header 21  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr21;
			exit();
		}
	}

	$dataRes23['header'] = array('nojurnal' => $nojurnal23, 'kodejurnal' => 'PKSB14', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjdinas'], 'totalkredit' => -1 * $param['tottunjdinas'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut23 = 1;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akundebet23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akunkredit23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	if (($dataRes23['header']['totaldebet'] == '') || ($dataRes23['header']['totalkredit'] == '') || ($dataRes23['header']['totaldebet'] == 0) || ($dataRes23['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead23 = insertQuery($dbname, 'keu_jurnalht', $dataRes23['header']);

		if (!mysql_query($insHead23)) {
			$headErr23 .= 'Insert Header BTL 23 Error : ' . mysql_error() . "\n";
		}

		if ($headErr23 == '') {
			$detailErr23 = '';

			foreach ($dataRes23['detail'] as $row) {
				$insDet23 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr23 .= 'Insert Detail Error 23: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr23 == '') {
				$updJurnal23 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter23), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal23 . '\'');

				if (!mysql_query($updJurnal23)) {
					echo 'Update Kode Jurnal 23 Error : ' . mysql_error() . "\n";
					$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

					if (!mysql_query($RBDet23)) {
						echo 'Rollback Delete Header BTL 23 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr23;
				$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

				if (!mysql_query($RBDet23)) {
					echo 'Rollback Delete Header 23  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr23;
			exit();
		}
	}

	$dataRes12['header'] = array('nojurnal' => $nojurnal12, 'kodejurnal' => 'PKSB6', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjcuti'], 'totalkredit' => -1 * $param['tottunjcuti'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut12 = 1;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akundebet12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akunkredit12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	if (($dataRes12['header']['totaldebet'] == '') || ($dataRes12['header']['totalkredit'] == '') || ($dataRes12['header']['totaldebet'] == 0) || ($dataRes12['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead12 = insertQuery($dbname, 'keu_jurnalht', $dataRes12['header']);

		if (!mysql_query($insHead12)) {
			$headErr12 .= 'Insert Header BTL 12 Error : ' . mysql_error() . "\n";
		}

		if ($headErr12 == '') {
			$detailErr12 = '';

			foreach ($dataRes12['detail'] as $row) {
				$insDet12 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr12 .= 'Insert Detail Error 12: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr12 == '') {
				$updJurnal12 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter12), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal12 . '\'');

				if (!mysql_query($updJurnal12)) {
					echo 'Update Kode Jurnal 12 Error : ' . mysql_error() . "\n";
					$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

					if (!mysql_query($RBDet12)) {
						echo 'Rollback Delete Header BTL 12 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr12;
				$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

				if (!mysql_query($RBDet12)) {
					echo 'Rollback Delete Header 12  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr12;
			exit();
		}
	}

	$dataRes62['header'] = array('nojurnal' => $nojurnal62, 'kodejurnal' => 'PKSB42', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlistrik'], 'totalkredit' => -1 * $param['tottunjlistrik'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut62 = 1;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akundebet62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akunkredit62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	if (($dataRes62['header']['totaldebet'] == '') || ($dataRes62['header']['totalkredit'] == '') || ($dataRes62['header']['totaldebet'] == 0) || ($dataRes62['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead62 = insertQuery($dbname, 'keu_jurnalht', $dataRes62['header']);

		if (!mysql_query($insHead62)) {
			$headErr62 .= 'Insert Header BTL 62 Error : ' . mysql_error() . "\n";
		}

		if ($headErr62 == '') {
			$detailErr62 = '';

			foreach ($dataRes62['detail'] as $row) {
				$insDet62 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr62 .= 'Insert Detail Error 62: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr62 == '') {
				$updJurnal62 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter62), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal62 . '\'');

				if (!mysql_query($updJurnal62)) {
					echo 'Update Kode Jurnal 62 Error : ' . mysql_error() . "\n";
					$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

					if (!mysql_query($RBDet62)) {
						echo 'Rollback Delete Header BTL 62 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr62;
				$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

				if (!mysql_query($RBDet62)) {
					echo 'Rollback Delete Header 62  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr62;
			exit();
		}
	}

	$dataRes6['header'] = array('nojurnal' => $nojurnal6, 'kodejurnal' => 'PKSB4', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkk'], 'totalkredit' => -1 * $param['tottunjjkk'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut6 = 1;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akundebet6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akunkredit6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	if (($dataRes6['header']['totaldebet'] == '') || ($dataRes6['header']['totalkredit'] == '') || ($dataRes6['header']['totaldebet'] == 0) || ($dataRes6['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead6 = insertQuery($dbname, 'keu_jurnalht', $dataRes6['header']);

		if (!mysql_query($insHead6)) {
			$headErr6 .= 'Insert Header BTL 6 Error : ' . mysql_error() . "\n";
		}

		if ($headErr6 == '') {
			$detailErr6 = '';

			foreach ($dataRes6['detail'] as $row) {
				$insDet6 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr6 .= 'Insert Detail Error 6: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr6 == '') {
				$updJurnal6 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter6), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal6 . '\'');

				if (!mysql_query($updJurnal6)) {
					echo 'Update Kode Jurnal 6 Error : ' . mysql_error() . "\n";
					$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

					if (!mysql_query($RBDet6)) {
						echo 'Rollback Delete Header BTL 6 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr6;
				$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

				if (!mysql_query($RBDet6)) {
					echo 'Rollback Delete Header 6  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr6;
			exit();
		}
	}

	$dataRes7['header'] = array('nojurnal' => $nojurnal7, 'kodejurnal' => 'PKSB5', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkm'], 'totalkredit' => -1 * $param['tottunjjkm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut7 = 1;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akundebet7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akunkredit7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	if (($dataRes7['header']['totaldebet'] == '') || ($dataRes7['header']['totalkredit'] == '') || ($dataRes7['header']['totaldebet'] == 0) || ($dataRes7['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead7 = insertQuery($dbname, 'keu_jurnalht', $dataRes7['header']);

		if (!mysql_query($insHead7)) {
			$headErr7 .= 'Insert Header BTL 7 Error : ' . mysql_error() . "\n";
		}

		if ($headErr7 == '') {
			$detailErr7 = '';

			foreach ($dataRes7['detail'] as $row) {
				$insDet7 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr7 .= 'Insert Detail Error 7: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr7 == '') {
				$updJurnal7 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter7), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal7 . '\'');

				if (!mysql_query($updJurnal7)) {
					echo 'Update Kode Jurnal 7 Error : ' . mysql_error() . "\n";
					$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

					if (!mysql_query($RBDet7)) {
						echo 'Rollback Delete Header BTL 7 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr7;
				$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

				if (!mysql_query($RBDet7)) {
					echo 'Rollback Delete Header 7  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr7;
			exit();
		}
	}

	$dataRes57['header'] = array('nojurnal' => $nojurnal57, 'kodejurnal' => 'PKSB37', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbpjskes'], 'totalkredit' => -1 * $param['tottunjbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut57 = 1;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akundebet57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akunkredit57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	if (($dataRes57['header']['totaldebet'] == '') || ($dataRes57['header']['totalkredit'] == '') || ($dataRes57['header']['totaldebet'] == 0) || ($dataRes57['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead57 = insertQuery($dbname, 'keu_jurnalht', $dataRes57['header']);

		if (!mysql_query($insHead57)) {
			$headErr57 .= 'Insert Header BTL 57 Error : ' . mysql_error() . "\n";
		}

		if ($headErr57 == '') {
			$detailErr57 = '';

			foreach ($dataRes57['detail'] as $row) {
				$insDet57 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr57 .= 'Insert Detail Error 57: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr57 == '') {
				$updJurnal57 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter57), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal57 . '\'');

				if (!mysql_query($updJurnal57)) {
					echo 'Update Kode Jurnal 57 Error : ' . mysql_error() . "\n";
					$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

					if (!mysql_query($RBDet57)) {
						echo 'Rollback Delete Header BTL 57 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr57;
				$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

				if (!mysql_query($RBDet57)) {
					echo 'Rollback Delete Header 57  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr57;
			exit();
		}
	}

	$dataRes5['header'] = array('nojurnal' => $nojurnal5, 'kodejurnal' => 'PKSB45', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjhtkar'], 'totalkredit' => -1 * $param['totpotjhtkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut5 = 1;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akundebet5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akunkredit5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	if (($dataRes5['header']['totaldebet'] == '') || ($dataRes5['header']['totalkredit'] == '') || ($dataRes5['header']['totaldebet'] == 0) || ($dataRes5['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead5 = insertQuery($dbname, 'keu_jurnalht', $dataRes5['header']);

		if (!mysql_query($insHead5)) {
			$headErr5 .= 'Insert Header BTL 5 Error : ' . mysql_error() . "\n";
		}

		if ($headErr5 == '') {
			$detailErr5 = '';

			foreach ($dataRes5['detail'] as $row) {
				$insDet5 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr5 .= 'Insert Detail Error 5: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr5 == '') {
				$updJurnal5 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter5), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal5 . '\'');

				if (!mysql_query($updJurnal5)) {
					echo 'Update Kode Jurnal 5 Error : ' . mysql_error() . "\n";
					$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

					if (!mysql_query($RBDet5)) {
						echo 'Rollback Delete Header BTL 5 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr5;
				$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

				if (!mysql_query($RBDet5)) {
					echo 'Rollback Delete Header 5  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr5;
			exit();
		}
	}

	$dataRes9['header'] = array('nojurnal' => $nojurnal9, 'kodejurnal' => 'PKSB47', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjpkar'], 'totalkredit' => -1 * $param['totpotjpkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut9 = 1;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akundebet9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akunkredit9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	if (($dataRes9['header']['totaldebet'] == '') || ($dataRes9['header']['totalkredit'] == '') || ($dataRes9['header']['totaldebet'] == 0) || ($dataRes9['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead9 = insertQuery($dbname, 'keu_jurnalht', $dataRes9['header']);

		if (!mysql_query($insHead9)) {
			$headErr9 .= 'Insert Header BTL 9 Error : ' . mysql_error() . "\n";
		}

		if ($headErr9 == '') {
			$detailErr9 = '';

			foreach ($dataRes9['detail'] as $row) {
				$insDet9 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr9 .= 'Insert Detail Error 9: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr9 == '') {
				$updJurnal9 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter9), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal9 . '\'');

				if (!mysql_query($updJurnal9)) {
					echo 'Update Kode Jurnal 9 Error : ' . mysql_error() . "\n";
					$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

					if (!mysql_query($RBDet9)) {
						echo 'Rollback Delete Header BTL 9 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr9;
				$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

				if (!mysql_query($RBDet9)) {
					echo 'Rollback Delete Header 9  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr9;
			exit();
		}
	}

	$dataRes24['header'] = array('nojurnal' => $nojurnal24, 'kodejurnal' => 'PKSB54', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotpph21'], 'totalkredit' => -1 * $param['totpotpph21'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut24 = 1;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akundebet24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akunkredit24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	if (($dataRes24['header']['totaldebet'] == '') || ($dataRes24['header']['totalkredit'] == '') || ($dataRes24['header']['totaldebet'] == 0) || ($dataRes24['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead24 = insertQuery($dbname, 'keu_jurnalht', $dataRes24['header']);

		if (!mysql_query($insHead24)) {
			$headErr24 .= 'Insert Header BTL 24 Error : ' . mysql_error() . "\n";
		}

		if ($headErr24 == '') {
			$detailErr24 = '';

			foreach ($dataRes24['detail'] as $row) {
				$insDet24 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr24 .= 'Insert Detail Error 24: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr24 == '') {
				$updJurnal24 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter24), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal24 . '\'');

				if (!mysql_query($updJurnal24)) {
					echo 'Update Kode Jurnal 24 Error : ' . mysql_error() . "\n";
					$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

					if (!mysql_query($RBDet24)) {
						echo 'Rollback Delete Header BTL 24 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr24;
				$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

				if (!mysql_query($RBDet24)) {
					echo 'Rollback Delete Header 24  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr24;
			exit();
		}
	}

	$dataRes25['header'] = array('nojurnal' => $nojurnal25, 'kodejurnal' => 'PKSB55', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotkoperasi'], 'totalkredit' => -1 * $param['totpotkoperasi'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut25 = 1;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akundebet25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akunkredit25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	if (($dataRes25['header']['totaldebet'] == '') || ($dataRes25['header']['totalkredit'] == '') || ($dataRes25['header']['totaldebet'] == 0) || ($dataRes25['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead25 = insertQuery($dbname, 'keu_jurnalht', $dataRes25['header']);

		if (!mysql_query($insHead25)) {
			$headErr25 .= 'Insert Header BTL 25 Error : ' . mysql_error() . "\n";
		}

		if ($headErr25 == '') {
			$detailErr25 = '';

			foreach ($dataRes25['detail'] as $row) {
				$insDet25 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr25 .= 'Insert Detail Error 25: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr25 == '') {
				$updJurnal25 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter25), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal25 . '\'');

				if (!mysql_query($updJurnal25)) {
					echo 'Update Kode Jurnal 25 Error : ' . mysql_error() . "\n";
					$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

					if (!mysql_query($RBDet25)) {
						echo 'Rollback Delete Header BTL 25 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr25;
				$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

				if (!mysql_query($RBDet25)) {
					echo 'Rollback Delete Header 25  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr25;
			exit();
		}
	}

	$dataRes52['header'] = array('nojurnal' => $nojurnal52, 'kodejurnal' => 'PKSB58', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotvop'], 'totalkredit' => -1 * $param['totpotvop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut52 = 1;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akundebet52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akunkredit52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	if (($dataRes52['header']['totaldebet'] == '') || ($dataRes52['header']['totalkredit'] == '') || ($dataRes52['header']['totaldebet'] == 0) || ($dataRes52['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead52 = insertQuery($dbname, 'keu_jurnalht', $dataRes52['header']);

		if (!mysql_query($insHead52)) {
			$headErr52 .= 'Insert Header BTL 52 Error : ' . mysql_error() . "\n";
		}

		if ($headErr52 == '') {
			$detailErr52 = '';

			foreach ($dataRes52['detail'] as $row) {
				$insDet52 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr52 .= 'Insert Detail Error 52: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr52 == '') {
				$updJurnal52 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter52), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal52 . '\'');

				if (!mysql_query($updJurnal52)) {
					echo 'Update Kode Jurnal 52 Error : ' . mysql_error() . "\n";
					$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

					if (!mysql_query($RBDet52)) {
						echo 'Rollback Delete Header BTL 52 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr52;
				$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

				if (!mysql_query($RBDet52)) {
					echo 'Rollback Delete Header 52  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr52;
			exit();
		}
	}

	$dataRes10['header'] = array('nojurnal' => $nojurnal10, 'kodejurnal' => 'PKSB48', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotmotor'], 'totalkredit' => -1 * $param['totpotmotor'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut10 = 1;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akundebet10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akunkredit10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	if (($dataRes10['header']['totaldebet'] == '') || ($dataRes10['header']['totalkredit'] == '') || ($dataRes10['header']['totaldebet'] == 0) || ($dataRes10['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead10 = insertQuery($dbname, 'keu_jurnalht', $dataRes10['header']);

		if (!mysql_query($insHead10)) {
			$headErr10 .= 'Insert Header BTL 10 Error : ' . mysql_error() . "\n";
		}

		if ($headErr10 == '') {
			$detailErr10 = '';

			foreach ($dataRes10['detail'] as $row) {
				$insDet10 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr10 .= 'Insert Detail Error 10: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr10 == '') {
				$updJurnal10 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter10), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal10 . '\'');

				if (!mysql_query($updJurnal10)) {
					echo 'Update Kode Jurnal 10 Error : ' . mysql_error() . "\n";
					$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

					if (!mysql_query($RBDet10)) {
						echo 'Rollback Delete Header BTL 10 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr10;
				$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

				if (!mysql_query($RBDet10)) {
					echo 'Rollback Delete Header 10  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr10;
			exit();
		}
	}

	$dataRes11['header'] = array('nojurnal' => $nojurnal11, 'kodejurnal' => 'PKSB49', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotlaptop'], 'totalkredit' => -1 * $param['totpotlaptop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut11 = 1;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akundebet11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akunkredit11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	if (($dataRes11['header']['totaldebet'] == '') || ($dataRes11['header']['totalkredit'] == '') || ($dataRes11['header']['totaldebet'] == 0) || ($dataRes11['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead11 = insertQuery($dbname, 'keu_jurnalht', $dataRes11['header']);

		if (!mysql_query($insHead11)) {
			$headErr11 .= 'Insert Header BTL 11 Error : ' . mysql_error() . "\n";
		}

		if ($headErr11 == '') {
			$detailErr11 = '';

			foreach ($dataRes11['detail'] as $row) {
				$insDet11 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr11 .= 'Insert Detail Error 11: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr11 == '') {
				$updJurnal11 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter11), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal11 . '\'');

				if (!mysql_query($updJurnal11)) {
					echo 'Update Kode Jurnal 11 Error : ' . mysql_error() . "\n";
					$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

					if (!mysql_query($RBDet11)) {
						echo 'Rollback Delete Header BTL 11 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr11;
				$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

				if (!mysql_query($RBDet11)) {
					echo 'Rollback Delete Header 11  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr11;
			exit();
		}
	}

	$dataRes64['header'] = array('nojurnal' => $nojurnal64, 'kodejurnal' => 'PKSB62', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotdenda'], 'totalkredit' => -1 * $param['totpotdenda'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut64 = 1;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akundebet64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akunkredit64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	if (($dataRes64['header']['totaldebet'] == '') || ($dataRes64['header']['totalkredit'] == '') || ($dataRes64['header']['totaldebet'] == 0) || ($dataRes64['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead64 = insertQuery($dbname, 'keu_jurnalht', $dataRes64['header']);

		if (!mysql_query($insHead64)) {
			$headErr64 .= 'Insert Header BTL 64 Error : ' . mysql_error() . "\n";
		}

		if ($headErr64 == '') {
			$detailErr64 = '';

			foreach ($dataRes64['detail'] as $row) {
				$insDet64 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr64 .= 'Insert Detail Error 64: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr64 == '') {
				$updJurnal64 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter64), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal64 . '\'');

				if (!mysql_query($updJurnal64)) {
					echo 'Update Kode Jurnal 64 Error : ' . mysql_error() . "\n";
					$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

					if (!mysql_query($RBDet64)) {
						echo 'Rollback Delete Header BTL 64 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr64;
				$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

				if (!mysql_query($RBDet64)) {
					echo 'Rollback Delete Header 64  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr64;
			exit();
		}
	}

	$dataRes8['header'] = array('nojurnal' => $nojurnal8, 'kodejurnal' => 'PKSB46', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotbpjskes'], 'totalkredit' => -1 * $param['totpotbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut8 = 1;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akundebet8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akunkredit8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	if (($dataRes8['header']['totaldebet'] == '') || ($dataRes8['header']['totalkredit'] == '') || ($dataRes8['header']['totaldebet'] == 0) || ($dataRes8['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead8 = insertQuery($dbname, 'keu_jurnalht', $dataRes8['header']);

		if (!mysql_query($insHead8)) {
			$headErr8 .= 'Insert Header BTL 8 Error : ' . mysql_error() . "\n";
		}

		if ($headErr8 == '') {
			$detailErr8 = '';

			foreach ($dataRes8['detail'] as $row) {
				$insDet8 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr8 .= 'Insert Detail Error 8: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr8 == '') {
				$updJurnal8 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter8), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal8 . '\'');

				if (!mysql_query($updJurnal8)) {
					echo 'Update Kode Jurnal 8 Error : ' . mysql_error() . "\n";
					$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

					if (!mysql_query($RBDet8)) {
						echo 'Rollback Delete Header BTL 8 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr8;
				$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

				if (!mysql_query($RBDet8)) {
					echo 'Rollback Delete Header 8  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr8;
			exit();
		}
	}
}

function prosesGajiHORO()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	global $periode;
	$periode = $param['periode'];
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$group = 'KNTB0';
	}
	else if ($param['komponenlembur'] == 17) {
		$group = 'KNTB11';
	}
	else if ($param['komponentunjkom'] == 63) {
		$group = 'KNTB43';
	}
	else if ($param['komponentunjlok'] == 58) {
		$group = 'KNTB38';
	}
	else if ($param['komponentunjprt'] == 59) {
		$group = 'KNTB39';
	}
	else if ($param['komponentunjbbm'] == 61) {
		$group = 'KNTB41';
	}
	else if ($param['komponentunjair'] == 65) {
		$group = 'KNTB44';
	}
	else if ($param['komponentunjspart'] == 60) {
		$group = 'KNTB40';
	}
	else if ($param['komponentunjharian'] == 21) {
		$group = 'KNTB12';
	}
	else if ($param['komponentunjdinas'] == 23) {
		$group = 'KNTB14';
	}
	else if ($param['komponentunjcuti'] == 12) {
		$group = 'KNTB6';
	}
	else if ($param['komponentunjlistrik'] == 62) {
		$group = 'KNTB42';
	}
	else if ($param['komponentunjjkk'] == 6) {
		$group = 'KNTB4';
	}
	else if ($param['komponentunjjkm'] == 7) {
		$group = 'KNTB5';
	}
	else if ($param['komponentunjbpjskes'] == 57) {
		$group = 'KNTB37';
	}
	else if ($param['komponenpotjhtkar'] == 5) {
		$group = 'KNTB45';
	}
	else if ($param['komponenpotjpkar'] == 9) {
		$group = 'KNTB47';
	}
	else if ($param['komponenpotpph21'] == 24) {
		$group = 'KNTB53';
	}
	else if ($param['komponenpotkoperasi'] == 25) {
		$group = 'KNTB55';
	}
	else if ($param['komponenpotvop'] == 52) {
		$group = 'KNTB58';
	}
	else if ($param['komponenpotmotor'] == 10) {
		$group = 'KNTB48';
	}
	else if ($param['komponenpotlaptop'] == 11) {
		$group = 'KNTB49';
	}
	else if ($param['komponenpotdenda'] == 64) {
		$group = 'KNTB62';
	}
	else if ($param['komponenpotbpjskes'] == 8) {
		$group = 'KNTB46';
	}
	else {
		$group = 'KNTB99';
	}

	$nojurnal = '';
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB0\' limit 1';
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

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB0/' . $konter;
		$kodeJurnal = 'KNTB0';
	}

	$nojurnal17 = '';

	if ($param['komponenlembur'] == 17) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB11\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet17 = '';
			$akunkredit17 = '';
			$bar = mysql_fetch_object($res);
			$akundebet17 = $bar->noakundebet;
			$akunkredit17 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB11\' ');
		$tmpKonter = fetchData($queryJ);
		$konter17 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal17 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB11/' . $konter17;
		$kodeJurnal17 = 'KNTB11';
	}

	$nojurnal29 = '';

	if ($param['komponentunjkom'] == 63) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB43\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet29 = '';
			$akunkredit29 = '';
			$bar = mysql_fetch_object($res);
			$akundebet29 = $bar->noakundebet;
			$akunkredit29 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB43\' ');
		$tmpKonter = fetchData($queryJ);
		$konter29 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal29 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB43/' . $konter29;
		$kodeJurnal29 = 'KNTB43';
	}

	$nojurnal14 = '';

	if ($param['komponentunjlok'] == 58) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB38\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet14 = '';
			$akunkredit14 = '';
			$bar = mysql_fetch_object($res);
			$akundebet14 = $bar->noakundebet;
			$akunkredit14 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB38\' ');
		$tmpKonter = fetchData($queryJ);
		$konter14 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal14 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB38/' . $konter14;
		$kodeJurnal14 = 'KNTB38';
	}

	$nojurnal13 = '';

	if ($param['komponentunjprt'] == 59) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB39\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet13 = '';
			$akunkredit13 = '';
			$bar = mysql_fetch_object($res);
			$akundebet13 = $bar->noakundebet;
			$akunkredit13 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB39\' ');
		$tmpKonter = fetchData($queryJ);
		$konter13 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal13 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB39/' . $konter13;
		$kodeJurnal13 = 'KNTB39';
	}

	$nojurnal61 = '';

	if ($param['komponentunjbbm'] == 61) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB41\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet61 = '';
			$akunkredit61 = '';
			$bar = mysql_fetch_object($res);
			$akundebet61 = $bar->noakundebet;
			$akunkredit61 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB41\' ');
		$tmpKonter = fetchData($queryJ);
		$konter61 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal61 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB41/' . $konter61;
		$kodeJurnal61 = 'KNTB41';
	}

	$nojurnal65 = '';

	if ($param['komponentunjair'] == 65) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB44\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet65 = '';
			$akunkredit65 = '';
			$bar = mysql_fetch_object($res);
			$akundebet65 = $bar->noakundebet;
			$akunkredit65 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB44\' ');
		$tmpKonter = fetchData($queryJ);
		$konter65 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal65 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB44/' . $konter65;
		$kodeJurnal65 = 'KNTB44';
	}

	$nojurnal60 = '';

	if ($param['komponentunjspart'] == 60) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB40\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet60 = '';
			$akunkredit60 = '';
			$bar = mysql_fetch_object($res);
			$akundebet60 = $bar->noakundebet;
			$akunkredit60 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB40\' ');
		$tmpKonter = fetchData($queryJ);
		$konter60 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal60 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB40/' . $konter60;
		$kodeJurnal60 = 'KNTB40';
	}

	$nojurnal21 = '';

	if ($param['komponentunjharian'] == 21) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB12\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet21 = '';
			$akunkredit21 = '';
			$bar = mysql_fetch_object($res);
			$akundebet21 = $bar->noakundebet;
			$akunkredit21 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB12\' ');
		$tmpKonter = fetchData($queryJ);
		$konter21 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal21 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB12/' . $konter21;
		$kodeJurnal21 = 'KNTB12';
	}

	$nojurnal23 = '';

	if ($param['komponentunjdinas'] == 23) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB14\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet23 = '';
			$akunkredit23 = '';
			$bar = mysql_fetch_object($res);
			$akundebet23 = $bar->noakundebet;
			$akunkredit23 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB14\' ');
		$tmpKonter = fetchData($queryJ);
		$konter23 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal23 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB14/' . $konter23;
		$kodeJurnal23 = 'KNTB14';
	}

	$nojurnal12 = '';

	if ($param['komponentunjcuti'] == 12) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB6\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet12 = '';
			$akunkredit12 = '';
			$bar = mysql_fetch_object($res);
			$akundebet12 = $bar->noakundebet;
			$akunkredit12 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB6\' ');
		$tmpKonter = fetchData($queryJ);
		$konter12 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal12 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB6/' . $konter12;
		$kodeJurnal12 = 'KNTB6';
	}

	$nojurnal62 = '';

	if ($param['komponentunjlistrik'] == 62) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB42\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet62 = '';
			$akunkredit62 = '';
			$bar = mysql_fetch_object($res);
			$akundebet62 = $bar->noakundebet;
			$akunkredit62 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB42\' ');
		$tmpKonter = fetchData($queryJ);
		$konter62 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal62 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB42/' . $konter62;
		$kodeJurnal62 = 'KNTB42';
	}

	$nojurnal22 = '';

	if ($param['komponentunjlain'] == 22) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet22 = '';
			$akunkredit22 = '';
			$bar = mysql_fetch_object($res);
			$akundebet22 = $bar->noakundebet;
			$akunkredit22 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter22 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal22 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB0/' . $konter22;
		$kodeJurnal22 = 'KNTB0';
	}

	$nojurnal54 = '';

	if ($param['komponentunjrapel'] == 54) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet54 = '';
			$akunkredit54 = '';
			$bar = mysql_fetch_object($res);
			$akundebet54 = $bar->noakundebet;
			$akunkredit54 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter54 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal54 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB0/' . $konter54;
		$kodeJurnal54 = 'KNTB0';
	}

	$nojurnal6 = '';

	if ($param['komponentunjjkk'] == 6) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB4\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet6 = '';
			$akunkredit6 = '';
			$bar = mysql_fetch_object($res);
			$akundebet6 = $bar->noakundebet;
			$akunkredit6 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB4\' ');
		$tmpKonter = fetchData($queryJ);
		$konter6 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal6 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB4/' . $konter6;
		$kodeJurnal6 = 'KNTB4';
	}

	$nojurnal7 = '';

	if ($param['komponentunjjkm'] == 7) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB5\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet7 = '';
			$akunkredit7 = '';
			$bar = mysql_fetch_object($res);
			$akundebet7 = $bar->noakundebet;
			$akunkredit7 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB5\' ');
		$tmpKonter = fetchData($queryJ);
		$konter7 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal7 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB5/' . $konter7;
		$kodeJurnal7 = 'KNTB5';
	}

	$nojurnal57 = '';

	if ($param['komponentunjbpjskes'] == 57) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB37\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet57 = '';
			$akunkredit57 = '';
			$bar = mysql_fetch_object($res);
			$akundebet57 = $bar->noakundebet;
			$akunkredit57 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB37\' ');
		$tmpKonter = fetchData($queryJ);
		$konter57 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal57 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB37/' . $konter57;
		$kodeJurnal57 = 'KNTB37';
	}

	$nojurnal5 = '';

	if ($param['komponenpotjhtkar'] == 5) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB45\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet5 = '';
			$akunkredit5 = '';
			$bar = mysql_fetch_object($res);
			$akundebet5 = $bar->noakundebet;
			$akunkredit5 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB45\' ');
		$tmpKonter = fetchData($queryJ);
		$konter5 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal5 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB45/' . $konter5;
		$kodeJurnal5 = 'KNTB45';
	}

	$nojurnal9 = '';

	if ($param['komponenpotjpkar'] == 9) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB47\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet9 = '';
			$akunkredit9 = '';
			$bar = mysql_fetch_object($res);
			$akundebet9 = $bar->noakundebet;
			$akunkredit9 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB47\' ');
		$tmpKonter = fetchData($queryJ);
		$konter9 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal9 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB47/' . $konter9;
		$kodeJurnal9 = 'KNTB47';
	}

	$nojurnal24 = '';

	if ($param['komponenpotpph21'] == 24) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB53\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet24 = '';
			$akunkredit24 = '';
			$bar = mysql_fetch_object($res);
			$akundebet24 = $bar->noakundebet;
			$akunkredit24 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB53\' ');
		$tmpKonter = fetchData($queryJ);
		$konter24 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal24 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB53/' . $konter24;
		$kodeJurnal24 = 'KNTB53';
	}

	$nojurnal25 = '';

	if ($param['komponenpotkoperasi'] == 25) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB55\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet25 = '';
			$akunkredit25 = '';
			$bar = mysql_fetch_object($res);
			$akundebet25 = $bar->noakundebet;
			$akunkredit25 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB55\' ');
		$tmpKonter = fetchData($queryJ);
		$konter25 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal25 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB55/' . $konter25;
		$kodeJurnal25 = 'KNTB55';
	}

	$nojurnal52 = '';

	if ($param['komponenpotvop'] == 52) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB58\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet52 = '';
			$akunkredit52 = '';
			$bar = mysql_fetch_object($res);
			$akundebet52 = $bar->noakundebet;
			$akunkredit52 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB58\' ');
		$tmpKonter = fetchData($queryJ);
		$konter52 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal52 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB58/' . $konter52;
		$kodeJurnal52 = 'KNTB58';
	}

	$nojurnal10 = '';

	if ($param['komponenpotmotor'] == 10) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB48\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet10 = '';
			$akunkredit10 = '';
			$bar = mysql_fetch_object($res);
			$akundebet10 = $bar->noakundebet;
			$akunkredit10 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB48\' ');
		$tmpKonter = fetchData($queryJ);
		$konter10 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal10 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB48/' . $konter10;
		$kodeJurnal10 = 'KNTB48';
	}

	$nojurnal11 = '';

	if ($param['komponenpotlaptop'] == 11) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB49\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet11 = '';
			$akunkredit11 = '';
			$bar = mysql_fetch_object($res);
			$akundebet11 = $bar->noakundebet;
			$akunkredit11 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB49\' ');
		$tmpKonter = fetchData($queryJ);
		$konter11 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal11 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB49/' . $konter11;
		$kodeJurnal11 = 'KNTB49';
	}

	$nojurnal64 = '';

	if ($param['komponenpotdenda'] == 64) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB62\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet64 = '';
			$akunkredit64 = '';
			$bar = mysql_fetch_object($res);
			$akundebet64 = $bar->noakundebet;
			$akunkredit64 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB62\' ');
		$tmpKonter = fetchData($queryJ);
		$konter64 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal64 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB62/' . $konter64;
		$kodeJurnal64 = 'KNTB62';
	}

	$nojurnal8 = '';

	if ($param['komponenpotbpjskes'] == 8) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KNTB46\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet8 = '';
			$akunkredit8 = '';
			$bar = mysql_fetch_object($res);
			$akundebet8 = $bar->noakundebet;
			$akunkredit8 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KNTB46\' ');
		$tmpKonter = fetchData($queryJ);
		$konter8 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal8 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KNTB46/' . $konter8;
		$kodeJurnal8 = 'KNTB46';
	}

	$dataRes['header'] = '';
	$dataRes2['header'] = '';
	$dataRes29['header'] = '';
	$dataRes14['header'] = '';
	$dataRes13['header'] = '';
	$dataRes61['header'] = '';
	$dataRes65['header'] = '';
	$dataRes60['header'] = '';
	$dataRes21['header'] = '';
	$dataRes23['header'] = '';
	$dataRes12['header'] = '';
	$dataRes62['header'] = '';
	$dataRes22['header'] = '';
	$dataRes54['header'] = '';
	$dataRes6['header'] = '';
	$dataRes7['header'] = '';
	$dataRes57['header'] = '';
	$dataRes66['header'] = '';
	$dataRes5['header'] = '';
	$dataRes9['header'] = '';
	$dataRes24['header'] = '';
	$dataRes25['header'] = '';
	$dataRes52['header'] = '';
	$dataRes10['header'] = '';
	$dataRes11['header'] = '';
	$dataRes64['header'] = '';
	$dataRes8['header'] = '';
	$dataResTunjAll['header'] = '';
	$dataRes['detail'] = '';
	$dataRes2['detail'] = '';
	$dataRes29['detail'] = '';
	$dataRes14['detail'] = '';
	$dataRes13['detail'] = '';
	$dataRes61['detail'] = '';
	$dataRes65['detail'] = '';
	$dataRes60['detail'] = '';
	$dataRes21['detail'] = '';
	$dataRes23['detail'] = '';
	$dataRes12['detail'] = '';
	$dataRes62['detail'] = '';
	$dataRes22['detail'] = '';
	$dataRes54['detail'] = '';
	$dataRes6['detail'] = '';
	$dataRes7['detail'] = '';
	$dataRes57['detail'] = '';
	$dataRes66['detail'] = '';
	$dataRes5['detail'] = '';
	$dataRes9['detail'] = '';
	$dataRes24['detail'] = '';
	$dataRes25['detail'] = '';
	$dataRes52['detail'] = '';
	$dataRes10['detail'] = '';
	$dataRes11['detail'] = '';
	$dataRes64['detail'] = '';
	$dataRes8['detail'] = '';
	$dataResTunjAll['detail'] = '';
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => 'KNTB0', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'totalkredit' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	if (($dataRes['header']['totaldebet'] == '') || ($dataRes['header']['totalkredit'] == '') || ($dataRes['header']['totaldebet'] == 0) || ($dataRes['header']['totalkredit'] == 0)) {
	}
	else {
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

	$dataRes2['header'] = array('nojurnal' => $nojurnal17, 'kodejurnal' => 'KNTB11', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totlembur'], 'totalkredit' => -1 * $param['totlembur'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUruttunjab = 1;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akundebet17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akunkredit17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	if (($dataRes2['header']['totaldebet'] == '') || ($dataRes2['header']['totalkredit'] == '') || ($dataRes2['header']['totaldebet'] == 0) || ($dataRes2['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

		if (!mysql_query($insHead2)) {
			$headErr2 .= 'Insert Header BTL 67 Error : ' . mysql_error() . "\n";
		}

		if ($headErr2 == '') {
			$detailErr2 = '';

			foreach ($dataRes2['detail'] as $row) {
				$insDet2 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr2 .= 'Insert Detail Error 17: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr2 == '') {
				$updJurnal17 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter17), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal17 . '\'');

				if (!mysql_query($updJurnal17)) {
					echo 'Update Kode Jurnal 17 Error : ' . mysql_error() . "\n";
					$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

					if (!mysql_query($RBDet17)) {
						echo 'Rollback Delete Header BTL 17 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr2;
				$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

				if (!mysql_query($RBDet17)) {
					echo 'Rollback Delete Header 17 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr2;
			exit();
		}
	}

	$dataRes29['header'] = array('nojurnal' => $nojurnal29, 'kodejurnal' => 'KNTB43', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjkom'], 'totalkredit' => -1 * $param['tottunjkom'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_KOMUNIKASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut29 = 1;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akundebet29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akunkredit29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	if (($dataRes29['header']['totaldebet'] == '') || ($dataRes29['header']['totalkredit'] == '') || ($dataRes29['header']['totaldebet'] == 0) || ($dataRes29['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead29 = insertQuery($dbname, 'keu_jurnalht', $dataRes29['header']);

		if (!mysql_query($insHead29)) {
			$headErr29 .= 'Insert Header BTL29 Error : ' . mysql_error() . "\n";
		}

		if ($headErr29 == '') {
			$detailErr29 = '';

			foreach ($dataRes29['detail'] as $row) {
				$insDet29 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr29 .= 'Insert Detail Error 29: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr29 == '') {
				$updJurnal29 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter29), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal29 . '\'');

				if (!mysql_query($updJurnal29)) {
					echo 'Update Kode Jurnal 29 Error : ' . mysql_error() . "\n";
					$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

					if (!mysql_query($RBDet29)) {
						echo 'Rollback Delete Header BTL 29 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr29;
				$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

				if (!mysql_query($RBDet29)) {
					echo 'Rollback Delete Header 29 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr29;
			exit();
		}
	}

	$dataRes14['header'] = array('nojurnal' => $nojurnal14, 'kodejurnal' => 'KNTB38', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlok'], 'totalkredit' => -1 * $param['tottunjlok'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut14 = 1;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akundebet14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akunkredit14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	if (($dataRes14['header']['totaldebet'] == '') || ($dataRes14['header']['totalkredit'] == '') || ($dataRes14['header']['totaldebet'] == 0) || ($dataRes14['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead14 = insertQuery($dbname, 'keu_jurnalht', $dataRes14['header']);

		if (!mysql_query($insHead14)) {
			$headErr14 .= 'Insert Header BTL 14 Error : ' . mysql_error() . "\n";
		}

		if ($headErr14 == '') {
			$detailErr14 = '';

			foreach ($dataRes14['detail'] as $row) {
				$insDet14 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr14 .= 'Insert Detail Error 14: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr14 == '') {
				$updJurnal14 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter14), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal14 . '\'');

				if (!mysql_query($updJurnal14)) {
					echo 'Update Kode Jurnal 14 Error : ' . mysql_error() . "\n";
					$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

					if (!mysql_query($RBDet14)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr14;
				$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

				if (!mysql_query($RBDet14)) {
					echo 'Rollback Delete Header 14 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr14;
			exit();
		}
	}

	$dataRes13['header'] = array('nojurnal' => $nojurnal13, 'kodejurnal' => 'KNTB39', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjprt'], 'totalkredit' => -1 * $param['tottunjprt'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut13 = 1;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akundebet13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akunkredit13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	if (($dataRes13['header']['totaldebet'] == '') || ($dataRes13['header']['totalkredit'] == '') || ($dataRes13['header']['totaldebet'] == 0) || ($dataRes13['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead13 = insertQuery($dbname, 'keu_jurnalht', $dataRes13['header']);

		if (!mysql_query($insHead13)) {
			$headErr13 .= 'Insert Header BTL 13 Error : ' . mysql_error() . "\n";
		}

		if ($headErr13 == '') {
			$detailErr13 = '';

			foreach ($dataRes13['detail'] as $row) {
				$insDet13 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr13 .= 'Insert Detail Error 13: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr13 == '') {
				$updJurnal13 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter13), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal13 . '\'');

				if (!mysql_query($updJurnal13)) {
					echo 'Update Kode Jurnal 13 Error : ' . mysql_error() . "\n";
					$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

					if (!mysql_query($RBDet13)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr13;
				$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

				if (!mysql_query($RBDet13)) {
					echo 'Rollback Delete Header 13 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr13;
			exit();
		}
	}

	$dataRes61['header'] = array('nojurnal' => $nojurnal61, 'kodejurnal' => 'KNTB41', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbbm'], 'totalkredit' => -1 * $param['tottunjbbm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut61 = 1;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akundebet61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akunkredit61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	if (($dataRes61['header']['totaldebet'] == '') || ($dataRes61['header']['totalkredit'] == '') || ($dataRes61['header']['totaldebet'] == 0) || ($dataRes61['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead61 = insertQuery($dbname, 'keu_jurnalht', $dataRes61['header']);

		if (!mysql_query($insHead61)) {
			$headErr61 .= 'Insert Header BTL 61 Error : ' . mysql_error() . "\n";
		}

		if ($headErr61 == '') {
			$detailErr61 = '';

			foreach ($dataRes61['detail'] as $row) {
				$insDet61 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr61 .= 'Insert Detail Error 61: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr61 == '') {
				$updJurnal61 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter61), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal61 . '\'');

				if (!mysql_query($updJurnal61)) {
					echo 'Update Kode Jurnal 61 Error : ' . mysql_error() . "\n";
					$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

					if (!mysql_query($RBDet61)) {
						echo 'Rollback Delete Header BTL 61 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr61;
				$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

				if (!mysql_query($RBDet61)) {
					echo 'Rollback Delete Header 61  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr61;
			exit();
		}
	}

	$dataRes65['header'] = array('nojurnal' => $nojurnal65, 'kodejurnal' => 'KNTB44', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjair'], 'totalkredit' => -1 * $param['tottunjair'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut65 = 1;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akundebet65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akunkredit65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	if (($dataRes65['header']['totaldebet'] == '') || ($dataRes65['header']['totalkredit'] == '') || ($dataRes65['header']['totaldebet'] == 0) || ($dataRes65['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead65 = insertQuery($dbname, 'keu_jurnalht', $dataRes65['header']);

		if (!mysql_query($insHead65)) {
			$headErr65 .= 'Insert Header BTL 65 Error : ' . mysql_error() . "\n";
		}

		if ($headErr65 == '') {
			$detailErr65 = '';

			foreach ($dataRes65['detail'] as $row) {
				$insDet65 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr65 .= 'Insert Detail Error 65: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr65 == '') {
				$updJurnal65 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter65), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal65 . '\'');

				if (!mysql_query($updJurnal65)) {
					echo 'Update Kode Jurnal 65 Error : ' . mysql_error() . "\n";
					$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

					if (!mysql_query($RBDet65)) {
						echo 'Rollback Delete Header BTL 65 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr65;
				$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

				if (!mysql_query($RBDet65)) {
					echo 'Rollback Delete Header 65  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr65;
			exit();
		}
	}

	$dataRes60['header'] = array('nojurnal' => $nojurnal60, 'kodejurnal' => 'KNTB40', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjspart'], 'totalkredit' => -1 * $param['tottunjspart'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut60 = 1;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akundebet60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akunkredit60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	if (($dataRes60['header']['totaldebet'] == '') || ($dataRes60['header']['totalkredit'] == '') || ($dataRes60['header']['totaldebet'] == 0) || ($dataRes60['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead60 = insertQuery($dbname, 'keu_jurnalht', $dataRes60['header']);

		if (!mysql_query($insHead60)) {
			$headErr60 .= 'Insert Header BTL 60 Error : ' . mysql_error() . "\n";
		}

		if ($headErr60 == '') {
			$detailErr60 = '';

			foreach ($dataRes60['detail'] as $row) {
				$insDet60 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr60 .= 'Insert Detail Error 60: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr60 == '') {
				$updJurnal60 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter60), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal60 . '\'');

				if (!mysql_query($updJurnal60)) {
					echo 'Update Kode Jurnal 60 Error : ' . mysql_error() . "\n";
					$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

					if (!mysql_query($RBDet60)) {
						echo 'Rollback Delete Header BTL 60 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr60;
				$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

				if (!mysql_query($RBDet60)) {
					echo 'Rollback Delete Header 60  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr60;
			exit();
		}
	}

	$dataRes21['header'] = array('nojurnal' => $nojurnal21, 'kodejurnal' => 'KNTB12', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjharian'], 'totalkredit' => -1 * $param['tottunjharian'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut21 = 1;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akundebet21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akunkredit21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	if (($dataRes21['header']['totaldebet'] == '') || ($dataRes21['header']['totalkredit'] == '') || ($dataRes21['header']['totaldebet'] == 0) || ($dataRes21['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead21 = insertQuery($dbname, 'keu_jurnalht', $dataRes21['header']);

		if (!mysql_query($insHead21)) {
			$headErr21 .= 'Insert Header BTL 21 Error : ' . mysql_error() . "\n";
		}

		if ($headErr21 == '') {
			$detailErr21 = '';

			foreach ($dataRes21['detail'] as $row) {
				$insDet21 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr21 .= 'Insert Detail Error 21: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr21 == '') {
				$updJurnal21 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter21), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal21 . '\'');

				if (!mysql_query($updJurnal21)) {
					echo 'Update Kode Jurnal 21 Error : ' . mysql_error() . "\n";
					$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

					if (!mysql_query($RBDet21)) {
						echo 'Rollback Delete Header BTL 21 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr21;
				$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

				if (!mysql_query($RBDet21)) {
					echo 'Rollback Delete Header 21  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr21;
			exit();
		}
	}

	$dataRes23['header'] = array('nojurnal' => $nojurnal23, 'kodejurnal' => 'KNTB14', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjdinas'], 'totalkredit' => -1 * $param['tottunjdinas'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut23 = 1;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akundebet23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akunkredit23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	if (($dataRes23['header']['totaldebet'] == '') || ($dataRes23['header']['totalkredit'] == '') || ($dataRes23['header']['totaldebet'] == 0) || ($dataRes23['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead23 = insertQuery($dbname, 'keu_jurnalht', $dataRes23['header']);

		if (!mysql_query($insHead23)) {
			$headErr23 .= 'Insert Header BTL 23 Error : ' . mysql_error() . "\n";
		}

		if ($headErr23 == '') {
			$detailErr23 = '';

			foreach ($dataRes23['detail'] as $row) {
				$insDet23 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr23 .= 'Insert Detail Error 23: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr23 == '') {
				$updJurnal23 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter23), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal23 . '\'');

				if (!mysql_query($updJurnal23)) {
					echo 'Update Kode Jurnal 23 Error : ' . mysql_error() . "\n";
					$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

					if (!mysql_query($RBDet23)) {
						echo 'Rollback Delete Header BTL 23 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr23;
				$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

				if (!mysql_query($RBDet23)) {
					echo 'Rollback Delete Header 23  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr23;
			exit();
		}
	}

	$dataRes12['header'] = array('nojurnal' => $nojurnal12, 'kodejurnal' => 'KNTB6', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjcuti'], 'totalkredit' => -1 * $param['tottunjcuti'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut12 = 1;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akundebet12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akunkredit12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	if (($dataRes12['header']['totaldebet'] == '') || ($dataRes12['header']['totalkredit'] == '') || ($dataRes12['header']['totaldebet'] == 0) || ($dataRes12['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead12 = insertQuery($dbname, 'keu_jurnalht', $dataRes12['header']);

		if (!mysql_query($insHead12)) {
			$headErr12 .= 'Insert Header BTL 12 Error : ' . mysql_error() . "\n";
		}

		if ($headErr12 == '') {
			$detailErr12 = '';

			foreach ($dataRes12['detail'] as $row) {
				$insDet12 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr12 .= 'Insert Detail Error 12: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr12 == '') {
				$updJurnal12 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter12), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal12 . '\'');

				if (!mysql_query($updJurnal12)) {
					echo 'Update Kode Jurnal 12 Error : ' . mysql_error() . "\n";
					$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

					if (!mysql_query($RBDet12)) {
						echo 'Rollback Delete Header BTL 12 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr12;
				$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

				if (!mysql_query($RBDet12)) {
					echo 'Rollback Delete Header 12  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr12;
			exit();
		}
	}

	$dataRes62['header'] = array('nojurnal' => $nojurnal62, 'kodejurnal' => 'KNTB42', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlistrik'], 'totalkredit' => -1 * $param['tottunjlistrik'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut62 = 1;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akundebet62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akunkredit62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	if (($dataRes62['header']['totaldebet'] == '') || ($dataRes62['header']['totalkredit'] == '') || ($dataRes62['header']['totaldebet'] == 0) || ($dataRes62['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead62 = insertQuery($dbname, 'keu_jurnalht', $dataRes62['header']);

		if (!mysql_query($insHead62)) {
			$headErr62 .= 'Insert Header BTL 62 Error : ' . mysql_error() . "\n";
		}

		if ($headErr62 == '') {
			$detailErr62 = '';

			foreach ($dataRes62['detail'] as $row) {
				$insDet62 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr62 .= 'Insert Detail Error 62: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr62 == '') {
				$updJurnal62 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter62), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal62 . '\'');

				if (!mysql_query($updJurnal62)) {
					echo 'Update Kode Jurnal 62 Error : ' . mysql_error() . "\n";
					$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

					if (!mysql_query($RBDet62)) {
						echo 'Rollback Delete Header BTL 62 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr62;
				$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

				if (!mysql_query($RBDet62)) {
					echo 'Rollback Delete Header 62  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr62;
			exit();
		}
	}

	$dataRes6['header'] = array('nojurnal' => $nojurnal6, 'kodejurnal' => 'KNTB4', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkk'], 'totalkredit' => -1 * $param['tottunjjkk'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut6 = 1;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akundebet6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akunkredit6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	if (($dataRes6['header']['totaldebet'] == '') || ($dataRes6['header']['totalkredit'] == '') || ($dataRes6['header']['totaldebet'] == 0) || ($dataRes6['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead6 = insertQuery($dbname, 'keu_jurnalht', $dataRes6['header']);

		if (!mysql_query($insHead6)) {
			$headErr6 .= 'Insert Header BTL 6 Error : ' . mysql_error() . "\n";
		}

		if ($headErr6 == '') {
			$detailErr6 = '';

			foreach ($dataRes6['detail'] as $row) {
				$insDet6 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr6 .= 'Insert Detail Error 6: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr6 == '') {
				$updJurnal6 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter6), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal6 . '\'');

				if (!mysql_query($updJurnal6)) {
					echo 'Update Kode Jurnal 6 Error : ' . mysql_error() . "\n";
					$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

					if (!mysql_query($RBDet6)) {
						echo 'Rollback Delete Header BTL 6 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr6;
				$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

				if (!mysql_query($RBDet6)) {
					echo 'Rollback Delete Header 6  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr6;
			exit();
		}
	}

	$dataRes7['header'] = array('nojurnal' => $nojurnal7, 'kodejurnal' => 'KNTB5', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkm'], 'totalkredit' => -1 * $param['tottunjjkm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut7 = 1;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akundebet7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akunkredit7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	if (($dataRes7['header']['totaldebet'] == '') || ($dataRes7['header']['totalkredit'] == '') || ($dataRes7['header']['totaldebet'] == 0) || ($dataRes7['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead7 = insertQuery($dbname, 'keu_jurnalht', $dataRes7['header']);

		if (!mysql_query($insHead7)) {
			$headErr7 .= 'Insert Header BTL 7 Error : ' . mysql_error() . "\n";
		}

		if ($headErr7 == '') {
			$detailErr7 = '';

			foreach ($dataRes7['detail'] as $row) {
				$insDet7 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr7 .= 'Insert Detail Error 7: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr7 == '') {
				$updJurnal7 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter7), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal7 . '\'');

				if (!mysql_query($updJurnal7)) {
					echo 'Update Kode Jurnal 7 Error : ' . mysql_error() . "\n";
					$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

					if (!mysql_query($RBDet7)) {
						echo 'Rollback Delete Header BTL 7 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr7;
				$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

				if (!mysql_query($RBDet7)) {
					echo 'Rollback Delete Header 7  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr7;
			exit();
		}
	}

	$dataRes57['header'] = array('nojurnal' => $nojurnal57, 'kodejurnal' => 'KNTB37', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbpjskes'], 'totalkredit' => -1 * $param['tottunjbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut57 = 1;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akundebet57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akunkredit57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	if (($dataRes57['header']['totaldebet'] == '') || ($dataRes57['header']['totalkredit'] == '') || ($dataRes57['header']['totaldebet'] == 0) || ($dataRes57['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead57 = insertQuery($dbname, 'keu_jurnalht', $dataRes57['header']);

		if (!mysql_query($insHead57)) {
			$headErr57 .= 'Insert Header BTL 57 Error : ' . mysql_error() . "\n";
		}

		if ($headErr57 == '') {
			$detailErr57 = '';

			foreach ($dataRes57['detail'] as $row) {
				$insDet57 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr57 .= 'Insert Detail Error 57: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr57 == '') {
				$updJurnal57 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter57), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal57 . '\'');

				if (!mysql_query($updJurnal57)) {
					echo 'Update Kode Jurnal 57 Error : ' . mysql_error() . "\n";
					$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

					if (!mysql_query($RBDet57)) {
						echo 'Rollback Delete Header BTL 57 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr57;
				$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

				if (!mysql_query($RBDet57)) {
					echo 'Rollback Delete Header 57  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr57;
			exit();
		}
	}

	$dataRes5['header'] = array('nojurnal' => $nojurnal5, 'kodejurnal' => 'KNTB45', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjhtkar'], 'totalkredit' => -1 * $param['totpotjhtkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut5 = 1;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akundebet5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akunkredit5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	if (($dataRes5['header']['totaldebet'] == '') || ($dataRes5['header']['totalkredit'] == '') || ($dataRes5['header']['totaldebet'] == 0) || ($dataRes5['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead5 = insertQuery($dbname, 'keu_jurnalht', $dataRes5['header']);

		if (!mysql_query($insHead5)) {
			$headErr5 .= 'Insert Header BTL 5 Error : ' . mysql_error() . "\n";
		}

		if ($headErr5 == '') {
			$detailErr5 = '';

			foreach ($dataRes5['detail'] as $row) {
				$insDet5 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr5 .= 'Insert Detail Error 5: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr5 == '') {
				$updJurnal5 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter5), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal5 . '\'');

				if (!mysql_query($updJurnal5)) {
					echo 'Update Kode Jurnal 5 Error : ' . mysql_error() . "\n";
					$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

					if (!mysql_query($RBDet5)) {
						echo 'Rollback Delete Header BTL 5 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr5;
				$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

				if (!mysql_query($RBDet5)) {
					echo 'Rollback Delete Header 5  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr5;
			exit();
		}
	}

	$dataRes9['header'] = array('nojurnal' => $nojurnal9, 'kodejurnal' => 'KNTB47', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjpkar'], 'totalkredit' => -1 * $param['totpotjpkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut9 = 1;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akundebet9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akunkredit9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	if (($dataRes9['header']['totaldebet'] == '') || ($dataRes9['header']['totalkredit'] == '') || ($dataRes9['header']['totaldebet'] == 0) || ($dataRes9['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead9 = insertQuery($dbname, 'keu_jurnalht', $dataRes9['header']);

		if (!mysql_query($insHead9)) {
			$headErr9 .= 'Insert Header BTL 9 Error : ' . mysql_error() . "\n";
		}

		if ($headErr9 == '') {
			$detailErr9 = '';

			foreach ($dataRes9['detail'] as $row) {
				$insDet9 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr9 .= 'Insert Detail Error 9: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr9 == '') {
				$updJurnal9 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter9), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal9 . '\'');

				if (!mysql_query($updJurnal9)) {
					echo 'Update Kode Jurnal 9 Error : ' . mysql_error() . "\n";
					$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

					if (!mysql_query($RBDet9)) {
						echo 'Rollback Delete Header BTL 9 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr9;
				$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

				if (!mysql_query($RBDet9)) {
					echo 'Rollback Delete Header 9  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr9;
			exit();
		}
	}

	$dataRes24['header'] = array('nojurnal' => $nojurnal24, 'kodejurnal' => 'KNTB53', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotpph21'], 'totalkredit' => -1 * $param['totpotpph21'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut24 = 1;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akundebet24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akunkredit24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	if (($dataRes24['header']['totaldebet'] == '') || ($dataRes24['header']['totalkredit'] == '') || ($dataRes24['header']['totaldebet'] == 0) || ($dataRes24['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead24 = insertQuery($dbname, 'keu_jurnalht', $dataRes24['header']);

		if (!mysql_query($insHead24)) {
			$headErr24 .= 'Insert Header BTL 24 Error : ' . mysql_error() . "\n";
		}

		if ($headErr24 == '') {
			$detailErr24 = '';

			foreach ($dataRes24['detail'] as $row) {
				$insDet24 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr24 .= 'Insert Detail Error 24: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr24 == '') {
				$updJurnal24 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter24), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal24 . '\'');

				if (!mysql_query($updJurnal24)) {
					echo 'Update Kode Jurnal 24 Error : ' . mysql_error() . "\n";
					$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

					if (!mysql_query($RBDet24)) {
						echo 'Rollback Delete Header BTL 24 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr24;
				$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

				if (!mysql_query($RBDet24)) {
					echo 'Rollback Delete Header 24  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr24;
			exit();
		}
	}

	$dataRes25['header'] = array('nojurnal' => $nojurnal25, 'kodejurnal' => 'KNTB55', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotkoperasi'], 'totalkredit' => -1 * $param['totpotkoperasi'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut25 = 1;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akundebet25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akunkredit25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	if (($dataRes25['header']['totaldebet'] == '') || ($dataRes25['header']['totalkredit'] == '') || ($dataRes25['header']['totaldebet'] == 0) || ($dataRes25['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead25 = insertQuery($dbname, 'keu_jurnalht', $dataRes25['header']);

		if (!mysql_query($insHead25)) {
			$headErr25 .= 'Insert Header BTL 25 Error : ' . mysql_error() . "\n";
		}

		if ($headErr25 == '') {
			$detailErr25 = '';

			foreach ($dataRes25['detail'] as $row) {
				$insDet25 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr25 .= 'Insert Detail Error 25: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr25 == '') {
				$updJurnal25 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter25), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal25 . '\'');

				if (!mysql_query($updJurnal25)) {
					echo 'Update Kode Jurnal 25 Error : ' . mysql_error() . "\n";
					$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

					if (!mysql_query($RBDet25)) {
						echo 'Rollback Delete Header BTL 25 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr25;
				$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

				if (!mysql_query($RBDet25)) {
					echo 'Rollback Delete Header 25  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr25;
			exit();
		}
	}

	$dataRes52['header'] = array('nojurnal' => $nojurnal52, 'kodejurnal' => 'KNTB58', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotvop'], 'totalkredit' => -1 * $param['totpotvop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut52 = 1;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akundebet52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akunkredit52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	if (($dataRes52['header']['totaldebet'] == '') || ($dataRes52['header']['totalkredit'] == '') || ($dataRes52['header']['totaldebet'] == 0) || ($dataRes52['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead52 = insertQuery($dbname, 'keu_jurnalht', $dataRes52['header']);

		if (!mysql_query($insHead52)) {
			$headErr52 .= 'Insert Header BTL 52 Error : ' . mysql_error() . "\n";
		}

		if ($headErr52 == '') {
			$detailErr52 = '';

			foreach ($dataRes52['detail'] as $row) {
				$insDet52 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr52 .= 'Insert Detail Error 52: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr52 == '') {
				$updJurnal52 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter52), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal52 . '\'');

				if (!mysql_query($updJurnal52)) {
					echo 'Update Kode Jurnal 52 Error : ' . mysql_error() . "\n";
					$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

					if (!mysql_query($RBDet52)) {
						echo 'Rollback Delete Header BTL 52 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr52;
				$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

				if (!mysql_query($RBDet52)) {
					echo 'Rollback Delete Header 52  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr52;
			exit();
		}
	}

	$dataRes10['header'] = array('nojurnal' => $nojurnal10, 'kodejurnal' => 'KNTB48', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotmotor'], 'totalkredit' => -1 * $param['totpotmotor'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut10 = 1;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akundebet10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akunkredit10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	if (($dataRes10['header']['totaldebet'] == '') || ($dataRes10['header']['totalkredit'] == '') || ($dataRes10['header']['totaldebet'] == 0) || ($dataRes10['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead10 = insertQuery($dbname, 'keu_jurnalht', $dataRes10['header']);

		if (!mysql_query($insHead10)) {
			$headErr10 .= 'Insert Header BTL 10 Error : ' . mysql_error() . "\n";
		}

		if ($headErr10 == '') {
			$detailErr10 = '';

			foreach ($dataRes10['detail'] as $row) {
				$insDet10 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr10 .= 'Insert Detail Error 10: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr10 == '') {
				$updJurnal10 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter10), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal10 . '\'');

				if (!mysql_query($updJurnal10)) {
					echo 'Update Kode Jurnal 10 Error : ' . mysql_error() . "\n";
					$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

					if (!mysql_query($RBDet10)) {
						echo 'Rollback Delete Header BTL 10 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr10;
				$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

				if (!mysql_query($RBDet10)) {
					echo 'Rollback Delete Header 10  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr10;
			exit();
		}
	}

	$dataRes11['header'] = array('nojurnal' => $nojurnal11, 'kodejurnal' => 'KNTB49', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotlaptop'], 'totalkredit' => -1 * $param['totpotlaptop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut11 = 1;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akundebet11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akunkredit11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	if (($dataRes11['header']['totaldebet'] == '') || ($dataRes11['header']['totalkredit'] == '') || ($dataRes11['header']['totaldebet'] == 0) || ($dataRes11['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead11 = insertQuery($dbname, 'keu_jurnalht', $dataRes11['header']);

		if (!mysql_query($insHead11)) {
			$headErr11 .= 'Insert Header BTL 11 Error : ' . mysql_error() . "\n";
		}

		if ($headErr11 == '') {
			$detailErr11 = '';

			foreach ($dataRes11['detail'] as $row) {
				$insDet11 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr11 .= 'Insert Detail Error 11: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr11 == '') {
				$updJurnal11 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter11), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal11 . '\'');

				if (!mysql_query($updJurnal11)) {
					echo 'Update Kode Jurnal 11 Error : ' . mysql_error() . "\n";
					$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

					if (!mysql_query($RBDet11)) {
						echo 'Rollback Delete Header BTL 11 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr11;
				$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

				if (!mysql_query($RBDet11)) {
					echo 'Rollback Delete Header 11  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr11;
			exit();
		}
	}

	$dataRes64['header'] = array('nojurnal' => $nojurnal64, 'kodejurnal' => 'KNTB62', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotdenda'], 'totalkredit' => -1 * $param['totpotdenda'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut64 = 1;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akundebet64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akunkredit64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	if (($dataRes64['header']['totaldebet'] == '') || ($dataRes64['header']['totalkredit'] == '') || ($dataRes64['header']['totaldebet'] == 0) || ($dataRes64['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead64 = insertQuery($dbname, 'keu_jurnalht', $dataRes64['header']);

		if (!mysql_query($insHead64)) {
			$headErr64 .= 'Insert Header BTL 64 Error : ' . mysql_error() . "\n";
		}

		if ($headErr64 == '') {
			$detailErr64 = '';

			foreach ($dataRes64['detail'] as $row) {
				$insDet64 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr64 .= 'Insert Detail Error 64: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr64 == '') {
				$updJurnal64 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter64), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal64 . '\'');

				if (!mysql_query($updJurnal64)) {
					echo 'Update Kode Jurnal 64 Error : ' . mysql_error() . "\n";
					$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

					if (!mysql_query($RBDet64)) {
						echo 'Rollback Delete Header BTL 64 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr64;
				$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

				if (!mysql_query($RBDet64)) {
					echo 'Rollback Delete Header 64  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr64;
			exit();
		}
	}

	$dataRes8['header'] = array('nojurnal' => $nojurnal8, 'kodejurnal' => 'KNTB46', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotbpjskes'], 'totalkredit' => -1 * $param['totpotbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut8 = 1;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akundebet8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akunkredit8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	if (($dataRes8['header']['totaldebet'] == '') || ($dataRes8['header']['totalkredit'] == '') || ($dataRes8['header']['totaldebet'] == 0) || ($dataRes8['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead8 = insertQuery($dbname, 'keu_jurnalht', $dataRes8['header']);

		if (!mysql_query($insHead8)) {
			$headErr8 .= 'Insert Header BTL 8 Error : ' . mysql_error() . "\n";
		}

		if ($headErr8 == '') {
			$detailErr8 = '';

			foreach ($dataRes8['detail'] as $row) {
				$insDet8 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr8 .= 'Insert Detail Error 8: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr8 == '') {
				$updJurnal8 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter8), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal8 . '\'');

				if (!mysql_query($updJurnal8)) {
					echo 'Update Kode Jurnal 8 Error : ' . mysql_error() . "\n";
					$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

					if (!mysql_query($RBDet8)) {
						echo 'Rollback Delete Header BTL 8 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr8;
				$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

				if (!mysql_query($RBDet8)) {
					echo 'Rollback Delete Header 8  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr8;
			exit();
		}
	}
}

function prosesGajiKebun()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	global $periode;
	$periode = $param['periode'];
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$group = 'KBNB0';
	}
	else if ($param['komponenlembur'] == 17) {
		$group = 'KBNB11';
	}
	else if ($param['komponentunjpremi'] == 16) {
		$group = 'KBNB10';
	}
	else if ($param['komponentunjkom'] == 63) {
		$group = 'KBNB43';
	}
	else if ($param['komponentunjlok'] == 58) {
		$group = 'KBNB38';
	}
	else if ($param['komponentunjprt'] == 59) {
		$group = 'KBNB39';
	}
	else if ($param['komponentunjbbm'] == 61) {
		$group = 'KBNB41';
	}
	else if ($param['komponentunjair'] == 65) {
		$group = 'KBNB44';
	}
	else if ($param['komponentunjspart'] == 60) {
		$group = 'KBNB40';
	}
	else if ($param['komponentunjharian'] == 21) {
		$group = 'KBNB12';
	}
	else if ($param['komponentunjdinas'] == 23) {
		$group = 'KBNB14';
	}
	else if ($param['komponentunjcuti'] == 12) {
		$group = 'KBNB6';
	}
	else if ($param['komponentunjlistrik'] == 62) {
		$group = 'KBNB42';
	}
	else if ($param['komponentunjjkk'] == 6) {
		$group = 'KBNB4';
	}
	else if ($param['komponentunjjkm'] == 7) {
		$group = 'KBNB5';
	}
	else if ($param['komponentunjbpjskes'] == 57) {
		$group = 'KBNB37';
	}
	else if ($param['komponenpotjhtkar'] == 5) {
		$group = 'KBNB45';
	}
	else if ($param['komponenpotjpkar'] == 9) {
		$group = 'KBNB47';
	}
	else if ($param['komponenpotpph21'] == 24) {
		$group = 'KBNB54';
	}
	else if ($param['komponenpotkoperasi'] == 25) {
		$group = 'KBNB55';
	}
	else if ($param['komponenpotvop'] == 52) {
		$group = 'KBNB58';
	}
	else if ($param['komponenpotmotor'] == 10) {
		$group = 'KBNB48';
	}
	else if ($param['komponenpotlaptop'] == 11) {
		$group = 'KBNB49';
	}
	else if ($param['komponenpotdenda'] == 64) {
		$group = 'KBNB62';
	}
	else if ($param['komponenpotbpjskes'] == 8) {
		$group = 'KBNB46';
	}
	else if ($param['komponenpotdendapanen'] == 26) {
		$group = 'KBNB56';
	}
	else {
		$group = 'KBNB99';
	}

	$nojurnal = '';
	if (($param['komponen'] == 1) || ($param['komponentunjlain'] == 22) || ($param['komponentunjrapel'] == 54)) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameter jurnal KBNB0 belum ada. untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet = '';
			$akunkredit = '';
			$bar = mysql_fetch_object($res);
			$akundebet = $bar->noakundebet;
			$akunkredit = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB0/' . $konter;
		$kodeJurnal = 'KBNB0';
	}

	$nojurnal17 = '';

	if ($param['komponenlembur'] == 17) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB11\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameter jurnal KBNB11 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet17 = '';
			$akunkredit17 = '';
			$bar = mysql_fetch_object($res);
			$akundebet17 = $bar->noakundebet;
			$akunkredit17 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB11\' ');
		$tmpKonter = fetchData($queryJ);
		$konter17 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal17 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB11/' . $konter17;
		$kodeJurnal17 = 'KBNB11';
	}

	$nojurnal16 = '';

	if ($param['komponentunjpremi'] == 16) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB10\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameter jurnal KBNB10 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet16 = '';
			$akunkredit16 = '';
			$bar = mysql_fetch_object($res);
			$akundebet16 = $bar->noakundebet;
			$akunkredit16 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB10\' ');
		$tmpKonter = fetchData($queryJ);
		$konter16 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal16 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB10/' . $konter16;
		$kodeJurnal16 = 'KBNB10';
	}

	$nojurnal29 = '';

	if ($param['komponentunjkom'] == 63) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB43\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB43 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet29 = '';
			$akunkredit29 = '';
			$bar = mysql_fetch_object($res);
			$akundebet29 = $bar->noakundebet;
			$akunkredit29 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB43\' ');
		$tmpKonter = fetchData($queryJ);
		$konter29 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal29 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB43/' . $konter29;
		$kodeJurnal29 = 'KBNB43';
	}

	$nojurnal14 = '';

	if ($param['komponentunjlok'] == 58) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB38\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB38 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet14 = '';
			$akunkredit14 = '';
			$bar = mysql_fetch_object($res);
			$akundebet14 = $bar->noakundebet;
			$akunkredit14 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB38\' ');
		$tmpKonter = fetchData($queryJ);
		$konter14 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal14 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB38/' . $konter14;
		$kodeJurnal14 = 'KBNB38';
	}

	$nojurnal13 = '';

	if ($param['komponentunjprt'] == 59) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB39\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB39 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet13 = '';
			$akunkredit13 = '';
			$bar = mysql_fetch_object($res);
			$akundebet13 = $bar->noakundebet;
			$akunkredit13 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB39\' ');
		$tmpKonter = fetchData($queryJ);
		$konter13 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal13 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB39/' . $konter13;
		$kodeJurnal13 = 'KBNB39';
	}

	$nojurnal61 = '';

	if ($param['komponentunjbbm'] == 61) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB41\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB41 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet61 = '';
			$akunkredit61 = '';
			$bar = mysql_fetch_object($res);
			$akundebet61 = $bar->noakundebet;
			$akunkredit61 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB41\' ');
		$tmpKonter = fetchData($queryJ);
		$konter61 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal61 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB41/' . $konter61;
		$kodeJurnal61 = 'KBNB41';
	}

	$nojurnal65 = '';

	if ($param['komponentunjair'] == 65) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB44\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB44 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet65 = '';
			$akunkredit65 = '';
			$bar = mysql_fetch_object($res);
			$akundebet65 = $bar->noakundebet;
			$akunkredit65 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB44\' ');
		$tmpKonter = fetchData($queryJ);
		$konter65 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal65 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB44/' . $konter65;
		$kodeJurnal65 = 'KBNB44';
	}

	$nojurnal60 = '';

	if ($param['komponentunjspart'] == 60) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB40\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB40 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet60 = '';
			$akunkredit60 = '';
			$bar = mysql_fetch_object($res);
			$akundebet60 = $bar->noakundebet;
			$akunkredit60 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB40\' ');
		$tmpKonter = fetchData($queryJ);
		$konter60 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal60 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB40/' . $konter60;
		$kodeJurnal60 = 'KBNB40';
	}

	$nojurnal21 = '';

	if ($param['komponentunjharian'] == 21) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB12\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB12 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet21 = '';
			$akunkredit21 = '';
			$bar = mysql_fetch_object($res);
			$akundebet21 = $bar->noakundebet;
			$akunkredit21 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB12\' ');
		$tmpKonter = fetchData($queryJ);
		$konter21 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal21 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB12/' . $konter21;
		$kodeJurnal21 = 'KBNB12';
	}

	$nojurnal23 = '';

	if ($param['komponentunjdinas'] == 23) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB14\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB14 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet23 = '';
			$akunkredit23 = '';
			$bar = mysql_fetch_object($res);
			$akundebet23 = $bar->noakundebet;
			$akunkredit23 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB14\' ');
		$tmpKonter = fetchData($queryJ);
		$konter23 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal23 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB14/' . $konter23;
		$kodeJurnal23 = 'KBNB14';
	}

	$nojurnal12 = '';

	if ($param['komponentunjcuti'] == 12) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB6\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB6 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet12 = '';
			$akunkredit12 = '';
			$bar = mysql_fetch_object($res);
			$akundebet12 = $bar->noakundebet;
			$akunkredit12 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB6\' ');
		$tmpKonter = fetchData($queryJ);
		$konter12 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal12 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB6/' . $konter12;
		$kodeJurnal12 = 'KBNB6';
	}

	$nojurnal62 = '';

	if ($param['komponentunjlistrik'] == 62) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB42\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB42 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet62 = '';
			$akunkredit62 = '';
			$bar = mysql_fetch_object($res);
			$akundebet62 = $bar->noakundebet;
			$akunkredit62 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB42\' ');
		$tmpKonter = fetchData($queryJ);
		$konter62 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal62 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB42/' . $konter62;
		$kodeJurnal62 = 'KBNB42';
	}

	$nojurnal22 = '';

	if ($param['komponentunjlain'] == 22) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB0 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet22 = '';
			$akunkredit22 = '';
			$bar = mysql_fetch_object($res);
			$akundebet22 = $bar->noakundebet;
			$akunkredit22 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter22 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal22 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB0/' . $konter22;
		$kodeJurnal22 = 'KBNB0';
	}

	$nojurnal54 = '';

	if ($param['komponentunjrapel'] == 54) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB0\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB0 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet54 = '';
			$akunkredit54 = '';
			$bar = mysql_fetch_object($res);
			$akundebet54 = $bar->noakundebet;
			$akunkredit54 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB0\' ');
		$tmpKonter = fetchData($queryJ);
		$konter54 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal54 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB0/' . $konter54;
		$kodeJurnal54 = 'KBNB0';
	}

	$nojurnal6 = '';

	if ($param['komponentunjjkk'] == 6) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB4\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB4 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet6 = '';
			$akunkredit6 = '';
			$bar = mysql_fetch_object($res);
			$akundebet6 = $bar->noakundebet;
			$akunkredit6 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB4\' ');
		$tmpKonter = fetchData($queryJ);
		$konter6 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal6 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB4/' . $konter6;
		$kodeJurnal6 = 'KBNB4';
	}

	$nojurnal7 = '';

	if ($param['komponentunjjkm'] == 7) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB5\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB5 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet7 = '';
			$akunkredit7 = '';
			$bar = mysql_fetch_object($res);
			$akundebet7 = $bar->noakundebet;
			$akunkredit7 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB5\' ');
		$tmpKonter = fetchData($queryJ);
		$konter7 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal7 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB5/' . $konter7;
		$kodeJurnal7 = 'KBNB5';
	}

	$nojurnal57 = '';

	if ($param['komponentunjbpjskes'] == 57) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB37\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB37 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet57 = '';
			$akunkredit57 = '';
			$bar = mysql_fetch_object($res);
			$akundebet57 = $bar->noakundebet;
			$akunkredit57 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB37\' ');
		$tmpKonter = fetchData($queryJ);
		$konter57 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal57 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB37/' . $konter57;
		$kodeJurnal57 = 'KBNB37';
	}

	$nojurnal5 = '';

	if ($param['komponenpotjhtkar'] == 5) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB45\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB45 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet5 = '';
			$akunkredit5 = '';
			$bar = mysql_fetch_object($res);
			$akundebet5 = $bar->noakundebet;
			$akunkredit5 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB45\' ');
		$tmpKonter = fetchData($queryJ);
		$konter5 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal5 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB45/' . $konter5;
		$kodeJurnal5 = 'KBNB45';
	}

	$nojurnal9 = '';

	if ($param['komponenpotjpkar'] == 9) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB47\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB47 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet9 = '';
			$akunkredit9 = '';
			$bar = mysql_fetch_object($res);
			$akundebet9 = $bar->noakundebet;
			$akunkredit9 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB47\' ');
		$tmpKonter = fetchData($queryJ);
		$konter9 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal9 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB47/' . $konter9;
		$kodeJurnal9 = 'KBNB47';
	}

	$nojurnal24 = '';

	if ($param['komponenpotpph21'] == 24) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB54\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB54 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet24 = '';
			$akunkredit24 = '';
			$bar = mysql_fetch_object($res);
			$akundebet24 = $bar->noakundebet;
			$akunkredit24 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB54\' ');
		$tmpKonter = fetchData($queryJ);
		$konter24 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal24 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB54/' . $konter24;
		$kodeJurnal24 = 'KBNB54';
	}

	$nojurnal25 = '';

	if ($param['komponenpotkoperasi'] == 25) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB55\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB55 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet25 = '';
			$akunkredit25 = '';
			$bar = mysql_fetch_object($res);
			$akundebet25 = $bar->noakundebet;
			$akunkredit25 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB55\' ');
		$tmpKonter = fetchData($queryJ);
		$konter25 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal25 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB55/' . $konter25;
		$kodeJurnal25 = 'KBNB55';
	}

	$nojurnal52 = '';

	if ($param['komponenpotvop'] == 52) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB58\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB58 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet52 = '';
			$akunkredit52 = '';
			$bar = mysql_fetch_object($res);
			$akundebet52 = $bar->noakundebet;
			$akunkredit52 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB58\' ');
		$tmpKonter = fetchData($queryJ);
		$konter52 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal52 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB58/' . $konter52;
		$kodeJurnal52 = 'KBNB58';
	}

	$nojurnal10 = '';

	if ($param['komponenpotmotor'] == 10) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB48\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB48 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet10 = '';
			$akunkredit10 = '';
			$bar = mysql_fetch_object($res);
			$akundebet10 = $bar->noakundebet;
			$akunkredit10 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB48\' ');
		$tmpKonter = fetchData($queryJ);
		$konter10 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal10 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB48/' . $konter10;
		$kodeJurnal10 = 'KBNB48';
	}

	$nojurnal11 = '';

	if ($param['komponenpotlaptop'] == 11) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB49\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB49 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet11 = '';
			$akunkredit11 = '';
			$bar = mysql_fetch_object($res);
			$akundebet11 = $bar->noakundebet;
			$akunkredit11 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB49\' ');
		$tmpKonter = fetchData($queryJ);
		$konter11 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal11 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB49/' . $konter11;
		$kodeJurnal11 = 'KBNB49';
	}

	$nojurnal64 = '';

	if ($param['komponenpotdenda'] == 64) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB62\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB62 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet64 = '';
			$akunkredit64 = '';
			$bar = mysql_fetch_object($res);
			$akundebet64 = $bar->noakundebet;
			$akunkredit64 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB62\' ');
		$tmpKonter = fetchData($queryJ);
		$konter64 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal64 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB62/' . $konter64;
		$kodeJurnal64 = 'KBNB62';
	}

	$nojurnal8 = '';

	if ($param['komponenpotbpjskes'] == 8) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB46\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB46 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet8 = '';
			$akunkredit8 = '';
			$bar = mysql_fetch_object($res);
			$akundebet8 = $bar->noakundebet;
			$akunkredit8 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB46\' ');
		$tmpKonter = fetchData($queryJ);
		$konter8 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal8 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB46/' . $konter8;
		$kodeJurnal8 = 'KBNB46';
	}

	$nojurnal26 = '';

	if ($param['komponenpotdendapanen'] == 26) {
		$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'KBNB56\' limit 1';
		$res = mysql_query($str);

		if (mysql_num_rows($res) < 1) {
			exit('Error: No.Akun pada parameterjurnal KBNB56 belum ada untuk ' . $param['namakomponen']);
		}
		else {
			$akundebet26 = '';
			$akunkredit26 = '';
			$bar = mysql_fetch_object($res);
			$akundebet26 = $bar->noakundebet;
			$akunkredit26 = $bar->noakunkredit;
		}

		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'KBNB56\' ');
		$tmpKonter = fetchData($queryJ);
		$konter26 = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal26 = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/KBNB56/' . $konter26;
		$kodeJurnal26 = 'KBNB56';
	}

	$dataRes['header'] = '';
	$dataRes2['header'] = '';
	$dataRes16['header'] = '';
	$dataRes29['header'] = '';
	$dataRes14['header'] = '';
	$dataRes13['header'] = '';
	$dataRes61['header'] = '';
	$dataRes65['header'] = '';
	$dataRes60['header'] = '';
	$dataRes21['header'] = '';
	$dataRes23['header'] = '';
	$dataRes12['header'] = '';
	$dataRes62['header'] = '';
	$dataRes22['header'] = '';
	$dataRes54['header'] = '';
	$dataRes6['header'] = '';
	$dataRes7['header'] = '';
	$dataRes57['header'] = '';
	$dataRes66['header'] = '';
	$dataRes5['header'] = '';
	$dataRes9['header'] = '';
	$dataRes24['header'] = '';
	$dataRes25['header'] = '';
	$dataRes52['header'] = '';
	$dataRes10['header'] = '';
	$dataRes11['header'] = '';
	$dataRes64['header'] = '';
	$dataRes8['header'] = '';
	$dataRes26['header'] = '';
	$dataResTunjAll['header'] = '';
	$dataRes['detail'] = '';
	$dataRes2['detail'] = '';
	$dataRes16['detail'] = '';
	$dataRes29['detail'] = '';
	$dataRes14['detail'] = '';
	$dataRes13['detail'] = '';
	$dataRes61['detail'] = '';
	$dataRes65['detail'] = '';
	$dataRes60['detail'] = '';
	$dataRes21['detail'] = '';
	$dataRes23['detail'] = '';
	$dataRes12['detail'] = '';
	$dataRes62['detail'] = '';
	$dataRes22['detail'] = '';
	$dataRes54['detail'] = '';
	$dataRes6['detail'] = '';
	$dataRes7['detail'] = '';
	$dataRes57['detail'] = '';
	$dataRes66['detail'] = '';
	$dataRes5['detail'] = '';
	$dataRes9['detail'] = '';
	$dataRes24['detail'] = '';
	$dataRes25['detail'] = '';
	$dataRes52['detail'] = '';
	$dataRes10['detail'] = '';
	$dataRes11['detail'] = '';
	$dataRes64['detail'] = '';
	$dataRes8['detail'] = '';
	$dataRes26['detail'] = '';
	$dataResTunjAll['detail'] = '';
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => 'KBNB0', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'totalkredit' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => $param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'TOTAL_GAPOK+TUNJTETAP_PERIODE :' . ' ' . $periode, 'jumlah' => -1 * ($param['tottunjtetap'] + $param['tottunjlain'] + $param['tottunjrapel']), 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	if (($dataRes['header']['totaldebet'] == '') || ($dataRes['header']['totalkredit'] == '') || ($dataRes['header']['totaldebet'] == 0) || ($dataRes['header']['totalkredit'] == 0)) {
	}
	else {
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

	$dataRes2['header'] = array('nojurnal' => $nojurnal17, 'kodejurnal' => 'KBNB11', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totlembur'], 'totalkredit' => -1 * $param['totlembur'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUruttunjab = 1;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akundebet17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal17, 'tanggal' => $tanggal, 'nourut' => $noUruttunjab, 'noakun' => $akunkredit17, 'keterangan' => 'TOTAL_LEMBUR_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totlembur'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUruttunjab;
	if (($dataRes2['header']['totaldebet'] == '') || ($dataRes2['header']['totalkredit'] == '') || ($dataRes2['header']['totaldebet'] == 0) || ($dataRes2['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

		if (!mysql_query($insHead2)) {
			$headErr2 .= 'Insert Header BTL 67 Error : ' . mysql_error() . "\n";
		}

		if ($headErr2 == '') {
			$detailErr2 = '';

			foreach ($dataRes2['detail'] as $row) {
				$insDet2 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr2 .= 'Insert Detail Error 17: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr2 == '') {
				$updJurnal17 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter17), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal17 . '\'');

				if (!mysql_query($updJurnal17)) {
					echo 'Update Kode Jurnal 17 Error : ' . mysql_error() . "\n";
					$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

					if (!mysql_query($RBDet17)) {
						echo 'Rollback Delete Header BTL 17 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr2;
				$RBDet17 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal17 . '\'');

				if (!mysql_query($RBDet17)) {
					echo 'Rollback Delete Header 17 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr2;
			exit();
		}
	}

	$dataRes16['header'] = array('nojurnal' => $nojurnal16, 'kodejurnal' => 'KBNB10', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpremi'], 'totalkredit' => -1 * $param['totpremi'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_PREMI_BULANAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrutpremi = 1;
	$dataRes16['detail'][] = array('nojurnal' => $nojurnal16, 'tanggal' => $tanggal, 'nourut' => $noUrutpremi, 'noakun' => $akundebet16, 'keterangan' => 'TOTAL_PREMI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpremi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrutpremi;
	$dataRes16['detail'][] = array('nojurnal' => $nojurnal16, 'tanggal' => $tanggal, 'nourut' => $noUrutpremi, 'noakun' => $akunkredit16, 'keterangan' => 'TOTAL_PREMI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpremi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrutpremi;
	if (($dataRes16['header']['totaldebet'] == '') || ($dataRes16['header']['totalkredit'] == '') || ($dataRes16['header']['totaldebet'] == 0) || ($dataRes16['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead16 = insertQuery($dbname, 'keu_jurnalht', $dataRes16['header']);

		if (!mysql_query($insHead16)) {
			$headErr16 .= 'Insert Header BTL 16 Error : ' . mysql_error() . "\n";
		}

		if ($headErr16 == '') {
			$detailErr16 = '';

			foreach ($dataRes16['detail'] as $row) {
				$insDet16 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr16 .= 'Insert Detail Error 16: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr16 == '') {
				$updJurnal16 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter16), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal16 . '\'');

				if (!mysql_query($updJurnal16)) {
					echo 'Update Kode Jurnal 16 Error : ' . mysql_error() . "\n";
					$RBDet16 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal16 . '\'');

					if (!mysql_query($RBDet16)) {
						echo 'Rollback Delete Header BTL 16 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr16;
				$RBDet16 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal16 . '\'');

				if (!mysql_query($RBDet16)) {
					echo 'Rollback Delete Header 16 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr16;
			exit();
		}
	}

	$dataRes29['header'] = array('nojurnal' => $nojurnal29, 'kodejurnal' => 'KBNB43', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjkom'], 'totalkredit' => -1 * $param['tottunjkom'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_KOMUNIKASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut29 = 1;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akundebet29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	$dataRes29['detail'][] = array('nojurnal' => $nojurnal29, 'tanggal' => $tanggal, 'nourut' => $noUrut29, 'noakun' => $akunkredit29, 'keterangan' => 'TOTAL_TUNJKOMUNIKASI_BULANAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjkom'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut29;
	if (($dataRes29['header']['totaldebet'] == '') || ($dataRes29['header']['totalkredit'] == '') || ($dataRes29['header']['totaldebet'] == 0) || ($dataRes29['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead29 = insertQuery($dbname, 'keu_jurnalht', $dataRes29['header']);

		if (!mysql_query($insHead29)) {
			$headErr29 .= 'Insert Header BTL29 Error : ' . mysql_error() . "\n";
		}

		if ($headErr29 == '') {
			$detailErr29 = '';

			foreach ($dataRes29['detail'] as $row) {
				$insDet29 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr29 .= 'Insert Detail Error 29: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr29 == '') {
				$updJurnal29 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter29), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal29 . '\'');

				if (!mysql_query($updJurnal29)) {
					echo 'Update Kode Jurnal 29 Error : ' . mysql_error() . "\n";
					$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

					if (!mysql_query($RBDet29)) {
						echo 'Rollback Delete Header BTL 29 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr29;
				$RBDet29 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal29 . '\'');

				if (!mysql_query($RBDet29)) {
					echo 'Rollback Delete Header 29 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr29;
			exit();
		}
	}

	$dataRes14['header'] = array('nojurnal' => $nojurnal14, 'kodejurnal' => 'KBNB38', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlok'], 'totalkredit' => -1 * $param['tottunjlok'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut14 = 1;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akundebet14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	$dataRes14['detail'][] = array('nojurnal' => $nojurnal14, 'tanggal' => $tanggal, 'nourut' => $noUrut14, 'noakun' => $akunkredit14, 'keterangan' => 'TOTAL_TUNJ_LOK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlok'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut14;
	if (($dataRes14['header']['totaldebet'] == '') || ($dataRes14['header']['totalkredit'] == '') || ($dataRes14['header']['totaldebet'] == 0) || ($dataRes14['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead14 = insertQuery($dbname, 'keu_jurnalht', $dataRes14['header']);

		if (!mysql_query($insHead14)) {
			$headErr14 .= 'Insert Header BTL 14 Error : ' . mysql_error() . "\n";
		}

		if ($headErr14 == '') {
			$detailErr14 = '';

			foreach ($dataRes14['detail'] as $row) {
				$insDet14 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr14 .= 'Insert Detail Error 14: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr14 == '') {
				$updJurnal14 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter14), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal14 . '\'');

				if (!mysql_query($updJurnal14)) {
					echo 'Update Kode Jurnal 14 Error : ' . mysql_error() . "\n";
					$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

					if (!mysql_query($RBDet14)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr14;
				$RBDet14 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal14 . '\'');

				if (!mysql_query($RBDet14)) {
					echo 'Rollback Delete Header 14 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr14;
			exit();
		}
	}

	$dataRes13['header'] = array('nojurnal' => $nojurnal13, 'kodejurnal' => 'KBNB39', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjprt'], 'totalkredit' => -1 * $param['tottunjprt'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut13 = 1;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akundebet13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	$dataRes13['detail'][] = array('nojurnal' => $nojurnal13, 'tanggal' => $tanggal, 'nourut' => $noUrut13, 'noakun' => $akunkredit13, 'keterangan' => 'TOTAL_TUNJ_PRT_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjprt'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut13;
	if (($dataRes13['header']['totaldebet'] == '') || ($dataRes13['header']['totalkredit'] == '') || ($dataRes13['header']['totaldebet'] == 0) || ($dataRes13['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead13 = insertQuery($dbname, 'keu_jurnalht', $dataRes13['header']);

		if (!mysql_query($insHead13)) {
			$headErr13 .= 'Insert Header BTL 13 Error : ' . mysql_error() . "\n";
		}

		if ($headErr13 == '') {
			$detailErr13 = '';

			foreach ($dataRes13['detail'] as $row) {
				$insDet13 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr13 .= 'Insert Detail Error 13: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr13 == '') {
				$updJurnal13 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter13), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal13 . '\'');

				if (!mysql_query($updJurnal13)) {
					echo 'Update Kode Jurnal 13 Error : ' . mysql_error() . "\n";
					$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

					if (!mysql_query($RBDet13)) {
						echo 'Rollback Delete Header BTL 14 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr13;
				$RBDet13 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal13 . '\'');

				if (!mysql_query($RBDet13)) {
					echo 'Rollback Delete Header 13 Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr13;
			exit();
		}
	}

	$dataRes61['header'] = array('nojurnal' => $nojurnal61, 'kodejurnal' => 'KBNB41', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbbm'], 'totalkredit' => -1 * $param['tottunjbbm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut61 = 1;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akundebet61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	$dataRes61['detail'][] = array('nojurnal' => $nojurnal61, 'tanggal' => $tanggal, 'nourut' => $noUrut61, 'noakun' => $akunkredit61, 'keterangan' => 'TOTAL_TUNJ_BBM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbbm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut61;
	if (($dataRes61['header']['totaldebet'] == '') || ($dataRes61['header']['totalkredit'] == '') || ($dataRes61['header']['totaldebet'] == 0) || ($dataRes61['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead61 = insertQuery($dbname, 'keu_jurnalht', $dataRes61['header']);

		if (!mysql_query($insHead61)) {
			$headErr61 .= 'Insert Header BTL 61 Error : ' . mysql_error() . "\n";
		}

		if ($headErr61 == '') {
			$detailErr61 = '';

			foreach ($dataRes61['detail'] as $row) {
				$insDet61 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr61 .= 'Insert Detail Error 61: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr61 == '') {
				$updJurnal61 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter61), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal61 . '\'');

				if (!mysql_query($updJurnal61)) {
					echo 'Update Kode Jurnal 61 Error : ' . mysql_error() . "\n";
					$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

					if (!mysql_query($RBDet61)) {
						echo 'Rollback Delete Header BTL 61 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr61;
				$RBDet61 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal61 . '\'');

				if (!mysql_query($RBDet61)) {
					echo 'Rollback Delete Header 61  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr61;
			exit();
		}
	}

	$dataRes65['header'] = array('nojurnal' => $nojurnal65, 'kodejurnal' => 'KBNB44', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjair'], 'totalkredit' => -1 * $param['tottunjair'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut65 = 1;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akundebet65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	$dataRes65['detail'][] = array('nojurnal' => $nojurnal65, 'tanggal' => $tanggal, 'nourut' => $noUrut65, 'noakun' => $akunkredit65, 'keterangan' => 'TOTAL_TUNJ_AIRMINUM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjair'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut65;
	if (($dataRes65['header']['totaldebet'] == '') || ($dataRes65['header']['totalkredit'] == '') || ($dataRes65['header']['totaldebet'] == 0) || ($dataRes65['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead65 = insertQuery($dbname, 'keu_jurnalht', $dataRes65['header']);

		if (!mysql_query($insHead65)) {
			$headErr65 .= 'Insert Header BTL 65 Error : ' . mysql_error() . "\n";
		}

		if ($headErr65 == '') {
			$detailErr65 = '';

			foreach ($dataRes65['detail'] as $row) {
				$insDet65 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr65 .= 'Insert Detail Error 65: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr65 == '') {
				$updJurnal65 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter65), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal65 . '\'');

				if (!mysql_query($updJurnal65)) {
					echo 'Update Kode Jurnal 65 Error : ' . mysql_error() . "\n";
					$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

					if (!mysql_query($RBDet65)) {
						echo 'Rollback Delete Header BTL 65 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr65;
				$RBDet65 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal65 . '\'');

				if (!mysql_query($RBDet65)) {
					echo 'Rollback Delete Header 65  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr65;
			exit();
		}
	}

	$dataRes60['header'] = array('nojurnal' => $nojurnal60, 'kodejurnal' => 'KBNB40', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjspart'], 'totalkredit' => -1 * $param['tottunjspart'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut60 = 1;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akundebet60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	$dataRes60['detail'][] = array('nojurnal' => $nojurnal60, 'tanggal' => $tanggal, 'nourut' => $noUrut60, 'noakun' => $akunkredit60, 'keterangan' => 'TOTAL_TUNJ_SP-PART_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjspart'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut60;
	if (($dataRes60['header']['totaldebet'] == '') || ($dataRes60['header']['totalkredit'] == '') || ($dataRes60['header']['totaldebet'] == 0) || ($dataRes60['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead60 = insertQuery($dbname, 'keu_jurnalht', $dataRes60['header']);

		if (!mysql_query($insHead60)) {
			$headErr60 .= 'Insert Header BTL 60 Error : ' . mysql_error() . "\n";
		}

		if ($headErr60 == '') {
			$detailErr60 = '';

			foreach ($dataRes60['detail'] as $row) {
				$insDet60 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr60 .= 'Insert Detail Error 60: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr60 == '') {
				$updJurnal60 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter60), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal60 . '\'');

				if (!mysql_query($updJurnal60)) {
					echo 'Update Kode Jurnal 60 Error : ' . mysql_error() . "\n";
					$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

					if (!mysql_query($RBDet60)) {
						echo 'Rollback Delete Header BTL 60 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr60;
				$RBDet60 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal60 . '\'');

				if (!mysql_query($RBDet60)) {
					echo 'Rollback Delete Header 60  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr60;
			exit();
		}
	}

	$dataRes21['header'] = array('nojurnal' => $nojurnal21, 'kodejurnal' => 'KBNB12', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjharian'], 'totalkredit' => -1 * $param['tottunjharian'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut21 = 1;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akundebet21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	$dataRes21['detail'][] = array('nojurnal' => $nojurnal21, 'tanggal' => $tanggal, 'nourut' => $noUrut21, 'noakun' => $akunkredit21, 'keterangan' => 'TOTAL_TUNJ_HARIAN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjharian'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut21;
	if (($dataRes21['header']['totaldebet'] == '') || ($dataRes21['header']['totalkredit'] == '') || ($dataRes21['header']['totaldebet'] == 0) || ($dataRes21['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead21 = insertQuery($dbname, 'keu_jurnalht', $dataRes21['header']);

		if (!mysql_query($insHead21)) {
			$headErr21 .= 'Insert Header BTL 21 Error : ' . mysql_error() . "\n";
		}

		if ($headErr21 == '') {
			$detailErr21 = '';

			foreach ($dataRes21['detail'] as $row) {
				$insDet21 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr21 .= 'Insert Detail Error 21: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr21 == '') {
				$updJurnal21 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter21), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal21 . '\'');

				if (!mysql_query($updJurnal21)) {
					echo 'Update Kode Jurnal 21 Error : ' . mysql_error() . "\n";
					$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

					if (!mysql_query($RBDet21)) {
						echo 'Rollback Delete Header BTL 21 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr21;
				$RBDet21 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal21 . '\'');

				if (!mysql_query($RBDet21)) {
					echo 'Rollback Delete Header 21  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr21;
			exit();
		}
	}

	$dataRes23['header'] = array('nojurnal' => $nojurnal23, 'kodejurnal' => 'KBNB14', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjdinas'], 'totalkredit' => -1 * $param['tottunjdinas'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut23 = 1;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akundebet23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	$dataRes23['detail'][] = array('nojurnal' => $nojurnal23, 'tanggal' => $tanggal, 'nourut' => $noUrut23, 'noakun' => $akunkredit23, 'keterangan' => 'TOTAL_TUNJ_DINAS_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjdinas'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut23;
	if (($dataRes23['header']['totaldebet'] == '') || ($dataRes23['header']['totalkredit'] == '') || ($dataRes23['header']['totaldebet'] == 0) || ($dataRes23['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead23 = insertQuery($dbname, 'keu_jurnalht', $dataRes23['header']);

		if (!mysql_query($insHead23)) {
			$headErr23 .= 'Insert Header BTL 23 Error : ' . mysql_error() . "\n";
		}

		if ($headErr23 == '') {
			$detailErr23 = '';

			foreach ($dataRes23['detail'] as $row) {
				$insDet23 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr23 .= 'Insert Detail Error 23: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr23 == '') {
				$updJurnal23 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter23), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal23 . '\'');

				if (!mysql_query($updJurnal23)) {
					echo 'Update Kode Jurnal 23 Error : ' . mysql_error() . "\n";
					$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

					if (!mysql_query($RBDet23)) {
						echo 'Rollback Delete Header BTL 23 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr23;
				$RBDet23 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal23 . '\'');

				if (!mysql_query($RBDet23)) {
					echo 'Rollback Delete Header 23  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr23;
			exit();
		}
	}

	$dataRes12['header'] = array('nojurnal' => $nojurnal12, 'kodejurnal' => 'KBNB6', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjcuti'], 'totalkredit' => -1 * $param['tottunjcuti'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut12 = 1;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akundebet12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	$dataRes12['detail'][] = array('nojurnal' => $nojurnal12, 'tanggal' => $tanggal, 'nourut' => $noUrut12, 'noakun' => $akunkredit12, 'keterangan' => 'TOTAL_TUNJ_CUTI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjcuti'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut12;
	if (($dataRes12['header']['totaldebet'] == '') || ($dataRes12['header']['totalkredit'] == '') || ($dataRes12['header']['totaldebet'] == 0) || ($dataRes12['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead12 = insertQuery($dbname, 'keu_jurnalht', $dataRes12['header']);

		if (!mysql_query($insHead12)) {
			$headErr12 .= 'Insert Header BTL 12 Error : ' . mysql_error() . "\n";
		}

		if ($headErr12 == '') {
			$detailErr12 = '';

			foreach ($dataRes12['detail'] as $row) {
				$insDet12 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr12 .= 'Insert Detail Error 12: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr12 == '') {
				$updJurnal12 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter12), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal12 . '\'');

				if (!mysql_query($updJurnal12)) {
					echo 'Update Kode Jurnal 12 Error : ' . mysql_error() . "\n";
					$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

					if (!mysql_query($RBDet12)) {
						echo 'Rollback Delete Header BTL 12 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr12;
				$RBDet12 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal12 . '\'');

				if (!mysql_query($RBDet12)) {
					echo 'Rollback Delete Header 12  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr12;
			exit();
		}
	}

	$dataRes62['header'] = array('nojurnal' => $nojurnal62, 'kodejurnal' => 'KBNB42', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjlistrik'], 'totalkredit' => -1 * $param['tottunjlistrik'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut62 = 1;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akundebet62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	$dataRes62['detail'][] = array('nojurnal' => $nojurnal62, 'tanggal' => $tanggal, 'nourut' => $noUrut62, 'noakun' => $akunkredit62, 'keterangan' => 'TOTAL_TUNJ_LISTRIK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjlistrik'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut62;
	if (($dataRes62['header']['totaldebet'] == '') || ($dataRes62['header']['totalkredit'] == '') || ($dataRes62['header']['totaldebet'] == 0) || ($dataRes62['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead62 = insertQuery($dbname, 'keu_jurnalht', $dataRes62['header']);

		if (!mysql_query($insHead62)) {
			$headErr62 .= 'Insert Header BTL 62 Error : ' . mysql_error() . "\n";
		}

		if ($headErr62 == '') {
			$detailErr62 = '';

			foreach ($dataRes62['detail'] as $row) {
				$insDet62 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr62 .= 'Insert Detail Error 62: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr62 == '') {
				$updJurnal62 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter62), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal62 . '\'');

				if (!mysql_query($updJurnal62)) {
					echo 'Update Kode Jurnal 62 Error : ' . mysql_error() . "\n";
					$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

					if (!mysql_query($RBDet62)) {
						echo 'Rollback Delete Header BTL 62 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr62;
				$RBDet62 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal62 . '\'');

				if (!mysql_query($RBDet62)) {
					echo 'Rollback Delete Header 62  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr62;
			exit();
		}
	}

	$dataRes6['header'] = array('nojurnal' => $nojurnal6, 'kodejurnal' => 'KBNB4', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkk'], 'totalkredit' => -1 * $param['tottunjjkk'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut6 = 1;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akundebet6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	$dataRes6['detail'][] = array('nojurnal' => $nojurnal6, 'tanggal' => $tanggal, 'nourut' => $noUrut6, 'noakun' => $akunkredit6, 'keterangan' => 'TOTAL_TUNJ_JKK_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkk'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut6;
	if (($dataRes6['header']['totaldebet'] == '') || ($dataRes6['header']['totalkredit'] == '') || ($dataRes6['header']['totaldebet'] == 0) || ($dataRes6['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead6 = insertQuery($dbname, 'keu_jurnalht', $dataRes6['header']);

		if (!mysql_query($insHead6)) {
			$headErr6 .= 'Insert Header BTL 6 Error : ' . mysql_error() . "\n";
		}

		if ($headErr6 == '') {
			$detailErr6 = '';

			foreach ($dataRes6['detail'] as $row) {
				$insDet6 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr6 .= 'Insert Detail Error 6: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr6 == '') {
				$updJurnal6 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter6), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal6 . '\'');

				if (!mysql_query($updJurnal6)) {
					echo 'Update Kode Jurnal 6 Error : ' . mysql_error() . "\n";
					$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

					if (!mysql_query($RBDet6)) {
						echo 'Rollback Delete Header BTL 6 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr6;
				$RBDet6 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal6 . '\'');

				if (!mysql_query($RBDet6)) {
					echo 'Rollback Delete Header 6  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr6;
			exit();
		}
	}

	$dataRes7['header'] = array('nojurnal' => $nojurnal7, 'kodejurnal' => 'KBNB5', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjjkm'], 'totalkredit' => -1 * $param['tottunjjkm'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut7 = 1;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akundebet7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	$dataRes7['detail'][] = array('nojurnal' => $nojurnal7, 'tanggal' => $tanggal, 'nourut' => $noUrut7, 'noakun' => $akunkredit7, 'keterangan' => 'TOTAL_TUNJ_JKM_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjjkm'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut7;
	if (($dataRes7['header']['totaldebet'] == '') || ($dataRes7['header']['totalkredit'] == '') || ($dataRes7['header']['totaldebet'] == 0) || ($dataRes7['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead7 = insertQuery($dbname, 'keu_jurnalht', $dataRes7['header']);

		if (!mysql_query($insHead7)) {
			$headErr7 .= 'Insert Header BTL 7 Error : ' . mysql_error() . "\n";
		}

		if ($headErr7 == '') {
			$detailErr7 = '';

			foreach ($dataRes7['detail'] as $row) {
				$insDet7 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr7 .= 'Insert Detail Error 7: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr7 == '') {
				$updJurnal7 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter7), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal7 . '\'');

				if (!mysql_query($updJurnal7)) {
					echo 'Update Kode Jurnal 7 Error : ' . mysql_error() . "\n";
					$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

					if (!mysql_query($RBDet7)) {
						echo 'Rollback Delete Header BTL 7 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr7;
				$RBDet7 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal7 . '\'');

				if (!mysql_query($RBDet7)) {
					echo 'Rollback Delete Header 7  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr7;
			exit();
		}
	}

	$dataRes57['header'] = array('nojurnal' => $nojurnal57, 'kodejurnal' => 'KBNB37', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['tottunjbpjskes'], 'totalkredit' => -1 * $param['tottunjbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut57 = 1;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akundebet57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	$dataRes57['detail'][] = array('nojurnal' => $nojurnal57, 'tanggal' => $tanggal, 'nourut' => $noUrut57, 'noakun' => $akunkredit57, 'keterangan' => 'TOTAL_TUNJ_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['tottunjbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut57;
	if (($dataRes57['header']['totaldebet'] == '') || ($dataRes57['header']['totalkredit'] == '') || ($dataRes57['header']['totaldebet'] == 0) || ($dataRes57['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead57 = insertQuery($dbname, 'keu_jurnalht', $dataRes57['header']);

		if (!mysql_query($insHead57)) {
			$headErr57 .= 'Insert Header BTL 57 Error : ' . mysql_error() . "\n";
		}

		if ($headErr57 == '') {
			$detailErr57 = '';

			foreach ($dataRes57['detail'] as $row) {
				$insDet57 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr57 .= 'Insert Detail Error 57: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr57 == '') {
				$updJurnal57 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter57), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal57 . '\'');

				if (!mysql_query($updJurnal57)) {
					echo 'Update Kode Jurnal 57 Error : ' . mysql_error() . "\n";
					$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

					if (!mysql_query($RBDet57)) {
						echo 'Rollback Delete Header BTL 57 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr57;
				$RBDet57 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal57 . '\'');

				if (!mysql_query($RBDet57)) {
					echo 'Rollback Delete Header 57  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr57;
			exit();
		}
	}

	$dataRes5['header'] = array('nojurnal' => $nojurnal5, 'kodejurnal' => 'KBNB45', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjhtkar'], 'totalkredit' => -1 * $param['totpotjhtkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut5 = 1;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akundebet5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	$dataRes5['detail'][] = array('nojurnal' => $nojurnal5, 'tanggal' => $tanggal, 'nourut' => $noUrut5, 'noakun' => $akunkredit5, 'keterangan' => 'TOTAL_POT_JHTKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjhtkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut5;
	if (($dataRes5['header']['totaldebet'] == '') || ($dataRes5['header']['totalkredit'] == '') || ($dataRes5['header']['totaldebet'] == 0) || ($dataRes5['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead5 = insertQuery($dbname, 'keu_jurnalht', $dataRes5['header']);

		if (!mysql_query($insHead5)) {
			$headErr5 .= 'Insert Header BTL 5 Error : ' . mysql_error() . "\n";
		}

		if ($headErr5 == '') {
			$detailErr5 = '';

			foreach ($dataRes5['detail'] as $row) {
				$insDet5 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr5 .= 'Insert Detail Error 5: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr5 == '') {
				$updJurnal5 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter5), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal5 . '\'');

				if (!mysql_query($updJurnal5)) {
					echo 'Update Kode Jurnal 5 Error : ' . mysql_error() . "\n";
					$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

					if (!mysql_query($RBDet5)) {
						echo 'Rollback Delete Header BTL 5 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr5;
				$RBDet5 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal5 . '\'');

				if (!mysql_query($RBDet5)) {
					echo 'Rollback Delete Header 5  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr5;
			exit();
		}
	}

	$dataRes9['header'] = array('nojurnal' => $nojurnal9, 'kodejurnal' => 'KBNB47', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotjpkar'], 'totalkredit' => -1 * $param['totpotjpkar'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut9 = 1;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akundebet9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	$dataRes9['detail'][] = array('nojurnal' => $nojurnal9, 'tanggal' => $tanggal, 'nourut' => $noUrut9, 'noakun' => $akunkredit9, 'keterangan' => 'TOTAL_POT_JPKAR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotjpkar'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut9;
	if (($dataRes9['header']['totaldebet'] == '') || ($dataRes9['header']['totalkredit'] == '') || ($dataRes9['header']['totaldebet'] == 0) || ($dataRes9['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead9 = insertQuery($dbname, 'keu_jurnalht', $dataRes9['header']);

		if (!mysql_query($insHead9)) {
			$headErr9 .= 'Insert Header BTL 9 Error : ' . mysql_error() . "\n";
		}

		if ($headErr9 == '') {
			$detailErr9 = '';

			foreach ($dataRes9['detail'] as $row) {
				$insDet9 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr9 .= 'Insert Detail Error 9: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr9 == '') {
				$updJurnal9 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter9), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal9 . '\'');

				if (!mysql_query($updJurnal9)) {
					echo 'Update Kode Jurnal 9 Error : ' . mysql_error() . "\n";
					$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

					if (!mysql_query($RBDet9)) {
						echo 'Rollback Delete Header BTL 9 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr9;
				$RBDet9 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal9 . '\'');

				if (!mysql_query($RBDet9)) {
					echo 'Rollback Delete Header 9  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr9;
			exit();
		}
	}

	$dataRes24['header'] = array('nojurnal' => $nojurnal24, 'kodejurnal' => 'KBNB54', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotpph21'], 'totalkredit' => -1 * $param['totpotpph21'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut24 = 1;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akundebet24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	$dataRes24['detail'][] = array('nojurnal' => $nojurnal24, 'tanggal' => $tanggal, 'nourut' => $noUrut24, 'noakun' => $akunkredit24, 'keterangan' => 'TOTAL_POT_PPH21_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotpph21'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut24;
	if (($dataRes24['header']['totaldebet'] == '') || ($dataRes24['header']['totalkredit'] == '') || ($dataRes24['header']['totaldebet'] == 0) || ($dataRes24['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead24 = insertQuery($dbname, 'keu_jurnalht', $dataRes24['header']);

		if (!mysql_query($insHead24)) {
			$headErr24 .= 'Insert Header BTL 24 Error : ' . mysql_error() . "\n";
		}

		if ($headErr24 == '') {
			$detailErr24 = '';

			foreach ($dataRes24['detail'] as $row) {
				$insDet24 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr24 .= 'Insert Detail Error 24: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr24 == '') {
				$updJurnal24 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter24), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal24 . '\'');

				if (!mysql_query($updJurnal24)) {
					echo 'Update Kode Jurnal 24 Error : ' . mysql_error() . "\n";
					$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

					if (!mysql_query($RBDet24)) {
						echo 'Rollback Delete Header BTL 24 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr24;
				$RBDet24 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal24 . '\'');

				if (!mysql_query($RBDet24)) {
					echo 'Rollback Delete Header 24  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr24;
			exit();
		}
	}

	$dataRes25['header'] = array('nojurnal' => $nojurnal25, 'kodejurnal' => 'KBNB55', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotkoperasi'], 'totalkredit' => -1 * $param['totpotkoperasi'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut25 = 1;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akundebet25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	$dataRes25['detail'][] = array('nojurnal' => $nojurnal25, 'tanggal' => $tanggal, 'nourut' => $noUrut25, 'noakun' => $akunkredit25, 'keterangan' => 'TOTAL_POT_KOPERASI_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotkoperasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut25;
	if (($dataRes25['header']['totaldebet'] == '') || ($dataRes25['header']['totalkredit'] == '') || ($dataRes25['header']['totaldebet'] == 0) || ($dataRes25['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead25 = insertQuery($dbname, 'keu_jurnalht', $dataRes25['header']);

		if (!mysql_query($insHead25)) {
			$headErr25 .= 'Insert Header BTL 25 Error : ' . mysql_error() . "\n";
		}

		if ($headErr25 == '') {
			$detailErr25 = '';

			foreach ($dataRes25['detail'] as $row) {
				$insDet25 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr25 .= 'Insert Detail Error 25: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr25 == '') {
				$updJurnal25 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter25), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal25 . '\'');

				if (!mysql_query($updJurnal25)) {
					echo 'Update Kode Jurnal 25 Error : ' . mysql_error() . "\n";
					$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

					if (!mysql_query($RBDet25)) {
						echo 'Rollback Delete Header BTL 25 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr25;
				$RBDet25 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal25 . '\'');

				if (!mysql_query($RBDet25)) {
					echo 'Rollback Delete Header 25  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr25;
			exit();
		}
	}

	$dataRes52['header'] = array('nojurnal' => $nojurnal52, 'kodejurnal' => 'KBNB58', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotvop'], 'totalkredit' => -1 * $param['totpotvop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut52 = 1;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akundebet52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	$dataRes52['detail'][] = array('nojurnal' => $nojurnal52, 'tanggal' => $tanggal, 'nourut' => $noUrut52, 'noakun' => $akunkredit52, 'keterangan' => 'TOTAL_POT_VOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotvop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut52;
	if (($dataRes52['header']['totaldebet'] == '') || ($dataRes52['header']['totalkredit'] == '') || ($dataRes52['header']['totaldebet'] == 0) || ($dataRes52['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead52 = insertQuery($dbname, 'keu_jurnalht', $dataRes52['header']);

		if (!mysql_query($insHead52)) {
			$headErr52 .= 'Insert Header BTL 52 Error : ' . mysql_error() . "\n";
		}

		if ($headErr52 == '') {
			$detailErr52 = '';

			foreach ($dataRes52['detail'] as $row) {
				$insDet52 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr52 .= 'Insert Detail Error 52: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr52 == '') {
				$updJurnal52 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter52), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal52 . '\'');

				if (!mysql_query($updJurnal52)) {
					echo 'Update Kode Jurnal 52 Error : ' . mysql_error() . "\n";
					$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

					if (!mysql_query($RBDet52)) {
						echo 'Rollback Delete Header BTL 52 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr52;
				$RBDet52 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal52 . '\'');

				if (!mysql_query($RBDet52)) {
					echo 'Rollback Delete Header 52  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr52;
			exit();
		}
	}

	$dataRes10['header'] = array('nojurnal' => $nojurnal10, 'kodejurnal' => 'KBNB48', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotmotor'], 'totalkredit' => -1 * $param['totpotmotor'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut10 = 1;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akundebet10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	$dataRes10['detail'][] = array('nojurnal' => $nojurnal10, 'tanggal' => $tanggal, 'nourut' => $noUrut10, 'noakun' => $akunkredit10, 'keterangan' => 'TOTAL_POT_MOTOR_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotmotor'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut10;
	if (($dataRes10['header']['totaldebet'] == '') || ($dataRes10['header']['totalkredit'] == '') || ($dataRes10['header']['totaldebet'] == 0) || ($dataRes10['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead10 = insertQuery($dbname, 'keu_jurnalht', $dataRes10['header']);

		if (!mysql_query($insHead10)) {
			$headErr10 .= 'Insert Header BTL 10 Error : ' . mysql_error() . "\n";
		}

		if ($headErr10 == '') {
			$detailErr10 = '';

			foreach ($dataRes10['detail'] as $row) {
				$insDet10 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr10 .= 'Insert Detail Error 10: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr10 == '') {
				$updJurnal10 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter10), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal10 . '\'');

				if (!mysql_query($updJurnal10)) {
					echo 'Update Kode Jurnal 10 Error : ' . mysql_error() . "\n";
					$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

					if (!mysql_query($RBDet10)) {
						echo 'Rollback Delete Header BTL 10 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr10;
				$RBDet10 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal10 . '\'');

				if (!mysql_query($RBDet10)) {
					echo 'Rollback Delete Header 10  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr10;
			exit();
		}
	}

	$dataRes11['header'] = array('nojurnal' => $nojurnal11, 'kodejurnal' => 'KBNB49', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotlaptop'], 'totalkredit' => -1 * $param['totpotlaptop'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut11 = 1;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akundebet11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	$dataRes11['detail'][] = array('nojurnal' => $nojurnal11, 'tanggal' => $tanggal, 'nourut' => $noUrut11, 'noakun' => $akunkredit11, 'keterangan' => 'TOTAL_POT_LAPTOP_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotlaptop'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut11;
	if (($dataRes11['header']['totaldebet'] == '') || ($dataRes11['header']['totalkredit'] == '') || ($dataRes11['header']['totaldebet'] == 0) || ($dataRes11['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead11 = insertQuery($dbname, 'keu_jurnalht', $dataRes11['header']);

		if (!mysql_query($insHead11)) {
			$headErr11 .= 'Insert Header BTL 11 Error : ' . mysql_error() . "\n";
		}

		if ($headErr11 == '') {
			$detailErr11 = '';

			foreach ($dataRes11['detail'] as $row) {
				$insDet11 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr11 .= 'Insert Detail Error 11: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr11 == '') {
				$updJurnal11 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter11), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal11 . '\'');

				if (!mysql_query($updJurnal11)) {
					echo 'Update Kode Jurnal 11 Error : ' . mysql_error() . "\n";
					$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

					if (!mysql_query($RBDet11)) {
						echo 'Rollback Delete Header BTL 11 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr11;
				$RBDet11 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal11 . '\'');

				if (!mysql_query($RBDet11)) {
					echo 'Rollback Delete Header 11  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr11;
			exit();
		}
	}

	$dataRes64['header'] = array('nojurnal' => $nojurnal64, 'kodejurnal' => 'KBNB62', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotdenda'], 'totalkredit' => -1 * $param['totpotdenda'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut64 = 1;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akundebet64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	$dataRes64['detail'][] = array('nojurnal' => $nojurnal64, 'tanggal' => $tanggal, 'nourut' => $noUrut64, 'noakun' => $akunkredit64, 'keterangan' => 'TOTAL_POT_DENDA_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotdenda'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut64;
	if (($dataRes64['header']['totaldebet'] == '') || ($dataRes64['header']['totalkredit'] == '') || ($dataRes64['header']['totaldebet'] == 0) || ($dataRes64['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead64 = insertQuery($dbname, 'keu_jurnalht', $dataRes64['header']);

		if (!mysql_query($insHead64)) {
			$headErr64 .= 'Insert Header BTL 64 Error : ' . mysql_error() . "\n";
		}

		if ($headErr64 == '') {
			$detailErr64 = '';

			foreach ($dataRes64['detail'] as $row) {
				$insDet64 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr64 .= 'Insert Detail Error 64: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr64 == '') {
				$updJurnal64 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter64), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal64 . '\'');

				if (!mysql_query($updJurnal64)) {
					echo 'Update Kode Jurnal 64 Error : ' . mysql_error() . "\n";
					$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

					if (!mysql_query($RBDet64)) {
						echo 'Rollback Delete Header BTL 64 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr64;
				$RBDet64 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal64 . '\'');

				if (!mysql_query($RBDet64)) {
					echo 'Rollback Delete Header 64  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr64;
			exit();
		}
	}

	$dataRes8['header'] = array('nojurnal' => $nojurnal8, 'kodejurnal' => 'KBNB46', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotbpjskes'], 'totalkredit' => -1 * $param['totpotbpjskes'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut8 = 1;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akundebet8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	$dataRes8['detail'][] = array('nojurnal' => $nojurnal8, 'tanggal' => $tanggal, 'nourut' => $noUrut8, 'noakun' => $akunkredit8, 'keterangan' => 'TOTAL_POT_BPJSKES_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotbpjskes'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut8;
	if (($dataRes8['header']['totaldebet'] == '') || ($dataRes8['header']['totalkredit'] == '') || ($dataRes8['header']['totaldebet'] == 0) || ($dataRes8['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead8 = insertQuery($dbname, 'keu_jurnalht', $dataRes8['header']);

		if (!mysql_query($insHead8)) {
			$headErr8 .= 'Insert Header BTL 8 Error : ' . mysql_error() . "\n";
		}

		if ($headErr8 == '') {
			$detailErr8 = '';

			foreach ($dataRes8['detail'] as $row) {
				$insDet8 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr8 .= 'Insert Detail Error 8: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr8 == '') {
				$updJurnal8 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter8), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal8 . '\'');

				if (!mysql_query($updJurnal8)) {
					echo 'Update Kode Jurnal 8 Error : ' . mysql_error() . "\n";
					$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

					if (!mysql_query($RBDet8)) {
						echo 'Rollback Delete Header BTL 8 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr8;
				$RBDet8 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal8 . '\'');

				if (!mysql_query($RBDet8)) {
					echo 'Rollback Delete Header 8  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr8;
			exit();
		}
	}

	$dataRes26['header'] = array('nojurnal' => $nojurnal26, 'kodejurnal' => 'KBNB56', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['totpotdendapanen'], 'totalkredit' => -1 * $param['totpotdendapanen'], 'amountkoreksi' => '0', 'noreferensi' => 'TOTAL_POT_DENDAPANEN_PERIODE:' . ' ' . $periode, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut26 = 1;
	$dataRes26['detail'][] = array('nojurnal' => $nojurnal26, 'tanggal' => $tanggal, 'nourut' => $noUrut26, 'noakun' => $akundebet26, 'keterangan' => 'TOTAL_POT_DENDAPANEN_PERIODE:' . ' ' . $periode, 'jumlah' => $param['totpotdendapanen'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut26;
	$dataRes26['detail'][] = array('nojurnal' => $nojurnal26, 'tanggal' => $tanggal, 'nourut' => $noUrut26, 'noakun' => $akunkredit26, 'keterangan' => 'TOTAL_POT_DENDAPANEN_PERIODE:' . ' ' . $periode, 'jumlah' => -1 * $param['totpotdendapanen'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'AUTO GENERATED BY SYSTEM', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut26;
	if (($dataRes26['header']['totaldebet'] == '') || ($dataRes26['header']['totalkredit'] == '') || ($dataRes26['header']['totaldebet'] == 0) || ($dataRes26['header']['totalkredit'] == 0)) {
	}
	else {
		$insHead26 = insertQuery($dbname, 'keu_jurnalht', $dataRes26['header']);

		if (!mysql_query($insHead26)) {
			$headErr26 .= 'Insert Header BTL 26 Error : ' . mysql_error() . "\n";
		}

		if ($headErr26 == '') {
			$detailErr26 = '';

			foreach ($dataRes26['detail'] as $row) {
				$insDet26 = insertQuery($dbname, 'keu_jurnaldt', $row);

				$detailErr26 .= 'Insert Detail Error 26: ' . mysql_error() . "\n";
				break;
			}

			if ($detailErr26 == '') {
				$updJurnal26 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter26), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal26 . '\'');

				if (!mysql_query($updJurnal26)) {
					echo 'Update Kode Jurnal 26 Error : ' . mysql_error() . "\n";
					$RBDet26 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal26 . '\'');

					if (!mysql_query($RBDet26)) {
						echo 'Rollback Delete Header BTL 26 Error : ' . mysql_error() . "\n";
						exit();
					}

					exit();
				}
			}
			else {
				echo $detailErr26;
				$RBDet26 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal26 . '\'');

				if (!mysql_query($RBDet26)) {
					echo 'Rollback Delete Header 26  Error : ' . mysql_error();
					exit();
				}
			}
		}
		else {
			echo $headErr26;
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
	$str = 'delete from ' . $dbname . '.keu_jurnalht where kodejurnal in (\'KBNB0\',\'KBNB1\',\'KBNB2\',\'KBNB3\',\'KBNB4\',\'KBNB5\',\'KBNB6\',\'KBNB7\',\'KBNB8\',\'KBNB9\',\'KBNB10\',\'KBNB11\',\'KBNB12\',\'KBNB13\',\'KBNB14\',\'KBNB15\',\'KBNB16\',\'KBNB17\',\'KBNB18\',\'KBNB19\',\'KBNB20\',\'KBNB21\',\'KBNB22\',\'KBNB23\',\'KBNB24\',\'KBNB25\',\'KBNB26\',\'KBNB27\',\'KBNB28\',\'KBNB29\',\'KBNB30\',\'KBNB31\',\'KBNB32\',\'KBNB32\',\'KBNB33\',\'KBNB34\',\'KBNB35\',\'KBNB36\',\'KBNB37\',\'KBNB38\',\'KBNB39\',\'KBNB40\',\'KBNB41\',\'KBNB42\',\'KBNB43\',\'KBNB44\',\'KBNB45\',\'KBNB46\',\'KBNB47\',\'KBNB48\',\'KBNB49\',\'KBNB50\',\'KBNB51\',\'KBNB52\',\'KBNB53\',\'KBNB54\',\'KBNB55\',\'KBNB56\',\'KBNB57\',\'KBNB58\',\'KBNB59\',\'KBNB60\',\'KBNB61\',\'KBNB62\',\'KBNL0\',\'KBNL1\',\'KBNL2\',\'KBNL3\',\'M6\',\'PKS01\',\'PKS02\',\'PNN01\',\'SIPL1\',\'VHCG0\',\'VHCG1\',\'VHCG2\',\'VHCG3\',\'VHCG4\',\'VHCG5\',\'WSG0\',\'WSG1\',\'WSG2\',\'WSG3\',\'WSG4\',\'WSG5\',\'KNTB0\',\'KNTB1\',\'KNTB2\',\'KNTB3\',\'KNTB4\',\'KNTB5\',\'KNTB6\',\'KNTB7\',\'KNTB8\',\'KNTB9\',\'KNTB10\',\'KNTB11\',\'KNTB12\',\'KNTB13\',\'KNTB14\',\'KNTB15\',\'KNTB16\',\'KNTB17\',\'KNTB18\',\'KNTB19\',\'KNTB20\',\'KNTB21\',\'KNTB22\',\'KNTB23\',\'KNTB24\',\'KNTB25\',\'KNTB26\',\'KNTB27\',\'KNTB28\',\'KNTB29\',\'KNTB30\',\'KNTB31\',\'KNTB32\',\'KNTB32\',\'KNTB33\',\'KNTB34\',\'KNTB35\',\'KNTB36\',\'KNTB37\',\'KNTB38\',\'KNTB39\',\'KNTB40\',\'KNTB41\',\'KNTB42\',\'KNTB43\',\'KNTB44\',\'KNTB45\',\'KNTB46\',\'KNTB47\',\'KNTB48\',\'KNTB49\',\'KNTB50\',\'KNTB51\',\'KNTB52\',\'KNTB53\',\'KNTB54\',\'KNTB55\',\'KNTB56\',\'KNTB57\',\'KNTB58\',\'KNTB59\',\'KNTB60\',\'KNTB61\',\'KNTB62\') and tanggal=\'' . $tanggal . '\' and nojurnal like \'%/' . $_SESSION['empl']['lokasitugas'] . '/%\'';
	mysql_query($str);
}

$str = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
$res = mysql_query($str);
$tip = '';

while ($bar = mysql_fetch_object($res)) {
	$tip = $bar->tipe;
}

$param['subbagian'] = str_replace(' ', '', $param['subbagian']);

if ($tip == 'PABRIK') {
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
	else if ($tip == 'HOLDING') {
		prosesGajiHORO();
	}
	else if ($tip == 'KANWIL') {
		prosesGajiROTRAKSI();
	}
	else {
		prosesGajiKebun();
	}
}

?>
