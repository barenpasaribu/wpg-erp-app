<?php


function writeFile($path, $pemisah)
{
	global $jenisdata;
	$dir = $path;
	$ext = split('[.]', basename($_FILES['filex']['name']));
	$ext = $ext[count($ext) - 1];
	$ext = strtolower($ext);

	if ($ext == 'csv') {
		$path = $dir . '/' . date('ymd') . '.' . $ext;
		@unlink($path);

		try {
			if (move_uploaded_file($_FILES['filex']['tmp_name'], $path)) {
				$x = readCSV($path, $pemisah);
				simpanData($x, $jenisdata);

				echo '<script>alert("Error Writing File' . addslashes($e->getMessage()) . '");</script>';
			}
		}
		catch (Exception $e) {
			echo '<script>alert("Error Writing File' . addslashes($e->getMessage()) . '");</script>';
		}
	}
	else {
		echo '<script>alert(\'Filetype not support\');</script>';
	}

}

function simpanData($x, $jenisdata)
{
	global $dbname;
	global $conn;
	global $pemisah;
	global $optSatKeg;
	global $optSatBrg;
	$jlhbaris = count($x) - 1;

	foreach ($x[0] as $val) {
		$header[] = trim($val);
	}

	switch ($jenisdata) {
	case 'SDM':
		$str = 'select kodekegiatan from ' . $dbname . '.setup_kegiatan order by kodekegiatan asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$noakun[] = $bar->kodekegiatan;
		}

		foreach ($header as $ki => $val) {
			if ($val == 'tahunbudget') {
				$index1 = $ki;
			}

			if ($val == 'kodeblok') {
				$index2 = $ki;
			}

			if ($val == 'tipebudget') {
				$index3 = $ki;
			}

			if ($val == 'kodebudget') {
				$index4 = $ki;
			}

			if ($val == 'kodekegiatan') {
				$index5 = $ki;
			}

			if ($val == 'volumepekerjaansetahun') {
				$index6 = $ki;
			}

			if ($val == 'rupiahsetahun') {
				$index7 = $ki;
			}

			if ($val == 'rotasi') {
				$index8 = $ki;
			}

			if ($val == 'jumlahhk') {
				$index9 = $ki;
			}

			if ($val == 'satuan') {
				$index10 = $ki;
			}
		}

		if (count($x[0]) != 11) {
			exit('Error: Form not valid');
		}

		$str = 'select kodeblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and kodeblok like \'' . substr($x[1][$index2], 0, 4) . '%\' and closed=1' . "\r\n" . '                    order by kodeblok asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdblok[] = $bar->kodeblok;
		}

		$str = 'select distinct * from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                    tahunbudget=\'' . $x[1][$index1] . '\' and kodeorg like \'' . substr($x[1][$index2], 0, 4) . '%\' and tutup=1';

		#exit(mysql_error($conn));
		($res = mysql_query($str)) || true;
		$barcek = mysql_num_rows($res);

		if (0 < $barcek) {
			exit('error: budget data for this ' . $x[1][$index1] . ' year has been closed ');
		}

		if (count($kdblok) == 0) {
			exit('error: setup block budget has not been processed or closed ');
		}

		$thnBerjln = date('Y');

		foreach ($x as $key => $arr) {
			if ($key == 0) {
				continue;
			}

			foreach ($arr as $ids => $rinc) {
				if (($header[$ids] == 'tahunbudget') && (strlen($rinc) != 4)) {
					exit('Error: some data on budget year not valid (line ' . $key . ') ' . $rinc);
				}

				if (($header[$ids] == 'tahunbudget') && ($rinc < $thnBerjln)) {
					exit('Error: some data on budget year the format not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodeblok') && (strlen($rinc) != 10)) {
					exit('Error: some data on code block not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodekegiatan') && (strlen($rinc) != 9)) {
					exit('Error: some data on activity code not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'tipebudget') && ($rinc != 'ESTATE')) {
					exit('Error: some data on budget type not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'rupiahsetahun') && (intval($rinc) == '0')) {
					exit('Error: some data on rupiah a year not valid (line ' . $key . ')');
				}

				if ($header[$ids] == 'kodekegiatan') {
					$akunbermasalah[$rinc] = $rinc;

					foreach ($noakun as $bb => $cc) {
						if ($cc == $rinc) {
							unset($akunbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodeblok') {
					$blokbermasalah[$rinc] = $rinc;

					foreach ($kdblok as $bb => $cc) {
						if ($cc == $rinc) {
							unset($blokbermasalah[$rinc]);
						}
					}
				}
			}
		}

		if (0 < count($akunbermasalah)) {
			echo 'The following activity code were not defined:<br>';
			print_r($akunbermasalah);
			exit();
		}

		if (0 < count($blokbermasalah)) {
			echo 'The following block code were not defined:<br>';
			print_r($blokbermasalah);
			exit();
		}

		$jmlhRow = count($x);
		$aerto = 1;

		while ($aerto < $jmlhRow) {
			$str = 'delete from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                        kodeorg=\'' . $x[$aerto][$index2] . '\' and tahunbudget=\'' . $x[$aerto][$index1] . '\' and kegiatan=\'' . $x[$aerto][$index5] . '\'' . "\r\n" . '                        and kodebudget=\'' . trim($x[$aerto][$index4]) . '\'';

			if (mysql_query($str)) {
				$detData = 'insert into ' . $dbname . '.bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,' . "\r\n" . '                          `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`rotasi`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ';
				$rupiah[$aerto][$index7] = str_replace(',', '', trim($x[$aerto][$index7]));
				$detData .= '(\'' . trim($x[$aerto][$index1]) . '\',\'' . trim($x[$aerto][$index2]) . '\',\'' . trim($x[$aerto][$index3]) . '\',\'' . trim($x[$aerto][$index4]) . '\',\'' . trim($x[$aerto][$index5]) . '\',' . "\r\n" . '                                \'' . substr(trim($x[$aerto][$index5]), 0, 7) . '\',\'' . trim($x[$aerto][$index6]) . '\',\'' . $optSatKeg[trim($x[$aerto][$index5])] . '\',\'' . $rupiah[$aerto][$index7] . '\',' . "\r\n" . '                                \'' . trim($x[$aerto][$index8]) . '\',\'' . trim($x[$aerto][$index9]) . '\',\'HK\',\'' . $_SESSION['standard']['userid'] . '\',\'Data di upload oleh ' . $_SESSION['standard']['username'] . '\')';

				if (!mysql_query($detData)) {
					exit('error:' . "\n" . $detData . '__' . mysql_error());
				}
				else {
					echo '';
				}
			}
			else {
				exit('error:' . "\n" . $str . '__' . mysql_error());
			}

			++$aerto;
		}

		break;

	case 'MATANDTOOL':
		$str = 'select kodekegiatan from ' . $dbname . '.setup_kegiatan order by kodekegiatan asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$noakun[] = $bar->kodekegiatan;
		}

		foreach ($header as $ki => $val) {
			if ($val == 'tahunbudget') {
				$index1 = $ki;
			}

			if ($val == 'kodeblok') {
				$index2 = $ki;
			}

			if ($val == 'tipebudget') {
				$index3 = $ki;
			}

			if ($val == 'kodebudget') {
				$index4 = $ki;
			}

			if ($val == 'kodekegiatan') {
				$index5 = $ki;
			}

			if ($val == 'volumepekerjaansetahun') {
				$index6 = $ki;
			}

			if ($val == 'rupiahsetahun') {
				$index7 = $ki;
			}

			if ($val == 'rotasi') {
				$index8 = $ki;
			}

			if ($val == 'kodebarang') {
				$index9 = $ki;
			}

			if ($val == 'jumlahbrg') {
				$index11 = $ki;
			}

			if ($val == 'satuanbrg') {
				$index12 = $ki;
			}
		}

		if (count($x[0]) != 13) {
			exit('Error: Form not valid');
		}

		$str = 'select kodeblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and kodeblok like \'' . substr($x[1][$index2], 0, 4) . '%\' and closed=1' . "\r\n" . '                    order by kodeblok asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdblok[] = $bar->kodeblok;
		}

		$str = 'select kodebarang from ' . $dbname . '.log_5masterbarang ' . "\r\n" . '                    where inactive=0 order by kodebarang asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdbarang[] = $bar->kodebarang;
		}

		$str = 'select kodebudget from ' . $dbname . '.bgt_kode ' . "\r\n" . '                    where left(kodebudget,1)=\'M\' order by kodebudget asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdbudget[] = $bar->kodebudget;
		}

		if (count($kdblok) == 0) {
			exit('error: setup block budget has not been processed or closed ');
		}

		$str = 'select distinct * from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                    tahunbudget=\'' . $x[1][$index1] . '\' and kodeorg like \'' . substr($x[1][$index2], 0, 4) . '%\' and tutup=1';

		#exit(mysql_error($conn));
		($res = mysql_query($str)) || true;
		$barcek = mysql_num_rows($res);

		if (0 < $barcek) {
			exit('error: budget data for this ' . $x[1][$index1] . ' year has been closed ');
		}

		$thnBerjln = date('Y');

		foreach ($x as $key => $arr) {
			if ($key == 0) {
				continue;
			}

			foreach ($arr as $ids => $rinc) {
				if (($header[$ids] == 'tahunbudget') && (strlen($rinc) != 4)) {
					exit('Error: some data on budget year not valid (line ' . $key . ') ' . $rinc);
				}

				if (($header[$ids] == 'tahunbudget') && ($rinc < $thnBerjln)) {
					exit('Error: some data on budget year the format not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodeblok') && (strlen($rinc) != 10)) {
					exit('Error: some data on code block not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodekegiatan') && (strlen($rinc) != 9)) {
					exit('Error: some data on activity code not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'tipebudget') && ($rinc != 'ESTATE')) {
					exit('Error: some data on budget type not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'rupiahsetahun') && (intval($rinc) == '0')) {
					exit('Error: some data on rupiah a year not valid (line ' . $key . ')');
				}

				if ($header[$ids] == 'kodekegiatan') {
					$akunbermasalah[$rinc] = $rinc;

					foreach ($noakun as $bb => $cc) {
						if ($cc == $rinc) {
							unset($akunbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodeblok') {
					$blokbermasalah[$rinc] = $rinc;

					foreach ($kdblok as $bb => $cc) {
						if ($cc == $rinc) {
							unset($blokbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodebarang') {
					$kdbrgbermasalah[$rinc] = $rinc;

					foreach ($kdbarang as $bb => $cc) {
						if ($cc == $rinc) {
							unset($kdbrgbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodebudget') {
					$kdbgtbermasalah[$rinc] = $rinc;

					foreach ($kdbudget as $bb => $cc) {
						if ($cc == $rinc) {
							unset($kdbgtbermasalah[$rinc]);
						}
					}
				}
			}
		}

		if (0 < count($akunbermasalah)) {
			echo 'The following activity code were not defined:<br>';
			print_r($akunbermasalah);
			exit();
		}

		if (0 < count($blokbermasalah)) {
			echo 'The following block code were not defined:<br>';
			print_r($blokbermasalah);
			exit();
		}

		if (0 < count($kdbrgbermasalah)) {
			echo 'The following material code were not defined:<br>';
			print_r($kdbrgbermasalah);
			exit();
		}

		if (0 < count($kdbgtbermasalah)) {
			echo 'The following budget code were not defined:<br>';
			print_r($kdbgtbermasalah);
			exit();
		}

		$jmlhRow = count($x);
		$aerto = 1;

		while ($aerto < $jmlhRow) {
			$str = 'delete from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                        kodeorg=\'' . trim($x[$aerto][$index2]) . '\' and tahunbudget=\'' . trim($x[$aerto][$index1]) . '\' and kegiatan=\'' . trim($x[$aerto][$index5]) . '\'' . "\r\n" . '                        and kodebarang=\'' . trim($x[$aerto][$index9]) . '\' and kodebudget=\'' . trim($x[$aerto][$index4]) . '\'';

			if (mysql_query($str)) {
				$detData = 'insert into ' . $dbname . '.bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,' . "\r\n" . '                          `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`kodebarang`,`rotasi`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ';
				$rupiah[$aerto][$index7] = str_replace(',', '', trim($x[$aerto][$index7]));
				$detData .= '(\'' . trim($x[$aerto][$index1]) . '\',\'' . trim($x[$aerto][$index2]) . '\',\'' . trim($x[$aerto][$index3]) . '\',\'' . trim($x[$aerto][$index4]) . '\',\'' . trim($x[$aerto][$index5]) . '\',' . "\r\n" . '                                \'' . substr(trim($x[$aerto][$index5]), 0, 7) . '\',\'' . trim($x[$aerto][$index6]) . '\',\'' . $optSatKeg[trim($x[$aerto][$index5])] . '\',\'' . $rupiah[$aerto][$index7] . '\',' . "\r\n" . '                                \'' . trim($x[$aerto][$index9]) . '\',\'' . trim($x[$aerto][$index8]) . '\',\'' . trim($x[$aerto][$index11]) . '\',\'' . $optSatBrg[trim($x[$aerto][$index9])] . '\',' . "\r\n" . '                                \'' . $_SESSION['standard']['userid'] . '\',\'Data di upload oleh ' . $_SESSION['standard']['username'] . '\')';

				if (!mysql_query($detData)) {
					exit('error:' . "\n" . $detData . '__' . mysql_error());
				}
				else {
					echo '';
				}
			}
			else {
				exit('error:' . "\n" . $str . '__' . mysql_error());
			}

			++$aerto;
		}

		break;

	case 'VHC':
		$str = 'select kodekegiatan from ' . $dbname . '.setup_kegiatan order by kodekegiatan asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$noakun[] = $bar->kodekegiatan;
		}

		foreach ($header as $ki => $val) {
			if ($val == 'tahunbudget') {
				$index1 = $ki;
			}

			if ($val == 'kodeblok') {
				$index2 = $ki;
			}

			if ($val == 'tipebudget') {
				$index3 = $ki;
			}

			if ($val == 'kodebudget') {
				$index4 = $ki;
			}

			if ($val == 'kodekegiatan') {
				$index5 = $ki;
			}

			if ($val == 'volumepekerjaansetahun') {
				$index6 = $ki;
			}

			if ($val == 'rupiahsetahun') {
				$index7 = $ki;
			}

			if ($val == 'kodevhc') {
				$index9 = $ki;
			}

			if ($val == 'jumlahhmkmpertahun') {
				$index11 = $ki;
			}

			if ($val == 'satuan') {
				$index12 = $ki;
			}
		}

		if (count($x[0]) != 12) {
			exit('Error: Form not valid');
		}

		$str = 'select kodeblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and kodeblok like \'' . substr($x[1][$index2], 0, 4) . '%\' and closed=1' . "\r\n" . '                    order by kodeblok asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdblok[] = $bar->kodeblok;
		}

		$str = 'select kodevhc from ' . $dbname . '.bgt_vhc_jam ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and unitalokasi like \'' . substr($x[1][$index2], 0, 4) . '%\' order by kodevhc asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdvhc[] = $bar->kodevhc;
		}

		$str = 'select kodevhc from ' . $dbname . '.bgt_vhc_jam ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and unitalokasi like \'' . substr($x[1][$index2], 0, 4) . '%\' ' . "\r\n" . '                    and jumlahjam=0 order by kodevhc asc';
		$res = mysql_query($str);

		while ($bar2 = mysql_fetch_object($res)) {
			$kdvhcnol[] = $bar2->kodevhc;
		}

		$str = 'select kodevhc,jumlahjam from ' . $dbname . '.bgt_vhc_jam ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and unitalokasi like \'' . substr($x[1][$index2], 0, 4) . '%\' ' . "\r\n" . '                    and jumlahjam!=0 order by kodevhc asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$jmlVhc[$bar->kodevhc] = $bar->jumlahjam;
		}

		if (count($kdblok) == 0) {
			exit('error: setup block budget has not been processed or closed ');
		}

		$str = 'select distinct * from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                    tahunbudget=\'' . $x[1][$index1] . '\' and kodeorg like \'' . substr($x[1][$index2], 0, 4) . '%\' and tutup=1';

		#exit(mysql_error($conn));
		($res = mysql_query($str)) || true;
		$barcek = mysql_num_rows($res);

		if (0 < $barcek) {
			exit('error: budget data for this ' . $x[1][$index1] . ' year has been closed ');
		}

		$thnBerjln = date('Y');

		foreach ($x as $key => $arr) {
			if ($key == 0) {
				continue;
			}

			foreach ($arr as $ids => $rinc) {
				if (($header[$ids] == 'tahunbudget') && (strlen($rinc) != 4)) {
					exit('Error: some data on budget year  (' . $rinc . ') not valid (line ' . $key . ') ' . $rinc);
				}

				if (($header[$ids] == 'tahunbudget') && ($rinc < $thnBerjln)) {
					exit('Error: some data on budget year (' . $rinc . ') the format not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodeblok') && (strlen($rinc) != 10)) {
					exit('Error: some data on code block not  (' . $rinc . ') valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodekegiatan') && (strlen($rinc) != 9)) {
					exit('Error: some data on activity code (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'tipebudget') && ($rinc != 'ESTATE')) {
					exit('Error: some data on budget type (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodebudget') && ($rinc != 'VHC')) {
					exit('Error: some data on budget code (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodevhc') && ($rinc == '')) {
					exit('Error: some data on kode vhc not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'rupiahsetahun') && (intval($rinc) == '0')) {
					exit('Error: some data on rupiah a year not valid (line ' . $key . ')');
				}

				if ($header[$ids] == 'kodekegiatan') {
					$akunbermasalah[$rinc] = $rinc;

					foreach ($noakun as $bb => $cc) {
						if ($cc == $rinc) {
							unset($akunbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodeblok') {
					$blokbermasalah[$rinc] = $rinc;

					foreach ($kdblok as $bb => $cc) {
						if ($cc == $rinc) {
							unset($blokbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodevhc') {
					$kdvhcbermasalah[$rinc] = $rinc;

					foreach ($kdvhc as $bb => $cc) {
						if ($cc == $rinc) {
							unset($kdvhcbermasalah[$rinc]);
						}
					}

					if (count($kdvhcnol) != 0) {
						$kdvhcnolbermasalah[$rinc] = $rinc;

						foreach ($kdvhcnol as $bb => $cc) {
							if ($cc == $rinc) {
								unset($kdvhcnolbermasalah[$rinc]);
							}
						}
					}
				}
			}
		}

		if (0 < count($akunbermasalah)) {
			echo 'The following activity code were not defined:<br>';
			print_r($akunbermasalah);
			exit();
		}

		if (0 < count($kdvhcbermasalah)) {
			echo 'The following vhc code were not alocate to your site:<br>';
			print_r($kdvhcbermasalah);
			exit();
		}

		if (0 < count($kdvhcnolbermasalah)) {
			echo 'The following vhc code were alocate but 0 KM/HM or is not alocate to your site:<br>';
			print_r($kdvhcnolbermasalah);
			exit();
		}

		if (0 < count($blokbermasalah)) {
			echo 'The following block code were not defined:<br>';
			print_r($blokbermasalah);
			exit();
		}

		$jmlhRow = count($x);
		$aerto = 1;

		while ($aerto < $jmlhRow) {
			$jmlhBgtJam += $x[$aerto][$index9];

			if ($jmlhBgtJam[$x[$aerto][$index9]] < $jmlVhc[trim($x[$aerto][$index9])]) {
				$str = 'delete from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                            kodeorg=\'' . trim($x[$aerto][$index2]) . '\' and tahunbudget=\'' . trim($x[$aerto][$index1]) . '\' and kegiatan=\'' . trim($x[$aerto][$index5]) . '\'' . "\r\n" . '                            and kodevhc=\'' . trim($x[$aerto][$index9]) . '\' and kodebudget=\'VHC\'';

				if (mysql_query($str)) {
					$detData = 'insert into ' . $dbname . '.bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,' . "\r\n" . '                              `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`kodevhc`,`jumlah`,`satuanj`,`updateby`,`keterangan`) values ';
					$rupiah[$aerto][$index7] = str_replace(',', '', trim($x[$aerto][$index7]));
					$detData .= '(\'' . trim($x[$aerto][$index1]) . '\',\'' . trim($x[$aerto][$index2]) . '\',\'' . trim($x[$aerto][$index3]) . '\',\'' . trim($x[$aerto][$index4]) . '\',\'' . trim($x[$aerto][$index5]) . '\',' . "\r\n" . '                                    \'' . substr(trim($x[$aerto][$index5]), 0, 7) . '\',\'' . trim($x[$aerto][$index6]) . '\',\'' . $optSatKeg[trim($x[$aerto][$index5])] . '\',\'' . $rupiah[$aerto][$index7] . '\',' . "\r\n" . '                                    \'' . trim($x[$aerto][$index9]) . '\',\'' . trim($x[$aerto][$index11]) . '\',\'' . trim($x[$aerto][$index12]) . '\',' . "\r\n" . '                                    \'' . $_SESSION['standard']['userid'] . '\',\'Data di upload oleh ' . $_SESSION['standard']['username'] . '\')';

					if (!mysql_query($detData)) {
						exit('error:' . "\n" . $detData . '__' . mysql_error());
					}
					else {
						echo '';
					}
				}
				else {
					exit('error:' . "\n" . $str . '__' . mysql_error());
				}
			}
			else {
				exit('error: alocation for this vhc code (' . $x[$aerto][$index9] . ') already over in line ' . $aerto);
			}

			++$aerto;
		}

		break;

	case 'KONTRAK':
		$str = 'select kodekegiatan from ' . $dbname . '.setup_kegiatan order by kodekegiatan asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$noakun[] = $bar->kodekegiatan;
		}

		foreach ($header as $ki => $val) {
			if ($val == 'tahunbudget') {
				$index1 = $ki;
			}

			if ($val == 'kodeblok') {
				$index2 = $ki;
			}

			if ($val == 'tipebudget') {
				$index3 = $ki;
			}

			if ($val == 'kodebudget') {
				$index4 = $ki;
			}

			if ($val == 'kodekegiatan') {
				$index5 = $ki;
			}

			if ($val == 'volumepekerjaansetahun') {
				$index6 = $ki;
			}

			if ($val == 'rupiahsetahun') {
				$index7 = $ki;
			}
		}

		if (count($x[0]) != 8) {
			exit('Error: Form not valid');
		}

		$str = 'select kodeblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '                    where tahunbudget=\'' . $x[1][$index1] . '\' and kodeblok like \'' . substr($x[1][$index2], 0, 4) . '%\' and closed=1' . "\r\n" . '                    order by kodeblok asc';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$kdblok[] = $bar->kodeblok;
		}

		if (count($kdblok) == 0) {
			exit('error: setup block budget has not been processed or closed ');
		}

		$str = 'select distinct * from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                    tahunbudget=\'' . $x[1][$index1] . '\' and kodeorg like \'' . substr($x[1][$index2], 0, 4) . '%\' and tutup=1';

		#exit(mysql_error($conn));
		($res = mysql_query($str)) || true;
		$barcek = mysql_num_rows($res);

		if (0 < $barcek) {
			exit('error: budget data for this ' . $x[1][$index1] . ' year has been closed ');
		}

		$thnBerjln = date('Y');

		foreach ($x as $key => $arr) {
			if ($key == 0) {
				continue;
			}

			foreach ($arr as $ids => $rinc) {
				if (($header[$ids] == 'tahunbudget') && (strlen($rinc) != 4)) {
					exit('Error: some data on budget year  (' . $rinc . ') not valid (line ' . $key . ') ' . $rinc);
				}

				if (($header[$ids] == 'tahunbudget') && ($rinc < $thnBerjln)) {
					exit('Error: some data on budget year (' . $rinc . ') the format not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodeblok') && (strlen($rinc) != 10)) {
					exit('Error: some data on code block not  (' . $rinc . ') valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodekegiatan') && (strlen($rinc) != 9)) {
					exit('Error: some data on activity code (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'tipebudget') && ($rinc != 'ESTATE')) {
					exit('Error: some data on budget type (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'kodebudget') && ($rinc != 'KONTRAK')) {
					exit('Error: some data on budget code (' . $rinc . ') not valid (line ' . $key . ')');
				}

				if (($header[$ids] == 'rupiahsetahun') && (intval($rinc) == '0')) {
					exit('Error: some data on rupiah a year not valid (line ' . $key . ')');
				}

				if ($header[$ids] == 'kodekegiatan') {
					$akunbermasalah[$rinc] = $rinc;

					foreach ($noakun as $bb => $cc) {
						if ($cc == $rinc) {
							unset($akunbermasalah[$rinc]);
						}
					}
				}

				if ($header[$ids] == 'kodeblok') {
					$blokbermasalah[$rinc] = $rinc;

					foreach ($kdblok as $bb => $cc) {
						if ($cc == $rinc) {
							unset($blokbermasalah[$rinc]);
						}
					}
				}
			}
		}

		if (0 < count($akunbermasalah)) {
			echo 'The following activity code were not defined:<br>';
			print_r($akunbermasalah);
			exit();
		}

		if (0 < count($blokbermasalah)) {
			echo 'The following block code were not defined:<br>';
			print_r($blokbermasalah);
			exit();
		}

		$jmlhRow = count($x);
		$aerto = 1;

		while ($aerto < $jmlhRow) {
			$str = 'delete from ' . $dbname . '.bgt_budget where ' . "\r\n" . '                            kodeorg=\'' . trim($x[$aerto][$index2]) . '\' and tahunbudget=\'' . trim($x[$aerto][$index1]) . '\' and kegiatan=\'' . trim($x[$aerto][$index5]) . '\'' . "\r\n" . '                            and kodevhc=\'' . trim($x[$aerto][$index9]) . '\'';

			if (mysql_query($str)) {
				$detData = 'insert into ' . $dbname . '.bgt_budget(`tahunbudget`,`kodeorg`,`tipebudget`,' . "\r\n" . '                              `kodebudget`,`kegiatan`,`noakun`,`volume`,`satuanv`,`rupiah`,`updateby`,`keterangan`) values ';
				$rupiah[$aerto][$index7] = str_replace(',', '', trim($x[$aerto][$index7]));
				$detData .= '(\'' . trim($x[$aerto][$index1]) . '\',\'' . trim($x[$aerto][$index2]) . '\',\'' . trim($x[$aerto][$index3]) . '\',\'' . trim($x[$aerto][$index4]) . '\',\'' . trim($x[$aerto][$index5]) . '\',' . "\r\n" . '                                    \'' . substr(trim($x[$aerto][$index5]), 0, 7) . '\',\'' . trim($x[$aerto][$index6]) . '\',\'' . $optSatKeg[trim($x[$aerto][$index5])] . '\',\'' . $rupiah[$aerto][$index7] . '\',' . "\r\n" . '                                    \'' . $_SESSION['standard']['userid'] . '\',\'Data di upload oleh ' . $_SESSION['standard']['username'] . '\')';

				if (!mysql_query($detData)) {
					exit('error:' . "\n" . $detData . '__' . mysql_error());
				}
				else {
					echo '';
				}
			}
			else {
				exit('error:' . "\n" . $str . '__' . mysql_error());
			}

			++$aerto;
		}

		break;
	}
}

require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$pemisah = $_POST['pemisah'];
$jenisdata = $_POST['jenisdata'];
$path = 'tempExcel';
$optSatKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optSatBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

if (is_dir($path)) {
	writeFile($path, $pemisah);
}
else if (mkdir($path)) {
	writeFile($path, $pemisah);
}
else {
	echo '<script> alert(\'Gagal, Can`t create folder for uploaded file\');</script>';
	exit(0);
}

?>
