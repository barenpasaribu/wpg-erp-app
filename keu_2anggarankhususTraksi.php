<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$sKdvhc = 'select kodevhc from '.$dbname.'.vhc_5master order by kodevhc desc';
$qKdvhc = mysql_query($sKdvhc);
while ($rKdvhc = mysql_fetch_assoc($qKdvhc)) {
    $optKdvhc .= '<option value='.$rKdvhc['kodevhc'].'>'.$rKdvhc['kodevhc'].'</option>';
}
$arr = '##thn##kdVhc';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Laporan Anggaran Traksi</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['tahun'];
echo '</label></td><td><input type="text" class="myinputtextnumber" value="';
echo date('Y');
echo "\" id=\"thn\" name=\"thn\" onkeypress=\"return angka_doang(event)\" style=\"width:150px\"  /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['kodevhc'];
echo '</label></td><td><select id="kdVhc" name="kdVhc" style="width:150px">';
echo $optKdvhc;
echo "</select></td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('keu_slave_2anggarankhususTraksi','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('keu_slave_2anggarankhususTraksi','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'keu_slave_2anggarankhususTraksi.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>