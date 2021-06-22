<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;

if ($param['kodeorgpengirim'] == $_SESSION['empl']['lokasitugas']) {
	$param['tanggal'] = tanggalsystem($param['tanggal']);
	$where = 'tanggal=\'' . $param['tanggal'] . '\' and kodeorgpengirim=\'' . $param['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $param['kodeorgpenerima'] . '\' and nogiro=\'' . $param['nogiro'] . '\'';
	$query = updateQuery($dbname, 'keu_transferdana', array('postingkirim' => 1), $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}
}
else {
	$pt1 = getPT($dbname, $param['kodeorgpengirim']);

	if ($pt1 == false) {
		$pt1 = getHolding($dbname, $param['kodeorgpengirim']);
	}

	$pt2 = getPT($dbname, $param['kodeorgpenerima']);

	if ($pt2 == false) {
		$pt2 = getHolding($dbname, $param['kodeorgpenerima']);
	}

	$ayatSilang = '1120400';
	$param['tanggal'] = tanggalsystem($param['tanggal']);
	$where = 'tanggal=\'' . $param['tanggal'] . '\' and kodeorgpengirim=\'' . $param['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $param['kodeorgpenerima'] . '\' and nogiro=\'' . $param['nogiro'] . '\'';
	$query = selectQuery($dbname, 'keu_transferdana', '*', $where);
	$data = fetchData($query);
	$queryKode = selectQuery($dbname, 'keu_5parameterjurnal', '*', '(noakunkredit<\'' . $data[0]['noakunpengirim'] . '\' and sampaikredit>\'' . $data[0]['noakunpengirim'] . '\') or ' . '(noakundebet<\'' . $data[0]['noakunpenerima'] . '\' and sampaidebet>\'' . $data[0]['noakunpenerima'] . '\')');
	$resKode = fetchData($queryKode);

	foreach ($resKode as $row) {
		if (substr($row['jurnalid'], 1, 1) == 'K') {
			$kodejurnal1 = $row['jurnalid'];
		}

		if (substr($row['jurnalid'], 1, 1) == 'M') {
			$kodejurnal2 = $row['jurnalid'];
		}
	}

	$queryC1 = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal1 . '\'');
	$tmpC1 = fetchData($queryC1);

	if (empty($tmpC1)) {
		echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
		exit();
	}

	$konter1 = addZero($tmpC1[0]['nokounter'] + 1, 3);
	$nojurnal1 = $param['tanggal'] . '/' . $data[0]['kodeorgpengirim'] . '/' . $kodejurnal1 . '/' . $konter1;
	$dataRes1['header'] = array('nojurnal' => $nojurnal1, 'kodejurnal' => $kodejurnal1, 'tanggal' => $data[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $param['tanggal'] . '/' . $param['kodeorgpengirim'] . '/' . $param['kodeorgpenerima'] . '/' . $param['nogiro'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1');
	$noUrut = 1;
	$dataRes1['detail'][] = array('nojurnal' => $nojurnal1, 'tanggal' => $param['tanggal'], 'nourut' => $noUrut, 'noakun' => $ayatSilang, 'keterangan' => 'Transfer dana tanggal ' . $param['tanggal'] . ' ke ' . $param['kodeorgpenerima'], 'jumlah' => $data[0]['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pt1['kode'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '');
	++$noUrut;
	$dataRes1['detail'][] = array('nojurnal' => $nojurnal1, 'tanggal' => $param['tanggal'], 'nourut' => $noUrut, 'noakun' => $data[0]['noakunpengirim'], 'keterangan' => 'Transfer dana tanggal ' . $param['tanggal'] . ' ke ' . $param['kodeorgpenerima'], 'jumlah' => -1 * $data[0]['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pt1['kode'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '');
	++$noUrut;
	$queryC2 = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal2 . '\'');
	$tmpC2 = fetchData($queryC2);

	if (empty($tmpC2)) {
		echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
		exit();
	}

	$konter2 = addZero($tmpC2[0]['nokounter'] + 1, 3);
	$nojurnal2 = $param['tanggal'] . '/' . $data[0]['kodeorgpenerima'] . '/' . $kodejurnal2 . '/' . $konter2;
	$dataRes2['header'] = array('nojurnal' => $nojurnal2, 'kodejurnal' => $kodejurnal2, 'tanggal' => $param['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $param['tanggal'] . '/' . $param['kodeorgpengirim'] . '/' . $param['kodeorgpenerima'] . '/' . $param['nogiro'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1');
	$noUrut = 1;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal2, 'tanggal' => $param['tanggal'], 'nourut' => $noUrut, 'noakun' => $data[0]['noakunpenerima'], 'keterangan' => 'Transfer dana tanggal ' . $param['tanggal'] . ' dari ' . $param['kodeorgpengirim'], 'jumlah' => $data[0]['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pt2['kode'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '');
	++$noUrut;
	$dataRes2['detail'][] = array('nojurnal' => $nojurnal2, 'tanggal' => $param['tanggal'], 'nourut' => $noUrut, 'noakun' => $ayatSilang, 'keterangan' => 'Transfer dana tanggal ' . $param['tanggal'] . ' dari ' . $param['kodeorgpengirim'], 'jumlah' => -1 * $data[0]['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pt2['kode'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '');
	++$noUrut;
	$headErr = '';
	$insHead1 = insertQuery($dbname, 'keu_jurnalht', $dataRes1['header']);

	if (!mysql_query($insHead1)) {
		$headErr .= 'Insert Header 1 Error : ' . mysql_error() . "\n";
	}

	$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);

	if (!mysql_query($insHead2)) {
		$headErr .= 'Insert Header 2 Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes1['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
			break;
		}

		foreach ($dataRes2['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);

			$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
			break;
		}

		if ($detailErr == '') {
			$updErr = '';
			$updJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal1 . '\'');
			$updJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter2), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal2 . '\'');

			if (!mysql_query($updJurnal1)) {
				$updErr .= 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
			}

			if (!mysql_query($updJurnal2)) {
				$updErr .= 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
			}

			if ($updErr != '') {
				echo $updErr;
				$RBErr = '';
				$RBDet1 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal1 . '\'');

				if (!mysql_query($RBDet1)) {
					$RBErr .= 'Rollback Delete Header 1 Error : ' . mysql_error() . "\n";
					exit();
				}

				$RBDet2 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal2 . '\'');

				if (!mysql_query($RBDet2)) {
					$RBErr .= 'Rollback Delete Header 2 Error : ' . mysql_error() . "\n";
					exit();
				}

				$RBJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter1 - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal1 . '\'');

				if (!mysql_query($RBJurnal1)) {
					$RBErr .= 'Rollback Update Jurnal 1 Error : ' . mysql_error() . "\n";
				}

				$RBJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter2 - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal2 . '\'');

				if (!mysql_query($RBJurnal2)) {
					$RBErr .= 'Rollback Update Jurnal 2 Error : ' . mysql_error() . "\n";
				}

				if ($RBErr != '') {
					echo $RBErr;
				}

				exit();
			}
			else {
				$updTransErr = '';
				$where = 'tanggal=\'' . $param['tanggal'] . '\' and kodeorgpengirim=\'' . $param['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $param['kodeorgpenerima'] . '\' and nogiro=\'' . $param['nogiro'] . '\'';
				$query = updateQuery($dbname, 'keu_transferdana', array('postingterima' => 1), $where);

				if (!mysql_query($query)) {
					$updTransErr .= 'Update Transaksi Error : ' . mysql_error();
				}

				if ($updTransErr != '') {
					echo $updTransErr;
					$RBErr = '';
					$RBDet1 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal1 . '\'');

					if (!mysql_query($RBDet1)) {
						$RBErr .= 'Rollback Delete Header 1 Error : ' . mysql_error() . "\n";
						exit();
					}

					$RBDet2 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal2 . '\'');

					if (!mysql_query($RBDet2)) {
						$RBErr .= 'Rollback Delete Header 2 Error : ' . mysql_error() . "\n";
						exit();
					}

					$RBJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter1 - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal1 . '\'');

					if (!mysql_query($RBJurnal1)) {
						$RBErr .= 'Rollback Update Jurnal 1 Error : ' . mysql_error() . "\n";
					}

					$RBJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter2 - 1), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodejurnal2 . '\'');

					if (!mysql_query($RBJurnal2)) {
						$RBErr .= 'Rollback Update Jurnal 2 Error : ' . mysql_error() . "\n";
					}

					if ($RBErr != '') {
						echo $RBErr;
					}

					exit();
				}
				else {
					echo '1';
				}
			}
		}
		else {
			echo $detailErr;
			$RBErr = '';
			$RBDet1 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal1 . '\'');

			if (!mysql_query($RBDet1)) {
				$RBErr .= 'Rollback Delete Header 1 Error : ' . mysql_error() . "\n";
				exit();
			}

			$RBDet2 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal2 . '\'');

			if (!mysql_query($RBDet2)) {
				$RBErr .= 'Rollback Delete Header 2 Error : ' . mysql_error() . "\n";
				exit();
			}

			if ($RBErr != '') {
				echo $RBErr;
			}
		}
	}
	else {
		echo $headErr;
		$RBErr = '';
		$RBDet1 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal1 . '\'');

		if (!mysql_query($RBDet1)) {
			$RBErr .= 'Rollback Delete Header 1 Error : ' . mysql_error() . "\n";
			exit();
		}

		$RBDet2 = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal2 . '\'');

		if (!mysql_query($RBDet2)) {
			$RBErr .= 'Rollback Delete Header 2 Error : ' . mysql_error() . "\n";
			exit();
		}

		if ($RBErr != '') {
			echo $RBErr;
		}

		exit();
	}
}

?>
