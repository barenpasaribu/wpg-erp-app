<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>THR:</b>');
echo '<div id=period>';
$optc = "<option value='".date('Y-m')."'>".date('m-Y').'</option>';
for ($v = -2; $v < 3; ++$v) {
    $per = mktime(0, 0, 0, date('m') - $v, 15, date('Y'));
    $optc .= '<option value='.date('Y-m', $per).'>'.date('m-Y', $per).'</option>';
}
echo "<fieldset style='width:500px'>\r\n \t\t\t\t <legend><b>Periode THR:</b>\r\n\t\t\t\t </legend>\r\n\t\t\t\t Pilih Periode pembayaran THR:<select id=periode>".$optc."</select>\r\n\t\t\t\t Tanggal Hari Raya:<input type text id=tglthr onmousemove=setCalendar(this.id) class=myinputtext size=10 onkeypress=\"return false;\" value=".date('d-m-Y').">\r\n\t\t\t\t <button class=mybutton onclick=setTHRPeriod()>OK</button><br>\r\n\t\t\t\t Note:<i>Tanggal THR berfungsi untuk menghitung masa kerja pada perhitungan proporsional gaji ke THR.</i>\r\n\t\t\t\t </fieldset>";
echo '</div>';
CLOSE_BOX();
echo close_body();

?>