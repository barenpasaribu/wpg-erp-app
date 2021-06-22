<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript1.2 src='js/vhc_5jenisKegiatan.js'></script>\r\n";
// $optkegiatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('ID' === $_SESSION['language']) {
    $fild = 'namaakun';
} else {
    $fild = 'namaakun1';
}

$i = 'select noakun,namaakun from '.$dbname.".keu_5akun where length(noakun)='7'";
// $n = mysql_query($i) || exit(mysql_error($conns));
// while ($d = mysql_fetch_assoc($n)) {
//     $optkegiatan .= "<option value='".$d['noakun']."'>".$d['noakun'].' - '.$d[$fild].'</option>';
// }

$optkegiatan = makeOption2($i ,
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'noakun',"captionfield"=> 'namaakun' ),null,true
);
$optnil .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
for ($nil = 0; $nil < 9; ++$nil) {
    $optnil .= "<option value='".$nil."'>".$nil.'</option>';
}
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['vhc_kegiatan'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=4 id=regional onkeypress=\"return_tanpa_kutip(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodekegiatan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=7 id=kdKegiatan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['namakegiatan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=45 id=nmKegiatan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['satuan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=10 id=satuan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['noakun']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=noakun style=\"width:150px;\">".$optkegiatan."</select></td>\r\n\t\t\t\t</tr>\r\n\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['basis']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=basis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['hargasatuan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=hrgSatuan onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['hargalbhbasis']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=hrgLbhBasis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>   \r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['hargaHariMinggu']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text maxlength=8 id=hrgHrMngg onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['isiauto']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><select id=auto style=width:150px>".$optnil."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>";
echo "<fieldset style='text-align:left;width:350px;float:left;'>\r\n<legend><b>[Info]</b></legend>\r\n<p align=justify>Perubahan premi  hanya boleh dilaukan di awal bulan setelah tutup buku bulan sebelumnya dan input data bulan berjalan untuk BKM belum dilakukan. \r\n</p>";
echo '<fieldset><legend>'.$_SESSION['lang']['form'].'</legend><table>';
echo "<tr>\r\n     <td rowspan=5 valign=top>Kenaikan</td>\r\n     <td>".$_SESSION['lang']['basis']."</td>\r\n     <td><input type=text maxlength=8 id=bsisPrsn onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr>";
echo '<tr><td>'.$_SESSION['lang']['hargasatuan'].'</td>';
echo '<td><input type=text maxlength=8 id=hrgStnPrsn onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:100px;" />%</td></tr>';
echo '<tr><td>'.$_SESSION['lang']['hargalbhbasis'].'</td>';
echo '<td><input type=text maxlength=8 id=hrgLbhBsisPrsn onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:100px;" />%</td></tr>';
echo '<tr><td>'.$_SESSION['lang']['hargaHariMinggu'].'</td>';
echo '<td><input type=text maxlength=8 id=hrgMnggPrsn onkeypress="return angka_doang(event);"  class=myinputtextnumber style="width:100px;" />%</td></tr>';
echo '<tr><td><button class=mybutton onclick=upGrade()>'.$_SESSION['lang']['save'].'</button></td></tr>';
echo "</table></fieldset></fieldset><fieldset style=float:left;width:470px;><legend><b>[Info]</b></legend><ol type=a>\r\n    <li>0=(basis*hargasatuan)+(kelebihan basis*hargalebihbasis),minggu=prestasi*hargaminggu</li>\r\n    <li>1=(sum(hargasatuan)- (sum(basis)/jumlahbaris))*2+(sum(basis)/jumlahbaris)),minggu=sum(hargaminggu)</li>\r\n    <li>2= jika MO01 maka UMP+hargalebihbasis per hari</li>\r\n    <li>3=(sum(hargasatuan)- (sum(basis)/jumlahbaris))*2+(sum(basis)/jumlahbaris)),minggu=prestasi*hargaminggu</li>\r\n    <li>4=tidak mengenal hari sum(hargasatuan)</li>\r\n    <li>6=(prestasi*hargasatuan)+(lebihbasis*hargalebihbasis) tanpa hari minggu</li>\r\n    <li>7=(hargasatuan)+sum(hargaminggu)/jlhbaris</li>\r\n    <li>8=hargasatuan+((prestasi-basis)*hargalebihbasis)+hargaminggu</li></ol></fieldset>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset  style=float:left;clear:both;><legend>'.$_SESSION['lang']['find'].' '.$_SESSION['lang']['data'].'</legend>';
echo '<table>';
echo '<tr><td>'.$_SESSION['lang']['namakegiatan']."</td>\r\n     <td><input type=text maxlength=45 id=nmKegiatanCr onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>";
echo '<td>'.$_SESSION['lang']['noakun']."</td>\r\n     <td><select style=\"width:150px;\" onchange=loadData(0) id=noakunCr>".$optkegiatan."</select></td>\r\n     <td>".$_SESSION['lang']['isiauto']."</td>\r\n     <td><select style=\"width:150px;\" onchange=loadData(0) id=autoCr>".$optnil."</select></td>\r\n    </tr>";
echo '<tr><td colspan=4><button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button></td></tr>';
echo '</table></fieldset>';
echo "<fieldset style=float:left;clear:both;>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData(0)</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>