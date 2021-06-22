<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'  order by kodeorganisasi";

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$arr = '##kodeorg##kegiatan##tgl1##tgl2';

if ('EN' === $_SESSION['language']) {

    $zz = 'namakegiatan1 as namaakun';

} else {

    $zz = 'namakegiatan as namaakun';

}



$kegiatan = '';

$str = 'select kodekegiatan as noakun,'.$zz.' from '.$dbname.".setup_kegiatan\r\n      order by kodekegiatan,namakegiatan";

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {

    $kegiatan .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';

}

echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Cost per Block Report</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";

echo $_SESSION['lang']['kebun'];

echo '</label></td><td><select id="kodeorg" name="kdOrg" style="width:150px">';

echo $optOrg;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['kegiatan'];

echo '</label></td><td><select id="kegiatan" name="kdAfd" style="width:150px">';

echo $kegiatan;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['tanggal'];

echo "</label></td><td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /> s.d.\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n    <button onclick=\"zPreview('kebun_slave_2kegiatanPerBlok','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n    <button onclick=\"zPdf('kebun_slave_2kegiatanPerBlok','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n    <button onclick=\"zExcel(event,'kebun_slave_2kegiatanPerBlok.php','";

echo $arr;

echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto; height:50%; max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";

CLOSE_BOX();

echo close_body();



?>