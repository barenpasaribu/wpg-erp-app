<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_qc_panen.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n\r\n";
$optKeg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$a = 'select * from '.$dbname.'.setup_kegiatan order by namakegiatan asc';
$b = mysql_query($a) ;
while ($c = mysql_fetch_assoc($b)) {
    $optKeg .= "<option value='".$c['kodekegiatan']."'>".$c['namakegiatan'].' - '.$c['kelompok'].' - '.$c['satuan'].'</option>';
}
$optDiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h = mysql_query($g) ;
while ($i = mysql_fetch_assoc($h)) {
    $optDiv .= "<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi'].'</option>';
}
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct substr(tanggalcek,1,7) as periode from '.$dbname.'.kebun_qc_panenht order by periode desc limit 10';
$j = mysql_query($i) ;
while ($k = mysql_fetch_assoc($j)) {
    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';
}
$optAfd = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optMandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optAstn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "\r\n\r\n";
$frm[0] = '';
$frm[1] = '';
OPEN_BOX();
$frm[0] .= '<fieldset>';
$frm[0] .= '<legend><b>'.$_SESSION['lang']['header'].'</b></legend>';
$frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= "\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['cek']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text class=myinputtext  id=tanggalcek onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['panen']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text class=myinputtext  id=tanggalpanen onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n\t\t\t\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['divisi']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdDiv onchange=getAfd() style=\"width:100px;\">".$optDiv."</select></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['diperiksa']."</td> \r\n\t\t\t\t<td>:</td> \r\n\t\t\t\t<td><select id=diperiksa style=\"width:100px;\">".$optKadiv."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['afdeling']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdAfd  onchange=getBlok() style=\"width:100px;\">".$optAfd."</select></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['pendamping']."</td> \r\n\t\t\t\t<td>:</td> \r\n\t\t\t\t<td><select id=pendamping style=\"width:100px;\">".$optKar."</select></td>\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['blok']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><select id=kdBlok style=\"width:100px;\">".$optBlok."</select></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['mengetahui']."</td> \r\n\t\t\t\t<td>:</td> \r\n\t\t\t\t<td><select id=mengetahui style=\"width:100px;\">".$optKadiv."</select></td>\t\r\n\t\t\t</tr> \r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['pusingan']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=pusingan onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t<button class=mybutton id=saveHeader onclick=saveHeader()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t\t<button class=mybutton id=cancelHeader onclick=cancel()>".$_SESSION['lang']['baru']."</button>\t\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\t\t\t\r\n</table></fieldset>";
$frm[0] .= "<div id=detailForm  style='display:none;'>";
$frm[0] .= '<fieldset>';
$frm[0] .= '<legend><b>'.$_SESSION['lang']['detail'].'</b></legend>';
$frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= "\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['nourut'].' '.$_SESSION['lang']['pokok']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=nopokok onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['brondolan'].' '.$_SESSION['lang']['tdkdikutip']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=brdtdkdikutip onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['panen']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=jjgpanen onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['rumpukan']."</td>\r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=checkbox id=rumpukan value=0 /></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=jjgtdkpanen onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['piringan']."</td>\r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=checkbox id=piringan value=0 /></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jjg'].'  '.$_SESSION['lang']['tidakdikumpul']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=jjgtdkkumpul onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['jalur'].' '.$_SESSION['lang']['panen']."</td>\r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=checkbox id=jalurpanen value=0 /></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['mentah']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=jjgmentah onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t\t<td style=\"width:25px;\"/></td>\r\n\t\t\t\t<td>".$_SESSION['lang']['tukulan']."</td>\r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=checkbox id=tukulan value=0 /></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['menggantung']."</td> \r\n\t\t\t\t<td>:</td>\r\n\t\t\t\t<td><input type=text maxlength=10 id=jjggantung onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t<button class=mybutton id=saveDetail onclick=saveDetail()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t\t<button class=mybutton id=cancelDetail onclick=cancel()>".$_SESSION['lang']['selesai']."</button>\t\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\t";
$frm[0] .= '</table></fieldset>';
$frm[0] .= "\t<div id=containList  style='display:none;'>\r\n\t\t\t</div>";
$frm[1] .= "<fieldset style='float:left;'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t".$_SESSION['lang']['divisi'].' : <select id=kdDivSch style="width:100px;" onchange=loadData()>'.$optDiv."</select>\r\n\t\t".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;" onchange=loadData()>'.$optPer."</select>\t\t\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 250, 800);
CLOSE_BOX();
echo close_body();

?>