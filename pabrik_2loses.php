<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='KEBUN'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$intex = ['External', 'Internal', 'Afiliasi'];
$optTbs = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optTbsRe = "<option value='3'>".$_SESSION['lang']['all'].'</option>';
foreach ($intex as $dt => $rw) {
    $optTbs .= '<option value='.$dt.'>'.$rw.'</option>';
    $optTbsRe .= '<option value='.$dt.'>'.$rw.'</option>';
}
$arrRe = '##kdPabrik##tgl1##tgl2';
$optPabrik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK'";
$qOrg2 = mysql_query($sOrg2);
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optPabrik .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$sOrg = 'select distinct kodeorg from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by kodeorg";
$qOrg = mysql_query($sOrg);
$optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$unitintimbangan = '(';
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optUnit .= '<option value='.$rData['kodeorg'].'>'.$rData['kodeorg'].'</option>';
    $unitintimbangan .= "'".$rData['kodeorg']."',";
}
$unitintimbangan = substr($unitintimbangan, 0, -1);
$unitintimbangan .= ')';
$sOrg = 'select kodeorganisasi from '.$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan.' order by kodeorganisasi';
$qOrg = mysql_query($sOrg);
$optAfdeling2 = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optAfdeling2 .= '<option value='.$rData['kodeorganisasi'].'>'.$rData['kodeorganisasi'].'</option>';
}
$sOrg = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by periode desc";
$qOrg = mysql_query($sOrg);
$optPeriode = "<option value=''></option>";
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optPeriode .= '<option value='.$rData['periode'].'>'.$rData['periode'].'</option>';
}
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n      <div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>CPO & Kernel Loses</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pabrik'];
echo '</label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:170px">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" />\r\n        s.d. <input type=\"text\" class=\"myinputtext\" id=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" />\r\n</td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2loses','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('pabrik_slave_2loses','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n        <button onclick=\"zExcel(event,'pabrik_slave_2loses.php','";
echo $arrRe;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n             \r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>