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
echo '";' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear2()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget_afd\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit_afd\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer2\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear3()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget_sebaran\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnit_sebaran\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer3\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . 'function Clear5()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudgetCst\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdUnitCst\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer5\').innerHTML=\'\';' . "\r\n" . '}Clear5' . "\r\n" . '</script>' . "\r\n";
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
$arr3 = '##thnBudget_sebaran##kdUnit_sebaran';
$arr5 = '##thnBudgetCst##kdUnitCst';
OPEN_BOX('', '<b>' . $_SESSION['lang']['lapLangsungKlmpkAkun'] . '</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 2;
$frm .= 2;
$frm .= 2;
$optKd = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$_SESSION['empl']['tipelokasitugas'] == 'HOLDING' ? $sKd = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\'' : $sKd = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

exit(mysql_error($sKd));
($qList = mysql_query($sKd)) || true;

while ($rKd = mysql_fetch_assoc($qList)) {
	$optKd .= '<option value=\'' . $rKd['kodeorganisasi'] . '\'>' . $rKd['namaorganisasi'] . '</option>';
}

$frm .= 3;
$frm .= 3;
$frm .= 3;
$hfrm[0] = $_SESSION['lang']['thntnm'];
$hfrm[1] = $_SESSION['lang']['afdeling'];
$hfrm[2] = $_SESSION['lang']['sebaran'];
$hfrm[3] = $_SESSION['lang']['costelement'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
