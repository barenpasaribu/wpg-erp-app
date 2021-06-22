<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

echo "\r\n";

$optOrg .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$optPer .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$optAfd .= "<option value=''>".$_SESSION['lang']['all'].'</option>';

$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);

$sOrg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'";

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$sOrg = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_taksasi order by tanggal desc';

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optPer .= '<option value='.$rOrg['periode'].'>'.$rOrg['periode'].'</option>';

}

$arr0 = '##kebun0##afdeling0##periode0';

$arr = '##kebun##afdeling##tanggal';

$arr2 = '##kebun2##afdeling2##periode2';

echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\nfunction getPeriode(tab){\r\n    if(tab==0){\r\n        kebun=document.getElementById('kebun0').options[document.getElementById('kebun0').selectedIndex].value;        \r\n        param='kebun='+kebun+'&proses=getAfdeling0';\r\n    }\r\n    if(tab==1){\r\n        kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;        \r\n        param='kebun='+kebun+'&proses=getAfdeling';\r\n    }\r\n    if(tab==2){\r\n        kebun=document.getElementById('kebun2').options[document.getElementById('kebun2').selectedIndex].value;        \r\n        param='kebun='+kebun+'&proses=getAfdeling';\r\n    }\r\n\r\n    tujuan='kebun_slave_2taksasipanen.php';\r\n    post_response_text(tujuan, param, respon);\r\n    function respon(){\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n                    cor=con.responseText.split(\"####\");\r\n                    if(tab==0){\r\n                        document.getElementById('afdeling0').innerHTML=cor[0];                        \r\n                        document.getElementById('mandor0').innerHTML=cor[1];                        \r\n                    }\r\n                    if(tab==1){\r\n                        document.getElementById('afdeling').innerHTML=cor[0];                        \r\n                    }\r\n                    if(tab==2){\r\n                        document.getElementById('afdeling2').innerHTML=cor[0];                        \r\n                    }\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n}\r\n\r\nfunction pindahtanggal(kebun,afdeling,tanggal) {\r\n    var workField = document.getElementById('printContainer');\r\n    var param = \"kebun=\"+kebun+\"&afdeling=\"+afdeling+\"&tanggal=\"+tanggal;\r\n\r\n    function respon() {\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    //=== Success Response\r\n                    workField.innerHTML = con.responseText;\r\n                    document.getElementById('tanggal').value=tanggal;\r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n\r\n    post_response_text('kebun_slave_2taksasipanen.php?proses=preview', param, respon);\r\n}\r\n</script>\r\n\r\n<link rel='stylesheet' type='text/css' href='style/zTable.css'>\r\n\r\n";

$title[0] = $_SESSION['lang']['laporan'].' '.$_SESSION['lang']['rencanapanen'].' '.$_SESSION['lang']['harian'];

$title[1] = $_SESSION['lang']['laporan'].' '.$_SESSION['lang']['rencanapanen'];

$title[2] = $_SESSION['lang']['laporan'].' '.$_SESSION['lang']['rencanapanen'].' '.$_SESSION['lang']['bulanan'];

$frm[0] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[0]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['kebun']."</label></td>\r\n    <td><select id=\"kebun0\" name=\"kebun0\"  style=\"width:150px\" onchange=getPeriode(0)>".$optOrg."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['afdeling']."</label></td>\r\n    <td><select id=\"afdeling0\" name=\"afdeling0\"  style=\"width:150px\">".$optAfd."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['periode']."</label></td>\r\n    <td><select id=\"periode0\" name=\"periode0\"  style=\"width:150px\">".$optPer."</select></td>\r\n</tr>\r\n\r\n<tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button onclick=\"zPreview('kebun_slave_2taksasipanen0','".$arr0."','printContainer0')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen0.php','".$arr0."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer0' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

$frm[1] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[1]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['kebun']."</label></td>\r\n    <td><select id=\"kebun\" name=\"kebun\"  style=\"width:150px\" onchange=getPeriode(1)>".$optOrg."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['afdeling']."</label></td>\r\n    <td><select id=\"afdeling\" name=\"afdeling\"  style=\"width:150px\">".$optAfd."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['tanggal']."</label></td>\r\n    <td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n</tr>\r\n\r\n<tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\"> \r\n        <button onclick=\"zPreview('kebun_slave_2taksasipanen','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

$frm[2] .= "<fieldset style=\"float: left;\">\r\n<legend><b>".$title[2]."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['kebun']."</label></td>\r\n    <td><select id=\"kebun2\" name=\"kebun2\"  style=\"width:150px\" onchange=getPeriode(2)>".$optOrg."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['afdeling']."</label></td>\r\n    <td><select id=\"afdeling2\" name=\"afdeling2\"  style=\"width:150px\">".$optAfd."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['periode']."</label></td>\r\n    <td><select id=\"periode2\" name=\"periode2\"  style=\"width:150px\">".$optPer."</select></td>\r\n</tr>\r\n\r\n<tr height=\"20\">\r\n    <td colspan=\"2\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"2\">\r\n        <button onclick=\"zPreview('kebun_slave_2taksasipanen2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen2.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n    </td>    \r\n</tr>    \r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer2' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";

list($hfrm[0], $hfrm[1], $hfrm[2]) = $title;

drawTab('FRM', $hfrm, $frm, 200, 1100);

CLOSE_BOX();

echo close_body();



?>