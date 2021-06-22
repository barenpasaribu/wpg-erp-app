<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$karyawanid = $_POST['karyawanid'];
$kodeorg = $_POST['kodeorg'];
$namakaryawan = $_POST['namakaryawan'];
echo '<fieldset><legend>'.$_SESSION['lang']['form']."</legend>\r\n    <table>\r\n\t <tr>\r\n\t \r\n     <input type=hidden class=myinputtext id=kodeorgJ  value='".$kodeorg."'>\r\n\t <input type=hidden class=myinputtext id=karyawanidJ value='".$karyawanid."'>\r\n\t <input type=hidden class=myinputtext id=periodeJ value='".$periode."'>\r\n\t \r\n\t <td> ".$_SESSION['lang']['namakaryawan']."</td><td><input type=text class=myinputtext id=namakaryawan disabled value='".$namakaryawan."' size=25></td>\r\n\t <td>".$_SESSION['lang']['tangalcuti']."</td><td><input type=text class=myinputtext id=dariJ onmouseover=setCalendar(this) onkeypress=\"return false;\" size=15></td>\r\n\t <td>".$_SESSION['lang']['tglcutisampai']."</td><td><input type=text class=myinputtext id=sampaiJ onmouseover=setCalendar(this) onkeypress=\"return false;\" size=15>\r\n\t </tr>\r\n\t \r\n\t <tr>\r\n\t <td>".$_SESSION['lang']['diambil']."</td><td><input type=text class=myinputtextnumber id=diambilJ  size=25 onkeypress=\"return angka_doang(event);\"  size=3 maxlength=2></td>\r\n\t <td>".$_SESSION['lang']['keterangan']."</td><td colspan=3><input type=text class=myinputtext id=keteranganJ onkeypress=\"return tanpa_kutip(event);\" size=35 maxlength=45>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t </td>\r\n\t </tr>\r\n\t </table>\r\n\t </fieldset>\r\n\t<fieldset>\r\n\t<legend>".$_SESSION['lang']['cuti'].'->['.$namakaryawan.'] '.$_SESSION['lang']['periode'].':'.$periode."</legend>\r\n\t<div style='width:750px;height:220px;overflow:scroll;' id=containerlist3>\r\n\t<table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t   <td>\r\n\t      No\r\n\t   </td>\r\n\t   <td>".$_SESSION['lang']['tangalcuti']."</td>\r\n\t   <td>".$_SESSION['lang']['tglcutisampai']."</td>\r\n\t   <td>".$_SESSION['lang']['diambil']."</td>\r\n\t   <td>".$_SESSION['lang']['keterangan']."</td>\r\n\t   <td>".$_SESSION['lang']['aksi']."</td>\r\n\t</tr>\r\n\t</thead>\r\n\t<tbody>\r\n\t";
$str = 'select * from '.$dbname.'.sdm_cutidt where karyawanid='.$karyawanid."\r\n\t      and periodecuti='".$periode."' and kodeorg='".$kodeorg."'";
$res = mysql_query($str);
$no = 0;
$ttl = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr class=rowcontent id=barisJ'.$no.">\r\n\t   <td>".$no."</td>\r\n\t   <td>".tanggalnormal($bar->daritanggal)."</td>\r\n\t   <td>".tanggalnormal($bar->sampaitanggal)."</td>\r\n\t   <td align=right>".$bar->jumlahcuti."</td>\r\n\t   <td>".$bar->keterangan."</td>\r\n\t   <td>\r\n\t   <img src='images/application/application_delete.png'  title='".$_SESSION['lang']['delete']."' class=resicon onclick=\"hapusData('".$periode."','".$karyawanid."','".$kodeorg."','".$bar->daritanggal."','barisJ".$no."',".$bar->jumlahcuti.");\">\r\n\t   </td>\r\n\t   </tr>";
    $ttl += $bar->jumlahcuti;
}
echo "<tr class=rowcontent>\r\n\t   <td></td>\r\n\t   <td>TOTAL</td>\r\n\t   <td></td>\r\n\t   <td align=right id=cellttl>".$ttl."</td>\r\n\t   <td></td>\r\n\t   <td></td>\r\n\t   </tr>";
echo "</tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n     </div>\r\n\t</fieldset> \r\n\t";

?>