<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$str = 'select notransaksi from ' . $dbname . '.sdm_penggantiantransport ' . "\r\n" . '      where periode=\'' . $periode . '\' and kodeorg=\'' . $kodeorg . '\'' . "\r\n\t" . '  order by notransaksi desc limit 1';
$res = mysql_query($str);
$notran = '';

while ($bar = mysql_fetch_object($res)) {
	$notran = $bar->notransaksi;
}

if ($notran == '') {
	$notran = 0;
}
else {
	$notran = substr($notran, 10, 4);
}

$newNo = $notran + 1;

if ($newNo < 10) {
	$newNo = '000' . $newNo;
}
else if ($newNo < 100) {
	$newNo = '00' . $newNo;
}
else if ($newNo < 1000) {
	$newNo = '0' . $newNo;
}

echo $kodeorg . str_replace('-', '', $periode) . $newNo;

?>
