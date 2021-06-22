<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n\r\n";
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi where tipe='PT' AND kodeorganisasi like '".substr($_SESSION['empl']['lokasitugas'], 0, 3)."' ";
$qry = mysql_query($sql);
while ($data = mysql_fetch_assoc($qry)) {
    $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$optAst = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT kodetipe,namatipe FROM '.$dbname.'.sdm_5tipeasset';
$qry = mysql_query($sql);
while ($data = mysql_fetch_assoc($qry)) {
    $optAst .= '<option value='.$data['kodetipe'].'>'.$data['namatipe'].'</option>';
}
$optBatch = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sBatch = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
$qBatch = mysql_query($sBatch);
while ($rBatch = mysql_fetch_assoc($qBatch)) {
    $optBatch .= "<option value='".$rBatch['periode']."'>".$rBatch['periode'].'</option>';
}
$optTipeAsset = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTipeAsset = 'select distinct kodetipe,namatipe from '.$dbname.'.sdm_5tipeasset order by namatipe asc';
$qTipeAsset = mysql_query($sTipeAsset);
while ($rTipeAsset = mysql_fetch_assoc($qTipeAsset)) {
    $optTipeAsset .= "<option value='".$rTipeAsset['kodetipe']."'>".$rTipeAsset['namatipe'].'</option>';
}
$arr = '##kdOrg##unit##kdAst##tpAsset';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n<script language=javascript>\r\n\tfunction batal()\r\n\t{\r\n\t\tdocument.getElementById('kdOrg').value='';\t\r\n\t\tdocument.getElementById('kdAst').value='';\r\n\t\tdocument.getElementById('printContainer').innerHTML='';\r\n\t}\r\n\t\r\n\tfunction getUnit(obj) {\r\n\t\tvar pt = obj.options[obj.selectedIndex].value,\r\n\t\t\tparam='pt='+pt,\r\n\t\t\ttujuan = 'keu_slave_2laporanAsset_unit.php';\r\n\t\tif(pt=='') {\r\n\t\t\tunit.disabled = true;\r\n\t\t} else {\r\n\t\t\tpost_response_text(tujuan, param, respog);\r\n\t\t}\r\n\t\tfunction respog(){\r\n\t\t\tif (con.readyState == 4) {\r\n\t\t\t\tif (con.status == 200) {\r\n\t\t\t\t\tbusy_off();\r\n\t\t\t\t\tif (!isSaveResponse(con.responseText)) {\r\n\t\t\t\t\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t\t\t\t} else {\r\n\t\t\t\t\t\tvar unit = document.getElementById('unit');\r\n\t\t\t\t\t\t\tunit.innerHTML = con.responseText;\r\n\t\t\t\t\t\t\tunit.disabled = false;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t\telse {\r\n\t\t\t\t\tbusy_off();\r\n\t\t\t\t\terror_catch(con.status);\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n</script>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\" >\r\n<legend><b>";
echo $_SESSION['lang']['daftarasset'];
echo "</b></legend>\r\n\r\n<table cellspacing=\"1\" border=\"0\" >\r\n    <tr><td><label>";
echo $_SESSION['lang']['pt'];
echo '</label></td><td width="10">:</td><td width="155"><select id="kdOrg" name="kdOrg" style="width:150px;" onchange="getUnit(this)">';
echo $optOrg;
echo "</select></td></tr>\r\n\t<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td width="10">:</td><td width="155"><select id="unit" name="unit" style="width:150px;" disabled ><option>';
echo $_SESSION['lang']['pilihdata'];
echo "</option></select></td></tr>\r\n    <tr><td><label>";
echo $_SESSION['lang']['sdbulanini'].' '.$_SESSION['lang']['periode'];
echo '</label></td><td>:</td><td><select id="kdAst" name="kdAst" style="width:150px;">';
echo $optBatch;
echo "</select></td></tr>\r\n    <tr><td><label>";
echo $_SESSION['lang']['tipeasset'];
echo '</label></td><td>:</td><td><select id="tpAsset" name="tpAsset" style="width:150px;">';
echo $optTipeAsset;
echo "</select></td></tr>\r\n\t<tr></tr></table>\r\n    \r\n    <table width=\"400\"><td width=\"115\"></tr><td>&nbsp;</td>\r\n    <td width=\"273\" colspan=\"3\">\r\n        <button onclick=\"zPreview('keu_slave_2laporanAsset','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'keu_slave_2laporanAsset.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n        <button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>