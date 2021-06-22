<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
$tableName = $_POST['tableName'];
$data = $_POST;
unset($data['tableName']);
$where = '';
$i = 0;

foreach ($data as $key => $row) {
	if ($i == 0) {
		if (is_string($row)) {
			$where .= $key . ' = \'' . $row . '\'';
		}
		else {
			$where .= $key . ' = ' . $row;
		}
	}
	else if (is_string($row)) {
		$where .= ' and ' . $key . ' = \'' . $row . '\'';
	}
	else {
		$where .= ' and ' . $key . ' = ' . $row;
	}

	++$i;
}

$query = 'delete from ' . $dbname . '.' . $tableName . ' where ' . $where;
echo $query;
if (!mysql_query($query)) {
	echo 'DB Error : ' . mysql_error($conn);
	exit();
}

?>
