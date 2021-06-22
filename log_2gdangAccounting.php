<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function getUnit(){' . "\r\n" . '    pro=document.getElementById(\'ptId\');' . "\r\n" . '    prod=pro.options[pro.selectedIndex].value;' . "\r\n" . '    param=\'proses=getUnit\'+\'&kdPt=\'+prod;' . "\r\n" . '    tujuan=\'log_slave_2gdangAccounting.php\';' . "\r\n" . '        post_response_text(tujuan, param, respog);' . "\r\n\t" . 'function respog()' . "\r\n\t" . '{' . "\r\n" . '              if(con.readyState==4)' . "\r\n" . '              {' . "\r\n" . '                    if (con.status == 200) {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                            }' . "\r\n" . '                            else {' . "\r\n" . '                               //alert(con.responseText);' . "\r\n" . '                               document.getElementById(\'unitId\').innerHTML=con.responseText;' . "\r\n" . '                            }' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            error_catch(con.status);' . "\r\n" . '                    }' . "\r\n" . '              }' . "\t\r\n\t" . ' }  ' . "\r\n\t\t\r\n" . '}' . "\r\n" . 'function getUnit2(){' . "\r\n" . '    pro=document.getElementById(\'ptId2\');' . "\r\n" . '    prod=pro.options[pro.selectedIndex].value;' . "\r\n" . '    param=\'proses=getUnit\'+\'&ptId2=\'+prod;' . "\r\n" . '    tujuan=\'log_slave_2gdangAccounting2.php\';' . "\r\n" . '        post_response_text(tujuan, param, respog);' . "\r\n\t" . 'function respog()' . "\r\n\t" . '{' . "\r\n" . '              if(con.readyState==4)' . "\r\n" . '              {' . "\r\n" . '                    if (con.status == 200) {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                            }' . "\r\n" . '                            else {' . "\r\n" . '                               //alert(con.responseText);' . "\r\n" . '                               document.getElementById(\'unitId2\').innerHTML=con.responseText;' . "\r\n" . '                            }' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                            busy_off();' . "\r\n" . '                            error_catch(con.status);' . "\r\n" . '                    }' . "\r\n" . '              }' . "\t\r\n\t" . ' }  ' . "\r\n" . '}' . "\r\n\r\n" . '</script>' . "\r\n";
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['laporangudang']) . '</b>');
OPEN_BOX('', '<b>GUDANG VS ACCOUNTING</b>');
$optPt .= '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$spt =getQuery("pt");// 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qpt = mysql_query($spt)) || true;

while ($rpt = mysql_fetch_assoc($qpt)) {
	$optPt .= '<option value=\'' . $rpt['kodeorganisasi'] . '\'>' . $rpt['namaorganisasi'] . '</option>';
}

$optUnit = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sdr = 'select distinct left(tanggal,7) as periode from ' . $dbname . '.log_transaksiht where post=1 order by tanggal asc';

#exit(mysql_error($conn));
($qdr = mysql_query($sdr)) || true;

while ($rdr = mysql_fetch_assoc($qdr)) {
	$optPrdDr .= '<option value=\'' . $rdr['periode'] . '\'>' . $rdr['periode'] . '</option>';
}

$sdr = 'select distinct left(tanggal,7) as periode from ' . $dbname . '.log_transaksiht where post=1 order by tanggal desc';

#exit(mysql_error($conn));
($qdr = mysql_query($sdr)) || true;

while ($rdr = mysql_fetch_assoc($qdr)) {
	$optPrdSmp .= '<option value=\'' . $rdr['periode'] . '\'>' . $rdr['periode'] . '</option>';
}

$arr = '##ptId##unitId##prdIdDr##prdIdSmp';
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$arr2 = '##ptId2##unitId2##prdIdDr2##prdIdSmp2';
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['laporangudang'];
$hfrm[1] = $_SESSION['lang']['laporangudangakunting'];
drawTab('FRM', $hfrm, $frm, 200, 1050);
close_body();

?>
