<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

if (isTransactionPeriod()) {
	$param = $_POST;

	switch ($param['method']) {
	case 'ngemail':
		$tgl = tanggaldgnbar($param['tanggal']);
		$whrht = 'notransaksi=\'' . $param['notransaksi'] . '\'';
		$optCekLogHt = makeOption($dbname, 'log_transaksiht', 'nopo,notransaksi', $whrht);

		if ($optCekLogHt[$param['nopo']] != '') {
			$semial = 'select distinct a.kodebarang,b.nopp,b.dibuat from ' . $dbname . '.log_prapodt a' . "\r\n" . '                             left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp' . "\r\n" . '                             where a.nopp in (select distinct nopp from ' . $dbname . '.log_podt where nopo=\'' . $param['nopo'] . '\') ';

			#exit(mysql_error($conn));
			($qemail = mysql_query($semial)) || true;

			while ($remail = mysql_fetch_assoc($qemail)) {
				$whrd = 'karyawanid=\'' . $remail['dibuat'] . '\'';
				$arrEmail = makeOption($dbname, 'datakaryawan', 'karyawanid,email', $whrd);
				$dafEmail[$remail['dibuat'] . $remail['nopp']] = $arrEmail[$remail['dibuat']];
				$dafBrg[$remail['dibuat'] . $remail['nopp'] . $remail['kodebarang']] = $remail['kodebarang'];
				$dtPembuat[$remail['dibuat']] = $remail['dibuat'];
				$dtPp[$remail['nopp']] = $remail['nopp'];
				$dtBrg[$remail['kodebarang']] = $remail['kodebarang'];
			}

			$adaAsset = 0;
			$sDafBrg = 'select distinct kodebarang,jumlah from ' . $dbname . '.log_transaksidt where notransaksi=\'' . $param['notransaksi'] . '\'';

			#exit(mysql_error($conn));
			($qDafBrg = mysql_query($sDafBrg)) || true;

			while ($rDafBrg = mysql_fetch_assoc($qDafBrg)) {
				$lstJmlh[$rDafBrg['kodebarang']] = $rDafBrg['jumlah'];
				$brgAsset = substr($rDafBrg['kodebarang'], 0, 1);

				if ($brgAsset == '9') {
					$adaAsset += 1;
				}
			}

			if ($adaAsset != 0) {
				$sAct = 'select distinct nilai from ' . $dbname . '.setup_parameterappl where kodeparameter=\'ACT\'';

				#exit(mysql_error());
				($qAct = mysql_query($sAct)) || true;
				$rAct = mysql_fetch_assoc($qAct);
			}

			$awal = 0;
			$tab .= 'Di bawah ini No. PP sudah diterima di gudang dengan No. PO : ' . $param['nopo'] . ', pada tanggal : ' . $param['tanggal'];
			$tab .= '<table cellspacing=1 cellpadding=1 border=1>';
			$tab .= '<thead><tr  bgcolor=#DEDEDE align=center>';
			$tab .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
			$tab .= '<td>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['penerimaanbarang'] . '</td>';
			$tab .= '<td>' . $_SESSION['lang']['dibuat'] . '</td>';
			$tab .= '</tr></thead><tbody>';
			$cekEmail = count($dafEmail);
			$rowBrg = count($dafBrg);

			if ($cekEmail != 0) {
				foreach ($dtPembuat as $lstPembuat) {
					foreach ($dtPp as $LstPP) {
						$tmplBarang = false;

						foreach ($dtBrg as $LstBrg) {
							if ($dergt != $LstPP) {
								$subject = $_SESSION['lang']['penerimaanbarang'] . ' ' . $LstPP;
								$dergt = $LstPP;
							}

							if ($tmplBarang == false) {
								$tab .= '<tr>';
								$tab .= '<td>' . $LstPP . '</td>';
								$tab .= '<td>' . date('d-m-Y') . '</td>';
								$tab .= '<td>' . $optNmKar[$lstPembuat] . '</td>';
								$tab .= '</tr>';

								if ($awal == 0) {
									$dert = '\'' . $dafEmail[$lstPembuat . $LstPP] . '\'';
								}
								else {
									$dert .= ',\'' . $dafEmail[$lstPembuat . $LstPP] . '\'';
								}

								if ($adaAsset != 0) {
									$dert .= ',' . $rAct['nilai'];
								}

								$fyiass = '';
								$brgAssetis = substr($LstBrg, 0, 1);

								if ($brgAssetis == '9') {
									$fyiass = '[<b>Kelompok Barang Asset</b>]';
								}

								$tab .= '<tr bgcolor=#DEDEDE align=center>';
								$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
								$tab .= '<td>' . $_SESSION['lang']['namabarang'] . ' ' . $fyiass . '</td>';
								$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
								$tab .= '</tr>';
								$tab .= '<tr class=rowcontent>';

								if ($dafBrg[$lstPembuat . $LstPP . $LstBrg] != '') {
									$tab .= '<td>' . $dafBrg[$lstPembuat . $LstPP . $LstBrg] . '</td>';
									$tab .= '<td>' . $optNmBrg[$dafBrg[$lstPembuat . $LstPP . $LstBrg]] . '</td>';
									$tab .= '<td align=right>' . number_format($lstJmlh[$dafBrg[$lstPembuat . $LstPP . $LstBrg]], 0) . '</td>';
									$tab .= '</tr>';
								}

								$tmplBarang = true;
								$brsdt = 1;
							}
							else if ($rowBrg != $brsdt) {
								if ($dafBrg[$lstPembuat . $LstPP . $LstBrg] != '') {
									$tab .= '<td>' . $dafBrg[$lstPembuat . $LstPP . $LstBrg] . '</td>';
									$tab .= '<td>' . $optNmBrg[$dafBrg[$lstPembuat . $LstPP . $LstBrg]] . '</td>';
									$tab .= '<td align=right>' . number_format($lstJmlh[$dafBrg[$lstPembuat . $LstPP . $LstBrg]], 0) . '</td>';
									$tab .= '</tr>';
								}

								$brsdt += 1;
							}
						}
					}

					$tab .= '</tbody></table>' . "\r\n" . '                         nb. Barang yang di tampilkan adalah keseluruhan dari nopp tetapi yang sudah di terima gudang adalah jumlah tidak sama dengan kosong';
				}

				kirimEmailWindows($dert, $subject, $tab);
			}
		}

		break;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
