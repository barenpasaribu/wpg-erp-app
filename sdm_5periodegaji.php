<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_5periodegajiunit.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['periodepenggajian']);
$optPrd = "<option value=''></option>";
for ($x = 0; $x <= 12; ++$x) {
    $dte = mktime(0, 0, 0, (date('m') + 2) - $x, 15, date('Y'));
    $optPrd .= '<option value='.date('Y-m', $dte).'>'.date('m-Y', $dte).'</option>';
}
$metodepenggajian = '';
$metodepenggajian .= "<option value='H'>".$_SESSION['lang']['harian']."</option>\r\n\t\t    <option value='B'>".$_SESSION['lang']['bulanan']."</option>\t                 \r\n\t\t\t\t\t";
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'>".substr($_SESSION['empl']['lokasitugas'], 0, 4)."</option></select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['metodepanggajian'].'</td><td><select id=metodepenggajian>'.$metodepenggajian."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['periode'].'</td><td><select id=periode name=periode>'.$optPrd."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['tanggalmulai']."</td><td><input type=text id=tanggalmulai size=10 onkeypress=\"return false;\" class=myinputtext maxlength=10  onmouseover=setCalendar(this)></td></tr>\r\n     <tr><td>".$_SESSION['lang']['tanggalsampai']."</td><td><input type=text id=tanggalsampai size=10 onkeypress=\"return false;\" class=myinputtext maxlength=10 onmouseover=setCalendar(this)></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['tutup'].'</td><td><input type=checkbox id=tutup>'.$_SESSION['lang']['yes'].'/'.$_SESSION['lang']['no']."</td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo '<div>';
$str1 = "select *,\r\n\t     case jenisgaji when 'H' then '".$_SESSION['lang']['harian']."'\r\n\t\t when 'B' then '".$_SESSION['lang']['bulanan']."'\r\n\t\t end as ketgroup, \r\n\t\t case sudahproses when '1' then '".$_SESSION['lang']['yes']."'\r\n\t\t when '0' then '".$_SESSION['lang']['no']."'\r\n\t\t end as sts\r\n\t     from ".$dbname.".sdm_5periodegaji \r\n\t\t where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t order by periode desc";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:650px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['metodepanggajian']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalmulai']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tutup']."</td>\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$bar1->kodeorg."</td>\r\n\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t   <td align=center>".substr(tanggalnormal($bar1->periode), 1, 7)."</td>\r\n\t\t\t\t   <td align=center>".tanggalnormal($bar1->tanggalmulai)."</td>\r\n\t\t\t\t   <td align=center>".tanggalnormal($bar1->tanggalsampai)."</td>\r\n\t\t\t\t   <td align=center>".$bar1->sts."</td>\r\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenisgaji."','".$bar1->periode."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalsampai)."','".$bar1->sudahproses."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>