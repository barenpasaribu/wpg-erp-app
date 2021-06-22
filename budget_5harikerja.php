<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/budget_5harikerja.js'></script>\r\n\r\n";
$arr = '##tahunbudget##hrsetahun##hrminggu##hrlibur##hrliburminggu##hkeffektif##method##oldtahunbudget';
include 'master_mainMenu.php';
OPEN_BOX();
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['hk']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=4 /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['JumHrSetahun']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hrsetahun name=hrsetahun onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=4 value=365 onchange=tambah() /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['JumHrMinggu']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hrminggu name=hrminggu onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['JumHrLibur']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hrlibur name=hrlibur onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['JumHrLiburMinggu']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hrliburminggu name=hrliburminggu onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['hkefektif']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hkeffektif name=hkeffektif onkeypress=\"return tanpa_kutip(event);\" style=\"width:100px;\" maxlength=100 disabled /></td>\r\n\t </tr>\r\n\t </table>\r\n\t\t <input type=hidden value=insert id=method>\r\n\t\t <button class=mybutton onclick=savehk('log_slave_budget_5harikerja','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=oldtahunbudget name=oldtahunbudget />";
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from '.$dbname.'.bgt_hk order by tahunbudget desc';
$res = mysql_query($str);
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['budgetyear']."</td>\r\n\t   <td>".$_SESSION['lang']['JumHrSetahun']."</td>\r\n\t   <td>".$_SESSION['lang']['JumHrMinggu']."</td>\r\n\t   <td>".$_SESSION['lang']['JumHrLibur']."</td>\r\n\t   <td>".$_SESSION['lang']['JumHrLiburMinggu']."</td>\r\n\t   <td>".$_SESSION['lang']['hkefektif']."</td>\r\n\t   <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>