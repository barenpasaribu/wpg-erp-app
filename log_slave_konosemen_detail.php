<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showDetail':
	$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'';
	$cols = '*';
	$query = selectQuery($dbname, 'log_konosemendt', $cols, $where);
	$data = fetchData($query);
	$kodeBarang = '';

	foreach ($data as $key => $row) {
		if (0 < $key) {
			$kodeBarang .= ',';
		}

		$kodeBarang .= '\'' . $row['kodebarang'] . '\'';
	}

	if (!empty($kodeBarang)) {
		$optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang', 'kodebarang in (' . $kodeBarang . ')');
	}

	$table = '';
	$table .= '<fieldset style=\'margin-top:10px\'><legend><b>Detail</b></legend>';
	$table .= '<table cellpadding=2 cellspacing=1 border=0 class=\'sortable\'>';
	$table .= '<thead><tr class=rowheader>';
	$table .= '<td>' . $_SESSION['lang']['nosj'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jenis'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['nopo'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td colspan=2>' . $_SESSION['lang']['action'] . '</td>';
	$table .= '</tr></thead>';
	$table .= '<tbody>';

	foreach ($data as $key => $row) {
		$table .= '<tr id=\'detRow_' . $key . '\' class=rowcontent>';
		$table .= '<td id=\'t_nosj_' . $key . '\'>' . $row['nosj'] . '</td>';
		$table .= '<td id=\'t_jenis_' . $key . '\'>' . $row['jenis'] . '</td>';
		$table .= '<td id=\'t_kodebarang_' . $key . '\'>' . $row['kodebarang'] . '</td>';
		$table .= '<td id=\'t_namabarang_' . $key . '\'>';

		if (isset($optBarang[$row['kodebarang']])) {
			$table .= $optBarang[$row['kodebarang']];
		}

		$table .= '</td>';

		if ($row['jenis'] == 'M') {
			$table .= '<td id=\'t_nopo_' . $key . '\' value=\'' . $row['nopo'] . '\'>';
			$table .= makeElement('el_nopo_' . $key, 'text', $row['nopo'], array('onkeyup' => 'changeBg(getById(\'detRow_' . $key . '\'),\'#FF00FF\')'));
			$table .= '</td>';
		}
		else {
			$table .= '<td id=\'t_nopo_' . $key . '\' value=\'' . $row['nopo'] . '\'>' . $row['nopo'] . '</td>';
		}

		if ($row['jenis'] == 'M') {
			$table .= '<td id=\'t_nopp_' . $key . '\'>';
			$table .= makeElement('el_nopp_' . $key, 'text', $row['nopp'], array('onkeyup' => 'changeBg(getById(\'detRow_' . $key . '\'),\'#FF00FF\')'));
			$table .= '</td>';
		}
		else {
			$table .= '<td id=\'t_nopp_' . $key . '\'>' . $row['nopp'] . '</td>';
		}

		$table .= '<td id=\'t_jumlah_' . $key . '\' align=right>';

		if ($row['jenis'] != 'PL') {
			$table .= makeElement('el_jumlah_' . $key, 'textnum', $row['jumlah'], array('onkeyup' => 'changeBg(getById(\'detRow_' . $key . '\'),\'#FF00FF\')'));
		}
		else {
			$table .= $row['jumlah'];
		}

		$table .= '</td><td id=\'t_satuan_' . $key . '\'>' . $row['satuanpo'] . '</td><td>';

		if ($row['jenis'] != 'PL') {
			$table .= '<img src=\'images/save.png\' class=zImgBtn onclick=\'saveDetail(' . $key . ')\' style=\'cursor:pointer\'>';
		}

		$table .= '</td><td><img src=\'images/delete_32.png\' class=zImgBtn onclick=\'deleteDetail(' . $key . ')\' style=\'cursor:pointer\'></td>';
		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '</table></fieldset>';
	echo $table;
	break;

case 'add2detail':
	$data = array();

	foreach ($_POST['data'] as $key => $row) {
		if ($_POST['jenis'] == 'po') {
			$jumlah = $row['jumlah'];
			$satuan = $row['satuan'];
		}
		else if ($_POST['jenis'] == 'pl') {
			$jumlah = 1;
			$satuan = 'PETI';
		}
		else {
			$jumlah = 0;
			$satuan = $row['satuan'];
		}

		$data[] = array('nokonosemen' => $_POST['nokonosemen'], 'kodept' => $_POST['kodept'], 'kodebarang' => $row['kodebarang'], 'jenis' => isset($row['jenis']) ? $row['jenis'] : strtoupper($_POST['jenis']), 'jumlah' => $jumlah, 'satuanpo' => $satuan, 'nopo' => isset($row['nopo']) ? $row['nopo'] : '', 'nopp' => isset($row['nopp']) ? $row['nopp'] : '', 'nosj' => isset($row['nosj']) ? $row['nosj'] : '');
	}

	$cols = array('nokonosemen', 'kodept', 'kodebarang', 'jenis', 'jumlah', 'satuanpo', 'nopo', 'nopp', 'nosj');
	$query = insertQuery($dbname, 'log_konosemendt', $data, $cols);
	echo $query;

	exit('Error DB: ' . mysql_error());
	mysql_query($query) || true;
	break;

case 'saveDetail':
	$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'' . ' and kodebarang=\'' . $param['kodebarang'] . '\' and nopo=\'' . $param['nopo'] . '\'';
	$cols = '*';
	$data = array('jumlah' => $param['jumlah']);

	if (isset($param['newNopo'])) {
		$data['nopo'] = $param['newNopo'];
		$data['nopp'] = $param['newNopp'];
	}

	$query = updateQuery($dbname, 'log_konosemendt', $data, $where);

	exit('Error DB: ' . mysql_error());
	mysql_query($query) || true;
	break;

case 'deleteDetail':
	$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'' . ' and kodebarang=\'' . $param['kodebarang'] . '\' and nopo=\'' . $param['nopo'] . '\'';
	$query = deleteQuery($dbname, 'log_konosemendt', $where);

	exit('Error DB: ' . mysql_error());
	mysql_query($query) || true;
	break;
}

?>
