<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
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
echo "\";</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/kebun_restan.js\"></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n";
$optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKdorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select kodeorg from '.$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optKdorg .= '<option value='.$rOrg2['kodeorg'].'>'.$optNmOrg[$rOrg2['kodeorg']].'</option>';
}
$optKdorg2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg3 = 'select kodeorg from '.$dbname.".setup_blok where  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
$qOrg3 = mysql_query($sOrg3) ;
while ($rOrg3 = mysql_fetch_assoc($qOrg3)) {
    $optKdorg2 .= '<option value='.$rOrg3['kodeorg'].'>'.$optNmOrg[$rOrg3['kodeorg']].'</option>';
}
$tglHrini = date('Ymd');
echo "<div id='formIsian' style='display:block;'>";
OPEN_BOX('', '<b>Input Restan</b>');
$frm[0] .= "<input type='hidden' id='proses1' value='saveTab1' /><input type='hidden' id='oldJnsbibit'  /><fieldset style='width:350px;float:left'><legend>".$_SESSION['lang']['tnmbibit'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type='text' class='myinputtext' id='tglRestan' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td></tr>\r\n<tr><td>".$_SESSION['lang']['blok'].'</td><td>:</td><td><select id=kdBlokRestan style=width:150px>'.$optKdorg2."</select></td></tr>\r\n<tr><td>JJG Panen</td><td>:</td><td><input type='text' class='myinputtextnumber' style='width:150px;' id='jjgPanen' onkeypress='return angka_doang(event)' value='0'   /></td></tr>\r\n<tr><td>JJG Kirim</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jjgKrm' onkeypress='return angka_doang(event)' value='0' /></td></tr>\r\n<tr><td>Umur Restan</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='umrRestan' onkeypress='return angka_doang(event)' maxlength=45 value='0'  />&nbsp;Jam</td></tr>";
$frm[0] .= "<tr><td>Catatan</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='cttn' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr></table>";
$frm[0] .= '<tr><td colspan=3 align=center><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(1)  >'.$_SESSION['lang']['save'].'</button><button class=mybutton id=canbtlTmbl name=canbtlTmbl onclick=cancelData1()  >'.$_SESSION['lang']['cancel'].'</button></td></tr>';
$frm[0] .= '</fieldset>';
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
$qPeriode = mysql_query($sPeriode) || exit(mysql_error($sPeriode));
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
}
$frm[0] .= '<div style=clear:both;>&nbsp;</div>';
$frm[0] .= '<fieldset style=width:550px;><legend>'.$_SESSION['lang']['datatersimpan'].'</legend>';
$frm[0] .= ''.$_SESSION['lang']['periode'].' : <select id=periodeCari onchange=getCari()>'.$optPeriode."</select>\r\n    &nbsp;".$_SESSION['lang']['blok'].' : <select id=kdBlokCari style=width:150px onchange=getCari()>'.$optKdorg2.'</select><br />';
$frm[0] .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td  rowspan=2>No</td>\r\n            <td  rowspan=2>".$_SESSION['lang']['tanggal']."</td>\r\n            <td  rowspan=2>".$_SESSION['lang']['blok']."</td>\r\n            <td  colspan=2 align=center>Panen</td>\r\n            <td  colspan=2  align=center>Kirim</td>\r\n            <td  rowspan=2 colspan=2>Action</td>\r\n            </tr>\r\n            <tr><td align=center>Janjang</td><td align=center>KG</td><td align=center>Janjang</td><td align=center>KG</td></tr>\r\n            </thead><tbody id=containData1><script>loadData1()</script> \r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optBlok = $optAfd;
$optUnit .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$optNm[$_SESSION['empl']['lokasitugas']].'</option>';
$arr = '##kdUnit##afdId##BlokId##periodeId';
$frm[1] .= "\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Laporan Restan</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\" onchange='getAfd()'>".$optUnit."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"afdId\" name=\"afdId\" style=\"width:150px\"  onchange='getBlok()'>".$optAfd."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['blok'].'</label></td><td><select id="BlokId" name="BlokId" style="width:150px">'.$optBlok."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['periode'].'</label></td><td><select id="periodeId" name="periodeId" style="width:150px">'.$optPeriode."</select></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n<button onclick=\"zPreview('kebun_slave_restan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n<button onclick=\"zPdf('kebun_slave_restan','".$arr."','printContainer')\" class=\"mybutton\">PDF</button>    \r\n<button onclick=\"zExcel(event,'kebun_slave_restan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>";
$frm[1] .= "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>";
$hfrm[0] = 'Input Restan';
$hfrm[1] = 'Laporan Restan';
drawTab('FRM', $hfrm, $frm, 150, 700);
echo "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>