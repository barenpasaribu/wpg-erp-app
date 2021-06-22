<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\'  order by kodeorganisasi';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$arr = '##kodeorg##periode';
$periode = '';
$strPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      order by periode desc';
$res = mysql_query($strPeriode);

while ($bar = mysql_fetch_object($res)) {
	$periode .= '<option value=\'' . $bar->periode . '\'>' . $bar->periode . '</option>';
}

echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo 'Biaya Langsung Kebun (Budget Vs Realisasi)';
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kebun'];
echo '</label></td><td><select id="kodeorg" name="kdOrg" style="width:150px">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $periode;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2">' . "\r\n" . '    <button onclick="zPreview(\'bgt_slave_2biayaLangsungKebun\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '    <button onclick="zPdf(\'bgt_slave_2biayaLangsungKebun\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button>' . "\r\n" . '    <button onclick="zExcel(event,\'bgt_slave_2biayaLangsungKebun.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both;\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto; height:50%; max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
