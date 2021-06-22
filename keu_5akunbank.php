<?php



require_once 'config/connection.php';
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/keu_5akunbank.js'></script>\r\n";
include 'master_mainMenu.php';
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$optAkun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sAkun = 'select  noakun,'.$zz.' from '.$dbname.".keu_5akun where noakun!='11102' and noakun like '11102%' order by noakun desc";
$qAkun = mysql_query($sAkun);
while ($rAkun = mysql_fetch_assoc($qAkun)) {
    $optAkun .= "<option value='".$rAkun['noakun']."'>".$rAkun['namaakun'].'</option>';
}
OPEN_BOX('');
echo "<fieldset style='width:500px;'><table>\r\n\t <tr><td>".$_SESSION['lang']['noakun'].'</td><td><select id=grup style=width:150px>'.$optAkun."</select></td></tr>\r\n     <tr><td>".$_SESSION['lang']['namaakun']."</td><td><input type=text id=jumlahhk maxlength=80 style=width:150px onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme('');
echo '<div>';
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
echo "<script>loadData()</script> </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>