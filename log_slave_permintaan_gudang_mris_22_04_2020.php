<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';

if (isTransactionPeriod()) {
	$param = $_POST;

	switch ($param['proses']) {
	case 'getNotrans':
		$gudang = $param['gudang'];
		$num = 1;
		$str = 'select max(notransaksi) as notransaksi from ' . $dbname . '.log_mrisht where tipetransaksi>4 and tanggal>=' . $_SESSION['gudang'][$gudang]['start'] . ' and tanggal<=' . $_SESSION['gudang'][$gudang]['end'] .' and right(notransaksi,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by notransaksi desc limit 1';
		
		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_object($res)) {
				$num = $bar->notransaksi;

				if ($num != '') {
					$num = intval(substr($num, 8, 5)) + 1;
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

			$tgld = date('Ymd');
			$num = $tgld . $num . '-' . $_SESSION['empl']['lokasitugas'];
			echo $num;
		}
		else {
			echo ' Gagal getNotrans,' . addslashes(mysql_error($conn));
		}

		break;

	case 'simpan':
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
		$whrdt = 'char_length(kodeorganisasi)=4';
		$optUtk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk', $whrdt);

		if ($subunit != '') {
			$untukunit = $subunit;
		}

		$post = 0;

		if ($blok == '') {
			$blok = $subunit;
		}

		if ($blok == '') {
			$blok = $untukunit;
		}

		$tipetransaksi = 5;
		$status = 0;

		if ($_POST['statInputan'] == '0') {
			$antri = 0;

			while ($antri == 0) {
				$str = 'select * from ' . $dbname . '.log_mrisht where notransaksi=\'' . $nodok . '\'';

				#exit(mysql_error($conn));
				($res = mysql_query($str)) || true;

				if (mysql_num_rows($res) == 1) {
					$antri = 1;
					$num = 1;
					$str = 'select max(notransaksi) as notransaksi from ' . $dbname . '.log_mrisht where tipetransaksi>4 and tanggal>=' . $_SESSION['gudang'][$gudang]['start'] . ' and tanggal<=' . $_SESSION['gudang'][$gudang]['end'] . "\r\n" . '                                           and right(notransaksi,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '                                           order by notransaksi desc limit 1';

					if ($res = mysql_query($str)) {
						while ($bar = mysql_fetch_object($res)) {
							$num = $bar->notransaksi;

							if ($num != '') {
								$num = intval(substr($num, 8, 5)) + 1;
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

						$tgld = date('Ymd');
						$nodok = $tgld . $num . '-' . $_SESSION['empl']['lokasitugas'];
						$str = 'select * from ' . $dbname . '.log_mrisht where notransaksi=\'' . $nodok . '\'';

						#exit(mysql_error($conn));
						($res = mysql_query($str)) || true;

						if (mysql_num_rows($res) == 1) {
							$antri = 0;
						}
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

		$str = 'select * from '. $dbname . '.log_mrisht where notransaksi=\'' . $nodok . '\' and post=1';

		if (0 < mysql_num_rows(mysql_query($str))) {
			$status = 3;
		}

		$ptpemintabarang = '';
		$stre = ' select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $untukunit . '\'';
		$rese = mysql_query($stre);

		while ($bare = mysql_fetch_object($rese)) {
			$strf = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bare->induk . '\'';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				if ($barf->tipe == 'PT') {
					$ptpemintabarang = $bare->induk;
				}
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
		$str = 'select a.jumlah as jumlah,a.notransaksi as notransaksi,a.waktutransaksi ' . "\r\n" . '                    from ' . $dbname . '.log_mrisdt a,' . "\r\n" . '                     ' . $dbname . '.log_mrisht b' . "\r\n" . '                       where a.notransaksi=b.notransaksi ' . "\r\n" . '                    and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                       and a.notransaksi<=\'' . $nodok . '\'' . "\r\n" . '                       and b.tipetransaksi>4 ' . "\r\n" . '                       and b.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                       order by notransaksi desc, waktutransaksi desc limit 1';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$jumlahlalu = $bar->jumlah;
		}

		$qtynotpostedin = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_mrisht a left join ' . $dbname . '.log_mrisdt' . "\r\n" . '                    b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n" . '                               and a.tipetransaksi<5' . "\r\n" . '                               and a.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                               and a.post=0' . "\t\t\t" . '   ' . "\r\n" . '                               group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotpostedin = $bar2->jumlah;
		}

		if ($qtynotpostedin == '') {
			$qtynotpostedin = 0;
		}

		$qtynotposted = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_mrisht a left join ' . $dbname . '.log_mrisdt' . "\r\n" . '                    b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n" . '                       and a.tipetransaksi>4' . "\r\n" . '                       and a.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                       and a.post=0' . "\t\t" . '   ' . "\r\n" . '                       group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotposted = $bar2->jumlah;
		}

		if ($qtynotposted == '') {
			$qtynotposted = 0;
		}

		$saldoqty = 0;
		//$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                    and kodeorg=\'' . $pemilikbarang . '\'' . "\r\n" . '                      and kodegudang=\'' . $gudang . '\'';
		//$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                    and kodeorg=\'' .$_SESSION['empl']['kodeorganisasi'] . '\'' . "\r\n" . '                      and kodegudang=\'' . $gudang . '\'';
		$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . ' and kodegudang=\'' . $gudang . '\'';
		 
		$ress = mysql_query($strs);

		while ($bars = mysql_fetch_object($ress)) {
			$saldoqty = $bars->saldoqty;
		}

		if (($status == 0) || ($status == 1)) {
			if (($saldoqty + $qtynotpostedin) < ($qty + $qtynotposted)) {
				echo ' Error status 0: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'];
				$status = 6;
				exit(0);
			}
		}
		else if ($status == 2) {
			$jlhlama = 0;
			$strt = 'select jumlah from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $nodok . '\'' . "\r\n" . '                    and kodebarang=\'' . $kodebarang . '\' and kodeblok=\'' . $blok . '\'';
			$rest = mysql_query($strt);

			while ($bart = mysql_fetch_object($rest)) {
				$jlhlama = $bart->jumlah;
			}

			if (($saldoqty + $jlhlama + $qtynotpostedin) < ($qty + $qtynotposted)) {
				echo ' Error status 2: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'];
				$status = 6;
				exit(0);
			}
		}

		if (($status == 0) || ($status == 1) || ($status == 2)) {
			$stro = 'select a.post from ' . $dbname . '.log_mrisht a' . "\r\n" . '                    left join ' . $dbname . '.log_mrisdt b' . "\r\n" . '                       on a.notransaksi=b.notransaksi' . "\r\n" . '                    where a.tanggal>' . $tanggal . ' and a.kodept=\'' . $pemilikbarang . '\'' . "\r\n" . '                       and b.kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\'' . "\r\n" . '                       and a.post=1';
			$reso = mysql_query($stro);

			if (0 < mysql_num_rows($reso)) {
				$status = 7;
				echo ' Error status 7:' . $_SESSION['lang']['tanggaltutup'];
				exit(0);
			}
		}

		if ($status == 0) {
			$str = 'insert into ' . $dbname . '.log_mrisht (' . "\r\n" . '                              `tipetransaksi`,`notransaksi`,' . "\r\n" . '                              `tanggal`,`keterangan`,' . "\r\n" . '                              `kodegudang`,`kodept`,`untukpt`,`dibuat`,' . "\r\n" . '                              `mengetahui`,`untukunit`,`post`)' . "\r\n" . '                    values(' . $tipetransaksi . ',\'' . $nodok . '\',' . "\r\n" . '                           ' . $tanggal . ',\'' . $catatan . '\',' . "\r\n" . '                              \'' . $gudang . '\',\'' . $pemilikbarang . '\',\'' . $optUtk[substr($untukunit, 0, 4)] . '\',\'' . $user . '\',' . "\r\n" . '                              \'' . $penerima . '\',\'' . $untukunit . '\',' . $post . "\r\n" . '                    )';

			if (mysql_query($str)) {
				$str = 'insert into ' . $dbname . '.log_mrisdt (' . "\r\n" . '                              `notransaksi`,`kodebarang`,' . "\r\n" . '                              `satuan`,`jumlah`,`jumlahrealisasi`,' . "\r\n" . '                              `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n" . '                              `kodemesin`)' . "\r\n" . '                              values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                              \'' . $satuan . '\',' . $qty . ',0,' . "\r\n" . '                              \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n" . '                              \'' . $mesin . '\')';

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
			$str = 'insert into ' . $dbname . '.log_mrisdt (' . "\r\n" . '                              `notransaksi`,`kodebarang`,' . "\r\n" . '                              `satuan`,`jumlah`,`jumlahrealisasi`,' . "\r\n" . '                              `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n" . '                              `kodemesin`)' . "\r\n" . '                              values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                              \'' . $satuan . '\',' . $qty . ',0,' . "\r\n" . '                              \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n" . '                              \'' . $mesin . '\')';

			if (mysql_query($str)) {
			}
			else {
				echo ' Gagal, (insert detail on status 1)' . addslashes(mysql_error($conn));
				exit(0);
			}
		}

		if ($status == 2) {
			$str = 'update ' . $dbname . '.log_mrisdt set' . "\r\n" . '                                  `jumlah`=' . $qty . ',' . "\r\n" . '                                      `updateby`=' . $user . ',' . "\r\n" . '                                      `kodekegiatan`=\'' . $kegiatan . '\',' . "\r\n" . '                                      `kodemesin`=\'' . $mesin . '\'' . "\r\n" . '                                      where `notransaksi`=\'' . $nodok . '\'' . "\r\n" . '                                      and `kodebarang`=\'' . $kodebarang . '\'' . "\r\n" . '                                      and `kodeblok`=\'' . $blok . '\'';
			mysql_query($str);

			if (mysql_affected_rows($conn) < 1) {
				echo ' Gagal, (update detail on status 2)' . addslashes(mysql_error($conn));
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
			$str = 'delete from ' . $dbname . '.log_mrisdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                     and notransaksi=\'' . $nodok . '\' and kodeblok=\'' . $blok . '\' and kodemesin=\'' . $_POST['kdmesin'] . '\'';
			mysql_query($str);

			if (0 < mysql_affected_rows($conn)) {
			}
		}

		$strj = 'select a.*,b.untukunit as unit from ' . $dbname . '.log_mrisdt a ' . "\r\n" . '                    left join  ' . $dbname . '.log_mrisht b' . "\r\n" . '                    on a.notransaksi=b.notransaksi' . "\r\n" . '                    where a.notransaksi=\'' . $nodok . '\'';
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

			$brt = $barj->kodeblok;

			if ($barj->kodemesin != '') {
				$brt = $barj->kodemesin;
			}

			$stipe = 'select distinct tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $brt . '\'';

			#exit(mysql_error($conn));
			($qtipe = mysql_query($stipe)) || true;
			$rtipe = mysql_fetch_assoc($qtipe);
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                            <td>' . $barj->kodebarang . '</td>' . "\r\n" . '                            <td>' . $namabarangk . '</td>' . "\r\n" . '                            <td>' . $barj->satuan . '</td>' . "\r\n" . '                            <td align=right>' . number_format($barj->jumlah, 2, '.', ',') . '</td>' . "\r\n" . '                            <td>' . $barj->unit . '</td>' . "\r\n" . '                            <td>' . $barj->kodeblok . '</td>' . "\r\n" . '                            <td>' . $namakegiatan . '</td>' . "\r\n" . '                            <td>' . $barj->kodemesin . '</td>' . "\r\n" . '                            <td>' . "\r\n" . '                                <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editBast(\'' . $barj->kodebarang . '\',\'' . $namabarangk . '\',\'' . $barj->satuan . '\',\'' . $barj->jumlah . '\',\'' . $barj->kodeblok . '\',\'' . $barj->kodekegiatan . '\',\'' . $barj->kodemesin . '\',\'' . $rtipe['tipe'] . '\');">' . "\r\n" . '                            &nbsp <img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delBast(\'' . $nodok . '\',\'' . $barj->kodebarang . '\',\'' . $barj->kodeblok . '\',\'' . $barj->kodemesin . '\');">' . "\r\n" . '                            </td>' . "\r\n" . '                       </tr>';
		}

		if (($status == 6) || ($status == 5)) {
			echo $tab;
		}
		else {
			echo $tab . '####' . $nodok;
		}

		break;

	case 'loadData':
		$limit = 20;
		$page = 0;
		$gudang = $_POST['gudang'];
		$add = '';

		if (isset($_POST['tex'])) {
			$add = ' and notransaksi like \'' . $_POST['tex'] . '%\'';
		}

		$str = 'select count(*) as jlhbrs from ' . $dbname . '.log_mrisht where untukunit like \''.$_SESSION['empl']['kodeorganisasi'].'%\' and kodegudang=\'' . $gudang . '\'' . "\r\n" . '                and tipetransaksi =5' . "\r\n" . '                and right(notransaksi,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'  ' . $add . "\t\t\r\n" . '                order by jlhbrs desc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$jlhbrs = $bar->jlhbrs;
		}

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;
		//$str = 'select * from ' . $dbname . '.log_mrisht where kodegudang=\'' . $gudang . '\' and tipetransaksi =5' . "\r\n" . '                and right(notransaksi,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'   ' . $add . "\r\n" . '                order by notransaksi desc limit ' . $offset . ',20';

		$str = 'select * from ' . $dbname . '.log_mrisht where untukunit like \''.$_SESSION['empl']['kodeorganisasi'].'%\' and kodegudang=\'' . $gudang . '\' and tipetransaksi =5' . "\r\n" . '                and right(notransaksi,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'   ' . $add .' and (dibuat ='.$_SESSION['empl']['karyawanid'].' or mengetahui='.$_SESSION['empl']['karyawanid'].') order by notransaksi desc limit ' . $offset . ',20';
		
		//echo "warning: ".$str;
		//exit();
		
		$res = mysql_query($str);
		$no = $page * $limit;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$sblok = 'select distinct kodeblok from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $bar->notransaksi . '\'';

			#exit(mysql_error($conn));
			($qblok = mysql_query($sblok)) || true;
			$rblok = mysql_fetch_assoc($qblok);
			$namapembuat = '';
			$stry = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->dibuat;
			$resy = mysql_query($stry);

			while ($bary = mysql_fetch_object($resy)) {
				$namapembuat = $bary->namauser;
			}

			$namaposting = 'Not Posted';

			if (intval($bar->postedby) != 0) {
				$stry = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->postedby;
				$resy = mysql_query($stry);

				while ($bary = mysql_fetch_object($resy)) {
					$namaposting = $bary->namauser;
				}
			}

			if (($namaposting == 'Not Posted') && ($bar->post == 1)) {
				$namaposting = ' Posted By ???';
			}

			if ($bar->post < 1) {
				$add = '<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editXBast(\'' . $bar->notransaksi . '\',\'' . substr($rblok['kodeblok'], 0, 6) . '\',\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->mengetahui . '\',\'' . $bar->keterangan . '\');">';
				$add .= '&nbsp<img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delXBapb(\'' . $bar->notransaksi . '\');">' . "\r\n" . '                       &nbsp<img src=images/hot.png class=resicon  title=\'posting\' onclick="postingData(\'' . $bar->notransaksi . '\',\'' . $bar->kodegudang . '\');">';
			}
			else {
				$add = '';
			}

			echo '<tr class=rowcontent>' . "\r\n" . '                <td>' . $no . '</td>' . "\r\n" . '                <td>' . $bar->kodegudang . '</td>' . "\r\n" . '                <td title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                <td>' . $bar->notransaksi . '</td>' . "\r\n" . '                <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                <td>' . $bar->untukunit . '</td>' . "\t\t\t" . '  ' . "\r\n" . '                <td>' . $namapembuat . '</td>' . "\r\n" . '                <td>' . $namaposting . '</td>' . "\r\n" . '                <td align=center>' . "\r\n" . '                ' . $add . "\r\n" . '                <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="previewBast(\'' . $bar->notransaksi . '\',event);"> ' . "\r\n" . '                </td>' . "\r\n" . '                </tr>';
		}

		echo '<tr><td colspan=11 align=center>' . "\r\n" . '                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '                <br>' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                </td>' . "\r\n" . '                </tr>';
		break;

	case 'getKegiatan':
		$blok = $_POST['blok'];
		$kdkeg = $_POST['kdkegiatan'];

		if ($blok != '') {
			$str = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $blok . '\'';
			$res = mysql_query($str);
			$tipe = $_POST['jenis'];

			while ($bar = mysql_fetch_object($res)) {
				$tipe = $bar->tipe;
			}

			if (($tipe == 'STENGINE') || ($tipe == 'STATION')) {
				$optKegiatan = '<option value=\'\'></option>';
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                   where kelompok=\'MIL\' order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else if ($tipe == 'BLOK') {
				$optSta = makeOption($dbname, 'setup_blok', 'kodeorg,statusblok');

				if ($optSta[$blok] == 'TM') {
					$str = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan where (kelompok=\'TM\' or kelompok=\'PNN\') order by kelompok,namakegiatan';
				}
				else {
					$str = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan where kelompok=\'' . $optSta[$blok] . '\' order by kelompok,namakegiatan';
				}

				$resf = mysql_query($str);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else if ($tipe == 'WORKSHOP') {
				$optKegiatan = '<option value=\'\'></option>';
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . ' where kelompok=\'WSH\' order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else if ($tipe == 'SIPIL') {
				$optKegiatan = '<option value=\'\'></option>';
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                   where kelompok=\'SPL\' order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else if ($tipe == 'TRAKSI') {
				$optKegiatan = '<option value=\'\'></option>';
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                   where kelompok=\'TRK\' order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else if ($tipe == 'BIBITAN') {
				$optKegiatan = '<option value=\'\'></option>';
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                   where  kelompok in (\'BBT\',\'MN\',\'PN\') order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					if ($kdkeg == $barf->kodekegiatan) {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
					else {
						$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
					}
				}

				echo $optKegiatan;
			}
			else {
				if ((substr($blok, 0, 2) == 'AK') || (substr($blok, 0, 2) == 'PB')) {
					$tipeasset = substr($blok, 3, 3);
					$tipeasset = str_replace('0', '', $tipeasset);
					$str = 'select akunak,namatipe from ' . $dbname . '.sdm_5tipeasset where kodetipe=\'' . $tipeasset . '\'';
					$resf = mysql_query($str);

					if (0 < mysql_num_rows($resf)) {
						while ($barf = mysql_fetch_object($resf)) {
							$optKegiatan .= '<option value=\'' . $barf->akunak . '\'>[Project]-' . $barf->namatipe . '</option>';
						}

						echo $optKegiatan;
					}
					else {
						exit(' Error: Akun aktiva dalam kontruksi belum ditentukan untuk kode ' . $tipeasset);
					}
				}
				else {
					$optKegiatan = '<option value=\'\'></option>';
					$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t\t\t\t\t\t" . '   where kelompok=\'KNT\' order by kelompok,namakegiatan';
					$resf = mysql_query($strf);

					while ($barf = mysql_fetch_object($resf)) {
						if ($kdkeg == $barf->kodekegiatan) {
							$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
						}
						else {
							$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
						}
					}

					echo $optKegiatan;
				}
			}
		}
		else {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                       where kelompok=\'KNT\' order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				if ($kdkeg == $barf->kodekegiatan) {
					$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\' selected>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
				}
				else {
					$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
				}
			}

			echo $optKegiatan;
		}

		break;

	case 'hapustrans':
		$notransaksi = $_POST['notransaksi'];
		$str = 'select post from ' . $dbname . '.log_mrisht where notransaksi=\'' . $notransaksi . '\'';
		$res = mysql_query($str);
		$ststus = 0;

		while ($bar = mysql_fetch_object($res)) {
			$status = $bar->post;
		}

		if ($status == 1) {
			echo ' Gagal/Error, Document has been posted';
		}
		else {
			$str = 'delete from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $notransaksi . '\'';

			if (mysql_query($str)) {
				$str = 'delete from ' . $dbname . '.log_mrisht where notransaksi=\'' . $notransaksi . '\'';
				mysql_query($str);
			}
		}

		break;

	case 'postingdata':
		$whrd = 'notransaksi=\'' . $param['notransaksi'] . '\'';
		$dcek = makeOption($dbname, 'log_mrisht', 'notransaksi,post', $whrd);

		if ($dcek[$param['notransaksi']] == 0) {
			$supd = 'update ' . $dbname . '.log_mrisht set post=1,postedby=\'' . $_SESSION['standard']['userid'] . '\' where notransaksi=\'' . $param['notransaksi'] . '\'';

			if (!mysql_query($supd)) {
				exit('error: ' . $supd . '___' . mysql_query($conn));
			}
		}
		else {
			exit('error: Already posted');
		}

		break;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
