<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."'";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.'.organisasi order by namaorganisasi asc ';
} else {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}

$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arr = '##kdOrg##periode##tgl1##tgl2';
$arrKry = '##kdeOrg##period##idKry##tgl_1##tgl_2';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/sdm_2rekapabsen.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rkpAbsen'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdOrg" name="kdOrg" style="width:150px"><option value="">';
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><option value=""></option>';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div >\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rkpAbsen'];
echo " Per Karyawan</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdeOrg" name="kdeOrg" style="width:150px" onchange="getKry()"><option value=""></option>';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['namakaryawan'];
echo "</label></td><td><select id=\"idKry\" name=\"idKry\" style=\"width:150px\"><option value=\"\"></option></select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()"><option value=""></option>';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" name=\"tgl_1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" name=\"tgl_2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen.php','";
echo $arrKry;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear2()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>