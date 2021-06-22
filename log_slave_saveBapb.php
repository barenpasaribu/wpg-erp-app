<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 1;

if (isTransactionPeriod()) {
	$nodok = $_POST['nodok'];
	$idsupplier = $_POST['idsupplier'];
	$tanggal = tanggalsystem($_POST['tanggal']);
	$nopo = $_POST['nopo'];
	$nofaktur = $_POST['nofaktur'];
	$nosj = $_POST['nosj'];
	$qty = $_POST['qty'];
	$kodebarang = $_POST['kodebarang'];
	$kodegudang = $_POST['kodegudang'];
	$post = 0;
	$user = $_SESSION['standard']['userid'];
	$satuan = $_POST['satuan'];
	$status = 0;
	$user1 = $_SESSION['standard']['userid'];
	$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
	$res1 = mysql_query($str);

	if (mysql_num_rows($res1) == 1) {
		$str = 'select distinct nopo from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\' and nopo=\'' . $nopo . '\'';
		$res = mysql_query($str);

		if (mysql_num_rows($res) == 0) {
			$status = 8;
		}
		else {
			while ($bar1 = mysql_fetch_object($res1)) {
				$user1 = $bar1->user;
			}

			if ($_SESSION['standard']['userid'] == $user1) {
				$status = 1;
			}
			else {
				exit('Error: This transaction belongs to other user, please reload and start over');
			}
		}
	}

	$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nodok . '\'' . "\r\n" . '                and kodebarang=\'' . $kodebarang . '\'';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 2;
	}

	$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'' . "\r\n" . '                and post=1';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 3;
	}

	if (($status == 5) || ($status == 2) || ($status == 1)) {
		$str = 'select * from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nodok . '\' and ststussaldo=1';

		if (0 < mysql_num_rows(mysql_query($str))) {
			$status = 3;
			exit(' Error, transaksi sudah dalam proses posting');
		}
	}

	$kurs = 1;
	$kodept = '';
	$str = 'select kodeorg,kurs,matauang from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';
	$res = mysql_query($str);
	$matauang = '';

	while ($bar = mysql_fetch_object($res)) {
		$kodept = $bar->kodeorg;
		$kurs = $bar->kurs;
		$matauang = str_replace(' ', '', $bar->matauang);
	}

	$str = 'select hargasatuan,jumlahpesan,satuan,matauang,kodebarang from ' . $dbname . '.log_podt where ' . "\r\n\t" . '      nopo=\'' . $nopo . '\' and kodebarang=\'' . $kodebarang . '\'';
	$res = mysql_query($str);
	$jumlahpesan = '';
	$hargasatuan = 0;

	while ($bar = mysql_fetch_object($res)) {
		$jumlahpesan = $bar->jumlahpesan;
		$hargasatuan = $bar->hargasatuan;

		if ($satuan != $bar->satuan) {
			$jlhkonversi = 1;
			$str1 = 'select jumlah from ' . $dbname . '.log_5stkonversi ' . "\r\n\t\t\t" . '       where darisatuan=\'' . $satuan . '\' and satuankonversi=\'' . $bar->satuan . '\'' . "\r\n" . '                                                        and kodebarang=\'' . $bar->kodebarang . '\'';
			$res3 = mysql_query($str1);

			if (0 < mysql_num_rows($res3)) {
				while ($bar2 = mysql_fetch_object($res3)) {
					$jlhkonversi = $bar2->jumlah;
				}
			}

			if ($jlhkonversi != 0) {
				$hargasatuan = $bar->hargasatuan * $jlhkonversi;
			}
		}
	}

	if (($kurs == 0) || ($matauang == 'IDR') || ($matauang == '')) {
		$kurs = 1;
	}

	$hargasatuan = $hargasatuan * $kurs;
	$jumlahlalu = 0;
	$str = 'select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi ' . "\r\n\t" . '    from ' . $dbname . '.log_transaksidt a,' . "\r\n\t" . '         ' . $dbname . '.log_transaksiht b' . "\r\n\t\t" . '   where a.notransaksi=b.notransaksi and  ' . "\r\n\t\t" . '   b.nopo=\'' . $nopo . '\' ' . "\r\n\t" . '       and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t" . '   and a.notransaksi<\'' . $nodok . '\'' . "\r\n\t\t" . '   order by notransaksi desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jumlahlalu = $bar->jumlah;
	}

	if (($status == 0) || ($status == 1) || ($status == 2)) {
		$stro = 'select a.post from ' . $dbname . '.log_transaksiht a' . "\r\n\t" . '       left join ' . $dbname . '.log_transaksidt b' . "\r\n\t\t" . '   on a.notransaksi=b.notransaksi' . "\r\n\t" . '       where a.tanggal>' . $tanggal . ' and a.kodept=\'' . $kodept . '\'' . "\r\n\t\t" . '   and b.kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\'' . "\r\n\t\t" . '   and a.post=1';
		$reso = mysql_query($stro);

		if (0 < mysql_num_rows($reso)) {
			$status = 7;
			echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
			exit(0);
		}
	}

	if ($hargasatuan == 0) {
		exit('Error: belum ada harga pada PO:' . $nopo);
	}

	if ($status == 0) {
		$str = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '                            `tipetransaksi`,`notransaksi`,`tanggal`,' . "\r\n" . '                            `kodept`,`nopo`,`nosj`,`kodegudang`,`user`,' . "\r\n" . '                            `idsupplier`,`nofaktur`,`post`)' . "\r\n" . '                    values(' . $tipetransaksi . ',\'' . $nodok . '\',' . $tanggal . ',' . "\r\n" . '                            \'' . $kodept . '\',\'' . $nopo . '\',\'' . $nosj . '\',\'' . $kodegudang . '\',' . $user . ',' . "\r\n" . '                                \'' . $idsupplier . '\',\'' . $nofaktur . '\',' . $post . "\r\n" . '                    )';

		if (mysql_query($str)) {
			$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                                `notransaksi`,`kodebarang`,' . "\r\n" . '                                `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n" . '                                `hargasatuan`,`kodeblok`)' . "\r\n" . '                                values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                                \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n" . '                                ' . $hargasatuan . ',\'\')';

			if (mysql_query($str)) {
				$str = 'update ' . $dbname . '.log_poht set statuspo=3 where nopo=\'' . $nopo . '\'';
				mysql_query($str);
			}
			else {
				echo ' Gagal, (insert detail on status 0)' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo ' Gagal,  (insert header on status 0)' . addslashes(mysql_error($conn));
		}
	}
	else if ($status == 1) {
		$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                        `notransaksi`,`kodebarang`,' . "\r\n" . '                        `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n" . '                        `hargasatuan`,`kodeblok`)' . "\r\n" . '                        values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                        \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n" . '                        ' . $hargasatuan . ',\'\')';

		if (mysql_query($str)) {
			$str = 'update ' . $dbname . '.log_poht set statuspo=3 where nopo=\'' . $nopo . '\'';
			mysql_query($str);
		}
		else {
			echo ' Gagal, (insert detail on status 1)' . addslashes(mysql_error($conn));
		}
	}
	else if ($status == 2) {
		$str = 'update ' . $dbname . '.log_transaksidt set' . "\r\n" . '                            `jumlah`=' . $qty . ',' . "\r\n" . '                                `updateby`=' . $user . "\r\n" . '                                where `notransaksi`=\'' . $nodok . '\'' . "\r\n" . '                                and `kodebarang`=\'' . $kodebarang . '\'';
		mysql_query($str);

		if (mysql_affected_rows($conn) < 1) {
			echo ' Gagal, (update detail on status 2)' . addslashes(mysql_error($conn));
		}
		else {
			$notrxnext = '';
			$strc = 'select a.notransaksi as notrx from ' . $dbname . '.log_transaksidt a, ' . $dbname . '.log_transaksiht b' . "\r\n" . '                                    where a.notransaksi= b.notransaksi ' . "\r\n" . '                                        and b.nopo=\'' . $nopo . '\'' . "\r\n" . '                                        and a.notransaksi>\'' . $nodok . '\'' . "\r\n" . '                                        and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                                        order by notrx asc limit 1';
			$resc = mysql_query($strc);

			while ($barc = mysql_fetch_object($resc)) {
				$notrxnext = $barc->notrx;
			}

			if ($notrxnext != '') {
				$str = 'update ' . $dbname . '.log_transaksidt set' . "\r\n" . '                                        `jumlahlalu`=' . $qty . ',' . "\r\n" . '                                            `updateby`=' . $user . "\r\n" . '                                            where `notransaksi`=\'' . $notrxnext . '\'' . "\r\n" . '                                            and `kodebarang`=\'' . $kodebarang . '\'';
				mysql_query($str);

				if (mysql_affected_rows($conn) < 1) {
				}
			}
		}
	}

	if ($status == 3) {
		echo ' Gagal: Data has been posted';
	}

	if ($status == 8) {
		echo ' Gagal: Material not registred on PO : ' . $nodok;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
