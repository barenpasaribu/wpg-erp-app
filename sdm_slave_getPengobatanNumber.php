<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$thnplaf = $_POST['tahun'];
$find = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$find = $find . $thnplaf;
$str = 'select notransaksi from ' . $dbname . '.sdm_pengobatanht where ' . "\r\n" . '      notransaksi like \'' . $find . '%\' order by notransaksi desc' . "\r\n\t" . '  limit 1';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no = substr($bar->notransaksi, 8, 4);
}

$no += 1;

if ($no < 10) {
	$no = '000' . $no;
}
else if ($no < 100) {
	$no = '00' . $no;
}
else if ($no < 1000) {
	$no = '0' . $no;
}

if ($thnplaf != '') {
	echo $find . $no;
}

?>
