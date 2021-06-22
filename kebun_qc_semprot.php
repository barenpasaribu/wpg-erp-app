<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_qc_semprot.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/iReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n\r\n\r\n\r\n\r\n";
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$a = 'select * from '.$dbname.'.setup_kegiatan order by namakegiatan asc';
$b = mysql_query($a) ;
while ($c = mysql_fetch_assoc($b)) {
    $optKeg .= "<option value='".$c['kodekegiatan']."'>".$c['namakegiatan'].' - '.$c['kelompok'].' - '.$c['satuan'].'</option>';
}
$optBarang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$d = 'select * from '.$dbname.".log_5masterbarang  where kelompokbarang='312' order by namabarang asc";
$e = mysql_query($d) ;
while ($f = mysql_fetch_assoc($e)) {
    $optBarang .= "<option value='".$f['kodebarang']."'>".$f['namabarang'].' - '.$f['satuan'].'</option>';
}
$optDiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h = mysql_query($g) ;
while ($i = mysql_fetch_assoc($h)) {
    $optDiv .= "<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi'].'</option>';
}
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_qc_semprot order by periode desc limit 10';
$j = mysql_query($i) ;
while ($k = mysql_fetch_assoc($j)) {
    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';
}
$optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optMandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAstn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'><legend><b>Semprot Kimia</b></legend><fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['header'].'</legend>';
echo '<table border=0 cellpadding=1 cellspacing=1>';
echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text class=myinputtext  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['divisi']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdDiv onchange=getAfd() style=\"width:100px;\">".$optDiv."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['afdeling']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdAfd  onchange=getBlok() style=\"width:100px;\">".$optAfd."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['blok']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdBlok onchange=getData() style=\"width:100px;\">".$optBlok."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['luasareal']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 disabled id=luasAreal onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jumlahpokok']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 disabled id=jmlPkk onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdKeg style=\"width:100px;\">".$optKeg."</select></td>\r\n\t\t\t</tr> \r\n\t\t</table>\r\n\t</fieldset>";
echo "<fieldset  style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['material'].' & Gulma</legend>';
echo '<fieldset><legend></legend><table border=0 cellpadding=1 cellspacing=1>';
echo "\r\n\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['takaranpakai']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=20 id=dosis onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jenisgulmadominan']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=20 id=jenisgulma onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t</tr>\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['kondisigulma']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=20 id=kondisigulma onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:100px;\"></td>\r\n\t\t\t</tr>\t\t\r\n\t\t</table>\r\n\t</fieldset>";
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['material'].'</legend>';
echo '<table table class=sortable border=0 cellpadding=1 cellspacing=1 >';
echo "\r\n\t\t\t<tr class=rowheader>\r\n\t\t\t\t<td align=center>".$_SESSION['lang']['dosis']."</td>\r\n\t\t\t\t<td align=center>".$_SESSION['lang']['material']."</td>\r\n\t\t\t\t<td align=center>".$_SESSION['lang']['dosis']."</td>\r\n\t\t\t\t<td align=center>".$_SESSION['lang']['pengeluaranbarang']."</td>\r\n\t\t\t\t<td align=center>".$_SESSION['lang']['pakaimaterial']."</td>\r\n\t\t\t</tr>";
for ($i = 1; $i <= 3; ++$i) {
    echo "<tr class=rowcontent>\r\n\t\t\t\t\t<td align=center>".$i."</td> \r\n\t\t\t\t\t<td><select id=dosismaterial".$i.' style="width:100px;">'.$optBarang."</select></td>\r\n\t\t\t\t\t<td><input type=text maxlength=10 id=dosisjumlah".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>\r\n\t\t\t\t\t\r\n\t\t\t\t\t<td><input type=text maxlength=10 id=jumlahdiambil".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:110px;\"></td>\r\n\t\t\t\t\t<td><input type=text maxlength=10 id=jumlahdipakai".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:70px;\"></td>\r\n\t\t\t\t</tr>";
}
echo "\r\n\t\t\t</table>\r\n\t\t</fieldset>\r\n\t\t</fieldset><fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['karyawan'].'</legend>';
echo '<table table class=sortable border=0 cellpadding=1 cellspacing=1 >';
echo "\r\n\t\t\t<tr class=rowheader>\r\n\t\t\t\t<td>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['hasil']."</td>\r\n\t\t\t</tr>";
for ($i = 1; $i <= 15; ++$i) {
    echo "\r\n\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$i."</td> \r\n\t\t\t\t\t<td><select id=karyawan".$i.' style="width:100px;">'.$optKar."</select></td>\r\n\t\t\t\t\t<td><input type=text maxlength=10 id=hasilkaryawan".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:50px;\"></td>\r\n\t\t\t\t</tr>";
}
echo "</table></fieldset><fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['keterangan'].'</legend>';
echo '<table border=0 cellpadding=1 cellspacing=1>';
echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td valign=top>".$_SESSION['lang']['keterangan']."</td> \r\n\t\t\t\t<td valign=top>:</td>\r\n\t\t\t\t<td><textarea cols=25 rows=6 id=keterangan onkeypress=\"return_tanpa_kutip(event);\"></textarea></td>\r\n\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['pengawasan']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=pengawas style=\"width:100px;\">".$optMandor."</select></td>\r\n\t\t\t\t</tr>\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['pendamping']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=asisten style=\"width:100px;\">".$optAstn."</select></td>\r\n\t\t\t\t</tr>\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['mengetahui']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=mengetahui style=\"width:100px;\">".$optKadiv."</select></td>\r\n\t\t\t\t</tr>\t\t\r\n\t\t\t\r\n\t\t</table>\r\n\t</fieldset>";
echo "<fieldset style='float:left;'>\t\r\n\t\t<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n\t\t<button class=mybutton onclick=cancel()>".$_SESSION['lang']['baru']."</button>\t\r\n\t\t</fieldset>";
echo '</fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo "<fieldset style='float:left;'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t".$_SESSION['lang']['kodeorg'].' : <select id=kdDivSch style="width:100px;" onchange=loadData()>'.$optDiv."</select>\r\n\t\t".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;" onchange=loadData()>'.$optPer."</select>\r\n\t\t\r\n\t\t\r\n\t\t\r\n\t\t\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>