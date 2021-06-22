<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include 'lib/zFunction.php';

echo open_body();

include 'master_mainMenu.php';

$frm[0] = '';

$frm[1] = '';

echo "<script>\r\npilh=\" ";

echo $_SESSION['lang']['pilihdata'];

echo "\";\r\n</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script type=\"text/javascript\" src=\"js/kebun_2curahHujan.js\" /></script>\r\n\r\n<script>\r\ndataKdvhc=\"";

echo $_SESSION['lang']['pilihdata'];

echo "\";\r\nfunction Clear1()\r\n{\r\n    document.getElementById('thnBudget').value='';\r\n    document.getElementById('kdUnit').value='';\r\n    document.getElementById('printContainer').innerHTML='';\r\n}\r\nfunction Clear2()\r\n{\r\n    document.getElementById('thnBudget_afd').value='';\r\n    document.getElementById('kdUnit_afd').value='';\r\n    document.getElementById('printContainer2').innerHTML='';\r\n}\r\nfunction Clear3()\r\n{\r\n    document.getElementById('thnBudget_sebaran').value='';\r\n    document.getElementById('kdUnit_sebaran').value='';\r\n    document.getElementById('printContainer3').innerHTML='';\r\n}\r\nfunction Clear5()\r\n{\r\n    document.getElementById('thnBudgetCst').value='';\r\n    document.getElementById('kdUnitCst').value='';\r\n    document.getElementById('printContainer5').innerHTML='';\r\n}\r\n</script>\r\n";

$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$optOrg2 = $optOrg;

if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {

    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='AFDELING' order by namaorganisasi asc";

    $sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";

} else {

    $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and induk='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";

    $sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";

}



$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$qOrg = mysql_query($sOrg2) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg2 .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$optper = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$arr = '##periodeUnit##kdUnit';

$arr2 = '##kdUnitOrg##periodeDt';

OPEN_BOX('', '<b>'.$_SESSION['lang']['laporanCurahHujan'].'</b>');

$frm[0] .= '<fieldset style="overflow: left;"><legend>'.$_SESSION['lang']['curahharian'].'</legend>';

$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><select id='kdUnit'  style=\"width:150px;\" onchange=getPeriode()>".$optOrg."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>\r\n<select id='periodeUnit' style='width:150px;'>".$optper."</select></td></tr>\r\n\r\n<tr><td colspan=3>\r\n<button onclick=\"zPreview('kebun_2slaveCurahHujan','".$arr."','printContainer')\" class=\"mybutton\" >Preview</button>\r\n    <button onclick=\"zPdf('kebun_2slaveCurahHujan','".$arr."','printContainer')\" class=\"mybutton\">PDF</button>\r\n    <button onclick=\"zExcel(event,'kebun_2slaveCurahHujan.php','".$arr."')\" class=\"mybutton\" >Excel</button></td></tr></table>\r\n";

$frm[0] .= "</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:350px;max-width:1000px'>\r\n\r\n</div></fieldset>";

$frm[1] .= '<fieldset style="float: left;"><legend>'.$_SESSION['lang']['curahbulanan'].'</legend>';

$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kebun']."</td><td>:</td><td><select id='kdUnitOrg'  style=\"width:150px;\" onchange=getPeriodeOrg()>".$optOrg2."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>\r\n<select id='periodeDt' style='width:150px;'>".$optper."</select></td></tr>\r\n\r\n<tr><td colspan=3>\r\n<button onclick=\"zPreview('kebun_2slaveCurahHujanOrg','".$arr2."','printContainer2')\" class=\"mybutton\" >Preview</button>\r\n    <button onclick=\"zPdf('kebun_2slaveCurahHujanOrg','".$arr2."','printContainer2')\" class=\"mybutton\">PDF</button>\r\n    <button onclick=\"zExcel(event,'kebun_2slaveCurahHujanOrg.php','".$arr2."')\" class=\"mybutton\" >Excel</button></td></tr></table>\r\n";

$frm[1] .= "</fieldset><fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer2' style='overflow:scroll;height:350px;max-width:1000px'>\r\n\r\n</div></fieldset>";

$hfrm[0] = $_SESSION['lang']['curahharian'];

$hfrm[1] = $_SESSION['lang']['curahbulanan'];

drawTab('FRM', $hfrm, $frm, 200, 900);

echo "\r\n\r\n";

CLOSE_BOX();

echo '</div>';

echo close_body();



?>