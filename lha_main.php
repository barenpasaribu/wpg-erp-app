<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if (($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
	$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'KEBUN\') order by namaorganisasi asc ';
}
else {
	$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\' and induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by kodeorganisasi asc';
}

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$arr = '##kdOrg##kdAfd##tgl1##tgl2';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=\'js/lha.js\'></script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['lha'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kebun'];
echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getAfd()"><option value=""></option>';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['afdeling'];
echo '</label></td><td><select id="kdAfd" name="kdAfd" style="width:150px"><option value=""></option></select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['tanggal'];
echo '</label></td><td>' . "\r\n" . '<input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;" maxlength="10" style="width:60px;" />' . "\r\n" . '<input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" maxlength="10" style="width:60px;" title="Leave it empty to provide one day report"/>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2">' . "\r\n" . '    <button onclick="zPreview(\'lha_slave_print\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="html">Preview</button>' . "\r\n" . '    <button onclick="zExcel(event,\'lha_slave_print.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="excel" id="excel">Excel</button>' . "\r\n" . '    <button onclick="zPdf(\'lha_slave_print\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="pdf" id="pdf">';
echo $_SESSION['lang']['pdf'];
echo '</button>' . "\r\n" . '    <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both;\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto; height:50%; max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
