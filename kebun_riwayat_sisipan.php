<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

echo open_body();

echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src='js/kebun_riwayat_sisipan.js'></script>\r\n";

include 'master_mainMenu.php';

OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['riwayatsisipan']).'</b>');

$str = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".kebun_aktifitas\r\n      where tipetransaksi = 'PNN' order by periode desc";

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {

    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';

}

$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n      where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";

$res = mysql_query($str);

$optpt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

while ($bar = mysql_fetch_object($res)) {

    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';

}

$arr = '##unitId##tgl1##tgl2';

echo "<fieldset style=width:150px;>\r\n     <legend>".$_SESSION['lang']['riwayatsisipan']."</legend>\r\n         <table cellpadding=1 cellspacing=1 border=0><tr><td>\r\n\t ".$_SESSION['lang']['unit'].'</td><td> :</td><td> '."<select id=unitId style='width:200px;'>".$optpt."</select></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td> <td>\r\n          <input type=text class=myinputtext id=tgl1 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> -\r\n\t  <input type=text class=myinputtext id=tgl2 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10></td></tr>\r\n          <tr><td colspan=3 align=center>\r\n   <button onclick=\"zPreview('kebun_slave_riwayat_sisipan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n    <button onclick=\"zPdf('kebun_slave_riwayat_sisipan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n    <button onclick=\"zExcel(event,'kebun_slave_riwayat_sisipan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr></table>\r\n\t </fieldset>";

CLOSE_BOX();

OPEN_BOX();

echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";

CLOSE_BOX();

close_body();



?>