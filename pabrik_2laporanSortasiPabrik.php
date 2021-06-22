<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$sPbk = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK'";
$qPbk = mysql_query($sPbk);
while ($rPbk = mysql_fetch_assoc($qPbk)) {
    $optPabrik .= '<option value='.$rPbk['kodeorganisasi'].'>'.$rPbk['namaorganisasi'].'</option>';
}
$arrOptIntex = ['External', 'Afliasi', 'Internal'];
foreach ($arrOptIntex as $isi => $tks) {
    $optBuah .= '<option value='.$isi.' >'.$tks.'</option>';
}
$arr = '##tglAwal##tglAkhir##statBuah##kdPbrk##suppId##kdOrg';
echo "<script>\r\noptInt=\"<option value=''>";
echo $_SESSION['lang']['all'];
echo "</option>\";\r\noptExt=\"<option value=''>";
echo $_SESSION['lang']['all'];
echo "</option>\";\r\n</script>\r\n\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n\r\n<script language=javascript src='js/pabrik_2laporanSortasiPabrik.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanSortasi'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['kdpabrik'];
echo '</label></td><td><select id="kdPbrk" name="kdPbrk" style="width:150px;"  ><option value="">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['statusBuah'];
echo '</label></td><td><select id="statBuah" name="statBuah" style="width:150px;" onchange="getKbn()" ><option value="5">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optBuah;
echo "</select></td></tr>\r\n<tr> \t \r\n\t\t\t<td style='valign:top'>";
echo $_SESSION['lang']['kebun'];
echo " </td><td>\r\n\t\t\t<select id=\"kdOrg\" name=\"kdOrg\"  style=\"width:150px;\"><option value=''>";
echo $_SESSION['lang']['all'];
echo "</option></select></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr> \t \r\n\t\t\t<td style='valign:top'>";
echo $_SESSION['lang']['namasupplier'];
echo " </td><td>\r\n\t\t\t<select id=\"suppId\" name=\"suppId\"  style=\"width:150px;\"><option value=''>";
echo $_SESSION['lang']['all'];
echo "</option></select></td>\r\n\t\t\t</tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['startdate'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tglAwal\" name=\"tglAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false; \" size=\"10\" maxlength=\"4\" style=\"width:150px;\" />\r\ns.d.</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tglAkhir\" name=\"tglAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false; \" size=\"10\" maxlength=\"4\" style=\"width:150px;\" /></td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_2laporanSortasiPabrik','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zExcel(event,'pabrik_slave_2laporanSortasiPabrik.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>