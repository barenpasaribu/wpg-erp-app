<?php


function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['nokonosemen'] = 'KS' . date('Ymdhi');
		$data['nokonosemenexp'] = '';
		$data['kodept'] = '';
		$data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
		$data['tanggal'] = '';
		$data['tanggalberangkat'] = '';
		$data['tanggaltiba'] = '';
		$data['shipper'] = '';
		$data['vessel'] = '';
		$data['franco'] = '';
		$data['asalbarang'] = '';
		$data['pengirim'] = '';
	}
	else {
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		$data['tanggalberangkat'] = tanggalnormal($data['tanggalberangkat']);
		$data['tanggaltiba'] = tanggalnormal($data['tanggaltiba']);
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
	}
	else {
		$disabled = '';
	}

	$optPT = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'tipe=\'PT\'');
	$optFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');

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

	$optKend = makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc', 'kelompokvhc=\'KD\'');
	$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'kodekelompok in(\'K002\',\'S003\')');
	$optTrans = array('DARAT' => $_SESSION['lang']['darat'], 'UDARA' => $_SESSION['lang']['udara'], 'LAUT' => $_SESSION['lang']['laut']);
	$els = array();
	$els[] = array(makeElement('nokonosemen', 'label', $_SESSION['lang']['nokonosemen']), makeElement('nokonosemen', 'text', $data['nokonosemen'], array('style' => 'width:200px', 'maxlength' => '20', 'disabled' => 'disabled')));
	$els[] = array(makeElement('nokonosemenexp', 'label', $_SESSION['lang']['nokonosemenexp']), makeElement('nokonosemenexp', 'text', $data['nokonosemenexp'], array('style' => 'width:200px', 'maxlength' => '20')));
	$els[] = array(makeElement('kodept', 'label', $_SESSION['lang']['kodept']), makeElement('kodept', 'select', $data['kodept'], array('style' => 'width:200px', $disabled => $disabled), $optPT));
	$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'text', $data['kodeorg'], array('style' => 'width:200px', 'disabled' => 'disabled')));
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tanggalberangkat', 'label', $_SESSION['lang']['tanggalberangkat']), makeElement('tanggalberangkat', 'text', $data['tanggalberangkat'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tanggaltiba', 'label', 'ETA'), makeElement('tanggaltiba', 'text', $data['tanggaltiba'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('shipper', 'label', $_SESSION['lang']['expeditor']), makeElement('shipper', 'select', $data['shipper'], array('style' => 'width:300px'), $optSupp));
	$els[] = array(makeElement('vessel', 'label', 'Nama Kapal'), makeElement('vessel', 'text', $data['vessel'], array('style' => 'width:300px')));
	$els[] = array(makeElement('franco', 'label', $_SESSION['lang']['franco']), makeElement('franco', 'select', $data['franco'], array('style' => 'width:300px'), $optFranco));
	$els[] = array(makeElement('asalbarang', 'label', $_SESSION['lang']['asalbarang']), makeElement('asalbarang', 'text', $data['asalbarang'], array('style' => 'width:300px')));
	$els[] = array(makeElement('pengirim', 'label', $_SESSION['lang']['pengirim']), makeElement('pengirim', 'select', $data['pengirim'], array('style' => 'width:300px'), $optKary));

	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'editDataTable()')) . makeElement('detailPo', 'btn', 'Add Detail from PO', array('onclick' => 'showPO(event)')) . makeElement('detailSj', 'btn', 'Add Detail from Delivery Order', array('onclick' => 'showSJ(event)')) . makeElement('detailManual', 'btn', 'Add Detail from Material List', array('onclick' => 'showMaterial(event)')));
	}

	if ($mode == 'add') {
		return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
	}

	if ($mode == 'edit') {
		return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
	}
}

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showHeadList':
	$where = '';

	if (isset($param['where'])) {
		$tmpW = str_replace('\\', '', $param['where']);
		$arrWhere = json_decode($tmpW, true);

		if (!empty($arrWhere)) {
			foreach ($arrWhere as $key => $r1) {
				if ($where != '') {
					$where .= ' and ';
				}

				$where .= $r1[0] . ' like \'%' . $r1[1] . '%\'';
			}
		}
	}

	$str = 'select karyawanid, namakaryawan from ' . $dbname . '.datakaryawan';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$nama[$bar->karyawanid] = $bar->namakaryawan;
	}

	$header = array($_SESSION['lang']['nokonosemen'], $_SESSION['lang']['nokonosemen'] . ' Expeditor', $_SESSION['lang']['pt'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['tanggalberangkat'], $_SESSION['lang']['tanggaltiba'], 'postingterimaby');
	$cols = 'nokonosemen,nokonosemenexp,kodept,tanggal,tanggalberangkat,tanggaltiba,postingby,posting,postingkirim';
	$order = 'nokonosemen desc';
	$query = selectQuery($dbname, 'log_konosemenht', $cols, $where, $order, false, $param['shows'], $param['page']);
	$data = fetchData($query);

	if (empty($where)) {
		$where = NULL;
	}

	$totalRow = getTotalRow($dbname, 'log_konosemenht', $where);

	foreach ($data as $key => $row) {
		if ($row['postingkirim'] == 1) {
			$data[$key]['switched'] = true;
		}

		if ($row['posting'] == 1) {
			$data[$key]['switched'] = true;
		}

		unset($data[$key]['posting']);
		unset($data[$key]['postingkirim']);
		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['tanggalberangkat'] = tanggalnormal($row['tanggalberangkat']);

		if (!empty($row['tanggaltiba'])) {
			$data[$key]['tanggaltiba'] = tanggalnormal($row['tanggaltiba']);
		}

		if ($row['postingby'] != 0) {
			$data[$key]['postingby'] = $nama[$row['postingby']];
		}
		else {
			$data[$key]['postingby'] = '';
		}
	}

	$tHeader = new rTable('headTable', 'headTableBody', $header, $data);
	$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
	$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');
	$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/' . $_SESSION['theme'] . '/pdf.jpg');
	$tHeader->_actions[3]->addAttr('event');
	$tHeader->_switchException = array('detailPDF');
	$tHeader->pageSetting($param['page'], $totalRow, $param['shows']);

	if (isset($param['where'])) {
		$tHeader->setWhere($arrWhere);
	}

	$tHeader->renderTable();
	break;

case 'showAdd':
	echo formHeader('add', array());
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'showEdit':
	$query = selectQuery($dbname, 'log_konosemenht', '*', 'nokonosemen=\'' . $param['nokonosemen'] . '\'');
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	echo formHeader('edit', $data);
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'add':
	$data = $_POST;
	$warning = '';

	if ($data['tanggal'] == '') {
		$warning .= 'Date is obligatory' . "\n";
	}

	if ($data['tanggalberangkat'] == '') {
		$warning .= 'Departure Date is obligatory' . "\n";
	}

	if ($warning != '') {
		echo 'Warning :' . "\n" . $warning;
		exit();
	}

	$data['tanggalberangkat'] = tanggalsystem($data['tanggalberangkat']);
	$data['tanggaltiba'] = tanggalsystem($data['tanggaltiba']);
	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['penerima'] = 0;
	$data['tanggalterima'] = '0000-00-00';
	$data['postingby'] = 0;
	$cols = array();

	foreach ($data as $key => $row) {
		$cols[] = $key;
	}

	$query = insertQuery($dbname, 'log_konosemenht', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'edit':
	$data = $_POST;
	$where = 'nokonosemen=\'' . $data['nokonosemen'] . '\'';
	unset($data['nokonosemen']);
	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['tanggaltiba'] = tanggalsystem($data['tanggaltiba']);
	$data['tanggalberangkat'] = tanggalsystem($data['tanggalberangkat']);
	$query = updateQuery($dbname, 'log_konosemenht', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'delete':
	$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'';
	$query = 'delete from `' . $dbname . '`.`log_konosemenht` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;

case 'posting':
	$data = array('postingkirim' => 1);
	$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'';
	$query = updateQuery($dbname, 'log_konosemenht', $data, $where);

	echo 'DB Error : ' . mysql_error();
}

?>
