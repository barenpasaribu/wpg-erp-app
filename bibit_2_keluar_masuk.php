<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
$optBatch = "<option value=''>".$_SESSION['lang']['all'].'</option>';
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $sBatch = "select distinct batch from $dbname.bibitan_mutasi order by batch desc";
    $sKodeorg = "select distinct kodeorganisasi,namaorganisasi from $dbname.organisasi where tipe='KEBUN' order by namaorganisasi asc";
} else {
    $sBatch = "select distinct batch from $dbname.bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
    $sKodeorg = "select distinct kodeorganisasi,namaorganisasi from $dbname.organisasi where tipe='KEBUN' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
}
//echoMessage(" sql ",$sKodeorg,true);
$qBatch = mysql_query($sBatch) || exit(mysql_error($conns));
while ($rBatch = mysql_fetch_assoc($qBatch)) {
    $optBatch .= "<option value='".$rBatch['batch']."'>".$rBatch['batch'].'</option>';
}
$optKodeorg = makeOption2($sKodeorg,
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
//$optKodeorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$qKodeOrg = mysql_query($sKodeorg) || exit(mysql_error($conns));
//while ($rKodeorg = mysql_fetch_assoc($qKodeOrg)) {
//    $optKodeorg .= "<option value='".$rKodeorg['kodeorganisasi']."'>".$rKodeorg['namaorganisasi'].'</option>';
//}
$arr = '##kdUnit##kdBatch##tanggal1##tanggal11';
echo "<script language=javascript src=\"js/zTools.js\"></script>\r\n<script language=javascript src=\"js/zReport.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/bibit_2_keluar_masuk.js\"></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n";
$frm[0] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$_SESSION['lang']['laporanStockBIbit']."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">\r\n".$optKodeorg."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['batch']."</label></td><td><select id=\"kdBatch\" name=\"kdBatch\" style=\"width:150px\">\r\n".$optBatch."</select></td></tr><tr>\r\n    <td><label>".$_SESSION['lang']['sampai']."</label></td>\r\n    <td><input type='text' class='myinputtext' id='tanggal11' onmousemove='setCalendar(this.id)' onkeypress='return false;'  \r\n    size='10' maxlength='10' style=\"width:150px;\"/></td>\r\n</tr><tr><td colspan=\"2\"><button onclick=\"zPreview('bibit_2_slave_keluar_masuk','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('bibit_2_slave_keluar_masuk','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'bibit_2_slave_keluar_masuk.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
//$optpt1 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$sOrg2 = "select kodeorganisasi, namaorganisasi from $dbname.organisasi where tipe ='pt' order by namaorganisasi asc";
//$qOrg2 = mysql_query($sOrg2);// || exit(mysql_error($conns));
//while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
//    $optpt1 .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
//}
$optpt1 = makeOption2(getQuery('pt'),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optkebun1 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$frm[1] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$_SESSION['lang']['laporanStockBIbit']."(Recap)</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['pt']."</label></td>\r\n    <td><select id=\"pt1\" name=\"pt1\" style=\"width:150px\" onchange=getkebun()>".$optpt1."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['kebun']."</label></td>\r\n    <td><select id=\"kebun1\" name=\"kebun1\" style=\"width:150px\">".$optkebun1."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['sampai']."</label></td>\r\n    <td><input type='text' class='myinputtext' id='tanggal1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  \r\n    size='10' maxlength='10' style=\"width:150px;\"/></td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button class=mybutton id=preview1 name=preview1 onclick=\"zPreview('bibit_2_slave_keluar_masuk2','".$arr."','printContainer')\">".$_SESSION['lang']['preview']."</button>\r\n        <button class=mybutton id=excel1 name=excel1 onclick=exceldata1(event,'bibit_2_slave_keluar_masuk.php')>".$_SESSION['lang']['excel']."</button>\r\n    </td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='container1' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$optbatch = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optkodeorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$kodeorg = "select distinct kodeorganisasi,namaorganisasi \r\n    from ".$dbname.".organisasi where tipe='KEBUN' \r\n    order by namaorganisasi asc";
$query = mysql_query($kodeorg);// || exit(mysql_error($conns));
while ($result = mysql_fetch_assoc($query)) {
    $optkodeorg .= "<option value='".$result['kodeorganisasi']."'>".$result['namaorganisasi'].'</option>';
}
$frm[2] .= "\r\n\r\n    <fieldset style=\"float: left;\">\r\n    <legend><b>".$_SESSION['lang']['laporanStockBIbit']."</b></legend>\r\n    <table cellspacing=\"1\" border=\"0\" >\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['unit']."</label></td>\r\n        <td><select id=\"kodeunit\" name=\"kodeunit\" onchange=\"ambilbatch(this.value);\" style=\"width:150px\">".$optkodeorg."</select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['batch']."</label></td>\r\n        <td><select id=\"kodebatch\" name=\"kodebatch\" style=\"width:150px\">".$optbatch."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan=\"2\">\r\n        <button onclick=\"previewdata2()\" class=\"mybutton\" >Preview</button>\r\n        <button onclick=\"exceldata2(event,'bibit_slave_2kartu.php')\" class=\"mybutton\">Excel</button>\r\n        </td>\r\n    </tr>\r\n    </table>\r\n    </fieldset>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n    <div id='printContainer3' style='overflow:auto;height:50%;max-width:100%;'>\r\n    </div>\r\n</fieldset>";
$hfrm[0] = $_SESSION['lang']['laporanStockBIbit'];
$hfrm[1] = $_SESSION['lang']['laporanStockBIbit'].'(Recap)';
$hfrm[2] = 'Seed Card';
drawTab('FRM', $hfrm, $frm, 150, 1200);
CLOSE_BOX();
echo close_body();

?>