<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zMysql.php';
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
unset($data['tableName']);
unset($data['numRow']);
unset($data['idField']);
unset($data['idVal']);

if ($data['kodeorg'] === $data['parent']) {
	echo 'alert(\'Error Constraint : Kode Organisasi dan Parent tidak boleh sama\');';
	exit();
}

$query = 'insert into `' . $dbname . '`.`' . $tableName . '`(';
$i = 0;

foreach ($data as $key => $row) {
	if ($i == 0) {
		$query .= '`' . $key . '`';
	}
	else {
		$query .= ',`' . $key . '`';
	}

	++$i;
}

$query .= ') values (';
$i = 0;

foreach ($data as $row) {
	if ($i == 0) {
		if (is_string($row)) {
			$query .= '\'' . $row . '\'';
		}
		else {
			$query .= $row;
		}
	}
	else if (is_string($row)) {
		$query .= ',\'' . $row . '\'';
	}
	else {
		$query .= ',' . $row;
	}

	++$i;
}

$query .= ');';

try {
	mysql_query($query);
	echo 'mTable = document.getElementById(\'mTabBody\');';
	echo 'mTable.innerHTML += ';
	echo '<tr id=\'tr_' . $numRow . '\' class=\'rowcontent\'>';
	$tmpField = '';
	$tmpVal = '';

	foreach ($data as $key => $row) {
		echo '<td id=\'' . $key . '_' . $numRow . '\'>' . $row . '</td>';
		$tmpField .= '##' . $key;
		$tmpVal .= '##' . $row;
	}

	echo '<td><img id=\'editRow' . $numRow . '\' title=\'Edit\' onclick="editRow(' . $numRow . ',\'' . $tmpField . '\',\'' . $tmpVal . '\')"' . "\r\n\t" . 'class=\'zImgBtn\' src=\'images/application/application_edit.png\' /></td>';
	echo '<td><img id=\'delRow' . $numRow . '\' title=\'Hapus\' onclick="delRow(' . $numRow . ',\'' . $idField . '\',\'' . $idVal . '\',null,\'' . $tableName . '\')"' . "\r\n\t" . 'class=\'zImgBtn\' src=\'images/application/application_delete.png\' /></td>';
	echo '</tr>;';
}
catch (Exception $e) {
	echo 'ERROR Query';
	echo $e->getMessage();
}

?>
