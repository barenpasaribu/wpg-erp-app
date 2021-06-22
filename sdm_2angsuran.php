<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();

echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n\r\n";
$optOrg= makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$sql = 'SELECT distinct periode FROM '.$dbname.'.sdm_5periodegaji order by periode desc limit 12';
$optPer= makeOption2($sql,
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'periode',"captionfield"=> 'periode' )
);
$optTipe = "\r\n\t<option value=lunas>Sudah Lunas</option>\r\n\t<option value=blmlunas>Belum Lunas</option>\r\n\t<option value=active>Active</option>\r\n\t<option value=notactive>Not Active</option>";
echo "\r\n\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$arr = '##kdorg##per##tipe';
echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['angsuran']."</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=kdorg style='width:200px;'>".$optOrg."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>Tampilkan Angsuran Bulan</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=per style='width:155px;'>".$optPer."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>Tipe</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=tipe style='width:155px;'>".$optTipe."</select></td>\r\n\t</tr>\r\n\t\r\n\t<tr>\r\n\t\t<td colspan=100>&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=100>\r\n\t\t<button onclick=zPreview('sdm_slave_2angsuran','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t<button onclick=zExcel(event,'sdm_slave_2angsuran.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t\r\n\t\t<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>\r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>";
echo "\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer'  >\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>