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
	exit('Error:Field Required');
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
$kodekode = ' kodekegiatan in (\'621010201\', \'621010401\', \'621020101\', \'621020201\')';
$kodekode2 = ' kodekegiatan in (\'126060201\', \'126060401\', \'126070101\', \'126070201\')';
$kodekode3 = ' and kodekegiatan in (\'621010201\', \'621010401\', \'621020101\', \'621020201\', \'126060201\', \'126060401\', \'126070101\', \'126070201\')';
$kegiatan = 'SELECT kodekegiatan, namakegiatan,namakegiatan1 FROM ' . $dbname . '.setup_kegiatan WHERE ' . $kodekode . ' order by kodekegiatan';

#exit(mysql_error($conn));
($query = mysql_query($kegiatan)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][kode] = $res['kodekegiatan'];
	$listkegiatan[$res['kodekegiatan']] = $res['kodekegiatan'];

	if ($_SESSION['language'] == 'EN') {
		$kamuskegiatan[$res['kodekegiatan']] = $res['namakegiatan1'];
	}
	else {
		$kamuskegiatan[$res['kodekegiatan']] = $res['namakegiatan'];
	}
}

$kegiatan = 'SELECT kodekegiatan, namakegiatan,namakegiatan1 FROM ' . $dbname . '.setup_kegiatan WHERE ' . $kodekode2 . ' order by kodekegiatan';

#exit(mysql_error($conn));
($query = mysql_query($kegiatan)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][kode] = $res['kodekegiatan'];
	$listkegiatan2[$res['kodekegiatan']] = $res['kodekegiatan'];

	if ($_SESSION['language'] == 'EN') {
		$kamuskegiatan[$res['kodekegiatan']] = $res['namakegiatan1'];
	}
	else {
		$kamuskegiatan[$res['kodekegiatan']] = $res['namakegiatan'];
	}
}

$kegiatan = 'SELECT kodebarang, namabarang, satuan FROM ' . $dbname . '.log_5masterbarang WHERE kodebarang in (\'31200049\', \'31200040\', \'31200006\', \'31200035\')';

#exit(mysql_error($conn));
($query = mysql_query($kegiatan)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kamusbarang[$res['kodebarang']] = $res['namabarang'];
	$satuanbarang[$res['kodebarang']] = $res['satuan'];
}

$str = 'SELECT kodekegiatan, sum(kwantitasha) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitasha) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][aktbin] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitasha) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $unit . '%\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitasha) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $afdId . '%\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][aktsdb] = $res['aktual'];
}

$barangbarang = ' and kodebarang in (\'31200049\', \'31200040\', \'31200006\', \'31200035\')';
$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200049\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][glybin] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200049\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200049\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][glysdb] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200040\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200040\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][flobin] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200040\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][flosdb] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200006\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200006\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][parbin] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200006\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200006\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][parsdb] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200035\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE tanggal like \'' . $periode . '%\' and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200035\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][metbin] = $res['aktual'];
}

$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $unit . '%\' and kodebarang = \'31200035\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';

if ($afdId != '') {
	$str = 'SELECT kodekegiatan, sum(kwantitas) as aktual FROM ' . $dbname . '.kebun_pakai_material_vw ' . "\r\n" . '    WHERE (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) and kodeorg like \'' . $afdId . '%\' and kodebarang = \'31200035\' ' . $kodekode3 . "\r\n" . '    GROUP BY kodekegiatan';
}

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['kodekegiatan']][metsdb] = $res['aktual'];
}

if (!empty($listkegiatan)) {
	foreach ($listkegiatan as $keg) {
		@$dzArr[$keg][efiglybin] = $dzArr[$keg][glybin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiglysdb] = $dzArr[$keg][glysdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efiflobin] = $dzArr[$keg][flobin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiflosdb] = $dzArr[$keg][flosdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efiparbin] = $dzArr[$keg][parbin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiparsdb] = $dzArr[$keg][parsdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efimetbin] = $dzArr[$keg][metbin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efimetsdb] = $dzArr[$keg][metsdb] / $dzArr[$keg][aktsdb];
	}
}

if (!empty($listkegiatan2)) {
	foreach ($listkegiatan2 as $keg) {
		@$dzArr[$keg][efiglybin] = $dzArr[$keg][glybin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiglysdb] = $dzArr[$keg][glysdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efiflobin] = $dzArr[$keg][flobin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiflosdb] = $dzArr[$keg][flosdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efiparbin] = $dzArr[$keg][parbin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efiparsdb] = $dzArr[$keg][parsdb] / $dzArr[$keg][aktsdb];
		@$dzArr[$keg][efimetbin] = $dzArr[$keg][metbin] / $dzArr[$keg][aktbin];
		@$dzArr[$keg][efimetsdb] = $dzArr[$keg][metsdb] / $dzArr[$keg][aktsdb];
	}
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=11 align=left><font size=3>12. ' . $judul . '</font></td>' . "\r\n" . '        <td colspan=8 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=19 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=19 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')</td></tr>';
	}

	$tab .= '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= '12. ' . $judul;
}

if ($proses != 'excel') {
	$tab .= '<br>12. 1. EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['TM'] . ' (HERBISIDA)';
}
else {
	$tab .= '<br><table border=0>' . "\r\n" . '         <tr><td colspan=11 align=left><font size=3>12. 1. EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['TM'] . ' (HERBISIDA)</font></td></tr> ' . "\r\n" . '    </table>';
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['pekerjaan'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['aktual'] . ' (Ha)</td>' . "\r\n" . '    <td align=center colspan=8 ' . $bg . '>' . $_SESSION['lang']['penggunaan'] . ' Herbisida</td>' . "\r\n" . '    <td align=center colspan=8 ' . $bg . '>' . $_SESSION['lang']['efisiensi'] . ' Herbisida</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200049] . ' (' . $satuanbarang[31200049] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200040] . ' (' . $satuanbarang[31200040] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200006] . ' (' . $satuanbarang[31200006] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200035] . ' (' . $satuanbarang[31200035] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200049] . ' (' . $satuanbarang[31200049] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200040] . ' (' . $satuanbarang[31200040] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200006] . ' (' . $satuanbarang[31200006] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200035] . ' (' . $satuanbarang[31200035] . '/Ha)</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=19>Data Empty.</td></tr>';
}
else if (!empty($listkegiatan)) {
	foreach ($listkegiatan as $keg) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $kamuskegiatan[$keg] . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][aktbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][aktsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][glybin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][glysdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][flobin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][flosdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][parbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][parsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][metbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][metsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiglybin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiglysdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiflobin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiflosdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiparbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiparsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efimetbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efimetsdb], 2) . '</td>';
		$tab .= '</tr>';
	}
}

$tab .= '</tbody></table>';

if ($proses != 'excel') {
	$tab .= '<br>12. 2. EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['tbm'] . ' (HERBISIDA)';
}
else {
	$tab .= '<br><table border=0>' . "\r\n" . '         <tr><td colspan=11 align=left><font size=3>12. 2. EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['tbm'] . ' (HERBISIDA)</font></td></tr> ' . "\r\n" . '    </table>';
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['pekerjaan'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['aktual'] . ' (Ha)</td>' . "\r\n" . '    <td align=center colspan=8 ' . $bg . '>' . $_SESSION['lang']['penggunaan'] . ' Herbisida</td>' . "\r\n" . '    <td align=center colspan=8 ' . $bg . '>' . $_SESSION['lang']['efisiensi'] . ' Herbisida</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200049] . ' (' . $satuanbarang[31200049] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200040] . ' (' . $satuanbarang[31200040] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200006] . ' (' . $satuanbarang[31200006] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200035] . ' (' . $satuanbarang[31200035] . ')</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200049] . ' (' . $satuanbarang[31200049] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200040] . ' (' . $satuanbarang[31200040] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200006] . ' (' . $satuanbarang[31200006] . '/Ha)</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $kamusbarang[31200035] . ' (' . $satuanbarang[31200035] . '/Ha)</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=19>Data Empty.</td></tr>';
}
else if (!empty($listkegiatan2)) {
	foreach ($listkegiatan2 as $keg) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $kamuskegiatan[$keg] . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][aktbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][aktsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][glybin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][glysdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][flobin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][flosdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][parbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][parsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][metbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][metsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiglybin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiglysdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiflobin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiflosdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiparbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efiparsdb], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efimetbin], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg][efimetsdb], 2) . '</td>';
		$tab .= '</tr>';
	}
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
	$nop_ = 'lbm_pemeliharan_efisiensi_' . $unit . $periode;

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
			global $wlain2;
			global $afdId;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width / 2, $height, '12. EFISIENSI  ' . $_SESSION['lang']['pemeltanaman'], NULL, 0, 'L', 1);
			$this->Cell($width / 2, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);

			if ($afdId != '') {
				$this->Ln();
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')', NULL, 0, 'L', 1);
			}

			$this->Ln();
			$this->Ln();
			$this->SetFillColor(255, 255, 255);
			$height = 15;
			$this->SetFont('Arial', 'B', 10);
			$this->Cell($width, $height, '12.1 EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['TM'] . ' (HERBISIDA)', 0, 0, 'L', 1);
			$this->Ln();
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 8) / 100) * $width, $height, $_SESSION['lang']['penggunaan'] . ' Herbisida', TRL, 0, 'C', 1);
			$this->Cell((($wlain2 * 8) / 100) * $width, $height, $_SESSION['lang']['efisiensi'] . ' Herbisida', TRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', RL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Glyphosate', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Flouroksipi', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Paraquat', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Metsulfuron', 1, 0, 'C', 1);
			$this->Cell((($wlain2 * 2) / 100) * $width, $height, 'Glyphosate', 1, 0, 'C', 1);
			$this->Cell((($wlain2 * 2) / 100) * $width, $height, 'Flouroksipi', 1, 0, 'C', 1);
			$this->Cell((($wlain2 * 2) / 100) * $width, $height, 'Paraquat', 1, 0, 'C', 1);
			$this->Cell((($wlain2 * 2) / 100) * $width, $height, 'Metsulfuron', 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
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
	$wkiri = 14;
	$wlain = 5;
	$wlain2 = 4.5;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 6);

	if (!empty($listkegiatan)) {
		foreach ($listkegiatan as $keg) {
			$pdf->Cell(($wkiri / 100) * $width, $height, $kamuskegiatan[$keg], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][aktbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][aktsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][glybin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][glysdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][flobin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][flosdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][parbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][parsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][metsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][metsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiglybin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiglysdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiflobin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiflosdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiparbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiparsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efimetbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efimetsdb], 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$pdf->Ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell($width, $height, '12.1 EFISIENSI ' . $_SESSION['lang']['pemeltanaman'] . ' ' . $_SESSION['lang']['tbm'] . ' (HERBISIDA)', 0, 0, 'L', 1);
	$pdf->Ln();
	$pdf->SetFillColor(220, 220, 220);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', TRL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', TRL, 0, 'C', 1);
	$pdf->Cell((($wlain * 8) / 100) * $width, $height, $_SESSION['lang']['penggunaan'] . ' Herbisida', TRL, 0, 'C', 1);
	$pdf->Cell((($wlain2 * 8) / 100) * $width, $height, $_SESSION['lang']['efisiensi'] . ' Herbisida', TRL, 0, 'C', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], RL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], RL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, 'sd BI', RL, 0, 'C', 1);
	$pdf->Cell((($wlain * 2) / 100) * $width, $height, 'Glyphosate', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 2) / 100) * $width, $height, 'Flouroksipi', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 2) / 100) * $width, $height, 'Paraquat', 1, 0, 'C', 1);
	$pdf->Cell((($wlain * 2) / 100) * $width, $height, 'Metsulfuron', 1, 0, 'C', 1);
	$pdf->Cell((($wlain2 * 2) / 100) * $width, $height, 'Glyphosate', 1, 0, 'C', 1);
	$pdf->Cell((($wlain2 * 2) / 100) * $width, $height, 'Flouroksipi', 1, 0, 'C', 1);
	$pdf->Cell((($wlain2 * 2) / 100) * $width, $height, 'Paraquat', 1, 0, 'C', 1);
	$pdf->Cell((($wlain2 * 2) / 100) * $width, $height, 'Metsulfuron', 1, 0, 'C', 1);
	$pdf->Ln();
	$pdf->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
	$pdf->Cell(($wlain2 / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
	$pdf->Ln();
	$pdf->SetFont('Arial', '', 6);
	$pdf->SetFillColor(255, 255, 255);

	if (!empty($listkegiatan2)) {
		foreach ($listkegiatan2 as $keg) {
			$pdf->Cell(($wkiri / 100) * $width, $height, $kamuskegiatan[$keg], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][aktbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][aktsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][glybin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][glysdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][flobin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][flosdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][parbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][parsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][metsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg][metsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiglybin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiglysdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiflobin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiflosdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiparbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efiparsdb], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efimetbin], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain2 / 100) * $width, $height, numberformat($dzArr[$keg][efimetsdb], 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$pdf->Output();
	break;
}

?>
