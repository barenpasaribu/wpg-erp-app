<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optSup = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sSup = 'select distinct substr(kodebudget,3,3) as kelompokbarang from ' . $dbname . '.bgt_budget_detail where kodebudget like \'M%\' order by substr(kodebudget,3,3) asc';

#exit(mysql_error($conn));
($qSup = mysql_query($sSup)) || true;

while ($rSup = mysql_fetch_assoc($qSup)) {
	$optSup .= '<option value=' . $rSup['kelompokbarang'] . '>' . $rSup['kelompokbarang'] . '-' . $optKelompok[$rSup['kelompokbarang']] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arrPo = array('Pusat', 'Lokal');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThnBUdget = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget order by tahunbudget desc';

#exit(mysql_error());
($qThnBudget = mysql_query($sThnBUdget)) || true;

while ($rThnBudget = mysql_fetch_assoc($qThnBudget)) {
	$optThn .= '<option value=' . $rThnBudget['tahunbudget'] . '>' . $rThnBudget['tahunbudget'] . '</option>';
}

$arrPilMode = array($_SESSION['lang']['fisik'], $_SESSION['lang']['rp']);

foreach ($arrPilMode as $pilihan => $lstData) {
	$optPilMode .= '<option value=' . $pilihan . '>' . $lstData . '</option>';
}

$arr = '##kdPt##kdUnit##thnBudget##kdBudget##pilMode';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>' . "\r\n" . '<script>' . "\r\n" . 'function getKdorg()' . "\r\n" . '{' . "\r\n\t" . 'kdPt=document.getElementById(\'kdPt\').options[document.getElementById(\'kdPt\').selectedIndex].value;' . "\r\n\t" . 'param=\'kdPt=\'+kdPt+\'&proses=getKdorg\';' . "\r\n\t" . 'tujuan="log_slave_2detail_pembelian.php";' . "\r\n\t" . '//alert(param);' . "\t\r\n" . '    ' . "\r\n\t" . ' function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n" . '                  ' . "\t" . 'document.getElementById(\'kdUnit\').innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    //' . "\r\n" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n" . '  post_response_text(tujuan, param, respon);' . "\r\n\r\n" . '}' . "\r\n" . 'function searchSupplier(title,content,ev)' . "\r\n" . '{' . "\r\n\t" . 'width=\'500\';' . "\r\n\t" . 'height=\'400\';' . "\r\n\t" . 'showDialog1(title,content,width,height,ev);' . "\r\n\t" . '//alert(\'asdasd\');' . "\r\n" . '}' . "\r\n" . 'function findSupplier()' . "\r\n" . '{' . "\r\n" . '    nmSupplier=document.getElementById(\'nmSupplier\').value;' . "\r\n" . '    param=\'proses=getSupplierNm\'+\'&nmSupplier=\'+nmSupplier;' . "\r\n" . '    tujuan=\'log_slave_save_po.php\';' . "\r\n" . '    post_response_text(tujuan, param, respog);' . "\t\t\t\r\n\r\n" . '    function respog(){' . "\r\n" . '            if (con.readyState == 4) {' . "\r\n" . '                    if (con.status == 200) {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                            }' . "\r\n" . '                            else {' . "\r\n" . '                                  document.getElementById(\'containerSupplier\').innerHTML=con.responseText;' . "\r\n" . '                        }' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            error_catch(con.status);' . "\r\n" . '                    }' . "\r\n" . '            }' . "\r\n" . '    }' . "\t\r\n" . '}' . "\r\n" . 'function setData(kdSupp)' . "\r\n" . '{' . "\r\n" . '    l=document.getElementById(\'kdSup\');' . "\r\n" . '    ' . "\r\n" . '    for(a=0;a<l.length;a++)' . "\r\n" . '        {' . "\r\n" . '            if(l.options[a].value==kdSupp)' . "\r\n" . '                {' . "\r\n" . '                    l.options[a].selected=true;' . "\r\n" . '                }' . "\r\n" . '        }' . "\r\n\t\t\r\n" . '       closeDialog();' . "\r\n\t" . '   get_supplier();' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['bgtMaterial'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id="thnBudget" name="thnBudget" style="width:150px">';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['pt'];
echo '</label></td><td><select id="kdPt" name="kdPt" style="width:150px" onchange="getKdorg()">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"><option value=\'\'>';
echo $_SESSION['lang']['all'];
echo '</option></select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kodebudget'];
echo '</label></td><td><select id="kdBudget" name="kdBudget" style="width:150px">';
echo $optSup;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['pilih'];
echo '</label></td><td><select id="pilMode" name="pilMode" style="width:150px">';
echo $optPilMode;
echo '</select></td></tr>' . "\r\n\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_laporan_budget_material\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'bgt_slave_laporan_budget_material.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
