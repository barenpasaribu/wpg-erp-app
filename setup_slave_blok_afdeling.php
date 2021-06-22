<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$opt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'tipe in (\'AFDELING\',\'BIBITAN\') and induk=\'' . $_POST['kebun'] . '\'');
echo 'var afdeling = document.getElementById(\'' . $_POST['afdelingId'] . '\');';

foreach ($opt as $key => $row) {
	echo 'afdeling.options[afdeling.options.length] = new Option(\'' . $row . '\',\'' . $key . '\');';
}

?>
