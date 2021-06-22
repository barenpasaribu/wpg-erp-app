<?php


function numberformat($qwe, $asd)
{
	if ($qwe == 0) {
		$zxc = '0';
	}
	else {
		$zxc = number_format($qwe, $asd);
	}

	return $zxc;
}

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
$dzArr = array();
$aresta = 'SELECT sum(kgsetahun) as setahun FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeunit like \'' . $unit . '%\' and tahunbudget =\'' . $tahun . '\'';

if ($afdId != '') {
	$aresta = 'SELECT sum(kgsetahun) as setahun FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeblok like \'' . $afdId . '%\' and tahunbudget =\'' . $tahun . '\'';
}

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kgbgth = $res['setahun'];
}

$aresta = 'SELECT sum(kg' . $bulan . ') as bi FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeunit like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

if ($afdId != '') {
	$aresta = 'SELECT sum(kg' . $bulan . ') as bi FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeblok like \'' . $afdId . '%\' and tahunbudget = \'' . $tahun . '\'';
}

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kgbgbi = $res['bi'];
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
$aresta = 'SELECT sum(' . $addstr . ') as sdbi FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeunit like \'' . $unit . '%\' and tahunbudget = \'' . $tahun . '\'';

if ($afdId != '') {
	$aresta = 'SELECT sum(' . $addstr . ') as sdbi FROM ' . $dbname . '.bgt_produksi_kbn_kg_vw' . "\r\n" . '    WHERE kodeblok like \'' . $afdId . '%\' and tahunbudget = \'' . $tahun . '\'';
}

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kgbgsd = $res['sdbi'];
}

$aresta = 'SELECT sum(beratbersih-kgpotsortasi) as bi FROM ' . $dbname . '.pabrik_timbangan' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\'';

if ($afdId != '') {
	$aresta = 'SELECT sum(beratbersih-kgpotsortasi) as bi FROM ' . $dbname . '.pabrik_timbangan' . "\r\n" . '    WHERE nospb like \'%' . $afdId . '%\' and tanggal like \'' . $periode . '%\'';
}

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kgrebi = $res['bi'];
}

$aresta = 'SELECT sum(beratbersih-kgpotsortasi) as sdbi FROM ' . $dbname . '.pabrik_timbangan' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\' and (substr(tanggal,1,10) between \'' . $tahun . '-01-01 00:00:00\' and LAST_DAY(\'' . $periode . '-15\'))';

if ($afdId != '') {
	$aresta = 'SELECT sum(beratbersih-kgpotsortasi) as sdbi FROM ' . $dbname . '.pabrik_timbangan' . "\r\n" . '    WHERE nospb like \'%' . $afdId . '%\' and (substr(tanggal,1,10) between \'' . $tahun . '-01-01 00:00:00\' and LAST_DAY(\'' . $periode . '-15\'))';
}

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kgresd = $res['sdbi'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and noakun like \'611%\'' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kegpanen[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$kegpanen[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$kegpanen[$res['noakun']]['namaakun'] = $res['namaakun'];
	}
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and noakun like \'621%\'' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kegpemel[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$kegpemel[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$kegpemel[$res['noakun']]['namaakun'] = $res['namaakun'];
	}
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun' . "\r\n" . '    WHERE length(noakun)=7 and noakun like \'71%\'' . "\r\n" . '    ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kegtidak[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$kegtidak[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$kegtidak[$res['noakun']]['namaakun'] = $res['namaakun'];
	}
}

$str = 'SELECT noakun, setahun FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][111] = $res['setahun'];
	@$dzArr[$res['noakun']][112] = $res['setahun'] / $kgbgth;
}

$str = 'SELECT noakun, rp' . $bulan . ' as bi FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][121] = $res['bi'];
	@$dzArr[$res['noakun']][122] = $res['bi'] / $kgbgbi;
}

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
$str = 'SELECT noakun, ' . $addstr2 . ' as jumlah FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][131] = $res['jumlah'];
	@$dzArr[$res['noakun']][132] = $res['jumlah'] / $kgbgsd;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\'' . "\r\n" . '    GROUP BY noakun';

if ($afdId != '') {
	$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeblok like \'%' . $afdId . '%\'' . "\r\n" . '    GROUP BY noakun';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][211] = $res['jumlah'];
	@$dzArr[$res['noakun']][212] = $res['jumlah'] / $kgrebi;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE (substr(tanggal,1,10) between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and nojurnal like \'%' . $unit . '%\'' . "\r\n" . '    GROUP BY noakun';

if ($afdId != '') {
	$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE (substr(tanggal,1,10) between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeblok like \'%' . $afdId . '%\'' . "\r\n" . '    GROUP BY noakun';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][221] = $res['jumlah'];
	@$dzArr[$res['noakun']][222] = $res['jumlah'] / $kgresd;
}

if (!empty($kegpanen)) {
	foreach ($kegpanen as $keg) {
		@$dzArr[$keg['noakun']][311] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][111];
		@$dzArr[$keg['noakun']][312] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][131];
	}
}

if (!empty($kegpemel)) {
	foreach ($kegpemel as $keg) {
		@$dzArr[$keg['noakun']][311] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][111];
		@$dzArr[$keg['noakun']][312] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][131];
	}
}

if (!empty($kegtidak)) {
	foreach ($kegtidak as $keg) {
		@$dzArr[$keg['noakun']][311] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][111];
		@$dzArr[$keg['noakun']][312] = (100 * $dzArr[$keg['noakun']][221]) / $dzArr[$keg['noakun']][131];
	}
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=8 align=left><font size=3>20. ' . strtoupper($_SESSION['lang']['biaya']) . ' ' . strtoupper($_SESSION['lang']['produksi']) . '</font></td>' . "\r\n" . '        <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>  ';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=14 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')</td></tr>  ';
	}

	$tab .= '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . strtoupper($_SESSION['lang']['produksi']) . ' (kg):</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($kgbgth, 2) . '</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($kgbgbi, 2) . '</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($kgbgsd, 2) . '</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($kgrebi, 2) . '</td>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . numberformat($kgresd, 2) . '</td>' . "\r\n" . '    <td align=left colspan=2' . $bg . '></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['pekerjaan'] . '</td>' . "\r\n" . '    <td align=center colspan=6 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=2 ' . $bg . '>% ' . $_SESSION['lang']['pencapaian'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./kg</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./kg</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./kg</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./kg</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp. (000)</td>' . "\r\n" . '    <td align=center ' . $bg . '>Rp./kg</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=14>Data Empty.</td></tr>';
}
else {
	if (!empty($kegpanen)) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=right>A.</td>';
		$tab .= '<td>' . strtoupper($_SESSION['lang']['panen']) . '</td><td colspan=12>&nbsp;</td>';
		$tab .= '</tr>';
		$totalpanen = array();
		$no = 1;

		foreach ($kegpanen as $keg) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $keg['namaakun'] . '</td>';
			$totalpanen += 111;
			$totalpanen += 112;
			$totalpanen += 121;
			$totalpanen += 122;
			$totalpanen += 131;
			$totalpanen += 132;
			$totalpanen += 211;
			$totalpanen += 212;
			$totalpanen += 221;
			$totalpanen += 222;
			$totalpanen += 311;
			$totalpanen += 312;
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][112], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][122], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][132], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][212], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][222], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][312], 2) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=2 align=center>' . strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['panen']) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[111] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[112], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[121] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[122], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[131] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[132], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[211] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[212], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[221] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpanen[222], 2) . '</td>';
		@$panen311 = (100 * $totalpanen[221]) / $totalpanen[111];
		@$panen312 = (100 * $totalpanen[221]) / $totalpanen[131];
		$tab .= '<td align=right>' . numberformat($panen311, 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($panen312, 2) . '</td>';
		$tab .= '</tr>';
	}

	if (!empty($kegpanen)) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=right>B.</td>';
		$tab .= '<td>' . strtoupper($_SESSION['lang']['pemeltanaman']) . ' ' . strtoupper($_SESSION['lang']['TM']) . ' </td><td colspan=12>&nbsp;</td>';
		$tab .= '</tr>';
		$totalpemel = array();
		$no = 1;

		foreach ($kegpemel as $keg) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $keg['namaakun'] . '</td>';
			$totalpemel += 111;
			$totalpemel += 112;
			$totalpemel += 121;
			$totalpemel += 122;
			$totalpemel += 131;
			$totalpemel += 132;
			$totalpemel += 211;
			$totalpemel += 212;
			$totalpemel += 221;
			$totalpemel += 222;
			$totalpemel += 311;
			$totalpemel += 312;
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][112], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][122], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][132], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][212], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][222], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][312], 2) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=2 align=center>' . strtoupper($_SESSION['lang']['biaya']) . ' ' . strtoupper($_SESSION['lang']['pemeltanaman']) . ' ' . strtoupper($_SESSION['lang']['TM']) . ' </td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[111] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[112], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[121] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[122], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[131] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[132], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[211] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[212], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[221] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totalpemel[222], 2) . '</td>';
		@$pemel311 = (100 * $totalpemel[221]) / $totalpemel[111];
		@$pemel312 = (100 * $totalpemel[221]) / $totalpemel[131];
		$tab .= '<td align=right>' . numberformat($pemel311, 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($pemel312, 2) . '</td>';
		$tab .= '</tr>';
	}

	if (!empty($kegpanen)) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=right>C.</td>';
		$tab .= '<td>BIAYA TIDAK LANGSUNG (OVER HEAD)</td><td colspan=12>&nbsp;</td>';
		$tab .= '</tr>';
		$totaltidak = array();
		$no = 1;

		foreach ($kegtidak as $keg) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $keg['namaakun'] . '</td>';
			$totaltidak += 111;
			$totaltidak += 112;
			$totaltidak += 121;
			$totaltidak += 122;
			$totaltidak += 131;
			$totaltidak += 132;
			$totaltidak += 211;
			$totaltidak += 212;
			$totaltidak += 221;
			$totaltidak += 222;
			$totaltidak += 311;
			$totaltidak += 312;
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][112], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][122], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][132], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][212], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][222], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg['noakun']][312], 2) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=2 align=center>BIAYA TIDAK LANGSUNG (OVER HEAD)</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[111] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[112], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[121] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[122], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[131] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[132], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[211] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[212], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[221] / 1000, 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($totaltidak[222], 2) . '</td>';
		@$tidak311 = (100 * $totaltidak[221]) / $totaltidak[111];
		@$tidak312 = (100 * $totaltidak[221]) / $totaltidak[131];
		$tab .= '<td align=right>' . numberformat($tidak311, 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($tidak312, 2) . '</td>';
		$tab .= '</tr>';
	}

	$totalbiaya[111] = $totalpanen[111] + $totalpemel[111] + $totaltidak[111];
	$totalbiaya[112] = $totalpanen[112] + $totalpemel[112] + $totaltidak[112];
	$totalbiaya[121] = $totalpanen[121] + $totalpemel[121] + $totaltidak[121];
	$totalbiaya[122] = $totalpanen[122] + $totalpemel[122] + $totaltidak[122];
	$totalbiaya[131] = $totalpanen[131] + $totalpemel[131] + $totaltidak[131];
	$totalbiaya[132] = $totalpanen[132] + $totalpemel[132] + $totaltidak[132];
	$totalbiaya[211] = $totalpanen[211] + $totalpemel[211] + $totaltidak[211];
	$totalbiaya[212] = $totalpanen[212] + $totalpemel[212] + $totaltidak[212];
	$totalbiaya[221] = $totalpanen[221] + $totalpemel[221] + $totaltidak[221];
	$totalbiaya[222] = $totalpanen[222] + $totalpemel[222] + $totaltidak[222];
	$totalbiaya[311] = $totalpanen[311] + $totalpemel[311] + $totaltidak[311];
	$totalbiaya[312] = $totalpanen[312] + $totalpemel[312] + $totaltidak[312];
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2 align=center>TOTAL ' . strtoupper($_SESSION['lang']['biaya']) . ' ' . strtoupper($_SESSION['lang']['produksi']) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[112], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[122], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[132], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[212], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($totalbiaya[222], 2) . '</td>';
	@$biaya311 = (100 * $totalbiaya[221]) / $totalbiaya[111];
	@$biaya312 = (100 * $totalbiaya[221]) / $totalbiaya[131];
	$tab .= '<td align=right>' . numberformat($biaya311, 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($biaya312, 2) . '</td>';
	$tab .= '</tr>';
}

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
	$nop_ = 'lbm_biayaroduksi_' . $unit . $periode;

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
			global $luas;
			global $wkiri;
			global $wlain;
			global $afdId;
			global $kgbgth;
			global $kgbgbi;
			global $kgbgsd;
			global $kgrebi;
			global $kgresd;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width / 2, $height, '20. ' . strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['produksi']) . ' (RP./KG)', NULL, 0, 'L', 1);
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
			$this->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Produksi (kg):', 0, 0, 'R', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($kgbgth, 2) . '', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($kgbgbi, 2) . '', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($kgbgsd, 2) . '', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($kgrebi, 2) . '', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($kgresd, 2) . '', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', 0, 0, 'L', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 6) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, 'No.', RL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], RL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['pencapaian'], BRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./kg', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./kg', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./kg', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./kg', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp. (000)', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Rp./kg', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
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
	$wkiri = 24;
	$wlain = 6;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 1;

	if (empty($dzArr)) {
		echo 'Data Empty.';
	}
	else {
		if (!empty($kegpanen)) {
			$pdf->Cell((3 / 100) * $width, $height, 'A.', 1, 0, 'R', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, strtoupper($_SESSION['lang']['panen']), 1, 0, 'L', 1);
			$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$pdf->Ln();
			$totalpanen = array();
			$no = 1;

			foreach ($kegpanen as $keg) {
				$totalpanen += 111;
				$totalpanen += 112;
				$totalpanen += 121;
				$totalpanen += 122;
				$totalpanen += 131;
				$totalpanen += 132;
				$totalpanen += 211;
				$totalpanen += 212;
				$totalpanen += 221;
				$totalpanen += 222;
				$totalpanen += 311;
				$totalpanen += 312;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $keg['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][112], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][122], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][132], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][212], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][222], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][311], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][312], 2), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}

			$pdf->Cell((($wkiri / 100) * $width) + ((3 / 100) * $width), $height, strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['panen']), 1, 0, 'C', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[111] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[112], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[121] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[122], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[131] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[132], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[211] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[212], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[221] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpanen[222], 2), 1, 0, 'R', 1);
			@$panen311 = (100 * $totalpanen[221]) / $totalpanen[111];
			@$panen312 = (100 * $totalpanen[221]) / $totalpanen[131];
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($panen311, 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($panen312, 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}

		if (!empty($kegpanen)) {
			$pdf->Cell((3 / 100) * $width, $height, 'B.', 1, 0, 'R', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, strtoupper($_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['TM']), 1, 0, 'L', 1);
			$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$pdf->Ln();
			$totalpemel = array();
			$no = 1;

			foreach ($kegpemel as $keg) {
				$totalpemel += 111;
				$totalpemel += 112;
				$totalpemel += 121;
				$totalpemel += 122;
				$totalpemel += 131;
				$totalpemel += 132;
				$totalpemel += 211;
				$totalpemel += 212;
				$totalpemel += 221;
				$totalpemel += 222;
				$totalpemel += 311;
				$totalpemel += 312;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $keg['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][112], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][122], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][132], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][212], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][222], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][311], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][312], 2), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}

			$pdf->Cell((($wkiri / 100) * $width) + ((3 / 100) * $width), $height, strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['TM']), 1, 0, 'C', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[111] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[112], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[121] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[122], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[131] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[132], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[211] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[212], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[221] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalpemel[222], 2), 1, 0, 'R', 1);
			@$pemel311 = (100 * $totalpemel[221]) / $totalpemel[111];
			@$pemel312 = (100 * $totalpemel[221]) / $totalpemel[131];
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($pemel311, 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($pemel312, 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}

		if (!empty($kegtidak)) {
			$pdf->Cell((3 / 100) * $width, $height, 'C.', 1, 0, 'R', 1);
			$pdf->Cell(($wkiri / 100) * $width, $height, 'BIAYA TIDAK LANGSUNG (OVER HEAD)', 1, 0, 'L', 1);
			$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
			$pdf->Ln();
			$totalpemel = array();
			$no = 1;

			foreach ($kegtidak as $keg) {
				$totaltidak += 111;
				$totaltidak += 112;
				$totaltidak += 121;
				$totaltidak += 122;
				$totaltidak += 131;
				$totaltidak += 132;
				$totaltidak += 211;
				$totaltidak += 212;
				$totaltidak += 221;
				$totaltidak += 222;
				$totaltidak += 311;
				$totaltidak += 312;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $keg['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][112], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][122], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][132], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][212], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][222], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][311], 2), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg['noakun']][312], 2), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}

			$pdf->Cell((($wkiri / 100) * $width) + ((3 / 100) * $width), $height, 'BIAYA TIDAK LANGSUNG (OVER HEAD)', 1, 0, 'C', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[111] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[112], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[121] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[122], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[131] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[132], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[211] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[212], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[221] / 1000, 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totaltidak[222], 2), 1, 0, 'R', 1);
			@$tidak311 = (100 * $totaltidak[221]) / $totaltidak[111];
			@$tidak312 = (100 * $totaltidak[221]) / $totaltidak[131];
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($tidak311, 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($tidak312, 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}

		$totalbiaya[111] = $totalpanen[111] + $totalpemel[111] + $totaltidak[111];
		$totalbiaya[112] = $totalpanen[112] + $totalpemel[112] + $totaltidak[112];
		$totalbiaya[121] = $totalpanen[121] + $totalpemel[121] + $totaltidak[121];
		$totalbiaya[122] = $totalpanen[122] + $totalpemel[122] + $totaltidak[122];
		$totalbiaya[131] = $totalpanen[131] + $totalpemel[131] + $totaltidak[131];
		$totalbiaya[132] = $totalpanen[132] + $totalpemel[132] + $totaltidak[132];
		$totalbiaya[211] = $totalpanen[211] + $totalpemel[211] + $totaltidak[211];
		$totalbiaya[212] = $totalpanen[212] + $totalpemel[212] + $totaltidak[212];
		$totalbiaya[221] = $totalpanen[221] + $totalpemel[221] + $totaltidak[221];
		$totalbiaya[222] = $totalpanen[222] + $totalpemel[222] + $totaltidak[222];
		$totalbiaya[311] = $totalpanen[311] + $totalpemel[311] + $totaltidak[311];
		$totalbiaya[312] = $totalpanen[312] + $totalpemel[312] + $totaltidak[312];
		$pdf->Cell((($wkiri / 100) * $width) + ((3 / 100) * $width), $height, 'TOTAL ' . strtoupper($_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['produksi']), 1, 0, 'C', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[112], 2), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[122], 2), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[132], 2), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[212], 2), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($totalbiaya[222], 2), 1, 0, 'R', 1);
		@$biaya311 = (100 * $totalbiaya[221]) / $totalbiaya[111];
		@$biaya312 = (100 * $totalbiaya[221]) / $totalbiaya[131];
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($biaya311, 2), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($biaya312, 2), 1, 0, 'R', 1);
		$pdf->Ln();
	}

	$pdf->Output();
	break;
}

?>
