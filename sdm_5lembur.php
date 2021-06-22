<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_5lembur.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['tipelembur']);
$tipelembur = '';
$tipelembur = '<option value=0>'.$_SESSION['lang']['haribiasa']."</option>\r\n            <option value=1>".$_SESSION['lang']['hariminggu']."</option>\r\n\t\t\t<option value=2>".$_SESSION['lang']['harilibur']."</option>\r\n\t\t\t<option value=3>".$_SESSION['lang']['hariraya']."</option>\r\n\t\t\t";
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'>".substr($_SESSION['empl']['lokasitugas'], 0, 4)."</option></select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['tipelembur'].'</td><td><select id=tipelembur>'.$tipelembur."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['jamaktual']."</td><td><input type=text id=jamaktual size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=0 onblur=change_number(this)></td></tr>\r\n     <tr><td>".$_SESSION['lang']['jamlembur']."</td><td><input type=text id=jamlembur size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=0 onblur=change_number(this)></td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availfunct']);
echo '<div>';
$str1 = "select *,\r\n\t     case tipelembur when '0' then '".$_SESSION['lang']['haribiasa']."'\r\n\t\t when '1' then '".$_SESSION['lang']['hariminggu']."'\r\n\t\t when '2' then '".$_SESSION['lang']['harilibur']."'\r\n\t\t when '3' then '".$_SESSION['lang']['hariraya']."'\r\n\t\t end as ketgroup \r\n\t     from ".$dbname.".sdm_5lembur \r\n\t\t where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t order by tipelembur,jamaktual";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tipelembur']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jamaktual']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jamlembur']."</td>\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$bar1->kodeorg."</td>\r\n\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t   <td align=center>".$bar1->jamaktual."</td>\r\n\t\t\t\t   <td align=center>".$bar1->jamlembur."</td>\r\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."','".$bar1->jamlembur."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>