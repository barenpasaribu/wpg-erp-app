<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$opKlmpkBrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sKelompokBrg = 'select distinct substr(kodebarang,1,3) as kelompokBrg from ' . $dbname . '.log_po_vw order by kodebarang asc';

#exit(mysql_error());
($qKlmpkBrg = mysql_query($sKelompokBrg)) || true;

while ($rKlmplkBrg = mysql_fetch_assoc($qKlmpkBrg)) {
	$opKlmpkBrg .= '<option value=\'' . $rKlmplkBrg['kelompokBrg'] . '\'>' . $optKelompok[$rKlmplkBrg['kelompokBrg']] . '</option>';
}

$str='select distinct kodeorganisasi, namaorganisasi from '.$dbname.'.organisasi where kodeorganisasi=\''.$_SESSION['empl']['kodeorganisasi'].'\'';
$qListUnit=mysql_query($str);
while ($rListUnit = mysql_fetch_assoc($qListUnit)) {
	$optListUnit .= '<option value=\'' . $rListUnit['kodeorganisasi'] . '\'>' . $rListUnit['namaorganisasi'] . '</option>';
}
/*
$optListUnit=makeOption2(getQuery("pt"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
*/
$optPeriodeCari = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sPeriodeCari = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_prapoht order by substr(tanggal,1,7) desc';

#exit(mysql_error());
($qPeriodeCari = mysql_query($sPeriodeCari)) || true;

while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
	$optPeriodeCari .= '<option value=\'' . $rPeriodeCari['periode'] . '\'>' . $rPeriodeCari['periode'] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arrPo = array('Pusat', 'Lokal');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$optStatusPP = '<option value=\'2\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$stataPP = array('Belum PO', $_SESSION['lang']['sdhPO']);

foreach ($stataPP as $dataIni => $listNama) {
	$optStatusPP .= '<option value=\'' . $dataIni . '\'>' . $listNama . '</option>';
}

$optPur = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sPur = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan ' . "\r\n" . 'where (bagian=\'PUR\'or kodejabatan=\'17\') and kodejabatan!=\'5\' and (tanggalkeluar>\'' . date('Y-m-d') . '\' or tanggalkeluar is NULL)  order by namakaryawan asc';
$qPur = fetchData($sPur);

foreach ($qPur as $brsKary) {
	$optPur .= '<option value=' . $brsKary['karyawanid'] . '>' . $brsKary['namakaryawan'] . '</option>';
}

$arr = '##kdUnit##periode';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=js/log_2produktivitas.js></script>' . "\r\n\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['lapproduktivitaspur'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriodeCari;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['pt'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">';
echo $optListUnit;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_2slave_produktivitas\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'log_2slave_produktivitas.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
