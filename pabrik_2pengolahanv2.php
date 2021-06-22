<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optPabrik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK'";
$qOrg2 = mysql_query($sOrg2);
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optPabrik .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$arr = '##kdPabrik##periode';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\nfunction getPeriode()\r\n{\r\n    kdPabrik=document.getElementById('kdPabrik').options[document.getElementById('kdPabrik').selectedIndex].value;\r\n    param='proses=getPeriode'+'&kdPabrik='+kdPabrik;\r\n    tujuan='pabrik_slave_2pengolahanv2.php';\r\n    //alert(param);\r\n    post_response_text(tujuan, param, respog);\r\n    function respog()\r\n    {\r\n          if(con.readyState==4)\r\n          {\r\n            if (con.status == 200) {\r\n                            busy_off();\r\n                            if (!isSaveResponse(con.responseText)) {\r\n                                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                            }\r\n                            else {\r\n                                    //alert(con.responseText);\r\n                                    document.getElementById('periode').innerHTML=con.responseText;\r\n                                    //load_data();\r\n                            }\r\n                    }\r\n                    else {\r\n                            busy_off();\r\n                            error_catch(con.status);\r\n                    }\r\n          }\t\r\n    }\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n    \r\n<legend><b>Mill Processing v2</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:169px" onchange="getPeriode()">';
echo $optPabrik;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:169px;">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('pabrik_slave_2pengolahanv2','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('pabrik_slave_2perawatanv2','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>-->\r\n        <button onclick=\"zExcel(event,'pabrik_slave_2pengolahanv2.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>