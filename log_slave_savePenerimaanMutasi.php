<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 3;

if (isTransactionPeriod()) {
	$nodok = $_POST['nodok'];
	$kodebarang = $_POST['kodebarang'];
	$tanggal = tanggalsystem($_POST['tanggal']);
	$gudangx = $_POST['gudangx'];
	$satuan = $_POST['satuan'];
	$jumlah = $_POST['jumlah'];
	$kodegudang = $_POST['kodegudang'];
	$referensi = $_POST['referensi'];
	$pemilikbarang = $_POST['pemilikbarang'];
	$post = 0;
	$user = $_SESSION['standard']['userid'];
	$status = 0;
	$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
	$res = mysql_query($str);

	if (mysql_num_rows($res) == 1) {
		$status = 1;
	}

	$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nodok . '\'' . "\r\n\t" . '       and kodebarang=\'' . $kodebarang . '\'';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 2;
	}

	$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'' . "\r\n\t" . '       and post=1';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 3;
	}

	$strx = 'select a.hargarata from ' . $dbname . '.log_transaksidt a' . "\r\n" . '            left join ' . $dbname . '.log_transaksiht b ' . "\r\n" . '            on a.notransaksi=b.notransaksi' . "\r\n" . '            where a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '            and a.notransaksi=\'' . $referensi . '\'' . "\r\n" . '            and  b.tipetransaksi=7' . "\r\n" . '            order by a.notransaksi desc limit 1';
	$hargasatuan = 0;
	$resx = mysql_query($strx);

	while ($barx = mysql_fetch_object($resx)) {
		$hargasatuan = $barx->hargarata;
	}

	if (($hargasatuan == 0) || ($hargasatuan == '')) {
		echo ' Error: Price is 0 on :' . $referensi;
		exit(0);
	}

	$jumlahlalu = 0;
	$str = 'select a.jumlah as jumlah,a.notransaksi as notransaksi ' . "\r\n" . '                from ' . $dbname . '.log_transaksidt a,' . "\r\n" . '                ' . $dbname . '.log_transaksiht b' . "\r\n" . '                where a.notransaksi=b.notransaksi' . "\r\n" . '                and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                and a.notransaksi<=\'' . $nodok . '\'' . "\r\n" . '                and b.kodegudang=\'' . $kodegudang . '\'' . "\r\n" . '                order by notransaksi desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jumlahlalu = $bar->jumlah;
	}

	if ($status == 0) {
		$sKdPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($kodegudang, 0, 4) . '\'';

		#exit(mysql_error($sKdPt));
		($qKdPt = mysql_query($sKdPt)) || true;
		$rKdpt = mysql_fetch_assoc($qKdPt);

		if ($rKdpt['induk'] == '') {
			exit('Kode PT Penerima Kosong');
		}

		$str = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '                        `tipetransaksi`,`notransaksi`,`tanggal`,' . "\r\n" . '                        `kodept`,`kodegudang`,`user`,' . "\r\n" . '                        `gudangx`,`notransaksireferensi`,`post`)' . "\r\n" . '                values(' . $tipetransaksi . ',\'' . $nodok . '\',' . $tanggal . ',' . "\r\n" . '                        \'' . $rKdpt['induk'] . '\',\'' . $kodegudang . '\',' . $user . ',' . "\r\n" . '                            \'' . $gudangx . '\',\'' . $referensi . '\',' . $post . "\r\n" . '                )';

		if (mysql_query($str)) {
			$str = 'update ' . $dbname . '.log_transaksiht ' . "\r\n" . '                                set notransaksireferensi=\'' . $nodok . '\'' . "\r\n" . '                                    where notransaksi=\'' . $referensi . '\'' . "\r\n" . '                                    and kodegudang=\'' . $gudangx . '\'';

			if (mysql_query($str)) {
			}
			else {
				echo ' Gagal, (update reference on status 0)' . addslashes(mysql_error($conn));
				$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
				mysql_query($str);
			}

			$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                            `notransaksi`,`kodebarang`,' . "\r\n" . '                            `satuan`,`jumlah`,`jumlahlalu`,hargasatuan)' . "\r\n" . '                            values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                            \'' . $satuan . '\',' . $jumlah . ',' . $jumlahlalu . ',' . $hargasatuan . ')';

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
			$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
			mysql_query($str);
		}
	}

	if ($status == 1) {
		$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                        `notransaksi`,`kodebarang`,' . "\r\n" . '                        `satuan`,`jumlah`,`jumlahlalu`,hargasatuan)' . "\r\n" . '                        values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                        \'' . $satuan . '\',' . $jumlah . ',' . $jumlahlalu . ',' . $hargasatuan . ')';

		if (mysql_query($str)) {
		}
		else {
			echo ' Gagal, (insert detail on status 1)' . addslashes(mysql_error($conn));
			$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
			mysql_query($str);
		}
	}

	if ($status == 3) {
		echo ' Gagal: Data has been posted';
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
