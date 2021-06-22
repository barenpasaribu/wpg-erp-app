<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$arr = '##kdBrg##tglDr##tglSmp';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n\r\n" . '<script>' . "\r\n\r\n" . 'function searchBrg(title,content,ev)' . "\r\n" . '{' . "\r\n\t" . 'width=\'500\';' . "\r\n\t" . 'height=\'400\';' . "\r\n\t" . 'showDialog1(title,content,width,height,ev);' . "\r\n\t" . '//alert(\'asdasd\');' . "\r\n" . '}' . "\r\n" . 'function findBrg()' . "\r\n" . '{' . "\r\n\t" . 'txt=trim(document.getElementById(\'no_brg\').value);' . "\r\n\t" . 'if(txt==\'\')' . "\r\n\t" . '{' . "\r\n\t\t" . 'alert(\'Text is obligatory\');' . "\r\n\t" . '}' . "\r\n\t" . 'else if(txt.length<3)' . "\r\n\t" . '{' . "\r\n\t\t" . 'alert(\'Too Short\');' . "\t\r\n\t" . '}' . "\r\n\t" . 'else' . "\r\n\t" . '{' . "\r\n\t\t" . 'param=\'txtfind=\'+txt;' . "\r\n\t\t" . 'tujuan=\'log_slave_get_brg.php\';' . "\r\n\t\t" . 'post_response_text(tujuan, param, respog);' . "\r\n\t" . '}' . "\r\n\t" . 'function respog()' . "\r\n\t" . '{' . "\r\n\t\t" . '      if(con.readyState==4)' . "\r\n\t\t" . '      {' . "\r\n\t\t\t" . '        if (con.status == 200) {' . "\r\n\t\t\t\t\t\t" . 'busy_off();' . "\r\n\t\t\t\t\t\t" . 'if (!isSaveResponse(con.responseText)) {' . "\r\n\t\t\t\t\t\t\t" . 'alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n\t\t\t\t\t\t" . '}' . "\r\n\t\t\t\t\t\t" . 'else {' . "\r\n\t\t\t\t\t\t\t" . '//alert(con.responseText);' . "\r\n\t\t\t\t\t\t\t" . 'document.getElementById(\'containerBrg\').innerHTML=con.responseText;' . "\r\n\t\t\t\t\t\t" . '}' . "\r\n\t\t\t\t\t" . '}' . "\r\n\t\t\t\t\t" . 'else {' . "\r\n\t\t\t\t\t\t" . 'busy_off();' . "\r\n\t\t\t\t\t\t" . 'error_catch(con.status);' . "\r\n\t\t\t\t\t" . '}' . "\r\n\t\t" . '      }' . "\r\n\t" . ' }' . "\r\n" . '}' . "\r\n" . 'function setBrg(kdbrg,namabrg,satuan)' . "\r\n" . '{' . "\r\n" . '         document.getElementById(\'kdBrg\').value=kdbrg;' . "\r\n\t" . ' document.getElementById(\'nmBrg\').value=namabrg;' . "\r\n\t" . ' //document.getElementById(\'sat_\'+nomor).value=satuan;' . "\r\n\t" . ' closeDialog();' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['lapPenawaran'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['namabarang'];
echo '</label></td><td><input type="text" class="myinputtext" id="nmBrg" name="nmBrg" disabled style="width:150px;"  />&nbsp;<img src=images/search.png class=dellicon title=';
echo $_SESSION['lang']['find'];
echo ' onclick="searchBrg(\'';
echo $_SESSION['lang']['findBrg'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['findnoBrg'];
echo '</legend> Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=containerBrg></div>\',event)";>' . "\r\n" . '     <input type="hidden" id="kdBrg" name="kdBrg" /></td></tr>' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['tgldari'];
echo '</label></td><td><input type="text" class="myinputtext" id="tglDr" name="tglDr" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>' . "\r\n" . '<tr><td>';
echo $_SESSION['lang']['tanggalsampai'];
echo '</td><td><input type="text" class="myinputtext" id="tglSmp" name="tglSmp" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2laporan_permintaan_harga\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'log_slave_2laporan_permintaan_harga\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'log_slave_2laporan_permintaan_harga.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
