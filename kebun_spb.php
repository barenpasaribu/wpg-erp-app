<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['suratPengantarBuah'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\">\r\n\r\nnmTmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nnmTmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n optIsi='";
$kodeOrg = substr($_SESSION['temp']['nSpb'], 8, 6);
$sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where `induk`='".$kodeOrg."' and tipe='BLOK' ORDER BY `namaorganisasi` ASC";
$query = mysql_query($sql) ;
$optBlok = '<option value=></option>';
while ($res = mysql_fetch_assoc($query)) {
    $optBlok .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
echo $optBlok;
echo "';\r\n</script>\r\n<script language=\"javascript\" src=\"js/kebun_spb.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n\r\n<div id=\"action_list\">\r\n";
for ($x = 0; $x <= 40; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$query = mysql_query($sql) ;
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
echo "<table cellspacing=1 border=0>\r\n     <tr valign=middle>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data('".$_SESSION['lang']['save']."','".$_SESSION['lang']['cancel']."')>\r\n           <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n         <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['nospb'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariSpb()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n         <td><fieldset><legend>".$_SESSION['lang']['exportData'].'</legend>';
echo $_SESSION['lang']['periode'].':<select id=periode nama=periode>'.$optPeriode.'</select>&nbsp;';
echo $_SESSION['lang']['kodeorg'].':<select id=unitOrg name=unitOrg>'.$optOrg.'</select>';
echo "&nbsp;<img onclick=dataKeExcel(event,'kebun_sbp_excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n         <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg></fieldset></td>\r\n         </tr>\r\n         </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"listSpb\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n<thead>\r\n<tr class=\"rowheader\">\r\n<td>No.</td>\r\n<td>";
echo $_SESSION['lang']['nospb'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n<script>loadData()</script>\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
$sORg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qOrg = mysql_query($sORg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg2 .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
for ($x = 0; $x <= 40; ++$x) {
    $dte = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPrd .= '<option value='.date('Y-m', $dte).'>'.date('Y-m', $dte).'</option>';
}
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:120px;\" onchange=\"getDiv(0)\"><option value=\"\"></option>";
echo $optOrg2;
echo "</select>\r\n<!--<input type=\"text\"  id=\"noSpb\" name=\"noSpb\" class=\"myinputtext\" style=\"width:120px;\" disabled=\"disabled\" />--></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['afdeling'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeDiv\" name=\"kodeDiv\" style=\"width:120px;\" ></select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['periode'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"period\" name=\"period\" style=\"width:120px;\" >";
echo $optPrd;
echo "</select>\r\n<!--<input type=\"text\"  id=\"noSpb\" name=\"noSpb\" class=\"myinputtext\" style=\"width:120px;\" disabled=\"disabled\" />--></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['nourut'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" id=\"nourut\" name=\"nourut\" class=\"myinputtextnumber\" style=\"width:120px;\" maxlength=\"7\" onkeypress=\"return angka_doang(event)\"  onblur=\"fillZero()\"/>\r\n<input type=\"hidden\"  id=\"noSpb\" name=\"noSpb\" class=\"myinputtext\" style=\"width:120px;\" disabled=\"disabled\" /></td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"tgl_ganti\" name=\"tgl_ganti\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:120px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detailSpb\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n<b>";
echo $_SESSION['lang']['nospb'];
echo "</b> : <input type=\"text\" id=\"detail_kode\" name=\"detail_kode\" disabled=\"disabled\" class=\"myinputtext\" style=\"width:150px;\" /><!--\".makeElement(\"detail_kode\",'text',\$noSpb,array('disabled'=>'disabled','style'=>'width:200px'));-->\r\n\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tbody id=\"detailIsi\">\r\n<tr><td>\r\n<input type=\"checkbox\" id=\"mnculSma\" onclick=\"getBlokSma()\" />";
echo $_SESSION['lang']['blok'].' '.$_SESSION['lang']['all'];
echo " \r\n<table id='ppDetailTable'>\r\n</table>\r\n</tbody>\r\n<tr><td id=\"tombol\">\r\n\r\n</td></tr>\r\n</table>\r\n</fieldset><br />\r\n<br />\r\n<div style=\"overflow:auto; height:300px;\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['datatersimpan'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<thead>\r\n <tr class=\"rowheader\">\r\n        <td>No.</td>\r\n    <td>";
echo $_SESSION['lang']['blok'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['bjr'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['janjang'];
echo "</td>\r\n    <td>";
echo $_SESSION['lang']['brondolan'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['mentah'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['busuk'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['matang'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['lewatmatang'];
echo "</td>\r\n    <td colspan=3>Action</td>\r\n    </tr>\r\n</thead>\r\n<tbody id=\"contentDetail\">\r\n</tbody>\r\n</table>\r\n\r\n</fieldset></div>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>