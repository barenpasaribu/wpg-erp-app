<?php


function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['nosj'] = 'SJ' . date('Ymdhi');
		$data['kodept'] = '';
		$data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
		$data['tanggal'] = '';
		$data['tanggalkirim'] = '';
		$data['expeditor'] = '';
		$data['pic'] = '';
		$data['nopol'] = '';
		$data['jeniskend'] = '';
		$data['driver'] = '';
		$data['hpdriver'] = '';
		$data['pengirim'] = '';
		$data['penerima'] = '';
		$data['checkedby'] = '';
		$data['franco'] = '';
		$data['transportasi'] = 'DARAT';
	}
	else {
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		$data['tanggalkirim'] = tanggalnormal($data['tanggalkirim']);
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
	}
	else {
		$disabled = '';
	}

	$optPT = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'tipe=\'PT\'');
	$optFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');
	$optKend = makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc', 'kelompokvhc=\'KD\'');
	$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'kodekelompok in(\'K002\',\'S003\')');
	$optTrans = array('DARAT' => $_SESSION['lang']['darat'], 'UDARA' => $_SESSION['lang']['udara'], 'LAUT' => $_SESSION['lang']['laut']);

	if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
		$qKary = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan  where bagian=\'HO_PROC\' and lokasitugas like \'%HO%\' ';
	}
	else {
		$qKary = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan  where bagian=\'HO_PROC\' and lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' ';
	}

	$resKary = fetchData($qKary);
	$optKary = array();

	foreach ($resKary as $row) {
		$optKary[$row['karyawanid']] = $row['namakaryawan'];
	}

	$tmpKend = array('Colt Diesel', 'Fuso', 'Tronton', 'Buildup', 'Trailer', 'Kereta Api', 'Pesawat');

	foreach ($tmpKend as $det) {
		$optKend[$det] = $det;
	}

	unset($optKend['DUMPTRUCK']);
	$els = array();
	$els[] = array(makeElement('nosj', 'label', $_SESSION['lang']['nosj']), makeElement('nosj', 'text', $data['nosj'], array('style' => 'width:200px', 'maxlength' => '20', 'disabled' => 'disabled')));
	$els[] = array(makeElement('kodept', 'label', $_SESSION['lang']['kodept']), makeElement('kodept', 'select', $data['kodept'], array('style' => 'width:200px', $disabled => $disabled), $optPT));
	$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'text', $data['kodeorg'], array('style' => 'width:200px', 'disabled' => 'disabled')));
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tanggalkirim', 'label', $_SESSION['lang']['tgl_kirim']), makeElement('tanggalkirim', 'text', $data['tanggalkirim'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('expeditor', 'label', $_SESSION['lang']['expeditor']), makeElement('expeditor', 'select', $data['expeditor'], array('style' => 'width:300px'), $optSupp));
	$els[] = array(makeElement('nopol', 'label', $_SESSION['lang']['nopol']), makeElement('nopol', 'text', $data['nopol'], array('style' => 'width:300px')));
	$els[] = array(makeElement('jeniskend', 'label', $_SESSION['lang']['jeniskend']), makeElement('jeniskend', 'select', $data['jeniskend'], array('style' => 'width:300px'), $optKend));
	$els[] = array(makeElement('driver', 'label', $_SESSION['lang']['supir']), makeElement('driver', 'text', $data['driver'], array('style' => 'width:300px')));
	$els[] = array(makeElement('hpdriver', 'label', $_SESSION['lang']['nohp'] . ' ' . $_SESSION['lang']['supir']), makeElement('hpdriver', 'textnum', $data['hpdriver'], array('style' => 'width:300px')));
	$els[] = array(makeElement('pengirim', 'label', $_SESSION['lang']['pengirim']), makeElement('pengirim', 'select', $data['pengirim'], array('style' => 'width:300px'), $optKary));
	$els[] = array(makeElement('penerima', 'label', $_SESSION['lang']['penerima']), makeElement('penerima', 'text', $data['penerima'], array('style' => 'width:300px')));
	$els[] = array(makeElement('checkedby', 'label', $_SESSION['lang']['cek']), makeElement('checkedby', 'text', $data['checkedby'], array('style' => 'width:300px')));
	$els[] = array(makeElement('franco', 'label', $_SESSION['lang']['franco']), makeElement('franco', 'select', $data['franco'], array('style' => 'width:300px'), $optFranco));
	$els[] = array(makeElement('transportasi', 'label', $_SESSION['lang']['transportasi']), makeElement('transportasi', 'select', $data['transportasi'], array('style' => 'width:300px'), $optTrans));

	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'editDataTable()')) . makeElement('detailPo', 'btn', 'Add Detail from PO', array('onclick' => 'showPO(event)')) . makeElement('detailPl', 'btn', 'Add Detail from Package List', array('onclick' => 'showPL(event)')) . makeElement('detailManual', 'btn', 'Add Detail from Material List', array('onclick' => 'showMaterial(event)')));
	}

	if ($mode == 'add') {
		return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
	}

	if ($mode == 'edit') {
		return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
	}
}


?>
