<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$pt = $_POST['tjbaru'];
$lokasibaru = substr($_POST['lokasibaru'], 0, 4);
$lokasilama = $_POST['lokasilama'];
$optTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
$a = 'select * from ' . $dbname . '.setup_temp_lokasitugas where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

#exit(mysql_error($conn));
($b = mysql_query($a)) || true;
$c = mysql_fetch_assoc($b);
$lokasi = $c['kodeorg'];
$jum = count($lokasi);

if (0 < $jum) {
	if ($lokasi == $lokasibaru) {
		$a = 'delete from ' . $dbname . '.setup_temp_lokasitugas where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

		if (mysql_query($a)) {
			$spndh = 'update `' . $dbname . '`.`datakaryawan` set lokasitugas=\'' . $lokasi . '\' where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

			if (!mysql_query($spndh)) {
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else if (($optTipe[$lokasi] != 'KANWIL') && ($optTipe[$lokasi] != 'HOLDING')) {
		$a = 'delete from ' . $dbname . '.setup_temp_lokasitugas where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

		if (mysql_query($a)) {
			$spndh = 'update `' . $dbname . '`.`datakaryawan` set lokasitugas=\'' . $lokasi . '\' where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

			if (!mysql_query($spndh)) {
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		$str = 'update ' . $dbname . '.datakaryawan set kodeorganisasi=\'' . $pt . '\',' . "\r\n\t\t\t" . '  lokasitugas=\'' . $lokasibaru . '\'' . "\r\n\t\t\t" . '   where karyawanid=' . $_SESSION['standard']['userid'];

		if (!mysql_query($str)) {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
}
else {
	$a = 'INSERT INTO `' . $dbname . '`.`setup_temp_lokasitugas` (`karyawanid`,`kodeorg`) values (\'' . $_SESSION['standard']['userid'] . '\',\'' . $lokasilama . '\')';

	if (mysql_query($a)) {
		$str = 'update ' . $dbname . '.datakaryawan set kodeorganisasi=\'' . $pt . '\',' . "\r\n\t" . '      lokasitugas=\'' . $lokasibaru . '\'' . "\r\n\t" . '       where karyawanid=' . $_SESSION['standard']['userid'];

		if (mysql_query($str)) {
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
	$str = 'update ' . $dbname . '.datakaryawan set kodeorganisasi=\'' . $pt . '\',' . "\r\n\t" . '      lokasitugas=\'' . $lokasibaru . '\'' . "\r\n\t" . '       where karyawanid=' . $_SESSION['standard']['userid'];

	if (!mysql_query($str)) {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

echo 'Update';

?>
