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
$arr = '##kdUnit##periode';
$optModel = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_aktifitas order by tanggal desc';
$qPeriode = mysql_query($sPeriode) ;
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optModel .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
}
echo "<script language=javascript src='js/zTools.js?v=".mt_rand()."'>
</script>\r\n<script language=javascript src='js/zReport.js?v=".mt_rand()."'></script>
<script>\r\nfunction Clear1()\r\n{\r\n    document.getElementById('thnBudget').value='';\r\n    document.getElementById('kdUnit').value='';\r\n    document.getElementById('printContainer').innerHTML='';\r\n}\r\n</script>
<link rel=stylesheet type=text/css href=style/zTable.css?v=".mt_rand().">\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['penggunaanhk'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td><td><select id='kdUnit'  style=\"width:150px;\">";
echo $optOrg;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td><td><select id='periode'  style=\"width:150px;\">";
echo $optModel;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_2penggunaanHK','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf2(event,'kebun_slave_2penggunaanHK','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'kebun_slave_2penggunaanHK.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n</td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:550px;max-width:1220px'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>