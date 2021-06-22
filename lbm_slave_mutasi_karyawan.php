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

$addTmb = ' lokasitugas=\'' . $kdUnit . '\'';

if ($afdId != '') {
	$addTmb = ' subbagian=\'' . $afdId . '\'';
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

$thn = explode('-', $periode);

if ($thn[1] != '12') {
	$blnData = intval($thn[1]) + 1;
	strlen($blnData) < 2 ? $blnData = '0' . $blnData : $blnData = $blnData;
	$nxtPeriode = $thn[0] . '-' . $blnData;
}
else {
	$dtThn = intval($thn[0]) + 1;
	$nxtPeriode = $dtThn . '-01';
}

$whereAwal = ' and (substr(tanggalmasuk,1,7)<\'' . $thn[0] . '-01\' and substr(tanggalkeluar,1,7)>=\'' . $thn[0] . '-01\')';
$whereBlnKmrn = ' and  (substr(tanggalmasuk,1,7)<\'' . $periode . '\' and substr(tanggalkeluar,1,7)>=\'' . $periode . '\')';
$whereBlnIniPnmbah = ' and  (substr(tanggalmasuk,1,7)>=\'' . $periode . '\' and substr(tanggalmasuk,1,7)<\'' . $nxtPeriode . '\')';
$whereBlnIniPnmbahSi = ' and  (substr(tanggalmasuk,1,7)>=\'' . $thn[0] . '-01\' and substr(tanggalmasuk,1,7)<\'' . $nxtPeriode . '\')';
$whereBlnIniPeng = ' and  (substr(tanggalkeluar,1,7)>=\'' . $periode . '\' and substr(tanggalkeluar,1,7)<\'' . $nxtPeriode . '\')';
$whereBlnIniPengSi = ' and  (substr(tanggalkeluar,1,7)>=\'' . $thn[0] . '-01\' and substr(tanggalkeluar,1,7)<\'' . $nxtPeriode . '\')';
$sStaff = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereAwal . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;
$rStaff = mysql_fetch_assoc($qStaff);
$jumKaryStaff[$kdUnit] = $rStaff['jumKary'];
$sStaffBln = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereBlnKmrn . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);
$jumKaryBlnLalu[$kdUnit] = $rStaffBln['jumKary'];
$sStaffBln = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereBlnIniPnmbah . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);
$jumKaryBlnIni[$kdUnit] = $rStaffBln['jumKary'];
$sStaffBln = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);
$jumKarySmpBlnIni[$kdUnit] = $rStaffBln['jumKary'];
$sStaffBln = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereBlnIniPeng . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);
$jumKaryPengBlnini[$kdUnit] = $rStaffBln['jumKary'];
$sStaffBln = 'select distinct count(karyawanid) as jumKary from ' . $dbname . '.datakaryawan where tipekaryawan=5 and ' . $addTmb . ' ' . $whereBlnIniPengSi . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);
$jumKaryPengSmpBlnini[$kdUnit] = $rStaffBln['jumKary'];
$posisiSbiStaff = ($jumKaryStaff[$kdUnit] + $jumKarySmpBlnIni[$kdUnit]) - $jumKaryPengSmpBlnini[$kdUnit];
@$turnOver = ($jumKaryPengSmpBlnini[$kdUnit] / $posisiSbiStaff) * 100;
$addBagian = '';
$bagian = '';

if ($afdId == '') {
	$addBagian = ' and subbagian in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') group by subbagian order by subbagian asc ';
	$bagian = ' and (subbagian is null or subbagian=\'\')';
}

$totBlnAwl = 0;
$totBlnKmrn = 0;
$totBlnPenBi = 0;
$totBlnPenSmpBi = 0;
$totBlnPeng = 0;
$totBlnPengSmpBi = 0;
$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereAwal . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn[$rStaff['subbagian']] = $rStaff['jumKary'];
	$totBlnAwl += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnKmrn += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenBi += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPeng += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and  ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi += $rStaffBln['jumKary'];
}

$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereAwal . ' ' . $bagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn[$kdUnit] = $rStaff['jumKary'];
	$totBlnAwl += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnKmrn += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenBi += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPeng += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=1 and ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi += $rStaffBln['jumKary'];
}

$posisiSbiBlnanAfd[$kdUnit] = ($jmKBlnanAwlThn[$kdUnit] + $jmKBSmpBlnini[$kdUnit]) - $jmKBPengSmpBlnIni[$kdUnit];
@$turnOverBlnanAfd[$kdUnit] = ($posisiSbiBlnanAfd[$kdUnit] / $jmKBSmpBlnini[$kdUnit]) * 100;
$posisiSbiBulanan = ($totBlnAwl + $totBlnPenSmpBi) - $totBlnPengSmpBi;
@$turnOverBlnan = ($totBlnPengSmpBi / $posisiSbiBulanan) * 100;

foreach ($dataAfd as $listAfd) {
	$posisiSbiBlnanAfd[$listAfd] = ($jmKBlnanAwlThn[$listAfd] + $jmKBSmpBlnini[$listAfd]) - $jmKBPengSmpBlnIni[$listAfd];
	@$turnOverBlnanAfd[$listAfd] = ($posisiSbiBlnanAfd[$listAfd] / $jmKBSmpBlnini[$listAfd]) * 100;
}

$totBlnAwl2 = 0;
$totBlnKmrn2 = 0;
$totBlnPenBi2 = 0;
$totBlnPenSmpBi2 = 0;
$totBlnPeng2 = 0;
$totBlnPengSmpBi2 = 0;
$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereAwal . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn2[$rStaff['subbagian']] = $rStaff['jumKary'];
	$totBlnAwl2 += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnKmrn2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenBi2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPeng2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi2 += $rStaffBln['jumKary'];
}

$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereAwal . ' ' . $bagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn2[$kdUnit] = $rStaff['jumKary'];
	$totBlnAwl2 += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu2[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnKmrn2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni2[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenBi2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini2[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng2[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPeng2 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan in (2,3) and ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni2[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi2 += $rStaffBln['jumKary'];
}

$posisiSbiBlnanAfd2[$kdUnit] = ($jmKBlnanAwlThn2[$kdUnit] + $jmKBSmpBlnini2[$kdUnit]) - $jmKBPengSmpBlnIni2[$kdUnit];
@$turnOverBlnanAfd2[$kdUnit] = ($posisiSbiBlnanAfd2[$kdUnit] / $jmKBSmpBlnini2[$kdUnit]) * 100;
$posisiSbiBulanan2 = ($totBlnAwl2 + $totBlnPenSmpBi2) - $totBlnPengSmpBi2;
@$turnOverBlnan2 = ($totBlnPengSmpBi2 / $posisiSbiBulanan2) * 100;

foreach ($dataAfd as $listAfd) {
	$posisiSbiBlnanAfd2[$listAfd] = ($jmKBlnanAwlThn2[$listAfd] + $jmKBSmpBlnini2[$listAfd]) - $jmKBPengSmpBlnIni2[$listAfd];
	@$turnOverBlnanAfd2[$listAfd] = ($posisiSbiBlnanAfd2[$listAfd] / $jmKBSmpBlnini2[$listAfd]) * 100;
}

$totBlnAwl3 = 0;
$totBlnKmrn3 = 0;
$totBlnPenBi3 = 0;
$totBlnPenSmpBi3 = 0;
$totBlnPeng3 = 0;
$totBlnPengSmpBi3 = 0;
$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4  and ' . $addTmb . ' ' . $whereAwal . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn3[$rStaff['subbagian']] = $rStaff['jumKary'];
	$totBlnAwl3 += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu2[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnKmrn3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4  and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni3[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenBi3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4  and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini3[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4  and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng3[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPeng3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4  and ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $addBagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni3[$rStaffBln['subbagian']] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi3 += $rStaffBln['jumKary'];
}

$sStaff = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereAwal . ' ' . $bagian . '';

#exit(mysql_error());
($qStaff = mysql_query($sStaff)) || true;

while ($rStaff = mysql_fetch_assoc($qStaff)) {
	$jmKBlnanAwlThn3[$kdUnit] = $rStaff['jumKary'];
	$totBlnAwl3 += $rStaff['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnKmrn . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnLalu3[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnKmrn3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnIniPnmbah . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBlnIni3[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenBi3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnIniPnmbahSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBSmpBlnini3[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPenSmpBi3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnIniPeng . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBpeng3[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPeng3 += $rStaffBln['jumKary'];
}

$sStaffBln = 'select distinct count(karyawanid) as jumKary,subbagian from ' . $dbname . '.datakaryawan where tipekaryawan=4 and ' . $addTmb . ' ' . $whereBlnIniPengSi . ' ' . $bagian . '';

#exit(mysql_error());
($qStaffBln = mysql_query($sStaffBln)) || true;
$rStaffBln = mysql_fetch_assoc($qStaffBln);

while ($rStaffBln = mysql_fetch_assoc($qStaffBln)) {
	$jmKBPengSmpBlnIni3[$kdUnit] = $rStaffBln['jumKary'];
	$totBlnPengSmpBi3 += $rStaffBln['jumKary'];
}

$posisiSbiBlnanAfd3[$kdUnit] = ($jmKBlnanAwlThn3[$kdUnit] + $jmKBSmpBlnini3[$kdUnit]) - $jmKBPengSmpBlnIni3[$kdUnit];
@$turnOverBlnanAfd3[$kdUnit] = ($posisiSbiBlnanAfd3[$kdUnit] / $jmKBSmpBlnini3[$kdUnit]) * 100;
$posisiSbiBulanan3 = ($totBlnAwl3 + $totBlnPenSmpBi3) - $totBlnPengSmpBi3;
@$turnOverBlnan3 = ($totBlnPengSmpBi3 / $posisiSbiBulanan3) * 100;

foreach ($dataAfd as $listAfd) {
	$posisiSbiBlnanAfd3[$listAfd] = ($jmKBlnanAwlThn3[$listAfd] + $jmKBSmpBlnini3[$listAfd]) - $jmKBPengSmpBlnIni3[$listAfd];
	@$turnOverBlnanAfd3[$listAfd] = ($posisiSbiBlnanAfd3[$listAfd] / $jmKBSmpBlnini3[$listAfd]) * 100;
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=8 align=left><b> 04.3 ' . $_SESSION['lang']['ket_mutasi'] . '  </b></td><td colspan=3 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=8 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3 colspan=3>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2 colspan=2>' . $_SESSION['lang']['jumlahkaryawan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2 colspan=2>' . $_SESSION['lang']['penambahan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2 colspan=2>' . $_SESSION['lang']['pengurang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3>TURN OVER  (%)</td>';
$tab .= '<tr>';
$tab .= '<tr class=rowcontent><td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tahunlalu'] . '(Dec)</td><td  ' . $bgcoloraja . '>' . $_SESSION['lang']['bulanlalu'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td  ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td><td  ' . $bgcoloraja . '>' . $_SESSION['lang']['bi'] . '</td><td  ' . $bgcoloraja . '>' . $_SESSION['lang']['sbi'] . '</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$tab .= '<tr class=rowcontent><td colspan=3>I. Staff</td>';
$tab .= '<td align=right>' . $jumKaryStaff[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jumKaryBlnLalu[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jumKaryBlnIni[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jumKarySmpBlnIni[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jumKaryPengBlnini[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jumKaryPengSmpBlnini[$kdUnit] . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiStaff, 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOver, 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3>II. BULANAN</td>';
$tab .= '<td align=right>' . $totBlnAwl . '</td>';
$tab .= '<td align=right>' . $totBlnKmrn . '</td>';
$tab .= '<td align=right>' . $totBlnPenBi . '</td>';
$tab .= '<td align=right>' . $totBlnPenSmpBi . '</td>';
$tab .= '<td align=right>' . $totBlnPeng . '</td>';
$tab .= '<td align=right>' . $totBlnPengSmpBi . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBulanan, 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnan, 0) . '</td>';
$tab .= '</tr>';
$awal = 0;

foreach ($dataAfd as $listAfd) {
	if ($awal == 0) {
		$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>2.1. ' . $_SESSION['lang']['afdeling'] . '</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}
	else {
		$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}

	$jmKBlnanAwlThn[$listAfd] == '' ? $jmKBlnanAwlThn[$listAfd] = 0 : $jmKBlnanAwlThn[$listAfd] = $jmKBlnanAwlThn[$listAfd];
	$jmKBlnLalu[$listAfd] == '' ? $jmKBlnLalu[$listAfd] = 0 : $jmKBlnLalu[$listAfd] = $jmKBlnLalu[$listAfd];
	$jmKBlnIni[$listAfd] == '' ? $jmKBlnIni[$listAfd] = 0 : $jmKBlnIni[$listAfd] = $jmKBlnIni[$listAfd];
	$jmKBSmpBlnini[$listAfd] == '' ? $jmKBSmpBlnini[$listAfd] = 0 : $jmKBSmpBlnini[$listAfd] = $jmKBSmpBlnini[$listAfd];
	$jmKBpeng[$listAfd] == '' ? $jmKBpeng[$listAfd] = 0 : $jmKBpeng[$listAfd] = $jmKBpeng[$listAfd];
	$jmKBPengSmpBlnIni[$listAfd] == '' ? $jmKBPengSmpBlnIni[$listAfd] = 0 : $jmKBPengSmpBlnIni[$listAfd] = $jmKBPengSmpBlnIni[$listAfd];
	$tab .= '<td align=right>' . $jmKBlnanAwlThn[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnLalu[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnIni[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBSmpBlnini[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBpeng[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBPengSmpBlnIni[$listAfd] . '</td>';
	$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($turnOverBlnanAfd[$listAfd], 0) . '</td>';
	++$awal;
}

$jmKBlnanAwlThn[$kdUnit] == '' ? $jmKBlnanAwlThn[$kdUnit] = 0 : $jmKBlnanAwlThn[$kdUnit] = $jmKBlnanAwlThn[$kdUnit];
$jmKBlnLalu[$kdUnit] == '' ? $jmKBlnLalu[$kdUnit] = 0 : $jmKBlnLalu[$kdUnit] = $jmKBlnLalu[$kdUnit];
$jmKBlnIni[$kdUnit] == '' ? $jmKBlnIni[$kdUnit] = 0 : $jmKBlnIni[$kdUnit] = $jmKBlnIni[$kdUnit];
$jmKBSmpBlnini[$kdUnit] == '' ? $jmKBSmpBlnini[$kdUnit] = 0 : $jmKBSmpBlnini[$kdUnit] = $jmKBSmpBlnini[$kdUnit];
$jmKBpeng[$kdUnit] == '' ? $jmKBpeng[$kdUnit] = 0 : $jmKBpeng[$kdUnit] = $jmKBpeng[$kdUnit];
$jmKBPengSmpBlnIni[$kdUnit] == '' ? $jmKBPengSmpBlnIni[$kdUnit] = 0 : $jmKBPengSmpBlnIni[$kdUnit] = $jmKBPengSmpBlnIni[$kdUnit];
$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
$tab .= '<td>Kantor</td>';
$tab .= '<td align=right>' . $jmKBlnanAwlThn[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnLalu[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnIni[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBSmpBlnini[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBpeng[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBPengSmpBlnIni[$kdUnit] . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd[$kdUnit], 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnanAfd[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3>III. KHT</td>';
$tab .= '<td align=right>' . $totBlnAwl2 . '</td>';
$tab .= '<td align=right>' . $totBlnKmrn2 . '</td>';
$tab .= '<td align=right>' . $totBlnPenBi2 . '</td>';
$tab .= '<td align=right>' . $totBlnPenSmpBi2 . '</td>';
$tab .= '<td align=right>' . $totBlnPeng2 . '</td>';
$tab .= '<td align=right>' . $totBlnPengSmpBi2 . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBulanan2, 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnan2, 0) . '</td>';
$tab .= '</tr>';
$awal = 0;

foreach ($dataAfd as $listAfd) {
	if ($awal == 0) {
		$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>3.1. ' . $_SESSION['lang']['afdeling'] . '</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}
	else {
		$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}

	$jmKBlnanAwlThn2[$listAfd] == '' ? $jmKBlnanAwlThn2[$listAfd] = 0 : $jmKBlnanAwlThn2[$listAfd] = $jmKBlnanAwlThn2[$listAfd];
	$jmKBlnLalu2[$listAfd] == '' ? $jmKBlnLalu2[$listAfd] = 0 : $jmKBlnLalu2[$listAfd] = $jmKBlnLalu2[$listAfd];
	$jmKBlnIni2[$listAfd] == '' ? $jmKBlnIni2[$listAfd] = 0 : $jmKBlnIni2[$listAfd] = $jmKBlnIni2[$listAfd];
	$jmKBSmpBlnini2[$listAfd] == '' ? $jmKBSmpBlnini2[$listAfd] = 0 : $jmKBSmpBlnini2[$listAfd] = $jmKBSmpBlnini2[$listAfd];
	$jmKBpeng2[$listAfd] == '' ? $jmKBpeng2[$listAfd] = 0 : $jmKBpeng2[$listAfd] = $jmKBpeng2[$listAfd];
	$jmKBPengSmpBlnIni2[$listAfd] == '' ? $jmKBPengSmpBlnIni2[$listAfd] = 0 : $jmKBPengSmpBlnIni2[$listAfd] = $jmKBPengSmpBlnIni2[$listAfd];
	$tab .= '<td align=right>' . $jmKBlnanAwlThn2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnLalu2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnIni2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBSmpBlnini2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBpeng2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBPengSmpBlnIni2[$listAfd] . '</td>';
	$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd2[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($turnOverBlnanAfd2[$listAfd], 0) . '</td>';
	++$awal;
}

$jmKBlnanAwlThn2[$kdUnit] == '' ? $jmKBlnanAwlThn2[$kdUnit] = 0 : $jmKBlnanAwlThn2[$kdUnit] = $jmKBlnanAwlThn2[$kdUnit];
$jmKBlnLalu2[$kdUnit] == '' ? $jmKBlnLalu2[$kdUnit] = 0 : $jmKBlnLalu2[$kdUnit] = $jmKBlnLalu2[$kdUnit];
$jmKBlnIni2[$kdUnit] == '' ? $jmKBlnIni2[$kdUnit] = 0 : $jmKBlnIni2[$kdUnit] = $jmKBlnIni2[$kdUnit];
$jmKBSmpBlnini2[$kdUnit] == '' ? $jmKBSmpBlnini2[$kdUnit] = 0 : $jmKBSmpBlnini2[$kdUnit] = $jmKBSmpBlnini2[$kdUnit];
$jmKBpeng2[$kdUnit] == '' ? $jmKBpeng2[$kdUnit] = 0 : $jmKBpeng2[$listAfd] = $jmKBpeng2[$kdUnit];
$jmKBPengSmpBlnIni2[$kdUnit] == '' ? $jmKBPengSmpBlnIni2[$kdUnit] = 0 : $jmKBPengSmpBlnIni2[$kdUnit] = $jmKBPengSmpBlnIni2[$kdUnit];
$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
$tab .= '<td>Kantor</td>';
$tab .= '<td align=right>' . $jmKBlnanAwlThn2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnLalu2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnIni2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBSmpBlnini2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBpeng2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBPengSmpBlnIni2[$kdUnit] . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd2[$kdUnit], 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnanAfd2[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent><td colspan=3>IV. KHL</td>';
$tab .= '<td align=right>' . $totBlnAwl3 . '</td>';
$tab .= '<td align=right>' . $totBlnKmrn3 . '</td>';
$tab .= '<td align=right>' . $totBlnPenBi3 . '</td>';
$tab .= '<td align=right>' . $totBlnPenSmpBi3 . '</td>';
$tab .= '<td align=right>' . $totBlnPeng3 . '</td>';
$tab .= '<td align=right>' . $totBlnPengSmpBi3 . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBulanan3, 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnan3, 0) . '</td>';
$tab .= '</tr>';
$awal = 0;

foreach ($dataAfd as $listAfd) {
	if ($awal == 0) {
		$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>4.1. ' . $_SESSION['lang']['afdeling'] . '</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}
	else {
		$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
		$tab .= '<td>' . $listAfd . '</td>';
	}

	$jmKBlnanAwlThn3[$listAfd] == '' ? $jmKBlnanAwlThn3[$listAfd] = 0 : $jmKBlnanAwlThn3[$listAfd] = $jmKBlnanAwlThn3[$listAfd];
	$jmKBlnLalu3[$listAfd] == '' ? $jmKBlnLalu3[$listAfd] = 0 : $jmKBlnLalu3[$listAfd] = $jmKBlnLalu3[$listAfd];
	$jmKBlnIni3[$listAfd] == '' ? $jmKBlnIni3[$listAfd] = 0 : $jmKBlnIni3[$listAfd] = $jmKBlnIni3[$listAfd];
	$jmKBSmpBlnini3[$listAfd] == '' ? $jmKBSmpBlnini3[$listAfd] = 0 : $jmKBSmpBlnini3[$listAfd] = $jmKBSmpBlnini3[$listAfd];
	$jmKBpeng3[$listAfd] == '' ? $jmKBpeng3[$listAfd] = 0 : $jmKBpeng3[$listAfd] = $jmKBpeng3[$listAfd];
	$jmKBPengSmpBlnIni3[$listAfd] == '' ? $jmKBPengSmpBlnIni3[$listAfd] = 0 : $jmKBPengSmpBlnIni3[$listAfd] = $jmKBPengSmpBlnIni3[$listAfd];
	$tab .= '<td align=right>' . $jmKBlnanAwlThn3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnLalu3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBlnIni3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBSmpBlnini3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBpeng3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . $jmKBPengSmpBlnIni3[$listAfd] . '</td>';
	$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd3[$listAfd], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($turnOverBlnanAfd3[$listAfd], 0) . '</td>';
	++$awal;
}

$jmKBlnanAwlThn3[$kdUnit] == '' ? $jmKBlnanAwlThn3[$kdUnit] = 0 : $jmKBlnanAwlThn3[$kdUnit] = $jmKBlnanAwlThn3[$kdUnit];
$jmKBlnLalu3[$kdUnit] == '' ? $jmKBlnLalu3[$kdUnit] = 0 : $jmKBlnLalu3[$kdUnit] = $jmKBlnLalu3[$kdUnit];
$jmKBlnIni3[$kdUnit] == '' ? $jmKBlnIni3[$kdUnit] = 0 : $jmKBlnIni3[$kdUnit] = $jmKBlnIni3[$kdUnit];
$jmKBSmpBlnini3[$kdUnit] == '' ? $jmKBSmpBlnini3[$kdUnit] = 0 : $jmKBSmpBlnini3[$kdUnit] = $jmKBSmpBlnini3[$kdUnit];
$jmKBpeng3[$kdUnit] == '' ? $jmKBpeng3[$kdUnit] = 0 : $jmKBpeng3[$listAfd] = $jmKBpeng3[$kdUnit];
$jmKBPengSmpBlnIni3[$kdUnit] == '' ? $jmKBPengSmpBlnIni3[$kdUnit] = 0 : $jmKBPengSmpBlnIni3[$kdUnit] = $jmKBPengSmpBlnIni3[$kdUnit];
$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;</td>';
$tab .= '<td>Kantor</td>';
$tab .= '<td align=right>' . $jmKBlnanAwlThn3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnLalu3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBlnIni3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBSmpBlnini3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBpeng3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . $jmKBPengSmpBlnIni3[$kdUnit] . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiBlnanAfd3[$kdUnit], 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverBlnanAfd3[$kdUnit], 0) . '</td>';
$tab .= '</tr>';
$totSmaAwlThn = $jumKaryStaff[$kdUnit] + $totBlnAwl + $totBlnAwl2 + $totBlnAwl3;
$totSmaBlnKmrn = $jumKaryBlnLalu[$kdUnit] + $totBlnKmrn + $totBlnKmrn2 + $totBlnKmrn3;
$totBlnIni = $totBlnPenBi[$kdUnit] + $totBlnPenBi + $totBlnPenBi2 + $totBlnPenBi3;
$totSmpBlnIni = $jumKarySmpBlnIni[$kdUnit] + $totBlnPenSmpBi + $totBlnPenSmpBi2 + $totBlnPenSmpBi3;
$totPengSma = $jumKaryPengBlnini[$kdUnit] + $totBlnPeng + $totBlnPeng2 + $totBlnPeng3;
$totPengSmaBlnini = $jumKaryPengSmpBlnini[$kdUnit] + $totBlnPengSmpBi + $totBlnPengSmpBi2 + $totBlnPengSmpBi3;
$posisiSbiTotal = ($totSmaAwlThn + $totSmpBlnIni) - $totPengSmaBlnini;
@$turnOverTotal = ($totPengSmaBlnini / $posisiSbiTotal) * 100;
$tab .= '<tr class=rowcontent><td colspan=3>' . $_SESSION['lang']['total'] . '  (I + II + III + IV) </td>';
$tab .= '<td align=right>' . $totSmaAwlThn . '</td>';
$tab .= '<td align=right>' . $totSmaBlnKmrn . '</td>';
$tab .= '<td align=right>' . $totBlnIni . '</td>';
$tab .= '<td align=right>' . $totSmpBlnIni . '</td>';
$tab .= '<td align=right>' . $totPengSma . '</td>';
$tab .= '<td align=right>' . $totPengSmaBlnini . '</td>';
$tab .= '<td align=right>' . number_format($posisiSbiTotal, 0) . '</td>';
$tab .= '<td align=right>' . number_format($turnOverTotal, 0) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'mutasi_karyawan_' . $dte;
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
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('04.3 ' . $_SESSION['lang']['ket_mutasi']), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$height = 60;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(120, $height, $_SESSION['lang']['uraian'], 1, 0, 'C', 1);
			$this->Cell(180, 30, strtoupper($_SESSION['lang']['jumlahkaryawan']), 1, 0, 'C', 1);
			$this->Cell(90, 30, strtoupper($_SESSION['lang']['penambahan']), 1, 0, 'C', 1);
			$this->Cell(90, 30, strtoupper($_SESSION['lang']['pengurang']), 1, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(90, $height, strtoupper($_SESSION['lang']['sbi']), 1, 0, 'C', 1);
			$this->Cell(90, $height, 'TURN OVER (%)', 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 30);
			$this->SetX($ksamping - 540);
			$this->Cell(90, 30, strtoupper($_SESSION['lang']['tahunlalu']), 1, 0, 'C', 1);
			$this->Cell(90, 30, strtoupper($_SESSION['lang']['bulanlalu']), 1, 0, 'C', 1);
			$this->Cell(45, 30, strtoupper($_SESSION['lang']['bi']), 1, 0, 'C', 1);
			$this->Cell(45, 30, strtoupper($_SESSION['lang']['sbi']), 1, 0, 'C', 1);
			$this->Cell(45, 30, strtoupper($_SESSION['lang']['bi']), 1, 0, 'C', 1);
			$this->Cell(45, 30, strtoupper($_SESSION['lang']['sbi']), 1, 1, 'C', 1);
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
	$pdf->Cell(120, $height, 'I. Staff', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(90, $height, $jumKaryStaff[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(90, $height, $jumKaryBlnLalu[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $jumKaryBlnIni[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $jumKarySmpBlnIni[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $jumKaryPengBlnini[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $jumKaryPengSmpBlnini[$kdUnit], 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiStaff, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiStaff, 0), 1, 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(120, $height, 'II. BULANAN', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(90, $height, $totBlnAwl, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, $totBlnKmrn, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenBi, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenSmpBi, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPeng, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPengSmpBi, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiBulanan, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($turnOverBlnan, 0), 1, 1, 'R', 1);
	$awal = 0;

	foreach ($dataAfd as $listAfd) {
		if ($awal == 0) {
			$tab .= '<tr class=rowcontent><td>&nbsp;</td><td>2.1. ' . $_SESSION['lang']['afdeling'] . '</td>';
			$tab .= '<td>' . $listAfd . '</td>';
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(30, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(60, $height, '2.1. ' . $_SESSION['lang']['afdeling'], 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}
		else {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(90, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}

		$pdf->SetFont('Arial', '', 6);
		$pdf->Cell(90, $height, $jmKBlnanAwlThn[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, $jmKBlnLalu[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBlnIni[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBSmpBlnini[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBpeng[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBPengSmpBlnIni[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($posisiSbiBlnanAfd[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($turnOverBlnanAfd[$listAfd], 0), 1, 1, 'R', 1);
		++$awal;
	}

	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(120, $height, 'III. KHT', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(90, $height, $totBlnAwl2, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, $totBlnKmrn2, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenBi2, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenSmpBi2, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPeng2, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPengSmpBi2, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiBulanan2, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($turnOverBlnan2, 0), 1, 1, 'R', 1);
	$awal = 0;

	foreach ($dataAfd as $listAfd) {
		if ($awal == 0) {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(30, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(60, $height, '3.1. ' . $_SESSION['lang']['afdeling'], 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}
		else {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(90, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}

		$pdf->SetFont('Arial', '', 6);
		$pdf->Cell(90, $height, $jmKBlnanAwlThn2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, $jmKBlnLalu2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBlnIni2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBSmpBlnini2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBpeng2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBPengSmpBlnIni2[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($posisiSbiBlnanAfd2[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($turnOverBlnanAfd2[$listAfd], 0), 1, 1, 'R', 1);
		++$awal;
	}

	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(120, $height, 'IV. KHL', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(90, $height, $totBlnAwl3, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, $totBlnKmrn3, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenBi3, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPenSmpBi3, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPeng3, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnPengSmpBi3, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiBulanan3, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($turnOverBlnan3, 0), 1, 1, 'R', 1);
	$awal = 0;

	foreach ($dataAfd as $listAfd) {
		if ($awal == 0) {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(30, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(60, $height, '3.1. ' . $_SESSION['lang']['afdeling'], 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}
		else {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(90, $height, ' ', 1, 0, 'L', 1);
			$pdf->Cell(30, $height, $listAfd, 1, 0, 'L', 1);
		}

		$pdf->SetFont('Arial', '', 6);
		$pdf->Cell(90, $height, $jmKBlnanAwlThn3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, $jmKBlnLalu3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBlnIni3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBSmpBlnini3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBpeng3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(45, $height, $jmKBPengSmpBlnIni3[$listAfd], 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($posisiSbiBlnanAfd3[$listAfd], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, $height, number_format($turnOverBlnanAfd3[$listAfd], 0), 1, 1, 'R', 1);
		++$awal;
	}

	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(120, $height, $_SESSION['lang']['total'] . '  (I + II + III + IV)', 1, 0, 'L', 1);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(90, $height, $totSmaAwlThn, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, $totSmaBlnKmrn, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totBlnIni, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totSmpBlnIni, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totPengSma, 1, 0, 'R', 1);
	$pdf->Cell(45, $height, $totPengSmaBlnini, 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($posisiSbiTotal, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, $height, number_format($turnOverTotal, 0), 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
