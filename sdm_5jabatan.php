<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/jabatan.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['jobfunction']);
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['functioncode']."</td><td><input type=text id=kodejabatan size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['functionname']."</td><td><input type=text id=namajabatan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availfunct']);
echo '<div id=container>';
$str1 = 'select * from '.$dbname.'.sdm_5jabatan order by kodejabatan';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['functioncode'].'</td><td>'.$_SESSION['lang']['functionname']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td align=center>'.$bar1->kodejabatan.'</td><td>'.$bar1->namajabatan."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->namajabatan."');\">&nbsp;<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"DeleteField('".$bar1->kodejabatan."','".$bar1->namajabatan."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>