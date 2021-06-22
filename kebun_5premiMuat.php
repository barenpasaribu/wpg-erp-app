<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_5premiMuat.js'></script>\r\n\r\n\r\n";
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select * from '.$dbname.'.setup_kegiatan order by namakegiatan asc';
$n = mysql_query($i) ;
while ($d = mysql_fetch_assoc($n)) {
    $optKeg .= "<option value='".$d['kodekegiatan']."'>".$d['kodekegiatan'].' '.$d['namakegiatan'].'</option>';
}
$optTipe = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optTipe .= "<option value='D'>Dump Truck</option>";
$optTipe .= "<option value='F'>Fuso</option>";
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['premimuat'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text  id=regional onkeypress=\"return char_only(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodekegiatan']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kodekegiatan style=\"width:150px;\">".$optKeg."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['volume']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=volume onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['rupiahsatuan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=rupiah onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['tipe']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=tipe style=\"width:150px;\">".$optTipe."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jumlahhari']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=jumlahhari onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>