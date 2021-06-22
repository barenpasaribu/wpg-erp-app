<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL') order by namaorganisasi asc ";
//    $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
//} else {
//    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' or tipe in ('KEBUN','PABRIK','KANWIL') order by kodeorganisasi asc";
//        $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
//    } else {
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
//        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
//    }
//}
$sPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$optOrg=makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
//$qOrg = mysql_query( getQuery("lokasitugas"));
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}
$arr = '##kdOrg##periode';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanPremi'].'/'.$_SESSION['lang']['mandor'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td>\r\n\t<td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\">";//<!--<option value=\"\">\r\n\t\t";
//echo $_SESSION['lang']['all'];
//echo '</option>-->';
echo $optOrg;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td>\r\n\t<td><select id=\"periode\" name=\"periode\" style=\"width:150px\">\r\n\t\t<!--<option value=\"\"></option>-->";
echo $optPeriode;
echo "\t</select></td>\r\n</tr>\r\n<!--\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n-->\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n\t<button onclick=\"zPreview('sdm_slave_2laporanPremiPerKemandoran','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n\r\n\t<button onclick=\"zExcel(event,'sdm_slave_2laporanPremiPerKemandoran.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n\r\n\t<button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:330px;max-width:1100px'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>