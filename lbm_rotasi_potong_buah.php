<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$arr = '##kdUnit##periode##judul##afdId';
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPeriode = $optUnit;
$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '    where CHAR_LENGTH(kodeorganisasi)=\'4\' and tipe=\'KEBUN\' order by namaorganisasi asc';

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

$optafd = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
echo "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>' . $_POST['judul'] . '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n\r\n" . '<tr><td><label>' . $_SESSION['lang']['periode'] . '</label></td><td><select id="periode" name="periode" style="width:150px">' . $optPeriode . '</select></td></tr>' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['unit'] . '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"  onchange=getAfd(this)>' . $optUnit . '</select></td></tr>' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['afdeling'] . '</label></td><td><select id=\'afdId\' style="width:150px;">' . $optafd . '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2"><input type=hidden id=judul name=judul value=\'' . $judul . '\'></td></tr>' . "\r\n" . '<tr><td colspan="2">' . "\r\n" . '<button onclick="zPreview(\'lbm_slave_rotasi_potong_buah\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '<button onclick="zPdf(\'lbm_slave_rotasi_potong_buah\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton">PDF</button>    ' . "\r\n" . '<button onclick="zExcel(event,\'lbm_slave_rotasi_potong_buah.php\',\'' . $arr . '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>';

?>
