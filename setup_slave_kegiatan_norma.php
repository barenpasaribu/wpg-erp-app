<?php


session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$data = $_POST;
unset($data['proses']);

switch ($_POST['proses']) {
case 'add':
	foreach ($data as $key => $row) {
		if ($row == '') {
			echo 'Error : Data ' . $key . ' tidak boleh kosong';
			exit();
		}
	}

	unset($data['namabarang']);
	unset($data['satuan']);
	$query = insertQuery($dbname, 'setup_kegiatannorma', $data);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
	else {
		echo $_SESSION['theme'];
	}

	break;

case 'edit':
	$data = $_POST;
	unset($data['proses']);
	unset($data['primary']);
	unset($data['primVal']);
	$primary = explode('##', $_POST['primary']);
	$primVal = explode('##', $_POST['primVal']);
	unset($primary['namabarang']);
	unset($primary['satuan']);
	unset($data['namabarang']);
	unset($data['satuan']);
	$where = '';
	$i = 1;

	while ($i < count($primary)) {
		if ($i == 1) {
			$where .= '`' . $primary[$i] . '`=\'' . $primVal[$i] . '\'';
		}
		else {
			$where .= ' AND `' . $primary[$i] . '`=\'' . $primVal[$i] . '\'';
		}

		++$i;
	}

	$query = updateQuery($dbname, 'setup_kegiatannorma', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'delete':
	$data = $_POST;
	unset($data['proses']);
	unset($data['primary']);
	unset($data['primVal']);
	$primary = explode('##', $_POST['primary']);
	$primVal = explode('##', $_POST['primVal']);
	unset($primary['namabarang']);
	unset($primary['satuan']);
	unset($data['namabarang']);
	unset($data['satuan']);
	$where = '';
	$i = 1;

	while ($i < count($primary)) {
		if ($i == 1) {
			$where .= $primary[$i] . '=\'' . $primVal[$i] . '\'';
		}
		else {
			$where .= ' AND ' . $primary[$i] . '=\'' . $primVal[$i] . '\'';
		}

		++$i;
	}

	$query = 'delete from `' . $dbname . '`.`setup_kegiatannorma` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'addRow':
	$tmpField = explode('##', $_POST['field']);
	$j = $_POST['numRow'];
	$primaryStr = $_POST['primary'];
	$fieldStr = $_POST['field'];

	foreach ($tmpField as $key => $row) {
		if ($key != 0) {
			$field[] = $row;
		}
	}

	$optTopografi = makeOption($dbname, 'setup_topografi', 'topografi,keterangan');
	$optTipeAng = getEnum($dbname, 'setup_kegiatannorma', 'tipeanggaran');
	$content = '';

	foreach ($field as $row) {
		if ($row == 'topografi') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'select', '', array('style' => 'width:100px'), $optTopografi) . '</td>';
		}
		else if ($row == 'tipeanggaran') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'select', '', array('style' => 'width:100px'), $optTipeAng) . '</td>';
		}
		else if ($row == 'kodebarang') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'text', '', array('style' => 'width:70px', 'readonly' => 'readonly')) . makeElement('getInvBtn_' . $j, 'btn', 'Cari', array('onclick' => 'getInv(event,\'' . $j . '\')')) . '</td>';
		}
		else if ($row == 'namabarang') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly')) . '</td>';
		}
		else if ($row == 'kuantitas1') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)')) . '&nbsp;<span id=\'uom1_' . $j . '\'></span></td>';
		}
		else if ($row == 'kuantitas2') {
			$content .= '<td>' . makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)')) . '&nbsp;<span id=\'uom2_' . $j . '\'></span></td>';
		}
		else {
			$content .= '<td>' . makeElement($row . '_' . $j, 'textnum', '0', array('style' => 'width:40px', 'onkeypress' => 'return tanpa_kutip(event)')) . '</td>';
		}
	}

	$content .= '<td><img id=\'addNorma_' . $j . '\' title=\'Tambah\' class=zImgBtn onclick="addNorma(\'' . $j . '\',\'' . $primaryStr . '\',\'' . $fieldStr . '\')" src=\'images/plus.png\'/>';
	$content .= '&nbsp;<img id=\'deleteNorma_' . $j . '\' /></td>';
	echo $content;
	break;
}

?>
