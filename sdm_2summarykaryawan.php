<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$arr0 = '##tanggal';

$str="SELECT 1 as level,d.karyawanid, d.namakaryawan, ".
    "o.kodeorganisasi,o.namaorganisasi,o.induk ".
    "FROM datakaryawan d ".
    "INNER JOIN user u on u.karyawanid=d.karyawanid ".
    "INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
    "WHERE u.namauser= '" .$_SESSION['standard']['username'] ."'";
$optPt= makeOption2(getQuery("lokasitugas"),
    array(),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

//$optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';

$str=" SELECT ".
    "o.kodeorganisasi,o.namaorganisasi,o.induk ".
    "FROM  organisasi o    ".
    "WHERE o.induk in ( ".
    "SELECT o.kodeorganisasi ".
    "FROM datakaryawan d ".
    "INNER JOIN user u on u.karyawanid=d.karyawanid ".
    "INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
    "WHERE u.namauser='" .$_SESSION['standard']['username'] ."'  ".
    ")";
$optUnit= makeOption2($str,
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

echo "<script language=javascript src='js/zTools.js'></script>\r\n<script type=\"text/javascript\" src=\"js/sdm_2summarykaryawan.js\"></script>\r\n<script>\r\n\r\n\r\n</script>\r\n\r\n<link rel='stylesheet' type='text/css' href='style/zTable.css'>\r\n\r\n";
$title[0] = $_SESSION['lang']['summary'].' '.$_SESSION['lang']['karyawan'];
$title[1] = $_SESSION['lang']['summary'].' '.$_SESSION['lang']['karyawan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['gaji'];
$frm[0] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[0]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['tanggal']."</label></td>\r\n    <td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n</tr><tr><td>".$_SESSION['lang']['unit']."</td><td><select id=kodeorgsk style=width:150px;>".$optUnit."</select></td></tr><tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button onclick=\"getlevel0()\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2summarykaryawan.php','".$arr0."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\nClick header to see details...\r\n<div id='printContainer0' style='overflow:auto;height:250px;max-width:1220px'>\r\n</div>\r\n<div id='printContainer1' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div>\r\n</fieldset>";
//$optPt .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
//$spt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
//$qpt = mysql_query($spt);
//while ($rpt = mysql_fetch_assoc($qpt)) {
//    $optPt .= "<option value='".$rpt['kodeorganisasi']."'>".$rpt['namaorganisasi'].'</option>';
//}


$sdr = 'select distinct periodegaji as periode from '.$dbname.'.sdm_gaji order by periodegaji desc';
$qdr = mysql_query($sdr);
while ($rdr = mysql_fetch_assoc($qdr)) {
    $optPrdDr .= "<option value='".$rdr['periode']."'>".$rdr['periode'].'</option>';
}
$arr = '##ptId2##unitId2##prdIdDr2';
$frm[1] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[1]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >";
$frm[1] .= '<tr><td>'.$_SESSION['lang']['unit']."</td>\r\n          <td><select id=unitId2 style=width:150px;>".$optUnit."</select></td>\r\n          </tr>";
$frm[1] .= '<tr><td>'.$_SESSION['lang']['periode']."</td>\r\n          <td><select id=prdIdDr2 style=width:150px;>".$optPrdDr."</select></td>\r\n          </tr>";
$frm[1] .= "<tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button class=mybutton onclick=zPreview('sdm_slave_2summarykaryawan2','".$arr."','printContainer2')>".$_SESSION['lang']['proses']."</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2summarykaryawan2.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n\r\n<div id='printContainer2' style='overflow:auto;height:250px;max-width:1220px;'>\r\n</div>\r\n\r\n<div id='printContainer5' style='overflow:auto;height:250px;max-width:1220px;'>\r\n</div>\r\n \r\n</fieldset>";
list($hfrm[0], $hfrm[1]) = $title;
drawTab('FRM', $hfrm, $frm, 200, 1100);
CLOSE_BOX();
echo close_body();

?>