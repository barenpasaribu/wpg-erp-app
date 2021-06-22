<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$sKlmpk = 'select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$thn = explode('-', $periode);
$sthnTnm = 'select distinct tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '          where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\'  order by tahuntanam asc';

if ($afdId != '') {
	$sthnTnm = 'select distinct tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '             where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)=\'' . $periode . '\'  order by tahuntanam asc';
}

#exit(mysql_error());
($qthnTnm = mysql_query($sthnTnm)) || true;

while ($rthnTnm = mysql_fetch_assoc($qthnTnm)) {
	if ($rthnTnm['tahuntanam'] != '') {
		$dtThnTnm[] = $rthnTnm['tahuntanam'];
	}
}

$sJmlHk = 'select distinct count(karyawanid) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\' group by tahuntanam,karyawanid';

if ($afdId != '') {
	$sJmlHk = 'select distinct count(karyawanid) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '             where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)=\'' . $periode . '\' group by tahuntanam,karyawanid';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHk += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct count(karyawanid) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by tahuntanam,karyawanid';

if ($afdId != '') {
	$sJmlHk = 'select distinct count(karyawanid) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by tahuntanam,karyawanid';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHkSbi += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct count(hasilkerjakg) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\' and hasilkerjakg>norma group by tahuntanam,hasilkerjakg';

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHkSiapBrg += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct count(hasilkerjakg) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . "\r\n" . '         and hasilkerjakg>norma group by tahuntanam,hasilkerjakg';

if ($afdId != '') {
	$sJmlHk = 'select distinct count(hasilkerjakg) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . "\r\n" . '         and hasilkerjakg>norma group by tahuntanam,hasilkerjakg';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHkSiapBrgSbi += $rJmlHk['tahuntanam'];
}

foreach ($dtThnTnm as $lsThnTnm) {
	@$persenBi[$lsThnTnm] = ($dtJmlHkSiapBrg[$lsThnTnm] / $dtJmlHk[$lsThnTnm]) * 100;
	@$persensBi[$lsThnTnm] = ($dtJmlHkSiapBrgSbi[$lsThnTnm] / $dtJmlHkSbi[$lsThnTnm]) * 100;
}

$sJmlHk = 'select distinct sum(norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\'  group by tahuntanam,norma';

if ($afdId != '') {
	$sJmlHk = 'select distinct sum(norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)=\'' . $periode . '\'  group by tahuntanam,norma';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHkNorma += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct sum(norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . "\r\n" . '         group by tahuntanam,norma';

if ($afdId != '') {
	$sJmlHk = 'select distinct sum(norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . "\r\n" . '         group by tahuntanam,norma';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlHkNormaSbi += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct sum(hasilkerjakg-norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' ' . "\r\n" . '         group by tahuntanam,norma';

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlLbhBrgSbi += $rJmlHk['tahuntanam'];
}

$sJmlHk = 'select distinct sum(hasilkerjakg-norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,4)=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\' ' . "\r\n" . '         group by tahuntanam,norma';

if ($afdId != '') {
	$sJmlHk = 'select distinct sum(hasilkerjakg-norma) as totJmlhHk,tahuntanam from ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '         where substr(kodeorg,1,6)=\'' . $afdId . '\' and substr(tanggal,1,7)=\'' . $periode . '\' ' . "\r\n" . '         group by tahuntanam,norma';
}

#exit(mysql_error());
($qJmlHk = mysql_query($sJmlHk)) || true;

while ($rJmlHk = mysql_fetch_assoc($qJmlHk)) {
	$dtJmlLbhBrg += $rJmlHk['tahuntanam'];
}

foreach ($dtThnTnm as $lsThnTnm) {
	@$totBi[$lsThnTnm] = $dtJmlHkNorma[$lsThnTnm] + $dtJmlLbhBrg[$lsThnTnm];
	@$totSbi[$lsThnTnm] = $dtJmlHkNormaSbi[$lsThnTnm] + $dtJmlLbhBrgSbi[$lsThnTnm];
}

$sJjg = 'select distinct sum(jjg) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.kodeorg=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\' group by blok';

#exit(mysql_error());
($qJjg = mysql_query($sJjg)) || true;

while ($rJgg = mysql_fetch_assoc($qJjg)) {
	$jumJjg += $rJgg['tahuntanam'];
}

$sJjg = 'select distinct sum(jjg) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.kodeorg=\'' . $kdUnit . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by blok';

if ($afdId != '') {
	$sJjg = 'select distinct sum(jjg) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.blok like \'' . $afdId . '%\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by blok';
}

#exit(mysql_error());
($qJjg = mysql_query($sJjg)) || true;

while ($rJgg = mysql_fetch_assoc($qJjg)) {
	$jumJjgSbi += $rJgg['tahuntanam'];
}

$sJjg = 'select distinct sum(kgwb) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.kodeorg=\'' . $kdUnit . '\' and substr(tanggal,1,7)=\'' . $periode . '\' group by blok';

#exit(mysql_error());
($qJjg = mysql_query($sJjg)) || true;

while ($rJgg = mysql_fetch_assoc($qJjg)) {
	$jumKgBi += $rJgg['tahuntanam'];
}

$sJjg = 'select distinct sum(kgwb) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.kodeorg=\'' . $kdUnit . '\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by blok';

if ($afdId != '') {
	$sJjg = 'select distinct sum(kgwb) jmlhJjg,b.tahuntanam from ' . $dbname . '.kebun_spb_vw a left join ' . $dbname . '.setup_blok b ' . "\r\n" . '       on a.blok=b.kodeorg where a.blok like \'' . $afdId . '%\' and substr(tanggal,1,7)  between \'' . $thn[0] . '-01\' and \'' . $periode . '\' group by blok';
}

#exit(mysql_error());
($qJjg = mysql_query($sJjg)) || true;

while ($rJgg = mysql_fetch_assoc($qJjg)) {
	$jumKgSbi += $rJgg['tahuntanam'];
}

if ($thn[1] < 7) {
	$sAvgBjr = 'select distinct avg(bjr) as rtBjr,b.tahuntanam from ' . $dbname . '.kebun_rencanapanen_vw ' . "\r\n" . '              a left join ' . $dbname . '.setup_blok b on a.kodeblok=b.kodeorg where bulan<7 and tahun=\'' . $thn[0] . '\' group by kodeblok';
}
else {
	$sAvgBjr = 'select distinct avg(bjr) as rtBjr,b.tahuntanam from ' . $dbname . '.kebun_rencanapanen_vw a left join ' . $dbname . '.setup_blok b on a.kodeblok=b.kodeorg' . "\r\n" . '              where bulan>6 and tahun=\'' . $thn[0] . '\' and tahun=\'' . $thn[0] . '\' group by kodeblok';
}

#exit(mysql_error());
($qAvgBjr = mysql_query($sAvgBjr)) || true;

while ($rAvgBjr = mysql_fetch_assoc($qAvgBjr)) {
	$dtAvgBjr += $rAvgBjr['tahuntanam'];
}

$jmlHari = cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$varCek = count($dtThnTnm);

if ($varCek < 1) {
	exit('Error:Data Kosong');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=8 align=left><b>06. OUTPUT ' . strtoupper($_SESSION['lang']['panen']) . '</b></td><td colspan=8 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=8 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=8 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
$tab .= '<tr><td rowspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['tahuntanam'] . '</td>';
$tab .= '<td colspan=6 ' . $bgcoloraja . '>' . $_SESSION['lang']['jumlahhk'] . '</td>';
$tab .= '<td colspan=6 ' . $bgcoloraja . '>' . $_SESSION['lang']['jumlah'] . ' (KG)</td>';
$tab .= '<td colspan=2 rowspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['janjang'] . ' ' . $_SESSION['lang']['kirim'] . '</td>';
$tab .= '<td colspan=2 rowspan=2 ' . $bgcoloraja . '>Total  ' . $_SESSION['lang']['produksi'] . ' (KG)</td>';
$tab .= '<td colspan=2 rowspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['tbs'] . ' ' . $_SESSION['lang']['kirim'] . ' (KG)</td>';
$tab .= '<td colspan=2 rowspan=2 ' . $bgcoloraja . '>Var. Prod.</td>';
$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $_SESSION['lang']['bjr'] . '</td>';
$tab .= '<td colspan=6 ' . $bgcoloraja . '>OUTPUT  ( KG / HK )</td>';
$tab .= '</tr>';
$tab .= '<tr><td colspan=2 ' . $bgcoloraja . '>Yang Bekerja</td><td colspan=4 ' . $bgcoloraja . '>HK Siap Borong</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>Target Basis</td><td colspan=2 ' . $bgcoloraja . '>Lebih Borong</td><td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>Actual</td><td rowspan=2 ' . $bgcoloraja . '>Census SM-I/II</td>';
$tab .= '<td colspan=2 ' . $bgcoloraja . '>Target Basis</td><td colspan=2 ' . $bgcoloraja . '>Lebih Borong</td><td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['realisasi'] . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>%</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>%</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td>' . $_SESSION['lang']['bi'] . '</td><td>' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td></tr>';
$tab .= '</thead><tbody>';

foreach ($dtThnTnm as $lstThnTnm) {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $lstThnTnm . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHk[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHkSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHkSiapBrg[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($persenBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHkSiapBrgSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($persensBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHkNorma[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlHkNormaSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlLbhBrg[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJmlLbhBrgSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jumJjg[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jumJjgSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($totSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jumKgBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jumKgSbi[$lstThnTnm], 2) . '</td>';
	$varTbs[$lstThnTnm] = $totBi[$lstThnTnm] - $jumKgBi[$lstThnTnm];
	$varTbsSbi[$lstThnTnm] = $totSbi[$lstThnTnm] - $jumKgSbi[$lstThnTnm];
	$tab .= '<td align=right>' . number_format($varTbs[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($varTbsSbi[$lstThnTnm], 2) . '</td>';
	@$bjrBi[$lstThnTnm] = $jumKgBi[$lstThnTnm] / $jumJjg[$lstThnTnm];
	@$bjrSbi[$lstThnTnm] = $jumKgSbi[$lstThnTnm] / $jumJjgSbi[$lstThnTnm];
	$tab .= '<td align=right>' . number_format($bjrBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($bjrSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtAvgBjr[$lstThnTnm], 2) . '</td>';
	@$outPtTrg[$lstThnTnm] = $dtJmlHkNorma[$lstThnTnm] / $dtJmlHk[$lstThnTnm];
	@$outPtTrgSbi[$lstThnTnm] = $dtJmlHkNormaSbi[$lstThnTnm] / $dtJmlHkSbi[$lstThnTnm];
	$tab .= '<td align=right>' . number_format($outPtTrg[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($outPtTrgSbi[$lstThnTnm], 2) . '</td>';
	@$outPtLbhBrg[$lstThnTnm] = $dtJmlLbhBrg[$lstThnTnm] / $dtJmlHkSiapBrg[$lstThnTnm];
	@$outPtLbhBrgSbi[$lstThnTnm] = $dtJmlLbhBrgSbi[$lstThnTnm] / $dtJmlHkSiapBrgSbi[$lstThnTnm];
	$tab .= '<td align=right>' . number_format($outPtLbhBrg[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($outPtLbhBrgSbi[$lstThnTnm], 2) . '</td>';
	$dtTotRowBi[$lstThnTnm] = $outPtTrg[$lstThnTnm] + $outPtLbhBrg[$lstThnTnm];
	$dtTotRowSbi[$lstThnTnm] = $outPtTrgSbi[$lstThnTnm] + $outPtLbhBrgSbi[$lstThnTnm];
	$tab .= '<td align=right>' . number_format($dtTotRowBi[$lstThnTnm], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtTotRowSbi[$lstThnTnm], 2) . '</td>';
	$tab .= '</tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'potong_buah' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('06. OUTPUT ' . $_SESSION['lang']['panen']), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');

			if ($afdId != '') {
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId], 0, 1, 'L');
			}

			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 5);
			$this->Cell(25, $height, $_SESSION['lang']['tahun'], TLR, 0, 'C', 1);
			$this->Cell(120, $height, $_SESSION['lang']['jhk'], TBLR, 0, 'C', 1);
			$this->Cell(180, $height, $_SESSION['lang']['jumlah'] . '  (KG)', TBLR, 0, 'C', 1);
			$this->Cell(50, $height, $_SESSION['lang']['janjang'] . ' ' . $_SESSION['lang']['kirim'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Total  ' . $_SESSION['lang']['produksi'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['tbs'] . ' ' . $_SESSION['lang']['kirim'], TLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Var. Prod.', TBLR, 0, 'C', 1);
			$this->Cell(70, $height, $_SESSION['lang']['bjr'], TBLR, 0, 'C', 1);
			$this->Cell(180, $height, 'OUTPUT  ( KG / HK )', TBLR, 1, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['tanam'], LR, 0, 'C', 1);
			$this->Cell(40, $height, 'Yang Bekerja', TBLR, 0, 'C', 1);
			$this->Cell(80, $height, 'HK Siap Borong', TBLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Target Basis', TBLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Lebih Borong', TBLR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['realisasi'], TBLR, 0, 'C', 1);
			$this->Cell(50, $height, '', BLR, 0, 'C', 1);
			$this->Cell(60, $height, '( Kg )', BLR, 0, 'C', 1);
			$this->Cell(60, $height, '( Kg )', BLR, 0, 'C', 1);
			$this->Cell(60, $height, 'KBN-PKS(%)', BLR, 0, 'C', 1);
			$this->Cell(40, $height, 'Actual', TBLR, 0, 'C', 1);
			$this->Cell(30, $height, 'Census', TLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Target Basis', TBLR, 0, 'C', 1);
			$this->Cell(60, $height, 'Lebih Borong', TBLR, 0, 'C', 1);
			$this->Cell(60, $height, $_SESSION['lang']['realisasi'], TBLR, 1, 'C', 1);
			$this->SetFont('Arial', '', 4);
			$this->Cell(25, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, '%', TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, '%', TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(25, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, 'SM-I/II', BLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['bi'], TBLR, 0, 'C', 1);
			$this->Cell(30, $height, $_SESSION['lang']['sbi'], TBLR, 1, 'C', 1);
			$this->SetFont('Arial', 'B', 8);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 4);

	foreach ($dtThnTnm as $lstThnTnm) {
		$pdf->Cell(25, $height, $lstThnTnm, TBLR, 0, 'C', 1);
		$pdf->Cell(20, $height, number_format($dtJmlHk[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtJmlHkSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtJmlHkSiapBrg[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($persenBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($dtJmlHkSiapBrgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($persensBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtJmlHkNorma[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtJmlHkNormaSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtJmlLbhBrg[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtJmlLbhBrgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($totBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($totSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(25, $height, number_format($jumJjg[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(25, $height, number_format($jumJjgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($totBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($totSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($jumKgBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($jumKgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($varTbs[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($varTbsSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($bjrBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(20, $height, number_format($bjrSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtAvgBjr[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($outPtTrg[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($outPtTrgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($outPtLbhBrg[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($outPtLbhBrgSbi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtTotRowBi[$lstThnTnm], 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($dtTotRowSbi[$lstThnTnm], 2), TBLR, 1, 'R', 1);
	}

	$pdf->Output();
	break;
}

?>
