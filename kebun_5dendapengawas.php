<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_5dendapengawas.js'></script>\r\n\r\n\r\n";
$optJabatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optJabatan .= "<option value='Mandor Satu'>Mandor Satu</option>";
$optJabatan .= "<option value='Kerani'>Kerani</option>";
$optJabatan .= "<option value='Mandor'>Mandor</option>";
$optJabatan .= "<option value='RECORDER'>RECORDER</option>";
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['dendapengawas'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kode']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=2 id=kode onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['nama']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text  id=nama onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jabatan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=jabatan style=\"width:150px;\">".$optJabatan."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['rp'].' '.$_SESSION['lang']['denda']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=denda onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>