<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
echo "<script language=javascript1.2 src='js/kebun_5basispanen.js'></script>\n";
include 'master_mainMenu.php';
OPEN_BOX('', '');
$optReg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment \n                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg = mysql_query($sreg) ;
while ($rreg = mysql_fetch_assoc($qreg)) {
    $optReg .= "<option value='".$rreg['regional']."'>".$rreg['regional'].'</option>';
    $regDt = $rreg['regional'];
}
$sreg2 = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\n                where left(kodeorganisasi,4) \n                in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$regDt."') \n                and tipe='AFDELING'";
$qreg2 = mysql_query($sreg2) ;
while ($rreg2 = mysql_fetch_assoc($qreg2)) {
    $optReg .= "<option value='".$rreg2['kodeorganisasi']."'>".$rreg2['kodeorganisasi'].' - '.$rreg2['namaorganisasi'].'</option>';
}
$optagama = '';
$arragama = getEnum($dbname, 'kebun_5basispanen', 'jenis');
foreach ($arragama as $kei => $fal) {
    $optagama .= "<option value='".$kei."'>".$fal.'</option>';
}
$arrDt = ['Tidak', 'Iya'];
foreach ($arrDt as $kei => $fal) {
    $optdenda .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<fieldset style='float:left;'><legend>".$_SESSION['lang']['basispanen']."</legend><table>\n     <tr><td>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['regional']."</td>\n         <td><select id=regId style=width:150px>".$optReg."</select></td></tr>\n\t <tr><td>".$_SESSION['lang']['jenis'].'</td><td><select id=jnsId style=width:150px>'.$optagama."</select></td></tr>\n\t <tr><td>".$_SESSION['lang']['bjr']."</td><td><input type=text id=bjr onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>    \n         <tr><td>".$_SESSION['lang']['basiskg']."</td><td><input type=text id=basisjjg onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>\n         <tr><td>".$_SESSION['lang']['rpperkg']."</td><td><input type=text id=rpperkg onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>\n         <tr><td>".$_SESSION['lang']['denda'].'</td><td><select id=denda style=width:150px>'.$optdenda."</select></td></tr>\n         <tr><td>".$_SESSION['lang']['insentif'].' '.$_SESSION['lang']['topografi']."</td><td><input type=text id=insentif onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>\n\t </table>\n\t <input type=hidden id=method value='insert'>\n         <input type=hidden id=oldReg value=''>\n         <input type=hidden id=oldJns value=''>\n         <input type=hidden id=oldBjr value=''>\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\n\t </fieldset>";
echo "<fieldset style='clear:both;float:left;'><legend>".$_SESSION['lang']['data'].'</legend>';
echo '<div id=container><script>loadData(0)</script></div></fieldset>';
CLOSE_BOX();
echo close_body();

?>