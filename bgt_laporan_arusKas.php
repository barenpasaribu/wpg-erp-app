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
echo '<script>' . "\r\n" . 'pilh=" ';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n\r\n" . '<script>' . "\r\n" . 'dataKdvhc="';
echo $_SESSION['lang']['pilihdata'];
echo '";' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget1\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit1\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer1\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear2()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget2\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit2\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer2\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear3()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget3\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer3\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear4()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget4\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit4\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer4\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear5()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget5\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer5\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function getUnit(n)' . "\r\n" . '{' . "\r\n\t" . 'kdPt=document.getElementById(\'kdPt\'+n).options[document.getElementById(\'kdPt\'+n).selectedIndex].value;' . "\r\n\t" . 'param="kodePt="+kdPt+"&proses=getUnit";' . "\r\n\t" . 'tujuan="bgt_slave_laporan_arusKas_neraca.php";' . "\r\n\t" . '//alert(param);' . "\t\r\n" . '    ' . "\r\n\t" . ' function respon() {' . "\r\n" . '        if (con.readyState == 4) {' . "\r\n" . '            if (con.status == 200) {' . "\r\n" . '                busy_off();' . "\r\n" . '                if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                    alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                } else {' . "\r\n" . '                    // Success Response' . "\r\n\t\t\t\t" . '//' . "\t" . 'alert(con.responseText);' . "\r\n" . '                  ' . "\t" . 'document.getElementById(\'kdUnit\'+n).innerHTML=con.responseText;' . "\r\n" . '                }' . "\r\n" . '            } else {' . "\r\n" . '                busy_off();' . "\r\n" . '                error_catch(con.status);' . "\r\n" . '            }' . "\r\n" . '        }' . "\r\n" . '    }' . "\r\n" . '    //' . "\r\n" . '  //  alert(fileTarget+\'.php?proses=preview\', param, respon);' . "\r\n" . '  post_response_text(tujuan, param, respon);' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n";
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optPt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe = \'PT\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optPt .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThn = 'select distinct  tahunbudget from ' . $dbname . '.bgt_summary_biaya_vw order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optThn .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$arr1 = '##thnBudget1##kdPt1##kdUnit1';
$arr2 = '##thnBudget2##kdPt2##kdUnit2';
$arr3 = '##thnBudget3##kdPt3##kdUnit3';
$arr4 = '##thnBudget4##kdUnit4';
$arr5 = '##thnBudget5';
OPEN_BOX('', '<b>' . $_SESSION['lang']['aruskas'] . ' ' . $_SESSION['lang']['anggaran'] . '</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 2;
$frm .= 2;
$frm .= 2;
$optPt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sPt = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qPt = mysql_query($sPt)) || true;

while ($rPt = mysql_fetch_assoc($qPt)) {
	$optPt .= '<option value=' . $rPt['kodeorganisasi'] . '>' . $rPt['namaorganisasi'] . '</option>';
}

$frm .= 3;
$frm .= 3;
$frm .= 3;
$frm .= 4;
$frm .= 4;
$frm .= 4;
$hfrm[0] = $_SESSION['lang']['neraca'];
$hfrm[1] = $_SESSION['lang']['labarugi'];
$hfrm[2] = $_SESSION['lang']['proyeksiaruskas'] . ' ' . $_SESSION['lang']['sebaran'];
$hfrm[3] = $_SESSION['lang']['proyeksiaruskas'] . ' ' . $_SESSION['lang']['perpt'];
$hfrm[4] = $_SESSION['lang']['proyeksiaruskas'] . ' ' . $_SESSION['lang']['konsolpt'];
drawTab('FRM', $hfrm, $frm, 150, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
