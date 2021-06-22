<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where  left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by kodeorganisasi asc";

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$arr = '##kdUnit##afdId##periodeData';

$optModel = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_aktifitas order by tanggal desc';

$qPeriode = mysql_query($sPeriode) ;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {

    $optModel .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';

}

echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script language=javascript src='js/kebun_restan.js'></script>\r\n<script>\r\nfunction Clear1()\r\n{\r\n    document.getElementById('thnBudget').value='';\r\n    document.getElementById('kdUnit').value='';\r\n    document.getElementById('printContainer').innerHTML='';\r\n}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";

echo $_SESSION['lang']['lapRestan'];

echo "</b></legend>\r\n<fieldset><legend>Info</legend>\r\n";

$_SESSION['language'] = 'EN';

if ($_SESSION['language']) {

    echo "Harvest document and FFB Transport Document should be recorded <br>as soon as posible to perform a valid FFB Remains information. \r\n       <br> FFB Remains at the end of the month should be zero";

} else {

    echo "\r\n    Inputan Transaksi Panen dan SPB Harus di Input Secepat Mungkin. <br> Untuk mendapatkan inforamsi restan,sesegera mungkin.<br>\r\n    Restan di akhir bulan harus nol.";

}



echo "        \r\n</fieldset>\r\n<table cellspacing='1' border='0' >\r\n";

$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sUnit = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";

$qUnit = mysql_query($sUnit) ;

while ($rUnit = mysql_fetch_assoc($qUnit)) {

    $optUnit .= "<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi'].'</option>';

}

$optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';

$optBlok = $optAfd;

$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$optBul = "<option value=''>Periode...</option>";

for ($d = 0; $d <= 40; ++$d) {

    $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));

    $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';

}

echo '<tr><td><label>'.$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\" onchange='getAfd()'>".$optUnit."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['afdeling'].'</label></td><td><select id="afdId" name="afdId" style="width:150px">'.$optAfd."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['periode'].'</label></td><td><select id="periodeData" name="periodeData" style="width:150px">'.$optBul.'</select>(* Akhir Bulan Sebelumnya Di anggap Nol(FFB Remains at the end of the month should be zero)</td></tr>';

echo "\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_2laporan_restan','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'kebun_slave_2laporan_restan.php','";

echo $arr;

echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n</td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:650px;max-width:1220px'>\r\n\r\n</div></fieldset>\r\n\r\n";

CLOSE_BOX();

echo close_body();



?>