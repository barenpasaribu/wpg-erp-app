<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

$lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 4);

if ('HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {

    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe in ('KEBUN') order by namaorganisasi asc ";

} else {

    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";

}



$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$optMandor = '<option value="all">'.$_SESSION['lang']['all'].'</option>';

$sMan = 'select a.nikmandor, b.namakaryawan from '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid\r\n    where a.kodeorg = '".$lokasi."'\r\n    group by a.nikmandor\r\n    order by b.namakaryawan";

$qMan = mysql_query($sMan) ;

while ($rMan = mysql_fetch_assoc($qMan)) {

    $optMandor .= '<option value='.$rMan['nikmandor'].'>'.$rMan['namakaryawan'].' ['.$rMan['nikmandor'].']</option>';

}

$arr = '##kebun##mandor##tanggal';

echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script language=javascript src='js/kebun_2kehadiranpermandor.js'></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";

if ('EN' === $_SESSION['language']) {

    echo 'Foreman Daily Absence';

} else {

    echo 'Laporan Kehadiran per Mandor';

}



echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";

echo $_SESSION['lang']['kebun'];

echo '</label></td><td><select id="kebun" name="kebun" style="width:150px"><option value=""></option>';

echo $optOrg;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['mandor'];

echo '</label></td><td><select id="mandor" name="mandor" style="width:150px"><option value=""></option>';

echo $optMandor;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['tanggal'];

echo "</label></td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tanggal\" name=\"tanggal\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" />\r\n</td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n    <button onclick=\"zPreview('kebun_slave_2kehadiranpermandor','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n    <button onclick=\"zExcel(event,'kebun_slave_2kehadiranpermandor.php','";

echo $arr;

echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";

echo $_SESSION['lang']['cancel'];

echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto; height:50%; max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";

CLOSE_BOX();

echo close_body();



?>