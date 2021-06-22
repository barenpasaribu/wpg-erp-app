<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
//echo '<script>\r\n'.
//'var \r\n'.
//'</script>';
echo '<script language=javascript1.2 src=\'js/log_laporan.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['laporanstok']) . '</b>');
OPEN_BOX('', '<b>LAPORAN PERSEDIAAN FISIK</b>');
$whr = 'namaorganisasi!=\'\'';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whr);
$str = 'select distinct periode from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '      order by periode desc';

function callbackPeriod($option,$value,$caption){
	$ret = array("newvalue"=>"","newcaption"=>"");
	if($option=='init'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=$caption;
	}
	if($option=='noninit'){
		$ret["newvalue"]=$value;
		$ret["newcaption"]=substr($value, 5, 2) . '-' . substr($value, 0, 4);
	}
	return $ret;
}
$optper = makeOption2($str,
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['sekarang'] ),
	array("valuefield"=>'periode',"captionfield"=> 'periode' ),
'callbackPeriod'
	);

$str="SELECT 1 as level,d.karyawanid, d.namakaryawan, ".
"o.kodeorganisasi,o.namaorganisasi,o.induk ".
"FROM datakaryawan d ".
"INNER JOIN user u on u.karyawanid=d.karyawanid ".
"INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi ".
"WHERE u.namauser= '" .$_SESSION['standard']['username'] ."'";
$optpt= makeOption2($str,
	array(),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optGdng= makeOption2('',
	//array("valueinit"=>'gudang',"captioninit"=> $_SESSION['lang']['all']." "),
	array("valueinit"=>'',"captioninit"=>"--"),
	array( )
);

//if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
//	$str = " SELECT " .
//		"o.kodeorganisasi,o.namaorganisasi,o.induk " .
//		"FROM  organisasi o    " .
//		"WHERE o.induk = '".$_SESSION['org']['induk']."'";
//} else {
//	$str = "SELECT 1 as level,d.karyawanid, d.namakaryawan, " .
//		"o.kodeorganisasi,o.namaorganisasi,o.induk " .
//		"FROM datakaryawan d " .
//		"INNER JOIN user u on u.karyawanid=d.karyawanid " .
//		"INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
//		"WHERE u.namauser= '" . $_SESSION['standard']['username'] . "'";
//}
$optUnit= makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optUnit2=$optUnit;

$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$x = 0;

while ($x < 13) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optPeriode .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	$optPeriode2 .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	++$x;
}

echo '<br />'.
//     '<fieldset style=width:250px;float:left;>' . "\r\n" .
//	 '<legend>' . $_SESSION['lang']['laporanstok'] . ' Per ' . $_SESSION['lang']['pt'] . '</legend>' . "\r\n" .
//	'<table border=0 cellpadding=1 cellspacing=1>' . "\r\n" .
//	'<tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['pt'] . '</td>'.
//	'<td>'.
//	'<select id=pt style=\'width:150px;\' onchange=hideById(\'printPanel\')>' . $optpt . '</select>'.
//	'</td></tr>'.
//	'<tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['periode'] . '</td>'.
//	'<td><select id=periode onchange=hideById(\'printPanel\')>' . $optper . '</select></td></tr>'.
//	'<tr><td>&nbsp;</td><td > <button class=mybutton onclick=getLaporanFisik()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table>'.
//	'</fieldset>'.
	'<fieldset style=width:250px;float:left;>'.
	'<legend>'  . $_SESSION['lang']['laporanstok'] .' '. $_SESSION['lang']['pt'] .   ' Per ' . $_SESSION['lang']['sloc'] . '</legend>'.
	'<table cellpadding=1 cellspacing=1 border=0>'.
	'<tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['unit'] . '</td>'.
	'<td><select id=unitDt style=\'width:150px;\' onchange=getGudangDt()>' . $optUnit . '</select></td></tr>'.
	'<tr><td>' . $_SESSION['lang']['sloc'] . '</td>'.
	'<td><select id=gudang2 style=\'width:150px;\' onchange=hideById(\'printPanel2\')>' . $optGdng . '</select></td></tr>'.
	'<tr><td>' . $_SESSION['lang']['periode'] . '</td>'.
	'<td><select id=periode2 onchange=hideById(\'printPanel2\')>' . $optPeriode . '</select></td></tr>'.
	'<tr><td>&nbsp;</td><td > <button class=mybutton onclick=getLaporanFisik2()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table> </fieldset>'.
	'<fieldset style=width:250px;>'.
	'<legend>'  . $_SESSION['lang']['laporanstok'] .' '. $_SESSION['lang']['pt'] .  ' Per ' . $_SESSION['lang']['unit'] . '</legend>'.
	'<table cellpadding=1 cellspacing=1 border=0>'.
	'<tr><td> '.$_SESSION['lang']['unit'].' </td>'.
	'<td><select id=unitDt2 style=\'width:150px;\' onchange=hideById(\'printPanel3\')>' . $optUnit2 . '</select></td></tr>'.
	'<tr><td>' . $_SESSION['lang']['periode'] . '</td>'.
	'<td><select id=periode3 onchange=hideById(\'printPanel3\')>' . $optPeriode2 . '</select></td></tr>'.
	'<tr><td>&nbsp;</td><td > <button class=mybutton onclick=getLaporanFisik3()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table> </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel(event,\'log_laporanPersediaanFisik_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=fisikKePDF(event,\'log_laporanPersediaanFisik_pdf.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>   ' . "\r\n" . '         <span id=printPanel2 style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel2(event,\'log_slaveLaporanPersediaanFisikUnit.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=fisikKePDF2(event,\'log_slaveLaporanPersediaanFisikUnit.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>' . "\r\n" . '         <span id=printPanel3 style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel3(event,\'log_slaveLaporanPersediaanFisikUnit2.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'>' . "\r\n\t" . ' <img onclick=fisikKePDF3(event,\'log_slaveLaporanPersediaanFisikUnit2.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>' . "\r\n\t" . ' <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '       <table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td align=center>No.</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n\t\t\t" . '</tr>  ' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\t\t" . ' ' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();
?>
