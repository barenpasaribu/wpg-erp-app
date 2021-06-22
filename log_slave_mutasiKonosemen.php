<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';

if (isTransactionPeriod()) {
	$param = $_POST;
	$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
	$optSatBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

	switch ($param['proses']) {
	case 'getKonosemen':
		echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '            <thead>' . "\r\n" . '            <tr class=rowheader>' . "\r\n" . '              <td>No</td>' . "\r\n" . '              <td>' . $_SESSION['lang']['nopacking1'] . '</td>' . "\r\n" . '              <td>' . $_SESSION['lang']['nokonosemen'] . '</td>' . "\r\n" . '              <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '            </tr>' . "\r\n" . '            </thead>' . "\r\n" . '            <tbody>';
		$sKono = 'select distinct nokonosemen,nokonosemenexp,shipper from ' . $dbname . '.log_konosemenht where (nokonosemen like \'%' . $param['txtcari'] . '%\' or nokonosemenexp like \'%' . $param['txtcari'] . '%\')' . "\r\n" . '                and postingkirim=1 and kodept=\'' . $param['pemilikbarang'] . '\' and statusmutasi=0 and kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

		#exit(mysql_error($conn));
		($qKono = mysql_query($sKono)) || true;

		while ($rKono = mysql_fetch_assoc($qKono)) {
			$spacking = 'select distinct nokonosemen,nopackl from ' . $dbname . '.log_rinciankono where nokonosemen=\'' . $rKono['nokonosemen'] . '\'';

			#exit(mysql_error($conn));
			($qPacking = mysql_query($spacking)) || true;

			while ($rPacking = mysql_fetch_assoc($qPacking)) {
				$no += 1;
				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick=saveKono(\'' . $rPacking['nopackl'] . '\',\'' . $param['gudang'] . '\',\'' . $param['gdngTujuan'] . '\',\'' . $param['pemilikbarang'] . '\',\'' . $param['tngl'] . '\',\'' . $param['nodok'] . '\') ' . "\r\n" . '                          title=\'' . $_SESSION['lang']['save'] . ' ' . $_SESSION['lang']['mutasi'] . ' ' . $_SESSION['lang']['dari'] . ' ' . $_SESSION['lang']['nokonosemen'] . '\'>';
				echo '<td>' . $no . '</td>';
				echo '<td>' . $rPacking['nopackl'] . '</td>';
				echo '<td>' . $rKono['nokonosemen'] . '</td>';
				echo '<td>' . $optSupp[$rKono['shipper']] . '</td>';
				echo '</tr>';
			}
		}

		echo '</tbody></table>';
		break;

	case 'saveKonosemen':
		$sdel = 'delete from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $param['notransaksiGdng'] . '\'';

		if (!mysql_query($sdel)) {
			exit('error: db bermasalah pas delete' . mysql_error($conn) . '__' . $sdel);
		}

		$scek2 = 'select * from ' . $dbname . '.log_transaksidt where statussaldo=1 and nopo=\'' . $param['nokonsemen'] . '\'';

		#exit(mysql_error());
		($qcek2 = mysql_query($scek2)) || true;
		$rcek2 = mysql_num_rows($qcek2);

		if ($rcek2 == 1) {
			exit('error: Notransaksi ini sudah terposting');
		}

		$strDet = 'insert into ' . $dbname . '.log_transaksidt (`notransaksi`,`kodebarang`,`satuan`,`jumlah`,`jumlahlalu`,`updateby`,nopo)' . "\r\n" . '                     values ';
		$nod = 1;
		$awal = 0;
		$statAda = 0;
		$isiDetailAja = 0;
		$sDataBrg = 'select * from ' . $dbname . '.log_rinciankono where nopackl=\'' . $param['nokonsemen'] . '\'  order by kodebarang desc';

		#exit(mysql_error($conn));
		($qDataBrg = mysql_query($sDataBrg)) || true;
		$rowDtBrg = mysql_num_rows($qDataBrg);

		if ($rowDtBrg == 0) {
			exit('error: This  ' . $_SESSION['lang']['nokonosemen'] . ' don\'t have PO from e-Agro system');
		}

		while ($rDataBrg = mysql_fetch_assoc($qDataBrg)) {
			$scekPenerimaan = 'select * from ' . $dbname . '.log_transaksi_vw ' . 'where nopo=\'' . $rDataBrg['nopo'] . '\' and kodebarang=\'' . $rDataBrg['kodebarang'] . '\'' . ' and  tipetransaksi=1 and post=1 and statussaldo=1 ';

			#exit(mysql_error($conn));
			($qcekPenerimaan = mysql_query($scekPenerimaan)) || true;
			$rcekPenerimaan = mysql_num_rows($qcekPenerimaan);

			if ($rcekPenerimaan != 1) {
				$errBrg += $rDataBrg['kodebarang'];
				continue;
			}

			$whrSat = 'nopo=\'' . $rDataBrg['nopo'] . '\' and kodebarang=\'' . $rDataBrg['kodebarang'] . '\'';
			$optSat = makeOption($dbname, 'log_podt', 'kodebarang,satuan', $whrSat);
			$whrKonv = 'kodebarang=\'' . $rDataBrg['kodebarang'] . '\' and satuankonversi=\'' . $optSat[$rDataBrg['kodebarang']] . '\'';
			$optKonv = makeOption($dbname, 'log_5stkonversi', 'kodebarang,jumlah', $whrKonv);
			$qty[$rDataBrg['kodebarang']] = $rDataBrg['jumlah'];

			if (isset($optKonv[$rDataBrg['kodebarang']]) != '') {
				$qty[$rDataBrg['kodebarang']] = $rDataBrg['jumlah'] / $optKonv[$rDataBrg['kodebarang']];
			}

			$str = 'select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi ' . "\r\n" . '                      from ' . $dbname . '.log_transaksidt a,' . $dbname . '.log_transaksiht b where a.notransaksi=b.notransaksi ' . "\r\n" . '                      and a.kodebarang=\'' . $rDataBrg['kodebarang'] . '\' and a.notransaksi<=\'' . $param['notransaksiGdng'] . '\' ' . "\r\n" . '                      and tipetransaksi>4 and b.kodegudang=\'' . $param['gdngPengirim'] . '\'' . "\r\n" . '                      order by notransaksi desc, waktutransaksi desc limit 1';
			$res = mysql_query($str);
			$bar = mysql_fetch_object($res);

			if ($bar->jumlah == '') {
				$bar->jumlah = 0;
			}

			$jumlahlalu[$rDataBrg['kodebarang']] = $bar->jumlah;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $param['pemilikBrg'] . '\' and b.kodebarang=\'' . $rDataBrg['kodebarang'] . '\' ' . "\r\n" . '                       and a.tipetransaksi<5 and a.kodegudang=\'' . $param['gdngPengirim'] . '\' and a.post=0 group by kodebarang';
			$res2 = mysql_query($str2);
			$bar2 = mysql_fetch_object($res2);
			$qtynotpostedin[$rDataBrg['kodebarang']] = $bar2->jumlah;

			if ($qtynotpostedin[$rDataBrg['kodebarang']] == '') {
				$qtynotpostedin[$rDataBrg['kodebarang']] = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                b on a.notransaksi=b.notransaksi where kodept=\'' . $param['pemilikBrg'] . '\' and b.kodebarang=\'' . $rDataBrg['kodebarang'] . '\' ' . "\r\n" . '                   and a.tipetransaksi>4' . "\r\n" . '                   and a.kodegudang=\'' . $param['gdngPengirim'] . '\'' . "\r\n" . '                   and a.post=0' . "\t\t" . '   ' . "\r\n" . '                   group by kodebarang';
			$res2 = mysql_query($str2);
			$bar2 = mysql_fetch_object($res2);
			$qtynotposted[$rDataBrg['kodebarang']] = $bar2->jumlah;
			$strs = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $rDataBrg['kodebarang'] . '\'' . "\r\n" . '                       and kodeorg=\'' . $param['pemilikBrg'] . '\' and kodegudang=\'' . $param['gdngPengirim'] . '\'';
			$ress = mysql_query($strs);
			$bars = mysql_fetch_object($ress);
			$saldoqty[$rDataBrg['kodebarang']] = $bars->saldoqty;

			if ($nod == '1') {
				$nod = 2;
				$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $param['notransaksiGdng'] . '\'';
				$res = mysql_query($str);

				if (mysql_num_rows($res) == 1) {
					$statAda = 1;
				}

				$str = 'select * from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $param['notransaksiGdng'] . '\' and post=1';

				if (0 < mysql_num_rows(mysql_query($str))) {
					$status = 3;
				}

				if ($param['pemilikBrg'] == '') {
					$status = 4;
				}

				if ($status == 4) {
					echo ' Gagal: Company code of the Recipient is not defined';
					exit(0);
				}

				if ($status == 3) {
					echo ' Gagal: Data has been posted';
					exit(0);
				}

				$sKdPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($param['gdngTujuan'], 0, 4) . '\'';

				exit(mysql_error($sKdPt));
				($qKdPt = mysql_query($sKdPt)) || true;
				$rKdpt = mysql_fetch_assoc($qKdPt);

				if ($rKdpt['induk'] == '') {
					exit('Kode PT Penerima Kosong');
				}

				if ($statAda == 0) {
					$strHead = 'insert into ' . $dbname . '.log_transaksiht (' . "\r\n" . '                          `tipetransaksi`,`notransaksi`,' . "\r\n" . '                          `tanggal`,`kodept`,`untukpt`,' . "\r\n" . '                          `gudangx`,`keterangan`,' . "\r\n" . '                          `kodegudang`,`user`,' . "\r\n" . '                          `post`)' . "\r\n" . '                          values(\'7\',\'' . $param['notransaksiGdng'] . '\',\'' . tanggaldgnbar($param['tanggal']) . '\',\'' . $param['pemilikBrg'] . '\',\'' . $rKdpt['induk'] . '\',' . "\r\n" . '                          \'' . $param['gdngTujuan'] . '\',\'' . $catatan . '\',' . "\r\n" . '                          \'' . $param['gdngPengirim'] . '\',' . $_SESSION['standard']['userid'] . ',\'0\')';

					if (!mysql_query($strHead)) {
						exit('error:db bermasalah 1 :' . mysql_error($conn) . ' ' . $strHead);
					}

					$whrDetBrg = 'nopo=\'' . $param['nokonsemen'] . '\' and kodebarang=\'' . $rDataBrg['kodebarang'] . '\'';
					$sCek = 'select distinct nopo from ' . $dbname . '.log_transaksidt where ' . $whrDetBrg . '';

					#exit(mysql_error($conn));
					($qCek = mysql_query($sCek)) || true;
					$rCek = mysql_fetch_assoc($qCek);

					if ($rCek['nopo'] != '') {
						$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
						$lbhSatuBrg += $rDataBrg['kodebarang'];
						continue;
					}

					if (($saldoqty[$rDataBrg['kodebarang']] + $qtynotpostedin[$rDataBrg['kodebarang']]) < ($qty[$rDataBrg['kodebarang']] + $qtynotposted[$rDataBrg['kodebarang']])) {
						$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
						continue;
					}

					$strDet .= '(\'' . $param['notransaksiGdng'] . '\',\'' . $rDataBrg['kodebarang'] . '\',\'' . $optSatBrg[$rDataBrg['kodebarang']] . '\',' . $qty[$rDataBrg['kodebarang']] . ',' . $jumlahlalu[$rDataBrg['kodebarang']] . ',\'' . $_SESSION['standard']['userid'] . '\',\'' . $param['nokonsemen'] . '\')';
					$awal = 1;
				}
				else {
					$whrDetBrg = 'nopo=\'' . $param['nokonsemen'] . '\' and kodebarang=\'' . $rDataBrg['kodebarang'] . '\'';
					$sCek = 'select distinct nopo from ' . $dbname . '.log_transaksidt where ' . $whrDetBrg . '';

					#exit(mysql_error($conn));
					($qCek = mysql_query($sCek)) || true;
					$rCek = mysql_fetch_assoc($qCek);

					if ($rCek['nopo'] != '') {
						$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
						$lbhSatuBrg += $rDataBrg['kodebarang'];
						continue;
					}

					if (($saldoqty[$rDataBrg['kodebarang']] + $qtynotpostedin[$rDataBrg['kodebarang']]) < ($qty[$rDataBrg['kodebarang']] + $qtynotposted[$rDataBrg['kodebarang']])) {
						$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
						continue;
					}

					$strDet .= '(\'' . $param['notransaksiGdng'] . '\',\'' . $rDataBrg['kodebarang'] . '\',\'' . $optSatBrg[$rDataBrg['kodebarang']] . '\',' . $qty[$rDataBrg['kodebarang']] . ',' . $jumlahlalu[$rDataBrg['kodebarang']] . ',\'' . $_SESSION['standard']['userid'] . '\',\'' . $param['nokonsemen'] . '\')';
					$awal = 1;
					$isiDetailAja += 1;
				}
			}
			else {
				$whrDetBrg = 'nopo=\'' . $param['nokonsemen'] . '\' and kodebarang=\'' . $rDataBrg['kodebarang'] . '\'';
				$sCek = 'select distinct nopo from ' . $dbname . '.log_transaksidt where ' . $whrDetBrg . '';

				#exit(mysql_error($conn));
				($qCek = mysql_query($sCek)) || true;
				$rCek = mysql_fetch_assoc($qCek);

				if ($rCek['nopo'] != '') {
					$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
					$lbhSatuBrg += $rDataBrg['kodebarang'];
					continue;
				}

				if (($saldoqty[$rDataBrg['kodebarang']] + $qtynotpostedin[$rDataBrg['kodebarang']]) < ($qty[$rDataBrg['kodebarang']] + $qtynotposted[$rDataBrg['kodebarang']])) {
					$errBrg[$rDataBrg['kodebarang']] = $rDataBrg['kodebarang'];
					continue;
				}

				if ($awal == 0) {
					$strDet .= ' (\'' . $param['notransaksiGdng'] . '\',\'' . $rDataBrg['kodebarang'] . '\',\'' . $optSatBrg[$rDataBrg['kodebarang']] . '\',' . $qty[$rDataBrg['kodebarang']] . ',' . $jumlahlalu[$rDataBrg['kodebarang']] . ',\'' . $_SESSION['standard']['userid'] . '\',\'' . $param['nokonsemen'] . '\')';
					$awal = 1;
					$isiDetailAja += 1;
				}
				else {
					$strDet .= ',(\'' . $param['notransaksiGdng'] . '\',\'' . $rDataBrg['kodebarang'] . '\',\'' . $optSatBrg[$rDataBrg['kodebarang']] . '\',' . $qty[$rDataBrg['kodebarang']] . ',' . $jumlahlalu[$rDataBrg['kodebarang']] . ',\'' . $_SESSION['standard']['userid'] . '\',\'' . $param['nokonsemen'] . '\')';
					$isiDetailAja += 1;
				}
			}
		}

		if ($isiDetailAja != 0) {
			$strDet .= ';';

			if (!mysql_query($strDet)) {
				exit('error:detail kosong :' . mysql_error($conn) . ' ' . $strDet);
			}

			$strj = 'select a.* from ' . $dbname . '.log_transaksidt a where a.notransaksi=\'' . $param['notransaksiGdng'] . '\'';
			$resj = mysql_query($strj);
			$no = 0;

			while ($barj = mysql_fetch_object($resj)) {
				$no += 1;
				$namabarangk = '';
				$strk = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $barj->kodebarang . '\'';
				$resk = mysql_query($strk);
				$bark = mysql_fetch_object($resk);
				$namabarangk = $bark->namabarang;
				$bg = 'class=rowcontent';
				$tab .= '<tr ' . $bg . ' >' . "\r\n" . '                                    <td>' . $no . '</td>' . "\r\n" . '                                        <td>' . $barj->kodebarang . '</td>' . "\r\n" . '                                        <td>' . $namabarangk . '</td>' . "\r\n" . '                                        <td>' . $barj->satuan . '</td>' . "\r\n" . '                                        <td align=right>' . number_format($barj->jumlah, 2, '.', ',') . '</td>' . "\r\n" . '                                        <td>' . "\r\n" . '                                        &nbsp <img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delMutasi(\'' . $param['notransaksiGdng'] . '\',\'' . $barj->kodebarang . '\');">' . "\r\n" . '                                        </td>' . "\r\n" . '                                   </tr>';
			}
		}
		else {
			$sdel = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $param['notransaksiGdng'] . '\'';

			if (!mysql_query($sdel)) {
				exit('error: db bermasalah pas delete' . mysql_error($conn) . '__' . $sdel);
			}

			$tab .= '<tr ' . $bg . '>' . "\r\n" . '                                <td colspan=6>Data Kosong ' . "\r\n" . '                                    </td>' . "\r\n" . '                               </tr>';
		}

		$isidet = count($errBrg);

		if ($isidet != 0) {
			foreach ($errBrg as $lstBrg) {
				$no += 1;
				$namabarangk = '';
				$strk = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $lstBrg . '\'';
				$resk = mysql_query($strk);
				$bark = mysql_fetch_object($resk);
				$namabarangk = $bark->namabarang;
				$bg = 'bgcolor=red';

				if (0 < $lbhSatuBrg[$lstBrg]) {
					$bg = 'bgcolor=orange';
				}

				$tab .= '<tr ' . $bg . '>' . "\r\n" . '                                <td>' . $no . '</td>' . "\r\n" . '                                    <td>' . $lstBrg . '</td>' . "\r\n" . '                                    <td>' . $namabarangk . '</td>' . "\r\n" . '                                    <td>' . $optSatBrg[$lstBrg] . '</td>' . "\r\n" . '                                    <td align=right>0</td>' . "\r\n" . '                                    <td>' . "\r\n" . '                                    &nbsp; ' . "\r\n" . '                                    </td>' . "\r\n" . '                               </tr>';
			}
		}

		$tab .= '<tr class=rowcontent><td colspan=6>' . "\r\n" . '                   *row berwarna merah dikarenakan saldo tidak mencukupi dan tidak tersimpan ke dalam database<br />' . "\r\n" . '                   *row berwarna orange dikarenakan ada dua barang yang sama dalam satu konosemen, silakan dimutasi ulang dgn notransaksi yang berbeda<br />' . "\r\n" . '                      </td></tr>';
		echo $tab;
		break;
	}
}

?>
