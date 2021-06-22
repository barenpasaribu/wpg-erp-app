<?php


function prosesByBengkel()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$group = 'WS2';
	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk WS2');
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
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_BY_WS', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'Biaya Bengkel/Reparasi ' . $param['kodevhc'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_BY_WS', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'Alokasi biaya bengkel ke ' . $param['kodevhc'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_BY_WS', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header WS Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
			if (!mysql_query($insDet)) {
				$detailErr .= 'Insert Detail WS Error : ' . mysql_error() . "\n";
				break;
			}
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

function prosesAlokasi()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi where ' . "\r\n" . '          kodeorg =\'' . $_SESSION['empl']['lokasitugas'] . '\' and tutupbuku=0';
	$tgmulai = '';
	$tgsampai = '';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: Tidak ada periode akuntansi untuk induk ' . $_SESSION['empl']['lokasitugas']);
	}

	while ($bar = mysql_fetch_object($res)) {
		$tgsampai = $bar->tanggalsampai;
		$tgmulai = $bar->tanggalmulai;
	}

	if (($tgmulai == '') || ($tgsampai == '')) {
		exit('Error: Periode akuntasi tidak terdaftar');
	}

	$group = 'VHC1';
	$str = 'select noakundebet from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk VHC1');
	}
	else {
		$bar = mysql_fetch_object($res);
		$akunalok = $bar->noakundebet;
	}

	$str = 'select sum(a.jumlah) as jlh,a.alokasibiaya,b.noakun from ' . $dbname . '.vhc_rundt a' . "\r\n" . '            left join ' . $dbname . '.vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan' . "\r\n" . '            left join ' . $dbname . '.vhc_runht c on a.notransaksi=c.notransaksi     ' . "\r\n" . '            where c.kodevhc=\'' . $param['kodevhc'] . '\'' . "\r\n" . '            and c.tanggal>=\'' . $tgmulai . '\' and c.tanggal <=\'' . $tgsampai . '\' and alokasibiaya!=\'\' ' . "\r\n" . '            and jenispekerjaan!=\'\'    ' . "\r\n" . '            group by jenispekerjaan,noakun,alokasibiaya';
	$res = mysql_query($str);
	$lokasi = array();
	$biaya = array();
	$jam = array();
	$akun = array();
	$kodeasset = array();
	$ttl = 0;

	while ($bar = mysql_fetch_object($res)) {
		if ((substr($bar->alokasibiaya, 0, 2) == 'AK') || (substr($bar->alokasibiaya, 0, 2) == 'PB')) {
			$tipeasset = substr($bar->alokasibiaya, 3, 3);
			$tipeasset = str_replace('0', '', $tipeasset);
			$str1 = 'select akunak from ' . $dbname . '.sdm_5tipeasset where kodetipe=\'' . $tipeasset . '\'';
			$res1 = mysql_query($str1);

			if (mysql_num_rows($res1) < 1) {
				exit(' Error: Akun aktiva dalam konstruksi untuk ' . $tipeasset . ' belum disetting dari keuangan->setup->tipeasset');
			}
			else {
				while ($bar1 = mysql_fetch_object($res1)) {
					if ($bar1->akunak == '') {
						exit(' Error: Akun aktiva dalam konstruksi untuk ' . $tipeasset . ' belum disetting dari keuangan->setup->tipeasset');
					}
					else {
						$akun[] = $bar1->akunak;
					}
				}

				$kodeasset[] = $bar->alokasibiaya;
				$lokasi[] = $bar->alokasibiaya;
				$jam[] = $bar->jlh;
				$biaya[] = $bar->jlh * $param['jumlah'];
				$kegiatan[] = '';
			}
		}
		else {
			$lokasi[] = $bar->alokasibiaya;
			$akun[] = $bar->noakun;
			$jam[] = $bar->jlh;
			$biaya[] = $bar->jlh * $param['jumlah'];
			$kegiatan[] = $bar->noakun . '01';
			$kodeasset[] = '';
		}
	}

	foreach ($biaya as $key => $nilai) {
		$dataRes['header'] = array();
		$dataRes['detail'] = array();
		$intern = true;
		$pengguna = substr($lokasi[$key], 0, 4);
		if ((substr($lokasi[$key], 0, 2) == 'AK') || (substr($lokasi[$key], 0, 2) == 'PB')) {
			$str = 'select kodeorg from ' . $dbname . '.project where kode=\'' . $lokasi[$key] . '\'';
			$res = mysql_query($str);

			while ($bar = mysql_fetch_object($res)) {
				$pengguna = $bar->kodeorg;
				$lokasi[$key] = '';
			}
		}

		$str = 'select akunpiutang,jenis from ' . $dbname . '.keu_5caco where kodeorg=\'' . $pengguna . '\'';
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

		$akunpekerjaan = $akun[$key];
		$ptpengguna = '';
		$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pengguna . '\'';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$ptpengguna = $bar->induk;
		}

		$ptGudang = '';
		$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$ptGudang = $bar->induk;
		}

		$akunpengguna = '';

		if ($ptGudang != $ptpengguna) {
			$intern = false;
			$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' and jenis=\'inter\'';
			$res = mysql_query($str);
			$akunpengguna = '';

			while ($bar = mysql_fetch_object($res)) {
				$akunpengguna = $bar->akunhutang;
			}

			$akunsendiri = $interco;

			if ($akunpengguna == '') {
				exit('Error: Akun intraco  atau interco belum ada untuk unit ' . $pengguna);
			}
		}
		else if ($pengguna != $_SESSION['empl']['lokasitugas']) {
			$intern = false;
			$str = 'select akunhutang from ' . $dbname . '.keu_5caco where kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' and jenis=\'intra\'';
			$res = mysql_query($str);
			$akunpengguna = '';

			while ($bar = mysql_fetch_object($res)) {
				$akunpengguna = $bar->akunhutang;
			}

			$akunsendiri = $intraco;

			if ($akunpengguna == '') {
				exit('Error: Akun intraco  atau interco belum ada untuk unit ' . $pengguna);
			}
		}
		else {
			$intern = true;
		}

		if ($intern) {
			$kodeJurnal = $group;
			$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
			$tmpKonter = fetchData($queryJ);
			$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
			$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
			$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $biaya[$key], 'totalkredit' => -1 * $biaya[$key], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_KERJA_AB', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
			$noUrut = 1;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $param['periode'] . ':Biaya Kendaraan ' . $param['kodevhc'], 'jumlah' => $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => $kodeasset[$key], 'kodebarang' => '', 'nik' => 0, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => $lokasi[$key], 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunalok, 'keterangan' => $param['periode'] . ':Alokasi biaya kend' . $param['kodevhc'], 'jumlah' => -1 * $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => $lokasi[$key], 'revisi' => '0');
			++$noUrut;
			$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

			if (!mysql_query($insHead)) {
				$headErr .= 'Insert Header Intern Error : ' . mysql_error() . "\n";
			}

			if ($headErr == '') {
				$detailErr = '';

				foreach ($dataRes['detail'] as $row) {
					$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
					if (!mysql_query($insDet)) {
						$detailErr .= 'Insert Detail Intern Error : ' . mysql_error() . "\n";
						break;
					}
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
		else {
			$noUrut = 1;
			$kodeJurnal = $group;
			$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
			$tmpKonter = fetchData($queryJ);
			$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
			$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
			$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $biaya[$key], 'totalkredit' => -1 * $biaya[$key], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_KERJA_AB', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunsendiri, 'keterangan' => $param['periode'] . ':Biaya Kendaraan ' . $param['kodevhc'], 'jumlah' => $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunalok, 'keterangan' => $param['periode'] . ':Alokasi biaya kend' . $param['kodevhc'], 'jumlah' => -1 * $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
			++$noUrut;
			$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

			if (!mysql_query($insHead)) {
				$headErr .= 'Insert Header Ex.Self Error : ' . mysql_error() . "\n";
			}

			if ($headErr == '') {
				$detailErr = '';

				foreach ($dataRes['detail'] as $row) {
					$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
					if (!mysql_query($insDet)) {
						$detailErr .= 'Insert Detail Ex.Self Error : ' . mysql_error() . "\n";
						break;
					}
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

			$kodeJurnal = $group;
			$tgmulaid = $tanggal;
			$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $ptpengguna . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
			$tmpKonter = fetchData($queryJ);
			$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
			$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $pengguna . '/' . $kodeJurnal . '/' . $konter;
			unset($dataRes['header']);
			$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tgmulaid, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $biaya[$key], 'totalkredit' => -1 * $biaya[$key], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_KERJA_AB', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
			$noUrut = 1;
			unset($dataRes['detail']);
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tgmulaid, 'nourut' => $noUrut, 'noakun' => $akunpekerjaan, 'keterangan' => $param['periode'] . ':Biaya Kendaraan ' . $param['kodevhc'], 'jumlah' => $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => $kodeasset[$key], 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => $lokasi[$key], 'revisi' => '0');
			++$noUrut;
			$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tgmulaid, 'nourut' => $noUrut, 'noakun' => $akunpengguna, 'keterangan' => $param['periode'] . ':Alokasi biaya kend' . $param['kodevhc'], 'jumlah' => -1 * $biaya[$key], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pengguna, 'kodekegiatan' => $kegiatan[$key], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_KERJA_AB', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => $lokasi[$key], 'revisi' => '0');
			++$noUrut;
			$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

			if (!mysql_query($insHead)) {
				$headErr .= 'Insert Header OSIDE Error : ' . mysql_error() . "\n";
			}

			if ($headErr == '') {
				$detailErr = '';

				foreach ($dataRes['detail'] as $row) {
					$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
					if (!mysql_query($insDet)) {
						$detailErr .= 'Insert Detail OSIDE Error : ' . mysql_error() . "\n" . $insDet;
						break;
					}
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
}

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = $param['periode'] . '-28';

if ($param['jenis'] == 'BYWS') {
	$str = 'select distinct nojurnal from ' . $dbname . '.keu_jurnaldt where noreferensi=\'ALK_BY_WS\'' . "\r\n" . '          and kodevhc=\'' . $param['kodevhc'] . '\' and tanggal=\'' . $tanggal . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$str = 'delete from ' . $dbname . '.keu_jurnalht where nojurnal=\'' . $bar->nojurnal . '\'';
		mysql_query($str);
	}

	prosesByBengkel();
}
else {
	$str = 'select distinct nojurnal from ' . $dbname . '.keu_jurnaldt where noreferensi=\'ALK_KERJA_AB\'' . "\r\n" . '          and kodevhc=\'' . $param['kodevhc'] . '\' and tanggal=\'' . $tanggal . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$str = 'delete from ' . $dbname . '.keu_jurnalht where nojurnal=\'' . $bar->nojurnal . '\'';
		mysql_query($str);
	}

	prosesAlokasi();
}

?>
