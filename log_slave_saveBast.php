<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 5;

if (isTransactionPeriod()) {
	$nodok = $_POST['nodok'];
	$tanggal = tanggalsystem($_POST['tanggal']);
	$kodebarang = $_POST['kodebarang'];
	$penerima = $_POST['penerima'];
	$satuan = $_POST['satuan'];
	$qty = $_POST['qty'];
	$blok = $_POST['blok'];
	$mesin = $_POST['mesin'];
	$untukunit = $_POST['untukunit'];
	$subunit = $_POST['subunit'];
	$gudang = $_POST['gudang'];
	$catatan = $_POST['catatan'];
	$kegiatan = $_POST['kegiatan'];
	$method = $_POST['method'];
	$pemilikbarang = $_POST['pemilikbarang'];
	$user = $_SESSION['standard']['userid'];
	$post = 0;

	if ($blok == '') {
		$blok = $subunit;
	}

	if ($blok == '') {
		$blok = $untukunit;
	}

	$status = 0;
	$user1 = $_SESSION['standard']['userid'];

	if ($_POST['statInputan'] == '0') {
		$antri = 0;

		while ($antri == 0) {
			$str = 'select user from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';

			#exit(mysql_error($conn));
			($res = mysql_query($str)) || true;

			if (mysql_num_rows($res) == 1) {
				$antri = 1;
				$num = 1;
				$str = 'select max(notransaksi) notransaksi from ' . $dbname . '.log_transaksiht where tipetransaksi>4 and tanggal>=' . $_SESSION['gudang'][$gudang]['start'] . ' and tanggal<=' . $_SESSION['gudang'][$gudang]['end'] . "\r\n" . '                                and kodegudang=\'' . $gudang . '\' order by notransaksi desc limit 1';

				if ($_SESSION['empl']['tipelokasitugas'] == 'KEBUN') {
					$str = '';
					$str = 'select max(notransaksi) notransaksi from ' . $dbname . '.log_transaksiht where tipetransaksi>4 and tanggal>=' . $_SESSION['gudang'][$gudang]['start'] . ' and tanggal<=' . $_SESSION['gudang'][$gudang]['end'] . "\r\n" . '                                    and kodegudang=\'' . $gudang . '\' and substr( `notransaksi` , 7, 1 ) not like \'%M%\' order by notransaksi desc limit 1';
				}

				if ($res = mysql_query($str)) {
					while ($bar = mysql_fetch_object($res)) {
						$num = $bar->notransaksi;

						if ($num != '') {
							$num = intval(substr($num, 6, 5)) + 1;
						}
						else {
							$num = 1;
						}
					}

					if ($num < 10) {
						$num = '0000' . $num;
					}
					else if ($num < 100) {
						$num = '000' . $num;
					}
					else if ($num < 1000) {
						$num = '00' . $num;
					}
					else if ($num < 10000) {
						$num = '0' . $num;
					}
					else {
						$num = $num;
					}

					$nodok = $_SESSION['gudang'][$gudang]['tahun'] . $_SESSION['gudang'][$gudang]['bulan'] . $num . '-GI-' . $gudang;
				}
			}
			else {
				$antri = 1;
			}
		}
	}
	else {
		$status = 1;
	}

	if ($method == 'update') {
		$status = 2;
	}

	if (isset($_POST['delete'])) {
		$status = 5;
	}

	$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'' . "\r\n\t" . '       and post=1';

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

	$ptpemintabarang = '';
	$stre = ' select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $untukunit . '\'';
	$rese = mysql_query($stre);

	if ($bare = mysql_fetch_object($rese)) {
		$strf = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bare->induk . '\'';
		$resf = mysql_query($strf);

		if ($barf->tipe == 'PT') {
			$ptpemintabarang = $bare->induk;
		}
	}

	if ($ptpemintabarang == '') {
		$strf = 'select alokasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $untukunit . '\' and alokasi<>\'\'';
		$resf = mysql_query($strf);

		while ($barf = mysql_fetch_object($resf)) {
			$ptpemintabarang = $barf->alokasi;
		}

		if ($ptpemintabarang == '') {
			$status = 4;
		}
	}

	if (isset($_POST['displayonly'])) {
		$status = 6;
	}

	$jumlahlalu = 0;
	$str = 'select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi ' . "\r\n\t" . '    from ' . $dbname . '.log_transaksidt a,' . "\r\n\t" . '         ' . $dbname . '.log_transaksiht b' . "\r\n\t\t" . '   where a.notransaksi=b.notransaksi ' . "\r\n\t" . '       and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n\t\t" . '   and a.notransaksi<=\'' . $nodok . '\'' . "\r\n\t\t" . '   and b.tipetransaksi>4 ' . "\r\n\t\t" . '   and b.kodegudang=\'' . $gudang . '\'' . "\r\n\t\t" . '   order by notransaksi desc, waktutransaksi desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jumlahlalu = $bar->jumlah;
	}

	$qtynotpostedin = 0;
	$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '               b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n\t\t\t" . '   and a.tipetransaksi<5' . "\r\n\t\t\t" . '   and a.kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t" . '   and a.post=0' . "\t\t\t" . '   ' . "\r\n\t\t\t" . '   group by kodebarang';
	$res2 = mysql_query($str2);

	while ($bar2 = mysql_fetch_object($res2)) {
		$qtynotpostedin = $bar2->jumlah;
	}

	if ($qtynotpostedin == '') {
		$qtynotpostedin = 0;
	}

	$qtynotposted = 0;
	$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '           b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n\t\t" . '   and a.tipetransaksi>4' . "\r\n\t\t" . '   and a.kodegudang=\'' . $gudang . '\'' . "\r\n\t\t" . '   and a.post=0' . "\t\t" . '   ' . "\r\n\t\t" . '   group by kodebarang';
	$res2 = mysql_query($str2);

	while ($bar2 = mysql_fetch_object($res2)) {
		$qtynotposted = $bar2->jumlah;
	}

	if ($qtynotposted == '') {
		$qtynotposted = 0;
	}

	$saldoqty = 0;
	$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '          and kodeorg=\'' . $pemilikbarang . '\'' . "\r\n\t\t" . '  and kodegudang=\'' . $gudang . '\'';
	$ress = mysql_query($strs);

	while ($bars = mysql_fetch_object($ress)) {
		$saldoqty = $bars->saldoqty;
	}

	if (($status == 0) || ($status == 1)) {
		if (($saldoqty + $qtynotpostedin) < ($qty + $qtynotposted)) {
			echo ' Error: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'];
			$status = 6;
			exit(0);
		}
	}
	else if ($status == 2) {
		$jlhlama = 0;
		$strt = 'select jumlah from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nodok . '\'' . "\r\n\t" . '       and kodebarang=\'' . $kodebarang . '\' and kodeblok=\'' . $blok . '\'';
		$rest = mysql_query($strt);

		while ($bart = mysql_fetch_object($rest)) {
			$jlhlama = $bart->jumlah;
		}

		if (($saldoqty + $jlhlama + $qtynotpostedin) < ($qty + $qtynotposted)) {
			echo ' Error: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'];
			$status = 6;
			exit(0);
		}
	}

	if (($status == 0) || ($status == 1) || ($status == 2)) {
		$stro = 'select a.post from ' . $dbname . '.log_transaksiht a' . "\r\n\t" . '       left join ' . $dbname . '.log_transaksidt b' . "\r\n\t\t" . '   on a.notransaksi=b.notransaksi' . "\r\n\t" . '       where a.tanggal>' . $tanggal . ' and a.kodept=\'' . $pemilikbarang . '\'' . "\r\n\t\t" . '   and b.kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\'' . "\r\n\t\t" . '   and a.post=1';
		$reso = mysql_query($stro);

		if (0 < mysql_num_rows($reso)) {
			$status = 7;
			echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
			exit(0);
		}
	}

	if ($status == 0) {
		$str = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '  ' . "\t\t\t" . '  `tipetransaksi`,`notransaksi`,' . "\r\n\t\t\t" . '  `tanggal`,`kodept`,' . "\r\n\t\t\t" . '  `untukpt`,`keterangan`,' . "\r\n\t\t\t" . '  `kodegudang`,`user`,' . "\r\n\t\t\t" . '  `namapenerima`,`untukunit`,`post`)' . "\r\n\t\t" . 'values(' . $tipetransaksi . ',\'' . $nodok . '\',' . "\r\n\t\t" . '       ' . $tanggal . ',\'' . $pemilikbarang . '\',' . "\r\n\t\t\t" . '  \'' . $ptpemintabarang . '\',\'' . $catatan . '\',' . "\r\n\t\t\t" . '  \'' . $gudang . '\',' . $user . ',' . "\r\n\t\t\t" . '  \'' . $penerima . '\',\'' . $untukunit . '\',' . $post . "\r\n\t\t" . ')';

		if (mysql_query($str)) {
			$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n\t\t\t" . '  `notransaksi`,`kodebarang`,' . "\r\n\t\t\t" . '  `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n\t\t\t" . '  `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n\t\t\t" . '  `kodemesin`)' . "\r\n\t\t\t" . '  values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n\t\t\t" . '  \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n\t\t\t" . '  \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n\t\t\t" . '  \'' . $mesin . '\')';

			if (mysql_query($str)) {
			}
			else {
				echo ' Gagal, (insert detail on status 0)' . addslashes(mysql_error($conn));
				exit(0);
			}
		}
		else {
			echo ' Gagal,  (insert header on status 0)' . addslashes(mysql_error($conn));
			exit(0);
		}
	}

	if ($status == 1) {
		$scek = 'select distinct * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\' and tipetransaksi=5';

		#exit(mysql_error($conn));
		($qcek = mysql_query($scek)) || true;
		$rcek = mysql_num_rows($qcek);

		if ($rcek == 0) {
			exit('Error: This transaction belongs to other user, please reload and start over');
		}

		$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n\t\t\t" . '  `notransaksi`,`kodebarang`,' . "\r\n\t\t\t" . '  `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n\t\t\t" . '  `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n\t\t\t" . '  `kodemesin`)' . "\r\n\t\t\t" . '  values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n\t\t\t" . '  \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n\t\t\t" . '  \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n\t\t\t" . '  \'' . $mesin . '\')';

		if (mysql_query($str)) {
		}
		else {
			echo ' Gagal, (insert detail on status 1)' . addslashes(mysql_error($conn));
			exit(0);
		}
	}

	if ($status == 2) {
		$str = 'update ' . $dbname . '.log_transaksidt set' . "\r\n\t\t\t" . '      `jumlah`=' . $qty . ',' . "\r\n\t\t\t\t" . '  `updateby`=' . $user . ',' . "\r\n\t\t\t\t" . '  `kodekegiatan`=\'' . $kegiatan . '\',' . "\r\n\t\t\t\t" . '  `kodemesin`=\'' . $mesin . '\'' . "\r\n\t\t\t\t" . '  where `notransaksi`=\'' . $nodok . '\'' . "\r\n\t\t\t\t" . '  and `kodebarang`=\'' . $kodebarang . '\'' . "\r\n\t\t\t\t" . '  and `kodeblok`=\'' . $blok . '\'';
		mysql_query($str);

		if (mysql_affected_rows($conn) < 1) {
			echo $str . ' Gagal, (update detail on status 2)' . addslashes(mysql_error($conn));
			exit(0);
		}
	}

	if ($status == 3) {
		echo ' Gagal: Data has been posted';
		exit(0);
	}

	if ($status == 4) {
		echo ' Gagal: Company code of the Recipient is not defined';
		exit(0);
	}

	if ($status == 5) {
		$str = 'delete from ' . $dbname . '.log_transaksidt where kodebarang=\'' . $kodebarang . '\'' . "\r\n\t" . '         and notransaksi=\'' . $nodok . '\' and kodeblok=\'' . $blok . '\' and kodemesin=\'' . $_POST['kdmesin'] . '\'';
		mysql_query($str);

		if (0 < mysql_affected_rows($conn)) {
		}
	}

	$strj = 'select a.*,b.untukpt as pt,' . "\r\n" . '        b.untukunit as unit from ' . $dbname . '.log_transaksidt a ' . "\r\n\t\t" . 'left join  ' . $dbname . '.log_transaksiht b' . "\r\n\t\t" . 'on a.notransaksi=b.notransaksi' . "\r\n" . '        where a.notransaksi=\'' . $nodok . '\'';
	$resj = mysql_query($strj);
	$no = 0;

	while ($barj = mysql_fetch_object($resj)) {
		$no += 1;
		$namabarangk = '';
		$strk = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $barj->kodebarang . '\'';
		$resk = mysql_query($strk);

		while ($bark = mysql_fetch_object($resk)) {
			$namabarangk = $bark->namabarang;
		}

		$namakegiatan = '';
		$strk = 'select namakegiatan from ' . $dbname . '.setup_kegiatan where kodekegiatan=\'' . $barj->kodekegiatan . '\'';
		$resk = mysql_query($strk);

		while ($bark = mysql_fetch_object($resk)) {
			$namakegiatan = $bark->namakegiatan;
		}

		$stream .= '<tr class=rowcontent>' . "\r\n\t\t" . '    <td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->kodebarang . '</td>' . "\r\n\t\t\t" . '<td>' . $namabarangk . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->satuan . '</td>' . "\r\n\t\t\t" . '<td align=right>' . number_format($barj->jumlah, 2, '.', ',') . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->pt . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->unit . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->kodeblok . '</td>' . "\r\n\t\t\t" . '<td>' . $namakegiatan . '</td>' . "\r\n\t\t\t" . '<td>' . $barj->kodemesin . '</td>' . "\r\n\t\t\t" . '<td>' . "\r\n\t\t\t" . '    <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editBast(\'' . $barj->kodebarang . '\',\'' . $namabarangk . '\',\'' . $barj->satuan . '\',\'' . $barj->jumlah . '\',\'' . $barj->kodeblok . '\',\'' . $barj->kodekegiatan . '\',\'' . $barj->kodemesin . '\');">' . "\r\n\t\t" . '        &nbsp <img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delBast(\'' . $nodok . '\',\'' . $barj->kodebarang . '\',\'' . $barj->kodeblok . '\',\'' . $barj->kodemesin . '\');">' . "\r\n\t\t\t" . '</td>' . "\r\n" . ' ' . "\t\t" . '   </tr>';
	}

	if (($status == 6) || ($status == 5)) {
		echo $stream;
	}
	else {
		echo $stream . '####' . $nodok;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
