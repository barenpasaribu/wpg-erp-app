<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$karyawanid = $_POST['karyawanid'];
$val = $_POST['val'];
$str = 'select * from ' . $dbname . '.sdm_5jatahbbm where karyawanid=' . $karyawanid;
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	$str = 'update ' . $dbname . '.sdm_5jatahbbm set jatah=' . $val . ' where karyawanid=' . $karyawanid;
}
else {
	$str = 'insert into ' . $dbname . '.sdm_5jatahbbm(karyawanid,jatah)' . "\r\n\t" . '      values(' . $karyawanid . ',' . $val . ')';
}

mysql_query($str);

?>
