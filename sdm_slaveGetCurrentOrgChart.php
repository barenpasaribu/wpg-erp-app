<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$code = $_POST['code'];
$sta = 'select * from ' . $dbname . '.sdm_strukturjabatan where kodestruktur=\'' . $code . '\'';
$re = mysql_query($sta);

if (0 < mysql_num_rows($re)) {
	while ($be = mysql_fetch_object($re)) {
		echo $be->kodestruktur . '|' . $be->karyawanid . '|' . $be->kodejabatan . '|' . $be->email . '|' . $be->detail . '|' . $be->kodept;
	}
}
else {
	echo '-1';
}

?>
