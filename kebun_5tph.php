<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=javascript1.2 src='js/kebun_5tph.js'></script>\r\n";
OPEN_BOX();
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='BLOK'\r\n      and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorganisasi";
$res = mysql_query($str);
$optorg = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
}
echo "<fieldset style='width:350px;'><legend>".$_SESSION['lang']['notph']."</legend>\r\n     <table>\r\n     <tr>\r\n       <td>".$_SESSION['lang']['kodeorg']."</td><td><select id='kodeorg' onchange=getList(this.options[this.selectedIndex].value) style='width:200px;'>".$optorg."</select></td>\r\n     </tr>\r\n     <tr>\r\n       <td>".$_SESSION['lang']['notph']."</td><td><input type=text class=myinputtextnumber size=8 maxlength=6 id=notph onkeypress=\"return angka_doang(event);\"></td>\r\n     </tr>\r\n     <tr>\r\n       <td>".$_SESSION['lang']['keterangan']."</td><td><input type=text class=myinputtext size=25 maxlength=35 id=keterangan onkeypress=\"return tanpa_kutip(event);\"></td>\r\n     </tr>\r\n     </table>\r\n     <button class=mybutton onclick=saveTph() state='save' id=tombol>".$_SESSION['lang']['save']."</button>\r\n     <button class=mybutton onclick=cancelTph() state='save' id=tombol>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset>";
echo "<fieldset style='width:440px;'><legend>".$_SESSION['lang']['list']."</legend>\r\n      <div id=contain style='width:430px;height:350px;overflow:scroll'>\r\n      </div>\r\n      </fieldset>";
CLOSE_BOX();
echo close_body();

?>