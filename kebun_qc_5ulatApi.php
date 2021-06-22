<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_qc_5ulatApi.js'></script>\r\n\r\n\r\n";
$optKret = "<option value='ringan'>Ringan</option>";
$optKret .= "<option value='sedang'>Sedang</option>";
$optKret .= "<option value='berat'>Berat</option>";
$optUlat = "<option value='jlhdarnatrima'>Darna Trima</option>";
$optUlat .= "<option value='jlhsetothosea'>Setothosea Asigna</option>";
$optUlat .= "<option value='jlhsetoranitens'>Setora Nitens</option>";
$optUlat .= "<option value='jlhulatkantong'>Ulat Kantong</option>";
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['dendapengawas'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>Ulat</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=ulat style='width:200px;'>".$optUlat."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>Kriteria</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kret style='width:200px;'>".$optKret."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>Minimal</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=minu onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>Maksimal</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=maxu onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>