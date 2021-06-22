<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$karyawanid = $_POST['karid'];
$str = 'select * from ' . $dbname . '.datakaryawan where karyawanid=' . $karyawanid . ' limit 1';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	echo '<?xml version=\'1.0\' ?>' . "\r\n" . '     <karyawan>' . "\r\n" . '         <tipekaryawan>' . ($bar->tipekaryawan != '' ? $bar->tipekaryawan : '*') . '</tipekaryawan>' . "\r\n" . '         <kodejabatan>' . ($bar->kodejabatan != '' ? $bar->kodejabatan : '*') . '</kodejabatan>' . "\r\n" . '         <kodegolongan>' . ($bar->kodegolongan != '' ? $bar->kodegolongan : '*') . '</kodegolongan>' . "\r\n" . '         <lokasitugas>' . ($bar->lokasitugas != '' ? $bar->lokasitugas : '*') . '</lokasitugas>' . "\r\n" . '         <bagian>' . ($bar->bagian != '' ? $bar->bagian : '*') . '</bagian>    ' . "\r\n" . '     </karyawan>';
}

?>
