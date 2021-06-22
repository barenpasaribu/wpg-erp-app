<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$param['hartot'] = str_replace(',', '', $param['hartot']);
$kodeJurnal = $param['kodejurnal'];
$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal ' . "\r\n" . '         where jurnalid=\'' . $kodeJurnal . '\'';
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	exit('Error: Tidak ada kode jurnal untuk ' . $kodeJurnal);
}
else {
	while ($bar = mysql_fetch_object($res)) {
		$debet = $bar->noakundebet;
		$kredit = $bar->noakunkredit;
	}

	$blm = str_replace('-', '', $param['periode']);
	$str = 'select * from ' . $dbname . '.keu_jurnalht where nojurnal ' . "\r\n" . '             like \'%' . $blm . '28/' . substr($_SESSION['empl']['lokasitugas'], 0, 4) . '/' . $kodeJurnal . '%\'';

	$res = mysql_query($str);


	if (0 < mysql_num_rows($res)) {
		exit('Error: Proses penarikan data Penyusutan sudah pernah dilakukan');
	}

	$konter = '001';
	$tanggal = $param['periode'] . '-28';
	$nojurnal = str_replace('-', '', $tanggal) . '/' . substr($_SESSION['empl']['lokasitugas'], 0, 4) . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => $kodeJurnal . ':' . str_replace('-', '', $tanggal), 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => $param['keterangan'] . ' Periode:' . $_POST['periode'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $kodeJurnal . ':' . str_replace('-', '', $tanggal), 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $kredit, 'keterangan' => $param['keterangan'] . ' Periode:' . $_POST['periode'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $kodeJurnal . ':' . str_replace('-', '', $tanggal), 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
			if (!mysql_query($insDet)) {
				$detailErr .= 'Insert Detail Error : ' . mysql_error() . "\n";
				break;
			}
		}
		if ($detailErr == '') {
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
