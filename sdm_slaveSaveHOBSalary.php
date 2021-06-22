<?php


require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$userid = $_POST['userid'];
$component = $_POST['component'];
$value = $_POST['value'];
$stra = 'select * from ' . $dbname . '.sdm_ho_basicsalary where ' . "\r\n" . '         karyawanid=' . $userid . ' and component=' . $component;
$res = mysql_query($stra);

if (0 < mysql_num_rows($res)) {
	$str = 'update ' . $dbname . '.sdm_ho_basicsalary' . "\r\n\t\t" . '       set value=' . $value . ',updateby=\'' . $_SESSION['standard']['username'] . '\'' . "\r\n\t\t\t" . '   where karyawanid=' . $userid . "\r\n\t\t\t" . '   and component=' . $component;
}
else {
	$str = 'insert into ' . $dbname . '.sdm_ho_basicsalary (karyawanid,component,value,updateby)' . "\r\n\t\t" . '       values(' . $userid . ',' . $component . ',' . $value . ',\'' . $_SESSION['standard']['username'] . '\')';
}

if (mysql_query($str)) {
}
else {
	echo ' Error: ' . addslashes(mysql_error($conn));
}

?>
