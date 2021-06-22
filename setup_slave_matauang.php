<?php


include_once 'lib/zMysql.php';
$data = $_POST;
unset($data['proses']);
$data['kurs'] = 0;
$data['kurspajak'] = 0;

if ($_POST['proses'] == 'main_add') {
	if (($data['kode'] == '') || ($data['matauang'] == '') || ($data['simbol'] == '') || ($data['kodeiso'] == '')) {
		echo 'Error : Data tidak boleh ada yang kosong';
		exit();
	}

	$query = insertQuery($dbname, 'setup_matauang', $data);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
}
else if ($_POST['proses'] == 'main_edit') {
	if (($data['kode'] == '') || ($data['matauang'] == '') || ($data['simbol'] == '') || ($data['kodeiso'] == '')) {
		echo 'Error : Data tidak boleh ada yang kosong';
		exit();
	}

	unset($data['primField']);
	unset($data['primVal']);
	$prim = array('field' => $_POST['primField'], 'value' => $_POST['primVal']);
	$where = '`' . $prim['field'] . '`=\'' . $prim['value'] . '\'';
	$query = updateQuery($dbname, 'setup_matauang', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
}
else if ($_POST['proses'] == 'main_delete') {
	$query = 'delete from `' . $dbname . '`.`setup_matauang` where `kode`=\'' . $_POST['primVal'] . '\'';

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
}

?>
