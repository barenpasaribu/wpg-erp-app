<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_5standarUsaku.js'></script>\r\n";
$arr = '##thnBudget##kdGol##ungSaku##ungMkn##htel##method';
include 'master_mainMenu.php';
OPEN_BOX();
$optGol = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sGol = 'select * from '.$dbname.'.sdm_5golongan order by namagolongan asc';
$qGol = mysql_query($sGol) || exit(mysql_error($sGol));
while ($rGol = mysql_fetch_assoc($qGol)) {
    $optGol .= "<option value='".$rGol['kodegolongan']."'>".$rGol['namagolongan'].'</option>';
}
echo "<input type='hidden' id='method' name='method' value='insert' />";
echo "<fieldset style=width:250px;>\r\n     <legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['standarduangsaku']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=thnBudget name=thnBudget onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t   <td><select id=kdGol name=kdGol style=\"width:150px;\" >".$optGol."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['uangsaku']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=ungSaku name=ungSaku style=\"width:150px;\" onkeypress=\"return angka_doang(event);\"  maxlength=20/></td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['uangmakan']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=ungMkn name=ungMkn  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t \r\n\t  <tr>\r\n\t   <td>".$_SESSION['lang']['hotel']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=htel name=htel  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n\t </table>\r\n\t \r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_5standardUsaku','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$optData = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$str = 'select distinct tahunbudget from '.$dbname.'.sdm_5sakupjd order by tahunbudget desc';
$res = mysql_query($str) || exit(mysql_error($str));
while ($rData = mysql_fetch_assoc($res)) {
    $optData .= "<option value='".$rData['tahunbudget']."'>".$rData['tahunbudget'].'</option>';
}
echo "<table><tr>\r\n    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='loadData()'>".$optData."</select></td>\r\n    <td>".$_SESSION['lang']['kodegolongan']." <select id=kdGOlHead style='width:100px' onchange='loadData()'>".$optGol."</select></td>\r\n    \r\n    </tr></table>";
echo "<fieldset style='width:450px;'><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n\t   <td>".$_SESSION['lang']['uangsaku']."</td>\r\n\t   <td>".$_SESSION['lang']['uangmakan']."</td>\r\n\t   <td>".$_SESSION['lang']['hotel']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>