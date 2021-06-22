<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\' and tipe=\'KEBUN\' order by kodeorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThn = 'select distinct  tahunbudget from ' . $dbname . '.bgt_budget order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optThn .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$arr = '##thnBudget##kdUnit##modPil';
$optModel = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$arrModel = array(0 => $_SESSION['lang']['tahuntanam'] . '/' . $_SESSION['lang']['afdeling'], 1 => $_SESSION['lang']['detail'], 3 => $_SESSION['lang']['tahuntanam'], 4 => $_SESSION['lang']['blok'] . '/' . $_SESSION['lang']['sebaran']);

foreach ($arrModel as $listModel => $dtModel) {
	$optModel .= '<option value=\'' . $listModel . '\'>' . $dtModel . '</option>';
}

echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['rProdKebun'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id=\'thnBudget\' style="width:150px;">';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id=\'kdUnit\'  style="width:150px;">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['list'];
echo ' By.</label></td><td><select id=\'modPil\'  style="width:150px;">';
echo $optModel;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_laporan_produksi\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '<button onclick="zPdf(\'bgt_slave_laporan_produksi\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'bgt_slave_laporan_produksi.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
