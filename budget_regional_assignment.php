<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/budget_regional_assignment.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['input'].' '.$_SESSION['lang']['regional'].' Assignment');
$optorg = '';
$str = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi \r\n      where char_length(kodeorganisasi) = 4\r\n      order by kodeorganisasi";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.' - '.$bar->namaorganisasi.'</option>';
}
$optreg = '';
$str = 'select regional, nama from '.$dbname.".bgt_regional \r\n      order by regional";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optreg .= "<option value='".$bar->regional."'>".$bar->regional.' - '.$bar->nama.'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td><select onchange=\"resetcontainer();\" id=organisasi style='width:150px'><option value=''>".$optorg."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['regional']."</td><td><select onchange=\"resetcontainer();\" id=regional style='width:150px'><option value=''>".$optreg."</select></td></tr>\r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);
$str1 = 'select * from '.$dbname.'.bgt_regional_assignment order by kodeunit';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeorganisasi'].'</td><td>'.$_SESSION['lang']['regional']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td align=center>'.$bar1->kodeunit.'</td><td>'.$bar1->regional."</td><td align=center><img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"deleteDep('".$bar1->kodeunit."','".$bar1->regional."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>