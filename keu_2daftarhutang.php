<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optNamaOrganisasi = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sPeriode = 'select distinct substring(tanggal,1,7) as periode from '.$dbname.'.keu_tagihanht order by substring(tanggal,1,7) desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    if ('12' === substr($rPeriode['periode'], 5, 2)) {
        $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
    } else {
        $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
    }
}
$optOrg = "<select id=kdOrg name=kdOrg style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['all'].'</option>';
$sOrg = 'select distinct kodeorg from '.$dbname.'.keu_tagihanht order by kodeorg asc ';
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorg'].'>'.$optNamaOrganisasi[$rOrg['kodeorg']].'</option>';
}
$optOrg .= '</select>';
$arr = '##kdOrg##periode##statTagihan##periode2';
$arrOpt = ['Belum Terbayar', 'Sudah Terbayar'];
$optStatus = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrOpt as $listBrs => $dtStat) {
    $optStatus .= "<option value='".$listBrs."'>".$dtStat.'</option>';
}
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\nfunction Clear1()\r\n{\r\n\tdocument.location.reload();\r\n/*    document.getElementById('kdOrg').value='';\r\n    document.getElementById('tgl1').value='';\r\n    document.getElementById('tgl2').value='';\r\n    document.getElementById('statTagihan').value='';\r\n    document.getElementById('printContainer').innerHTML='';*/\r\n}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['daftarHutang'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pt'];
echo '</label></td><td>';
echo $optOrg;
echo "</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['dari'].' '.$_SESSION['lang']['periode'];
echo "</label></td><td><select id='periode' style=\"width:150px\">";
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tglcutisampai'].' '.$_SESSION['lang']['periode'];
echo "</label></td><td><select id='periode2' style=\"width:150px\">";
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['status'];
echo '</label></td><td><select id="statTagihan" name="statTagihan" style="width:150px">';
echo $optStatus;
echo "</select></td></tr>\r\n<!--<tr><td><label>";
echo $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['tagihan'].' '.$_SESSION['lang']['dari'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>-->\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('keu_slave_2daftarhutang','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('keu_slave_2daftarhutang','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'keu_slave_2daftarhutang.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>