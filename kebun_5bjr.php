<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo '<script language=javascript>isidata="';
echo '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
echo "\";</script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/kebun_5bjr.js'></script>\r\n";
$optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$sBlok = 'select distinct kodeorg,bloklama from '.$dbname.".setup_blok where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ";
$qBlok = mysql_query($sBlok) ;
while ($rBlok = mysql_fetch_assoc($qBlok)) {
    $optBlok .= "<option value='".$rBlok['kodeorg']."'>".$rBlok['kodeorg'].' - '.$rBlok['bloklama'].'</option>';
}
$arr = '##thnProd##kdBlok##jmBjr##proses';
include 'master_mainMenu.php';
OPEN_BOX();
echo "<fieldset style=width:350px;float:left;>\r\n     <legend>".$_SESSION['lang']['bjr']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['tahunproduksi']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=thnProd name=thnProd onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 />\r\n           <button class=mybutton onclick=loadData() title=clik untuk get data>".$_SESSION['lang']['ok']."</button></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodeblok']."</td>\r\n\t   <td><select id='kdBlok'  style=\"width:150px;\" disabled>".$optBlok."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['bjr']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=jmBjr name=jmBjr onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=7  disabled /> </td>\r\n\t </tr>\r\n\r\n\t </table>\r\n\t <input type=hidden value=insert id=proses>\r\n\t <button class=mybutton onclick=saveFranco('kebun_slave_5bjr','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['done']."</button>\r\n     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
$str = 'select distinct substr(kodeorg,1,4) as kodeorg,tahunproduksi from '.$dbname.".kebun_5bjr where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by tahunproduksi desc";
$res = mysql_query($str) ;
echo '<div id=listThnProduksi><fieldset style=width:250px;float:left;><legend>'.$_SESSION['lang']['list'].' '.$_SESSION['lang']['tahunproduksi'].'</legend>';
echo '<table border=0 cellpadding=1 cellspacing=1><thead>';
echo '<tr class=rowheader><td>'.$_SESSION['lang']['kodeorg'].'</td><td>'.$_SESSION['lang']['tahunproduksi'].'</td></tr><tbody>';
while ($rowData = mysql_fetch_assoc($res)) {
    echo '<tr class=rowcontent><td>'.$optNmOrg[$rowData['kodeorg']].'</td><td>'.$rowData['tahunproduksi'].'</td></tr>';
}
echo '</tbody></table></fieldset></div><div id=listDataBjr style=display:none>';
echo '<fieldset style=width:650px;float:left;><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['kodeblok']."</td>\r\n           <td>".$_SESSION['lang']['bloklama']."</td>\r\n\t   <td>".$_SESSION['lang']['tahunproduksi']."</td>\r\n\t   <td>".$_SESSION['lang']['tahuntanam']."</td>\r\n\t   <td>".$_SESSION['lang']['jenisbibit']."</td>\r\n\t   <td>".$_SESSION['lang']['bjr']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
echo "</tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset></div>";
CLOSE_BOX();
echo close_body();

?>