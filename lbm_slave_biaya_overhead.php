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
$aresta = 'SELECT sum(luasareaproduktif) as luasareal FROM ' . $dbname . '.setup_blok' . "\r\n" . '    WHERE kodeorg like \'' . $unit . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$luasreal = $res['luasareal'];
}

$aresta = 'SELECT sum(hathnini) as luasareal FROM ' . $dbname . '.bgt_blok' . "\r\n" . '    WHERE kodeblok like \'' . $unit . '%\' and statusblok =\'TM\' and tahunbudget = \'' . $tahun . '\'';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$luasbudg = $res['luasareal'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71101%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71101[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71102%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71102[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'712%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list7120[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71301%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71301[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71302%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71302[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71303%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71303[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71304%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71304[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71305%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71305[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71306%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71306[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71307%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71307[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71308%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71308[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71308%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71308[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71409%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71409[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71501%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71501[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71502%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71502[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71503%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71503[$res['noakun']] = $res['noakun'];
}

$aresta = 'SELECT noakun, namaakun,namaakun1 FROM ' . $dbname . '.keu_5akun WHERE length(noakun)=7 and noakun like \'71899%\' ORDER BY noakun';

#exit(mysql_error($conn));
($query = mysql_query($aresta)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']]['noakun'] = $res['noakun'];

	if ($_SESSION['language'] == 'EN') {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun1'];
	}
	else {
		$dzArr[$res['noakun']]['namaakun'] = $res['namaakun'];
	}

	$list71899[$res['noakun']] = $res['noakun'];
}

$str = 'SELECT noakun, setahun FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][111] = $res['setahun'];
	@$dzArr[$res['noakun']][112] = $res['setahun'] / $luasreal;
}

$str = 'SELECT noakun, rp' . $bulan . ' as bi FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][121] = $res['bi'];
	@$dzArr[$res['noakun']][122] = $res['bi'] / $luasreal;
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
$str = 'SELECT noakun, ' . $addstr . ' as jumlah FROM ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    WHERE tahunbudget = \'' . $tahun . '\' and unit = \'' . $unit . '\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][131] = $res['jumlah'];
	@$dzArr[$res['noakun']][132] = $res['jumlah'] / $luasreal;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and nojurnal like \'%' . $unit . '%\' group by noakun' . "\r\n" . '    ';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][211] = $res['jumlah'];
	@$dzArr[$res['noakun']][212] = $res['jumlah'] / $luasreal;
}

$str = 'SELECT noakun, sum(jumlah) as jumlah FROM ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and nojurnal like \'%' . $unit . '%\' group by noakun' . "\r\n" . '    ';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['noakun']][221] = $res['jumlah'];
	@$dzArr[$res['noakun']][222] = $res['jumlah'] / $luasreal;
}

if (!empty($dzArr)) {
	foreach ($dzArr as $keg) {
		@$dzArr[$keg['noakun']][311] = (100 * $keg[221]) / $keg[111];
		@$dzArr[$keg['noakun']][312] = (100 * $keg[221]) / $keg[131];
	}
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=8 align=left><font size=3>24. ' . strtoupper($_SESSION['lang']['biayataklangsung']) . '</font></td>' . "\r\n" . '        <td colspan=6 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=14 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>   ' . "\r\n" . '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=right colspan=2 ' . $bg . '>' . $_SESSION['lang']['luas'] . ':</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . numberformat($luasbudg, 2) . ' Ha</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . numberformat($luasreal, 2) . ' Ha</td>' . "\r\n" . '    <td align=right colspan=7 ' . $bg . '>&nbsp;</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['pekerjaan'] . '</td>' . "\r\n" . '    <td align=center colspan=7 ' . $bg . '>' . $_SESSION['lang']['biaya'] . ' (000)</td>' . "\r\n" . '    <td align=center colspan=5 ' . $bg . '>' . $_SESSION['lang']['biaya'] . '/Ha</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['pencapaian'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['setahun'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';
$no = 1;
$total = array();

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=14>Data Empty.</td></tr>';
}
else {
	unset($total1);
	$no = 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['karyawan'] . '</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71101-' . $_SESSION['lang']['biaya'] . '  Staf</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71101)) {
		foreach ($list71101 as $keg) {
			$total1 += 111;
			$total1 += 121;
			$total1 += 131;
			$total1 += 211;
			$total1 += 221;
			$total1 += 311;
			$total1 += 312;
			$total1 += 112;
			$total1 += 122;
			$total1 += 132;
			$total1 += 212;
			$total1 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71102-' . $_SESSION['lang']['biaya'] . '  non staf</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71102)) {
		foreach ($list71102 as $keg) {
			$total1 += 111;
			$total1 += 121;
			$total1 += 131;
			$total1 += 211;
			$total1 += 221;
			$total1 += 311;
			$total1 += 312;
			$total1 += 112;
			$total1 += 122;
			$total1 += 132;
			$total1 += 212;
			$total1 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>7120 Operational ' . $_SESSION['lang']['karyawan'] . ' </td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list7120)) {
		foreach ($list7120 as $keg) {
			$total1 += 111;
			$total1 += 121;
			$total1 += 131;
			$total1 += 211;
			$total1 += 221;
			$total1 += 311;
			$total1 += 312;
			$total1 += 112;
			$total1 += 122;
			$total1 += 132;
			$total1 += 212;
			$total1 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Total ' . $_SESSION['lang']['biaya'] . '  ' . $_SESSION['lang']['karyawan'] . ' </td>';
	$tab .= '<td align=right>' . numberformat($total1[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[311], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[312], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[112], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[122], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[132], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[212], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total1[222], 0) . '</td>';
	$tab .= '</tr>';
	unset($total2);
	$no = 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['biayaadministrasi'] . ' </td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71301 Operational ' . $_SESSION['lang']['kantor'] . '/ ' . $_SESSION['lang']['mess'] . ' </td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71301)) {
		foreach ($list71301 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71302 Biaya Kantor Unit</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71302)) {
		foreach ($list71302 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71303 Biaya Kebersihan</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71303)) {
		foreach ($list71303 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71304 Biaya Telekomunikasi</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71304)) {
		foreach ($list71304 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71305 Biaya Asuransi</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71305)) {
		foreach ($list71305 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71306 Pajak dan Retribusi</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71306)) {
		foreach ($list71306 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71307 Perijinan</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71307)) {
		foreach ($list71307 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71308 Biaya Keamanan dan Sosial Lainnya</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71308)) {
		foreach ($list71308 as $keg) {
			$total2 += 111;
			$total2 += 121;
			$total2 += 131;
			$total2 += 211;
			$total2 += 221;
			$total2 += 311;
			$total2 += 312;
			$total2 += 112;
			$total2 += 122;
			$total2 += 132;
			$total2 += 212;
			$total2 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Total Biaya Administrasi dan Umum</td>';
	$tab .= '<td align=right>' . numberformat($total2[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[311], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[312], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[112], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[122], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[132], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[212], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total2[222], 0) . '</td>';
	$tab .= '</tr>';
	unset($total3);
	$no = 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Biaya Pemeliharaan</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71409 Pemeliharaan</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71409)) {
		foreach ($list71409 as $keg) {
			$total3 += 111;
			$total3 += 121;
			$total3 += 131;
			$total3 += 211;
			$total3 += 221;
			$total3 += 311;
			$total3 += 312;
			$total3 += 112;
			$total3 += 122;
			$total3 += 132;
			$total3 += 212;
			$total3 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Total Biaya Pemeliharaan</td>';
	$tab .= '<td align=right>' . numberformat($total3[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[311], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[312], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[112], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[122], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[132], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[212], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total3[222], 0) . '</td>';
	$tab .= '</tr>';
	unset($total4);
	$no = 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Biaya Administrasi Lainnya</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71501 Pembelian Barang Inventaris (non Asset)</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71501)) {
		foreach ($list71501 as $keg) {
			$total4 += 111;
			$total4 += 121;
			$total4 += 131;
			$total4 += 211;
			$total4 += 221;
			$total4 += 311;
			$total4 += 312;
			$total4 += 112;
			$total4 += 122;
			$total4 += 132;
			$total4 += 212;
			$total4 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71502 Penyusutan Tanaman Menghasilkan/Amortisasi</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71502)) {
		foreach ($list71502 as $keg) {
			$total4 += 111;
			$total4 += 121;
			$total4 += 131;
			$total4 += 211;
			$total4 += 221;
			$total4 += 311;
			$total4 += 312;
			$total4 += 112;
			$total4 += 122;
			$total4 += 132;
			$total4 += 212;
			$total4 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71503 Amortisasi</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71503)) {
		foreach ($list71503 as $keg) {
			$total4 += 111;
			$total4 += 121;
			$total4 += 131;
			$total4 += 211;
			$total4 += 221;
			$total4 += 311;
			$total4 += 312;
			$total4 += 112;
			$total4 += 122;
			$total4 += 132;
			$total4 += 212;
			$total4 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>71899 Biaya Bank Unit</td>';
	$tab .= '<td colspan=12></td>';
	$tab .= '</tr>';

	if (!empty($list71899)) {
		foreach ($list71899 as $keg) {
			$total4 += 111;
			$total4 += 121;
			$total4 += 131;
			$total4 += 211;
			$total4 += 221;
			$total4 += 311;
			$total4 += 312;
			$total4 += 112;
			$total4 += 122;
			$total4 += 132;
			$total4 += 212;
			$total4 += 222;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td align=right>' . $no . '</td>';
			$tab .= '<td>' . $dzArr[$keg]['namaakun'] . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][111] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][121] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][131] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][211] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][221] / 1000, 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][311], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][312], 2) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][112], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][122], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][132], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][212], 0) . '</td>';
			$tab .= '<td align=right>' . numberformat($dzArr[$keg][222], 0) . '</td>';
			$tab .= '</tr>';
			$no += 1;
		}
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Total Biaya Administrasi Lainnya</td>';
	$tab .= '<td align=right>' . numberformat($total4[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[311], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[312], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[112], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[122], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[132], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[212], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total4[222], 0) . '</td>';
	$tab .= '</tr>';
	$total9[111] = $total1[111] + $total2[111] + $total3[111] + $total4[111];
	$total9[121] = $total1[121] + $total2[121] + $total3[121] + $total4[121];
	$total9[131] = $total1[131] + $total2[131] + $total3[131] + $total4[131];
	$total9[211] = $total1[211] + $total2[211] + $total3[211] + $total4[211];
	$total9[221] = $total1[221] + $total2[221] + $total3[221] + $total4[221];
	$total9[311] = $total1[311] + $total2[311] + $total3[311] + $total4[311];
	$total9[312] = $total1[312] + $total2[312] + $total3[312] + $total4[312];
	$total9[112] = $total1[112] + $total2[112] + $total3[112] + $total4[112];
	$total9[122] = $total1[122] + $total2[122] + $total3[122] + $total4[122];
	$total9[132] = $total1[132] + $total2[132] + $total3[132] + $total4[132];
	$total9[212] = $total1[212] + $total2[212] + $total3[212] + $total4[212];
	$total9[222] = $total1[222] + $total2[222] + $total3[222] + $total4[222];
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>Total Biaya Kebun Tidak Langsung</td>';
	$tab .= '<td align=right>' . numberformat($total9[111] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[121] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[131] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[211] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[221] / 1000, 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[311], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[312], 2) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[112], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[122], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[132], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[212], 0) . '</td>';
	$tab .= '<td align=right>' . numberformat($total9[222], 0) . '</td>';
	$tab .= '</tr>';
}

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
	$nop_ = 'lbm_biayaoverhead_' . $unit . $periode;

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
			global $luas;
			global $wkiri;
			global $wlain;
			global $luasbudg;
			global $luasreal;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width / 2, $height, '24. BIAYA TIDAK LANGSUNG', NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);
			$this->Ln();
			$this->Ln();
			$height = 15;
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Luas:', 0, 0, 'R', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, numberformat($luasbudg, 2) . ' Ha', 0, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, numberformat($luasreal, 2) . ' Ha', 0, 0, 'C', 1);
			$this->Cell((($wlain * 7) / 100) * $width, $height, '', 0, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 7) / 100) * $width, $height, $_SESSION['lang']['biaya'] . ' (000)', 1, 0, 'C', 1);
			$this->Cell((($wlain * 5) / 100) * $width, $height, $_SESSION['lang']['biaya'] . '/Ha', 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, 'No.', RL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], RL, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['pencapaian'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 3) / 100) * $width, $height, $_SESSION['lang']['anggaran'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['realisasi'], 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['setahun'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
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

	if (!empty($dzArr)) {
		unset($total1);
		$no = 1;
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Biaya Karyawan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71101-Biaya Staf', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71101)) {
			foreach ($list71101 as $keg) {
				$total1 += 111;
				$total1 += 121;
				$total1 += 131;
				$total1 += 211;
				$total1 += 221;
				$total1 += 311;
				$total1 += 312;
				$total1 += 112;
				$total1 += 122;
				$total1 += 132;
				$total1 += 212;
				$total1 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71102-Biaya non staf', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71102)) {
			foreach ($list71102 as $keg) {
				$total1 += 111;
				$total1 += 121;
				$total1 += 131;
				$total1 += 211;
				$total1 += 221;
				$total1 += 311;
				$total1 += 312;
				$total1 += 112;
				$total1 += 122;
				$total1 += 132;
				$total1 += 212;
				$total1 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '7120 Operasional Karyawan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list7120)) {
			foreach ($list7120 as $keg) {
				$total1 += 111;
				$total1 += 121;
				$total1 += 131;
				$total1 += 211;
				$total1 += 221;
				$total1 += 311;
				$total1 += 312;
				$total1 += 112;
				$total1 += 122;
				$total1 += 132;
				$total1 += 212;
				$total1 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Total Biaya Karyawan', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[311], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[312], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[112], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[122], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[132], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[212], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total1[222], 0), 1, 0, 'R', 1);
		$pdf->Ln();
		unset($total2);
		$no = 1;
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Biaya Administrasi dan Umum', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71301 Operasional Mess dan Kantor', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71301)) {
			foreach ($list71301 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71302 Biaya Kantor Unit', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71302)) {
			foreach ($list71302 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71303 Biaya Kebersihan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71303)) {
			foreach ($list71303 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71304 Biaya Telekomunikasi', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71304)) {
			foreach ($list71304 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71305 Biaya Asuransi', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71305)) {
			foreach ($list71305 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71306 Pajak dan Retribusi', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71306)) {
			foreach ($list71306 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71307 Perijinan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71307)) {
			foreach ($list71307 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71308 Biaya Keamanan dan Sosial Lainnya', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71308)) {
			foreach ($list71308 as $keg) {
				$total2 += 111;
				$total2 += 121;
				$total2 += 131;
				$total2 += 211;
				$total2 += 221;
				$total2 += 311;
				$total2 += 312;
				$total2 += 112;
				$total2 += 122;
				$total2 += 132;
				$total2 += 212;
				$total2 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Total Biaya Administrasi dan Umum', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[311], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[312], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[112], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[122], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[132], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[212], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total2[222], 0), 1, 0, 'R', 1);
		$pdf->Ln();
		unset($total3);
		$no = 1;
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Biaya Pemeliharaan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71409 Pemeliharaan', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71409)) {
			foreach ($list71409 as $keg) {
				$total3 += 111;
				$total3 += 121;
				$total3 += 131;
				$total3 += 211;
				$total3 += 221;
				$total3 += 311;
				$total3 += 312;
				$total3 += 112;
				$total3 += 122;
				$total3 += 132;
				$total3 += 212;
				$total3 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Total Biaya Pemeliharaan', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[311], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[312], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[112], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[122], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[132], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[212], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total3[222], 0), 1, 0, 'R', 1);
		$pdf->Ln();
		unset($total4);
		$no = 1;
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Biaya Administrasi Lainnya', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71501 Pembelian Barang Inventaris (non Asset)', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71501)) {
			foreach ($list71501 as $keg) {
				$total4 += 111;
				$total4 += 121;
				$total4 += 131;
				$total4 += 211;
				$total4 += 221;
				$total4 += 311;
				$total4 += 312;
				$total4 += 112;
				$total4 += 122;
				$total4 += 132;
				$total4 += 212;
				$total4 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71502 Penyusutan Tanaman Menghasilkan/Amortisasi', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71502)) {
			foreach ($list71502 as $keg) {
				$total4 += 111;
				$total4 += 121;
				$total4 += 131;
				$total4 += 211;
				$total4 += 221;
				$total4 += 311;
				$total4 += 312;
				$total4 += 112;
				$total4 += 122;
				$total4 += 132;
				$total4 += 212;
				$total4 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71503 Amortisasi', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71503)) {
			foreach ($list71503 as $keg) {
				$total4 += 111;
				$total4 += 121;
				$total4 += 131;
				$total4 += 211;
				$total4 += 221;
				$total4 += 311;
				$total4 += 312;
				$total4 += 112;
				$total4 += 122;
				$total4 += 132;
				$total4 += 212;
				$total4 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, '71899 Biaya Bank Unit', 1, 0, 'L', 1);
		$pdf->Cell(((12 * $wlain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();

		if (!empty($list71899)) {
			foreach ($list71899 as $keg) {
				$total4 += 111;
				$total4 += 121;
				$total4 += 131;
				$total4 += 211;
				$total4 += 221;
				$total4 += 311;
				$total4 += 312;
				$total4 += 112;
				$total4 += 122;
				$total4 += 132;
				$total4 += 212;
				$total4 += 222;
				$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'R', 1);
				$pdf->Cell(($wkiri / 100) * $width, $height, $dzArr[$keg]['namaakun'], 1, 0, 'L', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][111] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][121] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][131] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][211] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][221] / 1000, 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][311], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][312], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][112], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][122], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][132], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][212], 0), 1, 0, 'R', 1);
				$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][222], 0), 1, 0, 'R', 1);
				$no += 1;
				$pdf->Ln();
			}
		}

		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Total Biaya Administrasi Lainnya', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[311], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[312], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[112], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[122], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[132], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[212], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total4[222], 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$total9[111] = $total1[111] + $total2[111] + $total3[111] + $total4[111];
		$total9[121] = $total1[121] + $total2[121] + $total3[121] + $total4[121];
		$total9[131] = $total1[131] + $total2[131] + $total3[131] + $total4[131];
		$total9[211] = $total1[211] + $total2[211] + $total3[211] + $total4[211];
		$total9[221] = $total1[221] + $total2[221] + $total3[221] + $total4[221];
		$total9[311] = $total1[311] + $total2[311] + $total3[311] + $total4[311];
		$total9[312] = $total1[312] + $total2[312] + $total3[312] + $total4[312];
		$total9[112] = $total1[112] + $total2[112] + $total3[112] + $total4[112];
		$total9[122] = $total1[122] + $total2[122] + $total3[122] + $total4[122];
		$total9[132] = $total1[132] + $total2[132] + $total3[132] + $total4[132];
		$total9[212] = $total1[212] + $total2[212] + $total3[212] + $total4[212];
		$total9[222] = $total1[222] + $total2[222] + $total3[222] + $total4[222];
		$pdf->Cell(((3 / 100) * $width) + (($wkiri / 100) * $width), $height, 'Total Biaya Kebun Tidak Langsung', 1, 0, 'L', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[111] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[121] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[131] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[211] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[221] / 1000, 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[311], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[312], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[112], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[122], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[132], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[212], 0), 1, 0, 'R', 1);
		$pdf->Cell(($wlain / 100) * $width, $height, numberformat($total9[222], 0), 1, 0, 'R', 1);
		$pdf->Ln();
	}
	else {
		echo 'Data Empty.';
		exit();
	}

	$pdf->Output();
	break;
}

?>
