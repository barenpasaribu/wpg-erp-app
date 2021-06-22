<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$arr = '##unit##periode##afdId';
$optunit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\' ' . "\r\n" . '    and tipe in (\'KEBUN\',\'PABRIK\') order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optunit .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$sOrg = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optperiode .= '<option value=' . $rOrg['periode'] . '>' . $rOrg['periode'] . '</option>';
}

$optafd = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
echo "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['periode'] . '</label></td><td><select id=\'periode\' style="width:200px;">' . $optperiode . '</select></td></tr>' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['unit'] . '</label></td><td><select id=\'unit\' style="width:200px;" onchange=getAfd(this)>' . $optunit . '</select></td></tr>' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['afdeling'] . '</label></td><td><select id=\'afdId\' style="width:200px;">' . $optafd . '</select></td></tr>' . "\r\n" . '    <tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '    <tr><td colspan="2">' . "\r\n" . '    <button onclick="zPdf(\'lbm_slave_sampul\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="pdf" id="pdf">' . $_SESSION['lang']['pdf'] . '</button>' . "\r\n" . '<!--    <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">' . $_SESSION['lang']['cancel'] . '</button>--></td></tr>' . "\r\n" . '</table>' . "\r\n";

?>
