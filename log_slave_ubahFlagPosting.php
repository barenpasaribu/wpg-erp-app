<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$notransaksi = $_POST['notransaksi'];
$status = $_POST['status'];
$gudang = $_POST['gudang'];
$user = $_SESSION['standard']['userid'];
$str = 'select kodebarang from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $notransaksi . '\'  and statussaldo=0';
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	echo 'Error : There is still unsucceed transaction, please re-process';
}
else {
	$str = 'update ' . $dbname . '.log_transaksiht set post=' . $status . ', postedby=' . $user . ',statusjurnal=1' . "\r\n\t\t\t\t" . ' where notransaksi=\'' . $notransaksi . '\'  and kodegudang=\'' . $gudang . '\'';

	if (mysql_query($str)) {
		if (mysql_affected_rows($conn) < 1) {
			echo 'Error : post status update nothing';
		}
		else {
			$i = 'select * from ' . $dbname . '.log_transaksiht where tipetransaksi=\'7\' and gudangx like \'%HO%\' and notransaksi=\'' . $notransaksi . '\' ';

			#exit(mysql_error($conn));
			($n = mysql_query($i)) || true;

			if (0 < mysql_num_rows($n)) {
				$x = 'select nilai from ' . $dbname . '.setup_parameterappl where kodeparameter=\'EMAILPR\'';
				$y = mysql_query($x);

				while ($z = mysql_fetch_assoc($y)) {
					$to = $z['nilai'];
					$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
					$subject = '[Notifikasi] Mutasi Barang ke HO : ' . $notransaksi . ' ';
					$body = '<html>' . "\r\n\t\t\t\t\t\t\t\t" . ' <head>' . "\r\n\t\t\t\t\t\t\t\t" . ' <body>' . "\r\n\t\t\t\t\t\t\t\t" . '   <dd>Dengan Hormat, Bapak./Ibu. ' . $nmpnlk . '</dd><br>' . "\r\n\t\t\t\t\t\t\t\t" . '   Pada hari ini, tanggal ' . date('d-m-Y') . ' karyawan a/n  ' . $namakaryawan . ', melakukan pengiriman barang ke HO dengan nomor document ' . $notransaksi . ' ' . "\r\n\t\t\t\t\t\t\t\t" . '   <br>' . "\r\n\t\t\t\t\t\t\t\t" . '   Regards,<br>' . "\r\n\t\t\t\t\t\t\t\t" . '   eAgro Plantation Management Software.' . "\r\n\t\t\t\t\t\t\t\t" . ' </body>' . "\r\n\t\t\t\t\t\t\t\t" . ' </head>' . "\r\n\t\t\t\t\t\t\t" . '   </html>' . "\r\n\t\t\t\t\t\t\t" . '   ';
					$x = kirimEmailWindows($to, $subject, $body);
				}
			}
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}
}

?>
