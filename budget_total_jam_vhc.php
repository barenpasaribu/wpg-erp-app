<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$bhs = $_SESSION['lang'];
$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' order by namaorganisasi asc";
$qOrg = mysql_query($sOrg) || exit(mysql_error($conns));
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optUnit .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['kodeorganisasi'].' - '.$rOrg['namaorganisasi'].'</option>';
}
$optSup = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sSup = 'select supplierid,namasupplier from '.$dbname.".log_5supplier where substring(kodekelompok,1,1)='S' order by namasupplier asc";
$qSup = mysql_query($sSup) || exit(mysql_error($conns));
while ($rSup = mysql_fetch_assoc($qSup)) {
    $optSup .= '<option value='.$rSup['supplierid'].'>'.$rSup['namasupplier'].'</option>';
}
$optLokal = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKdtraksi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sTraksi = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI' order by namaorganisasi asc";
$qTraksi = mysql_query($sTraksi) || exit(mysql_error($conns));
while ($rTraksi = mysql_fetch_assoc($qTraksi)) {
    $optKdtraksi .= '<option value='.$rTraksi['kodeorganisasi'].'>'.$rTraksi['namaorganisasi'].'</option>';
}
echo '<script>save="';
echo $_SESSION['lang']['save'];
echo '";btl="';
echo $_SESSION['lang']['cancel'];
echo '";pilih="';
echo $_SESSION['lang']['pilihdata'];
echo "\";</script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/budget_total_jam_vhc.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['totJamKendBudget'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['budgetyear'];
echo "</label></td><td><input type=\"text\" id=\"thnBudget\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event);\" style=\"width:150px\" maxlength=\"4\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['kodetraksi'];
echo '</label></td><td><select id="kdTraksi" name="kdTraksi" style="width:150px" onchange="getKdvhc(0,0)">';
echo $optKdtraksi;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['kodevhc'];
echo '</label></td><td><select id="kdVhc" name="kdVhc" style="width:150px">';
echo $optLokal;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit" style="width:150px;">';
echo $optUnit;
echo "</select></td></tr>\r\n<tr><td>";
echo $_SESSION['lang']['totJamThn'];
echo "</td><td><input type=\"text\" id=\"totJamThn\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event);\" style=\"width:150px\"  /></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><div id=\"tmblSave\"><button onclick=\"saveHead()\" class=\"mybutton\" name=\"saveDt\" id=\"saveDt\">";
echo $_SESSION['lang']['save'];
echo "</button>\r\n        <button onclick=\"batal()\" class=\"mybutton\" name=\"btl\" id=\"btl\">";
echo $_SESSION['lang']['cancel'];
echo "</button></div>\r\n</td></tr>\r\n\r\n</table><input type=\"hidden\" id=\"proses\" value=\"saveData\"/>\r\n</fieldset>\r\n</div>\r\n      <br />\r\n      <br />\r\n<div id='printContainer' style=\"display:none;\">\r\n      <fieldset style='clear:both;float: left;'><legend>Sebaran Bulanan</legend>\r\n\r\n";
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$tot = count($arrBln);
echo '<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>';
foreach ($arrBln as $brs => $dtBln) {
    echo '<td>'.$dtBln.'</td>';
}
echo '<td>action</td></tr></thead><tbody><tr class=rowcontent>';
foreach ($arrBln as $brs2 => $dtBln2) {
    echo "<td><input type='text' id=jam_x".$brs2.' class="myinputtextnumber" style="width:50px;" /></td>';
}
echo "<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveJam(".$tot.")\" src='images/save.png'/></td></tr></tbody></table>";
echo "</fieldset></div>\r\n\r\n";
CLOSE_BOX();
OPEN_BOX();
$optThnBudget = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKdvhc = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sThnBudget = 'select distinct tahunbudget from '.$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget = mysql_query($sThnBudget) || exit(mysql_error($conns));
while ($rThnBudget = mysql_fetch_assoc($qThnBudget)) {
    $optThnBudget .= "<option value='".$rThnBudget['tahunbudget']."'>".$rThnBudget['tahunbudget'].'</option>';
}
$sThnBudget2 = 'select distinct unitalokasi from '.$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget2 = mysql_query($sThnBudget2) || exit(mysql_error($conns));
while ($rThnBudget2 = mysql_fetch_assoc($qThnBudget2)) {
    $optUnit .= "<option value='".$rThnBudget2['unitalokasi']."'>".$rThnBudget2['unitalokasi'].'</option>';
}
$sThnBudget3 = 'select distinct kodevhc from '.$dbname.".bgt_vhc_jam where  kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qThnBudget3 = mysql_query($sThnBudget3) || exit(mysql_error($conns));
while ($rThnBudget3 = mysql_fetch_assoc($qThnBudget3)) {
    $optKdvhc .= "<option value='".$rThnBudget3['kodevhc']."'>".$rThnBudget3['kodevhc'].'</option>';
}
echo '<fieldset><legend>'.$_SESSION['lang']['datatersimpan'].'</legend>';
echo '<table><tr><td>'.$_SESSION['lang']['budgetyear'].' <select id=thndBudgetHead onchange=loadData()>'.$optThnBudget.'</select></td>';
echo '<td>'.$_SESSION['lang']['kodevhc'].' <select id=kdVhcHead onchange=loadData()>'.$optKdvhc.'</select></td>';
echo '<td>'.$_SESSION['lang']['unit'].' <select id=kdUnit onchange=loadData()>'.$optUnit.'</select></td>';
echo '</tr></table><div id=contain><script>loadData()</script></div></fieldset>';
CLOSE_BOX();
echo close_body();

?>