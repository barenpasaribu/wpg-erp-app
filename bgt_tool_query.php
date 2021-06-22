<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
$oprrt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

if ($_SESSION['language'] == 'ID') {
	$klpk = 'namakelompok';
}
else {
	$klpk = 'namakelompok1 as namakelompok';
}

echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/bgt_tool_query.js\'></script>' . "\r\n" . '<script>isi="';
echo $oprrt;
echo '";</script>' . "\r\n";
$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$arr = '##thnBudget##unitId##klmpKeg##kegId##kdBgt##pilUn_1##method##persenData##sbUnit##blokId##kdBrgRev##thnTnm';
include 'master_mainMenu.php';
OPEN_BOX();
$opt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optBrg = $optAct5 = $optBlok = $optAct = $optJnsKeg = $optKeg = $optThnBud = $opt;
$optKdBudget = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$pil = array(1 => 'VOLUME', 2 => 'ROTASI', 3 => 'VOLUME and  ROTASI', 4 => 'Cost and Fisic');
$pil2 = array(1 => 'VOLUME', 2 => 'ROTASI', 3 => 'FISIK', 4 => 'RUPIAH', 5 => 'HAPUS DATA', 6 => 'KEGIATAN', 7 => 'UNCLOSE BLOK', 8 => 'UNCLOSE UPAH', 9 => 'KODE BUDGET', 10 => 'MATERIAL');
$actionDt = array(1 => 'REVISI', 2 => 'MENGGANTI NILAI');
$actr = array(1 => 'BLOK', 2 => 'UPAH');

foreach ($pil as $dtl => $vw) {
	$opt .= '<option value=\'' . $dtl . '\'>' . $vw . '</option>';
}

foreach ($pil2 as $dtl => $vw) {
	$optAct5 .= '<option value=\'' . $dtl . '\'>' . $vw . '</option>';
}

$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optKdBarangRev = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sUnit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=4 order by namaorganisasi asc';

#exit(mysql_error($conn));
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $rUnit['kodeorganisasi'] . ' - ' . $rUnit['namaorganisasi'] . '</option>';
}

$sThnBud = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget order by tahunbudget desc';

#exit(mysql_error($conn));
($qThnBud = mysql_query($sThnBud)) || true;

while ($rThnBud = mysql_fetch_assoc($qThnBud)) {
	$optThnBud .= '<option value=\'' . $rThnBud['tahunbudget'] . '\'>' . $rThnBud['tahunbudget'] . '</option>';
}

$sKeg = 'select distinct kodeklp,' . $klpk . ' from ' . $dbname . '.setup_klpkegiatan order by namakelompok asc';

#exit(mysql_error($conn));
($qKeg = mysql_query($sKeg)) || true;

while ($rKeg = mysql_fetch_assoc($qKeg)) {
	$optKeg .= '<option value=\'' . $rKeg['kodeklp'] . '\'>' . $rKeg['namakelompok'] . '</option>';
}

$optKdBudget .= '<option value=\'M\'>MATERIAL</option>';
$optKdBudget .= '<option value=\'SDM\'>SDM HK</option>';
$sKdBudget = 'select distinct kodebudget,nama from ' . $dbname . '.bgt_kode order by nama asc';

#exit(mysql_error($conn));
($qKdBudget = mysql_query($sKdBudget)) || true;

while ($rKdBudget = mysql_fetch_assoc($qKdBudget)) {
	$optKdBudget .= '<option value=\'' . $rKdBudget['kodebudget'] . '\'>' . $rKdBudget['kodebudget'] . ' - ' . $rKdBudget['nama'] . '</option>';
}

echo '<fieldset style=width:350px;float:left;>' . "\r\n" . '     <legend>' . $_SESSION['lang']['revisi'] . ' ' . $_SESSION['lang']['nilai'] . ' ' . $_SESSION['lang']['budget'] . '</legend>' . "\r\n\t" . ' <table>' . "\r\n\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=thnBudget style=width:150px  onchange=unlockForm()>' . $optThnBud . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=unitId style=width:150px onchange=getSubunit()>' . $optUnit . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['subunit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=sbUnit style=width:150px  onchange=getBlok()>' . $optAct . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '          <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=thnTnm style=width:150px disabled>' . $optAct . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=blokId style=width:150px>' . $optBlok . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kelompokkegiatan'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=klmpKeg style=width:150px onchange=getKegiatan()>' . $optKeg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kegId style=width:150px>' . $optJnsKeg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdBgt style=width:150px onchange=getBarangRev()>' . $optKdBudget . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '          <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodebarang'] . '/' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdBrgRev style=width:150px disabled>' . $optKdBarangRev . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['jenis'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=pilUn_1 style=width:150px>' . $opt . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '        ' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['persen'] . ' ' . $_SESSION['lang']['revisi'] . ' (1=100%) </td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <input type=text id=persenData class=myinputtextnumber style=width:150px onkeypress=\'return angka_doang(event);\' />' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td colspan=2 >&nbsp;</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <button class=mybutton id=tmblDt onclick=saveFranco(\'bgt_slave_tool_query\',\'' . $arr . '\')>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '         <button class=mybutton onclick=unlockForm()>batal</button>' . "\r\n" . '     </fieldset><input type=hidden id=method value=getData />';
$arr2 = '##thnBudget2##unitId2##sbUnit2##blokId2##klmpKeg2##kegId2##kegIdR2##kdBgt2##pilUn_2##persenData2##method2##kdBgtR2##kdBarang';
echo '<fieldset style=width:330px;float:left;>' . "\r\n" . '     <legend>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['nilai'] . ' ' . $_SESSION['lang']['budget'] . '</legend>' . "\r\n\t" . ' <table>' . "\r\n\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=thnBudget2 style=width:150px onchange=unlockForm2()>' . $optThnBud . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['jenis'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=pilUn_2 style=width:150px onchange=pilGant()>' . $optAct5 . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=unitId2 style=width:150px onchange=getSubunit2()>' . $optUnit . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['subunit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=sbUnit2 style=width:150px  onchange=getBlok2()>' . $optAct . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=blokId2 style=width:150px>' . $optBlok . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kelompokkegiatan'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=klmpKeg2 style=width:150px onchange=getKegiatan2()>' . $optKeg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kegId2 style=width:150px onchange=getBarang()>' . $optJnsKeg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['kegiatan'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kegIdR2 style=width:150px disabled>' . $optJnsKeg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         ' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdBgt2 style=width:150px>' . $optKdBudget . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdBgtR2 style=width:150px disabled>' . $optKdBudget . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodebarang'] . ' ' . $_SESSION['lang']['lama'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdBrgLam style=width:150px disabled>' . $optBrg . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr><td>' . $_SESSION['lang']['kodebarang'] . '</td><td>' . "\r\n" . '          <input type=\'text\' class=\'myinputtext\' id=\'kdBarang\' style=\'width:150px;\' onkeypress=\'return angka_doang(event)\' disabled />&nbsp;<img src="images/search.png" class="resicon" title=\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '\' onclick="searchBrg(\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>\',event);">' . "\r\n" . '    <span id=\'namaBrg\'></span></td></tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['ganti'] . ' ' . $_SESSION['lang']['nilai'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <input type=text id=persenData2 class=myinputtextnumber style=width:150px onkeypress=\'return angka_doang(event);\' />' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n\r\n\t" . ' </table>' . "\r\n" . '         <input type=hidden id=method2 value=getData2 />' . "\r\n\t" . ' <button class=mybutton id=tmblDt2 onclick=saveFranco2(\'bgt_slave_tool_query\',\'' . $arr2 . '\')>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n" . '         <button class=mybutton onclick=unlockForm2()>batal</button>' . "\r\n" . '     </fieldset>';
$optVhc = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optDtvhc = $optVhc;
$sTraksi = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'TRAKSI\'';

#exit(mysql_error($conn));
($qTraksi = mysql_query($sTraksi)) || true;

while ($rTraksi = mysql_fetch_assoc($qTraksi)) {
	$optVhc .= '<option value=\'' . $rTraksi['kodeorganisasi'] . '\'>' . $rTraksi['namaorganisasi'] . '</option>';
}

echo '<fieldset style=width:250px;>' . "\r\n" . '     <legend>Ganti Rp/Km VHC</legend>' . "\r\n\t" . ' <table>' . "\r\n\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=thnBudget3 style=width:150px>' . $optThnBud . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdTraksi style=width:150px onchange=getUnit()>' . $optVhc . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodevhc'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=kdVhc style=width:150px>' . $optDtvhc . '</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>' . "\r\n\r\n\r\n\t" . ' </table>' . "\r\n\t" . ' <button class=mybutton id=tmblDt2 onclick=apDate()>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n" . '     </fieldset>';
CLOSE_BOX();
echo '<div id=listData style=display:none>';
OPEN_BOX();
echo '<fieldset style=width:1050px;><legend>' . $_SESSION['lang']['list'] . '</legend><div id=container>';
echo '</div></fieldset>';
CLOSE_BOX();
echo '</div>';
echo close_body();
echo "\r\n" . '  <!--<tr>' . "\r\n\t" . '   <td>".$_SESSION[\'lang\'][\'action\']."</td>' . "\r\n\t" . '   <td>' . "\r\n" . '           <select id=actId style=width:150px>".$optAct."</select>' . "\r\n" . '           </td>' . "\r\n\t" . ' </tr>-->';

?>
