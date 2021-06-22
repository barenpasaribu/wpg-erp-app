<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi \r\n       where tipe in ('kebun','pabrik') and length(kodeorganisasi)=4\r\n       order by namaorganisasi desc";
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arr = '##kodeorg##tgl1##tgl2';
echo "<script language=javascript src='js/zMaster.js'></script> \r\n<script language=javascript src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script languange=javascript1.2 src='js/formReport.js'></script>\r\n<script languange=javascript1.2 src='js/zGrid.js'></script>\r\n\r\n<script language=javascript src='js/kebun_pemakaian_vs_cu.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['pakaibarang'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['kodeorg'];
echo '</label></td><td><select id="kodeorg" name="kdOrg" style="width:150px">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /> s.d.\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n    ";
echo "<button onclick=\"zPrevi('kebun_slave_pemakaian_vs_cu','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n          <button onclick=\"zExcel(event,'kebun_slave_pemakaian_vs_cu.php','".$arr."','printContainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    \r\n          <button onclick=\"zPdf('kebun_slave_pemakaian_vs_cu','".$arr."','printContainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">".$_SESSION['lang']['pdf'].'</button>';
echo "    </td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<fieldset><legend>Periode <span id=tgl_1></span> s/d <span id=tgl_2></span></legend>\r\n         <div id='printContainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n     </fieldset>";
CLOSE_BOX();
close_body();

?>