<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();

if ($_SESSION['language'] == 'EN') {
	$zz = 'kelompok1 as kelompok';
}
else {
	$zz = 'kelompok';
}

$optKlmpk = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sOrg = 'select ' . $zz . ',kode from ' . $dbname . '.log_5klbarang order by kode asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optKlmpk .= '<option value=' . $rOrg['kode'] . '>' . $rOrg['kode'] . ' - ' . $rOrg['kelompok'] . '</option>';
}

$optSup = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sSup = 'select supplierid,namasupplier from ' . $dbname . '.log_5supplier where substring(kodekelompok,1,1)=\'S\' order by namasupplier asc';

#exit(mysql_error($conn));
($qSup = mysql_query($sSup)) || true;

while ($rSup = mysql_fetch_assoc($qSup)) {
	$optSup .= '<option value=' . $rSup['supplierid'] . '>' . $rSup['namasupplier'] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arrPo = array('Head Office', 'Local');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$arr = '##klmpkBrg##kdBrg##tglDr##tanggalSampai##lokBeli';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n\r\n" . '<script>' . "\r\n" . 'function getBrg()' . "\r\n" . '{' . "\r\n\t" . 'klmpkBrg=document.getElementById(\'klmpkBrg\').options[document.getElementById(\'klmpkBrg\').selectedIndex].value;' . "\r\n\t" . 'param=\'klmpkBrg=\'+klmpkBrg+\'&proses=getBrg\';' . "\r\n\t" . 'tujuan="log_slave_2detail_pembelian_brg.php";' . "\r\n\t" . '//alert(param);' . "\t\r\n" . '    ' . "\r\n\t" . ' function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                  ' . "\t" . 'document.getElementById(\'kdBrg\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    //' . "\r\n" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n" . '  post_response_text(tujuan, param, respon);' . "\r\n\r\n" . '}' . "\r\n" . 'function searchBrg(title,content,ev)' . "\r\n" . '{' . "\r\n" . '        klmpk=document.getElementById(\'klmpkBrg\').options[document.getElementById(\'klmpkBrg\').selectedIndex].value;' . "\r\n" . '        if(klmpk==\'\')' . "\r\n" . '            {' . "\r\n" . '                alert("Kelompok Barang Tidak Boleh Kosong!!");' . "\r\n" . '                return;' . "\r\n" . '            }' . "\r\n" . '            ' . "\r\n\t" . 'width=\'500\';' . "\r\n\t" . 'height=\'400\';' . "\r\n\t" . 'showDialog1(title,content,width,height,ev);' . "\r\n\t" . '//alert(\'asdasd\');' . "\r\n" . '}' . "\r\n" . 'function findBrg()' . "\r\n" . '{' . "\r\n" . '    klmpkBrg=document.getElementById(\'klmpkBrg\').value;' . "\r\n" . '    nmBrg=document.getElementById(\'nmBrg\').value;' . "\r\n" . '    param=\'klmpkBrg=\'+klmpkBrg+\'&nmBrg=\'+nmBrg+\'&proses=getBarang\';' . "\r\n" . '    tujuan=\'log_slave_2detail_pembelian_brg.php\';' . "\r\n" . '        post_response_text(tujuan, param, respog);' . "\r\n\t" . 'function respog()' . "\r\n\t" . '{' . "\r\n" . '              if(con.readyState==4)' . "\r\n" . '              {' . "\r\n" . '                        if (con.status == 200) {' . "\r\n" . '                                        busy_off();' . "\r\n" . '                                        if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                                alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                                        }' . "\r\n" . '                                        else {' . "\r\n" . '                                          //' . "\t" . 'alert(con.responseText);' . "\r\n" . '                                           document.getElementById(\'containerBarang\').innerHTML=con.responseText;' . "\r\n" . '                                        }' . "\r\n" . '                                }' . "\r\n" . '                                else {' . "\r\n" . '                                        busy_off();' . "\r\n" . '                                        error_catch(con.status);' . "\r\n" . '                                }' . "\r\n" . '              }' . "\t\r\n\t" . ' }  ' . "\r\n\t\t\r\n" . '}' . "\r\n" . 'function setData(kdbrg)' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'kdBrg\').value=kdbrg;' . "\r\n" . '    //document.getElementById(\'namaBrg\').value=namaBarang;' . "\r\n" . '    //document.getElementById(\'satuan\').innerHTML=sat;' . "\r\n" . '    closeDialog();' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['detPembBrg'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kelompokbarang'];
echo '</label></td><td><select id="klmpkBrg" name="klmpkBrg" style="width:150px" onchange="getBrg()">';
echo $optKlmpk;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['namabarang'];
echo '</label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><option value=\'\'>';
echo $_SESSION['lang']['all'];
echo '</option></select>&nbsp;<img src="images/search.png" class="resicon" title=\'';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'];
echo '\' onclick="searchBrg(\'';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'];
echo '</legend>';
echo $_SESSION['lang']['find'];
echo '&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>';
echo $_SESSION['lang']['find'];
echo '</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>\',event);"></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['lokasiBeli'];
echo '</label></td><td><select id="lokBeli" name="lokBeli" style="width:150px">';
echo $optLokal;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['tanggal'];
echo '</label></td><td><input type="text" class="myinputtext" id="tglDr" name="tglDr" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>' . "\r\n" . '<tr><td>';
echo $_SESSION['lang']['tanggalsampai'];
echo '</td><td><input type="text" class="myinputtext" id="tanggalSampai" name="tanggalSampai" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>' . "\r\n\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2detail_pembelian_brg\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'log_slave_2detail_pembelian_brg\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'log_slave_2detail_pembelian_brg.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
