<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tableName = $_POST['tableName'];
$IDs = $_POST['IDs'];
$id = explode('##', $IDs);
$data = $_POST;
unset($data['tableName']);
unset($data['IDs']);
unset($data['tahuntanamCurr']);
unset($data['opt']);
$where = 'kodeorg=\'' . $_POST['kodeorg'] . '\' and tahuntanam=' . $_POST['tahuntanamCurr'];
$query = 'update `' . $dbname . '`.`' . $tableName . '` set ';
$i = 0;

foreach ($data as $key => $row) {
	$tmpStr = explode('-', $row);

	if (count($tmpStr) == 3) {
		$row = tanggalsystem($row);
	}

	$int = (int) $row;

	if ($i == 0) {
		if (((string) $int == $row) && (strlen((string) $int) == strlen($row))) {
			$query .= '`' . $tableName . '`.`' . $key . '`=' . $row;
		}
		else if (is_string($row)) {
			$query .= '`' . $tableName . '`.`' . $key . '`=\'' . $row . '\'';
		}
		else {
			$query .= '`' . $tableName . '`.`' . $key . '`=' . $row;
		}
	}
	else if (((string) $int == $row) && (strlen((string) $int) == strlen($row))) {
		$query .= ',' . '`' . $tableName . '`.`' . $key . '`=' . $row;
	}
	else if (is_string($row)) {
		$query .= ',' . '`' . $tableName . '`.`' . $key . '`=\'' . $row . '\'';
	}
	else {
		$query .= ',' . '`' . $tableName . '`.`' . $key . '`=' . $row;
	}

	++$i;
}

$query .= ' where ' . $where;

try {
	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
		exit();
	}

	echo 'var currRow = document.getElementById(\'currRow\').value;';

	foreach ($data as $key => $row) {
		if ($key == 'jumlahpokok') {
			$row = number_format($row, 0);
		}

		echo 'document.getElementById(\'' . $key . '_\'+currRow).innerHTML = \'' . $row . '\';';
		echo 'document.getElementById(\'' . $key . '_\'+currRow).setAttribute(\'value\',\'' . $row . '\');';
	}
}
catch (Exception $e) {
	echo $e->getMessage();
}

?>
