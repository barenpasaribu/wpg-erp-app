<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n\r\n\r\n";
OPEN_BOX();
$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_budget ORDER BY tahunbudget desc';

exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optThn .= '<option value=' . $data['tahunbudget'] . '>' . $data['tahunbudget'] . '</option>';
}

$optWs = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM ' . $dbname . '.organisasi where tipe=\'WORKSHOP\' ORDER BY kodeorganisasi';

exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optWs .= '<option value=' . $data['kodeorganisasi'] . '>' . $data['namaorganisasi'] . '</option>';
}

$arr = '##thnbudget##kdWs';
echo "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n\r\n" . '<script language=javascript>' . "\r\n\t" . 'function batal()' . "\r\n\t" . '{' . "\r\n\t\t" . 'document.getElementById(\'thnbudget\').value=\'\';' . "\t\r\n\t\t" . 'document.getElementById(\'kdWs\').value=\'\';' . "\r\n\t\t" . 'document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n\t" . '}' . "\r\n\t\r\n\t" . 'function getDet(id)' . "\r\n\t" . '{' . "\r\n\t\t" . 'kdTrak=document.getElementById(\'kdTrak_\'+id).getAttribute(\'value\');' . "\r\n\t\t" . 'kdeWs=document.getElementById(\'kdeWs_\'+id).getAttribute(\'value\');' . "\r\n\t\t" . 'thnbudget=document.getElementById(\'thnbudget\').options[document.getElementById(\'thnbudget\').selectedIndex].value;' . "\r\n\t\t" . 'param="kdTrak="+kdTrak+"&brsKe="+id+"&kdeWs="+kdeWs+"&thnbudget="+thnbudget;' . "\r\n\t\t" . ' ' . "\r\n\t\t" . 'tujuan="bgt_slave_laporan_rp_jam_bengkel.php";' . "\r\n\t\t" . '//alert(param);' . "\t\r\n\t\t\r\n\t\t" . ' function respon() {' . "\r\n\t\t\t" . 'if (con.readyState == 4) ' . "\r\n\t\t\t" . '{' . "\r\n\t\t\t\t" . 'if (con.status == 200) ' . "\r\n\t\t\t\t" . '{' . "\r\n\t\t\t\t\t" . 'busy_off();' . "\r\n\t\t\t\t\t" . 'if (!isSaveResponse(con.responseText)) ' . "\r\n\t\t\t\t\t" . '{' . "\r\n\t\t\t\t\t\t" . 'alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n\t\t\t\t\t" . '} else' . "\r\n\t\t\t\t\t" . '{' . "\r\n\t\t\t\t\t\t" . '// Success Response' . "\r\n\t\t\t\t\t" . '//' . "\t" . 'alert(con.responseText);' . "\r\n\t\t\t\t\t\t" . 'document.getElementById(\'detail_\'+id).innerHTML=con.responseText;' . "\r\n\t\t\t\t\t" . '}' . "\r\n\t\t\t\t" . '} ' . "\r\n\t\t\t\t" . 'else ' . "\r\n\t\t\t\t" . '{' . "\r\n\t\t\t\t\t" . 'busy_off();' . "\r\n\t\t\t\t\t" . 'error_catch(con.status);' . "\r\n\t\t\t\t" . '}' . "\r\n\t\t\t" . '}' . "\r\n\t\t" . '}' . "\r\n\t" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n\t" . '  post_response_text(tujuan+\'?\'+\'proses=getDetail\', param, respon);' . "\r\n" . '}' . "\r\n\r\n" . 'function printFile(param,tujuan,title,event)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'200\';' . "\r\n" . '   height=\'150\';' . "\r\n" . '   ' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,event); ' . "\r\n" . '}' . "\r\n\r\n" . 'function dataKeExcel(event,kdTrak,kdeWs,thnBudget)' . "\r\n" . '{' . "\r\n\t" . 'kodeTraksi=kdTrak;' . "\r\n\t" . 'kodeWs=kdeWs;' . "\r\n\t" . 'thnBudget=thnbudget;' . "\r\n\t" . 'param=\'kdTrak=\'+kodeTraksi+\'&kdWs=\'+kodeWs+\'&thnbudget=\'+thnBudget+\'&proses=ExcelAlokasi\';' . "\r\n\t" . '//alert (param);' . "\r\n\t\r\n\t" . 'tujuan=\'bgt_slave_laporan_rp_jam_bengkel.php\';' . "\r\n\t" . 'judul=\'Report Ms.Excel\'; ' . "\r\n\t" . 'printFile(param,tujuan,judul,event) ' . "\r\n\r\n" . '}' . "\r\n\r\n" . 'function closeDet(id)' . "\r\n" . '{' . "\r\n\t" . 'document.getElementById(\'detail_\'+id).innerHTML=\'\';' . "\r\n" . '}' . "\r\n\r\n\r\n\r\n" . 'function printFile2(param,tujuan,title,event)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'1200\';' . "\r\n" . '   height=\'450\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   ' . "\r\n\r\n" . '   showDialog1(title,content,width,height,event); ' . "\r\n" . '}' . "\r\n\r\n" . 'function dataKePdf(event,kdTrak,kdeWs,thnBudget)' . "\r\n" . '{' . "\r\n\t" . 'kodeTraksi=kdTrak;' . "\r\n\t" . 'kodeWs=kdeWs;' . "\r\n\t" . 'thnBudget=thnbudget;' . "\r\n\t" . 'param=\'kdTrak=\'+kodeTraksi+\'&kdWs=\'+kodeWs+\'&thnbudget=\'+thnBudget+\'&proses=pdfAlokasi\';' . "\r\n\t" . '//alert (param);' . "\r\n\t\r\n\t" . 'tujuan=\'bgt_slave_laporan_rp_jam_bengkel.php\';' . "\r\n\t" . 'judul=\'Report Detail PDF \'+ kdeWs +\' Tahun \'+ thnBudget +\' \'; ' . "\r\n\t" . 'printFile2(param,tujuan,judul,event) ' . "\r\n\t" . '//alert (param);' . "\r\n\r\n" . '}' . "\r\n\r\n\r\n\r\n\r\n" . '/*function previewpdf(event,kdTrak,kdeWs,thnbudget)' . "\r\n" . '{' . "\r\n\t" . 'kodeTraksi=kdTrak;' . "\r\n\t" . 'kodeWs=kdeWs;' . "\r\n\t" . 'thnBudget=thnbudget;' . "\r\n\t" . 'param=\'kdTrak=\'+kodeTraksi+\'&kdWs=\'+kodeWs+\'&thnbudget=\'+thnBudget+\'&proses=pdfAlokasi\';' . "\r\n\t" . 'tujuan=\'bgt_slave_laporan_rp_jam_bengkel.php\';' . "\r\n" . ' //display window' . "\r\n\r\n" . '   title=kdTrak;' . "\r\n" . '   width=\'700\';' . "\r\n" . '   height=\'400\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,event);' . "\r\n" . '   ' . "\r\n" . '}*/' . "\r\n\r\n\r\n\r\n\r\n\r\n" . '</script>' . "\r\n\r\n\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['laporanrpjambengkel'];
echo '</b></legend>' . "\r\n\r\n" . '<table width="285" border="0" cellspacing="1" >' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td>:</td><td><select id="thnbudget" name="thnbudget" style="width:150px;" ></option>';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['workshop'];
echo '</label></td><td>:</td><td><select id="kdWs" name="kdWs" style="width:150px;"></option>';
echo $optWs;
echo '</select></td></tr>' . "\r\n" . '</table>' . "\r\n" . '    ' . "\r\n" . '    <table width="365" border="0" cellspacing="1" >   ' . "\r\n" . '    <tr>' . "\r\n" . '    <td width="95"></td>' . "\r\n" . '    <td width>' . "\r\n" . '      ' . "\r\n" . '        <button onclick="zPreview(\'bgt_slave_laporan_rp_jam_bengkel\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['preview'];
echo '</button>' . "\r\n" . '        <button onclick="zExcel(event,\'bgt_slave_laporan_rp_jam_bengkel.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['excel'];
echo '</button>   ' . "\r\n" . '        <button onclick="zPdf(\'bgt_slave_laporan_rp_jam_bengkel\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['pdf'];
echo '</button>' . "\r\n" . '        <button onclick="batal()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "\r\n" . '<fieldset style=\'clear:both\'><legend><b>';
echo $_SESSION['lang']['printArea'];
echo '</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
