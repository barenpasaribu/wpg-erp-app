<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$x = 0;

while ($x <= 24) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optPeriode .= '<option value=' . date('Y-m', $dt) . '>' . date('Y-m', $dt) . '</option>';
	++$x;
}

$sKbn = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'KEBUN\',\'PABRIK\',\'KANWIL\',\'TRAKSI\')';

#exit(mysql_error());
($qKbn = mysql_query($sKbn)) || true;

while ($rKbn = mysql_fetch_assoc($qKbn)) {
	$optKbn .= '<option value=' . $rKbn['kodeorganisasi'] . '>' . $rKbn['namaorganisasi'] . '</option>';
}

$sJnsvhc = 'select jenisvhc,namajenisvhc from ' . $dbname . '.vhc_5jenisvhc order by namajenisvhc asc';

#exit(mysql_error());
($qJnsVhc = mysql_query($sJnsvhc)) || true;

while ($rJnsvhc = mysql_fetch_assoc($qJnsVhc)) {
	$optJns .= '<option value=' . $rJnsvhc['jenisvhc'] . '>' . $rJnsvhc['namajenisvhc'] . '</option>';
}

$arrklvhc = getEnum($dbname, 'vhc_5master', 'kelompokvhc');

foreach ($arrklvhc as $kei => $fal) {
	switch ($kei) {
	case 'AB':
		$_SESSION['language'] != 'EN' ? $fal = 'Alat Berat' : $fal = 'Heavy Equipment';
		break;

	case 'KD':
		$_SESSION['language'] != 'EN' ? $fal = 'Kendaraan' : $fal = 'Vehicle';
		break;

	case 'MS':
		$_SESSION['language'] != 'EN' ? $fal = 'Mesin' : $fal = 'Machinery';
		break;
	}

	$optklvhc .= open_body() . $fal . '</option>';
}

$arr = '##kdKbn##klpmkVhc';    
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['laporanKendAb'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdKbn" name="kdKbn" style="width:150px">' . "\r\n" . '<option value="0">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optKbn;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kodekelompok'];
echo '</label></td><td><select id="klpmkVhc" name="klpmkVhc" style="width:150px">' . "\r\n" . '<option value="0">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optklvhc;
echo '</select></td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'3slave_daftarKendaran\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'3slave_daftarKendaran\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'3slave_daftarKendaran.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
