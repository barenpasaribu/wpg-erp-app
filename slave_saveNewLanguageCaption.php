<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$legend = $_POST['legend'];
$location = $_POST['location'];
$arg = explode('##', $_POST['arg']);
$cont = explode('##', $_POST['cont']);
$str = 'insert into ' . $dbname . '.bahasa(legend,location,';
$x = 0;

while ($x < count($arg)) {
	if ($x == 0) {
		$str .= $arg[$x];
	}
	else {
		$str .= ',' . $arg[$x];
	}

	++$x;
}

$str .= ') values(\'' . $legend . '\',\'' . $location . '\',';
$x = 0;

while ($x < count($cont)) {
	if ($x == 0) {
		$str .= '\'' . $cont[$x] . '\'';
	}
	else {
		$str .= ',\'' . $cont[$x] . '\'';
	}

	++$x;
}

$str .= ')';

if (mysql_query($str)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
