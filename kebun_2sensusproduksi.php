<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
//$sOrg = 'select distinct substr(kodeorg,1,4) as kodeorg from '.$dbname.'.kebun_rencanapanen_vw order by kodeorg asc';
//$qOrg = mysql_query($sOrg);
//$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//    $optOrg .= '<option value='.$rOrg['kodeorg'].'>'.$rOrg['kodeorg'].'</option>';
//}
//$sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_rencanapanen_vw order by tanggal asc';
//$qPeriode = mysql_query($sPeriode);
//$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//while ($rPeriode = mysql_fetch_object($qPeriode)) {
//    $optPeriode .= "<option value='".$rPeriode->periode."'>".$rPeriode->periode.'</option>';
//}

$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where  left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by kodeorganisasi asc";
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arr = '##kdUnit##periode';
$optModel = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_aktifitas order by tanggal desc';
$qPeriode = mysql_query($sPeriode) ;
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optModel .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
}

$arr = '##kodeorg##periode';
echo "<script language=javascript src='js/zMaster.js'></script> \r\n<script language=javascript src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script languange=javascript1.2 src='js/formReport.js'></script>\r\n<script languange=javascript1.2 src='js/zGrid.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['sensusproduksi'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['kodeorg'];
echo '</label></td><td><select id="kodeorg" name="kodeorg" style="width:150px">';
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optModel;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n    ";
echo "<button onclick=\"zPreview('kebun_slave_2sensusproduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>\r\n          <button onclick=\"zExcel(event,'kebun_slave_2sensusproduksi.php','".$arr."','printContainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    \r\n          <button onclick=\"zPdf('kebun_slave_2sensusproduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">".$_SESSION['lang']['pdf'].'</button>';
echo "    </td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<fieldset><legend>List</legend>\r\n         <div id='printContainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n     </fieldset>";
CLOSE_BOX();
close_body();

?>