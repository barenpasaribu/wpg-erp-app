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
	$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1');
}
else {
	$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
}

$optKegSat = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
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

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '         <tr>' . "\r\n" . '            <td colspan=8 align=left><font size=3>' . strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['tb']) . ' (TB)</font></td>' . "\r\n" . '            <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '         </tr> ' . "\r\n" . '         <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>';

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
$sRealLuas = 'select distinct sum(luasareaproduktif) as luasreal from ' . $dbname . '.setup_blok ' . "\r\n" . '            where kodeorg like \'' . $unit . '%\' and statusblok in (\'TB\')';

if ($afdId != '') {
	$sRealLuas = 'select distinct sum(luasareaproduktif) as luasreal from ' . $dbname . '.setup_blok ' . "\r\n" . '               where kodeorg like \'' . $afdId . '%\' and statusblok in (\'TB\')';
}

#exit(mysql_error($conn));
($qRealLuas = mysql_query($sRealLuas)) || true;
$rRealLuas = mysql_fetch_assoc($qRealLuas);
$sRealLuas2 = 'select distinct sum(hathnini) as luasbgt from ' . $dbname . '.bgt_blok where ' . "\r\n" . '             kodeblok like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and statusblok=\'TB\'';

if ($afdId != '') {
	$sRealLuas2 = 'select distinct sum(hathnini) as luasbgt from ' . $dbname . '.bgt_blok where ' . "\r\n" . '             kodeblok like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and statusblok=\'TB\'';
}

#exit(mysql_error($conn));
($qRealLuas2 = mysql_query($sRealLuas2)) || true;
$rRealLuas2 = mysql_fetch_assoc($qRealLuas2);
$sData = 'select sum(a.jumlah) as jumlah,a.kodekegiatan,b.satuan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\'    and jumlah>0 and ' . "\r\n" . '        b.kelompok=\'TB\' and a.kodekegiatan!=\'\'' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan,b.satuan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        a.tanggal like \'' . $periode . '%\' and kodeblok like \'' . $afdId . '%\' and  jumlah>0 and ' . "\r\n" . '        b.kelompok=\'TB\' and a.kodekegiatan!=\'\'' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	if (($rData['jumlah'] != 0) || ($rData['jumlah'] != '')) {
		$dtKegiatan[$rData['kodekegiatan']] = $rData['kodekegiatan'];
		$dtRupiah += $rData['kodekegiatan'];
		$dtSatuan[$rData['kegiatan']] = $rData['satuan'];
	}
}

$sData2 = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan,b.satuan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and nojurnal like \'%' . $unit . '%\' and b.kelompok=\'TB\' and a.kodekegiatan!=\'\' and  jumlah>0  ' . "\r\n" . '        group by a.kodekegiatan';

if ($afdId != '') {
	$sData2 = 'select  sum(a.jumlah) as jumlah,a.kodekegiatan,b.satuan  from ' . $dbname . '.keu_jurnaldt a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.noakun=b.noakun where' . "\r\n" . '        left(a.tanggal,7) between \'' . $tahun . '-01\' and  \'' . $periode . '\' ' . "\r\n" . '        and kodeblok like \'' . $afdId . '%\' and b.kelompok=\'TB\' and a.kodekegiatan!=\'\' and  jumlah>0  ' . "\r\n" . '        group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qData2 = mysql_query($sData2)) || true;

while ($rData2 = mysql_fetch_assoc($qData2)) {
	if (($rData2['jumlah'] != '') || ($rData2['jumlah'] != 0)) {
		$dtKegiatan[$rData2['kodekegiatan']] = $rData2['kodekegiatan'];
		$dtRupiahSi += $rData2['kodekegiatan'];
		$dtSatuan[$rData2['kegiatan']] = $rData2['satuan'];
	}
}

$sDbgt = 'select  sum(rp' . $bulan . ') as jumlah,a.kegiatan,b.satuan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group  by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select  sum(rp' . $bulan . ') as jumlah,a.kegiatan,b.satuan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group  by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgt += $rDbgt['kegiatan'];
		$dtSatuan[$rDbgt['kegiatan']] = $rDbgt['satuan'];
	}
}

$sDbgt = 'select  sum(' . $addstr2 . ') as jumlah,a.kegiatan,b.satuan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '       and a.kegiatan!=\'\' group  by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select  sum(' . $addstr2 . ') as jumlah,a.kegiatan,b.satuan from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group  by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahBgtSi += $rDbgt['kegiatan'];
		$dtSatuan[$rDbgt['kegiatan']] = $rDbgt['satuan'];
	}
}

$sVol = 'select  sum(hasilkerja) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '      ' . $dbname . '.kebun_perawatan_vw a left join ' . $dbname . '.setup_kegiatan b ' . "\r\n" . '      on a.kodekegiatan=b.kodekegiatan where tanggal like \'' . $periode . '%\' and ' . "\r\n" . '      a.kodeorg like \'' . $unit . '%\' and tipetransaksi=\'TB\' and jurnal=1' . "\r\n" . '      and a.kodekegiatan!=\'\' group  by a.kodekegiatan';

if ($afdId != '') {
	$sVol = 'select  sum(hasilkerja) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '         ' . $dbname . '.kebun_perawatan_vw a left join ' . $dbname . '.setup_kegiatan b ' . "\r\n" . '         on a.kodekegiatan=b.kodekegiatan where tanggal like \'' . $periode . '%\' and ' . "\r\n" . '         a.kodeorg like \'' . $afdId . '%\'  and tipetransaksi=\'TB\' and jurnal=1' . "\r\n" . '        and a.kodekegiatan!=\'\' group  by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qVol = mysql_query($sVol)) || true;

while ($rVol = mysql_fetch_assoc($qVol)) {
	if (($rVol['volreal'] != 0) || ($rVol['volreal'] != '')) {
		$dtVolReal += $rVol['kodekegiatan'];
		$dtKegiatan[$rVol['kegiatan']] = $rVol['kegiatan'];
		$dtSatuan[$rVol['kegiatan']] = $rVol['satuan'];
	}
}

$sVol2 = 'select  sum(hasilkerjarealisasi) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '        ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan' . "\r\n" . '        where tanggal like \'' . $periode . '%\' and kodeblok like \'' . $unit . '%\' and posting=1' . "\r\n" . '        and a.kodekegiatan!=\'\' group  by a.kodekegiatan';

if ($afdId != '') {
	$sVol2 = 'select  sum(hasilkerjarealisasi) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '           ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan' . "\r\n" . '           where tanggal like \'' . $periode . '%\' and kodeblok like \'' . $afdId . '%\' and posting=1' . "\r\n" . '          and a.kodekegiatan!=\'\' group  by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qVol2 = mysql_query($sVol2)) || true;

while ($rVol2 = mysql_fetch_assoc($qVol2)) {
	if (($rVol2['volreal'] != '') || ($rVol2['volreal'] != 0)) {
		$dtVolReal += $rVol2['kodekegiatan'];
		$dtSatuan[$rVol2['kegiatan']] = $rVol2['satuan'];
		$dtKegiatan[$rVol2['kegiatan']] = $rVol2['kegiatan'];
	}
}

$sVol = 'select  sum(hasilkerja) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '      ' . $dbname . '.kebun_perawatan_vw a left join ' . $dbname . '.setup_kegiatan b ' . "\r\n" . '      on a.kodekegiatan=b.kodekegiatan where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and ' . "\r\n" . '      a.kodeorg like \'' . $unit . '%\' and tipetransaksi=\'TB\' and jurnal=1 and a.kodekegiatan!=\'\'' . "\r\n" . '      group by a.kodekegiatan';

if ($afdId != '') {
	$sVol = 'select  sum(hasilkerja) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '         ' . $dbname . '.kebun_perawatan_vw a left join ' . $dbname . '.setup_kegiatan b ' . "\r\n" . '         on a.kodekegiatan=b.kodekegiatan where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and ' . "\r\n" . '         a.kodeorg like \'' . $afdId . '%\'  and tipetransaksi=\'TB\' and jurnal=1  and a.kodekegiatan!=\'\'' . "\r\n" . '         group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qVol = mysql_query($sVol)) || true;

while ($rVol = mysql_fetch_assoc($qVol)) {
	if (($rVol['volreal'] != 0) || ($rVol['volreal'] != '')) {
		$dtVolRealSi += $rVol['kodekegiatan'];
		$dtKegiatan[$rVol['kegiatan']] = $rVol['kegiatan'];
		$dtSatuan[$rVol['kegiatan']] = $rVol['satuan'];
	}
}

$sVol2 = 'select  sum(hasilkerjarealisasi) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '        ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan' . "\r\n" . '        where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and kodeblok like \'' . $unit . '%\' and posting=1' . "\r\n" . '         and a.kodekegiatan!=\'\' group by a.kodekegiatan';

if ($afdId != '') {
	$sVol2 = 'select  sum(hasilkerjarealisasi) as volreal,a.kodekegiatan,b.satuan from ' . "\r\n" . '           ' . $dbname . '.log_baspk a left join ' . $dbname . '.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan' . "\r\n" . '           where left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' and kodeblok like \'' . $afdId . '%\' and posting=1' . "\r\n" . '            and a.kodekegiatan!=\'\' group by a.kodekegiatan';
}

#exit(mysql_error($conn));
($qVol2 = mysql_query($sVol2)) || true;

while ($rVol2 = mysql_fetch_assoc($qVol2)) {
	if (($rVol2['volreal'] != 0) || ($rVol2['volreal'] != '')) {
		$dtVolRealSi += $rVol2['kodekegiatan'];
		$dtSatuan[$rVol2['kegiatan']] = $rVol2['satuan'];
		$dtKegiatan[$rVol2['kegiatan']] = $rVol2['kegiatan'];
	}
}

$sDbgt = 'select  sum(volume) as jumlah,a.kegiatan,satuanv from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '         and a.kegiatan!=\'\' group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select  sum(volume) as jumlah,a.kegiatan,satuanv from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtVolumeSthnBgt += $rDbgt['kegiatan'];
	}
}

$sDbgt = 'select  sum(rupiah) as jumlah,a.kegiatan,satuanv from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $unit . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group by a.kegiatan';

if ($afdId != '') {
	$sDbgt = 'select  sum(rupiah) as jumlah,a.kegiatan,satuanv from ' . $dbname . '.bgt_budget_detail a' . "\r\n" . '        left join ' . $dbname . '.setup_kegiatan b on a.kegiatan=b.kodekegiatan' . "\r\n" . '        where a.kodeorg like \'' . $afdId . '%\' and tahunbudget=\'' . $tahun . '\' and b.kelompok=\'TB\'' . "\r\n" . '        and a.kegiatan!=\'\' group by a.kegiatan';
}

#exit(mysql_error($conn));
($qDbgt = mysql_query($sDbgt)) || true;

while ($rDbgt = mysql_fetch_assoc($qDbgt)) {
	if (($rDbgt['jumlah'] != '') || ($rDbgt['jumlah'] != 0)) {
		$dtKegiatan[$rDbgt['kegiatan']] = $rDbgt['kegiatan'];
		$dtRupiahSthnBgt += $rDbgt['kegiatan'];
	}
}

$dcel = count($dtKegiatan);

if ($dcel == 0) {
	exit('Error:Data Kosong');
}

$bgcoloraja = '';

if ($preview == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
}

$tab .= $judul;
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>';
$tab .= '<td colspan=3 ' . $bgcoloraja . ' align=right>' . $_SESSION['lang']['luasareal'] . ' TBM:</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . ' align=right>' . $rRealLuas2['luasbgt'] . '</td>';
$tab .= '<td colspan=6 ' . $bgcoloraja . ' align=left>HA</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . ' align=right>' . $rRealLuas['luasreal'] . '</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . ' align=left>HA</td>';
$tab .= '<td rowspan=3 colspan=2 ' . $bgcoloraja . '>%  ' . $_SESSION['lang']['pencapaian'] . '(Volume)</td>';
$tab .= '<td rowspan=3 colspan=2 ' . $bgcoloraja . '>% Varians Rp. (000) ' . $_SESSION['lang']['realisasi'] . ' vs ' . $_SESSION['lang']['anggaran'] . '</td>';
$tab .= '<td rowspan=4 ' . $bgcoloraja . '>' . $_SESSION['lang']['sisa'] . ' ' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['setahun'] . ' (Rp. 000)</td></tr><tr>';
$tab .= '<td rowspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['kodekegiatan'] . '</td>';
$tab .= '<td rowspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
$tab .= '<td rowspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
$tab .= '<td colspan=9 ' . $bgcoloraja . '>' . $_SESSION['lang']['anggaran'] . '</td>';
$tab .= '<td colspan=6 ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td></tr><tr>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['setahun'] . '</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td>';
$tab .= '</tr><tr>';
$aret = 1;

while ($aret < 6) {
	$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td ' . $bgcoloraja . '>Rp. (000)</td>';
	$tab .= '<td ' . $bgcoloraja . '>Rp./Sat</td>';
	++$aret;
}

$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['setahun'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['bulanini'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['sdbulanini'] . '</td></tr>';
$tab .= '</thead><tbody>';

foreach ($dtKegiatan as $lsKegiata) {
	if ($lsKegiata != '') {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $lsKegiata . '</td>';
		$tab .= '<td>' . $optKeg[$lsKegiata] . '</td>';
		$tab .= '<td>' . $optKegSat[$lsKegiata] . '</td>';
		$tab .= '<td align=right>' . number_format($dtVolumeSthnBgt[$lsKegiata], 0) . '</td>';
		@$rupSthn[$lsKegiata] = $dtRupiahSthnBgt[$lsKegiata] / 1000;
		$tab .= '<td align=right>' . number_format($rupSthn[$lsKegiata], 2) . '</td>';
		@$rpPer1[$lsKegiata] = $rupSthn[$lsKegiata] / $dtVolumeSthnBgt[$lsKegiata];
		$tab .= '<td align=right>' . number_format($rpPer1[$lsKegiata], 2) . '</td>';
		@$rupBln[$lsKegiata] = $dtRupiahBgt[$lsKegiata] / 1000;
		@$volPbln[$lsKegiata] = ($rupBln[$lsKegiata] / $rupSthn[$lsKegiata]) * $dtVolumeSthnBgt[$lsKegiata];
		$tab .= '<td align=right>' . number_format($volPbln[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rupBln[$lsKegiata], 2) . '</td>';
		@$rpPer2[$lsKegiata] = $rupBln[$lsKegiata] / $volPbln[$lsKegiata];
		$tab .= '<td align=right>' . number_format($rpPer2[$lsKegiata], 2) . '</td>';
		@$rupBlnSi[$lsKegiata] = $dtRupiahBgtSi[$lsKegiata] / 1000;
		@$volPblnSi[$lsKegiata] = ($rupBlnSi[$lsKegiata] / $rupSthn[$lsKegiata]) * $dtVolumeSthnBgt[$lsKegiata];
		$tab .= '<td align=right>' . number_format($volPblnSi[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rupBlnSi[$lsKegiata], 2) . '</td>';
		@$rpPer2Si[$lsKegiata] = $rupBlnSi[$lsKegiata] / $volPblnSi[$lsKegiata];
		$tab .= '<td align=right>' . number_format($rpPer2Si[$lsKegiata], 2) . '</td>';
		@$rupBlnReal[$lsKegiata] = $dtRupiah[$lsKegiata] / 1000;
		$tab .= '<td align=right>' . number_format($dtVolReal[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rupBlnReal[$lsKegiata], 2) . '</td>';
		@$rpPer2Real[$lsKegiata] = $rupBlnReal[$lsKegiata] / $dtVolReal[$lsKegiata];
		$tab .= '<td align=right>' . number_format($rpPer2Real[$lsKegiata], 2) . '</td>';
		@$rupBlnRealSi[$lsKegiata] = $dtRupiahSi[$lsKegiata] / 1000;
		$tab .= '<td align=right>' . number_format($dtVolRealSi[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rupBlnRealSi[$lsKegiata], 2) . '</td>';
		@$rpPer2RealSi[$lsKegiata] = $rupBlnRealSi[$lsKegiata] / $dtVolRealSi[$lsKegiata];
		$tab .= '<td align=right>' . number_format($rpPer2RealSi[$lsKegiata], 2) . '</td>';
		@$prsniDt[$lsKegiata] = ($dtVolRealSi[$lsKegiata] / $dtVolumeSthnBgt[$lsKegiata]) * 100;
		@$prsniDtSi[$lsKegiata] = ($dtVolRealSi[$lsKegiata] / $volPblnSi[$lsKegiata]) * 100;
		$tab .= '<td align=right>' . number_format($prsniDt[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($prsniDtSi[$lsKegiata], 0) . '</td>';
		@$n[$lsKegiata] = (($rupBlnReal[$lsKegiata] - $rupBlnSi[$lsKegiata]) / $rupBlnSi[$lsKegiata]) * 100;
		@$v[$lsKegiata] = (($rupBlnRealSi[$lsKegiata] - $rupBlnSi[$lsKegiata]) / $rupBlnSi[$lsKegiata]) * 100;
		$tab .= '<td align=right>' . number_format($n[$lsKegiata], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($v[$lsKegiata], 0) . '</td>';
		$w[$lsKegiata] = $rupSthn[$lsKegiata] - $rupBlnRealSi[$lsKegiata];
		$tab .= '<td align=right>' . number_format($w[$lsKegiata], 2) . '</td></tr>';
		$totVolBgtthn += $dtVolumeSthnBgt[$lsKegiata];
		$totRupBgtThn += $rupSthn[$lsKegiata];
		$totVolBgtBln += $volPbln[$lsKegiata];
		$totRupBln += $rupBln[$lsKegiata];
		$totVolBgtBlnSi += $volPblnSi[$lsKegiata];
		$totRupBlnSi += $rupBlnSi[$lsKegiata];
		$totVolReal += $dtVolReal[$lsKegiata];
		$totRupReal += $rupBlnReal[$lsKegiata];
		$totVolRealSi += $dtVolRealSi[$lsKegiata];
		$totRupRealSi += $rupBlnRealSi[$lsKegiata];
	}
}

$tab .= '<tr class=header>';
$tab .= '<td colspan=3>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td align=right>' . number_format($totVolBgtthn, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRupBgtThn, 2) . '</td>';
@$rpPer1 = $totRupBgtThn / $totVolBgtthn;
$tab .= '<td align=right>' . number_format($rpPer1, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totVolBgtBln, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRupBln, 2) . '</td>';
@$rpPer2 = $totRupBln / $totVolBgtBln;
$tab .= '<td align=right>' . number_format($rpPer2, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totVolBgtBlnSi, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRupBlnSi, 2) . '</td>';
@$rpPer2Si = $totRupBlnSi / $totVolBgtBlnSi;
$tab .= '<td align=right>' . number_format($rpPer2Si, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totVolReal, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRupReal, 2) . '</td>';
@$rpPer2Real = $totRupReal / $totVolReal;
$tab .= '<td align=right>' . number_format($rpPer2Real, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totVolRealSi, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRupRealSi, 2) . '</td>';
@$rpPer2RealSi = $totRupRealSi / $totVolRealSi;
$tab .= '<td align=right>' . number_format($rpPer2RealSi, 2) . '</td>';
@$prsniDt = ($totVolRealSi / $totVolBgtBln) * 100;
@$prsniDtSi = ($totVolRealSi / $totVolBgtBlnSi) * 100;
$tab .= '<td align=right>' . number_format($prsniDt, 0) . '</td>';
$tab .= '<td align=right>' . number_format($prsniDtSi, 0) . '</td>';
@$n = (($totRupReal - $totRupBlnSi) / $totRupBlnSi) * 100;
@$v = (($totRupRealSi - $totRupBlnSi) / $totRupBlnSi) * 100;
$tab .= '<td align=right>' . number_format($n, 0) . '</td>';
$tab .= '<td align=right>' . number_format($v, 0) . '</td>';
$w = $totRupBgtThn - $totRupRealSi;
$tab .= '<td align=right>' . number_format($w, 2) . '</td></tr>';
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
	$nop_ = 'lbm_biayapemeliharan_tb_' . $unit . $periode;

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
		exit('Error:Field Tidak Boleh Kosong');
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
			$this->Cell((($wlain * 3) / 100) * $width, $height, 'Anggaran Tahunan', 1, 1, 'C', 1);
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
			$this->Cell((($wlain * 4) / 100) * $width, $height, 'LUAS - HA', TBRL, 0, 'L', 1);
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
			$this->Cell((($wlain * 4) / 100) * $width, $height, 'HASIL PRODUKSI (TON)', TBRL, 0, 'L', 1);
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
