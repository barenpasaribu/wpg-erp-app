<?php


require_once 'master_validation.php';
require_once 'config/connection.php';

if (isset($_POST['kelompok'])) {
	$kdkelompok = trim($_POST['kelompok']);

	if ($kdkelompok == '') {
		echo '';
	}
	else {
		$str = 'select max(kodecustomer) as id from ' . $dbname . '.pmn_4customer where klcustomer=\'' . $kdkelompok . '\'';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$newkode = $bar->id;
		}

		$newkode = substr($newkode, 6, 2);
		$mid = date('y');
		$newkode = intval($newkode) + 1;

		switch (intval($newkode)) {
		case $newkode < 10:
			$newkode = '0' . $newkode;
			break;

		case $_POST:
			$newkode = '' . $newkode;
			break;

		case $_POST:
			$newkode = '' . $newkode;
			break;
		}

		$newkode = $kdkelompok . $newkode;
		echo $newkode;
	}
}


?>