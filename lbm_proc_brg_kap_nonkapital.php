<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$arr = '##periode##judul##kdPt##regDt##smbrData';
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
echo "\r\n" . '    ' . "\r\n";
$optRegional .= '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sRegion = 'select distinct regional from ' . $dbname . '.bgt_regional where regional not in (\'DKI\',\'LAMPUNG\') order by regional asc';

#exit(mysql_error($conn));
($qRegion = mysql_query($sRegion)) || true;

while ($rRegion = mysql_fetch_assoc($qRegion)) {
	$optRegional .= '<option value=\'' . $rRegion['regional'] . '\'>' . $rRegion['regional'] . '</option>';
}

$arrTipe = array(1 => 'Kapital', 2 => 'Non Kapital');
$optPt = $optTipe = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

foreach ($arrTipe as $lstTipe => $dtTipe) {
	$optTipe .= '<option value=\'' . $lstTipe . '\'>' . $dtTipe . '</option>';
}

$optperiode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optperiode .= '<option value=' . $rOrg['periode'] . '>' . $rOrg['periode'] . '</option>';
}

$arrSmbr = array(3 => 'Default', 2 => 'Semua PO yang Di buat');

foreach ($arrSmbr as $lstSmbr => $dtSmbr) {
	$optSmbr .= '<option value=\'' . $lstSmbr . '\'>' . $dtSmbr . '</option>';
}

$derk = 1;
echo "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td colspan=4>' . $judul . '</td></tr>' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['periode'] . '</label></td><td><select id=\'periode\' style="width:150px;">' . $optperiode . '</select></td></tr><tr>';
echo '<td><label>' . $_SESSION['lang']['regional'] . '</label></td><td><select id=\'regDt\' style="width:150px;" onchange=getDtPt()>' . $optRegional . '</select></td></tr>' . "\r\n" . '    ';
echo '<tr><td><label>' . $_SESSION['lang']['pt'] . '</label></td><td><select id=\'kdPt\' style="width:150px;">' . $optPt . '</select></td></tr>';
echo '<tr>';
echo '<td><label>' . $_SESSION['lang']['data'] . '</label></td><td><select id=\'smbrData\' style="width:150px;">' . $optSmbr . '</select></td></tr>' . "\r\n" . '    ';
echo '<tr><td colspan="2"><input type=hidden id=judul name=judul value=\'' . $judul . '\'></td></tr>' . "\r\n" . '    <tr><td colspan="4">' . "\r\n" . '    <button onclick="zPreview(\'lbm_slave_proc_brg_kap_nonkapital\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '    <button onclick="zExcel(event,\'lbm_slave_proc_brg_kap_nonkapital.php\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>    ' . "\r\n" . '   <!--<button onclick="zPdf(\'lbm_slave_proc_brg_kap_nonkapital\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="pdf" id="pdf">' . $_SESSION['lang']['pdf'] . '</button>' . "\r\n" . '    <button onclick="batal()" class="mybutton" name="btnBatal" id="btnBatal">' . $_SESSION['lang']['cancel'] . '</button>--></td></tr>' . "\r\n" . '</table>' . "\r\n";

?>
