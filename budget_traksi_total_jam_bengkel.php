<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript1.2 src=js/budget_traksi_total_jam_bengkel.js></script>\r\n\r\n\r\n";
include 'master_mainMenu.php';
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI' order by namaorganisasi asc";
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$optws = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='width:380px;'>\r\n     <legend><b>".$_SESSION['lang']['totJamBengkel']."</b></legend>\r\n\t <table>\r\n\t\t <tr><td width=100>".$_SESSION['lang']['budgetyear']."</td><td width=10>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress=\"return angka_doang(event);\" style=\"width:250px;\" maxlength=4 /></td></tr>\r\n\t\t <tr><td>".$_SESSION['lang']['kodetraksi'].' </td><td width=10>:</td><td><select id=kdorg name=kdorg onchange=getws(0,0) style="width:250px;">'.$optOrg."</select></td></tr>\r\n\t\t <tr><td>".$_SESSION['lang']['workshop'].'</td><td width=10>:</td><td><select id=kdtrak name=kdtrak style="width:250px;">'.$optws."</select></td></tr>\r\n\t\t <tr><td>".$_SESSION['lang']['totJamThn']."</td><td width=10>:</td><td><input type=text class=myinputtextnumber  id=totjamthn name=totjamthn onkeypress=\"return angka_doang(event);\" style=\"width:250px;\" /></td></tr>\r\n\t <tr>\r\n\t </table>\r\n\t \r\n\t <table>\r\n\t <tr>\r\n\t <td width=113></td><td>\r\n\t\t <div id=tmblSave>\r\n\t\t <button onclick=saveHead() class=mybutton name=saveDt id=saveDt>".$_SESSION['lang']['save']."</button>\t \r\n     \t <button class=mybutton onclick=batal() name=btl id=btl>".$_SESSION['lang']['cancel']."</button></div>\r\n\t</td></tr>\r\n \t</table>\r\n     </fieldset><input type=hidden id=method value=saveData />";
echo "\r\n\r\n";
echo "<div id='printContainer' style=display:none;>\r\n      <fieldset style='clear:both;float: left;'><legend>".$_SESSION['lang']['sebaran'].' '.$_SESSION['lang']['bulanan'].'</legend>';
$arrBln = [1 => substr($_SESSION['lang']['jan'], 0, 3), 2 => substr($_SESSION['lang']['peb'], 0, 3), 3 => substr($_SESSION['lang']['mar'], 0, 3), 4 => substr($_SESSION['lang']['apr'], 0, 3), 5 => substr($_SESSION['lang']['mei'], 0, 3), 6 => substr($_SESSION['lang']['jun'], 0, 3), 7 => substr($_SESSION['lang']['jul'], 0, 3), 8 => substr($_SESSION['lang']['agt'], 0, 3), 9 => substr($_SESSION['lang']['sep'], 0, 3), 10 => substr($_SESSION['lang']['okt'], 0, 3), 11 => substr($_SESSION['lang']['nov'], 0, 3), 12 => substr($_SESSION['lang']['dec'], 0, 3)];
$tot = count($arrBln);
echo '<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader >';
foreach ($arrBln as $brs => $dtBln) {
    echo '<td align=center>'.$dtBln.'</td>';
}
echo '<td>'.$_SESSION['lang']['save'].'</td></tr></thead>';
echo '<tbody><tr class=rowcontent>';
foreach ($arrBln as $brs2 => $dtBln2) {
    echo "<td><input type='text' class='myinputtextnumber'  id=jam_x".$brs2." value=0 style='width:50px' onkeypress=\"return angka_doang(event);\" /></td>";
}
echo "<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveJam(".$tot.")\" src='images/save.png'/></td>";
echo '</tr></tbody></table></fieldset></div>';
CLOSE_BOX();
echo "\r\n\r\n";
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['list'].'</legend>';
echo '<div id=contain><script>loadData()</script></div></fieldset>';
CLOSE_BOX();
echo close_body();

?>