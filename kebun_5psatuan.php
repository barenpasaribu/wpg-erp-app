<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';

echo "<script language=javascript1.2 src='js/kebun_5psatuan.js'></script>\r\n";
$optkegiatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('ID' == $_SESSION['language']) {
    $fild = 'namaakun';
    $i = 'select * from '.$dbname.".setup_kegiatan where kelompok in ('BBT','TB','TBM','TM','PNN','MN','PN') order by namakegiatan asc";
    $n = mysql_query($i) ;
    while ($d = mysql_fetch_assoc($n)) {
        $optkegiatan .= "<option value='".$d['kodekegiatan']."'>".$d['namakegiatan'].' - '.$d['kodekegiatan'].' - '.$d['kelompok'].'</option>';
    }
} else {
    $fild = 'namaakun1';
    $i = 'select * from '.$dbname.".setup_kegiatan where kelompok in ('BBT','TB','TBM','TM','PNN','MN','PN') order by namakegiatan1 asc";
    $n = mysql_query($i) ;
    while ($d = mysql_fetch_assoc($n)) {
        $optkegiatan .= "<option value='".$d['kodekegiatan']."'>".$d['namakegiatan1'].' - '.$d['kodekegiatan'].' - '.$d['kelompok'].'</option>';
    }
}

$optkegiatan2 .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sNoakun = 'select distinct * from '.$dbname.".setup_kegiatan\r\n                  where kelompok in ('BBT','TB','TBM','TM','PNN','MN','PN') order by noakun asc";
$qNoakun = mysql_query($sNoakun) ;
while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
    $optkegiatan2 .= "<option value='".$rNoakun['kodekegiatan']."'>".$rNoakun['kodekegiatan'].' - '.$rNoakun['namakegiatan1'].' - '.$rNoakun['kelompok'].'</option>';
}
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
if ('ID' == $_SESSION['language']) {
    echo '<legend>Tarif Satuan Pekerjaan Non Panen</legend>';
} else {
    echo '<legend>Premi non Harvesting</legend>';
}

echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=4 id=regional onkeypress=\"return_tanpa_kutip(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodekegiatan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=kdkegiatan style=\"width:150px;\">".$optkegiatan."</select></td>\r\n\t\t\t\t</tr>\r\n\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['biaya']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=rp onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['insentif']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=insen onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['konversi']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=checkbox id=konversi value=0 /></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>";
echo "<fieldset style='text-align:left;width:650px;'>\r\n<legend><b>[Info]</b></legend>\r\n<p align=justify>Perubahan premi  hanya boleh dilaukan di awal bulan setelah tutup buku bulan sebelumnya dan input data bulan berjalan untuk BKM belum dilakukan. \r\n</p>";
echo '<fieldset><legend>'.$_SESSION['lang']['form'].'</legend><table>';
echo "<tr>\r\n     <td>".$_SESSION['lang']['kodekegiatan']."</td>\r\n     <td><select id=kdkegiatanCrPrsn style=\"width:150px;\">".$optkegiatan2."</select></td>\r\n     <td>".$_SESSION['lang']['biaya'].'</td>';
echo '<td><input type=text maxlength=8 id=prsnrpCr onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:100px;" />%</td></tr><tr>';
echo '<td>'.$_SESSION['lang']['insentif'].'</td>';
echo '<td><input type=text maxlength=8 id=prsninsenCr onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:100px;" />%</td>';
echo '<td><button class=mybutton onclick=upGrade()>'.$_SESSION['lang']['save'].'</button></td></tr>';
echo '</table></fieldset>';
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset  style=float:left;clear:both;><legend>'.$_SESSION['lang']['find'].' '.$_SESSION['lang']['data'].'</legend>';
echo '<table>';
echo '<tr><td>'.$_SESSION['lang']['kodekegiatan'].'</td><td><input type=text id=kdkegiatanCr style="width:150px;" onblur=loadData(0) /></td>';
echo '<td>'.$_SESSION['lang']['biaya']."</td>\r\n     <td><input type=text maxlength=8 id=rpCr onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\" onblur=loadData(0) /></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['insentif'].'</td><td><input type=text maxlength=8 id=insenCr onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:150px;"  onblur=loadData(0)/ ></td>';
echo '<td>'.$_SESSION['lang']['konversi']."</td>\r\n     <td><input type=checkbox id=konversiCr value=0  onclick=loadData(0) /></td></tr>";
echo '<tr><td colspan=4><button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button></td></tr>';
echo '</table></fieldset>';
echo "<fieldset style=float:left;clear:both;>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData(0)</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>