<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$_POST['method'] != '' ? $method = $_POST['method'] : $method = $_POST['method2'];
$_POST['pilUn_1'] != '' ? $pilUn_1 = $_POST['pilUn_1'] : $pilUn_1 = $_POST['pilUn_2'];
$_POST['unitId'] != '' ? $unitId = $_POST['unitId'] : $unitId = $_POST['unitId2'];
$_POST['thnBudget'] != '' ? $thnBudget = $_POST['thnBudget'] : $thnBudget = $_POST['thnBudget2'];
$_POST['klmpKeg'] != '' ? $klmpKeg = $_POST['klmpKeg'] : $klmpKeg = $_POST['klmpKeg2'];
$_POST['kdBgt'] != '' ? $kdBgt = $_POST['kdBgt'] : $kdBgt = $_POST['kdBgt2'];
$_POST['kegId'] != '' ? $kegId = $_POST['kegId'] : $kegId = $_POST['kegId2'];
$_POST['kdBrgRev'] != '' ? $kdBrgRev = $_POST['kdBrgRev'] : $kdBrgRev = $_POST['kdBrgRev'];
$kegIdR2 = $_POST['kegIdR2'];
$kdBgtR2 = $_POST['kdBgtR2'];
$kdBarang = $_POST['kdBarang'];
$actId = $_POST['actId'];
$_POST['sbUnit'] != '' ? $sbUnit = $_POST['sbUnit'] : $sbUnit = $_POST['sbUnit2'];
$_POST['thnTnm'] != '' ? $thnTnm = $_POST['thnTnm'] : $thnTnm = $_POST['thnTnm'];
$blokId = $_POST['blokId'];
$_POST['persenData'] != '' ? $persenData = $_POST['persenData'] : $persenData = $_POST['persenData2'];
$optNmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');

switch ($method) {
case 'getData':
	if ($kdBgt == 'UMUM') {
		if (($pilUn_1 == '') || ($unitId == '') || ($thnBudget == '') || ($persenData == '')) {
			exit('Error:Option can not be null');
		}
	}
	else {
		if (($pilUn_1 == '') || ($unitId == '') || ($thnBudget == '') || ($klmpKeg == '') || ($kegId == '') || ($persenData == '')) {
			exit('Error:Option can not be null');
		}
	}

	$unitId2 = 'kodeorg like \'' . $unitId . '%\'';

	if ($kdBgt != '') {
		$whr .= ' and kodebudget like \'' . $kdBgt . '%\'';
	}

	if ($sbUnit != '') {
		$unitId2 == 'kodeorg like \'' . $sbUnit . '%\'';

		if ($blokId != '') {
			$unitId2 = 'kodeorg like \'' . $blokId . '%\'';
		}
	}

	if ($thnTnm != '') {
		$unitId2 = 'kodeorg in (select distinct kodeblok from ' . $dbname . '.bgt_blok where' . "\r\n" . '                                         thntnm=\'' . $thnTnm . '\' and tahunbudget=\'' . $thnBudget . '\' and kodeblok like \'' . $unitId . '%\')';
	}

	if ($kdBrgRev != '') {
		if ($kdBgt == 'UMUM') {
			$whr .= ' and noakun=\'' . $kdBrgRev . '\'';
		}
		else {
			$whr .= ' and kodebarang=\'' . $kdBrgRev . '\'';
		}
	}

	if ($kdBgt != 'UMUM') {
		$sData = 'select distinct * from ' . $dbname . '.bgt_budget where ' . $unitId2 . "\r\n" . '                        and kegiatan=\'' . $kegId . '\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget!=\'UMUM\' ' . $whr . "\r\n" . '                        and tutup!=1 order by kunci';
	}
	else {
		$sData = 'select distinct * from ' . $dbname . '.bgt_budget where ' . $unitId2 . "\r\n" . '                        and tahunbudget=\'' . $thnBudget . '\' and kodebudget=\'UMUM\' ' . $whr . "\r\n" . '                        and tutup!=1 order by kunci';
	}

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;
	$rowdt = mysql_num_rows($qData);

	if ($rowdt == 0) {
		exit('Error:Data Kosong atau Sudah Ditutup untuk tahun budget : ' . $thnBudget . '');
	}

	$tab .= '<button class=mybutton onclick=revisi(' . $rowdt . ')  id=revTmbl>Revisi</button>';
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr><td>' . $_SESSION['lang']['index'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebudget'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kegiatan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namakegiatan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['volume'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rotasi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '  ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '</tr></thead><tbody id=dataIsi>';

	while ($rData = mysql_fetch_assoc($qData)) {
		++$nor;
		$revVol = $rData['volume'];
		$revRot = $rData['rotasi'];
		$revJum = $rData['jumlah'];
		$revRup = $rData['rupiah'];

		switch ($pilUn_1) {
		case '1':
			@$revVol = $rData['volume'] * $persenData;
			break;

		case '2':
			@$revRot = $rData['rotasi'] * $persenData;
			break;

		case '3':
			@$revRot = $rData['rotasi'] * $persenData;
			@$revVol = $rData['volume'] * $persenData;
			break;

		case '4':
			@$revJum = $rData['jumlah'] * $persenData;
			@$revRup = $rData['rupiah'] * $persenData;
			break;
		}

		$tab .= ($_POST['method']);
		$tab .= '<td id=knci_' . $nor . '>' . $rData['kunci'] . '</td>';
		$tab .= '<td>' . $rData['kodeorg'] . '</td>';
		$tab .= '<td>' . $rData['kodebudget'] . '</td>';
		$tab .= '<td>' . $rData['kegiatan'] . '</td>';
		$tab .= '<td>' . $optNmKeg[$rData['kegiatan']] . '</td>';
		$tab .= '<td>' . $rData['noakun'] . '</td>';
		$tab .= '<td align=right id=vol_' . $nor . '>' . $rData['volume'] . '</td>';
		$tab .= '<td align=right id=volRev_' . $nor . '>' . $revVol . '</td>';
		$tab .= '<td>' . $rData['satuanv'] . '</td>';
		$tab .= '<td align=right id=rot_' . $nor . '>' . $rData['rotasi'] . '</td>';
		$tab .= '<td align=right id=rotRev_' . $nor . '>' . $revRot . '</td>';
		$tab .= '<td>' . $rData['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmBrg[$rData['kodebarang']] . '</td>';
		$tab .= '<td align=right id=jum_' . $nor . '>' . $rData['jumlah'] . '</td>';
		$tab .= '<td align=right id=jumRev_' . $nor . '>' . $revJum . '</td>';
		$tab .= '<td>' . $rData['satuanj'] . '</td>';
		$tab .= '<td align=right id=rup_' . $nor . '>' . $rData['rupiah'] . '</td>';
		$tab .= '<td align=right id=rupRev_' . $nor . '>' . $revRup . '</td>';
		$tab .= '</tr>';
		$totRup += $rData['rupiah'];
		$totRupRev += $revRup;
		$totJuml += $rData['jumlah'];
		$totJumlRev += $revJum;
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=13>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totJuml, 2) . '</td><td align=right>' . number_format($totJumlRev, 2) . '</td>';
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totRup, 2) . '</td><td align=right>' . number_format($totRupRev, 2) . '</td></tr>';
	$tab .= '</tbody></table>';
	$tab .= '<button class=mybutton onclick=revisi(' . $rowdt . ')  id=revTmbl>Revisi</button>';
	echo $tab;
	break;

case 'getData2':
	if (($pilUn_1 == '') || ($unitId == '') || ($thnBudget == '') || ($klmpKeg == '') || ($kegId == '')) {
		exit('Error:Pilihan Tidak Boleh Kosong');
	}

	if ($pilUn_1 == '7') {
		$sUpdate = 'update ' . $dbname . '.bgt_blok set closed=0' . "\r\n" . '                             where kodeblok like \'' . substr($unitId, 0, 4) . '%\' and tahunbudget=\'' . $thnBudget . '\'';

		if (!mysql_query($sUpdate)) {
			#exit(mysql_error($conn));
		}
		else {
			break;
		}
	}

	if ($pilUn_1 == '8') {
		$sUpdate = 'update ' . $dbname . '.bgt_upah set closed=0' . "\r\n" . '                             where kodeorg=\'' . substr($unitId, 0, 4) . '\' and tahunbudget=\'' . $thnBudget . '\'';

		if (!mysql_query($sUpdate)) {
			#exit(mysql_error($conn));
		}
		else {
			break;
		}
	}

	if ($pilUn_1 <= '4') {
		if ($persenData == '') {
			exit('Error:Pilihan Tidak Boleh Kosong');
		}
	}

	if ($kdBgt != '') {
		$whr .= ' and kodebudget like \'' . $kdBgt . '%\'';
	}

	if ($pilUn_1 == '10') {
		$whr .= ' and kodebudget like \'M%\' and kodebarang=\'' . $_POST['kdBrgLam'] . '\'';
	}

	if ($sbUnit != '') {
		$unitId = $sbUnit;

		if ($blokId != '') {
			$unitId = $blokId;
		}
	}

	$sData = 'select distinct * from ' . $dbname . '.bgt_budget where kodeorg like \'' . $unitId . '%\'' . "\r\n" . '                        and kegiatan=\'' . $kegId . '\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget!=\'UMUM\' ' . $whr . "\r\n" . '                        and tutup!=1 order by kunci';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;
	$rowdt = mysql_num_rows($qData);

	if ($rowdt == 0) {
		exit('Error:Data Kosong atau Sudah Ditutup untuk tahun budget : ' . $thnBudget . '');
	}

	$tab .= '<button class=mybutton onclick=revisi2(' . $rowdt . ')  id=revTmbl2>Revisi</button>';
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr><td>' . $_SESSION['lang']['index'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebudget'] . '</td>';

	if ($pilUn_1 == '9') {
		$tab .= '<td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['kodebudget'] . '</td>';
	}

	$tab .= '<td>' . $_SESSION['lang']['kegiatan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namakegiatan'] . '</td>';

	if ($pilUn_1 == '6') {
		$tab .= '<td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['kegiatan'] . '</td>';
		$tab .= '<td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['namakegiatan'] . '</td>';
	}

	$tab .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['volume'] . '</td>';

	if ($pilUn_1 == '1') {
		$tab .= '<td>' . $_SESSION['lang']['volume'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	}

	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rotasi'] . '</td>';

	if ($pilUn_1 == '2') {
		$tab .= '<td>' . $_SESSION['lang']['rotasi'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	}

	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';

	if ($pilUn_1 == '10') {
		$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . ' PENGGANTI</td>';
		$tab .= '<td>' . $_SESSION['lang']['namabarang'] . ' PENGGANTI</td>';
	}

	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . ' ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '  ' . $_SESSION['lang']['revisi'] . '</td>';
	$tab .= '</tr></thead><tbody id=dataIsi>';

	while ($rData = mysql_fetch_assoc($qData)) {
		++$nor;
		$revVol = $rData['volume'];
		$revRot = $rData['rotasi'];
		$revJum = $rData['jumlah'];
		$revRup = $rData['rupiah'];
		$revKeg = $rData['kegiatan'];

		switch ($pilUn_1) {
		case '1':
			$revVol = $persenData * $rData['rotasi'];
			break;

		case '2':
			$revRot = $persenData;
			$revVol = $persenData * $rData['volume'];
			break;

		case '3':
			$revJum = $persenData;
			$revRup = $persenData * ($rData['rupiah'] / $rData['jumlah']);
			break;

		case '4':
			@$revRup = $rData['jumlah'] * $persenData;
			break;

		case '5':
			$revVol = 0;
			$revRot = 0;
			$revJum = 0;
			$revRup = 0;
			$revKeg = '';
			break;

		case '6':
			if ($kegIdR2 != '') {
				$persenData = $kegIdR2;
			}
			else {
				exit('Error:Kegiatan Tidak Boleh Kosong');
			}

			$revKeg = $persenData;
			break;

		case '9':
			$revKdbgt = $_POST['kdBgtR2'];
			$sUpah = 'select distinct jumlah from ' . $dbname . '.bgt_upah ' . "\r\n" . '                                where golongan=\'' . $revKdbgt . '\' and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '                                and kodeorg=\'' . substr($unitId, 0, 4) . '\' and closed=1';

			#exit(mysql_error($conn));
			($qUpah = mysql_query($sUpah)) || true;
			$row = mysql_num_rows($qUpah);

			if ($row == 0) {
				exit('Error:Data Kosong atau belum di tutup');
			}

			$rUpah = mysql_fetch_assoc($qUpah);
			if (($rUpah['jumlah'] != 0) || ($rUpah['jumlah'] != '')) {
				$revRup = $rUpah['jumlah'] * $rData['jumlah'];
			}
			else {
				exit('Error:Data Kosong atau belum di tutup');
			}

			break;

		case '10':
			$sReg = 'select distinct regional from ' . $dbname . '.bgt_regional_assignment where kodeunit=\'' . $unitId . '\'';

			#exit(mysql_error($conn));
			($qReg = mysql_query($sReg)) || true;
			$rReg = mysql_fetch_assoc($qReg);
			$sharga = 'select distinct hargasatuan from ' . $dbname . '.bgt_masterbarang ' . "\r\n" . '                                 where tahunbudget=\'' . $thnBudget . '\' and kodebarang=\'' . $kdBarang . '\'' . "\r\n" . '                                 and regional=\'' . $rReg['regional'] . '\'';

			#exit(mysql_error($conn));
			($qharga = mysql_query($sharga)) || true;
			$rharga = mysql_fetch_assoc($qharga);
			$revRup = $rharga['hargasatuan'] * $rData['jumlah'];
			break;
		}

		$tab .= ($_POST['method']);
		$tab .= '<td id=knci_' . $nor . '>' . $rData['kunci'] . '</td>';
		$tab .= '<td>' . $rData['kodeorg'] . '</td>';
		$tab .= '<td>' . $rData['kodebudget'] . '</td>';

		if ($pilUn_1 == '9') {
			$tab .= '<td id=kdBgtRe_' . $nor . '>' . $revKdbgt . '</td>';
		}

		$tab .= '<td>' . $rData['kegiatan'] . '</td>';
		$tab .= '<td>' . $optNmKeg[$rData['kegiatan']] . '</td>';

		if ($pilUn_1 == '6') {
			$tab .= '<td id=revKeg_' . $nor . '>' . $revKeg . '</td>';
			$tab .= '<td>' . $optNmKeg[$revKeg] . '</td>';
		}

		$tab .= '<td>' . $rData['noakun'] . '</td>';
		$tab .= '<td align=right id=vol_' . $nor . '>' . $rData['volume'] . '</td>';

		if ($pilUn_1 == '1') {
			$tab .= '<td align=right id=volRev_' . $nor . '>' . $revVol . '</td>';
		}

		$tab .= '<td>' . $rData['satuanv'] . '</td>';
		$tab .= '<td align=right id=rot_' . $nor . '>' . $rData['rotasi'] . '</td>';

		if ($pilUn_1 == '2') {
			$tab .= '<td align=right id=rotRev_' . $nor . '>' . $revRot . '</td>';
		}

		$tab .= '<td>' . $rData['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmBrg[$rData['kodebarang']] . '</td>';

		if ($pilUn_1 == '10') {
			$tab .= '<td id=kdbrgRev_' . $nor . '>' . $kdBarang . '</td>';
			$tab .= '<td>' . $optNmBrg[$kdBarang] . '</td>';
		}

		$tab .= '<td align=right id=jum_' . $nor . '>' . number_format($rData['jumlah'], 2) . '</td>';
		$tab .= '<td align=right id=jumRev_' . $nor . '>' . number_format($revJum, 2) . '</td>';
		$tab .= '<td>' . $rData['satuanj'] . '</td>';
		$tab .= '<td align=right id=rup_' . $nor . '>' . number_format($rData['rupiah'], 2) . '</td>';
		$tab .= '<td align=right id=rupRev_' . $nor . '>' . number_format($revRup, 2) . '</td>';
		$tab .= '</tr>';
		$totRup += $rData['rupiah'];
		$totRupRev += $revRup;
		$totJuml += $rData['jumlah'];
		$totJumlRev += $revJum;
	}

	$ar = '13';

	if ($pilUn_1 == '6') {
		$ar = '15';
	}

	if ($pilUn_1 == '9') {
		$ar = '14';
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=' . $ar . '>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totJuml, 2) . '</td><td align=right>' . number_format($totJumlRev, 2) . '</td>';
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totRup, 2) . '</td><td align=right>' . number_format($totRupRev, 2) . '</td></tr>';
	$tab .= '</tbody></table>';
	$tab .= '<button class=mybutton onclick=revisi2(' . $rowdt . ')  id=revTmbl2>Revisi</button>';
	echo $tab;
	break;

case 'getKeg':
	$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

	if ($_SESSION['language'] == 'ID') {
		$dd = 'namakegiatan';
	}
	else {
		$dd = 'namakegiatan1 as namakegiatan';
	}

	$skegt = 'select distinct kodekegiatan,' . $dd . ' from ' . $dbname . '.setup_kegiatan where kelompok=\'' . $klmpKeg . '\' order by kodekegiatan asc';

	#exit(mysql_error($conn));
	($qKegt = mysql_query($skegt)) || true;

	while ($rkegt = mysql_fetch_assoc($qKegt)) {
		$opt .= '<option value=\'' . $rkegt['kodekegiatan'] . '\'>' . $rkegt['kodekegiatan'] . ' - ' . $rkegt['namakegiatan'] . '</option>';
	}

	echo $opt;
	break;

case 'getSub':
	$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optthn = '';

	if ($optTipe[$unitId] == 'KEBUN') {
		$skegt = 'select distinct substr(kodeblok,1,6) as kodeorganisasi from ' . $dbname . '.bgt_blok where' . "\r\n" . '                            kodeblok like \'' . $unitId . '%\' and tahunbudget=\'' . $thnBudget . '\' order by kodeblok asc';
		$optthn = '';
		$optthn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$thn = 'select distinct thntnm from ' . $dbname . '.bgt_blok where' . "\r\n" . '                            kodeblok like \'' . $unitId . '%\' and tahunbudget=\'' . $thnBudget . '\' order by thntnm asc';

		#exit(mysql_error($conn));
		($qthn = mysql_query($thn)) || true;

		while ($rthn = mysql_fetch_assoc($qthn)) {
			$optthn .= '<option value=\'' . $rthn['thntnm'] . '\'>' . $rthn['thntnm'] . '</option>';
		}
	}

	if ($optTipe[$unitId] == 'PABRIK') {
		$skegt = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where' . "\r\n" . '                            induk=\'' . $unitId . '\' and tipe=\'STATION\' order by namaorganisasi asc';
	}

	#exit(mysql_error($conn));
	($qKegt = mysql_query($skegt)) || true;
	$row = mysql_num_rows($qKegt);

	if ($row == 0) {
		exit('Error:Data Kosong');
	}

	while ($rkegt = mysql_fetch_assoc($qKegt)) {
		$opt .= '<option value=\'' . $rkegt['kodeorganisasi'] . '\'>' . $rkegt['kodeorganisasi'] . '</option>';
	}

	echo $opt . '###' . $optthn;
	break;

case 'getBlok':
	$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

	if ($optTipe[$sbUnit] == 'AFDELING') {
		$skegt = 'select distinct kodeblok as kodeorganisasi from ' . $dbname . '.bgt_blok where' . "\r\n" . '                            kodeblok like \'' . $sbUnit . '%\' and tahunbudget=\'' . $thnBudget . '\' order by kodeblok asc';
	}

	if ($optTipe[$sbUnit] == 'STATION') {
		$skegt = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where' . "\r\n" . '                            induk=\'' . $sbUnit . '\' and tipe=\'STENGINE\' order by namaorganisasi asc';
	}

	#exit(mysql_error($conn));
	($qKegt = mysql_query($skegt)) || true;

	while ($rkegt = mysql_fetch_assoc($qKegt)) {
		$opt .= '<option value=\'' . $rkegt['kodeorganisasi'] . '\'>' . $rkegt['kodeorganisasi'] . '</option>';
	}

	echo $opt;
	break;

case 'getUnit':
	$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$skegt = 'select distinct kodevhc from ' . $dbname . '.bgt_vhc_jam where tahunbudget=\'' . $_POST['thnBudget'] . '\'' . "\r\n" . '                        and kodetraksi=\'' . $_POST['kdTraksi'] . '\'';

	#exit(mysql_error($conn));
	($qKegt = mysql_query($skegt)) || true;

	while ($rkegt = mysql_fetch_assoc($qKegt)) {
		$opt .= '<option value=\'' . $rkegt['kodevhc'] . '\'>' . $rkegt['kodevhc'] . '</option>';
	}

	echo $opt;
	break;

case 'saveRevisi':
	switch ($pilUn_1) {
	case '1':
		$qry = '`volume`=\'' . $_POST['volRev'] . '\'';
		break;

	case '2':
		$qry = '`rotasi`=\'' . $_POST['rotRev'] . '\'';
		break;

	case '3':
		$qry = '`rotasi`=\'' . $_POST['rotRev'] . '\',`volume`=\'' . $_POST['volRev'] . '\'';
		break;

	case '4':
		$_POST['jumRev'] = str_replace(',', '', $_POST['jumRev']);
		$_POST['rupRev'] = str_replace(',', '', $_POST['rupRev']);
		$qry = '`jumlah`=\'' . $_POST['jumRev'] . '\',`rupiah`=\'' . $_POST['rupRev'] . '\'';
		break;
	}

	$sUpdate = ($_POST['method']) . '.bgt_budget set ' . $qry . ' where kunci=\'' . $_POST['index'] . '\'';

	if (mysql_query($sUpdate)) {
		$ss = 1;
	}

	echo $ss;
	break;

case 'saveRevisi2':
	switch ($pilUn_1) {
	case '1':
		$str = 'update ' . $dbname . '.bgt_budget set volume=\'' . $_POST['volRev'] . '\' where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '2':
		$str = 'update ' . $dbname . '.bgt_budget set volume=\'' . $_POST['volRev'] . '\',rotasi=\'' . $_POST['rotRev'] . '\'' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '3':
		$_POST['jumRev'] = str_replace(',', '', $_POST['jumRev']);
		$_POST['rupRev'] = str_replace(',', '', $_POST['rupRev']);
		$str = 'update ' . $dbname . '.bgt_budget set jumlah=\'' . $_POST['jumRev'] . '\',rupiah=\'' . $_POST['rupRev'] . '\'' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '4':
		$scek = 'select distinct kodebudget from ' . $dbname . '.bgt_budget where kunci=\'' . $_POST['index'] . '\'';

		#exit(mysql_error($conn));
		($qCek = mysql_query($scek)) || true;
		$rCek = mysql_fetch_assoc($qCek);

		if (substr($rCek['kodebudget'], 0, 3) == 'SDM') {
			$hrg = $_POST['rupRev'] / $_POST['jumRev'];
			$sUp = 'update ' . $dbname . '.bgt_upah set jumlah=' . $hrg . ' ' . "\r\n" . '                                      where kodeorg=\'' . $unitId . '\' and tahunbudget=\'' . $thnBudget . '\' and golongan=\'' . $rCek['kodebudget'] . '\'';

			if (!mysql_query($sUp)) {
				#exit(mysql_error($conn));
			}
		}

		$_POST['rupRev'] = str_replace(',', '', $_POST['rupRev']);
		$str = 'update ' . $dbname . '.bgt_budget set rupiah=\'' . $_POST['rupRev'] . '\'' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '5':
		$str = 'delete from ' . $dbname . '.bgt_budget' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '6':
		$noakun = substr($_POST['revKeg'], 0, 7);
		$scek = 'select distinct kodebudget from ' . $dbname . '.bgt_budget where kunci=\'' . $_POST['index'] . '\'';

		#exit(mysql_error($conn));
		($qCek = mysql_query($scek)) || true;
		$rCek = mysql_fetch_assoc($qCek);
		$str = 'update from ' . $dbname . '.bgt_budget set kegiatan=\'' . $_POST['revKeg'] . '\',noakun=\'' . $noakun . '\'' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';

		if ($rCek['kodebudget'] == 'SUPERVISI') {
			$str = 'select distinct nilai from ' . $dbname . '.setup_parameterappl where kodeaplikasi=\'SB\'';
			$res = mysql_query($str);

			while ($bar = mysql_fetch_object($res)) {
				$akun[substr($bar->nilai, 0, 3)] = $bar->nilai;
			}

			$aks = substr($_POST['revKeg'], 0, 3);
			$str = 'update from ' . $dbname . '.bgt_budget set kegiatan=\'' . $_POST['revKeg'] . '\',noakun=\'' . $akun[$aks] . '\'' . "\r\n" . '                                  where kunci=\'' . $_POST['index'] . '\'';
		}

		break;

	case '9':
		$_POST['rupRev'] = str_replace(',', '', $_POST['rupRev']);
		$str = 'update ' . $dbname . '.bgt_budget set kodebudget=\'' . $_POST['kdBgtRe'] . '\',rupiah=\'' . $_POST['rupRev'] . '\'' . "\r\n" . '                              where kunci=\'' . $_POST['index'] . '\'';
		break;

	case '10':
		$kdbt = substr($_POST['kdbrgRev'], 0, 3);
		$kdbt = 'M-' . $kdbt;
		$_POST['rupRev'] = str_replace(',', '', $_POST['rupRev']);
		$str = 'update ' . $dbname . '.bgt_budget set kodebudget=\'' . $kdbt . '\',kodebarang=\'' . $_POST['kdbrgRev'] . '\',rupiah=\'' . $_POST['rupRev'] . '\'' . "\r\n" . '                              where kunci=\'' . $_POST['index'] . '\'';
		break;
	}

	if (($str)) {
		$ss = 1;
	}

	echo $ss;
	break;

case 'getBarang':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '                <td>No.</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                </tr><tbody>' . "\r\n" . '                ';

	if ($_POST['nmBrg'] == '') {
		exit('Error:Nama barang tidak Boleh kosong');
	}

	if (strlen($_POST['nmBrg']) < 4) {
		exit('Error:Nama barang min 3 Char');
	}

	$sLoad = 'select kodebarang,namabarang,satuan from ' . $dbname . '.log_5masterbarang where (kodebarang like \'%' . $_POST['nmBrg'] . '%\'' . "\r\n" . '            or namabarang like \'%' . $_POST['nmBrg'] . '%\')';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent onclick="setData(\'' . $res['kodebarang'] . '\',\'' . $res['namabarang'] . '\')">';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $res['kodebarang'] . '</td>';
		$tab .= '<td>' . $res['namabarang'] . '</td>';
		$tab .= '<td>' . $res['satuan'] . '</td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'updateVhc':
	$ss = 1;
	$sRupiah = 'select distinct rpperjam from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi' . "\r\n" . '                      where tahunbudget=\'' . $_POST['thnBudget3'] . '\' and kodetraksi=\'' . $_POST['kdTraksi'] . '\' and kodevhc=\'' . $_POST['kdVhc'] . '\'';

	#exit(mysql_error($conn));
	($qRupiah = mysql_query($sRupiah)) || true;
	$rRupiah = mysql_fetch_assoc($qRupiah);
	$sUpdate = 'update  ' . $dbname . '.bgt_budget set rupiah=jumlah*' . $rRupiah['rpperjam'] . "\r\n" . '                      where kodevhc=\'' . $_POST['kdVhc'] . '\' and tahunbudget=\'' . $_POST['thnBudget3'] . '\'' . "\r\n" . '                      and tipebudget!=\'TRK\';';

	if (!mysql_query($sUpdate)) {
		#exit(mysql_error($conn));
	}
	else {
		echo $ss;
	}

	break;

case 'getBrg':
	$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sKegt = 'select distinct kodebarang from ' . $dbname . '.bgt_budget' . "\r\n" . '                    where tahunbudget=\'' . $thnBudget . '\' and kodeorg like \'' . $unitId . '%\'' . "\r\n" . '                    and kegiatan=\'' . $kegId . '\' and kodebudget like \'M%\'';

	#exit(mysql_error($conn));
	($qKegt = mysql_query($sKegt)) || true;

	while ($rkegt = mysql_fetch_assoc($qKegt)) {
		$opt .= '<option value=\'' . $rkegt['kodebarang'] . '\'>' . $optNmBrg[$rkegt['kodebarang']] . '</option>';
	}

	echo $opt;
	break;

case 'getBrgRev':
	if ($kdBgt != 'UMUM') {
		$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$sKegt = 'select distinct kodebarang from ' . $dbname . '.bgt_budget' . "\r\n" . '                        where tahunbudget=\'' . $thnBudget . '\' and kodeorg like \'' . $unitId . '%\'' . "\r\n" . '                        and kegiatan=\'' . $kegId . '\' and kodebudget like \'' . $kdBgt . '%\'';

		#exit(mysql_error($conn));
		($qKegt = mysql_query($sKegt)) || true;
		$row = mysql_num_rows($qKegt);

		while ($rkegt = mysql_fetch_assoc($qKegt)) {
			$opt .= '<option value=\'' . $rkegt['kodebarang'] . '\'>' . $optNmBrg[$rkegt['kodebarang']] . '</option>';
		}
	}
	else {
		$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

		if ($_SESSION['language'] == 'ID') {
			$uu = 'namaakun';
		}
		else {
			$uu = 'namaakun1 as namaakun';
		}

		$sKegt = 'select noakun,' . $uu . ' from ' . $dbname . '.keu_5akun' . "\r\n" . '                       where detail=1 and tipeakun = \'Biaya\' order by noakun';

		#exit(mysql_error($conn));
		($qKegt = mysql_query($sKegt)) || true;
		$row = mysql_num_rows($qKegt);

		while ($rkegt = mysql_fetch_assoc($qKegt)) {
			$opt .= '<option value=\'' . $rkegt['noakun'] . '\'>' . $rkegt['noakun'] . ' - ' . $rkegt['namaakun'] . '</option>';
		}
	}

	echo $row . '###' . $opt;
	break;
}

list() = $_POST;
$sCek = 'select jumlah,rupiah from ' . $dbname . '.bgt_budget where kunci=\'' . $idx . '\'';

#exit(mysql_error($conn));
($qCek = mysql_query($sCek)) || true;
$rCek = mysql_fetch_assoc($qCek);
$dt = 'select *,sum(fis01+fis02+fis03+fis04+fis05+fis06+fis07+fis08+fis09+fis10+fis11+fis12) as totfisik,' . "\r\n" . '        sum(rp01+rp02+rp03+rp04+rp05+rp06+rp07+rp08+rp09+rp10+rp11+rp12) as totrupiah' . "\r\n" . '        from ' . $dbname . '.bgt_distribusi where kunci=\'' . $idx . '\'';

#exit(mysql_error($conn));
($qdt = mysql_query($dt)) || true;
$rdt = mysql_fetch_assoc($qdt);

if ($rdt['totrupiah'] != 0) {
	@$fis01 = ($rdt['fis01'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis02 = ($rdt['fis02'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis03 = ($rdt['fis03'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis04 = ($rdt['fis04'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis05 = ($rdt['fis05'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis06 = ($rdt['fis06'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis07 = ($rdt['fis07'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis08 = ($rdt['fis08'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis09 = ($rdt['fis09'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis10 = ($rdt['fis10'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis11 = ($rdt['fis11'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$fis12 = ($rdt['fis12'] / $rdt['totfisik']) * $rCek['jumlah'];
	@$rp01 = ($rdt['rp01'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp02 = ($rdt['rp02'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp03 = ($rdt['rp03'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp04 = ($rdt['rp04'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp05 = ($rdt['rp05'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp06 = ($rdt['rp06'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp07 = ($rdt['rp07'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp08 = ($rdt['rp08'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp09 = ($rdt['rp09'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp10 = ($rdt['rp10'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp11 = ($rdt['rp11'] / $rdt['totrupiah']) * $rCek['rupiah'];
	@$rp12 = ($rdt['rp12'] / $rdt['totrupiah']) * $rCek['rupiah'];
	$strupdate = 'update ' . $dbname . '.bgt_distribusi set' . "\r\n" . '                    fis01=' . $fis01 . ',' . "\r\n" . '                    fis02=' . $fis01 . ',' . "\r\n" . '                    fis03=' . $fis03 . ',' . "\r\n" . '                    fis04=' . $fis04 . ',' . "\r\n" . '                    fis05=' . $fis05 . ',' . "\r\n" . '                    fis06=' . $fis06 . ',' . "\r\n" . '                    fis07=' . $fis07 . ',' . "\r\n" . '                    fis08=' . $fis08 . ',' . "\r\n" . '                    fis09=' . $fis09 . ',' . "\r\n" . '                    fis10=' . $fis10 . ',' . "\r\n" . '                    fis11=' . $fis11 . ',' . "\r\n" . '                    fis12=' . $fis12 . ',' . "\r\n" . '                    rp01=' . $rp01 . ',' . "\r\n" . '                    rp02=' . $rp02 . ',' . "\r\n" . '                    rp03=' . $rp03 . ',' . "\r\n" . '                    rp04=' . $rp04 . ',' . "\r\n" . '                    rp05=' . $rp05 . ',' . "\r\n" . '                    rp06=' . $rp06 . ',' . "\r\n" . '                    rp07=' . $rp07 . ',' . "\r\n" . '                    rp08=' . $rp08 . ',' . "\r\n" . '                    rp09=' . $rp09 . ',' . "\r\n" . '                    rp10=' . $rp10 . ',' . "\r\n" . '                    rp11=' . $rp11 . ',' . "\r\n" . '                    rp12=' . $rp12 . "\r\n" . '                    where kunci=\'' . $idx . '\'' . "\r\n" . '                       ';
	mysql_query($strupdate);
}

?>
