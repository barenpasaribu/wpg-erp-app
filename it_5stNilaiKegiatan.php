<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/it_5stNilaiKegiatan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'Standard Nilai Kegiatan');
$arr = '##kdkegiatan##ket##satuan##nilsngtbaik##nilbaik##nilckp##nilkrg##method';
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodekegiatan']."</td><td><input type=text id=kdkegiatan size=8 maxlength=8 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=ket size=40 maxlength=25 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=8  maxlength=4 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext></td></tr>\r\n             <tr><td>Sangat Baik</td><td><input type=text id=nilsngtbaik size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>\r\n                 <tr><td>Baik</td><td><input type=text id=nilbaik size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>\r\n                     <tr><td>Cukup</td><td><input type=text id=nilckp size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>\r\n                         <tr><td>Kurang</td><td><input type=text id=nilkrg size=9  maxlength=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>\r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t  <input type=hidden id=eduid value=''>\r\n\t <button class=mybutton onclick=simpanPendidikan('it_slave_5stNilaiKegiatan','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme();
echo '<div id=container><script>loadData()</script></div>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>