<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<script language=javascript src='js/kebun_premiMuatTbs.js'></script>\r\n\r\n\r\n";
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT distinct periode FROM '.$dbname.'.sdm_5periodegaji ORDER BY periode DESC limit 12';
$qry = mysql_query($sql);
while ($data = mysql_fetch_assoc($qry)) {
    $optPer .= '<option value='.$data['periode'].'>'.$data['periode'].'</option>';
}
echo "\r\n\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$arr = '##kodeorg##per';
echo "<fieldset style='float:left;'><legend><b>Premi Kehadiran</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><input type=text  id=kodeorg onkeypress=\"return char_only(event);\" disabled value='".$_SESSION['empl']['lokasitugas']."' class=myinputtext style=\"width:150px;\"></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>Periode</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=per style='width:155px;'>".$optPer."</select></td>\r\n\t</tr>\r\n\t\r\n\t<tr>\r\n\t\t<td colspan=100>&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=100>\r\n\t\t<button id=tPreview onclick=zPreview('kebun_slave_premiMuatTbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t\r\n\t\t\r\n\t\t<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>\r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>";
echo "\r\n<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['list']."</b></legend>\r\n<div id='printContainer'>\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>