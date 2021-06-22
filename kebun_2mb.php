<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
echo open_body();
require_once 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optB = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$i = 'select * from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING' ";
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    $optB .= "<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi'].'</option>';
}
$i = 'select distinct(substr(tanggal,1,7)) as periode from '.$dbname.".kebun_pakai_material_vw where kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' ";
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    $optPer .= "<option value='".$d['periode']."'>".$d['periode'].'</option>';
}
$optBr = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$i = 'select distinct(kodebarang) as kodebarang from '.$dbname.".kebun_pakai_material_vw where kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' ";
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    $optBr .= "<option value='".$d['kodebarang']."'>".$nmBrg[$d['kodebarang']].'</option>';
}
$optKeg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$i = 'select distinct(kodekegiatan) as kodekegiatan from '.$dbname.".kebun_pakai_material_vw where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by kodekegiatan asc ";
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    $optKeg .= "<option value='".$d['kodekegiatan']."'>".$d['kodekegiatan'].' - '.$nmKeg[$d['kodekegiatan']].'</option>';
}
$arr2 = '##kdorg##kdkeg##per##kdbarang';
echo "<fieldset style='float:left;'>\r\n\t\t<legend><b>Material Per Blok</b></legend>\r\n\t\t\t<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kdorg style=\"width:150px;\">".$optB."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kdkeg style=\"width:150px;\">".$optKeg."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=per style=\"width:150px;\">".$optPer."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kdbarang style=\"width:150px;\">".$optBr."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td colspan=4>\r\n\t\t\t\t\t<button onclick=zPreview('kebun_slave_2mb','".$arr2."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t\t\t\t<button onclick=zExcel(event,'kebun_slave_2mb.php','".$arr2."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</table>\r\n\t\t</fieldset>";
CLOSE_BOX();
echo "\r\n\r\n";
OPEN_BOX();
echo "\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:400px;max-width:1220px'; >\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>