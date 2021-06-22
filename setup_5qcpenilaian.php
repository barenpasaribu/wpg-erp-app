<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/setup_5qcpenilaian.js\'></script>' . "\r\n";
$optTipe = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sTipe = 'select distinct * from ' . $dbname . '.qc_5parameter where tipe!=\'PUPUK\' order by id asc';

#exit(mysql_error($conn));
($qTipe = mysql_query($sTipe)) || true;

while ($rTipe = mysql_fetch_assoc($qTipe)) {
	$optTipe .= '<option value=\'' . $rTipe['id'] . '\'>' . $rTipe['tipe'] . '-' . $rTipe['nama'] . '(' . $rTipe['satuan'] . ')</option>';
}

$frm[0] = '';
$frm[1] = '';
$arr = '##tipeDt##maxData##nilData##method';
$arr2 = '##kdData##nmData##nilData2##method2##maxData2';
include 'master_mainMenu.php';
OPEN_BOX();
$frm .= 0;
$frm .= 0;
echo '<script>loadData()</script>';
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['qcnilai'];
$hfrm[1] = $_SESSION['lang']['qcnilaipupuk'];
drawTab('FRM', $hfrm, $frm, 100, 700);
CLOSE_BOX();
echo close_body();

?>
