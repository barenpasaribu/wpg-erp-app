<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';

if (isTransactionPeriod()) {
	$gudang = $_POST['gudang'];
	//$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi = \'' . substr($gudang, 0, 4) . '\'';
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi = \'' . $gudang . '\'';
	$res = mysql_query($str);
	
	while ($bar = mysql_fetch_object($res)) {
		$ptgudang = $bar->induk;
	}

	$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi = \'' . $ptgudang . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$namaptgudang = $bar->namaorganisasi;
	}

	$blehh = '<option value=\'' . $ptgudang . '\'>' . $namaptgudang . '</option>';
	echo $blehh;
}
else {
	echo ' Error: Transaction Period missing';
}

?>
