<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
$frm[3] = '';
$frm[4] = '';
$frm[5] = '';
echo "<script>\r\npilh=\" ";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n<script>plh=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/bibit_keluar_masuk.js\"></script>\r\n<script>\r\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n";
$optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
//$optKdorg2 = $optKdorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$sOrg2 = "select kodeorg from $dbname.setup_blok ".
//    "where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
//$qOrg2 = mysql_query($sOrg2) || exit(mysql_error($conns));
//while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
//    $optKdorg .= '<option value='.$rOrg2['kodeorg'].'>'.$optNmOrg[$rOrg2['kodeorg']].'</option>';
//}


//$sOrg22 = 'select kodeorg from '.$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%MN%' order by kodeorg asc";
//$qOrg22 = mysql_query($sOrg22) || exit(mysql_error($conns));
//while ($rOrg22 = mysql_fetch_assoc($qOrg22)) {
//    $optKdorg2 .= '<option value='.$rOrg22['kodeorg'].'>'.$optNmOrg[$rOrg22['kodeorg']].'</option>';
//}
$optKdorg2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$sOrg3 = 'select kodeorg from '.$dbname.".setup_blok where  statusblok='BBT'  order by kodeorg asc";
//$qOrg3 = mysql_query($sOrg3) || exit(mysql_error($conns));
//while ($rOrg3 = mysql_fetch_assoc($qOrg3)) {
//    $optKdorg2 .= '<option value='.$rOrg3['kodeorg'].'>'.$optNmOrg[$rOrg3['kodeorg']].'</option>';
//}
//$str = "SELECT o.kodeorganisasi,o.namaorganisasi
//from $dbname.setup_blok s
//INNER JOIN $dbname.organisasi o ON o.kodeorganisasi=s.kodeorg
//where s.statusblok='BBT' and s.kodeorg like '".$_SESSION['empl']['lokasitugas']."%'
//order by kodeorg ASC";
//echoMessage(" query ", $_SESSION['empl']);
//echoMessage(" query ", getQuery("unitkebun"));
$optorg= makeOption2(getQuery("unitkebun"),
    array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihdata'] ),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

//echoMessage(" option ",$optorg);

//$optJnsBbt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sBbt = 'select distinct  jenisbibit from  '.$dbname.'.setup_jenisbibit order by jenisbibit';
//$qBbt = mysql_query($sBbt) || exit(mysql_error($sBbt));
//while ($rBbt = mysql_fetch_assoc($qBbt)) {
//    $optJnsBbt .= "<option value='".$rBbt['jenisbibit']."'>".$rBbt['jenisbibit'].'</option>';
//}
$optJnsBbt= makeOption2($sBbt,
    array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihdata'] ),
    array("valuefield"=>'jenisbibit',"captionfield"=> 'jenisbibit' )
);
$optSup = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optStatPos = $optSup;
$arrStata = ['Not Posted', 'Posted'];
foreach ($arrStata as $lstStat => $dtstat) {
    $optStatPos .= "<option value='".$lstStat."'>".$dtstat.'</option>';
}
$sSupplier = "select distinct supplierid,namasupplier from $dbname.log_5supplier ".
    "where supplierid like 'S%' order by namasupplier asc";
//$qSupplier = mysql_query($sSupplier) || exit(mysql_error($sSupplier));
//while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
//    $optSup .= "<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier'].'</option>';
//}
$optSup= makeOption2($sSupplier,
    array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihdata'] ),
    array("valuefield"=>'supplierid',"captionfield"=> 'namasupplier' )
);
$tglHrini = date('Ymd');
echo "<div id='formIsian' style='display:block;'>";
OPEN_BOX('', '<b>'.$_SESSION['lang']['masukkeluarbibit'].'</b>');
$frm[0] .= "<input type='hidden' id='proses1' value='saveTab1' /><input type='hidden' id='oldJnsbibit'  /><fieldset style='width:350px;float:left'><legend>".$_SESSION['lang']['tnmbibit'].'</legend>';
if ('EN' === $_SESSION['language']) {
    $frm[0] .= 'Including receipt of seeds directly in the Main Nursery (from other sources)<br>';
} else {
    $frm[0] .= 'Termasuk penerimaan bibit langsung ke MN dari tempat lain<br>';
}

$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksi' value='TMB'  disabled /></td></tr>\r\n<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><input type='text' class='myinputtext' style='width:150px;' id='batch'  disabled /></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodeorg'].'</td><td>:</td><td><select id=kodeorgBibitan style=width:150px>'.$optorg."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhBibitan' onkeypress='return angka_doang(event)' value='0' />&nbsp;Biji</td></tr>\r\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ket' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[0] .= '<tr><td>'.$_SESSION['lang']['tgltanam']."</td><td>:</td><td><input type=text class=myinputtext id=tglTnm style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>";
$frm[0] .= '<tr><td colspan=3>&nbsp;</td></tr></table>';
$optKebun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKebun = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='KEBUN'";
$qKebun = mysql_query($sKebun) || exit(mysql_error($conns));
while ($rKebun = mysql_fetch_assoc($qKebun)) {
    $optKebun .= "<option value='".$rKebun['kodeorganisasi']."'>".$rKebun['namaorganisasi'].'</option>';
}
$frm[0] .= '</fieldset>';
$frm[0] .= "<fieldset style='width:300px;'><legend>".$_SESSION['lang']['sumber'].'</legend><table cellspacing=1 border=0>';
$frm[0] .= '<tr><td>'.$_SESSION['lang']['jenisbibit'].'</td><td>:</td><td><select id=jnsBibitan style=width:150px>'.$optJnsBbt.'</select></td></tr>';
$frm[0] .= '<tr><td>'.$_SESSION['lang']['supplier'].'</td><td>:</td><td><select id=supplier_id style=width:150px>'.$optSup."</select><img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['find'].'</legend>'.$_SESSION['lang']['namasupplier'].'&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>'.$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td></tr>";
$frm[0] .= '<tr><td>'.$_SESSION['lang']['tglproduksi']."</td><td>:</td><td><input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td></tr>";
$frm[0] .= '<tr><td>'.$_SESSION['lang']['nodo']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='nodo' onkeypress='return tanpa_kutip(event)' /></td></tr>";
$frm[0] .= '<tr><td>'.$_SESSION['lang']['jumlah']." PD DO</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlh' onkeypress='return angka_doang(event)' value='0' />&nbsp;Biji</td></tr>";
$frm[0] .= '<tr><td> '.$_SESSION['lang']['diterima']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhTrima' onkeypress='return angka_doang(event)' value='0' /></td></tr>";
$frm[0] .= '<tr><td>'.$_SESSION['lang']['afkirbibit']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='afkirKcmbh' onkeypress='return angka_doang(event)' value='0' /></td></tr>";
$frm[0] .= '</table></fieldset>';
$frm[0] .= '<div style=float:left;><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(1)  >'.$_SESSION['lang']['save'].'</button><button class=mybutton id=canbtlTmbl name=canbtlTmbl onclick=cancelData1()  >'.$_SESSION['lang']['cancel'].'</button></div>';
$frm[0] .= '<div style=clear:both;>&nbsp;</div>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type='text' class='myinputtext' id='tglCari2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['batch']."</td>\r\n        <td><input type='text' class='myinputtext' id='batchCari2'  style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['status']."</td>\r\n        <td><select id=statCari2  style=\"width:150px;\">".$optStatPos."</select></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData1()  >".$_SESSION['lang']['find']."</button>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['kodetransaksi']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            \r\n            <td>".$_SESSION['lang']['tgltanam']."</td>\r\n            <td>".$_SESSION['lang']['jenisbibit']."</td>\r\n             <td>".$_SESSION['lang']['supplier']."</td>\r\n            <td>".$_SESSION['lang']['tglproduksi']."</td>\r\n            <td colspan=2>Action</td>\r\n            </tr>\r\n            </thead><tbody id=containData1><script>loadData1()</script> \r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$optbatch = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$xbatch = "select distinct batch from $dbname.bibitan_mutasi ".
    "where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by batch desc";

$optbatch= makeOption2($xbatch,
    array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihdata'] ),
    array("valuefield"=>'batch',"captionfield"=> 'batch' )
);
//$ybatch = mysql_query($xbatch) || exit(mysql_error($xbatch));
//while ($zbatch = mysql_fetch_assoc($ybatch)) {
//    $optbatch .= "<option value='".$zbatch['batch']."'>".$zbatch['batch'].'</option>';
//}
$nott = 'Termasuk Pemindahan dari PN ke PN tempat lain, maupun ke MN tempat lain';
if ('EN' === $_SESSION['language']) {
    $nott = 'Include seed movement from Pre Nursery to other Pre Nursery, or from Main Nursery to other Nursery';
}

$frm[3] .= "<input type='hidden' id='proses2' value='saveTab2' /><fieldset style=width:650px;><legend>Mutasi / ".$_SESSION['lang']['transplatingbibit']."</legend>\r\n   <fieldset style='text-align:left;width:300px;float:right;'>\r\n\t\t\t\t   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>\r\n\t\t\t\t   <p>".$nott." \r\n\t\t\t\t   </p>\r\n\t\t\t\t   </fieldset>\t\r\n   \r\n<table cellspacing=1 border=0>\r\n\r\n<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksiTp' value='TPB'  disabled /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchTp' style=width:150px onchange='getKodeorg()'>".$optbatch."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id=kodeOrgTp style=width:150px onchange='cekSamaGak()'>".$optKdorg2."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id=tglTp style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>\r\n\r\n\r\n\r\n<tr><td>".$_SESSION['lang']['tujuan']."</td><td>:</td><td><select id=kodeOrgTjnTp style=width:150px onchange='cekSamaGak()'>".$optKdorg2."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhTpBbtn' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketTp' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[3] .= '<tr><td colspan=3><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(2)  >'.$_SESSION['lang']['save'].'</button><button class=mybutton id=canbtlTmbl name=canbtlTmbl onclick=cancelData2()  >'.$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>\r\n";
$frm[3] .= '<fieldset ><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type='text' class='myinputtext' id='tglCari3' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['batch']."</td>\r\n        <td><input type='text' class='myinputtext' id='batchCari3'  style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['status']."</td>\r\n        <td><select id=statCari3  style=\"width:150px;\">".$optStatPos."</select></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData2()  >".$_SESSION['lang']['find']."</button>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['kodetransaksi']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n            <td>".$_SESSION['lang']['tanggal']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['tujuan']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['keterangan']."</td>\r\n            <td colspan=2>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n            </thead><tbody id=containData2>\r\n\t\t";
$frm[3] .= '</tbody></table></fieldset>';
$frm[2] .= "<input type='hidden' id='proses3' value='saveTab3' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['afkirbibit']."</legend>\r\n<table cellspacing=1 border=0>\r\n\r\n<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransAfk' value='APB'  disabled /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchAfk' style='width:150px' onchange='getKodeorg2()'>".$optbatch."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglAfkirBibit' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgAfk' style=width:150px>".$optKdorg2."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhAfk' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketAfk' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[2] .= '<tr><td colspan=3><button class=mybutton   name=btlTmbl onclick=saveData(3)  >'.$_SESSION['lang']['save'].'</button><button class=mybutton   name=canbtlTmbl onclick=cancelData3()  >'.$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>\r\n";
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type='text' class='myinputtext' id='tglCari4' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['batch']."</td>\r\n        <td><input type='text' class='myinputtext' id='batchCari4'  style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['status']."</td>\r\n        <td><select id=statCari4  style=\"width:150px;\">".$optStatPos."</select></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData3()  >".$_SESSION['lang']['find']."</button>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['kodetransaksi']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n\t    <td>".$_SESSION['lang']['tanggal']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['keterangan']."</td>\r\n            <td colspan=2>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n            </thead><tbody id=containData3>\r\n\t\t";
$frm[2] .= '</tbody></table></fieldset>';
$frm[1] .= "<input type='hidden' id='proses5' value='saveTab5' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['doubletoon']."</legend>\r\n<table cellspacing=1 border=0>\r\n\r\n<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksiDbt' value='DBT'  disabled /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchDbt' style='width:150px' onchange='getKodeorg3()'>".$optbatch."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglDbt' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgDbt' style=width:150px>".$optKdorg2."</select></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhDbt' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketDbt' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[1] .= '<tr><td colspan=3><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(5)  >'.$_SESSION['lang']['save']."</button><button class=mybutton id='' name=canbtlTmbl onclick=cancelData5()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>\r\n";
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type='text' class='myinputtext' id='tglCari5' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['batch']."</td>\r\n        <td><input type='text' class='myinputtext' id='batchCari5'  style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['status']."</td>\r\n        <td><select id=statCari5  style=\"width:150px;\">".$optStatPos."</select></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData5()  >".$_SESSION['lang']['find']."</button>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['kodetransaksi']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['keterangan']."</td>\r\n            <td colspan=2>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n            </thead><tbody id=containData5>\r\n\t\t";
$frm[1] .= '</tbody></table></fieldset>';
$optKegiatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$arragama = getEnum($dbname, 'bibitan_mutasi', 'jenistanam');
foreach ($arragama as $kei => $fal) {
    $optKegiatan .= "<option value='".$kei."'>".$fal.'</option>';
}
$arr = ['External', 'Internal', 'Afliasi'];
$optintex = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arr as $isi => $eia) {
    $optintex .= '<option value='.$isi.' >'.$eia.'</option>';
}
$optKode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAfd = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKaryawan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKaryawan = "select distinct karyawanid,namakaryawan,nik ,subbagian from $dbname.datakaryawan ".
    "where ".//lokasitugas='".$_SESSION['empl']['lokasitugas']."' ".
    "   kodejabatan in ('32','124') ".
    "and karyawanid!='".$_SESSION['standard']['userid']."'";
$optKaryawan= makeOption2($sKaryawan,
    array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihdata'] ),
    array("valuefield"=>'karyawanid',"captionfield"=> 'namakaryawan' ) ,
    function($option,$value,$caption){
        $ret = array("newvalue"=>"","newcaption"=>"");
        if($option=='init'){
            $ret["newvalue"]=$value;
            $ret["newcaption"]=$caption;
        }
        if($option=='noninit'){
            $ret["newvalue"]=$value;
            $ret["newcaption"]=$value . " ".$caption;
        }
        return $ret;
    }
);
//$qKaryawan = mysql_query($sKaryawan)// || exit(mysql_error($sKaryawan));
//while ($rKaryawan = mysql_fetch_assoc($qKaryawan)) {
//    $optKaryawan .= "<option value='".$rKaryawan['karyawanid']."'>".$rKaryawan['nik'].' '.$rKaryawan['namakaryawan'].' [ '.$rKaryawan['subbagian'].' ]</option>';
//}
$frm[4] .= "<input type='hidden' id='proses7' value='saveTab7' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['pengirimanBibit']."</legend>\r\n<table cellspacing=1 border=0>\r\n\r\n<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransPnb' value='PNB'  disabled /></td></tr>\r\n<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchPnb' style=width:150px onchange='getKodeorgN()'>".$optbatch."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglPnb' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgPnb' style=width:150px>".$optKdorg2."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhPnb' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>\r\n<tr><td>".$_SESSION['lang']['nospb']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketPnb' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[4] .= "\r\n<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdvhc' onkeypress='return tanpa_kutip(event)' maxlength=8 /></td></tr>\r\n<tr><td>Rit </td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlRit' onkeypress='return angka_doang(event)' maxlength=20 /></td></tr>\r\n<tr><td>".$_SESSION['lang']['sopir']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='nmSupir' onkeypress='return tanpa_kutip(event)' maxlength=20 /></td></tr>\r\n\r\n<tr><td>".$_SESSION['lang']['Intex']."</td><td>:</td><td><select id='intexDt' style=width:150px onchange='getCustdata(0,0,0)'>".$optintex."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['customerlist']."</td><td>:</td><td><select id='custId' style=width:150px onchange='getKodeorgBlok()'>".$optKode."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodeblok']."</td><td>:</td><td><select id='kdAfdeling' style=width:150px disabled >".$optAfd."</select></td></tr>   \r\n<tr><td>".$_SESSION['lang']['lokasi'].' '.$_SESSION['lang']['detailPengiriman']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='detPeng' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>\r\n<tr><td>".$_SESSION['lang']['kegiatan']."</td><td>:</td><td><select id='kegId' style=width:150px>".$optKegiatan."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['asisten']."</td><td>:</td><td><select id='assistenPnb' style=width:150px>".$optKaryawan.'</select></td></tr>';
$frm[4] .= "<tr><td colspan=3><button class=mybutton id='' name=btlTmbl onclick=saveData(7)  >".$_SESSION['lang']['save']."</button><button class=mybutton id='' name=canbtlTmbl onclick=cancelData7()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>\r\n";
$frm[4] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n    <table cellpadding=1 cellspacing=1 border=0>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td><input type='text' class='myinputtext' id='tglCari7' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['batch']."</td>\r\n        <td><input type='text' class='myinputtext' id='batchCari7'  style=\"width:150px;\" /></td>\r\n        <td>".$_SESSION['lang']['status']."</td>\r\n        <td><select id=statCari7  style=\"width:150px;\">".$optStatPos."</select></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData7()  >".$_SESSION['lang']['find']."</button>\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['kodetransaksi']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n            <td>".$_SESSION['lang']['tanggal']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['kegiatan']."</td>\r\n            <td>".$_SESSION['lang']['nospb']."</td>\r\n            <td>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td>".$_SESSION['lang']['customerlist']."</td>\r\n            <td>".$_SESSION['lang']['kodeblok']."</td>\r\n            <td>".$_SESSION['lang']['asisten']."</td>\r\n            <td colspan=2>".$_SESSION['lang']['action']."</td>\r\n            </tr>\r\n            </thead><tbody id=containData7>\r\n\t\t";
$frm[4] .= '</tbody></table></fieldset>';
$frm[5] .= '<fieldset  style=width:650px;><legend>'.$_SESSION['lang']['stockdetail']."</legend>\r\n\r\n    <table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['saldo']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['supplier']."</td>\r\n            <td>".$_SESSION['lang']['umur'].'('.$_SESSION['lang']['bulan'].")</td>\r\n            </tr>\r\n            </thead><tbody id=containDataStock>\r\n\t\t";
$frm[5] .= '</tbody></table></fieldset>';
$hfrm[0] = $_SESSION['lang']['tnmbibit'];
$hfrm[1] = $_SESSION['lang']['doubletoon'];
$hfrm[2] = $_SESSION['lang']['afkirbibit'];
$hfrm[3] = $_SESSION['lang']['transplatingbibit'];
$hfrm[4] = $_SESSION['lang']['pengirimanBibit'];
$hfrm[5] = $_SESSION['lang']['stockdetail'];
drawTab('FRM', $hfrm, $frm, 150, 1100);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>