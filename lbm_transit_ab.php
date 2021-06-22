<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$arr = '##kdUnit##periode';
$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPeriode = $optUnit;
$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where  tipe=\'TRAKSI\' order by namaorganisasi asc';

#exit(mysql_error());
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $rUnit['namaorganisasi'] . '</option>';
}

$sPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';

#exit(mysql_error());
($qPeriode = mysql_query($sPeriode)) || true;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
	$optPeriode .= '<option value=\'' . $rPeriode['periode'] . '\'>' . $rPeriode['periode'] . '</option>';
}

echo "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>' . $_POST['judul'] . '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['unit'] . '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">' . $optUnit . '</select></td></tr>' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['periode'] . '</label></td><td><select id="periode" name="periode" style="width:150px">' . $optPeriode . '</select></td></tr>' . "\r\n\r\n\r\n\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2">' . "\r\n" . '<button onclick="zPreview(\'lbm_slave_transit_ab\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '<button onclick="zPdf(\'lbm_slave_transit_ab\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton">PDF</button>    ' . "\r\n" . '<button onclick="zExcel(event,\'lbm_slave_transit_ab.php\',\'' . $arr . '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>';

?>
