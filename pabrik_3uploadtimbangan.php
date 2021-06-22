<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script>\r\ndtAll='##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';\r\n</script>\r\n";
for ($x = 0; $x <= 24; $x++) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 3);
if ($lokasiTugas=='SPS'){
	$lokasiTugas ='Trial';
}
$sLokasi = "select id,lokasi from ".$dbname.".setup_remotetimbangan where lokasi ='".$lokasiTugas."' order by id asc";
$qLokasi = mysql_query($sLokasi);
while ($rLokasi = mysql_fetch_assoc($qLokasi)) {
    $optLksi .= '<option value='.$rLokasi['id'].'>'.$rLokasi['lokasi'].'</option>';
}
$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 3);
$sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kodebarang like '%40000%'";
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= ''.$rBrg['namabarang'].'<br />';
}
$arr = '##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/pabrik_3uploadtimbangan.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['uploadTimbangan'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['lokasi'];
echo "</label></td><td>:</td><td>\r\n<select id=\"lksiServer\" name=\"lksiServer\" style=\"width:150px\" onchange=\"getDt()\"><option value=\"\"></option>\r\n";
echo $optLksi;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td><td>:</td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"period\" name=\"period\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n<!--<select id=\"period\" name=\"period\" style=\"width:150px\"></select>--></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['komoditi'];
echo '</label></td><td>:</td><td>';
echo $optBrg;
echo "<!--<select id=\"kdBrg\" name=\"kdBrg\" style=\"width:150px\" >\r\n";
echo $optBrg;
echo "--></select></td></tr>\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_3uploadtimbangan','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"unLockForm()\" class=\"mybutton\" name=\"cancel\" id=\"cancel\">";
echo $_SESSION['lang']['cancel'];
echo "</button><!--<button onclick=\"zPdf('kebun_slave_2pengangkutan','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'kebun_slave_2pengangkutan.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>--></td></tr>\r\n</table>\r\n<input type=\"hidden\" name=\"dbnm\" id=\"dbnm\" />\r\n<input type=\"hidden\" name=\"prt\" id=\"prt\" />\r\n<input type=\"hidden\" name=\"pswrd\" id=\"pswrd\" />\r\n<input type=\"hidden\" name=\"ipAdd\" id=\"ipAdd\" />\r\n<input type=\"hidden\" name=\"usrName\" id=\"usrName\" />\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>