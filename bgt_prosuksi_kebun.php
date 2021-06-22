<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/bgt_prosuksi_kebun.js\'></script>' . "\r\n\r\n";
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optws = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optthnttp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optorgclose = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$lokasitugas = $_SESSION['empl']['lokasitugas'];
OPEN_BOX('', '<b>' . $_SESSION['lang']['produksikebun'] . '</b>');
echo '<br /><br /><fieldset style=\'float:left;\'>' . "\r\n" . '                <legend>' . $_SESSION['lang']['entryForm'] . '</legend> ' . "\r\n" . '                        <table border=0 cellpadding=1 cellspacing=1>' . "\r\n" . '                                <tr><td width=150>' . $_SESSION['lang']['budgetyear'] . '</td><td width=7>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress="return angka_doang(event);" style="width:125px;" maxlength=4 onblur=\'getKodeblok(0,0,0)\'></td></tr>' . "\r\n" . '                                <tr><td>' . $_SESSION['lang']['blok'] . '</td><td>:</td><td><select id=kdblok name=kdblok  onchange=ambil_pokok(0,0) style="width:125px;">' . $optOrg . '</select></td></tr>' . "\r\n" . '                                <tr><td>' . $_SESSION['lang']['pkkproduktif'] . ' </td><td>:</td><td><input type=text class=myinputtextnumber id=pokprod name=pokprod  style="width:125px;" readyonly onblur=jumlahkan() disabled></td></tr>' . "\r\n" . '                                <tr><td>' . $_SESSION['lang']['bjr'] . '</td><td>:</td><td><input type=text class=myinputtextnumber disabled id=bjr name=bjr onkeypress="return angka_doang(event);" style="width:125px;" readyonly onblur=jumlahkan()></td></tr>' . "\r\n" . '                                <tr><td>' . $_SESSION['lang']['jenjangpokoktahun'] . '</td><td>:</td><td><input type=text class=myinputtextnumber id=jjg name=jjg onkeypress="return angka_doang(event);" onblur=jumlahkan() style="width:125px;" readyonly  ></td></tr>' . "\r\n" . '                                <tr><td>' . $_SESSION['lang']['total'] . ' (Jjg)</td><td>:</td><td><input type=text class=myinputtextnumber id=total name=total onkeypress="return angka_doang(event);" style="width:125px;" readyonly disabled ></td></tr>' . "\r\n\r\n" . '                                <tr><td></td><td></td><td><br /><div id=tmblSave>' . "\r\n" . '                                        <button onclick=saveHead() class=mybutton name=saveDt id=saveDt>' . $_SESSION['lang']['save'] . '</button>' . "\t" . ' ' . "\r\n" . '                                        <button class=mybutton onclick=batal() name=btl id=btl>' . $_SESSION['lang']['cancel'] . '</button></div></td></tr>' . "\r\n" . '                        </table></fieldset><input type=hidden id=method value=saveData />';
echo '<fieldset  style=\'float:left\'><legend>' . $_SESSION['lang']['tutup'] . '</legend>' . "\r\n" . '    <table>' . "\r\n" . '                <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td>:</td><td><select id=thnttp style=\'widht:150px\'>' . $optthnttp . '</select></td></tr>' . "\r\n" . '                <tr><td>' . $_SESSION['lang']['unit'] . '</td><td>:</td><td><select id=lkstgs style=\'widht:150px\'>' . $optorgclose . '</select></td></tr>';
echo '<tr><td colspan=3><button class="mybutton"  id="saveData" onclick=\'closeBudget()\'>' . $_SESSION['lang']['tutup'] . '</button></td></tr></table></fieldset>';
$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
$tot = count($arrBln);
echo '<div id=\'printContainer\'></div>';
CLOSE_BOX();
OPEN_BOX();
$optTahunBudgetHeader = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optKodeBlokHeader = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
echo '<table><tr>';
echo '<td>' . $_SESSION['lang']['budgetyear'] . '</td><td>: <select id=\'thnbudgetHeader\' style=\'width:150px;\' onchange=\'ubah_list()\'>' . $optTahunBudgetHeader . '</select></td></tr>';
echo '<td>' . $_SESSION['lang']['kodeblok'] . '</td><td>: <select id=\'kodeblokHeader\' style=\'width:150px;\' onchange=\'ubah_list()\'>' . $optKodeBlokHeader . '</select></td></tr>';
echo '</tr></table>';
echo '<fieldset><legend><b>' . $_SESSION['lang']['list'] . '</b></legend>';
echo '<div id=\'contain\'>';
echo '<script>loadData()</script></div>';
echo '</fieldset>';
CLOSE_BOX();
close_body();

?>
