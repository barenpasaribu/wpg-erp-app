<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$optTipe = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTipe = 'select id,tipe from '.$dbname.".sdm_5tipekaryawan where id NOT IN ('2','5','6') order by tipe asc";
$qTipe = mysql_query($sTipe);
while ($rTipe = mysql_fetch_assoc($qTipe)) {
    $optTipe .= '<option value='.$rTipe['id'].'>'.$rTipe['tipe'].'</option>';
}
$optGaji = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$arrsgaj = getEnum($dbname, 'datakaryawan', 'sistemgaji');
foreach ($arrsgaj as $kei => $fal) {
    $optGaji .= "<option value='".$kei."'>".$_SESSION['lang'][strtolower($fal)].'</option>';
}
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    $optOrg = "<select id=kdOrg name=kdOrg onchange=getPeriode() style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $optOrg2 = "<select id=kdeOrg name=kdeOrg onchange=getKry() style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' and tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";
    $dr = '##kdOrg';
} else {
    $optOrg = "<select id=kdOrg name=kdOrg style=\"width:150px;\"><option value=''>".$_SESSION['lang']['all'].'</option>';
    $optOrg2 = "<select id=kdeOrg name=kdeOrg style=\"width:150px;\" onchange=getKry()><option value=''>".$_SESSION['lang']['all'].'</option>';
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}

$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
    $optOrg2 .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$optOrg .= '</select>';
$optOrg2 .= '</select>';
$arr = ''.$dr.'##periode##tipeKary##sistemGaji';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/sdm_2rekapabsen.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['dafJams'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    echo '<tr><td><label>';
    echo $_SESSION['lang']['unit'];
    echo '</label></td><td>';
    echo $optOrg;
    echo '</td></tr>';
}

echo '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tipekaryawan'];
echo '</label></td><td><select id="tipeKary" name="tipeKary" style="width:150px">';
echo $optTipe;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['sistemgaji'];
echo '</label></td><td><select id="sistemGaji" name="sistemGaji" style="width:150px">';
echo $optGaji;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2daftarIuran_jamsostek','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2daftarIuran_jamsostek','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2daftarIuran_jamsostek.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>