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
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode != '') {
	$where = ' substr(a.tanggal,1,7)=\'' . $periode . '\'';
	$whereb = ' substr(b.tanggal,1,7)=\'' . $periode . '\'';
}
else {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$sTotalLuas = 'select distinct luasareaproduktif,substr(kodeorg,1,6) as afdeling from ' . $dbname . '.setup_blok where kodeorg like \'' . $kdUnit . '%\' order by kodeorg asc';
$addTmb = ' lokasitugas=\'' . $kdUnit . '\'';

if ($afdId != '') {
	$addTmb = ' subbagian=\'' . $afdId . '\'';
	$sTotalLuas = 'select distinct luasareaproduktif,substr(kodeorg,1,6) as afdeling from ' . $dbname . '.setup_blok where kodeorg like \'' . $afdId . '%\' order by kodeorg asc';
}

exit(myql_error());
($qTotalLuas = mysql_query($sTotalLuas)) || true;

while ($rTotalLuas = mysql_fetch_assoc($qTotalLuas)) {
	$grandTotal += $rTotalLuas['luasareaproduktif'];
	$lsAfdeling += $rTotalLuas['afdeling'];
}

$sAfdeling = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\'';

if ($afdId != '') {
	$sAfdeling = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $afdId . '\'';
}

#exit(mysql_error());
($qAfdeling = mysql_query($sAfdeling)) || true;

while ($rAfdeling = mysql_fetch_assoc($qAfdeling)) {
	$dataAfd[] = $rAfdeling['kodeorganisasi'];
}

$sDataKary = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . "\r\n" . '            and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

#exit(mysql_error($conn));
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotal += $kdUnit;
	$TotalKary[$kdUnit] += $rDataKary['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error($conn));
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBag += $rDataKary2['subbagian'];
		$TotalKarBag[$rDataKary2['subbagian']] += $rDataKary2['jeniskelamin'];
	}
}

$sDataKary25 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . "\r\n" . '              ' . $addTmb . ' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')   group by jeniskelamin';

#exit(mysql_error($conn));
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKary += $kdUnit;
	$TotalKarKnbes[$kdUnit] += $rData['jeniskelamin'];
}

$sTotStaff = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . "\r\n" . '                    and alokasi=1  and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error($conn));
($qTotStaff = mysql_query($sTotStaff)) || true;

while ($rTotStaff = mysql_fetch_assoc($qTotStaff)) {
	$grTotStaff += $kdUnit;
	$totStaff[$kdUnit] += $rTotStaff['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' ' . "\r\n" . '                     and subbagian=\'' . $kdAfd . '\' and alokasi=1 and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error($conn));
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grTotStaff += $rKaryStaff['subbagian'];
		$TotStaff[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  and alokasi=1  group by jeniskelamin';

#exit(mysql_error($conn));
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grTotStaff2 += $kdUnit;
	$TotKarKnbesStaff[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(45,88) ' . "\r\n" . '                    and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowPanKht += $kdUnit;
	$totRowPanKht[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totalPekerjaLangsung += $rTotKhtL['total'];
	$totalPekerjaLangsungJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(45,88) and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowPanKht2 += $rKaryStaff['subbagian'];
		$totRowPanKht2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsungJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsung += $rKaryStaff['subbagian'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' ' . "\r\n" . '                      and  (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  and ' . "\r\n" . '                      kodejabatan in(45,88) and tipekaryawan in (2,3) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowPanKht3 += $kdUnit;
	$totRowPanKht3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKerjaLangKanBes += $rDataStaff['total'];
	$totKerjaLangKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$cols2 = $cols + 6;
$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                   kodejabatan in(45,88) and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  ' . "\r\n" . '                   group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowPanKhl += $kdUnit;
	$totRowPanKhl[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totalPekerjaLangsung += $rTotKhtL['total'];
	$totalPekerjaLangsungJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(45,88) and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and  ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowPanKhl2 += $rKaryStaff['subbagian'];
		$totRowPanKhl2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsungJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsung += $rKaryStaff['subbagian'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                      and kodejabatan in(45,88) and tipekaryawan in (4) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowPanKhl3 += $kdUnit;
	$totRowPanKhl3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKerjaLangKanBes += $rDataStaff['total'];
	$totKerjaLangKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(60,168) ' . "\r\n" . '                    and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                    (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowPerKht += $kdUnit;
	$totRowPerKht[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totalPekerjaLangsung += $rTotKhtL['total'];
	$totalPekerjaLangsungJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryPerKht = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                      and kodejabatan in(60,168) and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryPerKht = mysql_query($sKaryPerKht)) || true;

	while ($rKaryPerKht = mysql_fetch_assoc($qKaryPerKht)) {
		$grRowPerKht2 += $rKaryPerKht['subbagian'];
		$totRowPerKht2[$rKaryPerKht['subbagian']] += $rKaryPerKht['jeniskelamin'];
		$totalAfdKerjaLangsungJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsung += $rKaryStaff['subbagian'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  ' . "\r\n" . '                      and kodejabatan in(60,168) and tipekaryawan in (2,3) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowPerKht3 += $kdUnit;
	$totRowPerKht3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKerjaLangKanBes += $rDataStaff['total'];
	$totKerjaLangKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(60,168) ' . "\r\n" . '                   and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowPerKhl += $kdUnit;
	$totRowPerKhl[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totalPekerjaLangsung += $rTotKhtL['total'];
	$totalPekerjaLangsungJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(60,168) and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowPerKhl2 += $rKaryStaff['subbagian'];
		$totRowPerKhl2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsungJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totalAfdKerjaLangsung += $rKaryStaff['subbagian'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  and ' . "\r\n" . '                      kodejabatan in(60,168) and tipekaryawan in (4) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowPerKhl3 += $kdUnit;
	$totRowPerKhl3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKerjaLangKanBes += $rDataStaff['total'];
	$totKerjaLangKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$karyBulanan = array();
$karyKht = array();
$karyKhl = array();
$karyBulanan2 = array();
$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(37,38,48,53,55,132,136,138) ' . "\r\n" . '                   and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowManBul += $kdUnit;
	$totRowManBul[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(37,38,48,53,55,132,136,138) and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowManBul2 += $rKaryStaff['subbagian'];
		$totRowManBul2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                     subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                    (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') and kodejabatan in(37,38,48,53,55,132,136,138) ' . "\r\n" . '                    and tipekaryawan in (1) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowManBul3 += $kdUnit;
	$totRowManBul3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(37,38,48,53,55,132,136,138) ' . "\r\n" . '                   and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowManKht += $kdUnit;
	$totRowManKht[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(37,38,48,53,55,132,136,138) and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowManKht2 += $rKaryStaff['subbagian'];
		$totRowManKht2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')' . "\r\n" . '                      and kodejabatan in(37,38,48,53,55,132,136,138) and tipekaryawan in (2,3) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowManKht3 += $kdUnit;
	$totRowManKht3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(24,35,52,54,118,119,120,121,122,123) ' . "\r\n" . '                   and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowKranBul += $kdUnit;
	$totRowKranBul[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(24,35,52,54,118,119,120,121,122,123) and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowKranBul2 += $rKaryStaff['subbagian'];
		$totRowKranBul2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')' . "\r\n" . '                      and kodejabatan in(24,35,52,54,118,119,120,121,122,123) and tipekaryawan in (1) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowKranBul3 += $kdUnit;
	$totRowKranBul3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(24,35,52,54,118,119,120,121,122,123) ' . "\r\n" . '                   and tipekaryawan in (2,3) and  substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowKraniKht += $kdUnit;
	$totRowKraniKht[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(24,35,52,54,118,119,120,121,122,123) and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowKraniKht2 += $rKaryStaff['subbagian'];
		$totRowKraniKht2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')' . "\r\n" . '                      and kodejabatan in(24,35,52,54,118,119,120,121,122,123) and tipekaryawan in (2,3) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowKraniKht3 += $kdUnit;
	$totRowKraniKht3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan in(37,38,48,53,55,132,136,138) ' . "\r\n" . '                   and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowKraniKhl += $kdUnit;
	$totRowKraniKhl[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan in(37,38,48,53,55,132,136,138) and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowKraniKhl2 += $rKaryStaff['subbagian'];
		$totRowKraniKhl2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where lokasitugas=\'' . $kdUnit . '\' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') and ' . "\r\n" . '                      kodejabatan in(37,38,48,53,55,132,136,138) and tipekaryawan in (4) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowKraniKhl3 += $kdUnit;
	$totRowKraniKhl3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan NOT in (24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88) ' . "\r\n" . '                   and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowLainBul += $kdUnit;
	$totRowLainBul[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88) and tipekaryawan in (1) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowLainBul2 += $rKaryStaff['subbagian'];
		$totRowLainBul2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') and ' . "\r\n" . '                      kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88) and tipekaryawan in (1) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowLainBul3 += $kdUnit;
	$totRowLainBul3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88) ' . "\r\n" . '                   and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                   (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowLainKht += $kdUnit;
	$totRowLainKht[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88)  and tipekaryawan in (2,3) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                     (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowLainKht2 += $rKaryStaff['subbagian'];
		$totRowLainKht2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') and ' . "\r\n" . '                      kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88)  and tipekaryawan in (2,3)  group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowLainKht3 += $kdUnit;
	$totRowLainKht3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$sTotKhtL = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88) ' . "\r\n" . '                    and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')  group by jeniskelamin';

exit(myql_error());
($qTotKhtL = mysql_query($sTotKhtL)) || true;

while ($rTotKhtL = mysql_fetch_assoc($qTotKhtL)) {
	$grRowLainKhl += $kdUnit;
	$totRowLainKhl[$kdUnit] += $rTotKhtL['jeniskelamin'];
	$totTdkLang += $rTotKhtL['total'];
	$totTdkLangJen[$kdUnit] += $rTotKhtL['jeniskelamin'];
}

foreach ($dataAfd as $kdAfd) {
	$sKaryStaff = 'select distinct count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '                     and kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88)  and tipekaryawan in (4) and substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                    (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryStaff = mysql_query($sKaryStaff)) || true;

	while ($rKaryStaff = mysql_fetch_assoc($qKaryStaff)) {
		$grRowLainKhl2 += $rKaryStaff['subbagian'];
		$totRowLainKhl2[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
		$totAfdTdkLang += $rKaryStaff['subbagian'];
		$totAfdTdkLangJen[$rKaryStaff['subbagian']] += $rKaryStaff['jeniskelamin'];
	}
}

$sKaryStaff2 = 'select distinct count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where ' . $addTmb . ' and ' . "\r\n" . '                      subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and  substr(tanggalmasuk,1,7)<=\'' . $periode . '\' and ' . "\r\n" . '                      (substr(tanggalkeluar,1,7)>\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\') and ' . "\r\n" . '                      kodejabatan NOT in(24,35,37,38,48,52,53,54,55,118,119,120,121,122,123,132,136,138,60,168,45,88)  and tipekaryawan in (4) group by jeniskelamin';

#exit(mysql_error());
($qKaryStaff2 = mysql_query($sKaryStaff2)) || true;

while ($rDataStaff = mysql_fetch_assoc($qKaryStaff2)) {
	$grRowLainKhl3 += $kdUnit;
	$totRowLainKhl3[$kdUnit] += $rDataStaff['jeniskelamin'];
	$totKanBes += $rDataStaff['total'];
	$totKanBesJen[$kdUnit] += $rDataStaff['jeniskelamin'];
}

$TotalKary[$kdUnit][L] = $totalPekerjaLangsungJen[$kdUnit][L] + $totStaff[$kdUnit][L] + $totTdkLangJen[$kdUnit][L];
$TotalKary[$kdUnit][P] = $totalPekerjaLangsungJen[$kdUnit][P] + $totStaff[$kdUnit][P] + $totTdkLangJen[$kdUnit][P];
$grTotal[$kdUnit] = $totalPekerjaLangsung + $totTdkLang + $grTotStaff[$kdUnit];

foreach ($dataAfd as $listAfd) {
	$TotalKarBag[$listAfd][L] = $TotStaff[$listAfd][L] + $totalAfdKerjaLangsungJen[$listAfd][L] + $totAfdTdkLangJen[$listAfd][L];
	$TotalKarBag[$listAfd][P] = $TotStaff[$listAfd][P] + $totalAfdKerjaLangsungJen[$listAfd][P] + $totAfdTdkLangJen[$listAfd][P];
	$grTotalBag[$listAfd] = $grTotStaff[$listAfd] + $totalAfdKerjaLangsung[$listAfd] + $totAfdTdkLang[$listAfd];
}

$TotalKarKnbes[$kdUnit][L] = $totKerjaLangKanBesJen[$kdUnit][L] + $totKanBesJen[$kdUnit][L] + $TotKarKnbesStaff[$kdUnit][L];
$TotalKarKnbes[$kdUnit][P] = $totKerjaLangKanBesJen[$kdUnit][P] + $totKanBesJen[$kdUnit][P] + $TotKarKnbesStaff[$kdUnit][P];
$grTotKary[$kdUnit] = $totKanBes + $totKerjaLangKanBes + $grTotStaff2[$kdUnit];
$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=' . $cols . ' align=left><b>' . $_GET['judul'] . '</b></td><td colspan=9 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</td></tr>' . "\r\n" . '    <tr><td colspan=' . ($cols + 8) . ' align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=4 colspan=3>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2 colspan=3>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=' . $cols . ' >' . $_SESSION['lang']['afdeling'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' rowspan=2 colspan=3>' . $_SESSION['lang']['kantor'] . '</td></tr>';
$tab .= '<tr>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td colspan=3 ' . $bgcoloraja . '>' . $listAfd . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr><td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['luas'] . '</td>';
$tab .= '<td align=right ' . $bgcoloraja . '>' . number_format($grandTotal, 2) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['luas'] . '</td><td ' . $bgcoloraja . '>' . number_format($lsAfdeling[$listAfd], 2) . '</td>';
}

$tab .= "\r\n" . '            <td colspan=2 ' . $bgcoloraja . '>' . $_SESSION['lang']['luas'] . '</td>';
$tab .= '<td align=right ' . $bgcoloraja . ' >' . number_format(0, 2) . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>L</td>';
$tab .= '<td ' . $bgcoloraja . '>P</td>';
$tab .= '<td ' . $bgcoloraja . '>JML</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td ' . $bgcoloraja . '>L</td>';
	$tab .= '<td ' . $bgcoloraja . '>P</td>';
	$tab .= '<td ' . $bgcoloraja . '>JML</td>';
}

$tab .= '<td ' . $bgcoloraja . '>L</td>';
$tab .= '<td ' . $bgcoloraja . '>P</td>';
$tab .= '<td ' . $bgcoloraja . '>JML</td>';
$tab .= '</tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$tab .= '<tr class=rowcontent><td colspan=3><b>I. ' . $_SESSION['lang']['jumlahkaryawan'] . '</b> </td>';
$tab .= '<td align=right>' . number_format($TotalKary[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKary[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotal[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBag[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBag[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBag[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbes[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbes[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKary[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3><b>1. Staff </b></td>';
$tab .= '<td align=right>' . number_format($totStaff[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totStaff[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotStaff[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotStaff[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotStaff[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotStaff[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotKarKnbesStaff[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotKarKnbesStaff[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotStaff2[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3><b>2. ' . $_SESSION['lang']['karyawan'] . ' ' . $_SESSION['lang']['langsung1'] . ' </b></td>';
$tab .= '<td align=right>' . number_format($totalPekerjaLangsungJen[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totalPekerjaLangsungJen[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totalPekerjaLangsung, 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totalAfdKerjaLangsungJen[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totalAfdKerjaLangsungJen[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totalAfdKerjaLangsung[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totKerjaLangKanBesJen[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totKerjaLangKanBesJen[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totKerjaLangKanBes, 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>' . $_SESSION['lang']['panen'] . '</td><td>1. KHT </td>';
$tab .= '<td align=right>' . number_format($totRowPanKht[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPanKht[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPanKht[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowPanKht2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowPanKht2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowPanKht2[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totRowPanKht3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPanKht3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPanKht3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>2. KHL </td>';
$tab .= '<td align=right>' . number_format($totRowPanKhl[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPanKhl[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPanKhl[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowPanKhl2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowPanKhl2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowPanKhl2[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totRowPanKhl3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPanKhl3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPanKhl3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>' . $_SESSION['lang']['pemeltanaman'] . '</td><td>1. KHT </td>';
$tab .= '<td align=right>' . number_format($totRowPerKht[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPerKht[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPerKht[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowPerKht2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowPerKht2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowPerKht2[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totRowPerKht3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPerKht3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPerKht3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>2. KHL </td>';
$tab .= '<td align=right>' . number_format($totRowPerKhl[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPerKhl[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPerKhl[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowPerKhl2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowPerKhl2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowPerKhl2[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totRowPerKhl3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowPerKhl3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowPerKhl3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3><b>3. ' . $_SESSION['lang']['karyawan'] . ' ' . $_SESSION['lang']['tidaklangsung'] . '</b></td>';
$tab .= '<td align=right>' . number_format($totTdkLangJen[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totTdkLangJen[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totTdkLang, 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totAfdTdkLangJen[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totAfdTdkLangJen[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totAfdTdkLang[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totKanBesJen[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totKanBesJen[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totKanBes, 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>1.' . $_SESSION['lang']['mandor'] . '</td><td>BULANAN </td>';
$tab .= '<td align=right>' . number_format($totRowManBul[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManBul[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManBul[$kdUnit], 0) . '</td>';
$karyBulanan += $kdUnit;
$karyBulananLak[$kdUnit] += L;
$karyBulananPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowManBul2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowManBul2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowManBul2[$listAfd], 0) . '</td>';
	$karyBulananLakAfd[$listAfd] += L;
	$karyBulananPerAfd[$listAfd] += P;
	$karyBulananAfd += $listAfd;
}

$tab .= '<td align=right>' . number_format($totRowManBul3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManBul3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManBul3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyBulanan2Lak[$kdUnit] += L;
$karyBulanan2Per[$kdUnit] += P;
$karyBulanan2 += $kdUnit;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHT </td>';
$tab .= '<td align=right>' . number_format($totRowManKht[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManKht[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManKht[$kdUnit], 0) . '</td>';
$karyKht += $kdUnit;
$karyKhtLak[$kdUnit] += L;
$karyKhtPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowManKht2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowManKht2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowManKht2[$listAfd], 0) . '</td>';
	$karyKhtLakAfd[$listAfd] += L;
	$karyKhtPerAfd[$listAfd] += P;
	$karyKhtAfd += $listAfd;
}

$tab .= '<td align=right>' . number_format($totRowManKht3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManKht3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManKht3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKht2 += $kdUnit;
$karyKht2Lak[$kdUnit] += L;
$karyKht2Per[$kdUnit] += P;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHL </td>';
$tab .= '<td align=right>' . number_format($totRowManKhl[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManKhl[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManKhl[$kdUnit], 0) . '</td>';
$karyKhl += $kdUnit;
$karyKhlLak[$kdUnit] += L;
$karyKhlPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowManKhl2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowManKhl2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowManKhl2[$listAfd], 0) . '</td>';
	$karyKhlAfd += $listAfd;
	$karyKhlLakAfd[$listAfd] += L;
	$karyKhlPerAfd[$listAfd] += P;
}

$tab .= '<td align=right>' . number_format($totRowManKhl3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowManKhl3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowManKhl3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKhl2Lak[$kdUnit] += L;
$karyKhl2Per[$kdUnit] += P;
$karyKhl2 += $kdUnit;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>2.' . $_SESSION['lang']['kerani'] . '</td><td>BULANAN </td>';
$tab .= '<td align=right>' . number_format($totRowKranBul[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKranBul[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKranBul[$kdUnit], 0) . '</td>';
$karyBulananLak[$kdUnit] += L;
$karyBulananPer[$kdUnit] += P;
$karyBulanan += $kdUnit;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowKranBul2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowKranBul2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowKranBul2[$listAfd], 0) . '</td>';
	$karyBulananLakAfd[$listAfd] += L;
	$karyBulananPerAfd[$listAfd] += P;
	$karyBulananAfd += $listAfd;
}

$tab .= '<td align=right>' . number_format($totRowKranBul3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKranBul3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKranBul3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyBulanan2Lak[$kdUnit] += L;
$karyBulanan2Per[$kdUnit] += P;
$karyBulanan2 += $kdUnit;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHT </td>';
$tab .= '<td align=right>' . number_format($totRowKraniKht[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKraniKht[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKraniKht[$kdUnit], 0) . '</td>';
$karyKht += $kdUnit;
$karyKhtLak[$kdUnit] += L;
$karyKhtPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowKraniKht2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowKraniKht2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowKraniKht2[$listAfd], 0) . '</td>';
	$karyKhtAfd += $listAfd;
	$karyKhtLakAfd[$listAfd] += L;
	$karyKhtPerAfd[$listAfd] += P;
}

$tab .= '<td align=right>' . number_format($totRowKraniKht3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKraniKht3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKraniKht3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKht2 += $kdUnit;
$karyKht2Lak[$kdUnit] += L;
$karyKht2Per[$kdUnit] += P;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHL </td>';
$tab .= '<td align=right>' . number_format($totRowKraniKhl[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKraniKhl[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKraniKhl[$kdUnit], 0) . '</td>';
$karyKhl += $kdUnit;
$karyKhlLak[$kdUnit] += L;
$karyKhlPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowKraniKhl2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowKraniKhl2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowKraniKhl2[$listAfd], 0) . '</td>';
	$karyKhlAfd += $listAfd;
	$karyKhlLakAfd[$listAfd] += L;
	$karyKhlPerAfd[$listAfd] += P;
}

$tab .= '<td align=right>' . number_format($totRowKraniKhl3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowKraniKhl3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowKraniKhl3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKhl2 += $kdUnit;
$karyKhl2Lak[$kdUnit] += L;
$karyKhl2Per[$kdUnit] += P;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>3.' . $_SESSION['lang']['lain'] . '</td><td>BULANAN </td>';
$tab .= '<td align=right>' . number_format($totRowLainBul[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainBul[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainBul[$kdUnit], 0) . '</td>';
$karyBulananLak[$kdUnit] += L;
$karyBulananPer[$kdUnit] += P;
$karyBulanan += $kdUnit;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowLainBul2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowLainBul2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowLainBul2[$listAfd], 0) . '</td>';
	$karyBulananLakAfd[$listAfd] += L;
	$karyBulananPerAfd[$listAfd] += P;
	$karyBulananAfd += $listAfd;
}

$tab .= '<td align=right>' . number_format($totRowLainBul3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainBul3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainBul3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyBulanan2Lak[$kdUnit] += L;
$karyBulanan2Per[$kdUnit] += P;
$karyBulanan2 += $kdUnit;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHT </td>';
$tab .= '<td align=right>' . number_format($totRowLainKht[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainKht[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainKht[$kdUnit], 0) . '</td>';
$karyKht += $kdUnit;
$karyKhtLak[$kdUnit] += L;
$karyKhtPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowLainKht2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowLainKht2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowLainKht2[$listAfd], 0) . '</td>';
	$karyKhtAfd += $listAfd;
	$karyKhtLakAfd[$listAfd] += L;
	$karyKhtPerAfd[$listAfd] += P;
}

$tab .= '<td align=right>' . number_format($totRowLainKht3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainKht3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainKht3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKht2 += $kdUnit;
$karyKht2Lak[$kdUnit] += L;
$karyKht2Per[$kdUnit] += P;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHL </td>';
$tab .= '<td align=right>' . number_format($totRowLainKhl[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainKhl[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainKhl[$kdUnit], 0) . '</td>';
$karyKhl += $kdUnit;
$karyKhlLak[$kdUnit] += L;
$karyKhlPer[$kdUnit] += P;

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($totRowLainKhl2[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totRowLainKhl2[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grRowLainKhl2[$listAfd], 0) . '</td>';
	$karyKhlAfd += $listAfd;
	$karyKhlLakAfd[$listAfd] += L;
	$karyKhlPerAfd[$listAfd] += P;
}

$tab .= '<td align=right>' . number_format($totRowLainKhl3[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($totRowLainKhl3[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grRowLainKhl3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$karyKhl2 += $kdUnit;
$karyKhl2Lak[$kdUnit] += L;
$karyKhl2Per[$kdUnit] += P;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td><b>' . $_SESSION['lang']['jumlahkaryawan'] . '</b></td><td>Bulanan</td>';
$tab .= '<td align=right>' . number_format($karyBulananLak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyBulananPer[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyBulanan[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($karyBulananLakAfd[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyBulananPerAfd[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyBulananAfd[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($karyBulanan2Lak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyBulanan2Per[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyBulanan2[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHT</td>';
$tab .= '<td align=right>' . number_format($karyKhtLak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKhtPer[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKht[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($karyKhtLakAfd[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyKhtPerAfd[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyKhtAfd[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($karyKht2Lak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKht2Per[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKht2[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>&nbsp;</td><td>KHL</td>';
$tab .= '<td align=right>' . number_format($karyKhlLak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKhlPer[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKhl[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($karyKhlLakAfd[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyKhlPerAfd[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($karyKhlAfd[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($karyKhl2Lak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKhl2Per[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($karyKhl2[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2><b>4. ' . $_SESSION['lang']['hkborongan'] . '</b> </td>';
$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
$tab .= '<td align=right>' . number_format(0, 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
	$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
	$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
}

$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Pasangan\',\'Anak\') group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalSma += $kdUnit;
	$TotalKarySma[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Pasangan\',\'Anak\') group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagSma += $rDataKary2['subbagian'];
		$TotalKarBagSma[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')   and hubungankeluarga in (\'Pasangan\',\'Anak\') group by kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKarySma += $kdUnit;
	$TotalKarKnbesSma[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td colspan=3><b>II. ' . $_SESSION['lang']['tanggungan'] . '</b></td>';
$tab .= '<td align=right>' . number_format($TotalKarySma[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarySma[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalSma[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagSma[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagSma[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagSma[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesSma[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesSma[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKarySma[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Pasangan\') and kelminkeluarga=\'W\' group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalIstri += $kdUnit;
	$TotalKaryIstri[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Pasangan\') and kelminkeluarga=\'W\'  group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagIstri += $rDataKary2['subbagian'];
		$TotalKarBagIstri[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')   and hubungankeluarga in (\'Pasangan\') and kelminkeluarga=\'W\'  group by kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKaryIstri += $kdUnit;
	$TotalKarKnbesIstri[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>5. Istri Tanggungan (tidak bekerja)</td>';
$tab .= '<td align=right>' . number_format($TotalKaryIstri[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKaryIstri[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalIstri[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagIstri[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagIstri[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagIstri[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesIstri[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesIstri[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKaryIstri[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Anak\') group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalAnak += $kdUnit;
	$TotalKaryAnak[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Anak\') group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagAnak += $rDataKary2['subbagian'];
		$TotalKarBagAnak[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')   and hubungankeluarga in (\'Anak\') group by kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKaryAnak += $kdUnit;
	$TotalKarKnbesAnak[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>6. Anak</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnak[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnak[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalAnak[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagAnak[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagAnak[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagAnak[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesAnak[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesAnak[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKaryAnak[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Anak\')  and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=5 group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalAnakBal += $kdUnit;
	$TotalKaryAnakBal[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Anak\')  and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=5 group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagAnakBal += $rDataKary2['subbagian'];
		$TotalKarBagAnakBal[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as age,  count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')  and hubungankeluarga in (\'Anak\')  and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=5 group by karyawanid,kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKaryAnakBal += $kdUnit;
	$TotalKarKnbesAnakBal[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>6.1.  Balita  (1 - 5 Tahun)</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakBal[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakBal[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalAnakBal[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakBal[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakBal[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagAnakBal[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakBal[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakBal[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKaryAnakBal[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2)>5 and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=18 group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalAnakSek += $kdUnit;
	$TotalKaryAnakSek[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2)>5 and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=18 group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagAnakSek += $rDataKary2['subbagian'];
		$TotalKarBagAnakSek[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as age,  count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')  and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2)>5 and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) <=18 group by karyawanid,kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKaryAnakSek += $kdUnit;
	$TotalKarKnbesAnakSek[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>6.2.  Usia Sekolah  (6 - 18 Tahun)</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakSek[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakSek[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalAnakSek[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakSek[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakSek[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagAnakSek[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakSek[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakSek[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalBagAnakSek[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sDataKary = 'select distinct count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' ' . "\r\n" . '                    and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') ' . "\r\n" . '                    and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) >18 group by kelminkeluarga';

#exit(mysql_error());
($qDataKary = mysql_query($sDataKary)) || true;

while ($rDataKary = mysql_fetch_assoc($qDataKary)) {
	$grTotalAnakLapan += $kdUnit;
	$TotalKaryAnakLapan[$kdUnit] += $rDataKary['kelminkeluarga'];
}

foreach ($dataAfd as $listAfd) {
	$sDataKary2 = 'select distinct count(karyawanid) as total,kelminkeluarga,subbagian from ' . $dbname . '.sdm_tanggungan_vw where lokasitugas=\'' . $kdUnit . '\' and subbagian=\'' . $kdAfd . '\' ' . "\r\n" . '             and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\') and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) >18 group by kelminkeluarga,subbagian';

	#exit(mysql_error());
	($qDataKary2 = mysql_query($sDataKary2)) || true;

	while ($rDataKary2 = mysql_fetch_assoc($qDataKary2)) {
		$grTotalBagAnakLapan += $rDataKary2['subbagian'];
		$TotalKarBagAnakLapan[$rDataKary2['subbagian']] += $rDataKary2['kelminkeluarga'];
	}
}

$sDataKary25 = 'select distinct ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as age,  count(karyawanid) as total,kelminkeluarga from ' . $dbname . '.sdm_tanggungan_vw where ' . "\r\n" . '              lokasitugas=\'' . $kdUnit . '\' and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') ' . "\r\n" . '              and (substr(tglkeluar,1,7)>\'' . $periode . '\' or substr(tglkeluar,1,7)=\'0000-00\')  and hubungankeluarga in (\'Anak\') and ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) >18 group by karyawanid,kelminkeluarga';

#exit(mysql_error());
($qDataKary25 = mysql_query($sDataKary25)) || true;

while ($rData = mysql_fetch_assoc($qDataKary25)) {
	$grTotKaryAnakLapan += $kdUnit;
	$TotalKarKnbesAnakLapan[$kdUnit] += $rData['kelminkeluarga'];
}

$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>6.3.  Usia > 18 Tahun</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakLapan[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKaryAnakLapan[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotalAnakLapan[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakLapan[$listAfd][P], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBagAnakLapan[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBagAnakLapan[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakLapan[$kdUnit][P], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbesAnakLapan[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKaryAnakLapan[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2><b>' . $_SESSION['lang']['total'] . ' ' . $_SESSION['lang']['penduduk'] . '</b></td>';
$tab .= '<td align=right>' . number_format($TotalKary[$kdUnit][L] + $TotalKarySma[$kdUnit][L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKary[$kdUnit][P] + $TotalKarySma[$kdUnit][W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotal[$kdUnit] + $grTotalSma[$kdUnit], 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	$tab .= '<td align=right>' . number_format($TotalKarBag[$listAfd][L] + $TotalKarBagSma[$listAfd][L], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($TotalKarBag[$listAfd][P] + $TotalKarBagSma[$listAfd][W], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grTotalBag[$listAfd] + $grTotalBagSma[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($TotalKarKnbes[$kdUnit][L] + $TotalKarKnbesSma[L], 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotalKarKnbes[$kdUnit][P] + $TotalKarKnbesSma[W], 0) . '</td>';
$tab .= '<td align=right>' . number_format($grTotKary[$kdUnit] + $grTotKarySma[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$sKaryLangsung = 'select count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where  ' . "\r\n" . '                        kodejabatan in(45,88,60,168) and  tipekaryawan in (2,3,4) and lokasitugas=\'' . $kdUnit . '\' group by jeniskelamin';

#exit(mysql_error());
($qKaryLangsung = mysql_query($sKaryLangsung)) || true;

while ($rKaryLangsung = mysql_fetch_assoc($qKaryLangsung)) {
	$grKaryLangsung += $kdUnit;
	$totKaryLangsung[$kdUnit] += $rKaryLangsung['jeniskelamin'];
}

foreach ($dataAfd as $listAfd) {
	$sKaryLangsung = 'select count(karyawanid) as total,jeniskelamin,subbagian from ' . $dbname . '.datakaryawan where  ' . "\r\n" . '                            kodejabatan in(45,88,60,168) and  tipekaryawan in (2,3,4) and lokasitugas=\'' . $kdUnit . '\'and subbagian=\'' . $listAfd . '\' group by jeniskelamin,subbagian';

	#exit(mysql_error());
	($qKaryLangsung = mysql_query($sKaryLangsung)) || true;

	while ($rKaryLangsung = mysql_fetch_assoc($qKaryLangsung)) {
		$grKaryLang += $rKaryLangsung['subbagian'];
		$totKaryLang[$rKaryLangsung['subbagian']] += $rKaryLangsung['jeniskelamin'];
	}
}

$sKaryLangsung2 = 'select count(karyawanid) as total,jeniskelamin from ' . $dbname . '.datakaryawan where  ' . "\r\n" . '                         kodejabatan in(45,88,60,168) and  tipekaryawan in (2,3,4) and lokasitugas=\'' . $kdUnit . '\'' . "\r\n" . '                         and subbagian not in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') group by jeniskelamin';

#exit(mysql_error());
($qKaryLangsung2 = mysql_query($sKaryLangsung2)) || true;

while ($rKaryLangsung2 = mysql_fetch_assoc($qKaryLangsung2)) {
	$grKaryLangKanBes += $kdUnit;
	$totKaryLangKanBes[$kdUnit] += $rKaryLangsung2['jeniskelamin'];
}

$tab .= '<tr class=rowcontent><td colspan=3><b>III. R A S I O</b></td>';
$tab .= '<td colspan=' . ($cols + 8) . '>&nbsp;</td></tr>';
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>1. Rasio Pekerja per Ha</td>';
@$totKrjLak = $TotalKary[$kdUnit][L] / $grandTotal;
@$totKrjPer = $TotalKary[$kdUnit][P] / $grandTotal;
@$totKrj = $grTotal[$kdUnit] / $grandTotal;
$tab .= '<td align=right>' . number_format($totKrjLak, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjPer, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrj, 3) . '</td>';

foreach ($dataAfd as $listAfd) {
	@$totKrjAfdLak = $TotalKarBag[$listAfd][L] / $lsAfdeling[$listAfd];
	@$totKrjAfdPer = $TotalKarBag[$listAfd][P] / $lsAfdeling[$listAfd];
	@$totKrjAfd = $grTotalBag[$listAfd] / $lsAfdeling[$listAfd];
	$tab .= '<td align=right>' . number_format($totKrjAfdLak, 3) . '</td>';
	$tab .= '<td align=right>' . number_format($totKrjAfdPer, 3) . '</td>';
	$tab .= '<td align=right>' . number_format($totKrjAfd, 3) . '</td>';
}

@$totKrjKanBesLak = $TotalKarKnbes[$kdUnit][L] / $grandTotal;
@$totKrjKanBesPer = $TotalKarKnbes[$kdUnit][P] / $grandTotal;
@$totKrjKanBes = $grTotKary[$kdUnit] / $grandTotal;
$tab .= '<td align=right>' . number_format($totKrjKanBesLak, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjKanBesPer, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjKanBes, 3) . '</td>';
$tab .= '</tr>';
@$totKrjLangLak = $totKaryLangsung[$kdUnit][L] / $grandTotal;
@$totKrjLangPer = $totKaryLangsung[$kdUnit][P] / $grandTotal;
@$totKrjLang = $grKaryLangsung[$kdUnit] / $grandTotal;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>2. Rasio Pekerja Langsung per  Ha </td>';
$tab .= '<td align=right>' . number_format($totKrjLangLak, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjLangPer, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjLang, 3) . '</td>';

foreach ($dataAfd as $listAfd) {
	@$totKrjLangAfdLak = $totKaryLang[$listAfd][L] / $lsAfdeling[$listAfd];
	@$totKrjLangAfdPer = $totKaryLang[$listAfd][P] / $lsAfdeling[$listAfd];
	@$totKrjLangAfd = $grKaryLang[$listAfd] / $lsAfdeling[$listAfd];
	$tab .= '<td align=right>' . number_format($totKrjLangAfdLak, 3) . '</td>';
	$tab .= '<td align=right>' . number_format($totKrjLangAfdPer, 3) . '</td>';
	$tab .= '<td align=right>' . number_format($totKrjLangAfd, 3) . '</td>';
}

@$totKrjLangKanbesLak = $totKaryLangKanBes[$kdUnit][L] / $grandTotal;
@$totKrjLangKanbesPer = $totKaryLangKanBes[$kdUnit][P] / $grandTotal;
@$totKrjLangKanbes = $grKaryLangKanBes[$kdUnit] / $grandTotal;
$tab .= '<td align=right>' . number_format($totKrjLangKanbesLak, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjLangKanbesPer, 3) . '</td>';
$tab .= '<td align=right>' . number_format($totKrjLangKanbes, 3) . '</td>';
$tab .= '</tr>';
$totalMandorBlnLak = $karyBulananLak[$kdUnit][L] + $karyKhtLak[$kdUnit][L] + $karyKhlLak[$kdUnit][L];
$totalMandorBlnPer = $karyBulananPer[$kdUnit][P] + $karyKhtPer[$kdUnit][P] + $karyKhlPer[$kdUnit][P];
$totalMandor = $karyBulananAfd[$kdUnit] + $karyKhtAfd[$kdUnit] + $karyKhl[$kdUnit];

foreach ($dataAfd as $listAfd) {
	$totManAfdLak[$listAfd][L] = $karyBulananLakAfd[$listAfd][L] + $karyKhtLakAfd[$listAfd][L] + $karyKhlLakAfd[$listAfd][L];
	$totManAfdPer[$listAfd][P] = $karyBulananPerAfd[$listAfd][P] + $karyKhtPerAfd[$listAfd][P] + $karyKhlPerAfd[$listAfd][P];
	$totManAfd[$listAfd] = $karyBulananAfd[$listAfd] + $karyKhtAfd[$listAfd] + $karyKhlAfd[$listAfd];
}

$totManKanBes = $karyBulanan2[$kdUnit] + $karyKht2[$kdUnit] + $karyKhl2[$kdUnit];
$totManKanBesLak = $karyBulanan2Lak[$kdUnit][L] + $karyKht2Lak[$kdUnit][L] + $karyKhl2Lak[$kdUnit][L];
$totManKanBesPer = $karyBulanan2Per[$kdUnit][P] + $karyKht2Per[$kdUnit][P] + $karyKhl2Per[$kdUnit][P];
@$LakTotMan = $totKaryLangsung[$kdUnit][L] / $totalMandorBlnLak;
@$PerTotMan = $totKaryLangsung[$kdUnit][P] / $totalMandorBlnPer;
@$TotMan = $grKaryLangsung[$kdUnit] / $totalMandor;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>3. Rasio Pekerja Langsung per Mandor </td>';
$tab .= '<td align=right>' . number_format($LakTotMan, 0) . '</td>';
$tab .= '<td align=right>' . number_format($PerTotMan, 0) . '</td>';
$tab .= '<td align=right>' . number_format($TotMan, 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	@$AfdLak[$listAfd] = $totKaryLang[$listAfd][L] / $totManAfdLak[$listAfd][L];
	@$AfdPer[$listAfd] = $totKaryLang[$listAfd][P] / $totManAfdPer[$listAfd][P];
	@$totAfd[$listAfd] = $grKaryLang[$listAfd] / $totManAfd[$listAfd];
	$tab .= '<td align=right>' . number_format($AfdLak[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($AfdPer[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totAfd, 0) . '</td>';
}

@$kanBesLak = $totKaryLangKanBes[$kdUnit][L] / $totManKanBesLak;
@$kanBesPer = $totKaryLangKanBes[$kdUnit][P] / $totManKanBesPer;
@$totKanBes = $grKaryLangKanBes[$kdUnit] / $totManKanBes;
$tab .= '<td align=right>' . number_format($kanBesLak, 0) . '</td>';
$tab .= '<td align=right>' . number_format($kanBesPer, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totKanBes, 0) . '</td>';
$tab .= '</tr>';
@$persenLaki = ($totalPekerjaLangsungJen[$kdUnit][L] / $totalPekerjaLangsung) * 100;
@$persenPer = ($totalPekerjaLangsungJen[$kdUnit][P] / $totalPekerjaLangsung) * 100;
@$totPersen = $persenLaki + $persenPer;

foreach ($dataAfd as $listAfd) {
	@$persenLakiAfd[$listAfd] = ($totalAfdKerjaLangsungJen[$listAfd][L] / $totalAfdKerjaLangsung[$listAfd]) * 100;
	@$persenPerAfd[$listAfd] = ($totalAfdKerjaLangsungJen[$listAfd][P] / $totalAfdKerjaLangsung[$listAfd]) * 100;
	@$totPersenLakiAfd[$listAfd] = $persenLakiAfd[$listAfd] + $persenPerAfd[$listAfd];
}

@$persenKanbesLak = ($totKerjaLangKanBesJen[$kdUnit][L] / $totKerjaLangKanBes) * 100;
@$persenKanbesPer = ($totKerjaLangKanBesJen[$kdUnit][P] / $totKerjaLangKanBes) * 100;
$totPersenKanBes = $persenKanbesLak + $persenKanbesPer;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=2>4. % TK Langsung Perempuan per TK Langsung</td>';
$tab .= '<td align=right>' . number_format($persenLaki, 0) . '</td>';
$tab .= '<td align=right>' . number_format($persenPer, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totPersen, 0) . '</td>';

foreach ($dataAfd as $listAfd) {
	@$AfdLak[$listAfd] = $totKaryLang[$listAfd][L] / $totManAfdLak[$listAfd][L];
	@$AfdPer[$listAfd] = $totKaryLang[$listAfd][P] / $totManAfdPer[$listAfd][P];
	@$totAfd[$listAfd] = $grKaryLang[$listAfd] / $totManAfd[$listAfd];
	$tab .= '<td align=right>' . number_format($persenLakiAfd[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($persenPerAfd[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totPersenLakiAfd[$listAfd], 0) . '</td>';
}

$tab .= '<td align=right>' . number_format($totPersenKanBes, 0) . '</td>';
$tab .= '<td align=right>' . number_format($persenKanbesPer, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totPersenKanBes, 0) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHms');
	$nop_ = 'workersANDLiability_' . $purId . '_' . $dte;
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
			global $judul;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $grandTotal;
			global $lsAfdeling;
			global $cols;
			$cols = count($dataAfd) * 66;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper($judul), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$height = 80;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(180, $height, $_SESSION['lang']['uraian'], 1, 0, 'C', 1);
			$this->Cell(66, 40, $_SESSION['lang']['total'], 1, 0, 'C', 1);
			$this->Cell($cols, 20, $_SESSION['lang']['afdeling'], 1, 0, 'C', 1);
			$this->Cell(66, 40, $_SESSION['lang']['kantor'], 1, 1, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr - 20);
			$this->SetX($ksamping + 246);

			foreach ($dataAfd as $kdAfd) {
				$this->Cell(66, 20, $kdAfd, 1, 0, 'C', 1);
			}

			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping - ($cols + 66));
			$this->Cell(33, 20, $_SESSION['lang']['luas'], 1, 0, 'C', 1);
			$this->Cell(33, 20, number_format($grandTotal, 0), 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping);

			foreach ($dataAfd as $kdAfd) {
				$this->Cell(33, 20, $_SESSION['lang']['luas'], 1, 0, 'C', 1);
				$this->Cell(33, 20, number_format($lsAfdeling[$kdAfd], 0), 1, 0, 'C', 1);
			}

			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping);
			$this->Cell(33, 20, $_SESSION['lang']['luas'], 1, 0, 'C', 1);
			$this->Cell(33, 20, number_format($grandTotal, 0), 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping - ($cols + 132));
			$this->Cell(22, 20, 'L', 1, 0, 'C', 1);
			$this->Cell(22, 20, 'P', 1, 0, 'C', 1);
			$this->Cell(22, 20, 'JML', 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping);

			foreach ($dataAfd as $kdAfd) {
				$this->Cell(22, 20, 'L', 1, 0, 'C', 1);
				$this->Cell(22, 20, 'P', 1, 0, 'C', 1);
				$this->Cell(22, 20, 'JML', 1, 0, 'C', 1);
			}

			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping);
			$this->Cell(22, 20, 'L', 1, 0, 'C', 1);
			$this->Cell(22, 20, 'P', 1, 0, 'C', 1);
			$this->Cell(22, 20, 'JML', 1, 1, 'C', 1);
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
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);
	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->Cell(180, $height, 'I.' . $_SESSION['lang']['jumlahkaryawan'], 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKary[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKary[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotal[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBag[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBag[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBag[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbes[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbes[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKary[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '1. Staff', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totStaff[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totStaff[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totStaff[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotStaff[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotStaff[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotStaff[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotKarKnbesStaff[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotKarKnbesStaff[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotStaff2[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '2. ' . $_SESSION['lang']['karyawan'] . ' ' . $_SESSION['lang']['langsung1'], 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totalPekerjaLangsungJen[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totalPekerjaLangsungJen[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totalPekerjaLangsung, 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totalAfdKerjaLangsungJen[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totalAfdKerjaLangsungJen[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totalAfdKerjaLangsung[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totKerjaLangKanBesJen[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKerjaLangKanBesJen[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKerjaLangKanBes, 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '1). ' . $_SESSION['lang']['panen'], TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowPanKht[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPanKht[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPanKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowPanKht2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowPanKht2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowPanKht2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowPanKht3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPanKht3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPanKht3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowPanKhl[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPanKhl[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPanKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowPanKhl2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowPanKhl2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowPanKhl2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowPanKhl3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPanKhl3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPanKhl3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '1). ' . $_SESSION['lang']['pemeltanaman'], TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowPerKht[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPerKht[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPerKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowPerKht2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowPerKht2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowPerKht2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowPerKht3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPerKht3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPerKht3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowPerKhl[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPerKhl[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPerKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowPerKhl2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowPerKhl2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowPerKhl2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowPerKhl3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowPerKhl3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowPerKhl3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '3. ' . $_SESSION['lang']['karyawan'] . ' ' . $_SESSION['lang']['tidaklangsung'], 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totTdkLangJen[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totTdkLangJen[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totTdkLang, 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totAfdTdkLangJen[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totAfdTdkLangJen[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totAfdTdkLang[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totKanBesJen[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKanBesJen[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKanBes, 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '1). ' . $_SESSION['lang']['mandor'], TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': BULANAN', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowManBul[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManBul[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManBul[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowManBul2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowManBul2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowManBul2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowManBul3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManBul3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManBul3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowManKht[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManKht[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowManKht2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowManKht2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowManKht2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowManKht3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManKht3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManKht3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowManKhl[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManKhl[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowManKhl2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowManKhl2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowManKhl2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowManKhl3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowManKhl3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManKhl3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '2). ' . $_SESSION['lang']['kerani'], TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': BULANAN', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowKranBul[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKranBul[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowManBul[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowKranBul2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowKranBul2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowKranBul2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowKranBul3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKranBul3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowKranBul3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowKraniKht[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKraniKht[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowKraniKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowKraniKht2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowKraniKht2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowKraniKht2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowKraniKht3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKraniKht3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowKraniKht3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowKraniKhl[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKraniKhl[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowKraniKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowKraniKhl2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowKraniKhl2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowKraniKhl2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowKraniKhl3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowKraniKhl3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowKraniKhl3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '3). ' . $_SESSION['lang']['lain'], TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': BULANAN', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowLainBul[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainBul[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainBul[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowLainBul2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowLainBul2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowLainBul2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowLainBul3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainBul3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainBul3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowLainKht[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainKht[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowLainKht2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowLainKht2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowLainKht2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowLainKht3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainKht3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainKht3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, ' ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totRowLainKhl[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainKhl[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totRowLainKhl2[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totRowLainKhl2[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grRowLainKhl2[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totRowLainKhl3[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totRowLainKhl3[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grRowLainKhl3[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, 'TOTAL  ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': Bulanan', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($karyBulananLak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyBulananPer[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyBulanan[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($karyBulananLakAfd[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyBulananPerAfd[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyBulananAfd[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($karyBulanan2Lak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyBulanan2Per[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyBulanan2[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '  ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHT', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($karyKhtLak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKhtPer[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKht[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($karyKhtLakAfd[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyKhtPerAfd[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyKhtAfd[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($karyKht2Lak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKht2Per[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKht2[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(100, $height, '  ', TB, 0, 'L', 0);
	$pdf->Cell(60, $height, ': KHL', TB, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($karyKhlLak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKhlPer[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKhl[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($karyKhlLakAfd[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyKhlPerAfd[$listAfd][P], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($karyKhlAfd[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($karyKhl2Lak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKhl2Per[$kdUnit][P], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($karyKhl2[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '4. ' . $_SESSION['lang']['hkborongan'], 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format(0, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format(0, 0), 1, 1, 'R', 1);
	$pdf->Cell(180, $height, 'II. ' . strtoupper($_SESSION['lang']['tanggungan']), 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKarySma[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarySma[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalSma[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagSma[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagSma[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagSma[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesSma[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesSma[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKarySma[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '5.Istri Tanggungan (tidak bekerja) ', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryIstri[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryIstri[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalIstri[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagIstri[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagIstri[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagIstri[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesIstri[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesIstri[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKaryIstri[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '6. Anak', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryAnak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryAnak[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalAnak[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagAnak[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagAnak[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagAnak[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnak[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnak[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKaryAnak[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '6.1.  Balita  (1 - 5 Tahun)', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakBal[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakBal[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalAnakBal[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakBal[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakBal[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagAnakBal[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakBal[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakBal[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKaryAnakBal[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '6.2.  Usia Sekolah  (6 - 18 Tahun)', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakSek[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakSek[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalAnakSek[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakSek[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakSek[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagAnakSek[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakSek[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakSek[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalBagAnakSek[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '6.2.  Usia Sekolah  (6 - 18 Tahun)', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakSek[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakSek[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalAnakSek[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakSek[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakSek[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagAnakSek[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakSek[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakSek[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalBagAnakSek[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '6.3.  Usia > 18 Tahun', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakLapan[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKaryAnakSek[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotalAnakLapan[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakLapan[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBagAnakLapan[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBagAnakLapan[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakLapan[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbesAnakLapan[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKaryAnakLapan[$kdUnit], 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, 'TOTAL PENDUDUK KEBUN', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($TotalKary[$kdUnit][L] + $TotalKarySma[$kdUnit][L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKary[$kdUnit][P] + $TotalKarySma[$kdUnit][W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotal[$kdUnit] + $grTotalSma[$kdUnit], 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($TotalKarBag[$listAfd][L] + $TotalKarBagSma[$listAfd][L], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($TotalKarBag[$listAfd][P] + $TotalKarBagSma[$listAfd][W], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($grTotalBag[$listAfd] + $grTotalBagSma[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($TotalKarKnbes[$kdUnit][L] + $TotalKarKnbesSma[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotalKarKnbes[$kdUnit][P] + $TotalKarKnbesSma[W], 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($grTotKary[$kdUnit] + $grTotKarySma[$kdUnit], 0), 1, 1, 'R', 1);
	$cols = 132 + $cols;
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, 'III. R A S I O', 1, 0, 'L', 1);
	$pdf->Cell($cols, $height, '  ', 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '1. Rasio Pekerja per Ha', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totKrjLak, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjPer, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrj, 3), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totKrjAfdLak, 3), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totKrjAfdPer, 3), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totKrjAfd, 3), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totKrjKanBesLak, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjKanBesPer, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjKanBes, 3), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '2. Rasio Pekerja Langsung per  Ha', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($totKrjLangLak, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjLangPer, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjLang, 3), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($totKrjLangAfdLak, 3), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totKrjLangAfdPer, 3), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totKrjLangAfd, 3), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totKrjLangKanbesLak, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjLangKanbesPer, 3), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKrjLangKanbes, 3), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '3. Rasio Pekerja Langsung per Mandor ', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($LakTotMan, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($PerTotMan, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($TotMan, 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($AfdLak[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($AfdPer[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totAfd, 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($kanBesLak, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($kanBesPer, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totKanBes, 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(180, $height, '4. % TK Langsung Perempuan per TK Langsung', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(22, $height, number_format($persenLaki, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($persenPer, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totPersen, 0), 1, 0, 'R', 1);

	foreach ($dataAfd as $listAfd) {
		$pdf->Cell(22, $height, number_format($persenLakiAfd[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($persenPerAfd[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(22, $height, number_format($totPersenLakiAfd[$listAfd], 0), 1, 0, 'R', 1);
	}

	$pdf->Cell(22, $height, number_format($totPersenKanBes, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($persenKanbesPer, 0), 1, 0, 'R', 1);
	$pdf->Cell(22, $height, number_format($totPersenKanBes, 0), 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
