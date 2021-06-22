<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n" . '<script language=javascript1.2 src=\'js/log_packing.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n\r\n\r\n\r\n\r\n";
$optPt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$aPt = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi  where tipe=\'PT\' ';

#exit(mysql_error($conn));
($bPt = mysql_query($aPt)) || true;

while ($cPt = mysql_fetch_assoc($bPt)) {
	$optPt .= '<option value=\'' . $cPt['kodeorganisasi'] . '\'>' . $cPt['namaorganisasi'] . '</option>';
}

$optPtSch = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$aPt = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi  where tipe=\'PT\' ';

#exit(mysql_error($conn));
($bPt = mysql_query($aPt)) || true;

while ($cPt = mysql_fetch_assoc($bPt)) {
	$optPtSch .= '<option value=\'' . $cPt['kodeorganisasi'] . '\'>' . $cPt['namaorganisasi'] . '</option>';
}

$optKar = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
	$aKar = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan  where bagian=\'HO_PROC\' and lokasitugas like \'%HO%\' ';
}
else {
	$aKar = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan  where bagian=\'HO_PROC\' and lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' ';
}

#exit(mysql_error($conn));
($bKar = mysql_query($aKar)) || true;

while ($cKar = mysql_fetch_assoc($bKar)) {
	$optKar .= '<option value=\'' . $cKar['karyawanid'] . '\'>' . $cKar['namakaryawan'] . '</option>';
}

$optPer = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$i = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_packinght order by periode desc limit 10';

#exit(mysql_error($conn));
($j = mysql_query($i)) || true;

while ($k = mysql_fetch_assoc($j)) {
	$optPer .= '<option value=\'' . $k['periode'] . '\'>' . $k['periode'] . '</option>';
}

$optMandor = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optAstn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optKadiv = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$in = 'PL' . date('YmdHis');
echo "\r\n\r\n";
$frm[0] = '';
$frm[1] = '';
OPEN_BOX('', '<b>PACKING LIST</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$tmbl = '<tr>' . "\r\n\t\t\t" . '<td>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopo'] . ' : ' . "\r\n\t\t\t\t" . ' <img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\'  class=resicon onclick=cariNoPo(\'' . $_SESSION['lang']['find'] . '\',event)>' . "\r\n\t\t\t" . '</td>' . "\r\n\t\t" . '  </tr>';
$tmbl .= '<tr>' . "\r\n\t\t\t" . '<td>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['kodebarang'] . ' : ' . "\r\n\t\t\t\t" . ' <img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\'  class=resicon onclick=inputBarang(\'' . $_SESSION['lang']['find'] . '\',event)>' . "\r\n\t\t\t" . '</td>' . "\r\n\t\t" . '  </tr>';
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 250, 1150);
CLOSE_BOX();
echo close_body();

?>
