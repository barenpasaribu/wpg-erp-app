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
$arrRe = '##thnKontrak##kdKomoditi';
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select distinct kodebarang,namabarang from '.$dbname.".log_5masterbarang where left(kodebarang,1)='4'  order by namabarang asc";
$qOrg = mysql_query($sOrg);
while ($rData = mysql_fetch_assoc($qOrg)) {
    $optPeriode .= '<option value='.$rData['kodebarang'].'>'.$rData['namabarang'].'</option>';
}
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n      <div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['daftarkontrak'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['tahunkontrak'];
echo "</label></td><td><input type=\"text\" class=\"myinputtextnumber\" id=\"thnKontrak\" onkeypress='return angka_doang(event)' style=\"width:170px\" /></td></tr>        \r\n<tr><td><label>";
echo $_SESSION['lang']['komoditi'];
echo '</label></td><td><select id="kdKomoditi" name="kdKomoditi"  style="width:170px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2daftarkontrak','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('pabrik_slave_2loses','";
echo $arrRe;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n        <button onclick=\"zExcel(event,'pabrik_slave_2daftarkontrak.php','";
echo $arrRe;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n             \r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>