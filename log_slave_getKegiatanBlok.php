<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';

if (isTransactionPeriod()) {
	$blok = $_POST['blok'];

	if ($blok != '') {
		$str = 'select tipe from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $blok . '\'';
		$res = mysql_query($str);
		$tipe = $_POST['jenis'];

		while ($bar = mysql_fetch_object($res)) {
			$tipe = $bar->tipe;
		}

		if (($tipe == 'STENGINE') || ($tipe == 'STATION')) {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t" . '       where kelompok=\'MIL\' order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
			}

			echo $optKegiatan;
		}
		else if ($tipe == 'BLOK') {
			$blehh = '<option value=\'\'></option>';
			$blehh .= getKegiatanBlok('option', $blok);
			echo $blehh;
		}
		else if ($tipe == 'WORKSHOP') {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t" . '       where kelompok=\'WSH\' order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
			}

			echo $optKegiatan;
		}
		else if ($tipe == 'SIPIL') {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t" . '       where kelompok=\'SPL\' order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
			}

			echo $optKegiatan;
		}
		else if ($tipe == 'TRAKSI') {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t" . '       where kelompok=\'TRK\' order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
			}

			echo $optKegiatan;
		}
		else if ($tipe == 'BIBITAN') {
			$optKegiatan = '<option value=\'\'></option>';
			$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n\t\t\t" . '       where  kelompok in (\'BBT\',\'MN\',\'PN\') order by kelompok,namakegiatan';
			$resf = mysql_query($strf);

			while ($barf = mysql_fetch_object($resf)) {
				$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
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
				$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                                       where kelompok=\'KNT\' order by kelompok,namakegiatan';
				$resf = mysql_query($strf);

				while ($barf = mysql_fetch_object($resf)) {
					$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
				}

				echo $optKegiatan;
			}
		}
	}
	else {
		$optKegiatan = '<option value=\'\'></option>';
		$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan ' . "\r\n" . '                                   where kelompok=\'KNT\' order by kelompok,namakegiatan';
		$resf = mysql_query($strf);

		while ($barf = mysql_fetch_object($resf)) {
			$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
		}

		echo $optKegiatan;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
