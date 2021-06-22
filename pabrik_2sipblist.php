<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode2 = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sPeriode2 = 'select distinct left(tanggal,7) as periode from '.$dbname.'.pabrik_timbangan order by left(tanggal,7) desc';
$qPeriode2 = mysql_query($sPeriode2);
while ($rPeriode2 = mysql_fetch_assoc($qPeriode2)) {
    $optPeriode2 .= "<option value='".$rPeriode2['periode']."'>".$rPeriode2['periode'].'</option>';
}
$optBrg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optBrg2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sBrg = "select distinct b.namabarang,a.kodebarang from ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where a.kodebarang!='' and nosipb!='' order by namabarang asc";
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';
    $optBrg2 .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';
}
$arr = '##periode##kdBrg';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n \r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['sipblist'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode2;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['namabarang'];
echo '</label></td><td><select id="kdBrg" name="kdBrg" style="width:150px">';
echo $optBrg;
echo "</select></td></tr>\r\n<tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('pabrik_slave_2sipblist','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('pmn_slave_laporanPemenuhanKontrak','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n        <button onclick=\"zExcel(event,'pabrik_slave_2sipblist.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>