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
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if ($_SESSION['language'] == 'EN') {
	$zz = 'namakegiatan1';
}
else {
	$zz = 'namakegiatan';
}

$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,' . $zz);
if (($unit == '') || ($periode == '')) {
	exit('Error:Field required');
}

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

if ($_SESSION['language'] == 'EN') {
	$jud = 'MATURE UPKEEP COST ANALYSIS';
}
else {
	$jud = 'ANALISA BIAYA PEMELIHARAAN TM';
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '         <tr>' . "\r\n" . '            <td colspan=8 align=left><font size=3>' . $jud . '</font></td>' . "\r\n" . '            <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '         </tr> ' . "\r\n" . '         <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=14 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')</td></tr>';
	}

	$tab .= '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'kg0' . $W;
	}
	else {
		$jack = 'kg' . $W;
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
		$jack = 'rp0' . $W;
	}
	else {
		$jack = 'rp' . $W;
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
$sRealLuas = 'select distinct sum(luasareaproduktif) as luasreal from ' . $dbname . '.setup_blok where kodeorg like \'' . $unit . '%\'';

if ($afdId != '') {
	$sRealLuas = 'select distinct sum(luasareaproduktif) as luasreal from ' . $dbname . '.setup_blok where kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qRealLuas = mysql_query($sRealLuas)) || true;
$rRealLuas = mysql_fetch_assoc($qRealLuas);
$sRealLuas2 = 'select distinct sum(hathnini) as luasbgt from ' . $dbname . '.bgt_blok where ' . "\r\n" . '             kodeblok like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\'';

if ($afdId != '') {
	$sRealLuas2 = 'select distinct sum(hathnini) as luasbgt from ' . $dbname . '.bgt_blok where ' . "\r\n" . '             kodeblok like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\'';
}

#exit(mysql_error($conn));
($qRealLuas2 = mysql_query($sRealLuas2)) || true;
$rRealLuas2 = mysql_fetch_assoc($qRealLuas2);
$sProdReal = 'select distinct sum(hasilkerjakg) as hasilProd from ' . $dbname . '.kebun_prestasi_vw where ' . "\r\n" . '            tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\'';

if ($afdId != '') {
	$sProdReal = 'select distinct sum(hasilkerjakg) as hasilProd from ' . $dbname . '.kebun_prestasi_vw where ' . "\r\n" . '            tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qProdReal = mysql_query($sProdReal)) || true;
$rProdReal = mysql_fetch_assoc($qProdReal);
@$prodReal = $rProdReal['hasilProd'] / 1000;
$sProdReal2 = 'select distinct sum(kg' . $bulan . ') as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $unit . '%\'';

if ($afdId != '') {
	$sProdReal2 = 'select distinct sum(kg' . $bulan . ') as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qProdReal2 = mysql_query($sProdReal2)) || true;
$rProdReal2 = mysql_fetch_assoc($qProdReal2);
@$prodBgt = $rProdReal2['hasilProd'] / 1000;
$sProdReal = 'select distinct sum(hasilkerjakg) as hasilProd from ' . $dbname . '.kebun_prestasi_vw where ' . "\r\n" . '            left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and kodeorg like \'' . $unit . '%\'';

if ($afdId != '') {
	$sProdReal = 'select distinct sum(hasilkerjakg) as hasilProd from ' . $dbname . '.kebun_prestasi_vw where ' . "\r\n" . '            left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and kodeorg like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qProdReal = mysql_query($sProdReal)) || true;
$rProdReal = mysql_fetch_assoc($qProdReal);
@$prodRealSi = $rProdReal['hasilProd'] / 1000;
$sProdReal2 = 'select distinct sum(' . $addstr . ') as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $unit . '%\'';

if ($afdId != '') {
	$sProdReal2 = 'select distinct sum(' . $addstr . ') as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qProdReal2 = mysql_query($sProdReal2)) || true;
$rProdReal2 = mysql_fetch_assoc($qProdReal2);
@$prodBgtSi = $rProdReal2['hasilProd'] / 1000;
$sProdReal2 = 'select distinct sum(kgsetahun) as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $unit . '%\'';

if ($afdId != '') {
	$sProdReal2 = 'select distinct sum(kgsetahun) as hasilProd from ' . $dbname . '.bgt_produksi_kbn_kg_vw where ' . "\r\n" . '            tahunbudget=\'' . $tahun . '%\' and kodeblok like \'' . $afdId . '%\'';
}

#exit(mysql_error($conn));
($qProdReal2 = mysql_query($sProdReal2)) || true;
$rProdReal2 = mysql_fetch_assoc($qProdReal2);
$sthn = $rProdReal2['hasilProd'];
$sData = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\' and ' . "\r\n" . '        b.kelompok=\'TM\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and kodeblok like \'' . $afdId . '%\' and ' . "\r\n" . '        b.kelompok=\'TM\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$dtKegiatan[$rData['kodekegiatan']] = $rData['kodekegiatan'];
	$dtRupiahRealTm += $rData['kodekegiatan'];
}

$sData = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\' and ' . "\r\n" . '        b.kelompok=\'PNN\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and kodeblok like \'' . $afdId . '%\' and ' . "\r\n" . '        b.kelompok=\'PNN\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$dtKegiatanPnn[$rData['kodekegiatan']] = $rData['kodekegiatan'];
	$dtRupiahRealPnn += $rData['kodekegiatan'];
}

$sData2 = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and nojurnal like \'%' . $unit . '%\' and b.kelompok=\'TM\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData2 = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and kodeblok like \'' . $afdId . '%\' and b.kelompok=\'TM\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData2 = mysql_query($sData2)) || true;

while ($rData2 = mysql_fetch_assoc($qData2)) {
	$dtKegiatan[$rData2['kodekegiatan']] = $rData2['kodekegiatan'];
	$dtRupiahRealTmSi += $rData2['kodekegiatan'];
}

$sData2 = 'select sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and nojurnal like \'%' . $unit . '%\' and b.kelompok=\'PNN\' and a.kodekegiatan!=\'\' and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData2 = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and kodeblok like \'' . $afdId . '%\' and b.kelompok=\'PNN\' and a.kodekegiatan!=\'\'  and jumlah>0' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData2 = mysql_query($sData2)) || true;

while ($rData2 = mysql_fetch_assoc($qData2)) {
	$dtKegiatanPnn[$rData2['kodekegiatan']] = $rData2['kodekegiatan'];
	$dtRupiahRealPnnSi += $rData2['kodekegiatan'];
}

$sDbgt = 'select  sum(rp' . $bulan . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group  by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select  sum(rp' . $bulan . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group  by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtTm += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select distinct sum(' . $addstr2 . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group  by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select distinct sum(' . $addstr2 . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group  by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtTmSi += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select distinct sum(rp' . $bulan . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select distinct sum(rp' . $bulan . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatanPnn[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtlPnn += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select distinct sum(' . $addstr2 . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select distinct sum(' . $addstr2 . ') as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatanPnn[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtlPnnSi += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select distinct sum(rupiah) as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select distinct sum(rupiah) as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TM\'' . "\r\n" . '        group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtThn += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select distinct sum(rupiah) as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select distinct sum(rupiah) as jumlah,a.kegiatan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'PNN\'' . "\r\n" . '        group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatanPnn[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtThnPnn += $rDbgt['kegiatan'];
	}
}

$bgcoloraja = '';

if ($preview == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
}

$dcek = count($dtKegiatan);
$tab .= $judul;
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>';
$tab .= '<td rowspan=6 ' . $bgcoloraja . '>' . $_SESSION['lang']['kodekegiatan'] . '</td>';
$tab .= '<td rowspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
$tab .= '<td colspan=5 ' . $bgcoloraja . '>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td colspan=5 ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td>';
$tab .= '<td rowspan=2 colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'] . '</td></tr>';
$tab .= '<tr>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['anggaran'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>%</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['anggaran'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>%</td></tr>';
$tab .= '<tr>';
$tab .= '<td align=left ' . $bgcoloraja . '>' . $_SESSION['lang']['luasareal'] . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($rRealLuas['luasreal'], 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($rRealLuas2['luasbgt'], 2) . '</td>';
@$prsn1 = ($rRealLuas['luasreal'] / $rRealLuas2['luasbgt']) * 100;
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsn1, 0) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($rRealLuas['luasreal'], 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($rRealLuas2['luasbgt'], 2) . '</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsn1, 0) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($rRealLuas2['luasbgt'], 2) . '</td>';
$tab .= '<tr>';
$tab .= '<td align=left ' . $bgcoloraja . '>' . $_SESSION['lang']['produksi'] . ' (TON)</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($prodReal, 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($prodBgt, 2) . '</td>';
@$prsn2 = ($prodReal / $prodBgt) * 100;
@$tonhareal = $prodReal / $rRealLuas['luasreal'];
@$tonhabgt = $prodBgt / $rRealLuas2['luasbgt'];
@$prsntonha = ($tonhareal / $tonhabgt) * 100;
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsn2, 0) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($prodRealSi, 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($prodBgtSi, 2) . '</td>';
@$prsn3 = ($prodRealSi / $prodBgtSi) * 100;
@$tonharealSi = $prodRealSi / $rRealLuas['luasreal'];
@$tonhabgtSi = $prodBgtSi / $rRealLuas2['luasbgt'];
@$prsntonhaSi = ($tonharealSi / $tonhabgtSi) * 100;
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsn3, 0) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($sthn, 2) . '</td></tr>';
$tab .= '<tr>';
$tab .= '<td align=left ' . $bgcoloraja . '>TON/HA</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($tonhareal, 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($tonhabgt, 2) . '</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsntonha, 0) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($tonharealSi, 2) . '</td>';
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($tonhabgtSi, 2) . '</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($prsntonhaSi, 0) . '</td>';
@$thnData = $sthn / $rRealLuas2['luasbgt'];
$tab .= '<td colspan=2 align=right ' . $bgcoloraja . '>' . number_format($thnData, 2) . '</td></tr>';
$tab .= '<tr>';
$tab .= '<td  ' . $bgcoloraja . '>&nbsp;</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp./Ha</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp./Ha</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp./Ha</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp./Ha</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>Rp.</td><td align=right> ' . $bgcoloraja . 'Rp./Ha</td></tr>';
$tab .= '</thead><tbody>';

if ($dcek != 0) {
	foreach ($dtKegiatan as $lstKegiatan) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $lstKegiatan . '</td>';
		$tab .= '<td>' . $optKeg[$lstKegiatan] . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahRealTm[$lstKegiatan], 2) . '</td>';
		@$rpHa1[$lstKegiatan] = $dtRupiahRealTm[$lstKegiatan] / $rRealLuas['luasreal'];
		$tab .= '<td align=right>' . number_format($rpHa1[$lstKegiatan], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtTm[$lstKegiatan], 2) . '</td>';
		@$rpHa2[$lstKegiatan] = $dtRupiahBgtTm[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa2[$lstKegiatan], 2) . '</td>';
		@$prsnA1[$lstKegiatan] = ($dtRupiahRealTm[$lstKegiatan] / $dtRupiahBgtTm[$lstKegiatan]) * 100;
		$tab .= '<td align=right>' . number_format($prsnA1[$lstKegiatan], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahRealTmSi[$lstKegiatan], 2) . '</td>';
		@$rpHa1Si[$lstKegiatan] = $dtRupiahRealTmSi[$lstKegiatan] / $rRealLuas['luasreal'];
		$tab .= '<td align=right>' . number_format($rpHa1Si[$lstKegiatan], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtTmSi[$lstKegiatan], 2) . '</td>';
		@$rpHa2Si[$lstKegiatan] = $dtRupiahBgtTmSi[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa2Si[$lstKegiatan], 2) . '</td>';
		@$prsnA1Si[$lstKegiatan] = ($dtRupiahRealTmSi[$lstKegiatan] / $dtRupiahBgtTmSi[$lstKegiatan]) * 100;
		$tab .= '<td align=right>' . number_format($prsnA1Si[$lstKegiatan], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtThn[$lstKegiatan], 0) . '</td>';
		$rpHa3[$lstKegiatan] = $dtRupiahBgtThn[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa3[$lstKegiatan], 0) . '</td>';
		$tab .= '</tr>';
		$sub1 += $dtRupiahRealTm[$lstKegiatan];
		$sub2 += $dtRupiahBgtTm[$lstKegiatan];
		$sub1Si += $dtRupiahRealTmSi[$lstKegiatan];
		$sub2Si += $dtRupiahBgtTmSi[$lstKegiatan];
		$sub3 += $dtRupiahBgtThn[$lstKegiatan];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['subtotal'] . ' Biaya Pemeliharaan</td>';
	$tab .= '<td align=right>' . number_format($sub1, 2) . '</td>';
	@$rpHa1 = $sub1 / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sub2, 2) . '</td>';
	@$rpHa2 = $sub2 / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2, 2) . '</td>';
	@$prsnA1 = ($sub1 / $sub2) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sub1Si, 2) . '</td>';
	@$rpHa1Si = $sub1Si / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1Si, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sub2Si, 2) . '</td>';
	@$rpHa2Si = $sub2Si / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2Si, 2) . '</td>';
	@$prsnA1Si = ($sub1Si / $sub2Si) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1Si, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sub3, 0) . '</td>';
	$rpHa3 = $sub3 / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa3, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Cost Per Kg Untuk Biaya Pemeliharaan</td>';
	@$sub1b = $sub1 / $prodReal;
	$tab .= '<td align=right>' . number_format($sub1b, 2) . '</td>';
	@$rpHa1 = $sub1b / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1, 2) . '</td>';
	@$sub2b = $sub2 / $prodBgt;
	$tab .= '<td align=right>' . number_format($sub2b, 2) . '</td>';
	@$rpHa2 = $sub2 / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2, 2) . '</td>';
	@$prsnA1 = ($sub1b / $sub2b) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1, 0) . '</td>';
	@$sub1Sib = $sub1Si / $prodRealSi;
	$tab .= '<td align=right>' . number_format($sub1Sib, 2) . '</td>';
	@$rpHa1Si = $sub1Sib / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1Si, 2) . '</td>';
	@$sub2Sib = $sub2Si / $prodBgtSi;
	$tab .= '<td align=right>' . number_format($sub2Sib, 2) . '</td>';
	@$rpHa2Si = $sub2Sib / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2Si, 2) . '</td>';
	@$prsnA1Si = ($sub1Sib / $sub2Sib) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1Si, 0) . '</td>';
	@$sub3b = $sub3 / $prodBgtSi;
	$tab .= '<td align=right>' . number_format($sub3b, 0) . '</td>';
	$rpHa3 = $sub3b / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa3, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '</tr>';
}
else {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '</tr>';
}

$cekpan = count($dtKegiatanPnn);

if ($cekpan != 0) {
	foreach ($dtKegiatanPnn as $lstPanen) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $lstPanen . '</td>';
		$tab .= '<td>' . $optKeg[$lstPanen] . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahRealPnn[$lstPanen], 2) . '</td>';
		@$rpHa15[$lstPanen] = $dtRupiahRealPnn[$lstPanen] / $rRealLuas['luasreal'];
		$tab .= '<td align=right>' . number_format($rpHa15[$lstPanen], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtlPnn[$lstPanen], 2) . '</td>';
		@$rpHa25[$lstPanen] = $dtRupiahBgtlPnn[$lstPanen] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa25[$lstPanen], 2) . '</td>';
		@$prsnA15[$lstPanen] = ($dtRupiahRealTm[$lstPanen] / $dtRupiahBgtTm[$lstPanen]) * 100;
		$tab .= '<td align=right>' . number_format($prsnA15[$lstPanen], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahRealPnnSi[$lstPanen], 2) . '</td>';
		@$rpHa5Si[$lstPanen] = $dtRupiahRealPnnSi[$lstPanen] / $rRealLuas['luasreal'];
		$tab .= '<td align=right>' . number_format($rpHa5Si[$lstPanen], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtlPnnSi[$lstPanen], 2) . '</td>';
		@$rpHa3Si[$lstPanen] = $dtRupiahBgtlPnnSi[$lstPanen] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa3Si[$lstPanen], 2) . '</td>';
		@$prsnA2Si[$lstPanen] = ($dtRupiahRealPnnSi[$lstPanen] / $dtRupiahBgtlPnnSi[$lstPanen]) * 100;
		$tab .= '<td align=right>' . number_format($prsnA2Si[$lstPanen], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtRupiahBgtThnPnn[$lstPanen], 0) . '</td>';
		$rpHa5[$lstPanen] = $dtRupiahBgtThnPnn[$lstPanen] / $rRealLuas2['luasbgt'];
		$tab .= '<td align=right>' . number_format($rpHa5[$lstPanen], 0) . '</td>';
		$tab .= '</tr>';
		$sub5 += $dtRupiahRealPnn[$lstPanen];
		$sub6 += $dtRupiahBgtlPnn[$lstPanen];
		$sub5Si += $dtRupiahRealPnnSi[$lstPanen];
		$sub6Si += $dtRupiahBgtlPnnSi[$lstPanen];
		$sub7 += $dtRupiahBgtThnPnn[$lstPanen];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['subtotal'] . ' Panen</td>';
	$tab .= '<td align=right>' . number_format($sub5, 2) . '</td>';
	@$rpHa1 = $sub5 / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sub6, 2) . '</td>';
	@$rpHa2 = $sub6 / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2, 2) . '</td>';
	@$prsnA1 = ($sub5 / $sub6) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sub5Si, 2) . '</td>';
	@$rpHa1Si = $sub5Si / $rRealLuas['luasreal'];
	$tab .= '<td align=right>' . number_format($rpHa1Si, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($sub6Si, 2) . '</td>';
	@$rpHa2Si = $sub6Si / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa2Si, 2) . '</td>';
	@$prsnA1Si = ($sub5Si / $sub6Si) * 100;
	$tab .= '<td align=right>' . number_format($prsnA1Si, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($sub7, 0) . '</td>';
	$rpHa3 = $sub7 / $rRealLuas2['luasbgt'];
	$tab .= '<td align=right>' . number_format($rpHa3, 0) . '</td>';
	$tab .= '</tr>';
}
else {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '<td align=right>&nbsp;</td>';
	$tab .= '</tr>';
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
$tot1 = $sub1 + $sub5;
$tab .= '<td align=right>' . number_format($tot1, 2) . '</td>';
@$rpHa1 = $tot1 / $rRealLuas['luasreal'];
$tot2 = $sub2 + $sub6;
$tab .= '<td align=right>' . number_format($rpHa1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($tot2, 2) . '</td>';
@$rpHa2 = $tot2 / $rRealLuas2['luasbgt'];
$tab .= '<td align=right>' . number_format($rpHa2, 2) . '</td>';
@$prsnA1 = ($tot1 / $tot2) * 100;
$tab .= '<td align=right>' . number_format($prsnA1, 0) . '</td>';
$tot1Si = $sub1Si + $sub5Si;
$tab .= '<td align=right>' . number_format($tot1Si, 2) . '</td>';
@$rpHa1Si = $tot1Si / $rRealLuas['luasreal'];
$tab .= '<td align=right>' . number_format($rpHa1Si, 2) . '</td>';
$tot2Si = $sub2Si + $sub6Si;
$tab .= '<td align=right>' . number_format($tot2Si, 2) . '</td>';
@$rpHa2Si = $tot2Si / $rRealLuas2['luasbgt'];
$tab .= '<td align=right>' . number_format($rpHa2Si, 2) . '</td>';
@$prsnA1Si = ($tot1Si / $tot2Si) * 100;
$tab .= '<td align=right>' . number_format($prsnA1Si, 0) . '</td>';
$totThan = $sub3 + $sub7;
$tab .= '<td align=right>' . number_format($totThan, 0) . '</td>';
$rpHa3 = $totThan / $rRealLuas2['luasbgt'];
$tab .= '<td align=right>' . number_format($rpHa3, 0) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field required');
	}

	echo $tab;
	break;

case 'excel':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field required');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lbm_biayapemeliharan_tm_' . $unit . $periode;

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

case 'pdf':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field required');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $unit;
			global $optNm;
			global $optBulan;
			global $tahun;
			global $bulan;
			global $dbname;
			global $rRealLuas;
			global $wkiri;
			global $judul;
			global $wlain;
			global $afdId;
			global $rRealLuas2;
			global $luasreal;
			global $prodReal;
			global $prodBgt;
			global $prodRealSi;
			global $prodBgtSi;
			global $sthn;
			global $tonhareal;
			global $tonhabgt;
			global $tonharealSi;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width / 2, $height, strtoupper($judul), NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);

			if ($afdId != '') {
				$this->Ln();
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')', NULL, 0, 'L', 1);
			}

			$this->Ln();
			$this->Ln();
			$height = 15;
			$this->SetFont('Arial', 'B', 7);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['namakegiatan'], TBRL, 0, 'C', 1);
			$this->Cell((($wlain * 7) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 7) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'], 1, 1, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['kegiatan'], RL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, ' ', BRL, 0, 'L', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 1) / 100) * $width, $height, '%', 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 1) / 100) * $width, $height, '%', 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, '', RL, 1, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', RL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['luasareal'], TBRL, 0, 'L', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($rRealLuas['luasreal'], 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($rRealLuas2['luasbgt'], 2), 1, 0, 'R', 1);
			@$prsn1 = ($rRealLuas['luasreal'] / $rRealLuas2['luasbgt']) * 100;
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsn1, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($rRealLuas['luasreal'], 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($rRealLuas2['luasbgt'], 2), 1, 0, 'R', 1);
			@$prsn1 = ($rRealLuas['luasreal'] / $rRealLuas2['luasbgt']) * 100;
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsn1, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($rRealLuas2['luasbgt'], 2), TBRL, 1, 'R', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', RL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['produksi'] . ' (TON)', TBRL, 0, 'L', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($prodReal, 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($prodBgt, 2), 1, 0, 'R', 1);
			@$prsn2 = ($prodReal / $prodBgt) * 100;
			@$tonhareal = $prodReal / $rRealLuas['luasreal'];
			@$tonhabgt = $prodBgt / $rRealLuas2['luasbgt'];
			@$prsntonha = ($tonhareal / $tonhabgt) * 100;
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsn2, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($prodRealSi, 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($prodBgtSi, 2), 1, 0, 'R', 1);
			@$prsn3 = ($prodRealSi / $prodBgtSi) * 100;
			@$tonharealSi = $prodRealSi / $rRealLuas['luasreal'];
			@$tonhabgtSi = $prodBgtSi / $rRealLuas2['luasbgt'];
			@$prsntonhaSi = ($tonharealSi / $tonhabgtSi) * 100;
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsn3, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($sthn, 2), TBRL, 1, 'R', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', RL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, 'TON/HA', TBRL, 0, 'L', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($tonhareal, 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($tonhabgt, 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsntonha, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($tonharealSi, 2), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($tonhabgtSi, 2), 1, 0, 'R', 1);
			@$thnData = $sthn / $rRealLuas2['luasbgt'];
			$this->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsntonhaSi, 0), 1, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, number_format($thnData, 2), TBRL, 1, 'R', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, ' ', TBRL, 0, 'L', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, 'RP/HA', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, 'RP/HA', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, 'RP/HA', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, 'RP/HA', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$this->Cell((($wlain * 1.5) / 100) * $width, $height, 'RP/HA', TBRL, 1, 'R', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$cols = 247.5;
	$wkiri = 14;
	$wlain = 4.2999999999999998;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 6);

	foreach ($dtKegiatan as $lstKegiatan) {
		$pdf->Cell((($wlain * 2) / 100) * $width, $height, $lstKegiatan, 1, 0, 'C', 1);
		$pdf->Cell((($wlain * 4) / 100) * $width, $height, $optKeg[$lstKegiatan], 1, 0, 'L', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahRealTm[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$rpHa1[$lstKegiatan] = $dtRupiahRealTm[$lstKegiatan] / $rRealLuas['luasreal'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1[$lstKegiatan], 2), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtTm[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$rpHa2[$lstKegiatan] = $dtRupiahBgtTm[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$prsnA1[$lstKegiatan] = ($dtRupiahRealTm[$lstKegiatan] / $dtRupiahBgtTm[$lstKegiatan]) * 100;
		$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1[$lstKegiatan], 0), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahRealTmSi[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$rpHa1Si[$lstKegiatan] = $dtRupiahRealTmSi[$lstKegiatan] / $rRealLuas['luasreal'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1Si[$lstKegiatan], 2), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtTmSi[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$rpHa2Si[$lstKegiatan] = $dtRupiahBgtTmSi[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2Si[$lstKegiatan], 2), 1, 0, 'R', 1);
		@$prsnA1Si[$lstKegiatan] = ($dtRupiahRealTmSi[$lstKegiatan] / $dtRupiahBgtTmSi[$lstKegiatan]) * 100;
		$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1Si[$lstKegiatan], 0), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtThn[$lstKegiatan], 0), 1, 0, 'R', 1);
		@$rpHa3v[$lstKegiatan] = $dtRupiahBgtThn[$lstKegiatan] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa3v[$lstKegiatan], 0), TBRL, 1, 'R', 1);
	}

	$pdf->SetFont('Arial', '', 5);
	$pdf->Cell((($wlain * 6) / 100) * $width, $height, $_SESSION['lang']['subtotal'] . ' Biaya Pemeliharaan (A)', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub1, 2), 1, 0, 'R', 1);
	@$rpHa1 = $sub1 / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub2, 2), 1, 0, 'R', 1);
	@$rpHa2 = $sub2 / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2, 2), 1, 0, 'R', 1);
	@$prsnA1 = ($sub1 / $sub2) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub1Si, 2), 1, 0, 'R', 1);
	@$rpHa1Si = $sub1Si / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1Si, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub2Si, 2), 1, 0, 'R', 1);
	@$rpHa2Si = $sub2Si / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2Si, 2), 1, 0, 'R', 1);
	@$prsnA1Si = ($sub1Si / $sub2Si) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1Si, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub3, 0), 1, 0, 'R', 1);
	@$rpHa3 = $sub3 / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa3, 0), TBRL, 1, 'R', 1);
	$pdf->SetFont('Arial', '', 6);

	foreach ($dtKegiatanPnn as $lstPanen) {
		$pdf->Cell((($wlain * 2) / 100) * $width, $height, $lstPanen, 1, 0, 'C', 1);
		$pdf->Cell((($wlain * 4) / 100) * $width, $height, $optKeg[$lstPanen], 1, 0, 'L', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahRealPnn[$lstPanen], 2), 1, 0, 'R', 1);
		@$rpHa15[$lstPanen] = $dtRupiahRealPnn[$lstPanen] / $rRealLuas['luasreal'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa15[$lstPanen], 2), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtlPnn[$lstPanen], 2), 1, 0, 'R', 1);
		@$rpHa25[$lstPanen] = $dtRupiahBgtlPnn[$lstPanen] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa25[$lstPanen], 2), 1, 0, 'R', 1);
		@$prsnA15[$lstPanen] = ($dtRupiahRealTm[$lstPanen] / $dtRupiahBgtTm[$lstPanen]) * 100;
		$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA15[$lstPanen], 0), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahRealPnnSi[$lstPanen], 2), 1, 0, 'R', 1);
		@$rpHa5Si[$lstPanen] = $dtRupiahRealPnnSi[$lstPanen] / $rRealLuas['luasreal'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa5Si[$lstPanen], 2), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtlPnnSi[$lstPanen], 2), 1, 0, 'R', 1);
		@$rpHa3Si[$lstPanen] = $dtRupiahBgtlPnnSi[$lstPanen] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa3Si[$lstPanen], 2), 1, 0, 'R', 1);
		@$prsnA2Si[$lstPanen] = ($dtRupiahRealPnnSi[$lstPanen] / $dtRupiahBgtlPnnSi[$lstPanen]) * 100;
		$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA2Si[$lstPanen], 0), 1, 0, 'R', 1);
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($dtRupiahBgtThnPnn[$lstPanen], 0), 1, 0, 'R', 1);
		@$rpHa5[$lstPanen] = $dtRupiahBgtThnPnn[$lstPanen] / $rRealLuas2['luasbgt'];
		$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa5[$lstPanen], 0), TBRL, 1, 'R', 1);
	}

	$pdf->SetFont('Arial', '', 5);
	$pdf->Cell((($wlain * 6) / 100) * $width, $height, $_SESSION['lang']['subtotal'] . ' Biaya Panen (B)', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub5, 2), 1, 0, 'R', 1);
	@$rpHa1 = $sub5 / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub6, 2), 1, 0, 'R', 1);
	@$rpHa2 = $sub6 / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2, 2), 1, 0, 'R', 1);
	@$prsnA1 = ($sub5 / $sub6) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub5Si, 2), 1, 0, 'R', 1);
	@$rpHa1Si = $sub5Si / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1Si, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub6Si, 2), 1, 0, 'R', 1);
	@$rpHa2Si = $sub6Si / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2Si, 2), 1, 0, 'R', 1);
	@$prsnA1Si = ($sub5Si / $sub6Si) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1Si, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($sub7, 0), 1, 0, 'R', 1);
	@$rpHa3r = $sub7 / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa3r, 0), TBRL, 1, 'R', 1);
	$pdf->Cell((($wlain * 6) / 100) * $width, $height, $_SESSION['lang']['total'] . ' A+B', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($tot1, 2), 1, 0, 'R', 1);
	@$rpHa1 = $tot1 / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($tot2, 2), 1, 0, 'R', 1);
	@$rpHa2 = $tot2 / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2, 2), 1, 0, 'R', 1);
	@$prsnA1 = ($tot1 / $tot2) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($tot1Si, 2), 1, 0, 'R', 1);
	@$rpHa1Si = $tot1Si / $rRealLuas['luasreal'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa1Si, 2), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($tot2Si, 2), 1, 0, 'R', 1);
	@$rpHa2Si = $tot2Si / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa2Si, 2), 1, 0, 'R', 1);
	@$prsnA1Si = ($tot1Si / $tot2Si) * 100;
	$pdf->Cell((($wlain * 1) / 100) * $width, $height, number_format($prsnA1Si, 0), 1, 0, 'R', 1);
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($totThan, 0), 1, 0, 'R', 1);
	@$rpHa3e = $totThan / $rRealLuas2['luasbgt'];
	$pdf->Cell((($wlain * 1.5) / 100) * $width, $height, number_format($rpHa3e, 0), TBRL, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
