<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$opttipekaryawan = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optOrg = $optPeriode;
$sOrg = "select namaorganisasi,kodeorganisasi from $dbname.organisasi ".
    "where tipe in ('KEBUN','PABRIK','KANWIL') order by namaorganisasi asc ";
$sPeriode = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5tipekaryawan where id<>0 order by tipe';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttipekaryawan .= "<option value='".$bar->id."'>".$bar->tipe.'</option>';
}
//$qOrg = mysql_query($sOrg);
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
//}

$optOrg= makeOption2(getQuery("lokasitugas"),
    array(),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$arr = '##kdUnit##tpKary##periode';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\nfunction Clear1()\r\n{\r\n    document.getElementById('kdUnit').value='';\r\n    document.getElementById('periode').value='';\r\n    document.getElementById('tpKary').value='';\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['npwp'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td>\r\n\t<td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">";
echo $optOrg;
echo "\t</select></td>\r\n</tr>\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td>\r\n\t<td><select id=\"periode\" name=\"periode\" style=\"width:150px\">\r\n\t\t";
echo $optPeriode;
echo "\t</select></td>\r\n</tr>\r\n<tr>\r\n\t<td><label>";
echo $_SESSION['lang']['tipekaryawan'];
echo "</label></td>\r\n\t<td><select id=\"tpKary\" name=\"tpKary\" style=\"width:150px\">\r\n\t\t";
echo $opttipekaryawan;
echo "\t</select></td>\r\n</tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n\t<button onclick=\"zPreview('sdm_slave_2daftarKaryNpwp','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n\t<!--<button onclick=\"zPdf('sdm_slave_2daftarKaryNpwp','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n\t<button onclick=\"zExcel(event,'sdm_slave_2daftarKaryNpwp.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n\t<button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>