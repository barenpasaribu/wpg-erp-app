<?php


require_once 'master_validation.php';
require_once 'config/connection.php';

if (isset($_POST['kelompok'])) {
	$kdkelompok = trim($_POST['kelompok']);

	if ($kdkelompok == '') {
		echo '';
	}
	else {
		$str = 'select max(supplierid) as id from ' . $dbname . '.log_5supplier where kodekelompok=\'' . $kdkelompok . '\'';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$newkode = $bar->id;
		}

		$newkode = substr($newkode, 6, 4);
		$mid = date('y');
		$newkode = intval($newkode) + 1;

		switch (intval($newkode)) {
		case $newkode < 10:
			$newkode = '0' . $newkode;
			break;

		case $newkode > 9:
			$newkode = '' . $newkode;
			break;

		 // case $newkode > 100:
		 // 	$newkode = '0' . $newkode;
		 // 	break;
		 }

		$newkode = $kdkelompok . $mid . $newkode;
		echo $newkode;
	}
}
else {
	$tipe = $_POST['tipe'];
	$str1 = 'select max(kode) as kode from ' . $dbname . '.log_5klsupplier where tipe=\'' . $tipe . '\'';

	if ($res1 = mysql_query($str1)) {
		while ($bar1 = mysql_fetch_object($res1)) {
			$kode = $bar1->kode;
		}

		$kode = substr($kode, 1, 5);
		$newkode = $kode + 1;

		switch ($newkode) {
		case $newkode < 10:
			$newkode = '00' . $newkode;
			break;

		case $newkode > 10:
			$newkode = '0' . $newkode;
			break;
		}

		if ($tipe == 'SUPPLIER') {
			$newkode = 'S' . $newkode;
		}
		else {
			$newkode = 'K' . $newkode;
		}

		echo $newkode;
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
