<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optBrg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sOrg = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%' and kodebarang!='40000003'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optBrg .= '<option value='.$rOrg['kodebarang'].'>'.$rOrg['namabarang'].'</option>';
}
$optTimb = makeOption($dbname, 'pmn_4customer', 'kodetimbangan,namacustomer');
$optCust = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optPabrik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."%' ";
$qOrg2 = mysql_query($sOrg2);
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optPabrik .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$arr = '##kdPabrik##tgl_1##tgl_2##kdCust##nkntrak##kdBrg';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\n\r\nfunction cleart(){\r\n    document.getElementById('kdPabrik').value='';\r\n    document.getElementById('kdCust').value='';\r\n    document.getElementById('kdBrg').value='';\r\n    document.getElementById('nkntrak').value='';\r\n    \r\n}\r\nfunction getCust(){\r\n    tgl1=document.getElementById('tgl_1').value;\r\n    tgl2=document.getElementById('tgl_2').value;\r\n    kdpbr=document.getElementById('kdPabrik').options[document.getElementById('kdPabrik').selectedIndex].value;\r\n    if((tgl1=='')&&(tgl2==''))\r\n    {\r\n        cleart();\r\n        alert(\"Date Can't Empty!!\");\r\n        return;\r\n    }\r\n    if(kdpbr==''){\r\n        cleart();\r\n        alert(\"Mill Code Can't Empty\");\r\n    }\r\n    kdCustom=document.getElementById('kdBrg').options[document.getElementById('kdBrg').selectedIndex].value;\r\n    \r\n    param='proses=getCust'+'&kdBrg='+kdCustom+'&tgl1='+tgl1+'&tgl2='+tgl2+'&kdPabrik='+kdpbr;\r\n    tujuan='pabrik_slave_2pengiriman.php';\r\n    //alert(param);\r\n    post_response_text(tujuan, param, respog);\r\n    function respog()\r\n    {\r\n          if(con.readyState==4)\r\n          {\r\n            if (con.status == 200) {\r\n                            busy_off();\r\n                            if (!isSaveResponse(con.responseText)) {\r\n                                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                            }\r\n                            else {\r\n                                    //alert(con.responseText);\r\n                                    drt=con.responseText.split(\"####\");\r\n                                    document.getElementById('kdCust').innerHTML=drt[0];\r\n                                    document.getElementById('nkntrak').innerHTML=drt[1];\r\n                                    //load_data();\r\n                            }\r\n                    }\r\n                    else {\r\n                            busy_off();\r\n                            error_catch(con.status);\r\n                    }\r\n          }\t\r\n    }\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo "Laporan Logsheet Klarification";
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggal'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" onblur=\"cleart()\" /> s.d. <input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\"  onblur=\"cleart()\" />\r\n</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:169px" >';
echo $optPabrik;
// echo "</select></td></tr>\r\n<tr><td>";
// echo $_SESSION['lang']['materialname'];
// echo '</td><td><select id="kdBrg" style="width:169px" onchange="getCust()">';
// echo $optBrg;
// echo "</select></td></tr>\r\n<tr><td><label>";
// echo $_SESSION['lang']['transporter'];
// echo '</label></td><td><select id="kdCust" name="kdCust" style="width:169px;" onchange="getKontrak()">';
// echo $optCust;
// echo "</select></td></tr>\r\n<tr><td>";
// echo $_SESSION['lang']['NoKontrak'];
// echo "</td><td><select id=\"nkntrak\" style=\"width:169px\"><option value=''>";
// echo $_SESSION['lang']['all'];
echo "<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('pabrik_slave_klarification','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>";
// echo $arr;
echo "<button onclick=\"zExcel(event,'pabrik_slave_klarification.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n\r\n<fieldset style='clear:both;'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;;'>\r\n\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>