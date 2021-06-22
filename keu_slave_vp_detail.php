<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
require_once 'lib/tanaman.php';
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showDetail':
	$tipe = 'VP';
	$whereKel = 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $tipe . '\'';
	$optKel = makeOption($dbname, 'keu_5kelompokjurnal', 'kodekelompok,keterangan', $whereKel);

	if (empty($optKel)) {
		echo 'Warning : Journal Group  ' . $tipe . ' not assign for your unit/Company' . "\n";
		echo 'Please contact  IT Dept.';
		exit();
	}

	$invList = makeOption($dbname, 'keu_vp_inv', 'noinv,noinv', 'novp=\'' . $param['novp'] . '\'');
	$whereInv = '';

	foreach ($invList as $in) {
		if (!empty($whereInv)) {
			$whereInv .= ',';
		}

		$whereInv .= '\'' . $in . '\'';
	}

	if (!empty($whereInv)) {
		$qInv = 'SELECT sum(nilaiinvoice+nilaippn) as nilai FROM ' . $dbname . '.`keu_tagihanht` where noinvoice in (' . $whereInv . ')';
		$resInv = fetchData($qInv);
		$rpDef = $resInv[0]['nilai'];
	}
	else {
		$rpDef = 0;
	}

	$whereAkun = 'detail=1 and left(noakun,3) in (\'213\',\'117\',\'821\',\'211\',\'212\',\'113\',\'116\',\'121\',\'822\',\'713\',\'118\',\'115\',\'811\',\'114\',\'711\',\'611\',\'621\')';

	if ($_SESSION['language'] == 'EN') {
		$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,noakun,namaakun1', $whereAkun, '5');
	}
	else {
		$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,noakun,namaakun', $whereAkun, '5');
	}

	$optCurr = makeOption($dbname, 'setup_matauang', 'kode,matauang');
	$optDK = array('D' => $_SESSION['lang']['debet'], 'K' => $_SESSION['lang']['kredit']);
	$where = 'novp=\'' . $param['novp'] . '\'';
	$cols = 'noakun,matauang,kurs,jumlah';
	$query = selectQuery($dbname, 'keu_vpdt', $cols, $where);
	$data = fetchData($query);
	$dataShow = $data;
	$totalJumlah = 0;

	foreach ($dataShow as $key => $row) {
		$jml = $row['jumlah'];
		unset($data[$key]['jumlah']);
		$tipe = ($row['jumlah'] < 0 ? 'K' : 'D');
		$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
		$data[$key]['dk'] = $tipe;
		$dataShow[$key]['dk'] = $optDK[$tipe];
		$data[$key]['jumlah'] = $dataShow[$key]['jumlah'] = abs($row['jumlah']);
		$totalJumlah += $row['jumlah'];
	}

	$theForm2 = new uForm('vpForm', 'Form Voucher Payable');
//	$theForm2->addEls('noakun', $_SESSION['lang']['noakun'], '', 'select', 'L', 25, $optAkun);
	$theForm2->addEls('noakun', $_SESSION['lang']['noakun'], '', 'selectsearch', 'L', 25, $optAkun);
	$theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
	$theForm2->addEls('matauang', $_SESSION['lang']['matauang'], 'IDR', 'select', 'L', 25, $optCurr);
	$theForm2->addEls('kurs', $_SESSION['lang']['kurs'], '1', 'textnum', 'R', 25);
	$theForm2->addEls('dk', $_SESSION['lang']['debet'] . '/' . $_SESSION['lang']['kredit'], '', 'select', 'L', 25, $optDK);
	$theForm2->addEls('jumlah', $_SESSION['lang']['jumlah'], number_format($rpDef, 2), 'textnum', 'R', 25);
	$theForm2->_elements[2]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
	$theTable2 = new uTable('vpTable', 'Tabel Voucher Payable', $cols, $data, $dataShow);
	$formTab2 = new uFormTable('ftVp', $theForm2, $theTable2, NULL, array('novp', 'rpInvoiceReal'));
	$formTab2->_target = 'keu_slave_vp_detail';
	$formTab2->_noClearField = '##novp##kurs##matauang';
	$formTab2->_numberFormat = '##jumlah##kurs';
	$formTab2->_defValue = '##jumlah=' . $rpDef;
	echo '<fieldset><legend><b>Detail</b></legend>';
	echo '&nbsp;Nilai Invoice :';
	echo makeElement('rpInvoiceReal', 'hidden', $rpDef);
	echo makeElement('rpInvoice', 'textnum', number_format($rpDef, 2), array('disabled' => true)) . '<br>';
	$formTab2->render();
	echo '</fieldset>';
	break;

case 'add':
	$cols = array('novp', 'noakun', 'matauang', 'kurs', 'jumlah');
	$data = array('novp' => $param['novp'], 'noakun' => $param['noakun'], 'matauang' => $param['matauang'], 'kurs' => $param['kurs'], 'jumlah' => $param['jumlah']);
	$data['jumlah'] = str_replace(',', '', $data['jumlah']);

	if ($param['dk'] == 'K') {
		$data['jumlah'] = $data['jumlah'] * -1;
	}

	unset($data['numRow']);

	if ($param['dk'] == 'K') {
		$whereVp = ' and jumlah<0';
	}
	else {
		$whereVp = ' and jumlah>=0';
	}

	$qVp = selectQuery($dbname, 'keu_vpdt', 'sum(jumlah) as jumlah', 'novp=\'' . $param['novp'] . '\'' . $whereVp);
	$resVp = fetchData($qVp);

	if ($param['rpInvoiceReal'] < abs($data['jumlah'] + $resVp[0]['jumlah'])) {
		exit('Warning: Nilai tidak boleh melebihi nilai invoice: ' . number_format($param['rpInvoiceReal'], 2));
	}

	$query = insertQuery($dbname, 'keu_vpdt', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	$res = '##' . $data['noakun'] . '##' . $param['matauang'] . '##' . $param['kurs'] . '##' . $param['dk'] . '##' . $data['jumlah'];
	$result = '{res:"' . $res . '",theme:"' . $_SESSION['theme'] . '"}';
	echo $result;
	break;

case 'edit':
	$data = array('noakun' => $param['noakun'], 'matauang' => $param['matauang'], 'kurs' => $param['kurs'], 'jumlah' => str_replace(',', '', $param['jumlah']));

	if ($param['matauang'] == 'IDR') {
		$data['kurs'] = 1;
	}

	if ($param['dk'] == 'K') {
		$data *= 'jumlah';
	}

	if ($param['dk'] == 'K') {
		$whereVp = ' and jumlah<0';
	}
	else {
		$whereVp = ' and jumlah>=0';
	}

	$qVp = 'select noakun,sum(jumlah) as jumlah from ' . $dbname . '.keu_vpdt where novp=\'' . $param['novp'] . '\'' . $whereVp;
	$resVp = fetchData($qVp);
	$totalJumlah = 0;

	foreach ($resVp as $row) {
		if ($row['noakun'] != $param['noakun']) {
			$totalJumlah += $row['jumlah'];
		}
	}

	if ($param['rpInvoiceReal'] < abs($totalJumlah + $resVp[0]['jumlah'])) {
		exit('Warning: Nilai tidak boleh melebihi nilai invoice: ' . number_format($param['rpInvoiceReal'], 2));
	}

	$where = 'novp=\'' . $param['novp'] . '\' and noakun=\'' . $param['cond_noakun'] . '\'';
	$query = updateQuery($dbname, 'keu_vpdt', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	echo json_encode($param);
	break;

case 'delete':
	$where = 'novp=\'' . $param['novp'] . '\' and noakun=\'' . $param['noakun'] . '\'';
	$query = 'delete from `' . $dbname . '`.`keu_vpdt` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;
}

?>
