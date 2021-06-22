<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\' order by kodeorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$arr = '##kdUnit';
echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n\r\n\r\n" . '<script language=javascript>' . "\r\n\r\n" . 'function Clear1() {' . "\r\n\t\t" . 'document.getElementById(\'kdUnit\').value=\'\';' . "\t\r\n\t\t" . 'document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n\r\n\r\n" . '</script>' . "\r\n\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['lapPersonel'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id=\'kdUnit\'>';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_laporan_tenaga_kerja\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['preview'];
echo '</button><button onclick="zPdf(\'bgt_slave_laporan_tenaga_kerja\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['pdf'];
echo '</button><button onclick="zExcel(event,\'bgt_slave_laporan_tenaga_kerja.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['excel'];
echo '</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>';
echo $_SESSION['lang']['printArea'];
echo '</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n";
echo '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
