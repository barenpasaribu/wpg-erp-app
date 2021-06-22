<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/sdm_payrollHO.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/payroll.css>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>PAYROLL ENTRY:</b>');
echo '<div id=period>';
$optc = '<option value=\'' . date('Y-m') . '\'>' . date('m-Y') . '</option>';
$v = -2;

while ($v < 3) {
	$per = mktime(0, 0, 0, date('m') - $v, 15, date('Y'));
	$optc .= '<option value=' . date('Y-m', $per) . '>' . date('m-Y', $per) . '</option>';
	++$v;
}

echo '<fieldset style=\'width:300px\'>' . "\r\n" . ' ' . "\t\t\t\t" . ' <legend><b>Periode Penggajian:</b>' . "\r\n\t\t\t\t" . ' </legend>' . "\r\n\t\t\t\t" . ' Pilih Periode Penggajian:<select id=periode>' . $optc . '</select>' . "\r\n\t\t\t\t" . ' <button class=mybutton onclick=setPayrollPeriod()>OK</button>' . "\r\n\t\t\t\t" . ' </fieldset>';
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
