<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$ngapain = $_POST['ngapain'];
$noakun = $_POST['noakun'];
$table = '';

if ($ngapain == 'ambilkegiatan') {
	$str = 'select max(kodekegiatan) as kodekegiatan from ' . $dbname . '.`setup_kegiatan` where kodekegiatan like \'' . $noakun . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$max = $bar->kodekegiatan;
	}

	$ambildua = substr($max, -2);
	$ambildua = intval($ambildua) + 1;

	if (strlen($ambildua) == 1) {
		$kegiatan = $noakun . '0' . $ambildua;
	}
	else {
		$kegiatan = $noakun . $ambildua;
	}

	$table .= $kegiatan;
}

echo $table;

?>
