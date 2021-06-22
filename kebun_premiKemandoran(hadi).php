<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/kebun_premiMandor.js'></script>\r\n";
include 'master_mainMenu.php';
$str = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optPeriode .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
$str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='STMD'";
$res = mysql_query($str);
$jlh = '[not set]';
while ($bar = mysql_fetch_object($res)) {
    $jlh = $bar->nilai;
}
OPEN_BOX('', $_SESSION['lang']['premimandor']);
if ($_SESSION['language']=='EN') {
    $frm[0] = 'Make sure all harvesting transaction has been recorded completely on this date';
} else {
    $frm[0] = 'Pastikan semua transaksi (LP4) panen sudah diinput pada tanggal ini';
}

$frm[0] .= "<table class=sortable border=0 cellspacing=1><thead></thead>\r\n                        <tbody>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext onmouseover=setCalendar(this.id) id=tanggal onkeypress=\"return false\" onchange=ambilMandor(this.value)></td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['mandor']." (Karyawan)</td><td><select id=idkaryawan onchange=loadPremi(this.options[this.selectedIndex].value)></select></td></tr> \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['sumber'].' '.$_SESSION['lang']['upahpremi']."</td><td id=premipanen align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['orang']."</td><td id=anggota align=right>0</td></tr>\r\n                        \r\n                         <tr class=rowcontent><td>Devider(Pembagi)</td><td align=right><input type=text class=myinputtext style=\"text-align:right\" id=pembagi size=2 maxlength=2 onkeypress=\"return angka_doang(event)\" onblur=\"hitungpremimandor();\" disabled value=0></td></tr>\r\n                         <tr class=rowcontent><td>Computer Calculation</td><td id=komputer align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nilaipremi'].' '.$_SESSION['lang']['mandor']."</td><td align=right><input type=text id=premi class=myinputtextnumber maxlength=10 onkeypress=\"return angka_doang(event)\" disabled></td></tr>    \r\n              </tbody></table>\r\n              <button onclick=savePremiMandor() id=save1 class=mybutton>".$_SESSION['lang']['save'].'</button>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['list'].".</legend>\r\n              <div id=containerMANDORPANEN>\r\n              </div>\r\n              </fieldset>";
if ($_SESSION['language']=='EN') {
    $frm[1] = 'Make sure all Foreman premium has been recorded correctly as displayed in tab 1';
} else {
    $frm[1] = 'Pastikan semua premi mandor panen sudah diinput pada tanggal ini';
}

$frm[1] .= "<table class=sortable border=0 cellspacing=1><thead></thead>\r\n                        <tbody>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext onmouseover=setCalendar(this.id) id=tanggalmk onkeypress=\"return false\" onchange=ambilMandorKepala(this.value)></td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['mandor']."(Karyawan)</td><td><select id=idkaryawanmk onchange=loadPremiMK(this.options[this.selectedIndex].value)></select></td></tr> \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['premimandor']."</td><td id=premimandor align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['jumlah'].'  '.$_SESSION['lang']['orang']."</td><td id=anggotamk align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>Devider(Pembagi)</td><td align=right><input type=text class=myinputtext style=\"text-align:right\" id=pembagimk size=2 maxlength=2 onkeypress=\"return angka_doang(event)\" onblur=\"hitungpremimk();\" disabled value=0></td></tr>\r\n                         <tr class=rowcontent><td>Computer Calculation</td><td id=komputermk align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nilaipremi'].' '.$_SESSION['lang']['mandor']."</td><td align=right><input type=text id=premimk class=myinputtextnumber maxlength=10 onkeypress=\"return angka_doang(event)\" disabled></td></tr>    \r\n              </tbody></table>\r\n              <button onclick=savePremiMK() id=save2 class=mybutton>".$_SESSION['lang']['save'].'</button>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['list'].".</legend>\r\n              <div id=containerMANDOR1>\r\n              </div>\r\n              </fieldset>";
if ($_SESSION['language']=='EN') {
    $frm[2] = 'Make sure all Foreman premium has been recorded correctly as dilplayed in tab 1';
} else {
    $frm[2] = 'Pastikan semua Premi Mandor Panen sudah diinput pada tanggal ini';
}

$frm[2] .= "<table class=sortable border=0 cellspacing=1><thead></thead>\r\n                        <tbody>\r\n                         \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext onmouseover=setCalendar(this.id) id=tanggalKerani onkeypress=\"return false\" onchange=ambilKerani(this.value)></td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['kerani']." (Karyawan)</td><td><select id=idkaryawanKerani onchange=loadPremiKerani(this.options[this.selectedIndex].value)></select></td></tr> \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['total'].'  '.$_SESSION['lang']['sumber'].' '.$_SESSION['lang']['upahpremi']."</td><td id=premiPanenKerani align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['jumlah'].'  '.$_SESSION['lang']['orang']."</td><td id=anggotaKerani align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>Devider(Pembagi)</td><td align=right><input type=text class=myinputtext style=\"text-align:right\" id=pembagiKerani size=2 maxlength=2 onkeypress=\"return angka_doang(event)\" onblur=\"hitungpremikerani();\" disabled value=0></td></tr>\r\n                         <tr class=rowcontent><td>Computer Calculation</td><td id=komputerKerani align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nilaipremi'].' '.$_SESSION['lang']['k']."</td><td align=right><input type=text id=premiKerani class=myinputtextnumber maxlength=10 onkyepress=\"return angka_doang(event)\" disabled></td></tr>    \r\n              </tbody></table>\r\n              <button onclick=savePremiKerani() id=save2 class=mybutton>".$_SESSION['lang']['save'].'</button>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['list'].".</legend>\r\n              <div id=containerKERANI>\r\n              </div>\r\n              </fieldset>";

if ($_SESSION['language']=='EN') {
    $frm[3] = 'Make sure all Foreman premium has been recorded correctly as dilplayed in tab 1';
} else {
    $frm[3] = 'Pastikan semua Premi Mandor Panen sudah diinput pada tanggal ini';
}

$frm[3] .= "<table class=sortable border=0 cellspacing=1><thead></thead>\r\n                        <tbody>                         \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext onmouseover=setCalendar(this.id) id=tanggalKeraniPanen onkeypress=\"return false\" onchange=ambilKeraniPanen(this.value)></td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['kerani']." (Karyawan)</td><td><select id=idkaryawanKeraniPanen onchange=loadPremiKeraniPanen(this.options[this.selectedIndex].value)></select></td></tr> \r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['sumber'].' '.$_SESSION['lang']['upahpremi']."</td><td id=premiPanenKeraniPanen align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['orang']."</td><td id=anggotaKeraniPanen align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>Devider(Pembagi)</td><td align=right><input type=text class=myinputtext style=\"text-align:right\" id=pembagiKeraniPanen size=2 maxlength=2 onkeypress=\"return angka_doang(event)\" onblur=\"hitungpremikeranipanen();\" disabled value=0></td></tr>\r\n                         <tr class=rowcontent><td>Computer Calculation</td><td id=komputerKeraniPanen align=right>0</td></tr>\r\n                         <tr class=rowcontent><td>".$_SESSION['lang']['nilaipremi'].' '.$_SESSION['lang']['k']."</td><td align=right><input type=text id=premiKeraniPanen class=myinputtextnumber maxlength=10 onkyepress=\"return angka_doang(event)\"disabled></td></tr>    \r\n              </tbody></table>\r\n              <button onclick=savePremiKeraniPanen() id=save3 class=mybutton>".$_SESSION['lang']['save'].'</button>';
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['list'].".</legend>\r\n              <div id=containerKERANIPANEN>\r\n              </div>\r\n              </fieldset>";

for ($x = 0; $x < 15; $x++) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optperiode .= "<option value='".date('Y-m', $dt)."'>".date('m-Y', $dt).'</option>';
}
$frm[4] = "<fieldset style='width:400px;'><legeng>".$_SESSION['lang']['list']."</legend>\r\n             Periode<select id=periode>".$optperiode.'</select><button class=mybutton onclick=listAllPremi()>'.$_SESSION['lang']['view']."</button>\r\n                 <button class=mybutton onclick=getexcel(event,'kebun_slave_premiKemandoran.php')>".$_SESSION['lang']['excel']."</button>\r\n            </fieldset>";
$frm[4] .= '<fieldset><legend>'.$_SESSION['lang']['list'].".</legend>\r\n              <div id=containerALLLIST>\r\n              </div>\r\n              </fieldset>";
$hfrm[0] = '1 '.$_SESSION['lang']['mandor'];
$hfrm[1] = '2 '.$_SESSION['lang']['nikmandor1'];
$hfrm[2] = '3 Officer';
$hfrm[3] = '4 Kerani Produksi';
$hfrm[4] = '5 '.$_SESSION['lang']['list'].'/'.$_SESSION['lang']['posting'];
if ($_SESSION['language']=='EN') {
    echo "<fieldset style='width:600px;'><legend>Info</legend><b>\r\n         Fill and finish according to TAB order. If you disobey the order, the value of premium may false.</b>\r\n          </fieldset>";
} else {
    echo "<fieldset style='width:600px;'><legend>Info</legend><b>\r\n          Isi dan selesaikan input data permasing-masing tab sesuai urutannya. Jika ada yang terlewat penginputannya maka\r\n          Premi mandor atau kerani bisa menghasilkan angka yang salah.</b>\r\n          </fieldset>";
}

drawTab('FRM', $hfrm, $frm, 150, 900);
CLOSE_BOX();
echo close_body();

?>