<?php


function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['tanggal'] = '';
		$data['kodeorgpengirim'] = '0';
		$data['kodeorgpenerima'] = '';
		$data['noakunpengirim'] = '';
		$data['noakunpenerima'] = '';
		$data['jumlah'] = '0';
		$data['nogiro'] = '';
		$data['tglgiro'] = '';
		$data['tgljatuhtempo'] = '';
	}
	else {
		$data['jumlah'] = number_format($data['jumlah']);
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
	}
	else {
		$disabled = '';
	}

	$optOrgKirim = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'');
	$optOrgTerima = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and kodeorganisasi<>\'' . $_SESSION['empl']['lokasitugas'] . '\'');
	$whereJam = ' detail=1 and (pemilik=\'' . $_SESSION['empl']['tipelokasitugas'] . '\' or pemilik=\'GLOBAL\' or pemilik=\'' . $_SESSION['empl']['lokasitugas'] . '\')';
	$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam, '2');
	$els = array();
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('kodeorgpengirim', 'label', $_SESSION['lang']['kodeorgpengirim']), makeElement('kodeorgpengirim', 'select', $data['kodeorgpengirim'], array('style' => 'width:300px'), $optOrgKirim));
	$els[] = array(makeElement('kodeorgpenerima', 'label', $_SESSION['lang']['kodeorgpenerima']), makeElement('kodeorgpenerima', 'select', $data['kodeorgpenerima'], array('style' => 'width:300px'), $optOrgTerima));
	$els[] = array(makeElement('noakunpengirim', 'label', $_SESSION['lang']['noakunpengirim']), makeElement('noakunpengirim', 'select', $data['noakunpengirim'], array('style' => 'width:300px'), $optAkun));
	$els[] = array(makeElement('noakunpenerima', 'label', $_SESSION['lang']['noakunpenerima']), makeElement('noakunpenerima', 'select', $data['noakunpenerima'], array('style' => 'width:300px'), $optAkun));
	$els[] = array(makeElement('jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('jumlah', 'textnum', $data['jumlah'], array('style' => 'width:200px', 'this.value=remove_comma(this);onchange' => 'this.value = _formatted(this)')));
	$els[] = array(makeElement('nogiro', 'label', $_SESSION['lang']['nogiro']), makeElement('nogiro', 'text', $data['nogiro'], array('style' => 'width:300px')));
	$els[] = array(makeElement('tglgiro', 'label', $_SESSION['lang']['tglgiro']), makeElement('tglgiro', 'date', $data['tglgiro'], array('style' => 'width:300px')));
	$els[] = array(makeElement('tgljatuhtempo', 'label', $_SESSION['lang']['tgljatuhtempo']), makeElement('tgljatuhtempo', 'date', $data['tgljatuhtempo'], array('style' => 'width:300px')));

	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'editDataTable()')));
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
	$where = 'kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

	if (isset($param['where'])) {
		$arrWhere = json_decode($param['where'], true);

		if (!empty($arrWhere)) {
			foreach ($arrWhere as $key => $r1) {
				if ($key == 0) {
					$where .= $r1[0] . ' like \'%' . $r1[1] . '%\'';
				}
				else {
					$where .= ' and ' . $r1[0] . ' like \'%' . $r1[1] . '%\'';
				}
			}
		}
		else {
			$where .= NULL;
		}
	}
	else {
		$where .= NULL;
	}

	$header = array('Tanggal', 'Pengirim', 'Penerima', 'Jumlah', 'No. Giro');
	$cols = 'tanggal,kodeorgpengirim,kodeorgpenerima,jumlah,nogiro,postingkirim,postingterima';
	$query = selectQuery($dbname, 'keu_transferdana', $cols, 'kodeorgpengirim=\'' . $_SESSION['empl']['lokasitugas'] . '\' or ' . '(kodeorgpenerima=\'' . $_SESSION['empl']['lokasitugas'] . '\' and postingkirim=1)', '', false, 10, 1);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname, 'pabrik_masukkeluartangki');

	foreach ($data as $key => $row) {
		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);

		if ($row['kodeorgpengirim'] == $_SESSION['empl']['lokasitugas']) {
			if ($row['postingkirim'] == 1) {
				$data[$key]['switched'] = 1;
			}
		}

		if ($row['kodeorgpenerima'] == $_SESSION['empl']['lokasitugas']) {
			$data[$key]['switched'] = 1;

			if ($row['postingkirim'] == 1) {
				if ($row['postingterima'] != 1) {
					$data[$key]['noSwitchList'] = array('postingData');
				}
			}
			else {
				$data[$key]['noShow'] = 1;
			}
		}

		unset($data[$key]['postingkirim']);
		unset($data[$key]['postingterima']);
	}

	$dataShow = $data;

	foreach ($dataShow as $key => $row) {
		$dataShow[$key]['jumlah'] = number_format($row['jumlah'], 0);
	}

	$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
	$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
	$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');
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
	$tgl = tanggalsystem($param['tanggal']);
	$query = selectQuery($dbname, 'keu_transferdana', '*', 'tanggal=\'' . $tgl . '\' and kodeorgpengirim=\'' . $param['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $param['kodeorgpenerima'] . '\' and nogiro=\'' . $param['nogiro'] . '\'');
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit', $data);
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'add':
	$data = $_POST;
	$warning = '';

	if ($data['tanggal'] == '') {
		$warning .= 'Tanggal harus diisi' . "\n";
	}

	if ($warning != '') {
		echo 'Warning :' . "\n" . $warning;
		exit();
	}

	if ($data['tglgiro'] == '') {
		$data['tglgiro'] = '00-00-0000';
	}

	if ($data['tgljatuhtempo'] == '') {
		$data['tgljatuhtempo'] = '00-00-0000';
	}

	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['tglgiro'] = tanggalsystem($data['tglgiro']);
	$data['tgljatuhtempo'] = tanggalsystem($data['tgljatuhtempo']);
	$data['userid'] = $_SESSION['standard']['userid'];
	$data['jumlah'] = str_replace(',', '', $data['jumlah']);
	$cols = array('tanggal', 'kodeorgpengirim', 'kodeorgpenerima', 'noakunpengirim', 'noakunpenerima', 'jumlah', 'nogiro', 'tglgiro', 'tgljatuhtempo', 'userid');
	$query = insertQuery($dbname, 'keu_transferdana', $data, $cols);
	echo $query;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'edit':
	$data = $_POST;
	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['jumlah'] = str_replace(',', '', $data['jumlah']);
	$where = 'tanggal=\'' . $data['tanggal'] . '\' and kodeorgpengirim=\'' . $data['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $data['kodeorgpenerima'] . '\' and nogiro=\'' . $data['nogiro'] . '\'';
	$query = updateQuery($dbname, 'keu_transferdana', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'delete':
	$where = 'tanggal=\'' . $data['tanggal'] . '\' and kodeorgpengirim=\'' . $data['kodeorgpengirim'] . '\' and kodeorgpenerima=\'' . $data['kodeorgpenerima'] . '\' and nogiro=\'' . $data['nogiro'] . '\'';
	$query = 'delete from `' . $dbname . '`.`keu_transferdana` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;
}

?>
