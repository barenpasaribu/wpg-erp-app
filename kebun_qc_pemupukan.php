<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/kebun_qc_pemupukan.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n";
for ($i = 0; $i < 24; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $jam .= '<option value='.$i.'>'.$i.'</option>';
}
for ($i = 0; $i < 60; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $mnt .= '<option value='.$i.'>'.$i.'</option>';
}
$optDiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h = mysql_query($g) ;
while ($i = mysql_fetch_assoc($h)) {
    $optDiv .= "<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi'].'</option>';
}
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_qc_pemupukanht order by periode desc limit 10';
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
$optBarang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$d = 'select * from '.$dbname.".log_5masterbarang  where kelompokbarang='045' order by namabarang asc";
$e = mysql_query($d) ;
while ($f = mysql_fetch_assoc($e)) {
    $optBarang .= "<option value='".$f['kodebarang']."'>".$f['namabarang'].' - '.$f['satuan'].'</option>';
}
echo "\r\n";
$frm[0] = '';
$frm[1] = '';
OPEN_BOX();
$frm[0] .= '<fieldset style="width:975px;">';
$frm[0] .= '<legend><b>'.$_SESSION['lang']['header'].'</b></legend>';
$frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= "\r\n                            <tr>\r\n                                <td>".$_SESSION['lang']['tanggal']."</td><td>:</td>\r\n                                <td><input type=text class=myinputtext  id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n                                <td>".$_SESSION['lang']['jamkerja']."</td><td>:</td>\r\n                                <td><select id=jamMulai>".$jam.'</select>:<select id=mntMulai>'.$mnt."</select></td><td>S/d</td>\r\n                                <td><select id=jamSelesai>".$jam.'</select>:<select id=mntSelesai>'.$mnt."</select></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>Divisi</td><td>:</td>\r\n                                <td><select id=kodedivisi name=kodedivisi style=width:150px onchange=getAfdeling()>".$optDiv."</select></td>\r\n                                <td>".$_SESSION['lang']['jumlahpekerja']."</td><td>:</td>\r\n                                <td colspan=5><input type=text id=jumlahpekerja name=jumlahpekerja value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\"></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>".$_SESSION['lang']['afdeling']."</td><td>:</td>\r\n                                <td><select id=kodeafdeling name=kodeafdeling style=width:250px onchange=getBlok()>".$optAfd."</select></td>\r\n                              \r\n\t\t\t\t\t\t\t  \r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t<td>Pupuk & Dosis</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=barang style=\"width:100px;\">".$optBarang."</select></td>\r\n\t\t\t\t\t<td colspan=5><input type=text id=dosis name=dosis value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:50px;\"></td>\r\n                            </tr>\r\n\t\t\t\t\t\t\t\r\n                            <tr>\r\n                                <td>".$_SESSION['lang']['blok']."</td><td>:</td>\r\n                                <td><select id=kodeblok name=kodeblok style=width:250px>".$optBlok."</select></td>\r\n                                <td>".$_SESSION['lang']['teraplikasi']."</td><td>:</td>\r\n                                <td colspan=5><input type=text id=teraplikasi name=teraplikasi value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;Sak</td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>Nama Pengawas</td><td>:</td>\r\n                                <td><select id=namapengawas name=namapengawas style=width:150px>".$optAstn."</select></td>\r\n                                <td>".$_SESSION['lang']['kondisilahan']."</td><td>:</td>\r\n                                <td colspan=5><input type=text id=kondisilahan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtextstyle=\"width:150px;\"></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td valign=top>".$_SESSION['lang']['comment']."</td> \r\n                                <td valign=top>:</td>\r\n                                <td><textarea cols=35 rows=5 id=comment onkeypress=\"return tanpa_kutip(event);\"></textarea></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>Petugas.QC</td><td>:</td>\r\n                                <td><select id=pengawas name=pengawas style=width:150px>".$optMandor."</select></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>".$_SESSION['lang']['pendamping']."</td><td>:</td>\r\n                                <td><select id=asisten name=asisten style=width:150px>".$optAstn."</select></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td>".$_SESSION['lang']['mengetahui']."</td><td>:</td>\r\n                                <td><select id=mengetahui name=mengetahui style=width:150px>".$optKadiv."</select></td>\r\n                            </tr>\r\n                            <tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t<button class=mybutton id=saveHeader onclick=saveHeader()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t\t<button class=mybutton id=cancelHeader onclick=cancel()>".$_SESSION['lang']['baru']."</button>\t\r\n\t\t\t\t</td>\r\n                            </tr>\t\t\t\r\n</table></fieldset>";
$frm[0] .= "<div id=detailForm  style='display:none;'>";
$frm[0] .= '<fieldset>';
$frm[0] .= '<legend><b>'.$_SESSION['lang']['detail'].'</b></legend>';
$frm[0] .= '<table border=0 cellpadding=1 cellspacing=1>';
$frm[0] .= "  <tr>\r\n                <td>No. Jalur</td> \r\n                <td>:</td>\r\n                <td><input type=text maxlength=10 id=nojalur onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>\r\n            </tr>\r\n            <tr>\r\n                <td>".$_SESSION['lang']['pokok'].' '.$_SESSION['lang']['dipupuk']."</td> \r\n                <td>:</td>\r\n                <td><input type=text maxlength=10 id=pkkdipupuk onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>\r\n            </tr>\r\n            <tr>\r\n                <td>".$_SESSION['lang']['pokok'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['dipupuk']."</td> \r\n                <td>:</td>\r\n                <td><input type=text maxlength=10 id=pkktdkdipupuk onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>\r\n            </tr>\r\n            <tr>\r\n                <td>".$_SESSION['lang']['apl'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['standar']."</td> \r\n                <td>:</td>\r\n                <td><input type=text maxlength=10 id=apltdkstandar onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>\r\n            </tr>\r\n            <tr>\r\n                <td valign=top>".$_SESSION['lang']['keterangan']."</td> \r\n                <td valign=top>:</td>\r\n                <td><textarea cols=35 rows=5 id=keterangan onkeypress=\"return tanpa_kutip(event);\"></textarea></td>\r\n            </tr>\r\n            <tr>\r\n                <td>\r\n                <button class=mybutton id=saveDetail onclick=saveDetail()>".$_SESSION['lang']['save']."</button>\r\n                <button class=mybutton id=cancelDetail onclick=cancel()>".$_SESSION['lang']['selesai']."</button>\t\r\n                </td>\r\n            </tr>\t";
$frm[0] .= '</table></fieldset>';
$frm[0] .= "\t<div id=containList  style='display:none;'>\r\n\t\t\t</div>";
$frm[1] .= "<fieldset style='float:left;'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t".$_SESSION['lang']['kebun'].' : <select id=kdKebunSch style="width:100px;">'.$optDiv."</select>\r\n\t\t".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;">'.$optPer."</select>\r\n                <button class=mybutton id=preview onclick=loadDataPrev()>".$_SESSION['lang']['preview']."</button>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 250, 1000);
CLOSE_BOX();
echo close_body();

?>