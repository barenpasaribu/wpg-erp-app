<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript1.2 src='js/keu_5kursbulanan.js'></script>\r\n";
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optMt = $optPeriode;
$sPrd = 'select distinct periode from '.$dbname.".setup_periodeakuntansi \r\n               where tutupbuku=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$qPrd = mysql_query($sPrd);
while ($rPrd = mysql_fetch_assoc($qPrd)) {
    $optPeriode .= "<option value='".$rPrd['periode']."'>".$rPrd['periode'].'</option>';
}
$sPrd = 'select distinct kode from '.$dbname.".setup_matauang \r\n               where kode!='IDR' order by kode asc";
$qPrd = mysql_query($sPrd);
while ($rPrd = mysql_fetch_assoc($qPrd)) {
    $optMt .= "<option value='".$rPrd['kode']."'>".$rPrd['kode'].'</option>';
}
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['kursbulanan'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['periode']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=periodeDt style=\"width:150px;\">".$optPeriode."</select></td>\r\n\t\t\t\t</tr>\r\n\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['matauang']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=mtUang style=\"width:150px;\">".$optMt."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kurs']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=krsDt onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>".$_SESSION['lang']['reset']."</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t              <input type=hidden id=periodeold value=''>\r\n                      <input type=hidden id=mtUangold value=''>\r\n                      <input type=hidden id=method value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset  style=float:left;clear:both;><legend>'.$_SESSION['lang']['find'].' '.$_SESSION['lang']['data'].'</legend>';
echo '<table>';
echo '<tr><td>'.$_SESSION['lang']['periode']."</td><td><input type=text id=periodeCr style=\"width:150px;\" onfocus='bersihField()' onblur=loadData(0) /></td>";
echo "<td>ex. 2013-03</td>\r\n     <td>&nbsp;</td></tr>";
echo '<tr><td>'.$_SESSION['lang']['matauang'].'</td><td><select id=mtUangCr style="width:150px;">'.$optMt.'</select></td>';
echo "<td>&nbsp;</td>\r\n     <td>&nbsp;</td></tr>";
echo '<tr><td colspan=4><button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button></td></tr>';
echo '</table></fieldset>';
echo "<fieldset style=float:left;clear:both;>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData(0)</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>