<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=\"js/keu_laporan.js\"></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where tipe='PT'\r\n        order by namaorganisasi desc";
} else {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where tipe='PT' and kodeorganisasi = '".$_SESSION['empl']['kodeorganisasi']."'\r\n        order by namaorganisasi desc";
}

$res = mysql_query($str);
$optpt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$optunit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "<fieldset style=\"float: left;\"> \r\n<legend><b>";
echo $_SESSION['lang']['periode'].' '.$_SESSION['lang']['tutupbuku'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>";
echo $_SESSION['lang']['pt'];
echo "</label></td>\r\n    <td><select id=kodept style='width:200px;' onchange=ambilAnakPA(this.options[this.selectedIndex].value)>";
echo $optpt;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>";
echo $_SESSION['lang']['kodeorganisasi'];
echo "</label></td>\r\n    <td><select id=kodeunit style='width:200px;' onchange=document.getElementById('container').innerHTML=''>";
echo $optunit;
echo "</select></td>\r\n</tr>\r\n<tr height=\"20\"><td colspan=\"2\"><button class=mybutton onclick=getPeriodeAkuntansi()>";
echo $_SESSION['lang']['preview'];
echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<div id=container style='width:100%;height:50%;overflow:scroll;'>\r\n</div>";
CLOSE_BOX();
close_body();

?>