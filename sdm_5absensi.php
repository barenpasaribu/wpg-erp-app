<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_5absensi.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['jenisabsensi']);
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeabs']."</td><td><input type=text id=kode size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['grup'].'</td><td><select id=grup><option value=0>'.$_SESSION['lang']['tidakdibayar'].'</option><option value=1>'.$_SESSION['lang']['dibayar']."</option></select></td></tr>\r\n     <tr><td>".$_SESSION['lang']['jumlahhk']."</td><td><input type=text id=jumlahhk size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=3 value=0 onblur=change_number(this)></td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo '<div>';
$str1 = "select *,\r\n\t     case kelompok when 1 then '".$_SESSION['lang']['dibayar']."'\r\n\t\t when 0 then '".$_SESSION['lang']['tidakdibayar']."'\r\n\t\t end as ketgroup \r\n\t     from ".$dbname.'.sdm_5absensi order by kodeabsen';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['kodeabs']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['grup']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlahhk']."</td>\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t           <td align=center>".$bar1->kodeabsen."</td>\r\n\t\t\t\t   <td>".$bar1->keterangan."</td>\r\n\t\t\t\t   <td>".$bar1->ketgroup."</td>\r\n\t\t\t\t   <td>".$bar1->nilaihk."</td>\r\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeabsen."','".$bar1->keterangan."','".$bar1->kelompok."','".$bar1->nilaihk."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>