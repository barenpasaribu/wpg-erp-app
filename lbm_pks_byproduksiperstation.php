<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$arr = '##kdOrg##periode##judul';
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPeriode = $optOrg;
$sOrg = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where  tipe=\'PABRIK\' order by namaorganisasi asc';

#exit(mysql_error());
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=\'' . $rOrg['kodeorganisasi'] . '\'>' . $rOrg['namaorganisasi'] . '</option>';
}

$sPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';

#exit(mysql_error());
($qPeriode = mysql_query($sPeriode)) || true;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
	$optPeriode .= '<option value=\'' . $rPeriode['periode'] . '\'>' . $rPeriode['periode'] . '</option>';
}

echo "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>' . $_POST['judul'] . '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['organisasi'] . '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px">' . $optOrg . '</select></td></tr>' . "\r\n" . '<tr><td><label>' . $_SESSION['lang']['periode'] . '</label></td><td><select id="periode" name="periode" style="width:150px">' . $optPeriode . '</select></td></tr>' . "\r\n\r\n\r\n\r\n" . '<tr height="20"><td colspan="2"><input type=hidden id=judul name=judul value=\'' . $_POST['judul'] . '\'></td></tr>' . "\r\n" . '<tr><td colspan="2">' . "\r\n" . '<button onclick="zPreview(\'lbm_slave_pks_byproduksiperstation\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '<button onclick="zExcel(event,\'lbm_slave_pks_byproduksiperstation.php\',\'' . $arr . '\',\'excel\')" class="mybutton" name="preview" id="preview">Excel</button>' . "\r\n" . '<button onclick="zPdf(\'lbm_slave_pks_byproduksiperstation\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton">PDF</button></td></tr>    ' . "\r\n\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>';

?>
