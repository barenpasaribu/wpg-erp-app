<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc ';
} else {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}

$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arr = '##kodeorg';
echo "\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n\r\n<link rel=stylesheet type='text/css' href='style/zTable.css'>\r\n\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['user'];
echo " e-Agro</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td>\r\n    <td><select id=\"kodeorg\" name=\"kodeorg\" style=\"width:150px\"><!--<option value=\"\">\r\n        ";
echo $_SESSION['lang']['all'];
echo '</option>-->';
echo $optOrg;
echo "    </select></td>\r\n</tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n    <button onclick=\"zPreview('sdm_slave_2userowl','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n<!--<button onclick=\"zPdf('sdm_slave_2prasarana','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">PDF</button>\r\n    <button onclick=\"zExcel(event,'sdm_slave_2prasarana.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">Excel</button>\r\n<button onclick=\"zExcel(event,'sdm_slave_2rekapabsen.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>-->\r\n<!--<button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button>-->\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>