<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
//if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
//    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where \r\n               tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') and CHAR_LENGTH(kodeorganisasi)='4' order by namaorganisasi asc ";
//    $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji order by periode desc';
//} else {
//    if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
//        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' or (tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') and CHAR_LENGTH(kodeorganisasi)='4') order by kodeorganisasi asc";
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
//$qOrg = mysql_query(getQuery("lokasitugas"));
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}
$optOrg=makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optTip = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTipe = 'select distinct * from '.$dbname.'.sdm_5tipekaryawan where id!=0 order by tipe asc';
$qTipe = mysql_query($sTipe);
while ($rTipe = mysql_fetch_assoc($qTipe)) {
    $optTip .= "<option value='".$rTipe['id']."'>".$rTipe['tipe'].'</option>';
}
$arr = '##kdOrg##periode##tpKary';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanPremi'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td>\r\n\t<td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\">";
echo $optOrg;
echo "\t</select></td>\r\n</tr>\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td>\r\n\t<td><select id=\"periode\" name=\"periode\" style=\"width:150px\">\r\n\t\t";
echo $optPeriode;
echo "\t</select></td>\r\n</tr>\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['tipekaryawan'];
echo "</label></td>\r\n\t<td><select id=\"tpKary\" name=\"tpKary\" style=\"width:150px\">\r\n\t\t";
echo $optTip;
echo "\t</select></td>\r\n</tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n\t<button onclick=\"zPreview('sdm_slave_2laporanPremi','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n\t<button onclick=\"zPdf('sdm_slave_2laporanPremi','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n\t<button onclick=\"zExcel(event,'sdm_slave_2laporanPremi.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n\t<button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>