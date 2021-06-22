<?php



echo "<!--ind-->\r\n\r\n";
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/kebun_5nourutmandor.js'></script>\r\n\r\n\r\n";
include 'master_mainMenu.php';
$optaktif = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optaktif .= '<option value=0>Tidak Aktif</option>';
$optaktif .= '<option value=1>Aktif</option>';
$optnik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql = 'SELECT namakaryawan,karyawanid FROM '.$dbname.".datakaryawan where kodejabatan='37' ";
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optnik .= '<option value='.$data['karyawanid'].'>'.$data['namakaryawan'].'</option>';
}
$optkar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sql1 = 'SELECT namakaryawan,karyawanid FROM '.$dbname.".datakaryawan where tipekaryawan in('2','3') ";
$qry1 = mysql_query($sql1) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry1)) {
    $optkar .= '<option value='.$data['karyawanid'].'>'.$data['namakaryawan'].'</option>';
}
echo "\r\n\r\n";
OPEN_BOX('', '<b>Karyawan Kemandoran</b>');
echo "<br /><br /><fieldset style='float:left;'>\r\n\t\t<legend>".$_SESSION['lang']['entryForm']."</legend> \r\n\t\t\t<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td>Nik. Mandor<td>:</td></td><td><select id=nm style=\"width:125px;\" >".$optnik."</select></td></tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td width=100>No. Urut<td width=10>:</td></td><td><input type=text id=nu size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:25px;\"></td></tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td>Karyawan ID<td>:</td></td><td><select id=ki style=\"width:125px;\" >".$optkar."</select></td></tr>\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\r\n\t\t\t\t<tr><td>Status<td>:</td></td><td><select id=st style=\"width:125px;\" >".$optaktif."</select></td></tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr><td></td><td></td><br />\r\n\t\t\t\t\t<td><br /><button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t<button class=mybutton onclick=hapus()>Hapus</button></td></tr>\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>\r\n\t\t\t\t\t<input type=hidden id=oldnm value='insert'>\r\n\t\t\t\t\t<input type=hidden id=oldnu value='insert'>\r\n\t\t\t\t\t<input type=hidden id=oldki value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset>';
echo '<legend><b>'.$_SESSION['lang']['datatersimpan'].'</b></legend>';
echo "<div id=container><table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t\t <td align=center style='width:5px;'>No</td>\r\n\t\t\t <td align=center style='width:125px;'>Nik Mandor</td>\r\n\t\t\t <td align=center style='width:50px;'>No. Urut</td>\r\n\t\t\t <td align=center style='width:125px;'>Karyawan ID</td>\r\n\t\t\t <td align=center style='width:75px;'>Status Aktif</td>\r\n\t\t\t <td align=center style='width:40px;'>Aksi</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id='containerData'><script>loadData()</script>\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
echo '</fieldset>';
CLOSE_BOX();
echo close_body();

?>