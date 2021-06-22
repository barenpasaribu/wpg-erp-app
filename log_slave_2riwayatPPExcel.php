<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$totalSmaData = $dsetujui = $dtolak = $dmenungguKptsn = $blmDiajukan = $pros = 0;
$proses = $_GET['proses'];
$nopp = $_GET['nopp'];
$tglSdt = tanggalsystem($_GET['tglSdt']);
$statusPP = $_GET['statPP'];
$periode = $_GET['periode'];
$lokBeli = $_GET['lokBeli'];
$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';
$namapt = 'COMPANY NAME';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namapt = strtoupper($bar->namaorganisasi);
}

$stream .= '<table  cellspacing=\'1\' border=\'0\'>' . "\r\n" . '                                <tr><td colspan=10  align=center>' . strtoupper($_SESSION['lang']['riwayatPP']) . '</td></tr>' . "\r\n" . '                                <tr><td colspan=3  align=\'left\'>' . $_SESSION['lang']['user'] . ':' . $_SESSION['standard']['username'] . '</td></tr>' . "\r\n" . '                                <tr><td colspan=3  align=\'left\'>' . $_SESSION['lang']['tanggal'] . ':' . date('d-m-Y H:i:s') . '</td></tr></table>' . "\r\n" . '                                <table  cellspacing=\'1\' border=\'1\'>' . "\r\n" . '                                <tr>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jumlah'] . '</td> ' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>    ' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['keterangan'] . '</td>    ' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['status'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tgl_po'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['status'] . ' </td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['rapbNo'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                                </tr></table><table  cellspacing=\'1\' border=\'1\'>';

if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
}
else {
	$sortLokasi = 'and a.nopp like \'%' . $_SESSION['empl']['lokasitugas'] . '%\'';
}

if (($nopp == '') && ($tglSdt == '') && ($statusPP == '') && ($periode == '') && ($lokBeli == '')) {
	$tglSkrng = date('Y-m');
	$sql = 'select a.*,b.* FROM ' . $dbname . '.log_prapodt a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7)=\'' . $tglSkrng . '\' ' . $sortLokasi . ' order by a.nopp desc ';
}
else {
	if ($tglSdt != '') {
		$where = ' where b.tanggal=\'' . $tglSdt . '\'';
	}
	else {
		$where = ' where a.nopp!=\'\'';
	}

	if ($statusPP != '') {
		if ($statusPP == '3') {
			if ($tglSdt == '') {
				if ($periode == '') {
					exit('Error: Periode Tidak Boleh Kosong');
				}
				else {
					$where = 'where  a.create_po!=\'\'  and substr(b.tanggal,1,7) = \'' . $periode . '\' ';
				}
			}
		}
		else if ($statusPP == '4') {
			if ($tglSdt == '') {
				if ($periode == '') {
					exit('Error: Periode Tidak Boleh Kosong');
				}
				else {
					$where = 'where  a.create_po=\'\'  and substr(b.tanggal,1,7) = \'' . $periode . '\'';
				}
			}
		}
		else {
			if (($statusPP == '1') || ($statusPP != '2')) {
				if ($tglSdt == '') {
					if ($periode != '') {
						$where = ' where b.close=\'' . $statusPP . '\' and substr(b.tanggal,1,7) = \'' . $periode . '\'';
					}
					else {
						$where .= ' and b.close=\'' . $statusPP . '\'';
					}
				}
			}
			else if ($statusPP == '2') {
				if ($tglSdt == '') {
					if ($periode != '') {
						$where = ' where b.close=\'2\' and substr(b.tanggal,1,7) = \'' . $periode . '\'';
					}
					else {
						$where .= ' and b.close=\'2\'';
					}
				}
			}
		}
	}
	else if ($periode != '') {
		if ($tglSdt == '') {
			$where = ' where substr(b.tanggal,1,7)=\'' . $periode . '\'';
		}
	}

	if ($lokBeli != '') {
		$where .= ' and lokalpusat= \'' . $lokBeli . '\'';
	}

	if ($nopp != '') {
		$where .= ' and b.nopp like \'%' . $nopp . '%\'';
	}

	$sql = 'select a.*,b.* FROM ' . $dbname . '.log_prapodt a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp  ' . $where . ' ' . $sortLokasi . ' order by a.nopp desc ';
}

#exit(mysql_error());
($query = mysql_query($sql)) || true;
$row = mysql_num_rows($query);

if (0 < $row) {
	#exit(mysql_error());
	($query2 = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($query2)) {
		$no += 1;
		$dtolak = 0;

		if ($res['close'] == '2') {
			if (!is_null($res['tglp5'])) {
				$tgl = tanggalnormal($res['tglp5']);
			}
			else if (!is_null($res['tglp4'])) {
				$tgl = tanggalnormal($res['tglp4']);
			}
			else if (!is_null($res['tglp3'])) {
				$tgl = tanggalnormal($res['tglp3']);
			}
			else if (!is_null($res['tglp2'])) {
				$tgl = tanggalnormal($res['tglp2']);
			}
			else if (!is_null($res['tglp1'])) {
				$tgl = tanggalnormal($res['tglp1']);
			}

			if ($res['status'] == '3') {
				$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
				$npo = '';
				$dtolak += 1;
			}
			else if ($res['status'] == '0') {
				$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				$npo = 'Purchasing Process';
				$pros += 1;
			}
		}
		else if ($res['close'] == '1') {
			if (!is_null($res['hasilpersetujuan5'])) {
				$tgl = tanggalnormal($res['tglp5']);

				if ($res['hasilpersetujuan5'] == '1') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
				else if ($res['hasilpersetujuan5'] == '0') {
					$statPp = $_SESSION['lang']['wait_approval'];
					$npo = '';
					$dmenungguKptsn += 1;
				}
				else if ($res['hasilpersetujuan5'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$dtolak = 1;
					$npo = '';
				}
			}
			else if (!is_null($res['hasilpersetujuan4'])) {
				$tgl = tanggalnormal($res['tglp4']);

				if ($res['hasilpersetujuan4'] == '1') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
				else if ($res['hasilpersetujuan4'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$dtolak = 1;
					$npo = '';
				}
				else if ($res['hasilpersetujuan4'] == '0') {
					$statPp = $_SESSION['lang']['wait_approval'];
					$npo = '';
					$dmenungguKptsn += 1;
				}
			}
			else if (!is_null($rTgl['hasilpersetujuan3'])) {
				$tgl = tanggalnormal($res['tglp3']);

				if ($res['hasilpersetujuan3'] == '1') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
				else if ($res['hasilpersetujuan3'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$dtolak = 1;
				}
				else if ($res['hasilpersetujuan3'] == '0') {
					$statPp = $_SESSION['lang']['wait_approval'];
					$npo = '';
					$dmenungguKptsn += 1;
				}
			}
			else if (!is_null($res['hasilpersetujuan2'])) {
				$tgl = tanggalnormal($res['tglp2']);

				if ($res['hasilpersetujuan2'] == '1') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
				else if ($res['hasilpersetujuan2'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$dtolak = 1;
					$npo = '';
				}
				else if ($res['hasilpersetujuan2'] == '0') {
					$statPp = $_SESSION['lang']['wait_approval'];
					$npo = '';
					$dmenungguKptsn += 1;
				}
			}
			else if (!is_null($res['hasilpersetujuan1'])) {
				$tgl = tanggalnormal($res['tglp1']);

				if ($res['hasilpersetujuan1'] == '1') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
				else if ($res['hasilpersetujuan1'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$dtolak = 1;
					$npo = '';
				}
				else if ($res['hasilpersetujuan1'] == '0') {
					$statPp = $_SESSION['lang']['wait_approval'];
					$dmenungguKptsn += 1;
					$npo = '';
				}
			}
		}
		else {
			if (($res['close'] == 0) || ($res['close'] == '')) {
				$statPp = $_SESSION['lang']['belumdiajukan'];
				$npo = '';
				$blmDiajukan += 1;
			}
		}

		$statPo = '';
		$sBrg = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

		#exit(mysql_error());
		($qBrg = mysql_query($sBrg)) || true;
		$rBrg = mysql_fetch_assoc($qBrg);
		$sDet = 'select nopo from ' . $dbname . '.log_podt  where nopp=\'' . $res['nopp'] . '\' and kodebarang=\'' . $res['kodebarang'] . '\'';

		#exit(mysql_error());
		($qDet = mysql_query($sDet)) || true;
		$rDet = mysql_fetch_assoc($qDet);
		$sPo2 = 'select * from ' . $dbname . '.log_poht  where nopo=\'' . $rDet['nopo'] . '\'';

		#exit(mysql_error());
		($qPo2 = mysql_query($sPo2)) || true;
		$rPo2 = mysql_fetch_assoc($qPo2);
		$tglPO = '';

		if ($rDet['nopo'] != '') {
			if ($rPo2['tanggal'] != -0) {
				$tglPO = tanggalnormal($rPo2['tanggal']);
			}

			$sSup = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $rPo2['kodesupplier'] . '\'';

			#exit(mysql_error());
			($qSup = mysql_query($sSup)) || true;
			$rSup = mysql_fetch_assoc($qSup);
			$sRapb = 'select notransaksi,tanggal from ' . $dbname . '.log_transaksi_vw ' . "\r\n" . '                                                    where nopo=\'' . $rPo2['nopo'] . '\' and kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qRapb = mysql_query($sRapb)) || true;
			$rRapb = mysql_fetch_assoc($qRapb);

			if ($rPo2['statuspo'] == '3') {
				$tglR = '';
				$statPo = $_SESSION['lang']['disetujui'] . ',' . $tgl;

				if ($rRapb['notransaksi'] != '') {
					$tglR = tanggalnormal($rRapb['tanggal']);
					$statPo = 'Brg Sdh Di gudang ,' . $tglR;
				}
			}
			else if ($rPo2['statuspo'] == '2') {
				$accept = 0;
				$i = 1;

				while ($i < 4) {
					if ($rPo2['hasilpersetujuan' . $i] == '2') {
						$accept = 2;
						$tgl = tanggalnormal($rPo2['tglp' . $i]);
						break;
					}

					if ($rPo2['hasilpersetujuan' . $i] == '1') {
						$accept = 1;
					}

					++$i;
				}

				if ($accept == '2') {
					$statPo = $_SESSION['lang']['ditolak'] . ',' . $tgl;
				}
				else if ($accept == '1') {
					$statPo = $_SESSION['lang']['disetujui'] . ',' . $tgl;
				}
			}
			else if ($rPo2['statuspo'] == '1') {
				$i = 1;

				while ($i < 4) {
					if ($rPo2['tglp' . $i] == '') {
						$j = $i - 1;

						if ($j != 0) {
							$tgl = tanggalnormal($rPo2['tglp' . $j]);

							if ($rPo2['hasilpersetujuan' . $j] == 2) {
								$statPo = 'Persetujuan' . $j . ', ' . $_SESSION['lang']['ditolak'] . $tgl;
							}
							else if ($rPo2['hasilpersetujuan' . $j] == 1) {
								$statPo = 'Persetujuan' . $j . ', ' . $_SESSION['lang']['disetujui'] . $tgl;
							}
						}

						break;
					}

					++$i;
				}
			}
			else if ($rPo2['statuspo'] == '0') {
				$statPo = 'Approval Process';
			}
		}

		if ($rDet['nopo'] != '') {
			$res['lokalpusat'] == 0 ? $npo = $rPo2['nopo'] : $npo = $rPo2['nopo'];
			$dsetujui += 1;
		}
		else {
			$npo = '';
			$tglPO = '';
			$statPo = '';
			$rSup['namasupplier'] = '';
			$rRapb['notransaksi'] = '';
			$rRapb['tanggal'] = '0000-00-00';
		}

		if (($res['hasilpersetujuan1'] == 3) || ($res['hasilpersetujuan2'] == 3) || ($res['hasilpersetujuan3'] == 3) || ($res['hasilpersetujuan4'] == 3) || ($res['hasilpersetujuan5'] == 3) || ($res['status'] == 3)) {
			$npo = '';
		}

		$stream .= '<tr class=rowcontent>' . "\r\n" . '                                                <td>' . $no . '</td>' . "\r\n" . '                                                <td>' . $res['nopp'] . '</td>' . "\r\n" . '                                                <td>' . tanggalnormal($res['tanggal']) . '</td>' . "\r\n" . '                                                <td>' . $res['kodebarang'] . '</td>' . "\r\n" . '                                                <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                                                <td>' . $rBrg['satuan'] . '</td>' . "\r\n" . '                                                <td align=right>' . $res['jumlah'] . '</td>' . "\r\n" . '                                                <td align=right>' . $res['realisasi'] . '</td>' . "\r\n" . '                                                <td align=left>' . $res['keterangan'] . '</td>' . "\r\n" . '                                                ';
		$stream .= '<td>' . $statPp . '</td>';
		$stream .= "\r\n" . '                                                <td>' . $npo . '</td>' . "\r\n" . '                                                <td>' . $tglPO . '</td>';
		$stream .= '<td>' . $statPo . '</td>';
		$stream .= '<td>' . $rSup['namasupplier'] . '</td>' . "\r\n" . '                                                <td>' . $rRapb['notransaksi'] . '</td>';

		if ($rRapb['tanggal'] != -0) {
			$stream .= '<td>' . tanggalnormal($rRapb['tanggal']) . '</td></tr>';
		}
		else {
			$stream .= '<td></td></tr>';
		}
	}

	$stream .= '</table>';
}
else {
	$stream .= '<tr class=rowcontent><td colspan=16 align=center>Not Found</td></tr></table>';
}

$stream .= '<tr class=rowcontent><td colspan=16 align=left><table cellpadding=1 cellspacing=1 border=1 class=sortable>';
$stream .= '<thead><tr class=rowheader>';
$stream .= '<td  bgcolor=#DEDEDE align=center>Purchased</td>';
$stream .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['ditolak'] . '</td>';
$stream .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['wait_approval'] . '</td>';
$stream .= '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['belumdiajukan'] . '</td>';
$stream .= '<td bgcolor=#DEDEDE align=center>Purchasing Process</td>';
$stream .= '<td bgcolor=#DEDEDE align=center>Total</td>';
$stream .= '</thead><tbody>';
$totalSmaData = $dtolak + $dmenungguKptsn + $blmDiajukan + ($pros - $dsetujui) + $dsetujui;
$stream .= '<tr class=rowcontent>';
$stream .= '<td align=right>' . $dsetujui . '</td>';
$stream .= '<td align=right>' . $dtolak . '</td>';
$stream .= '<td align=right>' . $dmenungguKptsn . '</td>';
$stream .= '<td align=right>' . $blmDiajukan . '</td>';
$stream .= '<td align=right>' . ($pros - $dsetujui) . '</td>';
$stream .= '<td align=right>' . $totalSmaData . '</td>';
$stream .= '</tr>';
$stream .= '</tbody></table></tr>';
$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'ReportRiwayatPermintaanBarang' . date('YmdHis');

if (0 < strlen($stream)) {
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
}

?>
