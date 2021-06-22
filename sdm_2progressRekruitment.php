<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optDept = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optPeriode = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optPeriode2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sprd = 'select distinct departemen from '.$dbname.".sdm_permintaansdm \r\n       where stpersetujuanhrd=1 order by tanggal desc";
$qprd = mysql_query($sprd);
while ($rprd = mysql_fetch_assoc($qprd)) {
    $optPeriode .= "<option value='".$rprd['departemen']."'>".$optDept[$rprd['departemen']].'</option>';
}
$sprd = 'select distinct  periodetest as periode from '.$dbname.".sdm_testcalon \r\n       order by periodetest desc";
$qprd = mysql_query($sprd);
while ($rprd = mysql_fetch_assoc($qprd)) {
    $optPeriode2 .= "<option value='".$rprd['periode']."'>".$rprd['periode'].'</option>';
}
$arr = '##deptId##periode##periodesmp';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\nfunction masterPDF(table,column,cond,page,event) {\r\n\t// Prep Param\r\n       \r\n\tparam = \"table=\"+table;\r\n\tparam += \"&emailDt=\"+column;\r\n\t\r\n\t// Prep Condition\r\n\tparam += \"&cond=\"+cond;\r\n\t\r\n\t// Post to Slave\r\n\tif(page==null) {\r\n\t\tpage = 'null';\r\n\t}\r\n\tif(page=='null') {\r\n\t\tpage = \"slave_master_pdf\";\r\n\t}\r\n\t\r\n\tshowDialog1('Print PDF',\"<iframe frameborder=0 style='width:795px;height:400px' src='\"+page+\".php?proses=cvData&\"+param+\"'></iframe>\",'800','400',event);\r\n\tvar dialog = document.getElementById('dynamic1');\r\n\tdialog.style.top = '50px';\r\n\tdialog.style.left = '15%';\r\n}\r\nfunction masterPDF2(table,column,cond,page,event) {\r\n\t// Prep Param\r\n       \r\n\tparam = \"table=\"+table;\r\n\tparam += \"&column=\"+column;\r\n\t\r\n\t// Prep Condition\r\n\tparam += \"&cond=\"+cond;\r\n\t\r\n\t// Post to Slave\r\n\tif(page==null) {\r\n\t\tpage = 'null';\r\n\t}\r\n\tif(page=='null') {\r\n\t\tpage = \"slave_master_pdf\";\r\n\t}\r\n\t\r\n\tshowDialog1('Print PDF',\"<iframe frameborder=0 style='width:795px;height:400px' src='\"+page+\".php?proses=pdfDt&\"+param+\"'></iframe>\",'800','400',event);\r\n\tvar dialog = document.getElementById('dynamic1');\r\n\tdialog.style.top = '50px';\r\n\tdialog.style.left = '15%';\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>Laporan Progress Rekruitment</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['departemen'];
echo '</label></td><td><select id="deptId" name="deptId" style="width:150px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'].' '.$_SESSION['lang']['dari'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode2;
echo "</select></td></tr>\r\n        <tr><td><label>";
echo $_SESSION['lang']['periode'].' '.$_SESSION['lang']['sampai'];
echo '</label></td><td><select id="periodesmp" name="periodesmp" style="width:150px">';
echo $optPeriode2;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('sdm_slave_2progressRekruitment','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('sdm_slave_2progressRekruitment','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\">PDF</button>-->\r\n        <button onclick=\"zExcel(event,'sdm_slave_2progressRekruitment.php','";
echo $arr;
echo "')\" class=\"mybutton\">Excel</button>\r\n       </td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n      <div>\r\n\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>