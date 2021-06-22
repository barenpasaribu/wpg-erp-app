<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/pabrik_5fraksi.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['kodefraksi'].'</b>');
echo "<fieldset style='width:500px;'>
<legend>".$_SESSION['lang']['form']."</legend>
<table>\r\n     <tr><td>".$_SESSION['lang']['kodeabs']."</td><td><input type=text id=kode size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=nama size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n                    <tr><td>".$_SESSION['lang']['nama']."(EN)</td><td><input type=text id=nama1 size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n     <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
//echo open_theme();
echo '<div>';
$str1 = 'select * from '.$dbname.'.pabrik_5fraksi order by kode';
$res1 = mysql_query($str1);
echo "<br/>
<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>
<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeabs'].'</td><td>'.$_SESSION['lang']['nama'].'</td><td>'.$_SESSION['lang']['nama'].'(EN)</td><td>'.$_SESSION['lang']['satuan']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td align=center>'.$bar1->kode.'</td><td>'.$bar1->keterangan.'</td><td>'.$bar1->keterangan1.'</td><td>'.$bar1->type."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->keterangan."','".$bar1->type."','".$bar1->keterangan1."');\"></td></tr>";
}
echo "</tbody><tfoot></tfoot></table>
</fieldset>
</div>";
//echo close_theme();
CLOSE_BOX();
echo close_body();

?>