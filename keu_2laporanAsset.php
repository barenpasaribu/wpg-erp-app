<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n\r\n";
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.'.organisasi where length(kodeorganisasi)=4';
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$optAst = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT kodetipe,namatipe FROM '.$dbname.'.sdm_5tipeasset';
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optAst .= '<option value='.$data['kodetipe'].'>'.$data['namatipe'].'</option>';
}
$arr = '##kdOrg##kdAst';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n<script language=javascript>\r\n\tfunction batal()\r\n\t{\r\n\t\tdocument.getElementById('kdOrg').value='';\t\r\n\t\tdocument.getElementById('kdAst').value='';\r\n\t\tdocument.getElementById('printContainer').innerHTML='';\r\n\t}\r\n</script>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\" >\r\n<legend><b>";
echo $_SESSION['lang']['daftarasset'];
echo "</b></legend>\r\n\r\n<table cellspacing=\"1\" border=\"0\" >\r\n    <tr><td width=\"104\"><label>";
echo $_SESSION['lang']['kodeorganisasi'];
echo '</label></td><td width="10">:</td><td width="155"><select id="kdOrg" name="kdOrg" style="width:150px;" ></option>';
echo $optOrg;
echo "</select></td></tr>\r\n    <tr><td><label>";
echo $_SESSION['lang']['tipeasset'];
echo '</label></td><td>:</td><td><select id="kdAst" name="kdAst" style="width:150px;"></option>';
echo $optAst;
echo "</select></td></tr>\r\n\t<tr></tr></table>\r\n    \r\n    <table width=\"400\"><td width=\"115\"></tr><td>&nbsp;</td>\r\n    <td width=\"273\" colspan=\"3\">\r\n        <button onclick=\"zPreview('keu_slave_2laporanAsset','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zPdf('keu_slave_2laporanAsset','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n        <button onclick=\"zExcel(event,'keu_slave_2laporanAsset.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n        <button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>