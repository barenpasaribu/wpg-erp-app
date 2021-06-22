<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();

$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$optPeriode = "<option value''>".$_SESSION['lang']['pilihdata'].'</option>';
$sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where sudahproses='0' order by periode desc";
$qPeriode = mysql_query($sPeriode);
while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
    $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
}
$sKry = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan where lokasitugas='".$lksiTugas."' and sistemgaji='Harian' order by namakaryawan asc";
$qKry = mysql_query($sKry);
while ($rKry = mysql_fetch_assoc($qKry)) {
    $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
}



$optOrg = makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$optDept = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optTipe = $optDept;
$sDept = 'select * from '.$dbname.'.sdm_5departemen order by nama asc';
$qDept = mysql_query($sDept);
while ($rDept = mysql_fetch_assoc($qDept)) {
    $optDept .= '<option value='.$rDept['kode'].'>'.$rDept['nama'].'</option>';
}
$sTipeKary = 'select distinct * from '.$dbname.'.sdm_5tipekaryawan order by tipe asc';
$qTipeKary = mysql_query($sTipeKary);
while ($rTipeKary = mysql_fetch_assoc($qTipeKary)) {
    $optTipe .= "<option value='".$rTipeKary['id']."'>".$rTipeKary['tipe'].'</option>';
}
$arr = '##periode##kdBag##tPkary';
$arrKry = '##period##idKry';
$arrAfd = '##perod##idAfd##kdBag2##tPkary2';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n\r\n<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>\r\n<script>\r\nfunction  getPeriode()\r\n{\r\n    kdOrg=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;\r\n    tujuan='sdm_slave_2slipGajiHarianAfd';\r\n    param='idAfd='+kdOrg;\r\n    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);\r\n    function respog()\r\n\t{\r\n\t\t      if(con.readyState==4)\r\n\t\t      {\r\n\t\t\t        if (con.status == 200) {\r\n\t\t\t\t\t\tbusy_off();\r\n\t\t\t\t\t\tif (!isSaveResponse(con.responseText)) {\r\n\t\t\t\t\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\telse {\r\n\t\t\t\t\t\t\t//alert(con.responseText);\r\n\t\t\t\t\t\t\tdocument.getElementById('perod').innerHTML=con.responseText;\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}\r\n\t\t\t\t\telse {\r\n\t\t\t\t\t\tbusy_off();\r\n\t\t\t\t\t\terror_catch(con.status);\r\n\t\t\t\t\t}\r\n\t\t      }\t\r\n\t }  \t\r\n}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n      ";
if ('HOLDING' != $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
    echo "\r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajiharianper'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag" name="kdBag" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary" name="tPkary" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\" align=\"center\">\r\n<button onclick=\"zPreview('sdm_slave_2slipGajiHarian','";
    echo $arr;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiHarian','";
    echo $arr;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiHarian.php','";
    echo $arr;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>\r\n      \r\n<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajiharianper'].'/'.$_SESSION['lang']['karyawan'];
    echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="period" name="period" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n\r\n<tr><td><label>";
    echo $_SESSION['lang']['namakaryawan'];
    echo '</label></td><td><select id="idKry" name="idKry" style="width:150px">';
    echo $optKry;
    echo "</select></td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\" align=\"center\"><button onclick=\"zPreview('sdm_slave_2slipGajiHarian','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiHarian','";
    echo $arrKry;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajiharianper'].'/';
    echo "Afdeling</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="perod" name="perod" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n\r\n<tr><td><label>";
    echo $_SESSION['lang']['unit'];
    echo "</label></td><td>\r\n<select id=\"idAfd\" name=\"idAfd\" style=\"width:150px\">";
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag2" name="kdBag2" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary2" name="tPkary" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr><td colspan=\"2\" align=\"center\"><button onclick=\"zPreview('sdm_slave_2slipGajiHarianAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiHarianAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiHarianAfd.php','";
    echo $arrAfd;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
} else {
    echo "      <div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";
    echo $_SESSION['lang']['slipgajiharianper'].'/';
    echo "Afdeling</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
    echo $_SESSION['lang']['unit'];
    echo "</label></td><td>\r\n<select id=\"idAfd\" name=\"idAfd\" style=\"width:150px\">";
    echo $optOrg;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['periode'];
    echo '</label></td><td><select id="perod" name="perod" style="width:150px">';
    echo $optPeriode;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['bagian'];
    echo '</label></td><td><select id="kdBag2" name="kdBag2" style="width:150px">';
    echo $optDept;
    echo "</select></td></tr>\r\n<tr><td><label>";
    echo $_SESSION['lang']['tipekaryawan'];
    echo '</label></td><td><select id="tPkary2" name="tPkary2" style="width:150px">';
    echo $optTipe;
    echo "</select></td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2slipGajiHarianAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2slipGajiHarianAfd','";
    echo $arrAfd;
    echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2slipGajiHarianAfd.php','";
    echo $arrAfd;
    echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n      ";
}

echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>