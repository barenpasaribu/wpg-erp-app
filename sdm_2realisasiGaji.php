<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$str=" SELECT ".
    "o.kodeorganisasi,o.namaorganisasi,o.induk ".
    "FROM  organisasi o    ".
    "WHERE o.induk in ( ".
    "SELECT o.kodeorganisasi ".
    "FROM datakaryawan d ".
    "INNER JOIN user u on u.karyawanid=d.karyawanid ".
    "INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
    "WHERE u.namauser='" .$_SESSION['standard']['username'] ."'  ".
    ")";
$optDept= makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$arrKry = '##periode##kdUnit';
echo "\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\nfunction  getPeriode()\r\n{\r\n    kdOrg=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;\r\n    tujuan='sdm_slave_2realisasiGaji';\r\n    param='kdUnit='+kdOrg;\r\n    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);\r\n    function respog()\r\n        {\r\n                      if(con.readyState==4)\r\n                      {\r\n                                if (con.status == 200) {\r\n                                                busy_off();\r\n                                                if (!isSaveResponse(con.responseText)) {\r\n                                                        alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                                                }\r\n                                                else {\r\n                                                        //alert(con.responseText);\r\n                                                        document.getElementById('periode').innerHTML=con.responseText;\r\n                                                }\r\n                                        }\r\n                                        else {\r\n                                                busy_off();\r\n                                                error_catch(con.status);\r\n                                        }\r\n                      }\t\r\n         }  \t\r\n}\r\n</script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['gaji'].' '.$_SESSION['lang']['realisasi'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px" onchange="getPeriode()">';
echo $optDept;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2realisasiGaji','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <!--<button onclick=\"zPdf('sdm_slave_2realisasiGaji','";
echo $arrKry;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>--><button onclick=\"zExcel(event,'sdm_slave_2realisasiGaji.php','";
echo $arrKry;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>