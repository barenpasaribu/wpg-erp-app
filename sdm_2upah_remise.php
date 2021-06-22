<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optUnitId = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optUnitId .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arr = '##unitId##tglDari##tglSmp';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Payroll Remise I</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n    <tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="unitId" style="width:150px;" >';
echo $optUnitId;
echo "</select></td>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['dari'];
echo "</label></td><td>\r\n       <input type=text class=myinputtext id=tglDari name=tglDari onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"   maxlength=\"10\"  style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['tglcutisampai'];
echo "</label></td><td>\r\n       <input type=\"text\" class=\"myinputtext\" id=\"tglSmp\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px\" />  </td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2upah_remise','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zPdf('sdm_slave_2upah_remise','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2upah_remise.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>