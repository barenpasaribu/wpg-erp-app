<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$frm[0] = '';
$frm[1] = '';
$x = 0;

while ($x <= 3) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optper .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	++$x;
}

$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi asc';

#exit(mysql_error());
($qOrg = mysql_query($sOrg));

while ($bar = mysql_fetch_object($qOrg)) {
	$optOrg .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

echo "\r\n" . '<script type="text/javascript" src="js/pmn_rencanajual.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script>' . "\r\n" . 'tmblSave=\'';
echo $_SESSION['lang']['save'];
echo '\';' . "\r\n" . 'tmblCancel=\'';
echo $_SESSION['lang']['cancel'];
echo '\';' . "\r\n" . 'tmblDone=\'';
echo $_SESSION['lang']['done'];
echo '\';' . "\r\n" . '</script>' . "\r\n";
OPEN_BOX('', '<b>' . $_SESSION['lang']['rencanaJual'] . '</b><br>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$optBrg = '';
$sBrg = 'select kodebarang,namabarang from ' . $dbname . '.log_5masterbarang where kelompokbarang like \'400%\' order by namabarang asc';

#exit(mysql_error());
($qBrg = mysql_query($sBrg));

while ($rBrg = mysql_fetch_assoc($qBrg)) {
	$optBrg .= '<option value=' . $rBrg['kodebarang'] . '>' . $rBrg['namabarang'] . '</option>';
}

$sCust = 'select kodecustomer,namacustomer from ' . $dbname . '.pmn_4customer';

#exit(mysql_error());
($qCust = mysql_query($sCust));

while ($rCust = mysql_fetch_assoc($qCust)) {
	$optCust .= '<option value=' . $rCust['kodecustomer'] . '>' . $rCust['namacustomer'] . '</option>';
}

$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['header'];
$hfrm[1] = $_SESSION['lang']['rencanaJualdetail'];
drawTab('FRM', $hfrm, $frm, 250, 800);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
