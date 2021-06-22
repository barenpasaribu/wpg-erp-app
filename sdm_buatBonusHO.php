<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>Jasa Produksi:</b>');
echo '<div id=period>';
$optc = "<option value='".date('Y-m')."'>".date('m-Y').'</option>';
for ($v = -2; $v < 16; ++$v) {
    $per = mktime(0, 0, 0, date('m') - $v, 15, date('Y'));
    $optc .= '<option value='.date('Y-m', $per).'>'.date('m-Y', $per).'</option>';
}
echo "<fieldset style='width:500px'>\r\n \t\t\t\t <legend><b>Periode Bonus:</b>\r\n\t\t\t\t </legend>\r\n\t\t\t\t Periode pembayaran Bonus:<select id=periode>".$optc."</select><br>\r\n\t\t\t\t Dengan base gaji periode &nbsp : <select id=periodegaji>".$optc."</select><br>\t\r\n\t\t\t\t <button class=mybutton onclick=setBonusPeriod()>OK</button><br>\r\n\t\t\t\t </fieldset>";
echo '</div>';
CLOSE_BOX();
echo close_body();

?>