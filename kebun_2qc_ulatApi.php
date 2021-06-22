<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<script language=javascript>\r\n\tfunction batal()\r\n\t{\r\n\t\tdocument.getElementById('div').value='';\r\n\t\tdocument.getElementById('per').value='';\t\r\n\t\tdocument.getElementById('printContainer').innerHTML='';\t\r\n\t}\r\n</script>\r\n\r\n";

$optDiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$g = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' ";

$h = mysql_query($g);

while ($i = mysql_fetch_assoc($h)) {

    $optDiv .= "<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi'].'</option>';

}

$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$i = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_qc_ulatapiht order by periode desc limit 10';

$j = mysql_query($i);

while ($k = mysql_fetch_assoc($j)) {

    $optPer .= "<option value='".$k['periode']."'>".$k['periode'].'</option>';

}

$optUlat = "<option value='jlhdarnatrima'>Darna Trima</option>";

$optUlat .= "<option value='jlhsetothosea'>Setothosea Asigna</option>";

$optUlat .= "<option value='jlhsetoranitens'>Setora Nitens</option>";

$optUlat .= "<option value='jlhulatkantong'>Ulat Kantong</option>";

echo "\r\n\r\n\r\n\r\n\r\n\r\n";

include 'master_mainMenu.php';

OPEN_BOX();

$arr = '##div##per##ulat';

echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['laporan'].' QC '.$_SESSION['lang']['ulatapi']."</b></legend>\r\n<table>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['divisi']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=div style='width:200px;'>".$optDiv."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=per style='width:200px;'>".$optPer."</select></td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td>Ulat</td>\r\n\t\t<td>:</td>\r\n\t\t<td><select id=ulat style='width:200px;'>".$optUlat."</select></td>\r\n\t</tr>\r\n\t\r\n\t\r\n\t<tr>\r\n\t\t<td colspan=100>&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=100>\r\n\t\t<button onclick=zPreview('kebun_slave_2qc_ulatApi','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t<button onclick=zExcel(event,'kebun_slave_2qc_ulatApi.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t\r\n\t\t<button onclick=batal() class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>\r\n\t\t</td>\r\n\t</tr>\r\n</table>\r\n</fieldset>";

echo "\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer'  >\r\n</div></fieldset>";

CLOSE_BOX();

echo close_body();



?>