<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_5natura.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['setupnatura']);
$str = 'select * from '.$dbname.'.sdm_5catuporsi order by kode';
$res = mysql_query($str);
$st = '';
while ($bar = mysql_fetch_object($res)) {
    $st .= "<option value='".$bar->kode."'>[".$bar->kode.']-'.$bar->keterangan.'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n                <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'>".substr($_SESSION['empl']['lokasitugas'], 0, 4)."</option></select></td></tr>\r\n                <tr><td>".$_SESSION['lang']['tahun'].'</td><td><input type=text id=tahun size=4 onkeypress="return angka_doang(event);" class=myinputtextnumber maxlength=4 value='.date('Y')."></td></tr>\r\n                <tr><td>".$_SESSION['lang']['kodekelompok'].'</td><td><select id=kode>'.$st."</option></select></td></tr>\r\n                 <tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jumlah size=4 onkeypress=\"return angka_doang(event);\" onkeyup=\"changeValueNatura()\" class=myinputtextnumber maxlength=4 value=1>Ltr</td></tr>\r\n\t <tr><td>Jumlah Uang</td><td><input type=text id=jumlahuang size=4 onkeypress=\"return angka_doang(event);\" onkeyup=\"changeValueRp()\" class=myinputtextnumber maxlength=10 value=1>Rp</td></tr>\r\n\t <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=45></td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo '<div>';
$str1 = "select *\r\n\t     from ".$dbname.".sdm_5catu \r\n\t\t   where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t  order by tahun desc,kelompok";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:700px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tahun']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['kodekelompok']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."  (Ltr)</td>\r\n\t\t\t<td>Jumlah Uang (Rp)</td>\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t        <td align=center>".$bar1->kodeorg."</td>\r\n                                        <td align=center>".$bar1->tahun."</td>\r\n                                        <td align=center>".$bar1->kelompok."</td>    \r\n                                         <td>".$bar1->keterangan."</td>    \r\n                                        <td align=right>".$bar1->jumlah."</td>\r\n <td align=right>".$bar1->jumlahuang."</td>\r\n                                        <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tahun."','".$bar1->kelompok."','".$bar1->keterangan."','".$bar1->jumlah."','".$bar1->jumlahuang."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>