<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/bgt_alokasi_supervisi.js"></script>' . "\r\n" . '<script>' . "\r\n" . 'dataKdvhc="';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n";
$optOrg2 = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg2 = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe in (\'AFDELING\',\'BIBITAN\') and induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by namaorganisasi asc';

#exit(mysql_error());
($qOrg2 = mysql_query($sOrg2)) || true;

while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
	$optOrg2 .= '<option value=' . $rOrg2['kodeorganisasi'] . '>' . $rOrg2['namaorganisasi'] . '</option>';
}

OPEN_BOX('', '<b>' . $_SESSION['lang']['alokasisupervisi'] . '</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$optThn = '<option value=\'\'>' . $_SESSION['lang']['budgetyear'] . '</option>';
$frm .= 2;
$frm .= 2;
$frm .= 2;
$frm .= 3;
$frm .= 3;
$frm .= 3;
$frm .= 3;
$hfrm[0] = $_SESSION['lang']['keterangan'];
$hfrm[1] = $_SESSION['lang']['hksupervisi'];
$hfrm[2] = $_SESSION['lang']['sebaran'];
$hfrm[3] = $_SESSION['lang']['ulang'];
drawTab('FRM', $hfrm, $frm, 100, 1100);
echo "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
