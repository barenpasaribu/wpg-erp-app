<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script>\r\ndtAll='##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable';\r\n</script>\r\n";
$optd = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optLksi = $optd;
$sLokasi = 'select id,lokasi from '.$dbname.'.setup_remotetimbangan order by lokasi asc';
$qLokasi = mysql_query($sLokasi);
while ($rLokasi = mysql_fetch_assoc($qLokasi)) {
    $optLksi .= '<option value='.$rLokasi['id'].'>'.$rLokasi['lokasi'].'</option>';
}
$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$arr = '##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable';
$tbl = ['msvendorbuyer', 'msvendortrp', 'mssipb'];
foreach ($tbl as $lsttbl) {
    $optd .= "<option value='".$lsttbl."'>".$lsttbl.'</option>';
}
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script language=javascript src='js/pabrik_3uploadDataVendor.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['uploadDataVendor'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['lokasi'];
echo "</label></td><td>\r\n<select id=\"lksiServer\" name=\"lksiServer\" style=\"width:150px\" onchange=\"getDt()\">\r\n";
echo $optLksi;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['nmTabel'];
echo "</label></td><td>\r\n<select id=\"nmTable\" name=\"nmTable\" style=\"width:150px\" disabled>";
echo $optd;
echo "</select></td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_3uploadDataVendor','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"unLockForm()\" class=\"mybutton\" name=\"cancel\" id=\"cancel\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n<input type=\"hidden\" name=\"dbnm\" id=\"dbnm\" />\r\n<input type=\"hidden\" name=\"prt\" id=\"prt\" />\r\n<input type=\"hidden\" name=\"pswrd\" id=\"pswrd\" />\r\n<input type=\"hidden\" name=\"ipAdd\" id=\"ipAdd\" />\r\n<input type=\"hidden\" name=\"usrName\" id=\"usrName\" />\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>