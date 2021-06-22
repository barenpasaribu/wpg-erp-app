<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$IDs = $_POST;
$namaKeg = $_POST['namakegiatan'];
$uom = $_POST['satuan'];
unset($IDs['namakegiatan']);
unset($IDs['satuan']);
$tmpField = getFieldName('setup_kegiatannorma', 'array');
$tmpPrim = getPrimary($dbname, 'setup_kegiatannorma');
$fieldNew = $field = array();
$fieldStr = '';

foreach ($tmpField as $row) {
	if (($row != 'kodeorg') && ($row != 'kodekegiatan') && ($row != 'kelompok')) {
		$fieldNew[] = $field[] = $row;
		$fieldStr .= '##' . $row;

		if ($row == 'kodebarang') {
			$fieldNew[] = 'namabarang';
			$fieldStr .= '##namabarang';
		}
	}
}

$primaryStr = '';

foreach ($tmpPrim as $row) {
	$primaryStr .= '##' . $row;
}

$i = 0;

foreach ($IDs as $key => $row) {
	if ($i == 0) {
		$where = $key . '=\'' . $row . '\'';
	}
	else {
		$where .= ' AND ' . $key . '=\'' . $row . '\'';
	}

	++$i;
}

$query = selectQuery($dbname, 'setup_kegiatannorma', $field, $where);
$tmpData = fetchData($query);
$data = array();
$listInv = array();

foreach ($tmpData as $key => $row) {
	foreach ($row as $head => $cont) {
		$data[$key][$head] = $cont;

		if ($head == 'kodebarang') {
			$data[$key]['namabarang'] = '';
			$listInv[] = $cont;
		}
	}
}

$header = array();

foreach ($fieldNew as $row) {
	$header[] = $_SESSION['lang'][$row];
}

$header[] = 'Z';
$primary = '<table>';
$primary .= '<tr><td>' . makeElement('kodeorg_norma', 'label', $_SESSION['lang']['kodeorg']) . '</td><td>: ' . makeElement('kodeorg_norma', 'text', $IDs['kodeorg'], array('disabled' => 'disabled', 'style' => 'width:100px')) . '</td></tr><tr><td>';
$primary .= makeElement('kodekegiatan_norma', 'label', $_SESSION['lang']['kodekegiatan']) . '</td><td>: ' . makeElement('kodekegiatan_norma', 'text', $IDs['kodekegiatan'], array('disabled' => 'disabled', 'style' => 'width:50px')) . '&nbsp;' . makeElement('namakegiatan_norma', 'text', $namaKeg, array('disabled' => 'disabled', 'style' => 'width:200px')) . '</td></tr><tr><td>';
$primary .= makeElement('kelompok_norma', 'label', $_SESSION['lang']['kelompok']) . '</td><td>: ' . makeElement('kelompok_norma', 'text', $IDs['kelompok'], array('disabled' => 'disabled', 'style' => 'width:100px')) . '</td></tr>';
$primary .= '</table>';
$content = array();
$optTopografi = makeOption($dbname, 'setup_topografi', 'topografi,keterangan');
$optTipeAng = getEnum($dbname, 'setup_kegiatannorma', 'tipeanggaran');
$whereNB = '';

foreach ($listInv as $key => $row) {
	if ($key == 0) {
		$whereNB .= 'kodebarang=' . $row;
	}
	else {
		$whereNB .= ' or kodebarang=' . $row;
	}
}

if ($whereNB != '') {
	$query = selectQuery($dbname, 'log_5masterbarang', 'kodebarang,namabarang,satuan', $whereNB);
	$resBar = fetchData($query);
	$namaBarang = array();
	$satuanBarang = array();

	foreach ($resBar as $row) {
		$namaBarang[$row['kodebarang']] = $row['namabarang'];
		$satuanBarang[$row['kodebarang']] = $row['satuan'];
	}
}

foreach ($data as $key => $row) {
	$data[$key]['namabarang'] = $namaBarang[$row['kodebarang']];
}

$j = 0;

if ($data != array()) {
	foreach ($data as $i => $row) {
		foreach ($row as $key => $data) {
			if ($key == 'topografi') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'select', $data, array('style' => 'width:100px', 'disabled' => 'disabled'), array($data => $optTopografi[$data]));
			}
			else if ($key == 'tipeanggaran') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'select', $data, array('style' => 'width:100px', 'disabled' => 'disabled'), array($data => $optTipeAng[$data]));
			}
			else if ($key == 'kodebarang') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'text', $data, array('style' => 'width:70px', 'readonly' => 'readonly', 'disabled' => 'disabled')) . makeElement('getInvBtn_' . $i, 'btn', 'Cari', array('onclick' => 'getInv(event,\'' . $i . '\')', 'disabled' => 'disabled'));
			}
			else if ($key == 'namabarang') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'txt', $data, array('style' => 'width:120px', 'disabled' => 'disabled'));
			}
			else if ($key == 'kuantitas1') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'textnum', $data, array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)')) . '&nbsp;<span id=\'uom1_' . $i . '\'>' . $satuanBarang[$row['kodebarang']] . '</span>';
			}
			else if ($key == 'kuantitas2') {
				$content[$i][$key] = makeElement($key . '_' . $i, 'textnum', $data, array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)')) . '&nbsp;<span id=\'uom2_' . $i . '\'>' . $uom . '</span>';
			}
			else {
				$content[$i][$key] = makeElement($key . '_' . $i, 'textnum', $data, array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)'));
			}
		}

		$content[$i]['Z'] = '<img id=\'editNorma_' . $i . '\' title=\'Edit\' class=zImgBtn onclick="editNorma(\'' . $i . '\',\'' . $primaryStr . '\',\'' . $fieldStr . '\')" src=\'images/' . $_SESSION['theme'] . '/save.png\'/>';
		$content[$i] .= 'Z';
		$j = $i + 1;
	}
}

foreach ($fieldNew as $row) {
	if ($row == 'topografi') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'select', '', array('style' => 'width:100px'), $optTopografi);
	}
	else if ($row == 'tipeanggaran') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'select', '', array('style' => 'width:100px'), $optTipeAng);
	}
	else if ($row == 'kodebarang') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'text', '', array('style' => 'width:70px', 'readonly' => 'readonly')) . makeElement('getInvBtn_' . $j, 'btn', 'Cari', array('onclick' => 'getInv(event,\'' . $j . '\')'));
	}
	else if ($row == 'namabarang') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly'));
	}
	else if ($row == 'kuantitas1') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return angka_doang(event)')) . '&nbsp;<span id=\'uom1_' . $j . '\'></span>';
	}
	else if ($row == 'kuantitas2') {
		$content[$j][$row] = makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return angka_doang(event)')) . '&nbsp;<span id=\'uom2_' . $j . '\'>' . $uom . '</span>';
	}
	else {
		$content[$j][$row] = makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return angka_doang(event)'));
	}
}

$content[$j]['Z'] = '<img id=\'addNorma_' . $j . '\' title=\'Tambah\' class=zImgBtn onclick="addNorma(\'' . $j . '\',\'' . $primaryStr . '\',\'' . $fieldStr . '\')" src=\'images/plus.png\'/>';
$content[$j] .= 'Z';
$mainTable = makeTable('normaTable', 'normaBody', $header, $content, array(), true, 'detail_tr');
echo '<div id=\'mainTable\' style=\'float:left;\'>';
echo '<fieldset><legend><b>Norma</b></legend>';
echo '<div style=\'overflow:auto;width:770px;max-height:270px\'>';
echo $primary;
echo $mainTable;
echo '</div></fieldset></div>';

?>
