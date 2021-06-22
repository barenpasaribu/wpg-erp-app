<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$whrind = 'char_length(kodeorganisasi)=4';
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk', $whrind);
$param = $_POST;
$no = 0;

if (isTransactionPeriod()) {
	switch ($param['proses']) {
	case 'getAfd':
		$optKbn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$skbn = 'select distinct untukunit as kodeorg from ' . $dbname . '.log_mrisht ' . "\r\n" . '                   where left(untukunit,4)=\'' . $param['divisiId'] . '\'';

		#exit(mysql_error($conn));
		($qkbn = mysql_query($skbn)) || true;

		while ($rkbn = mysql_fetch_assoc($qkbn)) {
			$optKbn .= '<option value=\'' . $rkbn['kodeorg'] . '\'>' . $optNmOrg[$rkbn['kodeorg']] . '</options>';
		}

		echo $optKbn;
		break;

	case 'getPrd':
		$optKbn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$skbn = 'select distinct left(tanggal,7) as periode from ' . $dbname . '.log_mrisht ' . "\r\n" . '                   where untukunit=\'' . $param['afdId'] . '\' order by tanggal desc';

		#exit(mysql_error($conn));
		($qkbn = mysql_query($skbn)) || true;

		while ($rkbn = mysql_fetch_assoc($qkbn)) {
			$optKbn .= '<option value=\'' . $rkbn['periode'] . '\'>' . $rkbn['periode'] . '</options>';
		}

		echo $optKbn;
		break;

	case 'getHeader':
		if ($param['kbnId'] != '') {
			$whr .= 'and untukunit like \'' . $param['kbnId'] . '%\'';
		}

		if ($param['afdId'] != '') {
			$whr = '';
			$whr .= 'and untukunit=\'' . $param['afdId'] . '\'';
		}

		if ($param['periode'] != '') {
			$whr .= 'and tanggal like \'' . $param['periode'] . '%\'';
		}

		if ($param['nomris'] != '') {
			$whr = '';
			$whr .= ' and  notransaksi like \'' . $param['nomris'] . '%\'';
		}

		//$sdata = 'select * from ' . $dbname . '.log_mrisht where notransaksi!=\'\' ' . $whr . ' ';
		$sdata = 'select * from ' . $dbname . '.log_mrisht where untukunit like \''.$_SESSION['empl']['kodeorganisasi'].'%\' and notransaksi!=\'\' ' . $whr . ' ';

		#exit(mysql_error($conn));
		($qdata = mysql_query($sdata)) || true;

		while ($rdata = mysql_fetch_assoc($qdata)) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $rdata['notransaksi'] . '</td>';
			$tab .= '<td>' . $rdata['tanggal'] . '</td>';
			$tab .= '<td>' . substr($rdata['untukunit'], 0, 4) . '</td>';
			$tab .= '<td>' . $rdata['untukunit'] . '</td>';
			$tab .= '<td>' . $optNmKary[$rdata['dibuat']] . '</td>' . "\r\n" . '                <td align=center><img src=\'images/addplus.png\' style=\'cursor:pointer\' onclick=getDetail(\'' . $rdata['notransaksi'] . '\') title=\'' . $_SESSION['lang']['detail'] . ' ' . $rdata['notransaksi'] . '\' /></td></tr>';
		}

		echo $tab;
		break;

	case 'getDetail':
		$sht = 'select distinct untukunit as kebun,tanggal,kodegudang from ' . $dbname . '.log_mrisht where notransaksi=\'' . $param['notransaksi'] . '\'';

		#exit(mysql_error($conn));
		($qht = mysql_query($sht)) || true;
		$rht = mysql_fetch_assoc($qht);
		$sPrdStr = 'select distinct tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '                      where tutupbuku=0 and kodeorg=\'' . $rht['kodegudang'] . '\'';

		#exit(mysql_error($conn));
		($qPrsStr = mysql_query($sPrdStr)) || true;
		$rPrdStr = mysql_fetch_assoc($qPrsStr);
		$sDet = 'select distinct * from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $param['notransaksi'] . '\'';

		#exit(mysql_error($conn));
		($qDet = mysql_query($sDet)) || true;

		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td id=kdBrg_' . $no . '>' . $rDet['kodebarang'] . '</td>';
			$tab .= '<td id=nmBrg_' . $no . '>' . $optNmBrg[$rDet['kodebarang']] . '</td>';
			$tab .= '<td id=satBrg_' . $no . '>' . $optSatBrg[$rDet['kodebarang']] . '</td>';
			$tab .= '<td id=kdBlok_' . $no . '>' . $rDet['kodeblok'] . '</td>';
			$tab .= '<td id=kdMesin_' . $no . '>' . $rDet['kodemesin'] . '</td>' . "\r\n" . '            <td id=jmlh_' . $no . ' align=right>' . number_format($rDet['jumlah'], 2) . '</td>';
			$tab .= '<td id=realisasiSblm_' . $no . '  align=right>' . number_format($rDet['jumlahrealisasi'], 2) . '</td>';
			$tab .= '<td  align=right><input type=text  id=jmlhPengeluara_' . $no . ' onkeypress=\'return angka_doang(event)\' onblur=cekIsi(' . $no . ') style=width:100px class=myinputtextnumber />' . "\r\n" . '                   <input type=hidden  id=kegId_' . $no . ' value=\'' . $rDet['kodekegiatan'] . '\' />' . "\r\n" . '                   </td>';
			$tab .= '<td align=center><img src=images/save.png class=resicon style=\'cursor:pointer\' onclick=saveDt(' . $no . ',\'' . $param['notransaksi'] . '\') title=\'' . $_SESSION['lang']['save'] . ' ' . $optNmBrg[$rDet['kodebarang']] . '\' /></td></tr>';
		}

		echo $tab . '####' . $rht['kebun'] . '####' . tanggalnormal($rht['tanggal']) . '####' . $param['notransaksi'] . '####' . $rht['kodegudang'] . '####' . tanggalnormal($rPrdStr['tanggalmulai']) . '####' . tanggalnormal($rPrdStr['tanggalsampai']) . '####' . tanggalsystem(tanggalnormal($rPrdStr['tanggalmulai'])) . '####' . tanggalsystem(tanggalnormal($rPrdStr['tanggalsampai']));
		break;

	case 'saveData':
		$tipetransaksi = 5;
		$post = 0;
		$sheader = 'select * from ' . $dbname . '.log_mrisht where notransaksi=\'' . $param['notransaksi'] . '\' and post=1';

		#exit(mysql_error($conn));
		($qheader = mysql_query($sheader)) || true;

		if (mysql_num_rows($qheader) == 0) {
			exit('error: No.MRIS :' . $param['notransaksi'] . ' need to post');
		}

		$rheader = mysql_fetch_assoc($qheader);
		$gudang = $rheader['kodegudang'];
		$smris = 'select distinct notransaksi from ' . $dbname . '.log_transaksiht ' . "\r\n" . '                        where nomris=\'' . $param['notransaksi'] . '\' and ' . "\r\n" . '                        tanggal=\'' . tanggaldgnbar($param['tanggal']) . '\' and post=0 and kodegudang=\'' . $gudang . '\'';

		#exit(mysql_error($conn));
		($qmris = mysql_query($smris)) || true;

		if (0 < mysql_num_rows($qmris)) {
			$rmris = mysql_fetch_assoc($qmris);
			$nodok = $rmris['notransaksi'];
		}
		else {
			$ngantri = 0;
			$num = 1;

			while ($ngantri == 0) {
				$str = 'select max(notransaksi) notransaksi from ' . $dbname . '.log_transaksiht where tipetransaksi>4 and tanggal>=' . $_SESSION['gudang'][$gudang]['start'] . ' and tanggal<=' . $_SESSION['gudang'][$gudang]['end'] . ' and kodegudang=\'' . $gudang . '\' and substring(notransaksi,7,1)!=\'M\' order by notransaksi desc limit 1';

				#exit(mysql_error($conn));
				($res = mysql_query($str)) || true;

				if ($res) {
					while ($bar = mysql_fetch_object($res)) {
						$num = $bar->notransaksi;

						if ($num != '') {
							$num = intval(substr($num, 6, 5)) + 1;
						}
						else {
							$num = 0;
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

					$num = $_SESSION['gudang'][$gudang]['tahun'] . $_SESSION['gudang'][$gudang]['bulan'] . $num . '-GI-' . $gudang;
				}

				$nodok = $num;
				$scek = 'select distinct notransaksi,user from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';

				#exit(mysql_error());
				($qcek = mysql_query($scek)) || true;
				$rcek = mysql_fetch_assoc($qcek);

				if (mysql_num_rows($qcek) == 1) {
					$ngantri = 0;
				}
				else {
					$ngantri = 1;
				}
			}
		}

		if ($blok == '') {
			$blok = $subunit;
		}

		if ($blok == '') {
			$blok = $untukunit;
		}

		$tanggal = tanggalsystem($_POST['tanggal']);
		$kodebarang = $_POST['kdBarag'];
		$penerima = $rheader['dibuat'];
		$satuan = $_POST['satuan'];
		$qty = $_POST['jmlhKeluar'];
		$blok = $_POST['kdblok'];
		$mesin = $_POST['kdMesin'];
		$untukunit = substr($_POST['afdeling'], 0, 4);
		$subunit = $_POST['afdeling'];
		$gudang = $rheader['kodegudang'];
		$catatan = 'Pengeluaran Melalui MRIS dengan No.MRIS :' . $rheader['notransaksi'];
		$kegiatan = $_POST['kegiatan'];
		$pemilikbarang = $rheader['kodept'];
		$user = $_SESSION['standard']['userid'];
		$status = 0;
		$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
		$res = mysql_query($str);

		if (mysql_num_rows($res) == 1) {
			$wher = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $nodok . '\'';
			$optCek = makeOption($dbname, 'log_transaksidt', 'notransaksi,kodebarang', $wher);

			if ($optCek[$nodok] == '') {
				$status = 1;
			}
			else {
				$status = 2;
			}
		}

		if (isset($_POST['delete'])) {
			$status = 5;
		}

		$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'' . "\r\n" . '                   and post=1';

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
		$str = 'select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi ' . "\r\n" . '                from ' . $dbname . '.log_transaksidt a,' . "\r\n" . '                     ' . $dbname . '.log_transaksiht b' . "\r\n" . '                       where a.notransaksi=b.notransaksi ' . "\r\n" . '                   and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                       and a.notransaksi<=\'' . $nodok . '\'' . "\r\n" . '                       and b.tipetransaksi>4 ' . "\r\n" . '                       and b.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                       order by notransaksi desc, waktutransaksi desc limit 1';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$jumlahlalu = $bar->jumlah;
		}

		$qtynotpostedin = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                   b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n" . '                               and a.tipetransaksi<5' . "\r\n" . '                               and a.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                               and a.post=0' . "\t\t\t" . '   ' . "\r\n" . '                               group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotpostedin = $bar2->jumlah;
		}

		if ($qtynotpostedin == '') {
			$qtynotpostedin = 0;
		}

		$qtynotposted = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '               b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $kodebarang . '\' ' . "\r\n" . '                       and a.tipetransaksi>4' . "\r\n" . '                       and a.kodegudang=\'' . $gudang . '\'' . "\r\n" . '                       and a.post=0' . "\t\t" . '   ' . "\r\n" . '                       group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotposted = $bar2->jumlah;
		}

		if ($qtynotposted == '') {
			$qtynotposted = 0;
		}

		$saldoqty = 0;
		//$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '              and kodeorg=\'' . substr($pemilikbarang,0,3) . '\'' . "\r\n" . '                      and kodegudang=\'' . $gudang . '\'';
		$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . ' and kodegudang=\'' . $gudang . '\'';
		$ress = mysql_query($strs);

		while ($bars = mysql_fetch_object($ress)) {
			$saldoqty = $bars->saldoqty;
		}

		if (($status == 0) || ($status == 1)) {
			if (($saldoqty + $qtynotpostedin) < ($qty + $qtynotposted)) {
				echo ' Error X: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'].' (Saldoqty:'.$saldoqty.
					', Qtynotpostedin:'.$qtynotpostedin.', Qty:'.$qty.', Qtynotposted:'.$qtynotposted.') - Gudang:'.substr($pemilikbarang,0,3).'/'.$gudang;
				$status = 6;
				exit(0);
			}
		}
		else if ($status == 2) {
			$jlhlama = 0;
			$strt = 'select jumlah from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $nodok . '\'' . "\r\n" . '                   and kodebarang=\'' . $kodebarang . '\' and kodeblok=\'' . $blok . '\'';
			$rest = mysql_query($strt);

			while ($bart = mysql_fetch_object($rest)) {
				$jlhlama = $bart->jumlah;
			}

			if (($saldoqty + $jlhlama + $qtynotpostedin) < ($qty + $qtynotposted)) {
				echo ' Error XX: ' . $_SESSION['lang']['saldo'] . ' ' . $_SESSION['lang']['tidakcukup'];
				$status = 6;
				exit(0);
			}
		}

		if (($status == 0) || ($status == 1) || ($status == 2)) {
			$stro = 'select a.post from ' . $dbname . '.log_transaksiht a' . "\r\n" . '                   left join ' . $dbname . '.log_transaksidt b' . "\r\n" . '                       on a.notransaksi=b.notransaksi' . "\r\n" . '                   where a.tanggal>' . $tanggal . ' and a.kodept=\'' . $pemilikbarang . '\'' . "\r\n" . '                       and b.kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\'' . "\r\n" . '                       and a.post=1';
			$reso = mysql_query($stro);

			if (0 < mysql_num_rows($reso)) {
				$status = 7;
				echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
				exit(0);
			}
		}

		if ($status == 0) {
			$str = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '                              `tipetransaksi`,`notransaksi`,' . "\r\n" . '                              `tanggal`,`kodept`,' . "\r\n" . '                              `untukpt`,`keterangan`,' . "\r\n" . '                              `kodegudang`,`user`,' . "\r\n" . '                              `namapenerima`,`untukunit`,`post`,`nomris`)' . "\r\n" . '                    values(' . $tipetransaksi . ',\'' . $nodok . '\',' . "\r\n" . '                           ' . $tanggal . ',\'' . $pemilikbarang . '\',' . "\r\n" . '                              \'' . $ptpemintabarang . '\',\'' . $catatan . '\',' . "\r\n" . '                              \'' . $gudang . '\',' . $user . ',' . "\r\n" . '                              \'' . $penerima . '\',\'' . $untukunit . '\',' . $post . ',\'' . $rheader['notransaksi'] . '\'' . "\r\n" . '                    )';

			if (mysql_query($str)) {
				$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                              `notransaksi`,`kodebarang`,' . "\r\n" . '                              `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n" . '                              `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n" . '                              `kodemesin`,`nomris`)' . "\r\n" . '                              values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                              \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n" . '                              \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n" . '                              \'' . $mesin . '\',\'' . $rheader['notransaksi'] . '\')';

				if (mysql_query($str)) {
					$hwr = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $rheader['notransaksi'] . '\' ' . 'and kodemesin=\'' . $mesin . '\' and kodeblok=\'' . $blok . '\'';
					$optJmlhLalu = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi', $hwr);
					$optJmlhMinta = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah', $hwr);
					$jmlh = $qty + $optJmlhLalu[$kodebarang];

					if ($optJmlhMinta[$kodebarang] < $jmlh) {
						exit('error:Amount of expenditures bigger then demand for goods');
					}

					$supdate = 'update ' . $dbname . '.log_mrisdt set' . "\r\n" . '                                          jumlahrealisasi=\'' . $jmlh . '\' where ' . $hwr . '';

					if (!mysql_query($supdate)) {
						echo ' Gagal, (update status on log_mrisdt)' . addslashes(mysql_error($conn));
						exit(0);
					}
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
			$scek = 'select * from ' . $dbname . '.log_transaksiht ' . "\r\n" . '                       where notransaksi=\'' . $nodok . '\' and nomris=\'' . $rheader['notransaksi'] . '\'';

			#exit(mysql_error($conn));
			($qcek = mysql_query($scek)) || true;
			$rcek = mysql_num_rows($qcek);

			if ($rcek == 0) {
				exit('Error: This transaction belongs to other user, please reload and start over');
			}

			$str = 'insert into ' . $dbname . '.log_transaksidt (' . "\r\n" . '                              `notransaksi`,`kodebarang`,' . "\r\n" . '                              `satuan`,`jumlah`,`jumlahlalu`,' . "\r\n" . '                              `kodeblok`,`updateby`,`kodekegiatan`,' . "\r\n" . '                              `kodemesin`,`nomris`)' . "\r\n" . '                              values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                              \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ',' . "\r\n" . '                              \'' . $blok . '\',\'' . $user . '\',\'' . $kegiatan . '\',' . "\r\n" . '                              \'' . $mesin . '\',\'' . $rheader['notransaksi'] . '\')';

			if (mysql_query($str)) {
				$hwr = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $rheader['notransaksi'] . '\' ' . 'and kodemesin=\'' . $mesin . '\' and kodeblok=\'' . $blok . '\'';
				$optJmlhLalu = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi', $hwr);
				$optJmlhMinta = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah', $hwr);
				$jmlh = $qty + $optJmlhLalu[$kodebarang];

				if ($optJmlhMinta[$kodebarang] < $jmlh) {
					exit('error:Amount of expenditures bigger then demand for goods');
				}

				$supdate = 'update ' . $dbname . '.log_mrisdt set' . "\r\n" . '                                          jumlahrealisasi=\'' . $jmlh . '\' where ' . $hwr . '';

				if (!mysql_query($supdate)) {
					echo ' Gagal, (update status on log_mrisdt)' . mysql_error($conn);
					exit(0);
				}
			}
			else {
				echo ' Gagal, (insert detail on status 1)' . mysql_error($conn);
				exit(0);
			}
		}

		if ($status == 2) {
			$hwr = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $nodok . '\'';
			$optJmlhTrDt = makeOption($dbname, 'log_transaksidt', 'kodebarang,jumlah', $hwr);
			$hwr2 = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $param['notransaksi'] . '\'';
			$optJmlhLalu = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi', $hwr2);
			$optJmlh = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah', $hwr2);
			$jmlhIni = ($optJmlhLalu[$kodebarang] - $optJmlhTrDt[$kodebarang]) + $qty;

			if ($optJmlh[$kodebarang] < $jmlhIni) {
				exit('error:Amount of expenditures bigger then demand for goods');
			}

			$str = 'update ' . $dbname . '.log_transaksidt set' . "\r\n" . '                          `jumlah`=' . $qty . ',' . "\r\n" . '                              `updateby`=' . $user . ',' . "\r\n" . '                              `kodekegiatan`=\'' . $kegiatan . '\',' . "\r\n" . '                              `kodemesin`=\'' . $mesin . '\'' . "\r\n" . '                              where `notransaksi`=\'' . $nodok . '\'' . "\r\n" . '                              and `kodebarang`=\'' . $kodebarang . '\'' . "\r\n" . '                              and `kodeblok`=\'' . $blok . '\'';
			mysql_query($str);

			if (mysql_affected_rows($conn) < 1) {
				echo ' Gagal, (update detail on status 2)' . mysql_error($conn);
				exit(0);
			}
			else {
				$hwr = 'kodebarang=\'' . $kodebarang . '\' and notransaksi=\'' . $rheader['notransaksi'] . '\' ' . 'and kodemesin=\'' . $mesin . '\' and kodeblok=\'' . $blok . '\'';
				$supdate = 'update ' . $dbname . '.log_mrisdt set jumlahrealisasi=\'' . $jmlhIni . '\' where ' . "\r\n" . '                               ' . $hwr . '';

				if (!mysql_query($supdate)) {
					echo ' Gagal, (update detail on status 2)' . mysql_error($conn);
					exit(0);
				}
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
			$str = 'delete from ' . $dbname . '.log_transaksidt where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                     and notransaksi=\'' . $nodok . '\' and kodeblok=\'' . $blok . '\'';
			mysql_query($str);

			if (0 < mysql_affected_rows($conn)) {
			}
		}

		break;

	case 'detailLog':
		$limit = 20;
		$page = 0;
		$add = 'right(nomris,4) in (select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\') and tipetransaksi=5 and untukunit like \'' . $_SESSION['empl']['lokasitugas'] . '%\'';

		if ($param['tex'] != '') {
			$notransaksi = $_POST['tex'] . '%';
			$add .= ' and notransaksi like \'' . $notransaksi . '\'';
		}

		if ($param['kdGudng'] != '') {
			$add .= ' and kodegudang= \'' . $param['kdGudng'] . '\'';
		}

		$str = 'select count(*) as jlhbrs from ' . $dbname . '.log_transaksiht where ' . $add . "\t\t\r\n" . '                  order by jlhbrs desc';
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

		$tab .= '<table class=sortable cellspacing=1 border=0><thead>' . "\r\n" . '                   <tr class=rowheader>' . "\r\n" . '                   <td>No.</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['momordok'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['nomris'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['untukunit'] . '</td>' . "\t" . '  ' . "\t" . ' ' . "\r\n" . '                   <td>' . $_SESSION['lang']['dbuat_oleh'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['posted'] . '</td>' . "\r\n" . '                   <td></td></tr></head><tbody>';
		$sdta = 'select * from ' . $dbname . '.log_transaksiht where ' . $add . "\r\n" . '                       order by notransaksi desc limit ' . $offset . ',20';

		#exit(mysql_error($conn));
		($qdta = mysql_query($sdta)) || true;

		while ($rdta = mysql_fetch_assoc($qdta)) {
			$no += 1;
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                       <td>' . $no . '</td>' . "\r\n" . '                       <td>' . $rdta['kodegudang'] . '</td>' . "\r\n" . '                       <td>' . $rdta['tipetransaksi'] . '</td>' . "\r\n" . '                       <td>' . $rdta['notransaksi'] . '</td>' . "\r\n" . '                       <td>' . $rdta['nomris'] . '</td>' . "\r\n" . '                       <td>' . $rdta['tanggal'] . '</td>' . "\r\n" . '                       <td>' . $rdta['kodept'] . '</td>' . "\r\n" . '                       <td>' . $rdta['untukunit'] . '</td>' . "\t" . '  ' . "\t" . ' ' . "\r\n" . '                       <td>' . $optNmKary[$rdta['user']] . '</td>' . "\r\n" . '                       <td>' . $optNmKary[$rdta['posted']] . '</td>';
			$er = '';

			if ($rdta['post'] == 0) {
				$er = '<img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delXBapb(\'' . $rdta['notransaksi'] . '\',\'' . $rdta['nomris'] . '\');">&nbsp ';
			}

			$tab .= '<td>' . $er . ' <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="previewBast(\'' . $rdta['notransaksi'] . '\',event);"> </td>' . "\r\n" . '                        </tr>';
		}

		$tab .= '</tbody>' . "\r\n" . '                   <tfoot>' . "\r\n" . '                    <tr><td colspan=11 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '   <br>' . "\r\n" . '       <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '   <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '   </td>' . "\r\n\t" . '   </tr>' . "\r\n" . '                   </tfoot>' . "\r\n" . '                   </table>';
		echo $tab;
		break;

	case 'delData':
		$sdet = 'select distinct kodebarang,jumlah from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $param['notransaksi'] . '\'';

		#exit(mysql_error());
		($qdet = mysql_query($sdet)) || true;

		while ($rdata = mysql_fetch_assoc($qdet)) {
			$wr = 'kodebarang=\'' . $rdata['kodebarang'] . '\' and notransaksi=\'' . $param['nomris'] . '\'';
			$optJmlh = makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi', $wr);
			$er = $optJmlh[$rdata['kodebarang']] - $rdata['jumlah'];
			$sup = 'update ' . $dbname . '.log_mrisdt set jumlahrealisasi=' . $er . ' where ' . $wr . '';

			if (!mysql_query($sup)) {
				exit('error: db error' . mysql_error() . '___' . $sup);
			}
		}

		$sdel = 'delete from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $param['notransaksi'] . '\'';

		if (!mysql_query($sdel)) {
			exit('error: db error' . mysql_error() . '___' . $sdel);
		}
		else {
			$sdel = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $param['notransaksi'] . '\'';

			if (!mysql_query($sdel)) {
				exit('error: db error' . mysql_error() . '___' . $sdel);
			}
		}

		break;

	case 'getPostDt':
		$sDet = 'select distinct * from ' . $dbname . '.log_mrisdt where notransaksi=\'' . $param['notransaksi'] . '\'';

		#exit(mysql_error($conn));
		($qDet = mysql_query($sDet)) || true;

		while ($rDet = mysql_fetch_assoc($qDet)) {
			$sht = 'select distinct jumlahrealisasi,jumlah from ' . $dbname . '.log_mrisdt ' . "\r\n" . '                      where notransaksi=\'' . $param['notransaksi'] . '\' and kodebarang=\'' . $rDet['kodebarang'] . '\' ' . 'and kodemesin=\'' . $rDet['kodemesin'] . '\' and kodeblok=\'' . $rDet['kodeblok'] . '\'';

			#exit(mysql_error($conn));
			($qht = mysql_query($sht)) || true;
			$rht = mysql_fetch_assoc($qht);
			$no += 1;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td id=kdBrg_' . $no . '>' . $rDet['kodebarang'] . '</td>';
			$tab .= '<td id=nmBrg_' . $no . '>' . $optNmBrg[$rDet['kodebarang']] . '</td>';
			$tab .= '<td id=satBrg_' . $no . '>' . $optSatBrg[$rDet['kodebarang']] . '</td>';
			$tab .= '<td id=kdBlok_' . $no . '>' . $rDet['kodeblok'] . '</td>';
			$tab .= '<td id=kdMesin_' . $no . '>' . $rDet['kodemesin'] . '</td>' . "\r\n" . '                <td id=jmlh_' . $no . ' align=right>' . number_format($rht['jumlah'], 2) . '</td>';
			$tab .= '<td id=realisasiSblm_' . $no . '  align=right>' . number_format($rht['jumlahrealisasi'], 2) . '</td>';

			if ($optNotran[$param['notransaksi']] != '') {
				$rDet['jumlah'] = $rDet['jumlah'];
			}
			else {
				$rDet['jumlah'] = 0;
			}

			$tab .= '<td  align=right>' . "\r\n" . '                       <input type=text  id=jmlhPengeluara_' . $no . ' onkeypress=\'return angka_doang(event)\' onblur=cekIsi(' . $no . ') style=\'width:100px\' value=\'' . $rDet['jumlah'] . '\' class=myinputtextnumber />' . "\r\n" . '                       <input type=hidden  id=kegId_' . $no . ' value=\'' . $rDet['kodekegiatan'] . '\' />' . "\r\n" . '                       </td>';
			$tab .= '<td align=center><img src=images/save.png class=resicon style=\'cursor:pointer\' onclick=saveDt(' . $no . ',\'' . $param['notransaksi'] . '\') title=\'' . $_SESSION['lang']['save'] . ' ' . $optNmBrg[$rDet['kodebarang']] . '\' /></td></tr>';
		}

		echo $tab;
		break;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
