<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$notransaksi = $_POST['notransaksi'];
	$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $notransaksi . '\' and ststussaldo=1';

	if (0 < mysql_num_rows(mysql_query($str))) {
		exit(' Error, transaksi sudah dalam proses posting');
	}

	$str = 'select post from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $notransaksi . '\'';
	$res = mysql_query($str);
	$ststus = 0;

	while ($bar = mysql_fetch_object($res)) {
		$status = $bar->post;
	}

	if ($status == 1) {
		echo ' Gagal/Error, Document has been posted';
	}
	else {
		$str = 'delete from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $notransaksi . '\'';

		if (mysql_query($str)) {
			$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $notransaksi . '\'';
			mysql_query($str);
		}
	}
}

?>
