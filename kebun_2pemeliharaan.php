<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
//$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
//if ('HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN') order by namaorganisasi asc ";
//} else {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
//}
//
//$qOrg = mysql_query($sOrg) ;
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}
$optOrg= makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$sTah = 'select substr(tanggal,1,4) as tahun from '.$dbname.'.kebun_aktifitas group by substr(tanggal,1,4) order by tahun asc';
$qTah = mysql_query($sTah) ;
while ($rTah = mysql_fetch_assoc($qTah)) {
    $optTah .= '<option value='.$rTah['tahun'].'>'.$rTah['tahun'].'</option>';
}
$optKeg = '<option value="">'.$_SESSION['lang']['all'].'</option>';
if ('EN' === $_SESSION['language']) {
    $zz = 'namakegiatan1 as namakegiatan';
} else {
    $zz = 'namakegiatan';
}

$sKeg = 'select kodekegiatan, '.$zz.', kelompok from '.$dbname.'.setup_kegiatan order by kodekegiatan asc';
$qKeg = mysql_query($sKeg) ;
while ($rKeg = mysql_fetch_assoc($qKeg)) {
    $optKeg .= '<option value='.$rKeg['kodekegiatan'].'>'.$rKeg['kodekegiatan'].' - '.$rKeg['namakegiatan'].' ('.$rKeg['kelompok'].')</option>';
}
$arr = '##kdOrg##kdAfd##tgl1##tgl2##kegiatan##blok';
$arr1 = '##kdOrg1##kdAfd1##tahun1##kegiatan1##blok1';
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js?v=".mt_rand()."'></script>\r\n<script language=javascript src='js/kebun_2pemeliharaan.js?v=".mt_rand()."'></script>\r\n\r\n<link rel=stylesheet type='text/css' href='style/zTable.css'>\r\n";
$title[0] = $_SESSION['lang']['pemeltanaman'];
$title[1] = $_SESSION['lang']['rotasi'].' '.$_SESSION['lang']['pemeltanaman'];
$frm[0] .= "<fieldset style='float: left;'>\r\n<legend><b>".$title[0]."</b></legend>\r\n<table cellspacing='1' border='0' >\r\n<tr><td><label>".$_SESSION['lang']['kebun']."</label></td><td><select id='kdOrg' name='kdOrg' style='width:150px' onchange='getAfd()'> ".$optOrg."</select></td></tr>\r\n ".
"<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id='kdAfd' name='kdAfd' style='width:150px'  onchange='getBlok()'><option value=''>Seluruhnya</option></select></td></tr> ".
"<tr><td><label>Blok</label></td><td><select id='blok' name='blok' style='width:150px'><option value=''>Seluruhnya</option></select></td></tr> ".
"<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td>\r\n<input type='text' class='myinputtext' id='tgl1' name='tgl1' onmousemove='setCalendar(this.id);' onkeypress='return false;'  maxlength='10' style='width:60px;' /> s.d.\r\n<input type='text' class='myinputtext' id='tgl2' name='tgl2' onmousemove='setCalendar(this.id);' onkeypress='return false;'  maxlength='10' style='width:60px;' /></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['kegiatan']."</label></td><td><select id='kegiatan' name='kegiatan' style='width:150px'>".$optKeg."</select></td></tr>\r\n<tr height='20'><td colspan='2'>&nbsp;</td></tr>\r\n<tr><td colspan='2'>\r\n    <button onclick=\"zPreview('kebun_slave_2pemeliharaan','".$arr."','printContainer')\" class='mybutton' name='preview' id='preview'>Preview</button>\r\n    <button onclick=\"zExcel(event,'kebun_slave_2pemeliharaan.php','".$arr."')\" class='mybutton' name='preview' id='preview'>Excel</button>\r\n    <button onclick='Clear0()' class='mybutton' name='btnBatal' id='btnBatal'>".$_SESSION['lang']['cancel']."</button></td></tr>\r\n</table>\r\n</fieldset>\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto; height:50%; max-width:100%;'>\r\n\r\n</div></fieldset>";

$frm[1] .= "<fieldset style='float: left;'>\r\n<legend><b>".$title[1]."</b></legend>\r\n<table cellspacing='1' border='0' >\r\n<tr><td><label>".$_SESSION['lang']['kebun']."</label></td><td><select id='kdOrg1' name='kdOrg1' style='width:150px' onchange='getAfd1()'> ".$optOrg."</select></td></tr>\r\n ".
"<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id='kdAfd1' name='kdAfd1' style='width:150px'   onchange='getBlok()'><option value=''></option>".$optPeriode."</select></td></tr> ".
    "<tr><td><label>Blok</label></td><td><select id='blok1' name='blok1' style='width:150px'><option value=''>Seluruhnya</option></select></td></tr> ".
"<tr><td><label>".$_SESSION['lang']['tahun']."</label></td><td><select id='tahun1' name='tahun1' style='width:150px'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optTah."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['kegiatan']."</label></td><td><select id='kegiatan1' name='kegiatan1' style='width:150px'>".$optKeg."</select></td></tr>\r\n<tr height='20'><td colspan='2'>&nbsp;</td></tr>\r\n<tr><td colspan='2'>\r\n    <button onclick=\"zPreview('kebun_slave_2pemeliharaan1','".$arr1."','printContainer1')\" class='mybutton' name='preview' id='preview'>Preview</button>\r\n    <button onclick=\"zExcel(event,'kebun_slave_2pemeliharaan1.php','".$arr1."')\" class='mybutton' name='preview' id='preview'>Excel</button>\r\n    <button onclick='Clear1()' class='mybutton' name='btnBatal' id='btnBatal'>".$_SESSION['lang']['cancel']."</button></td></tr>\r\n</table>\r\n</fieldset>\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer1' style='overflow:auto; height:50%; max-width:100%;'>\r\n\r\n</div></fieldset>";
list($hfrm[0], $hfrm[1]) = $title;
drawTab('FRM', $hfrm, $frm, 200, 1220);
CLOSE_BOX();
echo close_body();

?>
