<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_5fasilitasMpp.js'></script>\r\n";
$arr = '##thnBudget##kdJabatan##kdBarang##hrgSat##sat##jmlhBrng##method##totBrg##oldKdBrg';
include 'master_mainMenu.php';
OPEN_BOX();
$optGol = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sGol = 'select * from '.$dbname.'.sdm_5jabatan order by namajabatan asc';
$qGol = mysql_query($sGol) || exit(mysql_error($sGol));
while ($rGol = mysql_fetch_assoc($qGol)) {
    $optGol .= "<option value='".$rGol['kodejabatan']."'>".$rGol['namajabatan'].'</option>';
}
$optReg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sReg = 'select distinct namabarang,kodebarang from '.$dbname.'.log_5masterbarang order by namabarang asc';
$qReg = mysql_query($sReg) || exit(mysql_error($sReg));
while ($rReg = mysql_fetch_assoc($qReg)) {
    $optReg .= "<option value='".$rReg['kodebarang']."'>".$rReg['kodebarang'].' ['.$rReg['namabarang'].']</option>';
}
echo "<input type='hidden' id='method' name='method' value='insert' />";
echo "<fieldset style=width:290px;>\r\n     <legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['fasiltasmpp']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=thnBudget name=thnBudget onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n\t   <td><select id=kdJabatan name=kdJabatan style=\"width:150px;\" >".$optGol."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n       <tr><td>".$_SESSION['lang']['namabarang']."</td>\r\n\t   <td><select id=kdBarang name=kdBarang style=\"width:150px;\" onchange='getSatuan()' >".$optReg."</select>&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."' onclick=\"searchBrg('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang'].'</legend>'.$_SESSION['lang']['find'].'&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>'.$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\"></td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hrgSat name=hrgSat  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 onblur=\"kalikan()\"></td>\r\n\t </tr>\t \r\n\t  <tr>\r\n\t   <td>".$_SESSION['lang']['satuan']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=sat name=sat disabled onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['jumlah']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=jmlhBrng name=jmlhBrng  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 onblur=\"kalikan()\"></td>\r\n\t </tr>\t\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['total']."</td>\r\n\t   <td><input type=text class=myinputtextnumber disabled id=totBrg name=totBrg  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>\r\n\t </tr>\t\r\n\r\n\t </table>\r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_5fasilitasMpp','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n        <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n<input type=hidden id=oldKdBrg value='' />\r\n     </fieldset>";
echo '</div>';
CLOSE_BOX();
OPEN_BOX();
$optData = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$str = 'select distinct tahunbudget from '.$dbname.'.sdm_5transportpjd order by tahunbudget desc';
$res = mysql_query($str) || exit(mysql_error($str));
while ($rData = mysql_fetch_assoc($res)) {
    $optData .= "<option value='".$rData['tahunbudget']."'>".$rData['tahunbudget'].'</option>';
}
echo "<table><tr>\r\n    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='loadData()'>".$optData."</select></td>\r\n    <td>".$_SESSION['lang']['kodejabatan']." <select id=kdJabtanHead style='width:100px' onchange='loadData()'>".$optGol."</select></td>\r\n   \r\n    </tr></table>";
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n\t   <td>".$_SESSION['lang']['namabarang']."</td>\r\n\t   <td>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t   <td>".$_SESSION['lang']['satuan']."</td>\r\n            <td>".$_SESSION['lang']['jumlah']."</td>\r\n            <td>".$_SESSION['lang']['total']."</td>\r\n\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>