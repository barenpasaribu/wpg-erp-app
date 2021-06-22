<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    $whrdt = "tipe='KEBUN'";
    $optDt = makeOption($dbname, 'organisasi', 'induk,kodeorganisasi', $whrdt);
    $kdLok = $optDt[$_SESSION['org']['kodeorganisasi']];
    $optRegion = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
    $regData = $optRegion[$kdLok];
    $sOrg = 'select distinct namaorganisasi,kodeorganisasi from '.$dbname.".organisasi \r\n           where (kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$regData."') or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."')";
} else {
    $sOrg = 'select distinct namaorganisasi,kodeorganisasi from '.$dbname.".organisasi \r\n           where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}

$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sSup = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$qSup = mysql_query($sSup);
while ($rSup = mysql_fetch_assoc($qSup)) {
    $optPeriode .= '<option value='.$rSup['periode'].'>'.$rSup['periode'].'</option>';
}
$arr = '##periode##kdUnit';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['aruskaslangsung'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" >';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('keu_slave_2laporankaslangsung','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'keu_slave_2laporankaslangsung.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>