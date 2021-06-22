<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/it_5stKepuasan.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'Standard Kepuasan');
$arr = '##kode##nilKode##ket##method';
$optagama = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optank = $optagama;
$arragama = getEnum($dbname, 'it_stkepuasan', 'kode');
foreach ($arragama as $kei => $fal) {
    $optagama .= "<option value='".$kei."'>".$fal.'</option>';
}
$arrankg = [1 => 'Sangat Memuaskan', 2 => 'Memuaskan', 3 => 'Cukup', 4 => 'Kurang'];
foreach ($arrankg as $kei => $fal) {
    $optank .= "<option value='".$kei."'>".$kei.'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n<tr><td>".$_SESSION['lang']['kode'].'</td><td><select id=kode style=width:105px>'.$optagama."</select></td></tr>\r\n<tr><td>Nilai</td><td><select id=nilKode style=width:105px>".$optank."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=ket size=45  maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n           \r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t  <input type=hidden id=eduid value=''>\r\n\t <button class=mybutton onclick=simpanPendidikan('it_slave_5stKepuasan','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme();
echo '<div id=container><script>loadData()</script></div>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>