<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showDetail':
	$where = 'noinvoice=\'' . $param['noinvoice'] . '\'';
	$cols = 'nosj,nilai';
	$query = selectQuery($dbname, 'keu_tagihandt', $cols, $where);
	$data = fetchData($query);
	$dataShow = $data;
	$theForm2 = new uForm('transForm', $_SESSION['lang']['form'] . ' ' . $_SESSION['lang']['invoice']);
	$theForm2->addEls('nosj', $_SESSION['lang']['nosj'], '', 'text', 'L', 10);
	$theForm2->addEls('nilai', $_SESSION['lang']['nilai'], '0', 'textnum', 'L', 11);
	$theForm2->_elements[1]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
	$theTable2 = new uTable('transTable', $_SESSION['lang']['tabel'] . ' ' . $_SESSION['lang']['invoice'], $cols, $data, $dataShow);
	$formTab2 = new uFormTable('transFT', $theForm2, $theTable2, NULL, array('noinvoice'));
	$formTab2->_target = 'keu_slave_tagihan_detail';
	$formTab2->_numberFormat = '##nilai';
	echo '<fieldset><legend><b>Detail</b></legend>';
	$formTab2->render();
	echo '</fieldset>';
	break;

case 'add':
	$cols = array('nosj', 'nilai', 'noinvoice');
	$data = $param;
	unset($data['numRow']);
	$data['nilai'] = str_replace(',', '', $data['nilai']);
	$query = insertQuery($dbname, 'keu_tagihandt', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	unset($data['noinvoice']);
	$res = '';

	foreach ($data as $cont) {
		$res .= '##' . $cont;
	}

	$result = '{res:"' . $res . '",theme:"' . $_SESSION['theme'] . '"}';
	echo $result;
	break;

case 'edit':
	$data = $param;
	unset($data['noinvoice']);
	$data['nilai'] = str_replace(',', '', $data['nilai']);

	foreach ($data as $key => $cont) {
		if (substr($key, 0, 5) == 'cond_') {
			unset($data[$key]);
		}
	}

	$where = 'noinvoice=\'' . $param['noinvoice'] . '\' and nosj=\'' . $param['cond_nosj'] . '\'';
	$query = updateQuery($dbname, 'keu_tagihandt', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	echo json_encode($param);
	break;

case 'delete':
	$where = 'noinvoice=\'' . $param['noinvoice'] . '\' and nosj=\'' . $param['nosj'] . '\'';
	$query = 'delete from `' . $dbname . '`.`keu_tagihandt` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;
}

?>
