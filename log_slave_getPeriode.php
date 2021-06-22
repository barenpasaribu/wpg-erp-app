<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$gudang = $_POST['gudang'];
$str = 'select kodeorg, periode from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '      where kodeorg=\'' . $gudang . '\'';
if ($gudang == 'gudang2') {
	$str = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi  order by periode desc';
}
if ($gudang == 'sumatera') {
	$str = 'select distinct kodeorg, periode from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '              where kodeorg in(\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') group by periode';
}

if ($gudang == 'kalimantan') {
	$str = 'select distinct kodeorg, periode from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '              where kodeorg in(\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') group by periode';
}

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$hasil .= '<option value=\'' . $bar->periode . '\'>' . $bar->periode . '</option>';
}

echo $hasil;

?>
