<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['pt'] == '' ? $pt = $_GET['pt'] : $pt = $_POST['pt'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
if ($proses == 'getkebun') {
	$optkebun = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '            where induk=\'' . $pt . '\' and tipe=\'KEBUN\'';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$optkebun .= '<option value=' . $res['kodeorganisasi'] . '>' . $res['namaorganisasi'] . '</option>';
	}

	if ($pt == '') {
		$optkebun = '<option value=\'\'></option>';
	}

	echo $optkebun;
}

if ($proses == 'getafdeling') {
	$optkebun = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '            where induk=\'' . $unit . '\' and tipe=\'AFDELING\'';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$optkebun .= '<option value=' . $res['kodeorganisasi'] . '>' . $res['namaorganisasi'] . '</option>';
	}

	if ($unit == '') {
		$optkebun = '<option value=\'\'></option>';
	}

	echo $optkebun;

	$str = 'SELECT namaorganisasi, kodeorganisasi FROM ' . $dbname . '.organisasi' . "\r\n" . '        WHERE tipe=\'KEBUN\' and induk=\'' . $pt . '\' order by namaorganisasi desc';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$listpt[$res['kodeorganisasi']] = $res['kodeorganisasi'];
		$namapt[$res['kodeorganisasi']] = $res['namaorganisasi'];
	}

	$str = 'SELECT a.kodeorg, (luasareaproduktif) as luas, b.kodeorganisasi,b.induk FROM ' . $dbname . '.setup_blok a' . "\r\n" . '        LEFT JOIN ' . $dbname . '.organisasi b ON substr(a.kodeorg,1,4)=b.kodeorganisasi' . "\r\n" . '        WHERE length(b.kodeorganisasi)=4 and a.statusblok=\'TM\' and b.induk=\'' . $pt . '\'' . "\r\n" . '        ';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$luaspt += $res['kodeorganisasi'];
	}

	$str = 'SELECT a.blok, (kgwbtanpabrondolan) as kg, b.induk,b.kodeorganisasi FROM ' . $dbname . '.kebun_spb_vs_rencana_blok_vw a' . "\r\n" . '        LEFT JOIN ' . $dbname . '.organisasi b ON substr(a.blok,1,4)=b.kodeorganisasi' . "\r\n" . '        WHERE b.tipe=\'KEBUN\' and a.periode = \'' . $periode . '\' and length(b.kodeorganisasi)=4  and b.induk=\'' . $pt . '\' ';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$prodpt += $res['kodeorganisasi'];
	}

	$str = 'SELECT a.blok, (kgwbtanpabrondolan) as kg, b.induk,b.kodeorganisasi FROM ' . $dbname . '.kebun_spb_vs_rencana_blok_vw a' . "\r\n" . '        LEFT JOIN ' . $dbname . '.organisasi b ON substr(a.blok,1,4)=b.kodeorganisasi' . "\r\n" . '        WHERE b.tipe=\'KEBUN\' and a.periode between \'' . $tahun . '-01\' and \'' . $periode . '\' and length(b.kodeorganisasi)=4  and b.induk=\'' . $pt . '\' ';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$prsdpt += $res['kodeorganisasi'];
	}

	$str = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '        WHERE length(noakun)=5 and noakun between \'61101\' and \'61102\' order by noakun';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$akun61[$res['noakun']] = $res['noakun'];
		$namaakun[$res['noakun']] = $res['namaakun'];
	}

	$str = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '        WHERE length(noakun)=5 and noakun between \'62101\' and \'62102\' order by noakun';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$akun62[$res['noakun']] = $res['noakun'];
		$namaakun[$res['noakun']] = $res['namaakun'];
	}

	$str = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '        WHERE length(noakun)=5 and noakun between \'62104\' and \'62111\' order by noakun';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$akun62[$res['noakun']] = $res['noakun'];
		$namaakun[$res['noakun']] = $res['namaakun'];
	}

	$str = 'SELECT noakun, namaakun FROM ' . $dbname . '.keu_5akun' . "\r\n" . '        WHERE length(noakun)=5 and noakun between \'62103\' and \'62103\' order by noakun';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$akun623[$res['noakun']] = $res['noakun'];
		$namaakun[$res['noakun']] = $res['namaakun'];
	}

	$str = 'SELECT a.kodeblok, substr(a.noakun,1,5) as noakun, a.debet, a.kredit, b.induk,b.kodeorganisasi FROM ' . $dbname . '.keu_jurnalsum_blok_vw a' . "\r\n" . '        LEFT JOIN ' . $dbname . '.organisasi b ON substr(a.kodeblok,1,4)=b.kodeorganisasi' . "\r\n" . '        WHERE b.tipe=\'KEBUN\' and a.periode = \'' . $periode . '\' and a.noakun between \'6110000\' and \'7199999\'  and b.induk=\'' . $pt . '\' ';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$bipt[$res['noakun']] += $res['kodeorganisasi'];

		if (substr($res['noakun'], 0, 3) == '711') {
			$bipt7[711] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '712') {
			$bipt7[712] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '713') {
			$bipt7[713] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '714') {
			$bipt7[714] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71501') {
			$bipt7[71501] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '716') {
			$bipt7[716] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71502') {
			$bipt9[71502] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71999') {
			$bipt9[71999] += $res['kodeorganisasi'];
		}
	}

	$str = 'SELECT a.kodeblok, substr(a.noakun,1,5) as noakun, a.debet, a.kredit, b.induk,b.kodeorganisasi FROM ' . $dbname . '.keu_jurnalsum_blok_vw a' . "\r\n" . '        LEFT JOIN ' . $dbname . '.organisasi b ON substr(a.kodeblok,1,4)=b.kodeorganisasi' . "\r\n" . '        WHERE b.tipe=\'KEBUN\' and a.periode between \'' . $tahun . '-01\' and \'' . $periode . '\' and a.noakun between \'6110000\' and \'7199999\'  and b.induk=\'' . $pt . '\' ';

	#exit(mysql_error($conn));
	($query = mysql_query($str)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$sbipt[$res['noakun']] += $res['kodeorganisasi'];

		if (substr($res['noakun'], 0, 3) == '711') {
			$sbipt7[711] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '712') {
			$sbipt7[712] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '713') {
			$sbipt7[713] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '714') {
			$sbipt7[714] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71501') {
			$sbipt7[71501] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 3) == '716') {
			$sbipt7[716] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71502') {
			$sbipt9[71502] += $res['kodeorganisasi'];
		}

		if (substr($res['noakun'], 0, 5) == '71999') {
			$sbipt9[71999] += $res['kodeorganisasi'];
		}
	}

	$akun7[711] = 711;
	$akun7[712] = 712;
	$akun7[713] = 713;
	$akun7[714] = 714;
	$akun7[71501] = 71501;
	$akun7[716] = 716;
	$akun9[71502] = 71502;
	$akun9[7199999] = 7199999;
	$namaakun[711] = 'Karyawan';
	$namaakun[712] = 'Operasional Karyawan';
	$namaakun[713] = 'Operasional Mess dan Kantor';
	$namaakun[714] = 'Pemeliharaan';
	$namaakun[71501] = 'Pembelian Barang Inventaris';
	$namaakun[716] = 'Biaya Lainnya';
	$namaakun[71502] = 'Penyusutan';
	$namaakun[7199999] = 'Biaya Overhead Alokasi';

	if ($proses == 'excel') {
		$bg = ' bgcolor=#DEDEDE';
		$brdr = 1;
		$tab .= 'BIAYA PRODUKSI TANDAN BUAH SEGAR PER DIVISI<br>01-' . $tahun . 'S/D ' . $bulan . '-' . $tahun;
	}
	else {
		echo 'BIAYA PRODUKSI TANDAN BUAH SEGAR PER DIVISI<br>01-' . $tahun . ' S/D ' . $bulan . '-' . $tahun;
		$bg = '';
		$brdr = 0;
	}

	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%\'>' . "\r\n" . '        <thead class=rowheader>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center colspan=2 ' . $bg . '>Keterangan</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			$tab .= '<td align=center colspan=6 ' . $bg . '>' . $namapt[$list] . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '<tr>' . "\r\n" . '            <td align=center colspan=2 ' . $bg . '>Luas TM (ha)</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			$tab .= '<td align=center colspan=6 ' . $bg . '>' . number_format($luaspt[$list], 2) . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '<tr>' . "\r\n" . '            <td align=center ' . $bg . '>Produksi BI (ton)</td>' . "\r\n" . '            <td align=center ' . $bg . '>Produksi SBI (ton)</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			$tab .= '<td align=center colspan=3 ' . $bg . '>' . number_format($prodpt[$list] / 1000, 2) . '</td>';
			$tab .= '<td align=center colspan=3 ' . $bg . '>' . number_format($prsdpt[$list] / 1000, 2) . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '<tr>' . "\r\n" . '            <td align=center colspan=2 ' . $bg . '></td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			$tab .= '<td align=center ' . $bg . '>BI Rp. (000)</td>';
			$tab .= '<td align=center ' . $bg . '>SBI Rp. (000)</td>';
			$tab .= '<td align=center ' . $bg . '>BI Rp/Ha</td>';
			$tab .= '<td align=center ' . $bg . '>BI Rp/Kg</td>';
			$tab .= '<td align=center ' . $bg . '>SBI Rp/Ha</td>';
			$tab .= '<td align=center ' . $bg . '>SBI Rp/Kg</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '</thead><tbody>' . "\r\n" . '    ';

	if (!empty($akun61)) {
		foreach ($akun61 as $akun) {
			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					$bipt61 += $list;
					$sbipt61 += $list;
					$bipt6t += $list;
					$sbipt6t += $list;
					$bipt7t += $list;
					$sbipt7t += $list;
					$bipt8t += $list;
					$sbipt8t += $list;
				}
			}
		}
	}

	if (!empty($akun62)) {
		foreach ($akun62 as $akun) {
			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					$bipt62 += $list;
					$sbipt62 += $list;
					$bipt6t += $list;
					$sbipt6t += $list;
					$bipt7t += $list;
					$sbipt7t += $list;
					$bipt8t += $list;
					$sbipt8t += $list;
				}
			}
		}
	}

	if (!empty($akun623)) {
		foreach ($akun623 as $akun) {
			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					$bipt623 += $list;
					$sbipt623 += $list;
					$bipt6t += $list;
					$sbipt6t += $list;
					$bipt7t += $list;
					$sbipt7t += $list;
					$bipt8t += $list;
					$sbipt8t += $list;
				}
			}
		}
	}

	if (!empty($akun7)) {
		foreach ($akun7 as $akun) {
			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					$bipt71 += $list;
					$sbipt71 += $list;
					$bipt7t += $list;
					$sbipt7t += $list;
					$bipt8t += $list;
					$sbipt8t += $list;
				}
			}
		}
	}

	if (!empty($akun9)) {
		foreach ($akun9 as $akun) {
			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					$bipt91 += $list;
					$sbipt91 += $list;
					$bipt8t += $list;
					$sbipt8t += $list;
				}
			}
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=left colspan=2 ' . $bg . '>Biaya Langsung</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt6tperluas = $bipt6t[$list] / $luaspt[$list];
			@$bipt6tperprod = $bipt6t[$list] / $prodpt[$list];
			@$sbipt6tperluas = $sbipt6t[$list] / $luaspt[$list];
			@$sbipt6tperprod = $sbipt6t[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt6t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt6t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt6tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt6tperprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt6tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt6tperprod) . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=right ' . $bg . '>611XXXX</td>' . "\r\n" . '    <td align=left ' . $bg . '>Panen dan Pengumpulan</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt61perluas = $bipt61[$list] / $luaspt[$list];
			@$bipt61perprod = $bipt61[$list] / $prodpt[$list];
			@$sbipt61perluas = $sbipt61[$list] / $luaspt[$list];
			@$sbipt61perprod = $sbipt61[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt61[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt61[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt61perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt61perprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt61perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt61perprod) . '</td>';
		}
	}

	$tab .= '</tr>';

	if (!empty($akun61)) {
		foreach ($akun61 as $akun) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>' . $akun . 'XX</td>' . "\r\n" . '        <td align=left>' . $namaakun[$akun] . '</td>';

			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					@$biptperluas = $bipt[$akun][$list] / $luaspt[$list];
					@$biptperprod = $bipt[$akun][$list] / $prodpt[$list];
					@$sbiptperluas = $sbipt[$akun][$list] / $luaspt[$list];
					@$sbiptperprod = $sbipt[$akun][$list] / $prsdpt[$list];
					$tab .= '<td align=right>' . number_format($bipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($sbipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperprod) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperprod) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=right ' . $bg . '>612XXXX</td>' . "\r\n" . '    <td align=left ' . $bg . '>Pemeliharaan TM</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt62perluas = $bipt62[$list] / $luaspt[$list];
			@$bipt62perprod = $bipt62[$list] / $prodpt[$list];
			@$sbipt62perluas = $sbipt62[$list] / $luaspt[$list];
			@$sbipt62perprod = $sbipt62[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt62[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt62[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt62perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt62perprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt62perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt62perprod) . '</td>';
		}
	}

	$tab .= '</tr>';

	if (!empty($akun62)) {
		foreach ($akun62 as $akun) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>' . $akun . 'XX</td>' . "\r\n" . '        <td align=left>' . $namaakun[$akun] . '</td>';

			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					@$biptperluas = $bipt[$akun][$list] / $luaspt[$list];
					@$biptperprod = $bipt[$akun][$list] / $prodpt[$list];
					@$sbiptperluas = $sbipt[$akun][$list] / $luaspt[$list];
					@$sbiptperprod = $sbipt[$akun][$list] / $prsdpt[$list];
					$tab .= '<td align=right>' . number_format($bipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($sbipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperprod) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperprod) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=right ' . $bg . '>61203XX</td>' . "\r\n" . '    <td align=left ' . $bg . '>Pemupukan TM</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt623perluas = $bipt62[$list] / $luaspt[$list];
			@$bipt623perprod = $bipt62[$list] / $prodpt[$list];
			@$sbipt623perluas = $sbipt62[$list] / $luaspt[$list];
			@$sbipt623perprod = $sbipt62[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt623[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt623[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt623perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt623perprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt623perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt623perprod) . '</td>';
		}
	}

	$tab .= '</tr>';

	if (!empty($akun623)) {
		foreach ($akun623 as $akun) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>' . $akun . 'XX</td>' . "\r\n" . '        <td align=left>' . $namaakun[$akun] . '</td>';

			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					@$biptperluas = $bipt[$akun][$list] / $luaspt[$list];
					@$biptperprod = $bipt[$akun][$list] / $prodpt[$list];
					@$sbiptperluas = $sbipt[$akun][$list] / $luaspt[$list];
					@$sbiptperprod = $sbipt[$akun][$list] / $prsdpt[$list];
					$tab .= '<td align=right>' . number_format($bipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($sbipt[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperprod) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperprod) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=right ' . $bg . '>7XXXXXX</td>' . "\r\n" . '    <td align=left ' . $bg . '>Biaya Umum</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt71perluas = $bipt71[$list] / $luaspt[$list];
			@$bipt71perprod = $bipt71[$list] / $prodpt[$list];
			@$sbipt71perluas = $sbipt71[$list] / $luaspt[$list];
			@$sbipt71perprod = $sbipt71[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt71[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt71[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt71perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt71perprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt71perluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt71perprod) . '</td>';
		}
	}

	$tab .= '</tr>';

	if (!empty($akun7)) {
		foreach ($akun7 as $akun) {
			$tab .= '<tr class=rowcontent>';

			if ($akun == '71501') {
				$tab .= '<td align=right>' . $akun . 'XX</td>';
			}
			else {
				$tab .= '<td align=right>' . $akun . 'XXXX</td>';
			}

			$tab .= '<td align=left>' . $namaakun[$akun] . '</td>';

			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					@$biptperluas = $bipt7[$akun][$list] / $luaspt[$list];
					@$biptperprod = $bipt7[$akun][$list] / $prodpt[$list];
					@$sbiptperluas = $sbipt7[$akun][$list] / $luaspt[$list];
					@$sbiptperprod = $sbipt7[$akun][$list] / $prsdpt[$list];
					$tab .= '<td align=right>' . number_format($bipt7[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($sbipt7[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperprod) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperprod) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=left colspan=2 ' . $bg . '>Total Sebelum Depresiasi dan Alokasi</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt7tperluas = $bipt7t[$list] / $luaspt[$list];
			@$bipt7tperprod = $bipt7t[$list] / $prodpt[$list];
			@$sbipt7tperluas = $sbipt7t[$list] / $luaspt[$list];
			@$sbipt7tperprod = $sbipt7t[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt7t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt7t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt7tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt7tperprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt7tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt7tperprod) . '</td>';
		}
	}

	$tab .= '</tr>';

	if (!empty($akun9)) {
		foreach ($akun9 as $akun) {
			$tab .= '<tr class=rowcontent>';

			if ($akun == '71502') {
				$tab .= '<td align=right>' . $akun . 'XX</td>';
			}
			else {
				$tab .= '<td align=right>' . $akun . '</td>';
			}

			$tab .= '<td align=left>' . $namaakun[$akun] . '</td>';

			if (!empty($listpt)) {
				foreach ($listpt as $list) {
					@$biptperluas = $bipt9[$akun][$list] / $luaspt[$list];
					@$biptperprod = $bipt9[$akun][$list] / $prodpt[$list];
					@$sbiptperluas = $sbipt9[$akun][$list] / $luaspt[$list];
					@$sbiptperprod = $sbipt9[$akun][$list] / $prsdpt[$list];
					$tab .= '<td align=right>' . number_format($bipt9[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($sbipt9[$akun][$list] / 1000) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($biptperprod) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperluas) . '</td>';
					$tab .= '<td align=right>' . number_format($sbiptperprod) . '</td>';
				}
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '<tr class=rowtitle>' . "\r\n" . '    <td align=left colspan=2 ' . $bg . '>Biaya Produksi TBS</td>';

	if (!empty($listpt)) {
		foreach ($listpt as $list) {
			@$bipt8tperluas = $bipt8t[$list] / $luaspt[$list];
			@$bipt8tperprod = $bipt8t[$list] / $prodpt[$list];
			@$sbipt8tperluas = $sbipt8t[$list] / $luaspt[$list];
			@$sbipt8tperprod = $sbipt8t[$list] / $prsdpt[$list];
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt8t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt8t[$list] / 1000) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt8tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($bipt8tperprod) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt8tperluas) . '</td>';
			$tab .= '<td align=right ' . $bg . '>' . number_format($sbipt8tperprod) . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '</tbody></table>';

	switch ($proses) {
	case 'preview':
		echo $tab;
		break;

	case 'excel':
		$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
		$dte = date('YmdHis');
		$nop_ = 'mr_biayaProduksiTbsPT_' . $periode;

		if (0 < strlen($tab)) {
			if ($handle = opendir('tempExcel')) {
				while (false !== $file = readdir($handle)) {
					if (($file != '.') && ($file != '..')) {
						@unlink('tempExcel/' . $file);
					}
				}

				closedir($handle);
			}

			$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

			if (!fwrite($handle, $tab)) {
				echo '<script language=javascript1.2>' . "\r\n" . '                parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                </script>';
				exit();
			}
			else {
				echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                </script>';
			}

			closedir($handle);
		}

		break;

	case 'pdf':
		class PDF extends FPDF
		{
			public function Header()
			{
				global $periode;
				global $pt;
				global $unit;
				global $optNm;
				global $optBulan;
				global $tahun;
				global $bulan;
				global $dbname;
				global $luas;
				global $wkiri;
				global $wlain;
				global $luasbudg;
				global $luasreal;
				global $tt;
				$width = $this->w - $this->lMargin - $this->rMargin;
				$height = 20;
				$this->SetFillColor(220, 220, 220);
				$this->SetFont('Arial', 'B', 12);
				$kepala = 'PT ' . $pt;

				if ($unit != '') {
					$kepala .= ', UNIT ' . $unit;
				}

				$this->Cell($width, $height, $kepala, NULL, 0, 'L', 1);
				$this->Ln();
				$this->Cell($width, $height, 'LAPORAN SUMMARY PEMBUKAAN DAN TANAMAN BELUM MENGHASILKAN', NULL, 0, 'L', 1);
				$this->Ln();
				$this->Cell($width, $height, 'PER TAHUN TANAM', NULL, 0, 'L', 1);
				$this->Ln();
				$this->Cell($width, $height, 'S/D ' . $bulan . '-' . $tahun, NULL, 0, 'L', 1);
				$this->Ln();
				$this->Ln();
				$height = 15;
				$this->SetFont('Arial', 'B', 7);
				$this->Cell((($wkiri + $wlain + $wlain) / 100) * $width, $height, 'Tahun Tanam', 1, 0, 'C', 1);

				if (!empty($tt)) {
					foreach ($tt as $tata) {
						if ($tata == 0) {
							$this->Cell((($wlain + $wlain) / 100) * $width, $height, 'Undefined', 1, 0, 'C', 1);
						}
						else {
							$this->Cell((($wlain + $wlain) / 100) * $width, $height, $tata, 1, 0, 'C', 1);
						}
					}
				}

				$this->Cell((($wlain + $wlain) / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
				$this->Ln();
			}

			public function Footer()
			{
				$this->SetY(-15);
				$this->SetFont('Arial', 'I', 8);
				$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
			}
		}

		$cols = 247.5;
		$wkiri = 5;
		$wlain = 4.5;
		$pdf = new PDF('L', 'pt', 'A4');
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
		$height = 15;
		$pdf->AddPage();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 7);
		$pdf->Output();
		break;
	}
}
else {
	break;
}

?>
