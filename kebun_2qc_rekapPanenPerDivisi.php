<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/kebun_2qc_rekapPanenPerDivisi.js'></script>\r\n\r\n\r\n\r\n";
$oprReg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select * from '.$dbname.'.bgt_regional';
$h = mysql_query($g);
while ($i = mysql_fetch_assoc($h)) {
    $oprReg .= "<option value='".$i['regional']."'>".$i['nama'].'</option>';
}
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct substr(tanggalcek,1,7) as periode from '.$dbname.'.kebun_qc_panenht order by periode desc limit 10';
$j = mysql_query($i);
while ($k = mysql_fetch_assoc($j)) {
    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';
}
echo "\r\n\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['cek'].' '.$_SESSION['lang']['panen'].' '.$_SESSION['lang']['divisi']."</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['regional']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=reg style='width:200px;'>".$oprReg."</select></td>\r\n\t</tr>\r\n\t\r\n\t\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=per style='width:200px;'>".$optPer."</select></td>\r\n\t</tr>\r\n\t\r\n\t<tr>\r\n\t\t<td colspan=100>&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=5 align=center>\r\n\r\n\t\t<img src='images/icons/Basic_set_Png/statistics_16.png' style='width:50px;' title='Graphics'  onclick=graph(event)>\r\n\t\t  \r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>";
CLOSE_BOX();
echo close_body();

?>