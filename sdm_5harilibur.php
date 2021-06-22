<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/sdm_5harilibur.js'></script>\n";
include 'master_mainMenu.php';
OPEN_BOX('', '');
$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment \n                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg = mysql_query($sreg);
$rreg = mysql_fetch_assoc($qreg);
echo "<fieldset style='float:left;'><legend>".$_SESSION['lang']['harilibur']."</legend><table>\n     <tr><td>".$_SESSION['lang']['regional']."</td><td><input type=text class=myinputtext id=regId disabled value='".$rreg['regional']."' style=width:150px /></td></tr>\n\t <tr><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>\n\t <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=ktrngan onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=width:150px /></td></tr>     \n\t<tr><td><input type=\"radio\" id=ishariraya-a name=\"ishariraya\" value=\"1\"> Hari Raya</td><td><input type=\"radio\" id=ishariraya-b name=\"ishariraya\" value=\"0\"> Bukan Hari Raya</td></tr>     \n\t </table>\n\t <input type=hidden id=method value='insert'>\n         <input type=hidden id=tglOld value=''>\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\n\t </fieldset>";
echo "<fieldset style='clear:both;float:left;'><legend>".$_SESSION['lang']['data'].'</legend>';
echo '<table><tr>';
echo '<td>'.$_SESSION['lang']['tanggal']."</td>\n     <td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\n     <td>".$_SESSION['lang']['keterangan']."</td>\n     <td><input type=text id=ktrnganCr onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=width:150px /></td>\n     </tr></table><button class=mybutton onclick=loadData(0)>".$_SESSION['lang']['find'].'</button>';
echo '<div id=container><script>loadData(0)</script></div></fieldset>';
CLOSE_BOX();
echo close_body();

?>