<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/bgt_uploaderkebun.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$frm[0] = '';
$optUnit = '<option value=\'\'></option>';
$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '        where tipe=\'KEBUN\' and kodeorganisasi in (select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '        order by namaorganisasi asc';

#exit(mysql_error($conn));
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $rUnit['kodeorganisasi'] . '-' . $rUnit['namaorganisasi'] . '</option>';
}

$frm .= 0;
$frm .= 0;
$hfrm[0] = 'Upload';
drawTab('FRM', $hfrm, $frm, 200, 900);
CLOSE_BOX();
echo close_body();

?>
