<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

$optOrg .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$optAfd .= "<option value=''>".$_SESSION['lang']['all'].'</option>';

$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);

$sOrg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'";

$qOrg = mysql_query($sOrg) ;

while ($rOrg = mysql_fetch_assoc($qOrg)) {

    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';

}

$arr = '##traksiId##afdId##periode';

$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

for ($x = 0; $x <= 24; ++$x) {

    $t = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));

    $optPeriode .= "<option value='".date('Y-m', $t)."'>".date('Y-m', $t).'</option>';

}

echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\n    function getPeriode(){\r\n        trksi=document.getElementById('traksiId').options[document.getElementById('traksiId').selectedIndex].value;\r\n \r\n\tparam='traksiId='+trksi+'&proses=getPrd';\r\n\t//alert(param);\r\n\ttujuan='kebun_slave_2spbvspenerimaan.php';\r\n\tpost_response_text(tujuan, param, respon);\r\n\tfunction respon(){\r\n        if (con.readyState == 4) {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                    alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                } else {\r\n                    // Success Response\r\n\t\t\t\t\t//alert(con.responseText);\r\n                                        cor=con.responseText.split(\"####\");\r\n\t\t//document.getElementById('periode').innerHTML=cor[0];\r\n                                        document.getElementById('afdId').innerHTML=cor[1];\r\n\t\t\t\t \r\n                }\r\n            } else {\r\n                busy_off();\r\n                error_catch(con.status);\r\n            }\r\n        }\r\n    }\r\n    }\r\n</script>\r\n\r\n<link rel='stylesheet' type='text/css' href='style/zTable.css'>\r\n\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";

echo $_SESSION['lang']['suratPengantarBuah'].' Vs Weighbridge';

echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";

echo $_SESSION['lang']['kebun'];

echo '</label></td><td><select id="traksiId" name="traksiId"  style="width:150px" onchange=getPeriode()>';

echo $optOrg;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['afdeling'];

echo '</label></td><td><select id="afdId" name="afdId"  style="width:150px">';

echo $optAfd;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['periode'];

echo "</label></td><td>\r\n            <select id=\"periode\"  style=width:150px>";

echo $optPeriode;

echo "</select></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_2spbvspenerimaan','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n                    <!--<button onclick=\"zPdf('sdm_slave_2rekapabsen','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n                        <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";

echo $_SESSION['lang']['cancel'];

echo "</button></td></tr>-->\r\n                    <button onclick=\"zExcel(event,'kebun_slave_2spbvspenerimaan.php','";

echo $arr;

echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n                    \r\n\r\n</table>\r\n</fieldset>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";

CLOSE_BOX();

echo close_body();



?>