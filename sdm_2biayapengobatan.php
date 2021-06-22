<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$arr0 = '##tanggal';
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script type=\"text/javascript\" src=\"js/sdm_2biayapengobatan.js\"></script>\r\n<script>\r\n\r\n\r\n</script>\r\n\r\n<link rel='stylesheet' type='text/css' href='style/zTable.css'>\r\n\r\n";
$title[1] = $_SESSION['lang']['biayapengobatan'];
$optPt .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
$spt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qpt = mysql_query($spt);
while ($rpt = mysql_fetch_assoc($qpt)) {
    $optPt .= "<option value='".$rpt['kodeorganisasi']."'>".$rpt['namaorganisasi'].'</option>';
}
$optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sdr = 'select distinct left(periodegaji,4) as periode from '.$dbname.'.sdm_gaji order by periodegaji desc';
$qdr = mysql_query($sdr);
while ($rdr = mysql_fetch_assoc($qdr)) {
    $optPrdSmp .= "<option value='".$rdr['periode']."'>".$rdr['periode'].'</option>';
}
$arrsmstr = ['I' => 'Satu', 'II' => 'Dua'];
foreach ($arrsmstr as $lstsmtr => $nmsstr) {
    $optsmstr .= "<option value='".$lstsmtr."'>".$nmsstr.'</option>';
}
$arrdata = ['Default', 'Rumah Sakit'];
foreach ($arrdata as $lstsmtr => $nmsstr) {
    $optsmstr2 .= "<option value='".$lstsmtr."'>".$nmsstr.'</option>';
}
$arr = '##ptId2##unitId2##thn##smstr';
echo "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[1]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >";
echo '<tr><td>'.$_SESSION['lang']['pt'].'</td>';
echo "<td><select id=ptId2  onchange='getUnit2()'  style=width:150px;>".$optPt.'</select></td>';
echo '</tr>';
echo '<tr><td>'.$_SESSION['lang']['lokasitugas']."</td>\r\n          <td><select id=unitId2 style=width:150px;>".$optUnit."</select></td>\r\n          </tr>";
echo '<tr><td>'.$_SESSION['lang']['tahun']."</td>\r\n          <td><select id=thn style=width:150px;>".$optPrdSmp."</select></td>\r\n          </tr>";
echo '<tr><td>'.$_SESSION['lang']['semester']."</td>\r\n          <td><select id=smstr style=width:150px;>".$optsmstr."</select></td>\r\n          </tr>";
echo "<tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button class=mybutton onclick=zPreview('sdm_slave_2biayapengobatan','".$arr."','printContainer2')>".$_SESSION['lang']['proses']."</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2biayapengobatan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n\r\n<div id='printContainer2' style='overflow:auto;height:250px;max-width:1220px;'>\r\n</div>\r\n\r\n<div id='printContainer5' style='overflow:auto;height:250px;max-width:1220px;display:none;'>\r\n</div>\r\n\r\n<div id='printContainer7' style='overflow:auto;height:250px;max-width:1220px;display:none;'>\r\n</div>\r\n \r\n</fieldset>";
CLOSE_BOX();
echo close_body();

?>