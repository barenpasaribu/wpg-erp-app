<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_5hargaTicket.js'></script>\r\n";
$arr = '##thnBudget##kdGol##region##tktPes##tksi##airport##visa##byaLain##method';
include 'master_mainMenu.php';
OPEN_BOX();
$optGol = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sGol = 'select * from '.$dbname.'.sdm_5golongan order by namagolongan asc';
$qGol = mysql_query($sGol) || exit(mysql_error($sGol));
while ($rGol = mysql_fetch_assoc($qGol)) {
    $optGol .= "<option value='".$rGol['kodegolongan']."'>".$rGol['namagolongan'].'</option>';
}
$optReg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sReg = 'select * from '.$dbname.'.bgt_regional order by nama asc';
$qReg = mysql_query($sReg) || exit(mysql_error($sReg));
while ($rReg = mysql_fetch_assoc($qReg)) {
    $optReg .= "<option value='".$rReg['regional']."'>".$rReg['nama'].'</option>';
}
echo "<input type='hidden' id='method' name='method' value='insert' />";
echo "<fieldset style=width:250px;>\r\n     <legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['hargatiket']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=thnBudget name=thnBudget onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t   <td><select id=kdGol name=kdGol style=\"width:150px;\" >".$optGol."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t   <td><select id=region name=region style=\"width:150px;\" >".$optReg."</select></td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['tiketPes']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=tktPes name=tktPes  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t \r\n\t  <tr>\r\n\t   <td>".$_SESSION['lang']['taksi']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=tksi name=tksi  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['airportax']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=airport name=airport  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['visa']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=visa name=visa  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['biayalain']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=byaLain name=byaLain  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n\t </table>\r\n\t \r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_5hargaTicket','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$optData = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$str = 'select distinct tahunbudget from '.$dbname.'.sdm_5transportpjd order by tahunbudget desc';
$res = mysql_query($str) || exit(mysql_error($str));
while ($rData = mysql_fetch_assoc($res)) {
    $optData .= "<option value='".$rData['tahunbudget']."'>".$rData['tahunbudget'].'</option>';
}
echo "<table><tr>\r\n    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='loadData()'>".$optData."</select></td>\r\n    <td>".$_SESSION['lang']['kodegolongan']." <select id=kdGOlHead style='width:100px' onchange='loadData()'>".$optGol."</select></td>\r\n    <td>".$_SESSION['lang']['tujuan']." <select id=tujuanHead style='width:100px' onchange='loadData()'>".$optReg."</select></td>\r\n    </tr></table>";
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t   <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t   <td>".$_SESSION['lang']['tiketPes']."</td>\r\n\t   <td>".$_SESSION['lang']['taksi']."</td>\r\n            <td>".$_SESSION['lang']['airportax']."</td>\r\n            <td>".$_SESSION['lang']['visa']."</td>\r\n            <td>".$_SESSION['lang']['biayalain']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>