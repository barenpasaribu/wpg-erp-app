<?php


function writeFile($path)
{
	global $conn;
	global $dbname;
	$dir = $path;
	$ext = split('[.]', basename($_FILES['photo']['name']));
	$ext = $ext[count($ext) - 1];
	$ext = strtolower($ext);
	if (($ext == 'zip') || ($ext == 'rar') || ($ext == 'gz') || ($ext == 'tgz') || ($ext == '7z') || ($ext == 'tar') || ($ext == 'png') || ($ext == 'jpg') || ($ext == 'jpeg')) {
		$path = $dir . '/' . basename($_FILES['photo']['name']);

		try {
			if (move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {
				$str = 'insert into ' . $dbname . '.rencana_gis_file (kode, unit, keterangan, namafile, tipe, ukuran,  tanggal, karyawanid)' . "\r\n" . '                                        values(\'' . $_POST['kode'] . '\',\'' . $_POST['kodeorg'] . '\',\'' . $_POST['keterangan'] . '\',\'' . basename($_FILES['photo']['name']) . '\',\'' . $ext . '\',' . $_FILES['photo']['size'] . ',\'' . date('Y-m-d') . '\',' . $_SESSION['standard']['userid'] . ')';
				mysql_query($str);

				if (0 < mysql_affected_rows($conn)) {
					echo '<script>' . "\r\n" . '                                          parent.loadList();' . "\r\n" . '                                     </script>';
				}
				else {
					echo mysql_error($conn) . $str;

					echo '<script>alert("Error Writing File' . addslashes($e->getMessage()) . '");</script>';
				}
			}
		}
		catch (Exception $e) {
			echo '<script>alert("Error Writing File' . addslashes($e->getMessage()) . '");</script>';
		}
	}
	else {
		echo '<script>alert(\'Filetype not support:' . $ext . ' or too large\');</script>';
	}

}

require_once 'master_validation.php';
require_once 'config/connection.php';
$path = 'filegis';

if ($_FILES['photo']['size'] <= $_POST['MAX_FILE_SIZE']) {
	if (is_dir($path)) {
		writeFile($path);
	}
	else if (mkdir($path)) {
		writeFile($path);
	}
	else {
		echo '<script> alert(\'Gagal, Can`t create folder for uploaded file\');</script>';
		exit(0);
	}
}
else {
	echo '<script>File size is ' . filesize($_FILES['photo']['tmp_name']) . ', greater then allowed</script>';
}

?>
