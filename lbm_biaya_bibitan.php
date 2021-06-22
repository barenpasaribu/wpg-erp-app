<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$optNMorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arr = '##kdUnit##periode';
$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optPeriode = $optUnit;
$sUnit = 'select distinct left(kodeorganisasi,4) as kodeorganisasi from '.$dbname.".organisasi where  tipe='BIBITAN' order by namaorganisasi asc";
$qUnit = mysql_query($sUnit);
while ($rUnit = mysql_fetch_assoc($qUnit)) {
    $optUnit .= "<option value='".$rUnit['kodeorganisasi']."'>".$optNMorg[$rUnit['kodeorganisasi']].'</option>';
}
$sPeriode = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
}
echo "\r\n<fieldset style=\"float: left;\">\r\n<legend><b>".$_POST['judul']."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['unit'].'</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">'.$optUnit."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['periode'].'</label></td><td><select id="periode" name="periode" style="width:150px">'.$optPeriode."</select></td></tr>\r\n\r\n\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n<button onclick=\"zPreview('lbm_slave_biaya_bibitan','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n<button onclick=\"zPdf('lbm_slave_biaya_bibitan','".$arr."','reportcontainer')\" class=\"mybutton\">PDF</button>    \r\n<button onclick=\"zExcel(event,'lbm_slave_biaya_bibitan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>";

?>