<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$fromId = $_POST['from'];
$toId = $_POST['to'];
$orderFrom = $_POST['orderfrom'];
$orderTo = $_POST['orderto'];
$temp = 329027;
$str = 'update ' . $dbname . '.menu set urut=' . $temp . ' where id=' . $toId;

if (mysql_query($str)) {
	$str1 = 'update ' . $dbname . '.menu set urut=' . $orderTo . ', lastuser=\'' . $_SESSION['standard']['username'] . '\' where id=' . $fromId;

	if (mysql_query($str1)) {
		$str2 = 'update ' . $dbname . '.menu set urut=' . $orderFrom . ', lastuser=\'' . $_SESSION['standard']['username'] . '\' where id=' . $toId;

		if (!mysql_query($str2)) {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
