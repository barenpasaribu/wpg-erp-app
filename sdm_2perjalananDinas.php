<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optTipe = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sTipe = 'select kode,nama from '.$dbname.'.sdm_5departemen order by nama asc';
$qTipe = mysql_query($sTipe);
while ($rTipe = mysql_fetch_assoc($qTipe)) {
    $optTipe .= '<option value='.$rTipe['kode'].'>'.$rTipe['nama'].'</option>';
}
$optPeriode = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$sPeriode = 'select distinct substring(tglpertanggungjawaban,1,7) as periode  from '.$dbname.'.sdm_pjdinasht order by tglpertanggungjawaban desc';
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    if ('0000-00' != $rPeriode['periode']) {
        $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
    }
}
$optOrg = "<select id=kdOrg name=kdOrg style=\"width:150px;\" onchange=getKaryawan()><option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$optOrg .= '</select>';
$arrSat = [$_SESSION['lang']['belumlunas'], $_SESSION['lang']['lunas']];
foreach ($arrSat as $brsAr => $rStat) {
    $optStat .= '<option value='.$brsAr.'>'.$rStat.'</option>';
}
$arr = '##kdOrg##bagId##periode##karyawanId##stat';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script>\r\nfunction getKaryawan()\r\n{\r\n    kdPt=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;\r\n    param='kdOrg='+kdPt;\r\n    tujuan='sdm_slave_2perjalananDinas.php';\r\n    post_response_text(tujuan+'?proses=getKaryawan', param, respog);\r\n        function respog()\r\n        {\r\n                      if(con.readyState==4)\r\n                      {\r\n                                if (con.status == 200) {\r\n                                                busy_off();\r\n                                                if (!isSaveResponse(con.responseText)) {\r\n                                                        alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                                                }\r\n                                                else {\r\n                                                  //\talert(con.responseText);\r\n                                                        document.getElementById('karyawanId').innerHTML=con.responseText;\r\n                                                }\r\n                                        }\r\n                                        else {\r\n                                                busy_off();\r\n                                                error_catch(con.status);\r\n                                        }\r\n                      }\t\r\n         }  \r\n}\r\nfunction getKaryawan2()\r\n{\r\n    kdPt=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;\r\n    bagian=document.getElementById('bagId').options[document.getElementById('bagId').selectedIndex].value;\r\n    param='kdOrg='+kdPt+'&bagId='+bagian;\r\n    tujuan='sdm_slave_2perjalananDinas.php';\r\n    post_response_text(tujuan+'?proses=getKaryawan', param, respog);\r\n        function respog()\r\n        {\r\n                      if(con.readyState==4)\r\n                      {\r\n                                if (con.status == 200) {\r\n                                                busy_off();\r\n                                                if (!isSaveResponse(con.responseText)) {\r\n                                                        alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                                                }\r\n                                                else {\r\n                                                  //\talert(con.responseText);\r\n                                                        document.getElementById('karyawanId').innerHTML=con.responseText;\r\n                                                }\r\n                                        }\r\n                                        else {\r\n                                                busy_off();\r\n                                                error_catch(con.status);\r\n                                        }\r\n                      }\t\r\n         }  \r\n}\r\nvar opt=\"<option value=''>";
echo $_SESSION['lang']['all'];
echo "</option>\";\r\nfunction Clear1()\r\n{\r\n    document.getElementById('kdOrg').value='';\r\n    document.getElementById('bagId').value='';\r\n    document.getElementById('karyawanId').innerHTML='';\r\n    document.getElementById('karyawanId').innerHTML=opt;\r\n    document.getElementById('printContainer').innerHTML='';\r\n    document.getElementById('stat').value='';\r\n}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['lapPjd'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['perusahaan'];
echo '</label></td><td>';
echo $optOrg;
echo "</td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['bagian'];
echo '</label></td><td><select id="bagId" name="bagId" style="width:150px" onchange="getKaryawan2()">';
echo $optTipe;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['namakaryawan'];
echo "</label></td><td><select id=\"karyawanId\" name=\"karyawanId\"  style=\"width:150px\"><option value=''>";
echo $_SESSION['lang']['all'];
echo "</option></select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['status'];
echo "</label></td><td><select id=\"stat\" name=\"stat\" style=\"width:150px\"><option value=''>";
echo $_SESSION['lang']['all'];
echo '</option>';
echo $optStat;
echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2perjalananDinas','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zPdf('sdm_slave_2perjalananDinas','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>\r\n        <button onclick=\"zExcel(event,'sdm_slave_2perjalananDinas.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n\r\n<div style=\"margin-bottom: 30px;\">\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>