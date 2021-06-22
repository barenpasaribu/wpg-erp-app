<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 2;

if (isTransactionPeriod()) {
	$nodok = $_POST['nodok'];
	$nomorlama = $_POST['nomorlama'];
	$idsupplier = $_POST['untukpt'];
	$tanggal = tanggalsystem($_POST['tanggal']);
	$nofaktur = '';
	$nosj = '';
	$qty = $_POST['jlhretur'];
	$kodebarang = $_POST['kodebarang'];
	$kodegudang = $_POST['gudang'];
	$kodept = $_POST['kodept'];
	$untukunit = $_POST['untukunit'];
	$hargasatuan = $_POST['hargasatuan'];
	$kodeblok = $_POST['kodeblok'];
	$post = 0;
	$keterangan = $_POST['keterangan'];
	$user = $_SESSION['standard']['userid'];
	$satuan = $_POST['satuan'];
	$jumlahlalu = 0;
	$stro = 'select * from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '            where kodeorg=\'' . $kodegudang . '\' and periode=\'' . substr($tanggal, 0, 7) . '\'' . "\r\n" . '            and tutupbuku=1';
	$reso = mysql_query($stro);

	if (0 < mysql_num_rows($reso)) {
		$status = 7;
		echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
		exit(0);
	}

	$str = 'select kodekegiatan,kodemesin from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nomorlama . '\' ' . "\r\n" . '        and kodebarang=\'' . $kodebarang . '\' and kodeblok=\'' . $kodeblok . '\'';
	$res = mysql_query($str);
	$kodekegiatan = '';
	$kodemesin = '';

	while ($bar = mysql_fetch_object($res)) {
		$kodekegiatan = $bar->kodekegiatan;
		$kodemesin = $bar->kodemesin;
	}

	$str = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '                `tipetransaksi`,`notransaksi`,`tanggal`,' . "\r\n" . '                `kodept`,`nopo`,`nosj`,`kodegudang`,`user`,' . "\r\n" . '                `idsupplier`,`nofaktur`,`post`,`untukunit`,' . "\r\n" . '                `keterangan`,`notransaksireferensi`)' . "\r\n" . '        values(' . $tipetransaksi . ',\'' . $nodok . '\',' . $tanggal . ',' . "\r\n" . '                \'' . $kodept . '\',\'\',\'' . $nosj . '\',\'' . $kodegudang . '\',' . $user . ',' . "\r\n" . '                    \'' . $idsupplier . '\',\'' . $nofaktur . '\',' . $post . ',\'' . $untukunit . '\',' . "\r\n" . '                    \'' . $keterangan . '\',\'' . $nomorlama . '\'' . "\r\n" . '        )';

	if (mysql_query($str)) {
		$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                    `notransaksi`,`kodebarang`,' . "\r\n" . '                    `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n" . '                    `hargasatuan`,`updateby`,`kodeblok`,' . "\r\n" . '                    `hargarata`,kodemesin,kodekegiatan)' . "\r\n" . '                    values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                    \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n" . '                    0,' . $user . ',' . "\r\n" . '                    \'' . $kodeblok . '\',0,\'' . $kodemesin . '\',\'' . $kodekegiatan . '\')';

		if (mysql_query($str)) {
		}
		else {
			echo ' Gagal, (insert detail on status 0)' . addslashes(mysql_error($conn));
			$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
			mysql_query($str);
		}
	}
	else {
		echo ' Gagal,  (insert header on status 0)' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
