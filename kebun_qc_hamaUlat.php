<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_qc_hamaUlat.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n\r\n\r\n\r\n\r\n";
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$a = 'select * from '.$dbname.'.setup_kegiatan order by namakegiatan asc';
$b = mysql_query($a) ;
while ($c = mysql_fetch_assoc($b)) {
    $optKeg .= "<option value='".$c['kodekegiatan']."'>".$c['namakegiatan'].' - '.$c['kelompok'].' - '.$c['satuan'].'</option>';
}
$optBarang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$d = 'select * from '.$dbname.".log_5masterbarang  where kodebarang like '312%' order by namabarang asc";
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
$i = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_qc_hama order by periode desc limit 10';
$j = mysql_query($i) ;
while ($k = mysql_fetch_assoc($j)) {
    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';
}
for ($t = 0; $t < 24; ++$t) {
    if (strlen($t) < 2) {
        $t = '0'.$t;
    }

    $jm .= '<option value='.$t.' '.((0 === $t ? 'selected' : '')).'>'.$t.'</option>';
}
for ($y = 0; $y < 60; ++$y) {
    if (strlen($y) < 2) {
        $y = '0'.$y;
    }

    $mnt .= '<option value='.$y.' '.((0 === $y ? 'selected' : '')).'>'.$y.'</option>';
}
$optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optMandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAstn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAlat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAlat .= "<option value='MISSBLOWER'>MISSBLOWER</option>";
$optAlat .= "<option value='BOR'>BOR</option>";
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'><legend><b>Hama</b></legend><table border=0 cellpadding=1 cellspacing=1>";
echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text class=myinputtext  id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['divisi']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdDiv onchange=getAfd() style=\"width:100px;\">".$optDiv."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['afdeling']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdAfd  onchange=getBlok() style=\"width:100px;\">".$optAfd."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['blok']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdBlok style=\"width:100px;\">".$optBlok."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['lapPersonel']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=tenagakerja onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jammulai']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<select id=jm1 name=jmId >".$jm.'</select>:<select id=mn1>'.$mnt."</select>\r\n\t\t\t\t\t".$_SESSION['lang']['jamselesai']." :\r\n\t\t\t\t\t<select id=jm2 name=jmId2 >".$jm.'</select>:<select id=mn2>'.$mnt."</select>\r\n\t\t\t\t\r\n\t\t\t\t</td>\r\n\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['peralatan']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=alat style=\"width:100px;\"()>".$optAlat."</select></td>\r\n\t\t\t</tr>";
for ($i = 1; $i <= 1; ++$i) {
    echo "<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['namabarang'].' '.$i."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=bahan".$i.' style="width:100px;"()>'.$optBarang."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['dosis'].' '.$i."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=20 id=dosis".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t</tr>";
}
for ($i = 2; $i <= 3; ++$i) {
    echo "\t<td><input type=hidden maxlength=20 id=bahan".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t";
    echo "\t<td><input type=hidden maxlength=20 id=dosis".$i." onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t";
}
echo "\t\t<td><input type=hidden maxlength=20 id=dosis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t";
echo "\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['jumlahpokok']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=20 id=pokok onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['bensin']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=20 id=bensin onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['oli']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=20 id=oli onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td valign=top>".$_SESSION['lang']['catatan']."</td> \r\n\t\t\t\t\t<td valign=top>:</td>\r\n\t\t\t\t\t<td><textarea rows=3 colspan=5 id=catatan onkeypress=\"return_tanpa_kutip(event);\"></textarea></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['pengawasan']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=pengawas style=\"width:100px;\">".$optMandor."</select></td>\r\n\t\t\t\t</tr>\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['pendamping']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=asisten style=\"width:100px;\">".$optAstn."</select></td>\r\n\t\t\t\t</tr>\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['mengetahui']."</td> \r\n\t\t\t\t\t<td>:</td> \r\n\t\t\t\t\t<td><select id=mengetahui style=\"width:100px;\">".$optKadiv."</select></td>\r\n\t\t\t\t</tr>\t\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t";
echo '<tr><td><button class=mybutton onclick=simpan()>'.$_SESSION['lang']['save']."</button>\r\n\t\t<button class=mybutton onclick=cancel()>".$_SESSION['lang']['baru'].'</button></td></tr>';
echo '</table></fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo "<fieldset style='float:left;'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t".$_SESSION['lang']['divisi'].' : <select id=kdDivSch style="width:100px;" onchange=loadData()>'.$optDiv."</select>\r\n\t\t".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;" onchange=loadData()>'.$optPer."</select>\r\n\t\t\r\n\t\t\r\n\t\t\r\n\t\t\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>