<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$klp = $_POST['klp'];
$where = 'kelompok=\'' . $klp . '\'';
$options = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $where);

foreach ($options as $key => $row) {
	echo 'keg.options[keg.options.length] = new Option(\'' . $row . '\',\'' . $key . '\');';
}

?>
