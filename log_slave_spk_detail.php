<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;


switch ($proses) {
case 'showDetail':
	if ($_SESSION['empl']['tipelokasitugas'] == 'KEBUN') {
		$scek = 'select distinct tipe from ' . $dbname . '.organisasi where induk=\'' . $param['divisi'] . '\'';

		#exit(mysql_error($conn));
		($qcek = mysql_query($scek)) || true;
		$rcek = mysql_fetch_assoc($qcek);
		$tpdt = 'BLOK';

		if ($rcek['tipe'] == 'BIBITAN') {
			$tpdt = 'BIBITAN';
		}

		$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . substr($param['divisi'], 0, 4) . '%\'');
	}
	else {
		$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . substr($param['divisi'], 0, 4) . '%\'');
	}

	if ($_SESSION['empl']['tipelokasitugas'] == 'KEBUN') {
		$optBlokStat = makeOption($dbname, 'setup_blok', 'kodeorg,statusblok', 'kodeorg=\'' . getFirstKey($optBlok) . '\'');

		if (strlen(getFirstKey($optBlokStat)) == 10) {
			$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';
		}
		else {
			$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';
		}

		$optAct = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', $whereAct, '6');
	}
	else {
		$tipeX = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');

		if (isset($tipeX[$param['divisi']]) && ($tipeX[$param['divisi']] == 'PABRIK')) {
			$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';
		}
		else {
			$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';
		}

		$optAct = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', $whereAct, '6');
	}

	if ((substr($param['divisi'], 0, 2) == 'AK') || (substr($param['divisi'], 0, 2) == 'PB')) {
		$optBlok = makeOption($dbname, 'project', 'kode,nama', 'kode=\'' . $param['divisi'] . '\' and posting=0');
		$optAct = makeOption($dbname, 'project_dt', 'kegiatan,namakegiatan', 'kodeproject=\'' . $param['divisi'] . '\'');
	}

	$optActT = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', '6');
	$where = 'notransaksi=\'' . $param['notransaksi'] . '\'';
	$cols = 'kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp';
	$query = selectQuery($dbname, 'log_spkdt', $cols, $where);
	$data = fetchData($query);
	$dataShow = $data;

	foreach ($dataShow as $key => $row) {
		$dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
		$dataShow[$key]['kodekegiatan'] = $optActT[$row['kodekegiatan']];
	}

	$theForm1 = new uForm('detailForm', 'Form Detail', 2);
	$theForm1->addEls('kodeblok', $_SESSION['lang']['subunit'], '', 'select', 'L', 25, $optBlok);
	$theForm1->_elements[0]->_attr['onchange'] = 'updKegiatan()';
	$theForm1->addEls('kodekegiatan', $_SESSION['lang']['kodekegiatan'], '', 'selectsearch', 'L', 25, $optAct);
	$theForm1->addEls('hk', $_SESSION['lang']['hk'], '1', 'textnum', 'R', 10);
	$theForm1->addEls('hasilkerjajumlah', $_SESSION['lang']['volumekontrak'], '0', 'textnum', 'R', 10);
	$theForm1->addEls('satuan', $_SESSION['lang']['satuan'], '', 'text', 'L', 10);
	$theForm1->addEls('jumlahrp', $_SESSION['lang']['total'] . ' ' . $_SESSION['lang']['rp'], '0', 'textnum', 'R', 10);
	$theForm1->_elements[5]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
	$theTable1 = new uTable('detailTable', 'Tabel Detail', $cols, $data, $dataShow);
	$formTab1 = new uFormTable('ftDetail', $theForm1, $theTable1, NULL, array('notransaksi'));
	$formTab1->_target = 'log_slave_spk_detail';
	$formTab1->_numberFormat = '##jumlah';
	$formTab1->_beforeEditMode = 'beforeEditMode';
	echo '<fieldset><legend><b>Detail</b></legend>';
	$formTab1->render();
	echo '</fieldset>';
	break;

case 'add':
	$cols = array('kodeblok', 'kodekegiatan', 'hk', 'hasilkerjajumlah', 'satuan', 'jumlahrp', 'notransaksi');
	$data = $param;
	unset($data['numRow']);
	$data['jumlahrp'] = str_replace(',', '', $data['jumlahrp']);
	$query = insertQuery($dbname, 'log_spkdt', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	unset($data['notransaksi']);
	$res = '';

	foreach ($data as $cont) {
		$res .= '##' . $cont;
	}

	$result = '{res:"' . $res . '",theme:"' . $_SESSION['theme'] . '"}';
	echo $result;
	break;

case 'edit':
	$data = $param;
	unset($data['notransaksi']);
	$data['jumlahrp'] = str_replace(',', '', $data['jumlahrp']);

	foreach ($data as $key => $cont) {
		if (substr($key, 0, 5) == 'cond_') {
			unset($data[$key]);
		}
	}

	$where = 'notransaksi=\'' . $param['notransaksi'] . '\'and kodekegiatan=\'' . $param['cond_kodekegiatan'] . '\' and kodeblok=\'' . $param['kodeblok'] . '\'';
	$query = updateQuery($dbname, 'log_spkdt', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	echo json_encode($param);
	break;

case 'delete':
	$m = 0;
	$strx = 'select sum(jumlahrealisasi) from ' . $dbname . '.log_baspk' . "\r\n" . '                  where notransaksi=\'' . $param['notransaksi'] . '\'';
	$resx = mysql_query($strx);

	while ($barx = mysql_fetch_array($resx)) {
		$m = $barx[0];
	}

	$n = '';
	$strx = 'select statusjurnal from ' . $dbname . '.log_baspk' . "\r\n" . '                  where notransaksi=\'' . $param['notransaksi'] . '\' and statusjurnal=0';
	$resx = mysql_query($strx);

	if (0 < mysql_num_rows($resx)) {
		$n = '?';
	}

	if (($n == '') && ($m == 0)) {
		$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodekegiatan=\'' . $param['kodekegiatan'] . '\'';
		$query = 'delete from `' . $dbname . '`.`log_spkdt` where ' . $where;

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error();
			exit();
		}
	}
	else {
		exit('Error:Realisasi sudah terisi');
	}

	break;

case 'updKegiatan':
	$optTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
	$optBlokStat = makeOption($dbname, 'setup_blok', 'kodeorg,statusblok', 'kodeorg=\'' . $param['kodeblok'] . '\'');

	if (strlen(getFirstKey($optBlokStat)) == 10) {
		$whereAct = 'kelompok=\'' . getFirstContent($optBlokStat) . '\'';
	}
	else {
		$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';

		if ($optTipe[$param['kodeblok']] == 'PABRIK') {
			$whereAct = 'kelompok in (\'KNT\',\'MIL\',\'TM\',\'PNN\',\'SPL\',\'TBM\',\'TB\',\'BBT\',\'TRK\')';
		}
	}

	$kodeorg = substr($param['kodeblok'], 0,3);
	$optAct = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', $whereAct, '6');
	$optActT = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', '6');

	echo json_encode($optActT);
	break;
}

?>
