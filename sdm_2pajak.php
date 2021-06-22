<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('KEBUN','PABRIK','TRAKSI','KANWIL') order by namaorganisasi asc ";
} else {
    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}
$sOrg=getQuery("lokasitugas");
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$sOrg = 'select distinct substr(periodegaji,1,4) as periodegaji from '.$dbname.'.sdm_gaji order by periodegaji desc';
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optTahun .= '<option value='.$rOrg['periodegaji'].'>'.$rOrg['periodegaji'].'</option>';
}
$arr = '##kodeorg##tahun';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/sdm_2pajak.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['pajak'].'(PPh21)';
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kodeorg" name="kodeorg" style="width:150px"><option value=""></option>';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tahun'];
echo '</label></td><td><select id="tahun" name="tahun" style="width:150px"><option value=""></option>';
echo $optTahun;
echo "</select></td></tr>\r\n<tr><td colspan=\"2\">\r\n    <button onclick=\"zPreview('sdm_slave_2pajak','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"html\">Preview</button>\r\n    <button onclick=\"zExcel(event,'sdm_slave_2pajak.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>\r\n    <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto; height:50%; max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";
CLOSE_BOX();
echo close_body();

?>