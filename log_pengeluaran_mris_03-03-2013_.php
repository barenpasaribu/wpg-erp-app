<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$frm[0] = '';
$frm[1] = '';
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_pengeluaran_mris.js" /></script>' . "\r\n" . '<script>' . "\r\n" . ' pild=\'';
echo '<option value="">' . $_SESSION['lang']['pilihdata'] . '</option>';
echo '\';' . "\r\n" . '</script><br />' . "\r\n";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKbn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPrd = $optAfd = $optKbn;
$skbn = 'select distinct left(untukunit,4) as kodeorg from ' . $dbname . '.log_mrisht ' . "\r\n" . '       where left(untukunit,4) in (select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')';

#exit(mysql_error($conn));
($qkbn = mysql_query($skbn)) || true;

while ($rkbn = mysql_fetch_assoc($qkbn)) {
	$optKbn .= '<option value=\'' . $rkbn['kodeorg'] . '\'>' . $optNmOrg[$rkbn['kodeorg']] . '</option>';
}

$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
echo "\r\n\r\n";
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$optGdngCr .= '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sGdng = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '        where induk in (select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '        and tipe=\'GUDANG\'';

#exit(mysql_error($conn));
($qGng = mysql_query($sGdng)) || true;

while ($rGdng = mysql_fetch_assoc($qGng)) {
	$optGdngCr .= '<option value=\'' . $rGdng['kodeorganisas'] . '\'>' . $rGdng['namaorganisasi'] . '</option>';
}

$frm .= 1;
$hfrm[0] = $_SESSION['lang']['pengeluaranbarang'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 200, 1050);
CLOSE_BOX();
echo close_body();

?>
