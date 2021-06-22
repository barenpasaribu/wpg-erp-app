<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optSup = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sSup = 'select supplierid,namasupplier from ' . $dbname . '.log_5supplier where substring(kodekelompok,1,1)=\'S\' order by namasupplier asc';

#exit(mysql_error($conn));
($qSup = mysql_query($sSup)) || true;

while ($rSup = mysql_fetch_assoc($qSup)) {
	$optSup .= '<option value=' . $rSup['supplierid'] . '>' . $rSup['namasupplier'] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['pilih'] . '</option>';
$arrPo = array('Laporan', 'Form Update');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$arr = '##nopp##formPil';
$optListNopp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sLnopp = 'select distinct nopp from ' . $dbname . '.log_permintaanhargadt order by nopp desc';

exit(mysql_error($sLnopp));
($qLnopp = mysql_query($sLnopp)) || true;

while ($rLnopp = mysql_fetch_assoc($qLnopp)) {
	$optListNopp .= '<option value=\'' . $rLnopp['nopp'] . '\'>' . $rLnopp['nopp'] . '</option>';
}

echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=\'js/log_2perbandingnan_harga.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function getKdorg()' . "\r\n" . '{' . "\r\n\t" . 'kdPt=document.getElementById(\'kdPt\').options[document.getElementById(\'kdPt\').selectedIndex].value;' . "\r\n\t" . 'param=\'kdPt=\'+kdPt+\'&proses=getKdorg\';' . "\r\n\t" . 'tujuan="log_slave_2detail_pembelian.php";' . "\r\n\t" . '//alert(param);' . "\t\r\n" . '    ' . "\r\n\t" . ' function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                  ' . "\t" . 'document.getElementById(\'kdUnit\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    //' . "\r\n" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n" . '  post_response_text(tujuan, param, respon);' . "\r\n\r\n" . '}' . "\r\n" . 'function searchNopp(title,content,ev)' . "\r\n" . '{' . "\r\n" . '    width=\'500\';' . "\r\n" . '    height=\'400\';' . "\r\n" . '    showDialog2(title,content,width,height,ev);' . "\r\n" . '}' . "\r\n" . 'function findNopp()' . "\r\n" . '{' . "\r\n" . '    kdNopp=document.getElementById(\'kdNopp\').value;' . "\r\n" . '    param=\'proses=getNopp\'+\'&kdNopp=\'+kdNopp;' . "\r\n" . '    tujuan=\'log_slave_2perbandingan_harga.php\';' . "\r\n" . '    post_response_text(tujuan, param, respog);' . "\t\t\t\r\n\r\n" . '    function respog(){' . "\r\n" . '            if (con.readyState == 4) {' . "\r\n" . '                    if (con.status == 200) {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                            }' . "\r\n" . '                            else {' . "\r\n" . '                                  document.getElementById(\'containerNopp\').innerHTML=con.responseText;' . "\r\n" . '                        }' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            error_catch(con.status);' . "\r\n" . '                    }' . "\r\n" . '            }' . "\r\n" . '    }' . "\t\r\n" . '}' . "\r\n" . 'function setDataNopp(brNopp)' . "\r\n" . '{' . "\r\n" . '    listdt=document.getElementById(\'nopp\');//.value=brNopp;' . "\r\n" . '    for(awal=0;awal<listdt.length;awal++)' . "\r\n" . '    {' . "\r\n" . '        if(listdt.options[awal].value==brNopp)' . "\r\n" . '        {' . "\r\n" . '            listdt.options[awal].selected=true;' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    closeDialog2();' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['bandingHarga'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['nopp'];
echo '</label></td><td><select id="nopp" name="nopp"  style="width:200px;" >';
echo $optListNopp;
echo '</select><img  src=\'images/search.png\' class=dellicon title=\'';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'];
echo '\' onclick="searchNopp(\'';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'];
echo '</legend>';
echo $_SESSION['lang']['find'];
echo '&nbsp;<input type=text class=myinputtext id=kdNopp><button class=mybutton onclick=findNopp()>';
echo $_SESSION['lang']['find'];
echo '</button></fieldset><div id=containerNopp style=overflow=auto;height=380;width=485></div>\',event);"></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['form'];
echo '</label></td><td><select id="formPil" name=""formPil style="width:200px;">';
echo $optLokal;
echo '</select></td></tr>' . "\r\n\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2perbandingan_harga\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><!--<button onclick="zPdf(\'log_slave_2perbandingan_harga\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button>--><button onclick="zExcel(event,\'log_slave_2perbandingan_harga.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:550px;width:1200px\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
