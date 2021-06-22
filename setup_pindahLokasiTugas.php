<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=js/setup_gantiLokasiTugas.js></script>' . "\r\n";
include 'master_mainMenu.php';
$a = 'select * from ' . $dbname . '.setup_temp_lokasitugas where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

#exit(mysql_error($conn));
($b = mysql_query($a)) || true;
$c = mysql_fetch_assoc($b);
$lokasi = $c['kodeorg'];
$jum = count($lokasi);
$whereMake = 'kodeorganisasi=\'' . $lokasi . '\'';
$tipeLok = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe', $whereMake);
$whereRo = 'kodeorganisasi in (select kodeunit from ' . $dbname . '.bgt_regional_assignment ' . "\r\n\t\t\t" . 'where regional=\'' . $_SESSION['empl']['regional'] . '\' and right(kodeunit,2)=\'RO\')';
$whereUnit = 'kodeorganisasi in (select kodeunit from ' . $dbname . '.bgt_regional_assignment ' . "\r\n\t\t\t" . 'where regional=\'' . $_SESSION['empl']['regional'] . '\' and right(kodeunit,2)!=\'HO\')';
$whereUnitQ = 'kodeorganisasi in (select kodeunit from ' . $dbname . '.bgt_regional_assignment ' . "\r\n\t\t\t" . 'where regional=\'' . $_SESSION['empl']['regional'] . '\' and right(kodeunit,2)!=\'HO\' and right(kodeunit,1)!=\'M\' and right(kodeunit,2)!=\'RO\')';
$whereTemp = 'kodeorganisasi=\'' . $lokasi . '\'';
$whereHo = 'tipe=\'HOLDING\' and length(kodeorganisasi)=4 ';

/*
if (0 < $jum) {
	if ($tipeLok[$lokasi] == 'KANWIL') {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereUnit . ' ';
	}
	else {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where (' . $whereRo . ') or (kodeorganisasi=\'' . $lokasi . '\')';
	}
}
else if ($tipeLok[$lokasi] != '') {
	if (($tipeLok[$lokasi] == 'KEBUN') || ($tipeLok[$lokasi] == 'PABRIK')) {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereRo . ' ';
	}
	else if ($tipeLok[$lokasi] == 'KANWIL') {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereUnit . ' ';
	}
}
else {
	if (($_SESSION['empl']['tipelokasitugas'] == 'KEBUN') || ($_SESSION['empl']['tipelokasitugas'] == 'PABRIK')) {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereUnitQ . ' ';
	}
	else if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereUnit . ' ';
	}
}

if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
	$str = 'select kodeorganisasi,namaorganisasi,alokasi from ' . $dbname . '.organisasi where ' . $whereHo . ' ';
}

if ($_SESSION['empl']['bagian'] == 'HO_ITGS') {
*/
//	$str = "select kodeorganisasi,namaorganisasi,alokasi from organisasi where length(kodeorganisasi)=4 order by namaorganisasi desc";
	$str = "select kodeorganisasi,namaorganisasi,alokasi from organisasi where length(kodeorganisasi)=4 and left(kodeorganisasi,3)='"
	.substr($_SESSION['empl']['lokasitugas'],0,3)."' order by namaorganisasi desc";
//}

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->alokasi . '\'>' . $bar->kodeorganisasi . ' - ' . $bar->namaorganisasi . '</option>';
}

OPEN_BOX('', $_SESSION['lang']['pindahtugas']);
echo '<input type=hidden maxlength=4  id=lokasilama value=' . $_SESSION['empl']['lokasitugas'] . '>' . "\r\n" . '<br><br>Anda berada di: <b>' . $_SESSION['empl']['lokasitugas'] . '</b><br> ' . $_SESSION['lang']['tujuan'] . "\r\n" . '      <select id=tjbaru>' . $opt . '</select><br>' . "\r\n" . '      <!--input type="text" id="txtAutoComplete" list="tjbaru" size=50>' . "\r\n" . '      <datalist id="tjbaru">' . $opt . '</datalist-->' . "\r\n\t" . '  <button class=mybutton onclick=gantiLokasitugas()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '  ';
CLOSE_BOX();
echo close_body();

?>
