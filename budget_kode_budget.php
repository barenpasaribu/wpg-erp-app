<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/budget_kode_budget.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['input'].' '.$_SESSION['lang']['kodeanggaran']);
$optAkun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('ID' === $_SESSION['language']) {
    $dd = 'namaakun';
} else {
    $dd = 'namaakun1 as namaakun';
}

$sJns = 'select noakun,'.$dd.' from '.$dbname.'.keu_5akun where detail=1 order by noakun asc';
$qJns = mysql_query($sJns);
//echo $sJns;
while ($rJns = mysql_fetch_assoc($qJns)) {
    $optAkun .= "<option value='".$rJns['noakun']."'>".$rJns['noakun'].' - ['.$rJns['namaakun'].']</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td><input type='text' id='kode' class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style='width:150px' /></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=nama size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n             <tr><td>".$_SESSION['lang']['noakun']."</td><td><select id='noakunId' style='width:150px'>".$optAkun."</select></td></tr>\r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);
$str1 = 'select * from '.$dbname.".bgt_kode   order by kodebudget \t";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeanggaran'].'</td><td>'.$_SESSION['lang']['nama'].'</td><td>'.$_SESSION['lang']['noakun']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td align=center>'.$bar1->kodebudget.'</td><td>'.$bar1->nama.'</td><td>'.$bar1->noakun."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodebudget."','".$bar1->nama."','".$bar1->noakun."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>