<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_5blok.js\"></script>\r\n<script>\r\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n";
$optOrg2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe in ('AFDELING','BIBITAN') and induk='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";
$qOrg2 = mysql_query($sOrg2) || exit(mysql_error($conns));
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optOrg2 .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['anggaran'].' '.$_SESSION['lang']['blok'].'</b>');
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['bloklm'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n<input type=text class=myinputtextnumber id=thnAnggran name=thnAnggran maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['afdeling'].'</td><td>:</td><td><select id=idAfd name=idAfd style=width:150px;>'.$optOrg2."</select></td></tr>\r\n<tr><td colspan=3>\r\n<button class=mybutton id=save_kepala name=save_kepala onclick=cekData()>Preview</button>\r\n<button class=mybutton id=btlTmbl name=btlTmbl onclick=batal()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /><br />\r\n<div id=dataList style=display:none;>\r\n<fieldset><legend>".$_SESSION['lang']['list']."</legend>\r\n\r\n<div id=isiContainer>\r\n</div>\r\n</fildset>\r\n</div>\r\n\r\n";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td>".$_SESSION['lang']['kebun']."</td>\r\n            <td>".$_SESSION['lang']['afdeling']."</td>\r\n            <td>".$_SESSION['lang']['status']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containData><script>loadDataLama()</script>\r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['blokbr'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n<input type=text class=myinputtextnumber id=thnAnggranBr name=thnAnggranBr maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['afdeling'].'</td><td>:</td><td><select id=idAfdBr name=idAfdBr style=width:150px;>'.$optOrg2."</select></td></tr>\r\n\r\n\r\n<tr><td colspan=3>\r\n<button class=mybutton id=save_kepalaBr name=save_kepalaBr onclick=cekDataBr()>Preview</button>\r\n<button class=mybutton id=btlTmbl name=btlTmbl onclick=batalBr()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br />\r\n<div id=dataListBr style=display:none;>\r\n<fieldset><legend>".$_SESSION['lang']['list']."</legend>\r\n<!--<div id=isiContainer style=overflow:auto;height:650px;width:750px;>-->\r\n<div id=isiContainerBr>\r\n</div>\r\n</fildset>\r\n</div>\r\n<input type=hidden id=prosesBr name=prosesBr value=insert_baru >\r\n";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['tahun']."</td>\r\n            <td>".$_SESSION['lang']['kebun']."</td>\r\n            <td>".$_SESSION['lang']['afdeling']."</td>\r\n            <td>".$_SESSION['lang']['blok']."</td>\r\n            <td>".$_SESSION['lang']['hathnini']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td>".$_SESSION['lang']['statusblok']."</td>\r\n            <td>".$_SESSION['lang']['topografi']."</td>\r\n            <td>".$_SESSION['lang']['thntnm']."</td>\r\n            <td>".$_SESSION['lang']['lcthnini']."</td>\r\n            <td>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containDetail>\r\n\t\t";
$frm[1] .= '</tbody></table></fieldset>';
$optThn = "<option value=''>".$_SESSION['lang']['budgetyear'].'</option>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['blokcls'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['ttpBudget']."</td><td>:</td><td>\r\n<select id=thnBudget style='width:100px;'>".$optThn."</select></td></tr>\r\n\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=prosesClose() >".$_SESSION['lang']['proses']."</button>\r\n<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />\r\n</td></tr>\r\n</table>";
$frm[2] .= '</fieldset>';
$hfrm[0] = $_SESSION['lang']['bloklm'];
$hfrm[1] = $_SESSION['lang']['blokbr'];
$hfrm[2] = $_SESSION['lang']['close'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>