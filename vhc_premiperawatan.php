<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/vhc_premiperawatan.js'></script>\n";
include 'master_mainMenu.php';
$str = 'select distinct periode from '.$dbname.".sdm_5periodegaji \n      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optPeriode .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
$optPremi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$skdprem = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \n          where induk='".$_SESSION['empl']['lokasitugas']."' and tipe='TRAKSI' order by namaorganisasi asc";
$qkdprem = mysql_query($skdprem);
while ($rkdprem = mysql_fetch_assoc($qkdprem)) {
    $optPremi .= "<option value='".$rkdprem['kodeorganisasi']."'>".$rkdprem['namaorganisasi'].'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['premiperawatan'].'</b>');
$frm[0] .= "<fieldset><legend>Form</legend>\n              <table>\n              <tr><td>".$_SESSION['lang']['periode'].'<td><td><select id=periode style=width:150px>'.$optPeriode."</select></td></tr> \n              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>\n              <tr><td>".$_SESSION['lang']['kodetraksi'].'<td><td><select id=kdpremi style=width:150px>'.$optPremi."</select></td></tr>     \n             </table>\n             <button class=mybutton onclick=getData()>".$_SESSION['lang']['preview']."</button>\n             <button class=mybutton onclick=getExcel(event,'vhc_slave_premiperawatan.php','RAWATKD')>".$_SESSION['lang']['excel']."</button>\n             </fieldset>\n             <div id=container style='width:850px;height:400px;overflow:scroll;'>\n             </div>";
$hfrm[0] = $_SESSION['lang']['form'];
drawTab('FRM', $hfrm, $frm, 300, 900);
CLOSE_BOX();
echo close_body();

?>