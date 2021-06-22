<?php


function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['novp'] = '';
		$data['tanggal'] = '';
		$data['nopo'] = '';
		$data['penjelasan'] = '';
		$data['noinv1'] = '';
		$data['noinv2'] = '';
		$data['noinv3'] = '';
		$data['noinv4'] = '';
		$data['tanggalterima'] = '';
		$data['tanggalbayar'] = '';
		$data['tanggaljatuhtempo'] = '';
	}
	else {
		$data['tanggal'] = tanggalnormal($data['tanggal'], 0);

		if (!empty($data['tanggalterima'])) {
			$data['tanggalterima'] = tanggalnormal($data['tanggalterima'], 0);
		}

		if (!empty($data['tanggalbayar'])) {
			$data['tanggalbayar'] = tanggalnormal($data['tanggalbayar'], 0);
		}

		if (!empty($data['tanggaljatuhtempo'])) {
			$data['tanggaljatuhtempo'] = tanggalnormal($data['tanggaljatuhtempo'], 0);
		}
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
		$query = selectQuery($dbname, 'keu_vp_inv', '*', 'novp=\'' . $data['novp'] . '\'');
		$res = fetchData($query);
		$listInvoice = '';

		foreach ($res as $key => $row) {
			$listInvoice .= '<div id=\'noinv_' . $key . '\'>' . $row['noinv'] . '</div>';
		}
	}
	else {
		$disabled = '';
		$listInvoice = '';
	}

	$els = array();
	$els[] = array(makeElement('novp', 'label', $_SESSION['lang']['novp']), makeElement('novp', 'text', $data['novp'], array('style' => 'width:200px', 'maxlength' => '25', 'disabled' => 'disabled')));
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('nopo', 'label', $_SESSION['lang']['nopo']), makeElement('nopo', 'text', $data['nopo'], array('style' => 'width:150px;cursor:pointer', 'maxlength' => '25', 'readonly' => 'readonly', $disabled => $disabled, 'onclick' => 'getPO(event)', 'placeholder' => 'Click to get PO')));
	$els[] = array($_SESSION['lang']['noinvoice'], '<fieldset><legend><b>List</b></legend><div id=\'listInvoice\'>' . $listInvoice . '</div></fieldset>' . makeElement('totalRpInv', 'hidden', ''));
	$els[] = array(makeElement('tanggalterima', 'label', $_SESSION['lang']['tglterima']), makeElement('tanggalterima', 'text', $data['tanggalterima'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tanggalbayar', 'label', $_SESSION['lang']['tanggalbayar']), makeElement('tanggalbayar', 'text', $data['tanggalbayar'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tanggaljatuhtempo', 'label', $_SESSION['lang']['jatuhtempo']), makeElement('tanggaljatuhtempo', 'text', $data['tanggaljatuhtempo'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('penjelasan', 'label', $_SESSION['lang']['keterangan']), makeElement('penjelasan', 'text', $data['penjelasan'], array('style' => 'width:250px', 'maxlength' => '100')));

	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn','UPDATE', array('onclick' => 'editDataTable()')));
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
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showHeadList':
	$where = 'kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' ';

	if (isset($param['where'])) {
		$arrWhere = json_decode(str_replace('\\', '', $param['where']), true);

		if (!empty($arrWhere)) {
			foreach ($arrWhere as $key => $r1) {
				$where .= ' and ' . $r1[0] . ' like \'%' . $r1[1] . '%\'';
			}
		}
	}

	$header = array($_SESSION['lang']['novp'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nopo'], $_SESSION['lang']['keterangan']);
	$align = explode(',', 'C,C,C,L');
	$cols = 'novp,tanggal,nopo,penjelasan,posting';
	$query = selectQuery($dbname, 'keu_vpht', $cols, $where, 'tanggal desc, novp desc', false, $param['shows'], $param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname, 'keu_vpht', $where);
	$whereAkun = '';
	$whereOrg = '';
	$i = 0;

	foreach ($data as $key => $row) {
		if ($row['posting'] == 1) {
			$data[$key]['switched'] = true;
		}

		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		unset($data[$key]['posting']);
	}

	$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', 'kodeaplikasi=\'keuangan\'');
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	$dataShow = $data;
	$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
	$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
	$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');
	$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/' . $_SESSION['theme'] . '/pdf.jpg');
	$tHeader->addAction('zoom', 'Lihat Detail', 'images/' . $_SESSION['theme'] . '/zoom.png');

	if (($postJabatan != $_SESSION['empl']['kodejabatan']) && ($_SESSION['empl']['tipelokasitugas'] != 'HOLDING')) {
		$tHeader->_actions[2]->_name = '';
	}

	$tHeader->_actions[3]->addAttr('event');
	$tHeader->_actions[4]->addAttr('event');
	$tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
	$tHeader->_switchException = array('detailPDF', 'zoom');

	if (isset($param['where'])) {
		$tHeader->setWhere($arrWhere);
	}

	$tHeader->setAlign($align);
	$tHeader->renderTable();
	break;

case 'showAdd':
	echo formHeader('add', array());
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'showEdit':
	$query = selectQuery($dbname, 'keu_vpht', '*', 'novp=\'' . $param['novp'] . '\'');
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

	if ($data['nopo'] == '') {
		$warning .= 'No. PO is obligatory' . "\n";
	}

	if ($data['noinv'][0] == '') {
		$warning .= 'Invoice is obligatory' . "\n" . 'At least an invoice must be selected';
	}

	if ($warning != '') {
		echo 'Warning :' . "\n" . $warning;
		exit();
	}

	$sekarang = tanggalsystemw($data['tanggal']);

	if ($sekarang < $_SESSION['org']['period']['start']) {
		echo 'Validation Error : Date out or range';
		break;
	}

	$data['novp'] = '[' . $_SESSION['empl']['lokasitugas'] . ']' . date('YmdHis');
	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['tanggalterima'] = tanggalsystem($data['tanggalterima']);
	$data['tanggalbayar'] = tanggalsystem($data['tanggalbayar']);
	$data['tanggaljatuhtempo'] = tanggalsystem($data['tanggaljatuhtempo']);
	$data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
	$data['updateby'] = $_SESSION['standard']['userid'];
	$noinv = $data['noinv'];
	$nopo = $data['nopo'];
	unset($data['noinv']);
	$cols = array('novp', 'tanggal', 'tanggalterima', 'tanggalbayar', 'tanggaljatuhtempo', 'nopo', 'penjelasan', 'kodeorg', 'updateby');
	$query = insertQuery($dbname, 'keu_vpht', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}
	else {
		foreach ($noinv as $no) {
			$data1 = array('novp' => $data['novp'], 'noinv' => $no);
			$query = insertQuery($dbname, 'keu_vp_inv', $data1);
			mysql_query($query);
		}

		foreach ($nopo as $no) {
			$data1 = array('novp' => $data['novp'], 'nopo' => $nopo);
			$query = insertQuery($dbname, 'keu_vp_po', $data1);
			mysql_query($query);
		}

		echo $data['novp'];
	}

	break;

case 'edit':
	$data = $_POST;
	$where = 'novp=\'' . $data['novp'] . '\'';
	unset($data['novp']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['tanggalterima'] = tanggalsystem($data['tanggalterima']);
	$data['tanggalbayar'] = tanggalsystem($data['tanggalbayar']);
	$data['tanggaljatuhtempo'] = tanggalsystem($data['tanggaljatuhtempo']);
	$query = updateQuery($dbname, 'keu_vpht', $data, $where);
	$querydt = updateQuery($dbname, 'keu_vpht', $datadt, $wheredt);

	if (!mysql_query($query)) {
		echo 'DB Error ht : ' . mysql_error();
	}

	break;

case 'delete':
	$where = 'novp=\'' . $param['novp'] . '\'';
	$query = 'delete from `' . $dbname . '`.`keu_vpht` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}
	else {
		$query = 'delete from `' . $dbname . '`.`keu_vp_inv` where ' . $where;
		mysql_query($query);
	}

	break;

case 'showInvoice':
	$where = 'novp=\'' . $param['novp'] . '\'';
	$query = selectQuery($dbname, 'keu_vp_inv', '*', $where);
	$res = fetchData($query);
	echo '<div>No VP: ' . $param['novp'] . '</div>';
	echo '<table class=data><thead><tr class=rowheader><td>No Invoice</td></tr></thead><tbody>';

	foreach ($res as $row) {
		echo '<tr class=rowcontent><td>' . $row['noinv'] . '</td></tr>';
	}

	echo '</tbody></table>';
	break;
}

?>
