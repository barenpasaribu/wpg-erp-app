<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."'";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$optTipe = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTipe = 'select id,tipe from '.$dbname.'.sdm_5tipekaryawan order by tipe asc';
$qTipe = mysql_query($sTipe);
while ($rTipe = mysql_fetch_assoc($qTipe)) {
    $optTipe .= '<option value='.$rTipe['id'].'>'.$rTipe['tipe'].'</option>';
}
$arrsgaj = getEnum($dbname, 'datakaryawan', 'sistemgaji');
foreach ($arrsgaj as $kei => $fal) {
    $optGaji .= "<option value='".$kei."'>".$fal.'</option>';
}
$arr = '##kdOrg##periode##tgl1##tgl2##tipeKary##sistemGaji';
$arrThn = '##kdeOrg2##periodThn##periodThnSmp##sistemGaji3##tipeKary2';

$sOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optOrg = "<select id=kdOrg name=kdOrg onchange=getPeriode() style='width:150px;' >".$sOrg ."</sekect>";
$optOrg2 = "<select id=kdeOrg name=kdeOrg onchange=getKry() style='width:150px;' >".$sOrg ."</sekect>";
$optOrg3 = "<select id=kdeOrg2 name=kdeOrg2 onchange=getPeriodeGaji5() style='width:150px;' >".$sOrg ."</sekect>";

$arrKry = '##kdeOrg##period##idKry##tgl_1##tgl_2';
$optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/sdm_2rekapabsen.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['rkpAbsen'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['lokasitugas'];
echo '</label></td><td>';
echo $optOrg;
echo "</td></tr>\r\n";
if ('KANWIL' == $_SESSION['empl']['tipelokasitugas'] || 'HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    echo '<tr><td><label>Sub ';
    echo $_SESSION['lang']['lokasitugas'];
    echo "</label></td><td><select id='afdId' style=\"width:150px;\">";
    echo $optAfd;
    echo "</select></td></tr>\r\n";
}

echo '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo '</label></td><td><select id="sistemGaji" name="sistemGaji" style="width:150px" onchange="getPeriodeGaji()">';
echo $optGaji;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tipekaryawan'];
echo '</label></td><td><select id="tipeKary" name="tipeKary" style="width:150px">';
echo $optTipe;
echo "</select></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    echo "<div>\r\n    <fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['rkpAbsen'];
    echo ' Per ';
    echo $_SESSION['lang']['tahun'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td>';
    echo $optOrg3;
    echo "</td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['dari'].' '.$_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periodThn" name="periodThn" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['sampai'].' '.$_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periodThnSmp" name="periodThnSmp" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['sistemgaji'];
    echo '</label></td><td><select id="sistemGaji3" name="sistemGaji3" style="width:150px">';
    echo $optGaji;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tipeKary2" name="tipeKary2" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr><td><label>Min Kehadiran</label></td><td><input type=\"text\" class=\"myinputtextnumber\" maxlength=\"2\" id=\"nilaiMax\" style=\"width:150px\" onkeypress=\"return angka_doang(event)\" /></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n\r\n<tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('sdm_slave_2daftarhadir','";
    echo $arrThn;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2daftarhadir.php','";
    echo $arrThn;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n      ";
}

if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
    echo "<div >\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['rkpAbsen'];
    echo " Per Karyawan</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td>';
    echo $optOrg2;
    echo "</td></tr>\r\n\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="period" name="period" style="width:150px" onchange="getTgl2()">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['sistemgaji'];
    echo '</label></td><td><select id="sistemGaji2" name="sistemGaji2" style="width:150px" onchange="getPeriodeGaji2()">';
    echo $optGaji;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tanggalmulai'];
    echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" name=\"tgl_1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tanggalsampai'];
    echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" name=\"tgl_2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['namakaryawan'];
    echo '</label></td><td><select id="idKry" name="idKry" style="width:150px"><option value="">';
    echo $_SESSION['lang']['pilihdata'];
    echo "</option></select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen_kary','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen_kary','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen_kary.php','";
    echo $arrKry;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear2()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
    echo $_SESSION['lang']['cancel'];
    echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n      <div >\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['rkpAbsen'];
    echo ' Per ';
    echo $_SESSION['lang']['tahun'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['lokasitugas'];
    echo '</label></td><td>';
    echo $optOrg3;
    echo "</td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['dari'].' '.$_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periodThn" name="periodThn" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['sampai'].' '.$_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periodThnSmp" name="periodThnSmp" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['sistemgaji'];
    echo '</label></td><td><select id="sistemGaji3" name="sistemGaji3" style="width:150px">';
    echo $optGaji;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tipeKary2" name="tipeKary2" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen_thn','";
    echo $arrThn;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen_thn','";
    echo $arrThn;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen_thn.php','";
    echo $arrThn;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear3()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
    echo $_SESSION['lang']['cancel'];
    echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
}

echo "<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>