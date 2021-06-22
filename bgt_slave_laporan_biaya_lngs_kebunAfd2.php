<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['kdUnit_afd'] == '' ? $kodeOrg = $_GET['kdUnit_afd'] : $kodeOrg = $_POST['kdUnit_afd'];
$_POST['thnBudget_afd'] == '' ? $thnBudget = $_GET['thnBudget_afd'] : $thnBudget = $_POST['thnBudget_afd'];
$_POST['noakun_afd'] == '' ? $noakun_afd = $_GET['noakun_afd'] : $noakun_afd = $_POST['noakun_afd'];
$where = ' kodeunit=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\' and thntnm in (select distinct thntnm from ' . $dbname . '.bgt_budget_kebun_perblok_vw where  kodeorg like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' order by thntnm desc)';
$sSum = 'select sum(jlhkg) as ton from ' . $dbname . '.bgt_produksi_afdeling where ' . $where . ' and thntnm in (select distinct thntnm from ' . $dbname . '.bgt_budget_kebun_perblok_vw where  kodeorg like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' order by thntnm desc)';

#exit(mysql_error($conn));
($qSum = mysql_query($sSum)) || true;
$rSum = mysql_fetch_assoc($qSum);
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKegiatan = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$sKodeOrg = 'select * from ' . $dbname . '.bgt_produksi_afdeling where  ' . $where . ' order by tahunbudget asc';

#exit(mysql_error($conn));
($qKodeOrg = mysql_query($sKodeOrg)) || true;

while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
	$a += 1;
	$dtJjg[$rKode['tahunbudget']] += $rKode['afdeling'];
	$dtJmlhKg[$rKode['tahunbudget']] += $rKode['afdeling'];
}

$str = 'select sum(hathnini) as luas,left(kodeblok,6) as afdeling from ' . $dbname . '.bgt_blok ' . "\r\n" . '      where kodeblok like \'' . $kodeOrg . '%\' and statusblok=\'TM\' and tahunbudget=\'' . $thnBudget . '\'' . "\r\n" . '      group by afdeling';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuastm[$thnBudget] += $bar->afdeling;
	$ttlLuastm += $bar->luas;
}

$str = 'select sum(hathnini) as luas,left(kodeblok,6) as afdeling from ' . $dbname . '.bgt_blok ' . "\r\n" . '      where kodeblok like \'' . $kodeOrg . '%\' and statusblok in (\'TBM\',\'TB\') and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '      group by afdeling';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuastbm[$thnBudget] += $bar->afdeling;
	$ttlLuastbm += $bar->luas;
}

$ttlLuas = $ttlLuastbm + $ttlLuastm;
$str = 'select sum(pokokthnini) as luas,left(kodeblok,6) as afdeling from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and statusblok =\'BBT\' and tahunbudget=\'' . $thnBudget . '\'' . "\r\n" . '      group by thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuasPkk[$thnBudget] += $bar->thntnm;
	$ttlLuasPkk += $bar->luas;
}

$str = 'select sum(lcthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and lcthnini!=\'\' and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '      group by thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dtJmlhLuasLc[$thnBudget] += $bar->thntnm;
	$ttlLuasLc += $bar->luas;
}

$sThnTnm = 'select distinct left(kodeblok,6) as afdeling  from ' . $dbname . '.bgt_blok where' . "\r\n" . '          kodeblok like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' order by afdeling asc';

#exit(mysql_error());
($qThnTnm = mysql_query($sThnTnm)) || true;

while ($rThnTnm = mysql_fetch_assoc($qThnTnm)) {
	$a += 1;
	$dtThnBudget[$a] = $rThnTnm['afdeling'];
}

$sNoakun = 'select * from ' . $dbname . '.bgt_budget_kegiatan_vw where ' . "\r\n" . '         substring(afdeling,1,4)=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\' ' . "\r\n" . '         and tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' order by noakun asc';

#exit(mysql_error($conn));
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	$lstRupiah[$rNoakun['tahunbudget']][$rNoakun['afdeling']] += $rNoakun['noakun'];
	$totRupiah[$rNoakun['tahunbudget']] += $rNoakun['noakun'];
	$dtThntnm[$rNoakun['tahunbudget']] = $rNoakun['afdeling'];
}

$sNoakun2 = 'select distinct noakun,sum(rupiah) as rupiah  from ' . $dbname . '.bgt_budget_kebun_perakun_vw where kodeorg=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'  group by noakun order by noakun asc';

#exit(mysql_error($conn));
($qNoakun2 = mysql_query($sNoakun2)) || true;

while ($rNoakun2 = mysql_fetch_assoc($qNoakun2)) {
	$dtNoakun[] = $rNoakun2['noakun'];
}

$sNoakunRupiah = 'select distinct substr(noakun,1,5) as aknKpala,sum(rupiah) as  rupiah,tahunbudget from ' . $dbname . '.bgt_budget_kebun_perblok_vw where kodeorg like \'' . $kodeOrg . '%\'  and tahunbudget=\'' . $thnBudget . '\'  group by aknKpala';

#exit(mysql_error($conn));
($qNoakunRupiah = mysql_query($sNoakunRupiah)) || true;

while ($rNoakunRupiah = mysql_fetch_assoc($qNoakunRupiah)) {
	$dtNoakunRup2[$thnBudget] += $rNoakunRupiah['aknKpala'];
}

$sAkunRupiah = 'select distinct substr(noakun,1,5) as aknKpala,sum(rupiah) as  rupiah,tahunbudget,substr(kodeorg,1,6) as thntnm from ' . $dbname . '.bgt_budget_kebun_perblok_vw where kodeorg like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and tipebudget=\'ESTATE\' and kodebudget!=\'UMUM\' group by substr(kodeorg,1,6),aknKpala';

#exit(mysql_error($conn));
($qAkunRupiah = mysql_query($sAkunRupiah)) || true;

while ($rAkunRupiah = mysql_fetch_assoc($qAkunRupiah)) {
	$dtNoakunRup[$rAkunRupiah['tahunbudget']][$rAkunRupiah['thntnm']] += $rAkunRupiah['aknKpala'];
}

$jmlhbrs = count($dtThnBudget);
$colTotal = ($jmlhbrs + 1) * 2;

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

$jmhThn = count($dtNoakun);
if (($jmhThn == 0) || ($jmhThn == '')) {
	exit('Error:Data Kosong');
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable width=1800><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td  rowspan=5 valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['noakun'] . '</td>';
$tab .= '<td  rowspan=5 valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['namakegiatan'] . '</td>';
$tab .= '<td colspan=\'3\'  align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td>';

foreach ($dtThnBudget as $listThn) {
	$tab .= '<td colspan=\'3\'  align=center ' . $bg . '>' . $listThn . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr>';
$tab .= '<td align=right ' . $bg . ' colspan=2>TM=' . number_format($ttlLuastm, 2) . ' TBM=' . number_format($ttlLuastbm, 2) . '</td><td ' . $bg . '>Ha</td>';

foreach ($dtThnBudget as $listThn2) {
	$tab .= '<td align=right ' . $bg . ' colspan=2>TM=' . number_format($dtJmlhLuastm[$thnBudget][$listThn2], 2) . ' TBM=' . number_format($dtJmlhLuastbm[$thnBudget][$listThn2], 2) . '</td><td ' . $bg . '> Ha</td>';
}

$tab .= '</tr>';
$tab .= '<tr>';
$tab .= '<td align=right ' . $bg . ' colspan=2>' . number_format($rSum['ton'], 2) . '</td><td ' . $bg . '>Kg</td>';

foreach ($dtThnBudget as $listThn2) {
	$tab .= '<td align=right ' . $bg . ' colspan=2>' . number_format($dtJmlhKg[$thnBudget][$listThn2], 2) . '</td><td ' . $bg . '>Kg</td>';
}

$tab .= '</tr>';
$kgTotal = $rSum['ton'];
@$hsilBagi = $kgTotal / 1000 / $ttlLuastm;
$tab .= '<tr>';
$tab .= '<td align=right ' . $bg . ' colspan=2>' . number_format($hsilBagi, 2, '.', ',') . '</td><td align=left ' . $bg . '>Ton/Ha</td>';

foreach ($dtThnBudget as $listThn2) {
	@$hslBag[$listThn2] = $dtJmlhKg[$thnBudget][$listThn2] / 1000 / $dtJmlhLuastm[$thnBudget][$listThn2];
	$tab .= '<td align=right ' . $bg . ' colspan=2>' . number_format($hslBag[$listThn2], 2, '.', ',') . '</td><td align=left ' . $bg . '>Ton/Ha</td>';
}

$tab .= '<tr>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['luas'] . '</td><td align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td><td align=center ' . $bg . '>Rp/Ha</td>';

foreach ($dtThnBudget as $listThn2) {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['luas'] . '</td><td align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td><td align=center ' . $bg . '>Rp/Ha</td>';
}

$tab .= '</tr>';
$tab .= '</thead><tbody>';
$awal = 0;

foreach ($dtNoakun as $barisNoakun) {
	if ($ktKrgng != substr($barisNoakun, 0, 5)) {
		$brs = 1;
	}

	if ($brs == 1) {
		$ktKrgng = substr($barisNoakun, 0, 5);

		if ($ktKrgng == '12601') {
			@$hslBagiTot = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuasLc;
			$luastTotal[$ktKrgng] = $ttlLuasLc;
		}
		else if ('12602' <= $ktKrgng) {
			if (substr($ktKrgng, 0, 3) == '126') {
				@$hslBagiTot = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuastbm;
				$luastTotal[$ktKrgng] = $ttlLuastbm;
			}
			else if (substr($ktKrgng, 0, 3) == '128') {
				@$hslBagiTot = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuasPkk;
				$luastTotal[$ktKrgng] = $ttlLuasPkk;
			}
			else if (substr($ktKrgng, 0, 1) == '6') {
				@$hslBagiTot = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuastm;
				$luastTotal[$ktKrgng] = $ttlLuastm;
			}
		}

		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td><b>' . $ktKrgng . '</b></td>';
		$tab .= '<td><b>' . $optKegiatan[$ktKrgng] . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($luastTotal[$ktKrgng], 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($dtNoakunRup2[$thnBudget][$ktKrgng], 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($hslBagiTot, 2) . '</b></td>';

		foreach ($dtThnBudget as $lstThaTnm) {
			if (substr($barisNoakun, 0, 5) == '12601') {
				@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = $dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng] / $dtJmlhLuasLc[$thnBudget][$lstThaTnm];
				$luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng] = $dtJmlhLuasLc[$thnBudget][$lstThaTnm];
				$tab .= '<td align=right><b>' . number_format($luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($hslBagi[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
			}
			else if ('12602' <= substr($barisNoakun, 0, 5)) {
				if (substr($barisNoakun, 0, 3) == '126') {
					@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = $dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng] / $dtJmlhLuastbm[$thnBudget][$lstThaTnm];
					$luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng] = $dtJmlhLuastbm[$thnBudget][$lstThaTnm];
					$tab .= '<td align=right><b>' . number_format($luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($hslBagi[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
				}
				else if (substr($barisNoakun, 0, 1) == '6') {
					@$hslBagi[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)] = $dtNoakunRup[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)] / $dtJmlhLuastm[$thnBudget][$lstThaTnm];
					$luastPerAkun[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)] = $dtJmlhLuastm[$thnBudget][$lstThaTnm];
					$tab .= '<td align=right><b>' . number_format($luastPerAkun[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($dtNoakunRup[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($hslBagi[$thnBudget][$lstThaTnm][substr($barisNoakun, 0, 5)], 2) . '</b></td>';
				}
				else if (substr($barisNoakun, 0, 3) == '128') {
					@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = $dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng] / $dtJmlhLuasPkk[$thnBudget][$lstThaTnm];
					$luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng] = $dtJmlhLuasPkk[$thnBudget][$lstThaTnm];
					$tab .= '<td align=right><b>' . number_format($luastPerAkun[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($hslBagi[$thnBudget][$lstThaTnm][$ktKrgng], 2) . '</b></td>';
				}
			}

			$brs = 0;
			$awal = 1;
		}

		$tab .= '</tr>';
	}

	$arr = 'thnBudget##' . $thnBudget . '##noakun##' . $barisNoakun . '##kdUnit##' . $kodeOrg;
	$tab .= '<tr class=\'rowcontent\' style=\'cursor:pointer;\' onclick="zDetail(event,\'bgt_slave_laporan_biaya_lngs_kebun.php\',\'' . $arr . '\')">';
	$tab .= '<td>' . $barisNoakun . '</td>';
	$tab .= '<td>' . $optKegiatan[$barisNoakun] . '</td>';

	if (substr($barisNoakun, 0, 5) == '12601') {
		@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuasLc;
		$gtlc += $totRupiah[$thnBudget][$barisNoakun];
		@$bagilc += $hasilBagi[$barisNoakun];
		$luastTotalAkn[$barisNoakun] = $ttlLuasLc;
	}
	else if ('12602' <= substr($barisNoakun, 0, 5)) {
		if (substr($barisNoakun, 0, 3) == '126') {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastbm;
			$gttbm += $totRupiah[$thnBudget][$barisNoakun];
			@$bagitbm += $hasilBagi[$barisNoakun];
			$luastTotalAkn[$barisNoakun] = $ttlLuastbm;
		}
		else if ('61' <= substr($barisNoakun, 0, 2)) {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastm;
			$gttm += $totRupiah[$thnBudget][$barisNoakun];
			$bagitm += $hasilBagi[$barisNoakun];
			$luastTotalAkn[$barisNoakun] = $ttlLuastm;
		}
		else if (substr($barisNoakun, 0, 3) == '128') {
			@$hasilBagi[$barisNoakun] = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuasPkk;
			$gtbbt += $totRupiah[$thnBudget][$barisNoakun];
			$bagibbt = $hasilBagi[$barisNoakun];
			$luastTotalAkn[$barisNoakun] = $ttlLuasPkk;
		}
	}

	$tab .= '<td>' . $luastTotalAkn[$barisNoakun] . '</td>';
	$tab .= '<td align=right>' . number_format($totRupiah[$thnBudget][$barisNoakun], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($hasilBagi[$barisNoakun], 2) . '</td>';
	$grndTotal += $totRupiah[$thnBudget][$barisNoakun];
	$grndTotalHsil += $hasilBagi[$barisNoakun];

	foreach ($dtThnBudget as $brsThnBudget) {
		if (substr($barisNoakun, 0, 5) == '12601') {
			@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuasLc[$thnBudget][$brsThnBudget];
			$totalRplc += $brsThnBudget;
			$totalbagilc += $brsThnBudget;
			$luastTotalsat[$brsThnBudget][$barisNoakun] = $dtJmlhLuasLc[$thnBudget][$brsThnBudget];
		}
		else if ('12602' <= substr($barisNoakun, 0, 5)) {
			if (substr($barisNoakun, 0, 3) == '126') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastbm[$thnBudget][$brsThnBudget];
				$totalRptbm += $brsThnBudget;
				$totalbagitbm += $brsThnBudget;
				$luastTotalsat[$brsThnBudget][$barisNoakun] = $dtJmlhLuastbm[$thnBudget][$brsThnBudget];
			}
			else if (substr($barisNoakun, 0, 1) == '6') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastm[$thnBudget][$brsThnBudget];
				$totalRptm += $brsThnBudget;
				$totalbagitm += $brsThnBudget;
				$luastTotalsat[$brsThnBudget][$barisNoakun] = $dtJmlhLuastm[$thnBudget][$brsThnBudget];
			}
			else if (substr($barisNoakun, 0, 3) == '128') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuasPkk[$thnBudget][$brsThnBudget];
				$totalRpbbt += $brsThnBudget;
				$totalbagibbt += $brsThnBudget;
				$luastTotalsat[$brsThnBudget][$barisNoakun] = $dtJmlhLuasPkk[$thnBudget][$brsThnBudget];
			}
		}

		$tab .= '<td align=right>' . number_format($luastTotalsat[$brsThnBudget][$barisNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($hasilBagi2[$brsThnBudget], 2) . '</td>';
	}

	$tab .= '</tr>';
}

$tab .= '<thead>';
$tab .= '<tr class=rowheader><td colspan=2>' . $_SESSION['lang']['total'] . ' TB</td>';
$tab .= '<td>&nbsp;</td><td align=right >' . number_format($gtlc, 2) . '</td>' . "\r\n" . '                      <td align=right>' . number_format($bagilc, 2) . '</td>';

foreach ($dtThnBudget as $brsThnBudget) {
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totalRplc[$brsThnBudget], 2) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($totalbagilc[$brsThnBudget], 2) . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr class=rowheader><td colspan=2>' . $_SESSION['lang']['total'] . ' BBT</td>';
$tab .= '<td>&nbsp;</td><td align=right>' . number_format($gtbbt, 2) . '</td>' . "\r\n" . '                      <td align=right>' . number_format($bagibbt, 2) . '</td>';

foreach ($dtThnBudget as $brsThnBudget) {
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totalRpbbt[$brsThnBudget], 2) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($totalbagibbt[$brsThnBudget], 2) . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr class=rowheader><td colspan=2>' . $_SESSION['lang']['total'] . ' TBM</td>';
$tab .= '<td>&nbsp;</td><td align=right>' . number_format($gttbm, 2) . '</td>' . "\r\n" . '                      <td align=right>' . number_format($bagitbm, 2) . '</td>';

foreach ($dtThnBudget as $brsThnBudget) {
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totalRptbm[$brsThnBudget], 2) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($totalbagitbm[$brsThnBudget], 2) . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr class=rowheader><td colspan=2>' . $_SESSION['lang']['total'] . ' TM</td>';
$tab .= '<td>&nbsp;</td><td align=right>' . number_format($gttm, 2) . '</td>' . "\r\n" . '                      <td align=right>' . number_format($bagitm, 2) . '</td>';

foreach ($dtThnBudget as $brsThnBudget) {
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($totalRptm[$brsThnBudget], 2) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($totalbagitm[$brsThnBudget], 2) . '</td>';
	$grandTotal += $brsThnBudget;
	$bagiGrandTotal += $brsThnBudget;
}

$grndTotal = $gtbbt + $gttbm + $gttm + $gtlc;
$bagigrndTotal = $bagilc + $bagibbt + $bagitbm + $bagitm;
$tab .= '</tr>';
$tab .= '<tr class=rowheader><td colspan=2>' . $_SESSION['lang']['grnd_total'] . ' </td>';
$tab .= '<td>&nbsp;</td><td align=right>' . number_format($grndTotal, 2) . '</td>' . "\r\n" . '                      <td align=right>' . number_format($bagigrndTotal, 2) . '</td>';

foreach ($dtThnBudget as $brsThnBudget) {
	$tab .= '<td>&nbsp;</td><td align=right>' . number_format($grandTotal[$brsThnBudget], 2) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bagiGrandTotal[$brsThnBudget], 2) . '</td>';
}

$tab .= '</tr>';
$tab .= '</thead></tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lapKebunBiayaLangsung_afd_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                        </script>';
	break;

case 'getDetail':
	$table = '<script language=javascript src=js/zMaster.js></script><script language=javascript src=js/zTools.js></script><script language=javascript src=js/zReport.js></script>' . "\r\n\t" . '<script language=javascript src=js/pmn_laporanPemenuhanKontrak.js></script><script language="javascript" src="js/generic.js"></script>';
	$table .= '<link rel=stylesheet type=text/css href=style/generic.css>';
	$sData = 'select distinct kodeorg, kodebudget,kegiatan,noakun,volume,satuanv,rupiah,thntnm,kodebarang,jumlah,satuanj from ' . $dbname . '.bgt_budget_kebun_perblok_vw where substring(kodeorg,1,4)=\'' . $kodeOrg . '\' and noakun=\'' . $noakun_afd . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;
	$arrd3 = 'proses=&thnBudget_afd=' . $thnBudget . '&noakun_afd=' . $noakun_afd . '&kdUnit_afd=' . $kodeOrg . '&proses=dExcel_afd';
	$table .= '<fieldset><legend>' . $_SESSION['lang']['detail'] . ': ' . $noakun_afd . ', ' . $optKegiatan[$noakun_afd] . '</legend>';
	$table .= '<img onclick="printFileData(\'' . $arrd3 . '\',\'bgt_slave_laporan_biaya_lngs_kebunAfd.php\',\'' . $_SESSION['lang']['detail'] . ' Excel\',event)" src="images/excel.jpg" class="resicon" title="MS.Excel"> ';
	$table .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable><thead>';
	$table .= '<tr class=rowheader>';
	$table .= '<td>No</td>';
	$table .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebudget'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['volume'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['rp'] . '</td>';
	$table .= '</tr></thead><tbody>';

	while ($rData = mysql_fetch_assoc($qData)) {
		$no += 1;
		$table .= '<tr class=rowcontent>';
		$table .= '<td>' . $no . '</td>';
		$table .= '<td>' . $rData['kodeorg'] . '</td>';
		$table .= '<td>' . $rData['kodebudget'] . '</td>';
		$table .= '<td  align=right>' . $rData['volume'] . '</td>';
		$table .= '<td>' . $rData['satuanv'] . '</td>';
		$table .= '<td>' . $optBrng[$rData['kodebarang']] . '</td>';
		$table .= '<td align=right>' . number_format($rData['jumlah'], 0) . '</td>';
		$table .= '<td>' . $rData['satuanj'] . '</td>';
		$table .= '<td align=right>' . number_format($rData['rupiah'], 0) . '</td>';
		$table .= '</tr>';
		$table .= $brt;
		$awal += 1;
	}

	$table .= '</tbody></table></fieldset>';
	echo $table;
	break;

case 'dExcel_afd':
	$sData = 'select distinct kodeorg, kodebudget,kegiatan,noakun,volume,satuanv,rupiah,thntnm,kodebarang,jumlah,satuanj from ' . $dbname . '.bgt_budget_kebun_perblok_vw where substring(kodeorg,1,4)=\'' . $kodeOrg . '\' and noakun=\'' . $noakun_afd . '\' and tahunbudget=\'' . $thnBudget . '\' order by kodeorg asc';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;
	$table .= '<table cellspacing=1 cellpadding=1 border=1 class=sortable><thead>';
	$table .= '<tr class=rowheader>';
	$table .= '<td bgcolor=#DEDEDE>No</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['kodebudget'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['volume'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['jumlah'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['rp'] . '</td>';
	$table .= '</tr></thead><tbody>';

	while ($rData = mysql_fetch_assoc($qData)) {
		$no += 1;
		$table .= '<tr class=rowcontent>';
		$table .= '<td>' . $no . '</td>';
		$table .= '<td>' . $rData['kodeorg'] . '</td>';
		$table .= '<td>' . $rData['kodebudget'] . '</td>';
		$table .= '<td  align=right>' . $rData['volume'] . '</td>';
		$table .= '<td>' . $rData['satuanv'] . '</td>';
		$table .= '<td>' . $rData['kodebarang'] . '</td>';
		$table .= '<td>' . $optBrng[$rData['kodebarang']] . '</td>';
		$table .= '<td align=right>' . number_format($rData['jumlah'], 0) . '</td>';
		$table .= '<td>' . $rData['satuanj'] . '</td>';
		$table .= '<td align=right>' . number_format($rData['rupiah'], 0) . '</td>';
		$table .= '</tr>';
		$table .= $brt;
		$awal += 1;
	}

	$table .= '</tbody></table>';
	$table .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'detaillapKebunBiayaLangsungAfd' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $table);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                    window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                    </script>';
	break;

case 'pdf':
	if (($kodeOrg == '') || ($thnBudget == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $dtJjg;
			global $dtThnBudget;
			global $dtNoakun;
			global $dtJmlhKg;
			global $brsThnBudget;
			global $dtJmlhLuastm;
			global $dtJmlhLuastbm;
			global $totKg;
			global $totJjg;
			global $ttlLuastm;
			global $ttlLuastbm;
			global $ttlLuas;
			global $dbname;
			global $barisNoakun;
			global $kodeOrg;
			global $totRupiah;
			global $rSum;
			global $lstRupiah;
			global $thnBudget;
			global $hasilBagi;
			global $hasilBagi2;
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
			$this->Cell(80, 10, $_SESSION['lang']['total'], 1, 1, 'C', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(80, 10, 'TM= ' . number_format($ttlLuastm, 2) . ' TBM= ' . number_format($ttlLuastm, 2) . ' Ha', 1, 1, 'R', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(40, 10, number_format($rSum['ton'], 2), 1, 0, 'R', 1);
			$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			@$tnha = $rSum['ton'] / 1000 / $ttlLuastm;
			$this->Cell(40, 10, number_format($tnha, 5), 1, 0, 'R', 1);
			$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
			$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
			$br = 288;

			foreach ($dtThnBudget as $listThn) {
				$no += 1;

				if ($no == 1) {
					$ypertama = $this->GetY();
					$this->SetY($ypertama - 50);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, $listThn, 1, 1, 'C', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, 'TM= ' . number_format($dtJmlhLuastm[$thnBudget][$listThn], 2) . ' TBM= ' . number_format($dtJmlhLuastbm[$thnBudget][$listThn], 2) . ' Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, number_format($dtJmlhKg[$thnBudget][$listThn], 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					@$tnha = number_format($dtJmlhKg[$thnBudget][$listThn] / 1000 / $dtJmlhLuastbm[$thnBudget][$listThn], 2);
					$this->Cell(40, 10, number_format($tnha, 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
					$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
				}
				else {
					$ypertama = $this->GetY();
					$this->SetY($ypertama - 50);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, $listThn, 1, 1, 'C', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, 'TM= ' . number_format($dtJmlhLuastm[$thnBudget][$listThn], 2) . ' TBM= ' . number_format($dtJmlhLuastbm[$thnBudget][$listThn], 2) . ' Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, number_format($dtJmlhKg[$thnBudget][$listThn], 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					@$tnha = number_format($dtJmlhKg[$thnBudget][$listThn] / 1000 / $dtJmlhLuastm[$thnBudget][$listThn], 2);
					$this->Cell(40, 10, number_format($tnha, 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
					$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
				}

				$br += 80;
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
	$totThn = count($dtThnBudget);
	$totAkun = count($dtNoakun);
	$totalRptm = '';
	$totalbagitm = '';
	$gttm = '';
	$bagitm = '';
	$totalRptbm = '';
	$totalbagitbm = '';
	$gttbm = '';
	$bagitbm = '';
	$gtbbt = '';
	$totalRpbbt = '';
	$ard = 1;
	$totThn = count($dtThnBudget);
	$totAkun = count($dtNoakun);
	$totalRptm = '';
	$totalbagitm = '';
	$gttm = '';
	$bagitm = '';
	$totalRptbm = '';
	$totalbagitbm = '';
	$gttbm = '';
	$bagitbm = '';
	$gtbbt = '';
	$totalRpbbt = '';
	$ard = 1;

	foreach ($dtNoakun as $barisNoakun) {
		$drAwal += 1;
		$pdf->SetFont('Arial', '', 5);
		$yAkhir = $pdf->GetY();
		$xPertama = $pdf->GetX();
		$pdf->SetY($yAkhir);
		$pdf->SetX($xPertama);
		$pdf->Cell(58, $height, $barisNoakun, 1, 0, 'L', 1);
		$pdf->Cell(150, $height, $optKegiatan[$barisNoakun], 1, 0, 'L', 1);

		if (substr($barisNoakun, 0, 1) == '1') {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastbm;
		}
		else {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastm;
		}

		$pdf->Cell(40, 10, number_format($totRupiah[$thnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
		$pdf->Cell(40, 10, number_format($hasilBagi[$barisNoakun], 0), 1, 0, 'R', 1);
		$grndTotal += $totRupiah[$thnBudget][$barisNoakun];
		$grndTotalHsil += $hasilBagi[$barisNoakun];
		$yAkhir = $pdf->GetY();
		$xPertama = $pdf->GetX();
		$pdf->SetY($yAkhir);
		$pdf->SetX($xPertama);
		$rd = 1;

		foreach ($dtThnBudget as $brsThnBudget) {
			if (substr($barisNoakun, 0, 3) == '126') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastbm[$thnBudget][$brsThnBudget];
				$totalRptbm += $brsThnBudget;
				$totalbagitbm += $brsThnBudget;
				$gttbm += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitbm = $gttbm / $ttlLuastbm;
			}
			else if (substr($barisNoakun, 0, 3) == '128') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastbm[$thnBudget][$brsThnBudget];
				$totalRpbbt += $brsThnBudget;
				$totalbagbbt += $brsThnBudget;
				$gtbbt += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitbm = 0;
			}
			else if (substr($barisNoakun, 0, 1) == '6') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastm[$thnBudget][$brsThnBudget];
				$totalRptm += $brsThnBudget;
				$totalbagitm += $brsThnBudget;
				$gttm += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitm = $gttm / $ttlLuastm;
			}

			if ($rd < $totThn) {
				$pdf->Cell(40, 10, number_format($lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagi2[$brsThnBudget], 0), 1, 0, 'R', 1);
			}
			else {
				$pdf->Cell(40, 10, number_format($lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagi2[$brsThnBudget], 0), 1, 1, 'R', 1);
			}

			$rd += 1;
		}
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'BBT', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gtbbt, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format(0, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRpbbt[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format(0, 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRpbbt[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format(0, 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'TBM', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gttbm, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($bagitbm, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRptbm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitbm[$brsThnBudget], 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRptbm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitbm[$brsThnBudget], 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'TM', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gttm, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($bagitm, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRptm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitm[$brsThnBudget], 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRptm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitm[$brsThnBudget], 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Output();
	break;
}

?>
