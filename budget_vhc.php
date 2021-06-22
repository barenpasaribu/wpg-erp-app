<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "<script>\r\npilh=\" ";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_vhc.js\"></script>\r\n<script>\r\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n";
$optTraksi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optVhc = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sVhc = 'select kodevhc from '.$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%'";
$qVhc = mysql_query($sVhc);
while ($rVhc = mysql_fetch_assoc($qVhc)) {
    $optVhc .= "<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc'].'</option>';
}
$sTraksi = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI'";
$qTraksi = mysql_query($sTraksi);
while ($rTraksi = mysql_fetch_assoc($qTraksi)) {
    $optTraksi .= "<option value='".$rTraksi['kodeorganisasi']."'>".$rTraksi['namaorganisasi'].'</option>';
}
$optKdbdgt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select * from '.$dbname.".bgt_upah where closed=0 and kodeorg LIKE '".substr($_SESSION['empl']['lokasitugas'], 0, 3)."%'";
$qOrg2 = mysql_query($sOrg2);
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optKdbdgt .= '<option value='.$rOrg2['golongan'].'>'.$rOrg2['kodeorg'].' - '.$rOrg2['golongan'].'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['anggaran'].'  Kendaraan/Mesin/AB</b>');
echo "<br /><br /><fieldset style='float:left;'><legend>".$_SESSION['lang']['form'].'</legend> <table border=0 cellpadding=1 cellspacing=1>';
echo '<tr><td>'.$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='TRK' id='tipeBudget' style=width:150px; /></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' /></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['kodetraksi']."</td><td><select style='width:150px;' id='kdTraksi'>".$optTraksi.'</select></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['kodevhc']."</td><td><select style='width:150px;' id='kodeVhc'>".$optVhc.'</select></td></tr>';
echo "<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData\" onclick='saveData()'>".$_SESSION['lang']['save']."</button><button  class=\"mybutton\"  id=\"newData\" onclick='newData()'>".$_SESSION['lang']['baru'].'</button></td></tr>';
echo '</table></fieldset>';

CLOSE_BOX();
$optData = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sThn = 'select distinct tahunbudget from '.$dbname.".bgt_budget where  kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK'";
$qTHn = mysql_query($sThn);
while ($rThn = mysql_fetch_assoc($qTHn)) {
    $optData .= "<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget'].'</option>';
}
echo "<div id='listDatHeader' style='display:block'>";
OPEN_BOX();
echo "<table  style='display:none;'><tr>\r\n    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='dataHeader()'>".$optData."</select></td>\r\n    <td>".$_SESSION['lang']['kodevhc']." <select id=kdVhcHead style='width:100px' onchange='dataHeader()'>".$optVhc."</select></td>\r\n    \r\n    </tr></table>";
echo "<div id='listDatHeader2' style='display: none;'><script>dataHeader()</script></div>";
CLOSE_BOX();
echo "</div><div id='formIsian' style='display:none;'>";
OPEN_BOX();
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['sdm'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n<select id='kdBudget' style='width:150px;' onchange='jumlahkan(1)'>".$optKdbdgt."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['hkefektif']."</td><td>:</td><td><input type='text' class='myinputtextnumber' disabled style='width:150px;' id='hkEfektif'  /></td></tr>\r\n<tr><td>".$_SESSION['lang']['jmlhPersonel']."</td><td>:</td><td><input type='text' class='myinputtextnumber' style='width:150px;' id='jmlh_1' onblur='jumlahkan(1)' onkeypress='return angka_doang(event)' /> ".$_SESSION['lang']['setahun']."</td></tr>\r\n<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='totBiaya' value='0' onkeypress='return false' /></td></tr>\r\n<tr><td colspan=3>\r\n\r\n<button class=mybutton id=btlTmbl name=btlTmbl onclick=saveBudget(1)  >".$_SESSION['lang']['save']."</button></td></tr></table><br /><br />\r\n\r\n";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>
<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader><td>No</td>
<td>".$_SESSION['lang']['index']."</td><td>".$_SESSION['lang']['budgetyear']."</td><td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['tipeBudget']."</td>\r\n            <td>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td>".$_SESSION['lang']['volume']."</td>\r\n            <td>".$_SESSION['lang']['satuan']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['satuan']."</td>\r\n            <td>".$_SESSION['lang']['rp']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containDataSDM>\r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$optKdbdgtM = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrgm = 'select kodebudget,nama from '.$dbname.".bgt_kode order by kodebudget asc";
$qOrgm = mysql_query($sOrgm);
while ($rOrgm = mysql_fetch_assoc($qOrgm)) {
    $optKdbdgtM .= "<option value='".$rOrgm['kodebudget']."'>".$rOrgm['kodebudget'].' ['.$rOrgm['nama'].']</option>';
}
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['material'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n<select id='kdBudgetM' style='width:150px;' onchange='getKlmpkbrg()'>".$optKdbdgtM."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td><input type='text' class='myinputtext' id='kdBarang' style='width:150px;' onkeypress='return angka_doang(event)' />&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."' onclick=\"searchBrg('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang'].'</legend>'.$_SESSION['lang']['find'].'&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>'.$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\">\r\n    <span id='namaBrg'></span></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_2' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(2)' /> ".$_SESSION['lang']['setahun']."&nbsp;<span id='satuan'></span></td></tr>\r\n<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHarga' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        \r\n\r\n\r\n<tr><td colspan=3>\r\n<button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='saveBudget(2)'   >".$_SESSION['lang']['save']."</button></td></tr></table><br />\r\n\r\n<input type=hidden id=prosesBr name=prosesBr value=insert_baru >\r\n";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['index']."</td>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['tipeBudget']."</td>\r\n            <td>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td>".$_SESSION['lang']['namabarang']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['satuan']."</td>\r\n            <td>".$_SESSION['lang']['rp']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containDataBrg>\r\n\t\t";
$frm[1] .= '</tbody></table></fieldset>';
$optKdbdgt_S = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrgs = 'select kodebudget,nama from '.$dbname.".bgt_kode order by nama asc";
$qOrgs = mysql_query($sOrgs);
while ($rOrgs = mysql_fetch_assoc($qOrgs)) {
    $optKdbdgt_S .= "<option value='".$rOrgs['kodebudget']."'>".$rOrgs['kodebudget']. ' '.$rOrgs['nama'].'</option>';
}
$sOrgWS = "select kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE tipe='WORKSHOP' AND induk LIKE '".substr($_SESSION['empl']['lokasitugas'], 0, 3)."%'";
//echo $sOrgWS;
$qOrgWS = mysql_query($sOrgWS);
while ($rOrgWS = mysql_fetch_assoc($qOrgWS)) {
    $optOrgWS .= "<option value='".$rOrgWS['kodeorganisasi']."'>".$rOrgWS['namaorganisasi'].'</option>';
}
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['service'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n<select id='kdBudgetS' style='width:150px;'>".$optKdbdgt_S."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['kdWorks']."</td><td>:</td><td><select id='kdWorkshop' style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option><option value=''>".$optOrgWS ."</option></select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jmThn']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_3' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(3)' /> ".$_SESSION['lang']['setahun']."</td></tr>\r\n<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHargaJam' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        \r\n\r\n\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=saveBudget(3)>".$_SESSION['lang']['save']."</button>\r\n<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />\r\n</td></tr>\r\n</table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['index']."</td>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['tipeBudget']."</td>\r\n            <td>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['satuan']."</td>\r\n            <td>".$_SESSION['lang']['rp']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containDataSrvc>\r\n\t\t";
$frm[2] .= '</tbody></table></fieldset>';
$optKdbdgt_B = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrgB = 'select kodebudget,nama from '.$dbname.".bgt_kode order by nama asc";
$qOrgB = mysql_query($sOrgB);
while ($rOrgB = mysql_fetch_assoc($qOrgB)) {
    $optKdbdgt_B .= "<option value='".$rOrgB['kodebudget']."'>".$rOrgB['nama'].'</option>';
}
$optAkun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJns = 'select noakun,namaakun from '.$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns = mysql_query($sJns);
while ($rJns = mysql_fetch_assoc($qJns)) {
    $optAkun .= "<option value='".$rJns['noakun']."'>".$rJns['noakun'].' - ['.$rJns['namaakun'].']</option>';
}
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['biayalain'].'</legend>';
$frm[3] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n<select id='kdBudgetB' style='width:150px;'>".$optKdbdgt_B."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['jenisbiaya']."</td><td>:</td><td><select id='noAkun' style='width:150px;'>".$optAkun."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totBiayaB' style='width:150px;' onkeypress='return angka_doang(event)' value='0' /> ".$_SESSION['lang']['setahun']."</td></tr>\r\n\r\n\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=saveBudget(4) >".$_SESSION['lang']['save']."</button>\r\n<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />\r\n</td></tr>\r\n</table>";
$frm[3] .= '</fieldset>';
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['index']."</td>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['tipeBudget']."</td>\r\n            <td>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td>".$_SESSION['lang']['noakun']."</td>\r\n            <td>".$_SESSION['lang']['namaakun']."</td>\r\n            <td>".$_SESSION['lang']['rp']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containDataLain>\r\n\t\t";
$frm[3] .= '</tbody></table></fieldset>';
$optThnTtp = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$frm[4] .= '<fieldset><legend>'.$_SESSION['lang']['tutup']."</legend>\r\n    <div><table><tr><td>".$_SESSION['lang']['budgetyear']."</td><td><select id='thnBudgetTutup' style='width:150px'>".$optThnTtp.'</select></td></tr>';
$frm[4] .= "<tr><td colspan=2 align=center><button class=\"mybutton\"  id=\"saveData\" onclick='closeBudget()'>".$_SESSION['lang']['tutup'].'</button></td></tr></table>';
$frm[4] .= '</div></fieldset>';
$hfrm[0] = $_SESSION['lang']['sdm'];
$hfrm[1] = $_SESSION['lang']['material'];
$hfrm[2] = $_SESSION['lang']['service'];
$hfrm[3] = $_SESSION['lang']['biayalain'];
//$hfrm[4] = $_SESSION['lang']['tutup'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>