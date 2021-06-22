<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['unit'] == '' ? $unit = $_GET['unit'] : $unit = $_POST['unit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$optNm = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optKegSat = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
if (($unit == '') || ($periode == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'rp0' . $W;
	}
	else {
		$jack = 'rp' . $W;
	}

	if ($W < intval($bulan)) {
		$addstr .= $jack . '+';
	}
	else {
		$addstr .= $jack;
	}

	++$W;
}

$addstr .= ')';
$addstr2 = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'fis0' . $W;
	}
	else {
		$jack = 'fis' . $W;
	}

	if ($W < intval($bulan)) {
		$addstr2 .= $jack . '+';
	}
	else {
		$addstr2 .= $jack;
	}

	++$W;
}

$addstr2 .= ')';
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
$bg = '';
$brdr = 0;
$sLuas = 'select distinct sum(luasareaproduktif) as luaskrja,left(kodeorg,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.`setup_blok`  ' . "\r\n" . '        where kodeorg like \'' . $unit . '%\' and tahuntanam!=\'\'' . "\r\n" . '        group by left(kodeorg,6),tahuntanam order by left(kodeorg,6) asc,tahuntanam asc';

if ($afdId != '') {
	$sLuas = 'select distinct sum(luasareaproduktif) as luaskrja,left(kodeorg,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.`setup_blok`  ' . "\r\n" . '        where kodeorg like \'' . $afdId . '%\' and tahuntanam!=\'\'' . "\r\n" . '        group by left(kodeorg,6),tahuntanam order by left(kodeorg,6) asc,tahuntanam asc';
}

#exit(mysql_error($conn));
($qLuas = mysql_query($sLuas)) || true;

while ($rLuas = mysql_fetch_assoc($qLuas)) {
	$dtAfd[$rLuas['afd']] = $rLuas['afd'];
	$dtThnTnm[$rLuas['tahuntanam']] = $rLuas['tahuntanam'];
	$dtLuas[$rLuas['afd'] . $rLuas['tahuntanam']] = $rLuas['luaskrja'];
}

$sBjr = 'select distinct sum(totalkg) as kg,sum(jjg) as jjg,substr(tanggal,6,2) as bulan,left(blok,6) as afd,' . "\r\n" . '       b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b on a.blok=b.kodeorg' . "\r\n" . '       where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and blok like \'' . $unit . '%\' and tahuntanam!=\'\'' . "\r\n" . '       group by left(blok,6),substr(tanggal,6,2),tahuntanam' . "\r\n" . '       order by left(blok,6) asc,b.tahuntanam asc';

if ($afdId != '') {
	$sBjr = 'select distinct sum(totalkg) as kg,sum(jjg) as jjg,substr(tanggal,6,2) as bulan,left(blok,6) as afd,' . "\r\n" . '       b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b on a.blok=b.kodeorg' . "\r\n" . '       where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and blok like \'' . $afdId . '%\' and tahuntanam!=\'\'' . "\r\n" . '       group by left(blok,6),substr(tanggal,6,2),tahuntanam' . "\r\n" . '       order by left(blok,6) asc,b.tahuntanam asc';
}

#exit(mysql_error($conn));
($qBjr = mysql_query($sBjr)) || true;

while ($rBjr = mysql_fetch_assoc($qBjr)) {
	$dtAfd[$rBjr['afd']] = $rBjr['afd'];
	$dtThnTnm[$rBjr['tahuntanam']] = $rBjr['tahuntanam'];
	@$dtBjr[$rBjr['afd'] . $rBjr['tahuntanam']][$rBjr['bulan']] = $rBjr['kg'] / $rBjr['jjg'];

	if (intval($rBjr['bulan']) < 7) {
		$smstrJjg += $rBjr['afd'] . $rBjr['tahuntanam'];
		$smstrkg += $rBjr['afd'] . $rBjr['tahuntanam'];
	}

	$smpBlnJjg += $rBjr['afd'] . $rBjr['tahuntanam'];
	$smpBlnKg += $rBjr['afd'] . $rBjr['tahuntanam'];
	$totKg += $rBjr['bulan'];
	$totJjg += $rBjr['bulan'];
}

$sSmtr1 = 'select distinct sum(jumlah) as jjg,sum(kgsensus) as kg,left(kodeblok,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.kebun_rencanapanen_vw where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $tahun . '-06\' and tahuntanam!=\'\'' . "\r\n" . '        and kodeblok like \'' . $unit . '%\'  group by left(kodeblok,6),tahuntanam order by left(kodeblok,6) asc,tahuntanam asc';

if ($afdId != '') {
	$sSmtr1 = 'select distinct sum(jumlah) as jjg,sum(kgsensus) as kg,left(kodeblok,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.kebun_rencanapanen_vw where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $tahun . '-06\' and tahuntanam!=\'\'' . "\r\n" . '        and kodeblok like \'' . $afdId . '%\'  group by left(kodeblok,6),tahuntanam order by left(kodeblok,6) asc,tahuntanam asc';
}

#exit(mysql_error($conn));
($qSmtr1 = mysql_query($sSmtr1)) || true;

while ($rSmtr1 = mysql_fetch_assoc($qSmtr1)) {
	$dtAfd[$rSmtr1['afd']] = $rSmtr1['afd'];
	$dtThnTnm[$rSmtr1['tahuntanam']] = $rSmtr1['tahuntanam'];
	@$dtSemester1[$rSmtr1['afd'] . $rSmtr1['tahuntanam']] = $rSmtr1['kg'] / $rSmtr1['jjg'];
	$totSmstrKg1 += $rSmtr1['kg'];
	$totSmstrJjg1 += $rSmtr1['jjg'];
}

$sSmtr1 = 'select distinct sum(jumlah) as jjg,sum(kgsensus) as kg,left(kodeblok,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.kebun_rencanapanen_vw where left(tanggal,7) between \'' . $tahun . '-07\' and \'' . $tahun . '-12\' and tahuntanam!=\'\'' . "\r\n" . '         and kodeblok like \'' . $unit . '%\' group by left(kodeblok,6),tahuntanam order by left(kodeblok,6) asc,tahuntanam asc';

if ($afdId != '') {
	$sSmtr1 = 'select distinct sum(jumlah) as jjg,sum(kgsensus) as kg,left(kodeblok,6) as afd,tahuntanam from ' . "\r\n" . '        ' . $dbname . '.kebun_rencanapanen_vw where left(tanggal,7) between \'' . $tahun . '-07\' and \'' . $tahun . '-12\' and tahuntanam!=\'\'' . "\r\n" . '         and kodeblok like \'' . $afdId . '%\' group by left(kodeblok,6),tahuntanam order by left(kodeblok,6) asc,tahuntanam asc';
}

#exit(mysql_error($conn));
($qSmtr1 = mysql_query($sSmtr1)) || true;

while ($rSmtr1 = mysql_fetch_assoc($qSmtr1)) {
	$dtAfd[$rSmtr1['afd']] = $rSmtr1['afd'];
	$dtThnTnm[$rSmtr1['tahuntanam']] = $rSmtr1['tahuntanam'];
	@$dtSemester2[$rSmtr1['afd'] . $rSmtr1['tahuntanam']] = $rSmtr1['kg'] / $rSmtr1['jjg'];
	$totSmstrKg2 += $rSmtr1['kg'];
	$totSmstrJjg2 += $rSmtr1['jjg'];
}

$sBgt = 'select distinct sum(jlhkg) as kg,sum(jlhjjg) as jjg,afdeling,thntnm from ' . $dbname . '.bgt_produksi_afdeling' . "\r\n" . '       where tahunbudget=\'' . $tahun . '\' and afdeling like \'' . $unit . '%\' ' . "\r\n" . '       group by afdeling,thntnm order by afdeling asc,thntnm asc';

if ($afdId != '') {
	$sBgt = 'select distinct sum(jlhkg) as kg,sum(jlhjjg) as jjg,afdeling,thntnm from ' . $dbname . '.bgt_produksi_afdeling' . "\r\n" . '       where tahunbudget=\'' . $tahun . '\' and afdeling like \'' . $afdId . '%\' ' . "\r\n" . '       group by afdeling,thntnm order by afdeling asc,thntnm asc';
}

#exit(mysql_error($conn));
($qBgt = mysql_query($sBgt)) || true;

while ($rBgt = mysql_fetch_assoc($qBgt)) {
	$dtAfd[$rBgt['afdeling']] = $rBgt['afdeling'];
	$dtThnTnm[$rBgt['thntnm']] = $rBgt['thntnm'];
	@$dtBgt[$rBgt['afdeling'] . $rBgt['thntnm']] = $rBgt['kg'] / $rBgt['jjg'];
	$totBgtKg += $rBgt['kg'];
	$totBgtJjg += $rBgt['jjg'];
}

$sPanen = 'select sum(hasilkerja) as jjg,substr(tanggal,6,2) as bln,left(kodeorg,6) as afd,tahuntanam ' . "\r\n" . '                    from ' . $dbname . '.kebun_prestasi_vw where kodeorg like \'' . $unit . '%\' and ' . "\r\n" . '                    left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' and tahuntanam!=\'\'' . "\r\n" . '                    group by left(kodeorg,6),tahuntanam,substr(tanggal,6,2)';

if ($afdId != '') {
	$sPanen = 'select sum(hasilkerja) as jjg,substr(tanggal,6,2) as bln,left(kodeorg,6) as afd,tahuntanam ' . "\r\n" . '                    from ' . $dbname . '.kebun_prestasi_vw where kodeorg like \'' . $afdId . '%\' and ' . "\r\n" . '                    left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' and tahuntanam!=\'\'' . "\r\n" . '                    group by left(kodeorg,6),tahuntanam,substr(tanggal,6,2)';
}

#exit(mysql_error($conn));
($qPanen = mysql_query($sPanen)) || true;

while ($rPanen = mysql_fetch_assoc($qPanen)) {
	$dtAfd[$rPanen['afd']] = $rPanen['afd'];
	$dtThnTnm[$rPanen['tahuntanam']] = $rPanen['tahuntanam'];
	$dtJJgPan[$rPanen['afd'] . $rPanen['tahuntanam']][$rPanen['bln']] = $rPanen['jjg'];
	$totJjgPnn += $rPanen['bln'];
}

$sparam = 'select  nilai from ' . $dbname . '.setup_parameterappl where kodeparameter=\'JJGKT\'';

#exit(mysql_error($conn));
($qParam = mysql_query($sparam)) || true;
$rParam = mysql_fetch_assoc($qParam);
$sKontanan = 'select sum(jjgkontanan) as jjgkontan,left(kodeblok,6) as afd,substr(tanggal,6,2) as bln,tahuntanam' . "\r\n" . '                       from ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_blok b on a.kodeblok=b.kodeorg' . "\r\n" . '                       where kodeblok like \'' . $unit . '%\'  and tanggal like \'' . $periodeData . '%\' and ' . "\r\n" . '                       left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' and tahuntanam!=\'\'' . "\r\n" . '                       group by left(kodeblok,6),tahuntanam,substr(tanggal,6,2)';

if ($afdId != '') {
	$sKontanan = 'select sum(jjgkontanan) as jjgkontan,left(kodeblok,6) as afd,substr(tanggal,6,2) as bln,tahuntanam' . "\r\n" . '                       from ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_blok b on a.kodeblok=b.kodeorg' . "\r\n" . '                       where kodeblok like \'' . $afdId . '%\'  and tanggal like \'' . $periodeData . '%\' and ' . "\r\n" . '                       left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' and tahuntanam!=\'\'' . "\r\n" . '                       group by left(kodeblok,6),tahuntanam,substr(tanggal,6,2)';
}

#exit(mysql_error($conn));
($qKontanan = mysql_query($sKontanan)) || true;

while ($rKontan = mysql_fetch_assoc($qKontanan)) {
	$dtAfd[$rKontan['afd']] = $rKontan['afd'];
	$dtThnTnm[$rKontan['tahuntanam']] = $rKontan['tahuntanam'];
	$dtJJgkntn[$rKontan['afd'] . $rKontan['tahuntanam']][$rKontan['bln']] = $rKontan['jjgkontan'];
	$totJjgKntn += $rKontan['bln'];
}

$sPanen2 = 'select SUM( jjg ) AS angkut, left(blok,6) as afd,substr(tanggal,6,2) as bln,tahuntanam' . "\r\n" . '                      from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b on a.blok=b.kodeorg' . "\r\n" . '                      where blok like \'' . $unit . '%\' and left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\'' . "\r\n" . '                       and tahuntanam!=\'\' group by left(blok,6),substr(tanggal,6,2),tahuntanam';

if ($afdId != '') {
	$sPanen2 = 'select SUM( jjg ) AS angkut, left(blok,6) as afd,substr(tanggal,6,2) as bln,tahuntanam' . "\r\n" . '                      from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b on a.blok=b.kodeorg' . "\r\n" . '                      where blok like \'' . $afdId . '%\' and left(tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\'' . "\r\n" . '                       and tahuntanam!=\'\' group by left(blok,6),substr(tanggal,6,2),tahuntanam';
}

#exit(mysql_error($conn));
($qPanen2 = mysql_query($sPanen2)) || true;

while ($rPanen2 = mysql_fetch_assoc($qPanen2)) {
	$dtAfd[$rPanen2['afd']] = $rPanen2['afd'];
	$dtThnTnm[$rPanen2['tahuntanam']] = $rPanen2['tahuntanam'];
	$dtJJg[$rPanen2['afd'] . $rPanen2['tahuntanam']][$rPanen2['bln']] = $rPanen2['angkut'];
	$totAngkut += $rPanen2['bln'];
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '         <tr>' . "\r\n" . '            <td colspan=8 align=left><font size=3>BERAT JANJANG RATA-RATA TAHUN ' . $tahun . '</font></td>' . "\r\n" . '            <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '         </tr> ' . "\r\n" . '         <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=14 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')</td></tr>';
	}

	$tab .= '</table>';
}

$bgcoloraja = '';

if ($preview == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE';
	$brdr = 1;
}

$tab .= $judul;
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['afdeling'] . '</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['tahuntanam'] . '</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['luas'] . '</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>UMUR TANAMAN</td>';
$tab .= '<td align=center colspan=' . (5 + intval($bulan)) . '>BJR  (Kg/JJG)</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>BI  VS SNS SM1 (KG)</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>S/D BI  VS SNS SM2 (KG)</td>';
$tab .= '<td rowspan=3  align=center ' . $bgcoloraja . '>S/D BI  VS  BGT (KG)</td></tr>';
$tab .= '<tr><td align=center colspan=' . (2 + intval($bulan)) . '>' . $_SESSION['lang']['realisasi'] . '</td>';
$tab .= '<td align=center colspan=2>HASIL SENSUS</td>';
$tab .= '<td align=center rowspan=2>BGT</td></tr><tr>';
$ard = 1;

while ($ard <= intval($bulan)) {
	if ($ard < 10) {
		$ert = '0' . $ard;
		$tab .= '<td align=center ' . $bgcoloraja . '>' . $optBulan[$ert] . '</td>';
	}
	else {
		$tab .= '<td align=center ' . $bgcoloraja . '>' . $optBulan[$ard] . '</td>';
	}

	if ($ard == 6) {
		$tab .= '<td align=center ' . $bgcoloraja . '>S/DSM I</td>';
	}

	if ($ard == intval($bulan)) {
		$tab .= '<td align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td>';
	}

	++$ard;
}

$tab .= '<td align=center ' . $bgcoloraja . '>SM I</td>';
$tab .= '<td align=center ' . $bgcoloraja . '>SM II</td>';
$tab .= '</tr></thead><tbody>';

foreach ($dtAfd as $lstAfd) {
	foreach ($dtThnTnm as $lstThnTnm) {
		if (($lstThnTnm != '') || ($lstThnTnm != 0)) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $lstAfd . '</td>';
			$tab .= '<td>' . $lstThnTnm . '</td>';
			$tab .= '<td align=right>' . number_format($dtLuas[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '<td align=right>' . ($tahun - $lstThnTnm) . '</td>';
			$ard = 1;

			while ($ard <= intval($bulan)) {
				if ($ard < 10) {
					$ert = '0' . $ard;
					$tab .= '<td align=right>' . number_format($dtBjr[$lstAfd . $lstThnTnm][$ert], 2) . '</td>';
				}
				else {
					$tab .= '<td align=right>' . number_format($dtBjr[$lstAfd . $lstThnTnm][$ard], 2) . '</td>';
				}

				if ($ard == 6) {
					@$smstran[$lstAfd . $lstThnTnm] = $smstrkg[$lstAfd . $lstThnTnm] / $smstrJjg[$lstAfd . $lstThnTnm];
					$tab .= '<td align=right>' . number_format($smstran[$lstAfd . $lstThnTnm], 2) . '</td>';
				}

				if ($ard == intval($bulan)) {
					@$smsBulan[$lstAfd . $lstThnTnm] = $smpBlnKg[$lstAfd . $lstThnTnm] / $smpBlnJjg[$lstAfd . $lstThnTnm];
					$tab .= '<td align=right>' . number_format($smsBulan[$lstAfd . $lstThnTnm], 2) . '</td>';
				}

				++$ard;
			}

			$tab .= '<td align=right>' . number_format($dtSemester1[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($dtSemester2[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($dtBgt[$lstAfd . $lstThnTnm], 2) . '</td>';
			$dtSns1[$lstAfd . $lstThnTnm] = $smsBulan[$lstAfd . $lstThnTnm] - $dtSemester1[$lstAfd . $lstThnTnm];
			$dtSns2[$lstAfd . $lstThnTnm] = $smsBulan[$lstAfd . $lstThnTnm] - $dtSemester2[$lstAfd . $lstThnTnm];
			$dtBgtDt[$lstAfd . $lstThnTnm] = $smsBulan[$lstAfd . $lstThnTnm] - $dtBgt[$lstAfd . $lstThnTnm];
			$tab .= '<td align=right>' . number_format($dtSns1[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($dtSns2[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($dtBgtDt[$lstAfd . $lstThnTnm], 2) . '</td>';
			$tab .= '</tr>';

			if ($dthntanme != $lstThnTnm) {
				$tab .= '<tr class=rowcontent>';
				$tab .= '<td></td>';
				$tab .= '<td colspan=3>Restan Akhir Bulan (ton)</td>';
				$ard = 1;

				while ($ard <= intval($bulan)) {
					if ($ard < 10) {
						$ert = '0' . $ard;
						@$rest[$lstAfd . $lstThnTnm][$ert] = (($dtJJgPan[$lstAfd . $lstThnTnm][$ert] + $dtJJgkntn[$lstAfd . $lstThnTnm][$ert]) - $dtJJg[$lstAfd . $lstThnTnm][$ert]) / 1000;
						$tab .= '<td align=right>' . number_format($rest[$lstAfd . $lstThnTnm][$ert], 2) . '</td>';

						if ($ard < 7) {
							$smRes += $lstAfd . $lstThnTnm;
						}

						$smpResBln += $lstAfd . $lstThnTnm;
					}
					else {
						@$rest[$lstAfd . $lstThnTnm][$ard] = (($dtJJgPan[$lstAfd . $lstThnTnm][$ard] + $dtJJgkntn[$lstAfd . $lstThnTnm][$ard]) - $dtJJg[$lstAfd . $lstThnTnm][$ard]) / 1000;
						$tab .= '<td align=right>' . number_format($rest[$lstAfd . $lstThnTnm][$ard], 2) . '</td>';
					}

					if ($ard == 6) {
						$tab .= '<td align=right>' . number_format($smRes[$lstAfd . $lstThnTnm], 2) . '</td>';
					}

					if ($ard == intval($bulan)) {
						$tab .= '<td align=right>' . number_format($smpResBln[$lstAfd . $lstThnTnm], 2) . '</td>';
					}

					++$ard;
				}

				$tab .= '<td colspan=6>&nbsp;</td></tr>';
			}
		}
	}
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td></td>';
$tab .= '<td colspan=3>' . $_SESSION['lang']['total'] . '</td>';
$ard = 1;

while ($ard <= intval($bulan)) {
	if ($ard < 10) {
		$ert = '0' . $ard;
		@$totBlnan[$ert] = $totKg[$ert] / $totJjg[$ert];
		$tab .= '<td align=right>' . number_format($totBlnan[$ert], 2) . '</td>';

		if ($ard < 7) {
			$smtranKg += $totKg[$ert];
			$smtranJjg += $totJjg[$ert];
		}
	}
	else {
		@$totBlnan[$ard] = $totKg[$ard] / $totJjg[$ard];
		$tab .= '<td align=right>' . number_format($totBlnan[$ard], 2) . '</td>';
	}

	$smtranKgSi += $totKg[$ert];
	$smtranJjgSi += $totJjg[$ert];

	if ($ard == 6) {
		@$totSmstrn = $smtranKg / $smtranJjg;
		$tab .= '<td align=right>' . number_format($totSmstrn, 2) . '</td>';
	}

	if ($ard == intval($bulan)) {
		@$totSmstrnSi = $smtranKgSi / $smtranJjgSi;
		$tab .= '<td align=right>' . number_format($totSmstrnSi, 2) . '</td>';
	}

	++$ard;
}

@$totSmtr1 = $totSmstrKg1 / $totSmstrJjg1;
@$totSmtr2 = $totSmstrKg2 / $totSmstrJjg2;
@$totBgt = $totBgtKg / $totBgtJjg;
$tab .= '<td align=right>' . number_format($totSmtr1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totSmtr2, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totBgt, 2) . '</td>';
$dtSns1 = $totSmstrnSi - $totSmtr1;
$dtSns2 = $totSmstrnSi - $totSmtr2;
$dtBgtDt = $totSmstrnSi - $totBgt;
$tab .= '<td align=right>' . number_format($dtSns1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($dtSns2, 2) . '</td>';
$tab .= '<td align=right>' . number_format($dtBgtDt, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td></td>';
$tab .= '<td colspan=3>Restan Akhir Bulan (ton)</td>';
$ard = 1;

while ($ard <= intval($bulan)) {
	if ($ard < 10) {
		$ert = '0' . $ard;
		@$rest[$ert] = (($totJjgPnn[$ert] + $totJjgKntn[$ert]) - $totAngkut[$ert]) / 1000;
		$tab .= '<td align=right>' . number_format($rest[$ert], 2) . '</td>';

		if ($ard < 7) {
			$smtranJjg += $totJjgPnn[$ert];
			$smtranJjgKntnan += $totJjgKntn[$ert];
			$smtranJjgAngkut += $totAngkut[$ert];
		}
	}
	else {
		@$rest[$ard] = (($totJjgPnn[$ard] + $totJjgKntn[$ard]) - $totAngkut[$ard]) / 1000;
		$tab .= '<td align=right>' . number_format($totBlnan[$ard], 2) . '</td>';
	}

	$smtranJjgSi += $totJjgPnn[$ert];
	$smtranJjgKntnanSi += $totJjgKntn[$ert];
	$smtranJjgAngkutSi += $totAngkut[$ert];

	if ($ard == 6) {
		@$restSmstran = (($smtranJjg + $smtranJjgKntnan) - $smtranJjgAngkut) / 1000;
		$tab .= '<td align=right>' . number_format($restSmstran, 2) . '</td>';
	}

	if ($ard == intval($bulan)) {
		@$restSmstranSi = (($smtranJjgSi + $smtranJjgKntnanSi) - $smtranJjgAngkutSi) / 1000;
		$tab .= '<td align=right>' . number_format($restSmstranSi, 2) . '</td>';
	}

	++$ard;
}

$tab .= '<td colspan=6>&nbsp;</td></tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lbm_bjr_' . $unit . $periode;

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
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;
}

?>
