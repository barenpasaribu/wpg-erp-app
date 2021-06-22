<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/pabrik_kelengkapanloses.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/iReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
$optProduk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct produk from '.$dbname.'.pabrik_5kelengkapanloses';
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    $optProduk .= "<option value='".$d['produk']."'>".$d['produk'].'</option>';
}
$frm[0] = '';
$frm[1] = '';
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi where kodeorganisasi like '%M' and length(kodeorganisasi)=4 ORDER BY kodeorganisasi";
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$arrLaporan = '##kodeorgLap##tglLap##produkLap';
echo "\r\n\r\n\r\n\r\n";
OPEN_BOX();
$frm[0] .= "<fieldset>";
$frm[0] .= '<legend><b>'.$_SESSION['lang']['kelengkapanloses'].'</b></legend>';
$frm[0] .= "<fieldset>";
$frm[0] .= '<legend>'.$_SESSION['lang']['form'].'</legend>';
$frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= "<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['kodeorg']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorg onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text class=myinputtext  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['produk']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=produk onchange=getForm() style=\"width:150px;\">".$optProduk."</select></td>\r\n\t\t\t\t\t</tr>";
$frm[0] .= '</table></fieldset>';
$frm[0] .= '<div id=form style=display:none>';
$frm[0] .= "<fieldset>";
$frm[0] .= '<legend>'.$_SESSION['lang']['form'].'</legend>';
$frm[0] .= '<table id=isi border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= '</table>';
$frm[0] .= '</fieldset></div>';
$frm[0] .= '<div id=editForm style=display:none>';
$frm[0] .= "<fieldset>";
$frm[0] .= '<legend>'.$_SESSION['lang']['edit'].' '.$_SESSION['lang']['form'].'</legend>';
$frm[0] .= "\t<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['kodeorg']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text maxlength=4 disabled value='".$_SESSION['empl']['lokasitugas']."' id=kodeorgEdit onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text class=myinputtext disabled  id=tglEdit onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['produk']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><select id=produkEdit disabled style=\"width:150px;\">".$optProduk."</select></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['namabarang']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=barangEdit disabled maxlength=50 disabled onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['nilai']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text id=inpEdit onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t\t\t</tr> \r\n\t\t\t\t\t\t\t\t<input type=hidden id=idEdit disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\">\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=saveEdit()>Simpan</button>\r\n\t\t\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t</table>";
$frm[0] .= '</fieldset></div>';
$frm[0] .= "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t<td>:</td>\r\n\t\t\t<td><input type=text class=myinputtext onchange=loadData() id=tglsch onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t\t</tr>\r\n\t\t\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
$frm[0] .= '</fieldset>';
$frm[1] = "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['kelengkapanloses']."</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td> \r\n\t\t<td>:</td>\r\n\t\t<td><select id=kodeorgLap style='width:150px;'>".$optOrg."</select></td>\r\n\t</tr> \r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t<td>:</td>\r\n\t\t<td><input type=text class=myinputtext  id=tglLap onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>\r\n\t</tr> \r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['produk']."</td> \r\n\t\t<td>:</td>\r\n\t\t<td><select id=produkLap style=\"width:150px;\">".$optProduk."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=100>\r\n\t\t<button onclick=iPreview('pabrik_slave_kelengkapanloses','".$arrLaporan."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t<button onclick=iExcel(event,'pabrik_slave_kelengkapanloses.php','".$arrLaporan."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t\r\n\t\t<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>\r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>\r\n\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer'  >\r\n</div></fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['printArea'];
drawTab('FRM', $hfrm, $frm, 250, 800);
CLOSE_BOX();
echo close_body();
echo "\t\t\t\t\r\n";

?>