<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script languange=javascript1.2 src=\'js/zSearch.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formTable.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formReport.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/zGrid.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/pta.js\'></script>' . "\r\n" . '<script>' . "\r\n" . '    tolak="';
echo $_SESSION['lang']['ditolak'];
echo '";' . "\r\n" . '    ajukan="';
echo $_SESSION['lang']['diajukan'];
echo '";' . "\r\n" . '    setujuak="';
echo $_SESSION['lang']['setujuakhir'];
echo '";' . "\r\n" . '    </script>' . "\r\n" . '<link rel=stylesheet type=text/css href=\'style/zTable.css\'>' . "\r\n";
$pta = 'PTA' . $_SESSION['empl']['lokasitugas'] . date('Ymd');
$sCek = 'select distinct notransaksi from ' . $dbname . '.pta_ht where notransaksi=\'' . $pta . '\'';

#exit(mysql_error($conn));
($qCek = mysql_query($sCek)) || true;
$optAkun = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

if ($_SESSION['language'] == 'EN') {
	$dd = 'namaakun1 as namaakun';
}
else {
	$dd = 'namaakun as namaakun';
}

$sAkun = 'select distinct  noakun,' . $dd . ' from ' . $dbname . '.keu_5akun where detail=1 order by noakun asc';

#exit(mysql_error($conn));
($qAkun = mysql_query($sAkun)) || true;

while ($rAkun = mysql_fetch_assoc($qAkun)) {
	$optAkun .= '<option value=\'' . $rAkun['noakun'] . '\'>' . $rAkun['noakun'] . ' - ' . $rAkun['namaakun'] . '</option>';
}

$optKeg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optAlokasi = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sAlokasi = 'select  kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi like \'' . $_SESSION['empl']['lokasitugas'] . '%\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qAlokasi = mysql_query($sAlokasi)) || true;

while ($rAlokasi = mysql_fetch_assoc($qAlokasi)) {
	$optAlokasi .= '<option value=\'' . $rAlokasi['kodeorganisasi'] . '\'>' . $rAlokasi['kodeorganisasi'] . '-' . $rAlokasi['namaorganisasi'] . '</option>';
}

$optVhc = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sVhc = 'select distinct kodevhc from ' . $dbname . '.vhc_5master order by kodevhc';

#exit(mysql_error($conn));
($qVhc = mysql_query($sVhc)) || true;

while ($rVhc = mysql_fetch_assoc($qVhc)) {
	$optVhc .= '<option value=\'' . $rVhc['kodevhc'] . '\'>' . $rVhc['kodevhc'] . '</option>';
}

$frm[0] = '';
$frm[1] = '';
$frm .= 0;
$frm .= 0;
$optTipe = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$arrTipe = getEnum($dbname, 'pta_dt', 'tipepta');

foreach ($arrTipe as $tipe => $pta) {
	$optTipe .= '<option value=\'' . $tipe . '\'>' . $pta . '</option>';
}

$optTipe = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optTipe .= '<option value=\'KAPITAL\'>CAPITAL</option>';
$frm .= 0;
$optJn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$arrJn = getEnum($dbname, 'pta_dt', 'jenispta');

foreach ($arrJn as $jenis => $pta) {
	$optJn .= '<option value=\'' . $jenis . '\'>' . $pta . '</option>';
}

$optJn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optJn .= '<option value=\'MATERIAL\'>MATERIAL</option>';
$frm .= 0;
$sLoad = 'select * from ' . $dbname . '.pta_dt ' . "\r\n" . '        where unit=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

#exit(mysql_error());
($qLoad = mysql_query($sLoad)) || true;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$hfrm[0] = 'Buat PTA';
$hfrm[1] = 'Daftar PTA';
drawTab('FRM', $hfrm, $frm, 100, 1000);
echo "\r\n";
CLOSE_BOX();

?>
