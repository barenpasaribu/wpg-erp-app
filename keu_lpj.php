<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/keu_lpj.js'></script>\r\n";
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n                or tipe='HOLDING')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
OPEN_BOX('', 'BAHAN LAPORAN LPJ');
echo "<fieldset><table>\r\n      <tr><td>".$_SESSION['lang']['tanggal']."</td><td>:\r\n             <input class='myinputtext' id=dari  onmousemove='setCalendar(this.id)' onkeypress=\"return false;\" maxlength=\"10\" style=\"width: 100px;\" type=\"text\">\r\n             ".$_SESSION['lang']['tanggalsampai']."<input class='myinputtext' id=sampai  onmousemove='setCalendar(this.id)' onkeypress=\"return false;\" maxlength=\"10\" style=\"width: 100px;\" type=\"text\">    \r\n      </td></tr>\r\n      <tr><td>".$_SESSION['lang']['unit'].'</td><td>:<select id=unit>'.$optgudang."</select></td></tr>\r\n      <tr><td colspan=2><button class=mybutton onclick=preview()>".$_SESSION['lang']['tampilkan']."</button></td></tr>    \r\n      </table>\r\n      </fieldset>    \r\n\t  <div id=container style='height:400px; overflow:scroll'>\r\n\t  </div>";
CLOSE_BOX();
echo close_body();

?>