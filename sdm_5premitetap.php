<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
echo "<script language=javascript src='js/zMaster.js'></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/sdm_5premitetap.js'></script>\r\n<script>\r\n\r\n</script>\r\n";
$optTipe = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optTipe5 = $optTipe;
$arrd = ['Premi Tetap', 'Insentif'];
foreach ($arrd as $rwdd => $lstarr) {
    $optTipe2 .= "<option value='".$rwdd."'>".$lstarr.'</option>';
    $optTipe .= "<option value='".$rwdd."'>".$lstarr.'</option>';
}
$arr = '##tpTransaksi##pilInp##premiIns##method';
include 'master_mainMenu.php';
OPEN_BOX();
echo "<fieldset style='width:380px;float:left;'>\r\n     <legend><b>".$_SESSION['lang']['premitetap']."</b></legend>\r\n\t <table>\r\n\t \r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['tipetransaksi']." </td>\r\n\t    <td><select id=tpTransaksi style='width:150px;' onchange='getDt(0,0)'>".$optTipe."</select></td>\r\n\t </tr>\t\r\n         <tr>\r\n\t   <td>".$_SESSION['lang']['kodejabatan'].'/'.$_SESSION['lang']['tipekaryawan']." </td>\r\n\t    <td><select id=pilInp style=width:150px;>".$optTipe5."</select> <img src='images/search.png' style=cursor:pointer onclick=\"searchNopo('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['kodejabatan'].'/'.$_SESSION['lang']['tipekaryawan']." ','<div id=formPencariandata></div>',event)\"</td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['premi']."/Insentif</td>\r\n\t   <td><input type=text class=myinputtextnumber id=premiIns style=width:150px; onkeypress='return angka_doang(event)' /></td>\r\n\t </tr>\t \r\n\t </table>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_5premitetap','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset  style=width:750px;><legend>'.$_SESSION['lang']['list']."</legend>\r\n     <table><tr>\r\n        <td>".$_SESSION['lang']['tipetransaksi']." </td>\r\n        <td><select id=tpTransaksi2 style='width:150px;' onchange='loadData()'>".$optTipe2."</select></td>\r\n\t </tr>\t</table>\r\n     <table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n           <td>".$_SESSION['lang']['tipetransaksi']."</td>\r\n           <td>".$_SESSION['lang']['kodejabatan'].'/'.$_SESSION['lang']['tipekaryawan']."</td>\r\n\t   <td>".$_SESSION['lang']['premi']."/insentif</td>\r\n        \r\n           <td>".$_SESSION['lang']['action']."</td>    \r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n     \r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>