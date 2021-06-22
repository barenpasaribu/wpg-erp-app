<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['kdUnitCst'] == '' ? $kodeOrg = $_GET['kdUnitCst'] : $kodeOrg = $_POST['kdUnitCst'];
$_POST['thnBudgetCst'] == '' ? $thnBudget = $_GET['thnBudgetCst'] : $thnBudget = $_POST['thnBudgetCst'];
$_POST['noakun'] == '' ? $noakun = $_GET['noakun'] : $noakun = $_POST['noakun'];
$where = ' kodeunit=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\' and thntnm in (select distinct thntnm from ' . $dbname . '.bgt_budget_kebun_perakun_vw where  kodeorg=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'  ) ';
$sSum = 'select sum(jlhkg) as ton from ' . $dbname . '.bgt_produksi_afdeling where ' . $where . '';

#exit(mysql_error($conn));
($qSum = mysql_query($sSum)) || true;
$rSum = mysql_fetch_assoc($qSum);
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optBrng = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$sNoakun = 'select distinct kegiatan from ' . $dbname . '.bgt_budget_kegiatan_vw where ' . "\r\n" . '          tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' and tahunbudget=\'' . $thnBudget . '\'' . "\r\n" . '          and afdeling like \'' . $kodeOrg . '%\' order by kegiatan asc';

exit(mysql_error($sNoakun));
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	$listNoakun[] = $rNoakun['kegiatan'];
}

$jmlBaris = count($listNoakun);
$sSdm = 'select tahunbudget, afdeling, tipebudget, kodebudget, kegiatan,sum(rupiah) as rupiah, namakegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '       where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and (kodebudget like \'SDM%\' or kodebudget like \'SUPERVISI\') and kodebudget!=\'UMUM\' group by kegiatan order by kegiatan asc';

exit(mysql_error($sSdm));
($qSdm = mysql_query($sSdm)) || true;

while ($rSdm = mysql_fetch_assoc($qSdm)) {
	$dataKeg[$rSdm['tahunbudget']][$rSdm['kegiatan']][$rSdm['kepalaAkn']][sdm] = $rSdm['rupiah'];
}

$sSdm2 = 'select tahunbudget, sum(rupiah) as rupiah,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and (kodebudget like \'SDM%\' or kodebudget like \'SUPERVISI\') and kodebudget!=\'UMUM\' group by kepalaAkn order by kepalaAkn asc';

exit(mysql_error($sSdm2));
($qSdm2 = mysql_query($sSdm2)) || true;

while ($rSdm2 = mysql_fetch_assoc($qSdm2)) {
	$totalKplaSDM[$rSdm2['tahunbudget']][$rSdm2['kepalaAkn']][sdm] = $rSdm2['rupiah'];
}

$sSdm = 'select tahunbudget, afdeling, tipebudget, kodebudget, kegiatan,sum(rupiah) as rupiah, namakegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and substr(kodebudget,1,1)=\'M\' and kodebudget!=\'UMUM\' group by kegiatan order by kegiatan asc';

exit(mysql_error($sSdm));
($qSdm = mysql_query($sSdm)) || true;

while ($rSdm = mysql_fetch_assoc($qSdm)) {
	$dataKeg[$rSdm['tahunbudget']][$rSdm['kegiatan']][$rSdm['kepalaAkn']][mat] = $rSdm['rupiah'];
}

$sSdm2 = 'select tahunbudget, sum(rupiah) as rupiah,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and substr(kodebudget,1,1)=\'M\' and kodebudget!=\'UMUM\' group by kepalaAkn order by kepalaAkn asc';

exit(mysql_error($sSdm2));
($qSdm2 = mysql_query($sSdm2)) || true;

while ($rSdm2 = mysql_fetch_assoc($qSdm2)) {
	$totalKplaSDM[$rSdm2['tahunbudget']][$rSdm2['kepalaAkn']][mat] = $rSdm2['rupiah'];
}

$sSdm = 'select tahunbudget, afdeling, tipebudget, kodebudget, kegiatan,sum(rupiah) as rupiah, namakegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'TOOL%\' and kodebudget!=\'UMUM\' group by kegiatan order by kegiatan asc';

exit(mysql_error($sSdm));
($qSdm = mysql_query($sSdm)) || true;

while ($rSdm = mysql_fetch_assoc($qSdm)) {
	$dataKeg[$rSdm['tahunbudget']][$rSdm['kegiatan']][$rSdm['kepalaAkn']] += tool;
}

$sSdm2 = 'select tahunbudget, sum(rupiah) as rupiah,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'TOOL%\' and kodebudget!=\'UMUM\' group by kepalaAkn order by kepalaAkn asc';

exit(mysql_error($sSdm2));
($qSdm2 = mysql_query($sSdm2)) || true;

while ($rSdm2 = mysql_fetch_assoc($qSdm2)) {
	$totalKplaSDM[$rSdm2['tahunbudget']][$rSdm2['kepalaAkn']][tool] = $rSdm2['rupiah'];
}

$sSdm = 'select tahunbudget, afdeling, tipebudget, kodebudget, kegiatan,sum(rupiah) as rupiah, namakegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'VHC%\' and kodebudget!=\'UMUM\' group by kegiatan order by kegiatan asc';

exit(mysql_error($sSdm));
($qSdm = mysql_query($sSdm)) || true;

while ($rSdm = mysql_fetch_assoc($qSdm)) {
	$dataKeg[$rSdm['tahunbudget']][$rSdm['kegiatan']][$rSdm['kepalaAkn']] += vhc;
}

$sSdm2 = 'select tahunbudget, sum(rupiah) as rupiah,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'VHC%\' and kodebudget!=\'UMUM\' group by kepalaAkn order by kepalaAkn asc';

exit(mysql_error($sSdm2));
($qSdm2 = mysql_query($sSdm2)) || true;

while ($rSdm2 = mysql_fetch_assoc($qSdm2)) {
	$totalKplaSDM[$rSdm2['tahunbudget']][$rSdm2['kepalaAkn']][vhc] = $rSdm2['rupiah'];
}

$sSdm = 'select tahunbudget, afdeling, tipebudget, kodebudget, kegiatan,sum(rupiah) as rupiah, namakegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'KONTRAK%\' and kodebudget!=\'UMUM\' group by kegiatan order by kegiatan asc';

exit(mysql_error($sSdm));
($qSdm = mysql_query($sSdm)) || true;

while ($rSdm = mysql_fetch_assoc($qSdm)) {
	$dataKeg[$rSdm['tahunbudget']][$rSdm['kegiatan']][$rSdm['kepalaAkn']] += kntrk;
	$totalKplaSDM[$rSdm['tahunbudget']][$rSdm['kepalaAkn']] += kntrk;
}

$sSdm2 = 'select tahunbudget, sum(rupiah) as rupiah,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw' . "\r\n" . '    where afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and kodebudget like \'KONTRAK%\'  and kodebudget!=\'UMUM\' group by kepalaAkn order by kepalaAkn asc';

exit(mysql_error($sSdm2));
($qSdm2 = mysql_query($sSdm2)) || true;

while ($rSdm2 = mysql_fetch_assoc($qSdm2)) {
	$totalKplaSDM[$rSdm2['tahunbudget']][$rSdm2['kepalaAkn']][kntrk] = $rSdm2['rupiah'];
}

$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and statusblok=\'TM\' and tahunbudget=\'' . $thnBudget . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$ttlLuastm += $bar->luas;
}

$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and statusblok =\'TBM\' and tahunbudget=\'' . $thnBudget . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$ttlLuastbm += $bar->luas;
}

$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and statusblok in (\'TB\') and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '      group by thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuasLc[$thnBudget] += $bar->thntnm;
	$ttlLuasLc += $bar->luas;
}

$str = 'select sum(pokokthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and statusblok =\'BBT\' and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '      group by thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuasPkk[$thnBudget] += $bar->thntnm;
	$ttlLuasPkk += $bar->luas;
}

$sTotAkun = 'select sum(rupiah) as total,substr(kegiatan,1,3) as akunkpla from ' . $dbname . '.bgt_budget_kegiatan_vw  where  afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\'  group by akunkpla';

exit(mysql_error($sTotAkun));
($qTotAkun = mysql_query($sTotAkun)) || true;

while ($rTotAkun = mysql_fetch_assoc($qTotAkun)) {
	if (substr($rTotAkun['akunkpla'], 0, 1) != '6') {
		$totRupiah[$thnBudget][$rTotAkun['akunkpla']] = $rTotAkun['total'];
	}
	else {
		$rTotAkun['akunkpla'] = substr($rTotAkun['akunkpla'], 0, 1);
		$totRupiah[$thnBudget] += $rTotAkun['akunkpla'];
	}
}

$sTotAkun2 = 'select sum(rupiah) as total,kegiatan,substr(kegiatan,1,3) as kepalaAkn from ' . $dbname . '.bgt_budget_kegiatan_vw  where  afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\'  group by kegiatan';

exit(mysql_error($sTotAkun2));
($qTotAkun2 = mysql_query($sTotAkun2)) || true;

while ($rTotAkun2 = mysql_fetch_assoc($qTotAkun2)) {
	$totRupiahKegiatan[$thnBudget][$rTotAkun2['kegiatan']][$rTotAkun2['kepalaAkn']] = $rTotAkun2['total'];
}

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab = '<table>' . "\r\n" . ' <tr><td colspan=5 align=left><font size=5>' . strtoupper($_SESSION['lang']['lapLangsung']) . '</font></td></tr> ' . "\r\n" . ' <tr><td colspan=5 align=left>' . $optNm[$kodeOrg] . '</td></tr>   ' . "\r\n" . ' <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td colspan=2 align=left>' . $thnBudget . '</td></tr>   ' . "\r\n" . ' </table>';
}
else {
	$bg = ' ';
	$brdr = 0;
}

if (($kodeOrg == '') || ($thnBudget == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

$arrLang = array($_SESSION['lang']['sdm'], $_SESSION['lang']['material'], $_SESSION['lang']['peralatan'], $_SESSION['lang']['kndran'], $_SESSION['lang']['kontrak']);
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable width=1800><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td  rowspan=5 valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['noakun'] . '</td>';
$tab .= '<td  rowspan=5 valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['namakegiatan'] . '</td>';
$tab .= '<td colspan=\'2\'  align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td>';
$dtLang = 0;

while ($dtLang <= 4) {
	$tab .= '<td colspan=\'2\' rowspan=\'4\' align=center ' . $bg . '>' . $arrLang[$dtLang] . '</td>';
	++$dtLang;
}

@$hslBagi = $rSum['ton'] / 1000 / $ttlLuastm;
$tab .= '<tr><td align=right ' . $bg . '>TM=' . number_format($ttlLuastm, 2) . ' TBM=' . number_format($ttlLuastbm, 2) . '</td><td ' . $bg . '>Ha</td></tr>';
$tab .= '<tr><td align=right ' . $bg . '>' . number_format($rSum['ton'], 2) . '</td><td ' . $bg . '>Kg</td></tr>';
$tab .= '<tr><td align=right ' . $bg . '>' . number_format($hslBagi, 2) . '</td><td ' . $bg . '>Ton/Ha</td></tr>';
$tab .= '<tr>';
$tdList = 1;

while ($tdList <= 6) {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td><td align=center ' . $bg . '>Rp/Ha</td>';
	++$tdList;
}

$tab .= '</tr>';
$tab .= '</thead><tbody>';

foreach ($listNoakun as $barisNoakun) {
	$new = substr($barisNoakun, 0, 3);

	if (($ktKrgng != '') && ($ktKrgng != $new)) {
		if (substr($ktKrgng, 0, 1) != 6) {
			if ($ktKrgng == '126') {
				@$totBagi = $totRupiah[$thnBudget][$ktKrgng] / $ttlLuastbm;
				@$totKepla[$thnBudget][$ktKrgng][sdm] = $totalKplaSDM[$thnBudget][$ktKrgng][sdm] / $ttlLuastbm;
				@$totKepla[$thnBudget][$ktKrgng][mat] = $totalKplaSDM[$thnBudget][$ktKrgng][mat] / $ttlLuastbm;
				@$totKepla[$thnBudget][$ktKrgng][tool] = $totalKplaSDM[$thnBudget][$ktKrgng][tool] / $ttlLuastbm;
				@$totKepla[$thnBudget][$ktKrgng][vhc] = $totalKplaSDM[$thnBudget][$ktKrgng][vhc] / $ttlLuastbm;
				@$totKepla[$thnBudget][$ktKrgng][kntrk] = $totalKplaSDM[$thnBudget][$ktKrgng][kntrk] / $ttlLuastbm;
				$pembagi[$ktKrgng] = $ttlLuastbm;
				$tab .= '<thead><tr class=rowheader><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' TBM</td>';
				$tab .= '<td align=right>' . number_format($totRupiah[$thnBudget][$ktKrgng], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totBagi, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepla[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][mat], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepla[$thnBudget][$ktKrgng][mat], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][tool], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepla[$thnBudget][$ktKrgng][tool], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepla[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepla[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
				$tab .= '</tr></thead>';
			}
			else if ($ktKrgng == '128') {
				@$totBagi = $ttlLuasPkk;
				$ttlLuastbm = $ttlLuasPkk;
				$tab .= '<thead><tr class=rowheader><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' BIBITAN</td>';
				$tab .= '<td align=right>' . number_format($totRupiah[$thnBudget][$ktKrgng], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totBagi, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
				$tab .= '<td align=right>' . number_format(@$ttlLuastbm, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][mat], 2) . '</td>';
				$tab .= '<td align=right>' . number_format(@$ttlLuastbm, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][tool], 2) . '</td>';
				$tab .= '<td align=right>' . number_format(@$ttlLuastbm, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
				$tab .= '<td align=right>' . number_format(@$ttlLuastbm, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
				$tab .= '<td align=right>' . number_format(@$ttlLuastbm, 2) . '</td>';
				$tab .= '</tr></thead>';
			}
		}
		else {
			$ktKrgng = substr($ktKrgng, 0, 1);
			$sTotal = 'select distinct kegiatan from ' . $dbname . '. bgt_budget_kegiatan_vw  where  afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and substring(kegiatan,1,1)=\'6\'';

			exit(mysql_error($sTotal));
			($qTotal = mysql_query($sTotal)) || true;
			$rTotal = mysql_num_rows($qTotal);
			$awal += 1;

			if ($awal == $rTotal) {
				@$totBagi = $totRupiah[$thnBudget][$ktKrgng] / $ttlLuastm;
				$ttlLuastbm = $ttlLuastm;
				@$totKepalas[$thnBudget][$ktKrgng][sdm] = $totalKplaSDM[$thnBudget][$ktKrgng][sdm] / $ttlLuastbm;
				@$totKepalas[$thnBudget][$ktKrgng][mat] = $totalKplaSDM[$thnBudget][$ktKrgng][mat] / $ttlLuastbm;
				@$totKepalas[$thnBudget][$ktKrgng][tool] = $totalKplaSDM[$thnBudget][$ktKrgng][tool] / $ttlLuastbm;
				@$totKepalas[$thnBudget][$ktKrgng][vhc] = $totalKplaSDM[$thnBudget][$ktKrgng][vhc] / $ttlLuastbm;
				@$totKepalas[$thnBudget][$ktKrgng][kntrk] = $totalKplaSDM[$thnBudget][$ktKrgng][kntrk] / $ttlLuastbm;
				$tab .= '<thead><tr class=rowheader><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' TM</td>';
				$tab .= '<td align=right>' . number_format($totRupiah[$thnBudget][$ktKrgng], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totBagi, 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepalas[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][mat], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepalas[$thnBudget][$ktKrgng][mat], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][tool], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepalas[$thnBudget][$ktKrgng][tool], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepalas[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
				$tab .= '<td align=right>' . number_format($totKepalas[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
				$tab .= '</tr></thead>';
				$awal = 0;
			}
		}
	}

	if (substr($new, 0, 1) != 6) {
		if ($new == '126') {
			$kwe = substr($barisNoakun, 0, 5);

			if ($kwe <= '12605') {
				$pembagi[$new] = $ttlLuasLc;
			}
			else {
				$pembagi[$new] = $ttlLuastbm;
			}
		}
		else if ($new == '128') {
			$pembagi[$new] = $ttlLuasPkk;
		}
	}
	else {
		$pembagi[$new] = $ttlLuastm;
	}

	@$kegHa[$thnBudget][$barisNoakun][$new] = $totRupiahKegiatan[$thnBudget][$barisNoakun][$new] / $pembagi[$new];
	@$kegSdm[$thnBudget][$barisNoakun][$new] = $dataKeg[$thnBudget][$barisNoakun][$new][sdm] / $pembagi[$new];
	@$kegMat[$thnBudget][$barisNoakun][$new] = $dataKeg[$thnBudget][$barisNoakun][$new][mat] / $pembagi[$new];
	@$kegTool[$thnBudget][$barisNoakun][$new] = $dataKeg[$thnBudget][$barisNoakun][$new][tool] / $pembagi[$new];
	@$kegVhc[$thnBudget][$barisNoakun][$new] = $dataKeg[$thnBudget][$barisNoakun][$new][vhc] / $pembagi[$new];
	@$kegKntrak[$thnBudget][$barisNoakun][$new] = $dataKeg[$thnBudget][$barisNoakun][$new][kntrk] / $pembagi[$new];
	$tab .= '<tr class=\'rowcontent\'>';
	$tab .= '<td><b>' . $barisNoakun . '</b></td><td><b>' . $optKegiatan[$barisNoakun] . '</b></td>';
	$tab .= '<td align=right>' . number_format($totRupiahKegiatan[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegHa[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dataKeg[$thnBudget][$barisNoakun][$new][sdm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegSdm[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dataKeg[$thnBudget][$barisNoakun][$new][mat], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegMat[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dataKeg[$thnBudget][$barisNoakun][$new][tool], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegTool[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dataKeg[$thnBudget][$barisNoakun][$new][vhc], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegVhc[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dataKeg[$thnBudget][$barisNoakun][$new][kntrk], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($kegKntrak[$thnBudget][$barisNoakun][$new], 2) . '</td>';
	$tab .= '</tr>';
	$grnTotKeg += $totRupiahKegiatan[$thnBudget][$barisNoakun][$new];
	$grnTotKegha += $kegHa[$thnBudget][$barisNoakun][$new];
	$grnTotKegSdm += $dataKeg[$thnBudget][$barisNoakun][$new][sdm];
	$grnTotKeghaSdm += $kegSdm[$thnBudget][$barisNoakun][$new];
	$grnTotKegMat += $dataKeg[$thnBudget][$barisNoakun][$new][mat];
	$grnTotKeghaMat += $kegMat[$thnBudget][$barisNoakun][$new];
	$grnTotKegTool += $dataKeg[$thnBudget][$barisNoakun][$new][tool];
	$grnTotKeghaTool += $kegTool[$thnBudget][$barisNoakun][$new];
	$grnTotKegVhc += $dataKeg[$thnBudget][$barisNoakun][$new][vhc];
	$grnTotKeghaVhc += $kegVhc[$thnBudget][$barisNoakun][$new];
	$grnTotKegKntrak += $dataKeg[$thnBudget][$barisNoakun][$new][kntrk];
	$grnTotKeghaKntrak += $kegKntrak[$thnBudget][$barisNoakun][$new];

	if (substr($barisNoakun, 0, 1) == '6') {
		$ktKrgng = substr($barisNoakun, 0, 1);
	}
	else {
		$ktKrgng = substr($barisNoakun, 0, 3);
	}
}

$tab .= '<thead>';

if ($ktKrgng == '126') {
	@$totBagi = $totRupiah[$thnBudget][$ktKrgng] / $ttlLuastbm;
	$ttlLuastbm = $ttlLuastbm;
	$tab .= '<tr class=\'rowheader\'><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' TBM</td>';
}
else if ($ktKrgng == '128') {
	@$totBagi = $totRupiah[$thnBudget][$ktKrgng] / $ttlLuasPkk;
	$ttlLuastbm = $ttlLuasPkk;
	$tab .= '<tr class=\'rowheader\'><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' BIBITAN</td>';
}
else if ($ktKrgng == '6') {
	@$totBagi = $totRupiah[$thnBudget][$ktKrgng] / $ttlLuastm;
	$ttlLuastbm = $ttlLuastm;
	$tab .= '<tr class=\'rowheader\'><td align=right colspan=2>' . $_SESSION['lang']['total'] . ' TM</td>';
}

@$bagiTotalSdm = $totalKplaSDM[$thnBudget][$ktKrgng][sdm] / $ttlLuastbm;
@$bagiTotalMat = $totalKplaSDM[$thnBudget][$ktKrgng][mat] / $ttlLuastbm;
@$bagiTotalTool = $totalKplaSDM[$thnBudget][$ktKrgng][tool] / $ttlLuastbm;
@$bagiTotalVhc = $totalKplaSDM[$thnBudget][$ktKrgng][vhc] / $ttlLuastbm;
@$bagiTotalKntrk = $totalKplaSDM[$thnBudget][$ktKrgng][kntrk] / $ttlLuastbm;
$tab .= '<td align=right>' . number_format($totRupiah[$thnBudget][$ktKrgng], 2) . '</td>';
$tab .= '<td align=right>' . number_format($totBagi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][sdm], 2) . '</td>';
$tab .= '<td align=right>' . number_format($bagiTotalSdm, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][mat], 2) . '</td>';
$tab .= '<td align=right>' . number_format($bagiTotalMat, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][tool], 2) . '</td>';
$tab .= '<td align=right>' . number_format($bagiTotalTool, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][vhc], 2) . '</td>';
$tab .= '<td align=right>' . number_format($bagiTotalVhc, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalKplaSDM[$thnBudget][$ktKrgng][kntrk], 2) . '</td>';
$tab .= '<td align=right>' . number_format($bagiTotalKntrk, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=\'rowheader\'>';
$tab .= '<td colspan=2 align=right>' . $_SESSION['lang']['grnd_total'] . '</b></td>';
$tab .= '<td align=right>' . number_format($grnTotKeg, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegha, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegSdm, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKeghaSdm, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegMat, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKeghaMat, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegTool, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKeghaTool, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegVhc, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKeghaVhc, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKegKntrak, 2) . '</td>';
$tab .= '<td align=right>' . number_format($grnTotKeghaKntrak, 2) . '</td>';
$tab .= '</tr></thead>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lapKebunByLngsng_cst_elmnt_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                        </script>';
	break;

case 'pdf':
	if (($kodeOrg == '') || ($thnBudget == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $dbname;
			global $optAkun;
			global $optKegiatan;
			global $totRupiahKegiatan;
			global $totRupiah;
			global $ttlLuastbm;
			global $arrLang;
			global $rSum;
			global $kodeOrg;
			global $thnBudget;
			global $awal;
			global $optNm;
			$sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';

			#exit(mysql_error());
			($qAlamat = mysql_query($sAlmat)) || true;
			$rAlamat = mysql_fetch_assoc($qAlamat);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 10;

			if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
				$path = 'images/SSP_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
				$path = 'images/MI_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
				$path = 'images/HS_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
				$path = 'images/BM_logo.jpg';
			}

			$this->Image($path, $this->lMargin, $this->tMargin, 70);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $rAlamat['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 11);
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['lapLangsung']), 0, 1, 'C');
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$kodeOrg], 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell(850, $height, $_SESSION['lang']['tanggal'], 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, date('d-m-Y H:i'), 0, 1, 'R');
			$this->Cell(850, $height, $_SESSION['lang']['page'], 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, $this->PageNo(), 0, 1, 'R');
			$this->Cell(850, $height, 'User', 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, $_SESSION['standard']['username'], 0, 1, 'R');
			$this->Ln();
			$this->Ln();
			$height = 50;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(58, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
			$this->Cell(150, $height, $_SESSION['lang']['namakegiatan'], 1, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 5);
			$this->Cell(100, 10, $_SESSION['lang']['total'], 1, 1, 'C', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(100, 10, 'TM=' . number_format($ttlLuastm, 2) . ' TBM=' . number_format($ttlLuastbm, 2) . ' Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(50, 10, number_format($rSum['ton'], 2), 1, 0, 'R', 1);
			$this->Cell(50, 10, 'Kg', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			@$hslBagi = $rSum['ton'] / 1000 / $ttlLuastm;
			$this->Cell(50, 10, number_format($hslBagi, 2), 1, 0, 'R', 1);
			$this->Cell(50, 10, 'Ton/Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(50, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
			$this->Cell(50, 10, 'RP/Ha', 1, 1, 'L', 1);
			$br = 308;
			$ypertama = $this->GetY();
			$dtLang = 0;

			while ($dtLang <= 4) {
				$this->SetY($ypertama - 50);
				$xPertama = $this->GetX();
				$this->SetX($xPertama + $br);
				$this->Cell(100, 40, $arrLang[$dtLang], 1, 1, 'C', 1);
				$xPertama = $this->GetX();
				$this->SetX($xPertama + $br);
				$this->Cell(50, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
				$this->Cell(50, 10, 'RP/Ha', 1, 1, 'L', 1);
				$br += 100;
				++$dtLang;
			}
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('L', 'pt', 'LEGAL');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 5);
	$awal = 0;

	foreach ($listNoakun as $barisNoakun) {
		$new2 = substr($barisNoakun, 0, 3);

		if (($ktKrgng2 != '') && ($ktKrgng2 != $new2)) {
			$pdf->SetFont('Arial', 'B', 5);
			$xPertama = $pdf->GetX();
			$pdf->SetX($xPertama);

			if (substr($ktKrgng2, 0, 1) != '6') {
				if ($ktKrgng2 == '126') {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TBM', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastbm;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
				}
				else if ($ktKrgng2 == '128') {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' BIBITAN', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = $ttlLuasPkk;
					$ttlLuastbm = $ttlLuasPkk;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
				}
			}
			else {
				$ktKrgng2 = substr($ktKrgng2, 0, 1);
				$sTotal = 'select distinct kegiatan from ' . $dbname . '. bgt_budget_kegiatan_vw  where  afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and substring(kegiatan,1,1)=\'6\'';

				exit(mysql_error($sTotal));
				($qTotal = mysql_query($sTotal)) || true;
				$rTotal = mysql_num_rows($qTotal);
				$awal += 1;

				if ($awal == $rTotal) {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TM', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastm;
					$ttlLuastbm = $ttlLuastm;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
					$awal = 0;
				}
			}
		}

		@$kegHa = $totRupiahKegiatan[$thnBudget][$barisNoakun] / $ttlLuastbm;
		@$kegSdm = $dataKeg[$thnBudget][$barisNoakun][sdm] / $ttlLuastbm;
		@$kegMat = $dataKeg[$thnBudget][$barisNoakun][mat] / $ttlLuastbm;
		@$kegTool = $dataKeg[$thnBudget][$barisNoakun][tool] / $ttlLuastbm;
		@$kegVhc = $dataKeg[$thnBudget][$barisNoakun][vhc] / $ttlLuastbm;
		@$kegKntrak = $dataKeg[$thnBudget][$barisNoakun][kntrk] / $ttlLuastbm;
		$pdf->Cell(58, $height, $barisNoakun, 1, 0, 'L', 1);
		$pdf->Cell(150, $height, $optKegiatan[$barisNoakun], 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($totRupiahKegiatan[$thnBudget][$barisNoakun], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegHa, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][sdm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegSdm, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][mat], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegMat, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][tool], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegTool, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][vhc], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegVhc, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][kntrk], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegKntrak, 2), 1, 1, 'L', 1);

		if (substr($barisNoakun, 0, 1) == '6') {
			$ktKrgng2 = substr($barisNoakun, 0, 1);
		}
		else {
			$ktKrgng2 = substr($barisNoakun, 0, 3);
		}

		$grnTotKeg2 += $totRupiahKegiatan[$thnBudget][$barisNoakun];
		$grnTotKegha2 += $kegHa;
		$grnTotKegSdm2 += $dataKeg[$thnBudget][$barisNoakun][sdm];
		$grnTotKeghaSdm2 += $kegSdm;
		$grnTotKegMat2 += $dataKeg[$thnBudget][$barisNoakun][mat];
		$grnTotKeghaMat2 += $kegMat;
		$grnTotKegTool2 += $dataKeg[$thnBudget][$barisNoakun][tool];
		$grnTotKeghaTool2 += $kegTool;
		$grnTotKegVhc2 += $dataKeg[$thnBudget][$barisNoakun][vhc];
		$grnTotKeghaVhc2 += $kegVhc;
		$grnTotKegKntrak2 += $dataKeg[$thnBudget][$barisNoakun][kntrk];
		$grnTotKeghaKntrak2 += $kegKntrak;
	}

	if ($ktKrgng2 == '126') {
		$ttlLuastbm = $ttlLuastbm;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TBM', 1, 0, 'R', 1);
	}
	else if ($ktKrgng2 == '128') {
		$ttlLuastbm = $ttlLuasPkk;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' BIBITAN', 1, 0, 'R', 1);
	}
	else if ($ktKrgng2 == '6') {
		$ttlLuastbm = $ttlLuastm;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TM', 1, 0, 'R', 1);
	}

	$xPertama = $pdf->GetX();
	$pdf->SetX($xPertama);
	@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastbm;
	$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
	$pdf->Cell(208, $height, $_SESSION['lang']['grnd_total'], 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeg2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegha2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegSdm2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaSdm2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegMat2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaMat2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegTool2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaTool2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegVhc2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaVhc2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegKntrak2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaKntrak2, 0), 1, 1, 'L', 1);
	$pdf->Output();
	break;
}

?>
