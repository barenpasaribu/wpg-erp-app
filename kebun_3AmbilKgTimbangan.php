<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['AmbilKgTimbangan'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"application/javascript\" src=\"js/kebun_3AmbilKgTimbangan.js\"></script>\r\n";
$lksi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sKbn = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$lksi."'";
$qKbn = mysql_query($sKbn) ;
while ($rKbn = mysql_fetch_assoc($qKbn)) {
    $optKbn .= '<option value='.$rKbn['kodeorganisasi'].'>'.$rKbn['namaorganisasi'].'</option>';
}
echo "<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"entryForm\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['entryForm'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<td>";
echo $_SESSION['lang']['kebun'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"idKbn\" name=\"idKbn\" style=\"width:150px;\">";
echo $optKbn;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tglNospb'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"tglData\" name=\"tglData\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n\r\n<tr>\r\n<td colspan=\"3\" id=\"tmblHeader\">\r\n<button class=mybutton id='dtl_pem' onclick='saveData()'>";
echo $_SESSION['lang']['save'];
echo "</button><button class=mybutton id='cancel_gti' onclick='cancelSave()'>Reset</button>\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n</div>\r\n\r\n";
CLOSE_BOX();
echo "<div id=\"result\" style=\"display:none;\">\r\n";
OPEN_BOX();
echo "<div id=\"list_ganti\" >\r\n\r\n\r\n\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>