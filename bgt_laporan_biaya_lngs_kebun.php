<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
$frm[3] = '';
$frm[4] = '';
echo '<script>' . "\r\n" . 'pilh=" ';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n\r\n" . '<script>' . "\r\n" . 'dataKdvhc="';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear2()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget_afd\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit_afd\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer2\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear3()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget_sebaran\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit_sebaran\').value=\'\';' . "\r\n" . '    document.getElementById(\'pilTampilan\').value=\'\';' . "\r\n" . '    document.getElementById(\'thnBudget_sebaran\').innerHTML=\'<option value=>\'+dataKdvhc+\'</option>\';' . "\r\n" . '    document.getElementById(\'printContainer3\').innerHTML=\'\';' . "\r\n" . '    document.getElementById(\'pdfSbrn\').disabled=false;' . "\r\n" . '}' . "\r\n" . 'function Clear5()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudgetCst\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnitCst\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer5\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function getTahunTanam()' . "\r\n" . '{' . "\r\n" . '    ' . "\r\n" . '    pil=document.getElementById(\'pilTampilan\').options[document.getElementById(\'pilTampilan\').selectedIndex].value;' . "\r\n" . '    th=document.getElementById(\'kdUnit_sebaran\').options[document.getElementById(\'kdUnit_sebaran\').selectedIndex].value;' . "\r\n" . '    thh=document.getElementById(\'thnBudget_sebaran\').options[document.getElementById(\'thnBudget_sebaran\').selectedIndex].value;' . "\r\n" . '    param=\'kdUnit_sebaran=\'+th+\'&thnBudget_sebaran=\'+thh+\'&pilTampilan=\'+pil;' . "\r\n" . '    if(th==\'\'||thh==\'\')' . "\r\n" . '        {' . "\r\n" . '            alert("Tahun Budget dan Unit Tidak Boleh Kosong");' . "\r\n" . '            return;' . "\r\n" . '        }' . "\r\n" . '    tujuan=\'bgt_slave_laporan_biaya_lngs_kebunSbrn.php\';' . "\r\n" . '    post_response_text(tujuan+\'?proses=getThnTanam\', param, respog);' . "\r\n\r\n" . '    function respog()' . "\r\n" . '    {' . "\r\n" . '                  if(con.readyState==4)' . "\r\n" . '                  {' . "\r\n" . '                            if (con.status == 200) {' . "\r\n" . '                                            busy_off();' . "\r\n" . '                                            if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                                            }' . "\r\n" . '                                            else {' . "\r\n" . '                                                    //alert(con.responseText);' . "\r\n" . '                                                    document.getElementById(\'thnTanamSeb\').innerHTML=con.responseText;' . "\r\n" . '                                                    if(pil!=\'\')' . "\r\n" . '                                                        {' . "\r\n" . '                                                            document.getElementById(\'pdfSbrn\').disabled=true;' . "\r\n" . '                                                        }' . "\r\n" . '                                                        else' . "\r\n" . '                                                            {' . "\r\n" . '                                                                document.getElementById(\'pdfSbrn\').disabled=false;' . "\r\n" . '                                                            }' . "\r\n" . '                                            }' . "\r\n" . '                                    }' . "\r\n" . '                                    else {' . "\r\n" . '                                            busy_off();' . "\r\n" . '                                            error_catch(con.status);' . "\r\n" . '                                    }' . "\r\n" . '                  }' . "\r\n" . '     }' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n";
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\' and tipe=\'KEBUN\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThn = 'select distinct  tahunbudget from ' . $dbname . '.bgt_budget order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optThn .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$arr = '##thnBudget##kdUnit';
$arr2 = '##thnBudget_afd##kdUnit_afd';
$arr3 = '##thnBudget_sebaran##kdUnit_sebaran##pilTampilan##thnTanamSeb';
$arr5 = '##thnBudgetCst##kdUnitCst';
$arr6 = '##thnBudgetRincian##kdUnitRincian';
OPEN_BOX('', '<b>' . $_SESSION['lang']['lapLangsung'] . '</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$arrPilDat = array(1 => 'Per Tahun Tanam');
$optPilD = '<option value=\'\'>Default</option>';
$optthntanam = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

foreach ($arrPilDat as $trPil => $ertl) {
	$optPilD .= '<option value=\'' . $trPil . '\'>' . $ertl . '</option>';
}

$frm .= 2;
$frm .= 2;
$frm .= 2;
$optKd = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKd = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\'';

exit(mysql_error($sKd));
($qList = mysql_query($sKd)) || true;

while ($rKd = mysql_fetch_assoc($qList)) {
	$optKd .= '<option value=\'' . $rKd['kodeorganisasi'] . '\'>' . $rKd['namaorganisasi'] . '</option>';
}

$frm .= 3;
$frm .= 3;
$frm .= 3;
$optKd = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKd = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\'';

exit(mysql_error($sKd));
($qList = mysql_query($sKd)) || true;

while ($rKd = mysql_fetch_assoc($qList)) {
	$optKd .= '<option value=\'' . $rKd['kodeorganisasi'] . '\'>' . $rKd['namaorganisasi'] . '</option>';
}

$frm .= 4;
$frm .= 4;
$frm .= 4;
$hfrm[0] = $_SESSION['lang']['thntnm'];
$hfrm[1] = $_SESSION['lang']['afdeling'];
$hfrm[2] = $_SESSION['lang']['sebaran'];
$hfrm[3] = $_SESSION['lang']['costelement'];
$hfrm[4] = 'Budget Rincian';
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
